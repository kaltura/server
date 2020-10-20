<?php

abstract class DeliveryProfileLive extends DeliveryProfile {
	const USER_TYPE_ADMIN = 'admin';
	const USER_TYPE_USER = 'user';
	const DEFAULT_MAINTENANCE_DC = -1;

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
		$data = curl_exec($ch);
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
		if(!$this->liveStreamConfig)
			$this->liveStreamConfig = new kLiveStreamConfiguration();
		
		$entry = $this->getDynamicAttributes()->getEntry();
		if(in_array($entry->getSource(), array(EntrySourceType::MANUAL_LIVE_STREAM, EntrySourceType::AKAMAI_UNIVERSAL_LIVE)))
		{
			$this->initManualLiveStreamConfiguration($entry);
			return;
		}
		$status = array(EntryServerNodeStatus::PLAYABLE);
		if($this->getDynamicAttributes()->getServeVodFromLive())
			$status[] = EntryServerNodeStatus::MARKED_FOR_DELETION;

		$entryId = $this->getDynamicAttributes()->getEntryId();
		$liveEntryServerNodes = EntryServerNodePeer::retrieveByEntryIdAndStatuses($entryId, $status);
		if(!count($liveEntryServerNodes))
			return;

		$requestedServerType = $this->getDynamicAttributes()->getStreamType();
		$dcInMaintenance = $this->getIsMaintenanceFromCache($entryId);
		KalturaLog::debug("Having requested-Server-Type of [$requestedServerType] and DC in maintenance of [$dcInMaintenance]");
		$liveEntryServerNodes = $this->filterAndSet($liveEntryServerNodes, $requestedServerType, $dcInMaintenance);
		if (empty($liveEntryServerNodes))
			KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_LIVE, "Entry [$entryId] is not broadcasting on stream type [$requestedServerType]");

		//sort the entryServerNode array by weight from the heaviest to lowest
		usort($liveEntryServerNodes, function ($a, $b) {return $b->weight - $a->weight;});
		
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
		if ($this->getDynamicAttributes()->getStreamType() == EntryServerNodeType::LIVE_BACKUP)
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
		$protocol = $this->getDynamicAttributes()->getMediaProtocol();
		$segmentDuration = $this->getDynamicAttributes()->getEntry()->getSegmentDuration();
		
		$livePackagerUrl = $serverNode->getPlaybackHost($protocol, $streamFormat, $this->getUrl());
		$livePackagerUrl = rtrim(str_replace('{DC}', $serverNode->getEnvDc(), $livePackagerUrl), '/');
		
		if(strpos($livePackagerUrl, "{m}") !== false)
		{
			$this->shouldRedirect = true;
			
			$hostname = $serverNode->getHostname();
			if(!$serverNode->getIsExternalMediaServer())
				$hostname = preg_replace('/\..*$/', '', $hostname);
			
			$livePackagerUrl = str_replace("{m}", $hostname, $livePackagerUrl);
		}
		
		$partnerID = $this->getDynamicAttributes()->getEntry()->getPartnerId();
		
		if($this->getDynamicAttributes()->getServeVodFromLive())
		{
			$entryId = $this->getDynamicAttributes()->getServeLiveAsVodEntryId();
			$liveType = "/recording/";
			$entry = entryPeer::retrieveByPK($entryId);
			if ($entry && $entry->getFlowType() == EntryFlowType::LIVE_CLIPPING)
				$liveType = "/clip/";
			$livePackagerUrl = str_replace("/live/", $liveType, $livePackagerUrl);
		}
		else
		{
			$entryId = $this->getDynamicAttributes()->getEntryId();
		}
		
		$livePackagerUrl = "$livePackagerUrl/p/$partnerID/e/$entryId/";
		$livePackagerUrl .= $serverNode->getSegmentDurationUrlString($segmentDuration);
		$livePackagerUrl .= $serverNode->getSessionType($entryServerNode);

		$entry = $this->getDynamicAttributes()->getEntry();
		if ($entry->getExplicitLive())
		{
			$userType = self::USER_TYPE_ADMIN;
		 	if (!$entry->canViewExplicitLive())
				$userType = self::USER_TYPE_USER;
			$livePackagerUrl .= "type/$userType/";
		}
		$secureToken = $this->generateLiveSecuredPackagerToken($livePackagerUrl);
		$livePackagerUrl .= "t/$secureToken/";

		KalturaLog::debug("Live Packager base stream Url [$livePackagerUrl]");
		return $livePackagerUrl;
	}
	
	private function generateLiveSecuredPackagerToken($url)
	{
		$livePackagerToken = kConf::get("live_packager_secure_token");
		
		$signingDomain = $this->getLivePackagerSigningDomain(); 
		if($signingDomain && $signingDomain != '')
		{
			$domain = parse_url($url, PHP_URL_HOST);
			if($domain && $domain != '')
			{
				$url = str_replace($domain, $signingDomain, $url);
			}
			else
			{ 
				KalturaLog::debug("Failed to parse domain from original url, signed domain will not be modified");
			}
		}

		//Remove schema from the signed token to avoid validation errors in case manifest is in http and urls are rtunined in https
		$url = preg_replace('#^https?://#', '', $url);
		
		$token = md5("$livePackagerToken $url", true);
		$token = rtrim(strtr(base64_encode($token), '+/', '-_'), '=');
		
		return $token;
	}
	
	protected function getRenderer($flavors)
	{
		if($this->shouldRedirect) 
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
}

