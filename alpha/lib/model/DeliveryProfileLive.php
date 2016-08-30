<?php

abstract class DeliveryProfileLive extends DeliveryProfile {
	
	/**
	 * @var kLiveStreamConfiguration
	 */
	protected $liveStreamConfig;
	
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
		$this->liveStreamConfig = new kLiveStreamConfiguration();
		$liveEntryServerNodes = EntryServerNodePeer::retrievePlayableByEntryId($this->getDynamicAttributes()->getEntryId());
		
		if(!count($liveEntryServerNodes))
			return;
		
		foreach($liveEntryServerNodes as $key => $liveEntryServerNode)
		{
			/* @var $liveEntryServerNode LiveEntryServerNode */
			$serverNode = ServerNodePeer::retrieveActiveMediaServerNode(null, $liveEntryServerNode->getServerNodeId());
			if($serverNode)
			{
				KalturaLog::debug("mediaServer->getDc [" . $serverNode->getDc() . "] == kDataCenterMgr::getCurrentDcId [" . kDataCenterMgr::getCurrentDcId() . "]");
				if($serverNode->getDc() == kDataCenterMgr::getCurrentDcId())
				{
					$this->liveStreamConfig->setUrl($this->getHttpUrl($serverNode));
					$this->liveStreamConfig->setPrimaryStreamInfo($liveEntryServerNode->getStreams());
					unset($liveEntryServerNodes[$key]);
					break;
				}
			}
		}
		
		if(!$this->liveStreamConfig->getUrl())
		{
			$liveEntryServerNode = array_shift($liveEntryServerNodes);
			$serverNode = ServerNodePeer::retrieveActiveMediaServerNode(null, $liveEntryServerNode->getServerNodeId());
			if($serverNode)
			{
				KalturaLog::debug("mediaServer->getDc [" . $serverNode->getDc() . "] == kDataCenterMgr::getCurrentDcId [" . kDataCenterMgr::getCurrentDcId() . "]");
				if($serverNode->getDc() == kDataCenterMgr::getCurrentDcId())
				{
					$this->liveStreamConfig->setUrl($this->getHttpUrl($serverNode));
					$this->liveStreamConfig->setPrimaryStreamInfo($liveEntryServerNode->getStreams());
				}
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
		$domain = ($this->getHostName() && $this->getHostName() !== '') ? $this->getHostName() : $serverNode->getHostName();
		$port = $serverNode->getPortByProtocolAndFormat($protocol, $streamFormat);
		$appPrefix = $serverNode->getApplicationPrefix();
		$appName = $serverNode->getApplicationName();
		
		$baseUrl = "$protocol://$domain:$port/$appPrefix/$appName";
		KalturaLog::debug("Live Stream base url [$baseUrl]");
		
		return $baseUrl;
	}
}

