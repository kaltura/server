<?php

abstract class DeliveryProfileLive extends DeliveryProfile {
	const DEFAULT_MAINTENANCE_DC = -1;
	const SHOULD_REDIRECT = "should_redirect";

	/**
	 * @var kLiveStreamConfiguration
	 */
	protected $liveStreamConfig;
	
	/**
	 * @var bool
	 */
	protected $shouldRedirect = false;
	
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kRedirectManifestRenderer';
	}
	
	public function setLiveStreamConfig(kLiveStreamConfiguration $liveStreamConfig)
	{
		$this->liveStreamConfig = $liveStreamConfig;
	}
	
	/**
	 * Method checks whether the URL passed to it as a parameter returns a response.
	 * @param string $url
	 * @return string
	 */
	protected function urlExists ($url, array $contentTypeToReturn, $range = null)
	{
		if (is_null($url))
			return false;
		if (!function_exists('curl_init'))
		{
			KalturaLog::err('Unable to use util when php curl is not enabled');
			return false;
		}
		KalturaLog::log("Checking URL [$url] with range [$range]");
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		if (!is_null($range))
		{
			curl_setopt($ch, CURLOPT_RANGE, $range);
		}

		$start = microtime(true);
		$data = curl_exec($ch);
		KalturaMonitorClient::monitorCurl(parse_url($url, PHP_URL_HOST), microtime(true) - $start, $ch);

		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
		curl_close($ch);
	
		$contentTypeToCheck = strstr($contentType, ";", true);
		if(!$contentTypeToCheck)
			$contentTypeToCheck = $contentType;
		if($data && $httpcode>=200 && $httpcode<300)
		{
			$contentTypeToCheck = strtolower( trim( $contentTypeToCheck ) );
			foreach ( $contentTypeToReturn as $cttr )
			{
				if ( $contentTypeToCheck === strtolower( trim( $cttr ) ) )
				{
					return $data;
				}
			}

			return true;
		}
		else
			return false;
	}
	
	/**
	 * Function check if URL provided is a valid one if not returns fixed url with the parent url relative path
	 * @param string $urlToCheck
	 * @param string $parentURL
	 * @return fixed url path
	 */
	protected function checkIfValidUrl($urlToCheck, $parentURL)
	{
		$urlToCheck = trim($urlToCheck);
		if (strpos($urlToCheck, '://') === false)
		{
			$urlToCheck = dirname($parentURL) . '/' . $urlToCheck;
		}
	
		return $urlToCheck;
	}
	
	public function buildServeFlavors() 
	{
		$this->initLiveStreamConfig();
		$flavors = $this->buildHttpFlavorsArray();
		return $flavors;
	}
	
	protected function initLiveStreamConfig()
	{
		if($this->params->getResponseFormat() == 'redirect')
		{
			$this->shouldRedirect = true;
		}

		if(!$this->liveStreamConfig)
			$this->liveStreamConfig = new kLiveStreamConfiguration();

		if(self::isManualEntryFlow())
		{
			$entry = $this->getDynamicAttributes()->getEntry();
			$this->initManualLiveStreamConfiguration($entry);
			return;
		}

		$entryId = $this->getDynamicAttributes()->getEntryId();
		$liveEntryServerNodes = $this->getSortedLiveEntryServerNodes($entryId);
		if(!count($liveEntryServerNodes))
			return;
		$liveEntryServerNode = array_shift($liveEntryServerNodes); // after sort first is the primary
		
		// If min/max bitrate was requested, add the constraint to the array of flavorParamsIds in the profile's attributes.
		
		$streams = $liveEntryServerNode->getStreams();
		$this->sanitizeAndFilterStreamIdsByBitrate($streams);

		$this->liveStreamConfig->setUrl($this->getHttpUrl($liveEntryServerNode));
		$this->liveStreamConfig->setPrimaryStreamInfo($liveEntryServerNode->getStreams());
		
		$liveEntryServerNode = array_shift($liveEntryServerNodes);
		
		if ($liveEntryServerNode) { // if list has another entry server node set it as backup
			// If min/max bitrate was requested, it needs to be applied to the backup stream as well.
			$streams = array_merge($streams, $liveEntryServerNode->getStreams());
			$this->sanitizeAndFilterStreamIdsByBitrate($streams);
			
			$this->liveStreamConfig->setBackupUrl($this->getHttpUrl($liveEntryServerNode));
			$this->liveStreamConfig->setBackupStreamInfo($liveEntryServerNode->getStreams());
		}
	}

	public function getSortedLiveEntryServerNodes($entryId): array
	{
		$statuses = array(EntryServerNodeStatus::PLAYABLE);
		$dynamicAttributes = $this->getDynamicAttributes();
		if($dynamicAttributes && $dynamicAttributes->getServeVodFromLive())
			$statuses[] = EntryServerNodeStatus::MARKED_FOR_DELETION;

		$liveEntryServerNodes = EntryServerNodePeer::retrieveByEntryIdAndStatuses($entryId, $statuses);
		if(!count($liveEntryServerNodes))
		{
			return array();
		}

		$requestedServerType = $this->getDynamicAttributes()->getStreamType();
		$dcInMaintenance = $this->getIsMaintenanceFromCache($entryId);
		KalturaLog::debug("Having requested-Server-Type of [$requestedServerType] and DC in maintenance of [$dcInMaintenance]");
		$liveEntryServerNodes = $this->filterAndSet($liveEntryServerNodes, $requestedServerType, $dcInMaintenance);
		if (empty($liveEntryServerNodes))
		{
			KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_LIVE, "Entry [$entryId] is not broadcasting on stream type [$requestedServerType]");
		}

		//sort the entryServerNode array by weight from the heaviest to lowest
		usort($liveEntryServerNodes, function ($a, $b) {return $b->weight - $a->weight;});
		return $liveEntryServerNodes;
	}

	private function filterAndSet($liveEntryServerNodes, $requestedServerType, $dcInMaintenance)
	{
		return array_filter($liveEntryServerNodes, function($esn) use ($dcInMaintenance, $requestedServerType) {
			if (!is_null($requestedServerType) && $esn->getServerType() != $requestedServerType)
				return false; // if request specific type then ignore all others
			$esn->serverNode = ServerNodePeer::retrieveActiveMediaServerNode(null, $esn->getServerNodeId());
			if (!$esn->serverNode)
				return false; // if the entry has no active server node ignore it
			$esn->weight = ($esn->getServerType() == EntryServerNodeType::LIVE_PRIMARY) ? 10 : 0;
			if ($esn->serverNode->getDc() == $dcInMaintenance)
				$esn->weight -= 100; // if DC in maintenance then lower its priority
			return true;
		});
	}

	private function getIsMaintenanceFromCache($entryId)
	{
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_LOCK_KEYS);
		if (!$cache)
			return self::DEFAULT_MAINTENANCE_DC;
		$val = $cache->get("Live-MaintenanceDataCacheKey");
		if (!$val)
			return self::DEFAULT_MAINTENANCE_DC;
		$result = json_decode($val, true);
		if (!is_array($result))
		{
			KalturaLog::notice("Got maintenance data from cache but could not parse it. Raw data: " . print_r($val, true));
			return self::DEFAULT_MAINTENANCE_DC;
		}
		KalturaLog::debug("Got maintenance data from cache: " . print_r($result, true));
		if (key_exists("maintenanceDC", $result))
			return $result["maintenanceDC"];
		if (key_exists($entryId, $result))
			return $result[$entryId];
		return self::DEFAULT_MAINTENANCE_DC;
	}
	
	protected function initManualLiveStreamConfiguration(LiveStreamEntry $entry)
	{
		$customLiveStreamConfigurations = array();
		
		if($entry->getHlsStreamUrl())
		{
			$hlsLiveStreamConfig = new kLiveStreamConfiguration();
			$hlsLiveStreamConfig->setUrl($entry->getHlsStreamUrl());
			$hlsLiveStreamConfig->setProtocol(PlaybackProtocol::APPLE_HTTP);
			$customLiveStreamConfigurations[] = $hlsLiveStreamConfig;
		}
		
		$customLiveStreamConfigurations = array_merge($entry->getCustomLiveStreamConfigurations(), $customLiveStreamConfigurations);
		foreach($customLiveStreamConfigurations as $customLiveStreamConfiguration)
		{
			/* @var $customLiveStreamConfiguration kLiveStreamConfiguration */
			if($this->getDynamicAttributes()->getFormat() == $customLiveStreamConfiguration->getProtocol())
			{
				$this->liveStreamConfig = $customLiveStreamConfiguration;
				return;
			}
		}
		
		KalturaLog::debug("Could not locate custom live stream configuration from manual liveStream entry [{$entry->getId()}]");
	}
	
	public function isLive ($url) {
		$url = $this->getTokenizedUrl($url);
		return $this->checkIsLive($url);
	}
	
	protected function getTokenizedUrl($url){
		$urlPath = parse_url($url, PHP_URL_PATH);
		if (!$urlPath || substr($url, -strlen($urlPath)) != $urlPath)
			return $url;
		$urlPrefix = substr($url, 0, -strlen($urlPath));
		$tokenizer = $this->getTokenizer();
		if ($tokenizer)
			return $urlPrefix.$tokenizer->tokenizeSingleUrl($urlPath);
		return $url;
	}
	
	protected function checkIsLive($url) {
		throw new Exception('Status cannot be determined for live stream protocol. Delivery Profile ID: '.$this->getId());
	}

	protected function buildHttpFlavorsArray()
	{
		$flavors = array();
		
		$httpUrl = $this->liveStreamConfig->getUrl();
		// the streamType set url in the init func. But for manual live we don't have the init with the entry-server-node
		if ($this->isManualEntryFlow() && $this->getDynamicAttributes()->getStreamType() == EntryServerNodeType::LIVE_BACKUP)
		{
			$httpUrl = $this->liveStreamConfig->getBackupUrl();
		}
		$flavors[] = $this->getFlavorAssetInfo('', $httpUrl); // passing the url as urlPrefix so that only the path will be tokenized
		return $flavors;
	}

	protected function getStreamName()
	{
		$flavorParamsIds = $this->getDynamicAttributes()->getFlavorParamIds();
		$streamName = $this->getDynamicAttributes()->getEntryId();
		$entry = entryPeer::retrieveByPK($streamName);

		$tag = $this->getDynamicAttributes()->getTags();
		if(is_null($tag) && ($entry->getConversionProfileId() || $entry->getType() == entryType::LIVE_CHANNEL))
			$tag = 'all';

		if(count($flavorParamsIds) === 1)
		{
			$streamName .= '_' . reset($flavorParamsIds);
		}
		elseif(count($flavorParamsIds) > 1)
		{
			sort($flavorParamsIds);
			$tag = implode('_', $flavorParamsIds);
			$streamName = "smil:{$streamName}_{$tag}.smil";
		}
		elseif($tag)
		{
			$streamName = "smil:{$streamName}_{$tag}.smil";
		}

		return $streamName;
	}

	protected function getQueryAttributes()
	{
		$flavorParamsIds = $this->getDynamicAttributes()->getFlavorParamIds();
		$streamName = $this->getDynamicAttributes()->getEntryId();
		$entry = entryPeer::retrieveByPK($streamName);

		$queryString = array();
		if($entry->getDvrStatus() == DVRStatus::ENABLED)
		{
			$queryString[] = 'DVR';
		}

		if(count($flavorParamsIds) > 1)
		{
			sort($flavorParamsIds);
			$queryString[] = 'flavorIds=' . implode(',', $flavorParamsIds);
		}

		if(count($queryString))
		{
			$queryString = '?' . implode('&', $queryString);
		}
		else
		{
			$queryString = '';
		}

		return $queryString;
	}
	
	protected function getHttpUrl($entryServerNode)
	{
		return "";
	}

	protected function getBaseUrl($serverNode, $streamFormat = null)
	{
		/* @var $serverNode MediaServerNode */
		$protocol = $this->getDynamicAttributes()->getMediaProtocol();
		$playbackHost = $serverNode->getPlaybackHost($protocol, $streamFormat, $this->getUrl());
		$appNameAndPrefix = $serverNode->getAppNameAndPrefix();
		
		$baseUrl = $playbackHost.$appNameAndPrefix;
		
		KalturaLog::debug("Live Stream base url [$baseUrl]");
		return $baseUrl;
	}
	
	protected function getLivePackagerUrl($entryServerNode, $streamFormat = null)
	{
		/* @var $serverNode MediaServerNode */
		$serverNode = $entryServerNode->serverNode;
		/* @var $entry LiveStreamEntry */
		$entry = $this->getDynamicAttributes()->getEntry();

		$protocol = $this->getDynamicAttributes()->getMediaProtocol();
		$segmentDuration = $entry->getSegmentDuration();
		if ($entry->isLowLatencyEntry())
		{
			$this->shouldRedirect = true; // low-latency manifest should be build by live-packager
		}
		$livePackagerUrl = $serverNode->getPlaybackHost($protocol, $streamFormat, $this->getUrl());
		$livePackagerUrl = rtrim(str_replace('{DC}', $serverNode->getEnvDc(), $livePackagerUrl), '/');
		
		//Used for ecdn mode
		list($matchedPattern, $shouldRedirect) = $this->matchLivePackagerUrlRegexPattern($livePackagerUrl);
		if($matchedPattern)
		{
			$this->shouldRedirect = $shouldRedirect;

			$hostname = $serverNode->getHostname();
			if(!$serverNode->getIsExternalMediaServer())
				$hostname = preg_replace('/\..*$/', '', $hostname);
			
			$livePackagerUrl = str_replace($matchedPattern, $hostname, $livePackagerUrl);
		}

		if ($this->getDynamicAttributes()->getServeVodFromLive())
		{
			$livePackagerUrl = $serverNode->modifyUrlForVodFromLive($livePackagerUrl, $this->getDynamicAttributes());
		}

		$livePackagerUrl .= $serverNode->getPartnerIdUrl($this->getDynamicAttributes());
		$livePackagerUrl .= $serverNode->getEntryIdUrl($this->getDynamicAttributes());
		$livePackagerUrl .= $serverNode->getSegmentDurationUrlString($segmentDuration);

		$livePackagerUrl .= $serverNode->getExplicitLiveUrl($livePackagerUrl, $entry);
		$livePackagerUrl .= $serverNode->getSessionType($entryServerNode);
		$livePackagerUrl .= $serverNode->getAdditionalUrlParam($entry, $entryServerNode);
		$secureToken = $this->generateLiveSecuredPackagerToken($livePackagerUrl);
		$livePackagerUrl .= "t/$secureToken/";

		KalturaLog::debug("Live Packager base stream Url [$livePackagerUrl]");
		return $livePackagerUrl;
	}
	
	private function matchLivePackagerUrlRegexPattern($livePackagerUrl)
	{
		$matchedPattern = null;
		$shouldRedirect = false;
		
		if (strpos($livePackagerUrl, "{m}") !== false)
		{
			$matchedPattern = "{m}";
			$shouldRedirect = true;
		}
		
		if (strpos($livePackagerUrl, "{mn}") !== false)
		{
			$matchedPattern = "{mn}";
		}
		
		return array($matchedPattern, $shouldRedirect);
	}

	private function generateLiveSecuredPackagerToken($url)
	{
		$signingDomain = $this->getLivePackagerSigningDomain();
		return myPackagerUtils::generateLivePackagerToken($url, $signingDomain);
	}
	
	protected function getRenderer($flavors)
	{
		if($this->getShouldRedirect())
		{
			$this->DEFAULT_RENDERER_CLASS = 'kRedirectManifestRenderer';
		}
		
		$renderer = parent::getRenderer($flavors);
		return $renderer;
	}
	
	protected function sanitizeAndFilterStreamIdsByBitrate ($streams)
	{
		if (!$streams || !count($streams))
		{
			return;
		}
		
		$streamIds = array();
		$allStreamIds = array();
		foreach ($streams as $stream)
		{
			/* @var $stream kLiveStreamParams */
                        $allStreamIds[] = $stream->getFlavorId();

                        if ($this->getDynamicAttributes()->getMinBitrate() && $stream->getBitrate()/1024 > $this->getDynamicAttributes()->getMinBitrate())
                        {
                                $streamIds[] = $stream->getFlavorId();
                        }
                        if ($this->getDynamicAttributes()->getMaxBitrate() && $stream->getBitrate()/1024 < $this->getDynamicAttributes()->getMaxBitrate())
                        {
                                $streamIds[] = $stream->getFlavorId();
                        }
			
		}
		
		$playableStreamIds = array_unique(array_intersect($this->getDynamicAttributes()->getFlavorParamIds(), $allStreamIds));
		if (count($playableStreamIds))
		{
			$streamIds = array_unique(array_intersect($streamIds, $playableStreamIds));
		}
		
		if (!count($streamIds))
		{
			// If the stream info is available to us, min and/or max bitrate params were passed, and no streams comply with them - issue warning and cancel restrictions
			KalturaLog::warning('Entry ['. $this->getDynamicAttributes()->getEntryId() .'] has no streams which comply with the min/max bitrate limitations.');
			$streamIds = $playableStreamIds;
			
			if (!count ($playableStreamIds))
			{
				KalturaLog::warning('None of the explicitly requested flavor params IDs are found in the stream list.');
			}
		}
		
		$this->getDynamicAttributes()->setFlavorParamIds($streamIds);
	}
	
	public function setLivePackagerSigningDomain($v)
	{
		$this->putInCustomData("livePackagerSigningDomain", $v);
	}
	
	public function getLivePackagerSigningDomain()
	{
		return $this->getFromCustomData("livePackagerSigningDomain");
	}

	public function setShouldRedirect($v)
	{
		// sets only the default value in custom data (won't affect "$this->shouldRedirect" which should be changed dynamically)
		$this->putInCustomData(self::SHOULD_REDIRECT, $v);
	}

	public function getShouldRedirect()
	{
		// if the shouldRedirect changed to true dynamically during the request - it takes priority
		return $this->shouldRedirect || $this->getFromCustomData(self::SHOULD_REDIRECT, null, false);
	}

	/**
	 * @return bool - if the source of the entry is external
	 */
	protected function isManualEntryFlow()
	{
		$entry = $this->getDynamicAttributes()->getEntry();
		/* @var $entry LiveEntry */
		return in_array($entry->getSource(), array(EntrySourceType::MANUAL_LIVE_STREAM, EntrySourceType::AKAMAI_UNIVERSAL_LIVE));
	}

	public function getPackagerUrl( $entryServerNode ): string
	{
		return $this->getLivePackagerUrl($entryServerNode);
	}

	public static function hasH265Codec($streams)
	{
		foreach($streams as $stream)
		{
			if ($stream->getCodec() == flavorParams::VIDEO_CODEC_H265 || str_contains($stream->getCodec(), 'hvc1'))
			{
				return true;
			}
		}
		return false;
	}

}

