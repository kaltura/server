<?php

abstract class DeliveryProfileLive extends DeliveryProfile {
	
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
		
		$liveEntryServerNodes = EntryServerNodePeer::retrieveByEntryIdAndStatuses($this->getDynamicAttributes()->getEntryId(), $status);
		if(!count($liveEntryServerNodes))
			return;
		
		foreach($liveEntryServerNodes as $key => $liveEntryServerNode)
		{
			/* @var $liveEntryServerNode LiveEntryServerNode */
			$serverNode = ServerNodePeer::retrieveActiveMediaServerNode(null, $liveEntryServerNode->getServerNodeId());
			if($serverNode)
			{
				//Order by current DC first
				//KalturaLog::debug("mediaServer->getDc [" . $serverNode->getDc() . "] == kDataCenterMgr::getCurrentDcId [" . kDataCenterMgr::getCurrentDcId() . "]");
				//if($serverNode->getDc() == kDataCenterMgr::getCurrentDcId())
				
				//Order by primary DC first
				KalturaLog::debug("liveEntryServerNode->getServerType [" . $liveEntryServerNode->getServerType() . "]");
				if($liveEntryServerNode->getServerType() === EntryServerNodeType::LIVE_PRIMARY)
				{
					$this->liveStreamConfig->setUrl($this->getHttpUrl($serverNode));
					$this->liveStreamConfig->setPrimaryStreamInfo($liveEntryServerNode->getStreams());
					unset($liveEntryServerNodes[$key]);
					break;
				}
			}
		}
		
		if(!$this->liveStreamConfig->getUrl() && count($liveEntryServerNodes))
		{
			$liveEntryServerNode = array_shift($liveEntryServerNodes);
			$serverNode = ServerNodePeer::retrieveActiveMediaServerNode(null, $liveEntryServerNode->getServerNodeId());
			if($serverNode)
			{
				$this->liveStreamConfig->setUrl($this->getHttpUrl($serverNode));
				$this->liveStreamConfig->setPrimaryStreamInfo($liveEntryServerNode->getStreams());
			}
		}
		
		if(count($liveEntryServerNodes))
		{
			$liveEntryServerNode = array_shift($liveEntryServerNodes);
			$serverNode = ServerNodePeer::retrieveActiveMediaServerNode(null, $liveEntryServerNode->getServerNodeId());
			if ($serverNode)
			{
				$this->liveStreamConfig->setBackupUrl($this->getHttpUrl($serverNode));
				$this->liveStreamConfig->setBackupStreamInfo($liveEntryServerNode->getStreams());
			}
		}
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
	
	protected function getHttpUrl($serverNode)
	{
		return "";
	}

	protected function getBaseUrl($serverNode, $streamFormat = null)
	{
		/* @var $serverNode WowzaMediaServerNode */
		$protocol = $this->getDynamicAttributes()->getMediaProtocol();
		$playbackHost = $serverNode->getPlaybackHost($protocol, $streamFormat, $this->getUrl());
		$appNameAndPrefix = $serverNode->getAppNameAndPrefix();
		
		$baseUrl = $playbackHost.$appNameAndPrefix;
		
		KalturaLog::debug("Live Stream base url [$baseUrl]");
		return $baseUrl;
	}
	
	protected function getLivePackagerUrl($serverNode, $streamFormat = null)
	{
		/* @var $serverNode WowzaMediaServerNode */
		$protocol = $this->getDynamicAttributes()->getMediaProtocol();
		$segmentDuration = $this->getDynamicAttributes()->getEntry()->getSegmentDuration();
		
		$livePackagerUrl = $serverNode->getPlaybackHost($protocol, $streamFormat, $this->getUrl());
		$livePackagerUrl = rtrim(str_replace("{DC}", "dc-".$serverNode->getDc(), $livePackagerUrl), "/");
		
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
			$livePackagerUrl = str_replace("/live/", "/recording/", $livePackagerUrl);
		}
		else
		{
			$entryId = $this->getDynamicAttributes()->getEntryId();
		}
		
		$livePackagerUrl = "$livePackagerUrl/p/$partnerID/e/$entryId/sd/$segmentDuration/";
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
	
	public function setLivePackagerSigningDomain($v)
	{
		$this->putInCustomData("livePackagerSigningDomain", $v);
	}
	
	public function getLivePackagerSigningDomain()
	{
		return $this->getFromCustomData("livePackagerSigningDomain");
	}
}

