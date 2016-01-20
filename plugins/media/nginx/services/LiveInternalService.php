<?php

/**
 * Enable serving live authentication, keep-alive and transcoding internally in the DC without ks
 * @service liveInternal
 * @package plugins.nginxLive
 * @subpackage api.services
 */
class LiveInternalService extends KalturaLiveEntryService
{
	/* (non-PHPdoc)
	 * @see KalturaBaseService::initService()
	 */
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		$this->applyPartnerFilterForClass('conversionProfile2');
		$this->applyPartnerFilterForClass('assetParams');
	}
	
	private function exitWithHttpError(Exception $e, $errorCode)
	{
		KalturaLog::err($e);
		if(function_exists('http_response_code')) {
			http_response_code($errorCode);
		}
		
		$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
		header("$protocol $errorCode " . $e->getMessage());
		throw $e;
	}
	
	/**
	 * Authenticate live-stream entry against stream token and partner limitations
	 * 
	 * @action authenticate
	 * @param string $e Live stream entry id
	 * @param string $t Live stream broadcasting token
	 * @param int $i Media server index
	 * @param string $app Application name
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::LIVE_STREAM_INVALID_TOKEN
	 */
	public function authenticateAction($e, $t, $i, $app)
	{
		try{
			$broadcastConfig = kConf::getMap('broadcast');
			if(!$broadcastConfig)
			{
				throw new Exception('Broadcast configuration not found');
			}
			if(!isset($broadcastConfig[$i]))
			{
				throw new Exception("Broadcast configuration not found for media-server index [$i]");
			}
		
			$mediaServerConfig = $broadcastConfig[$i];
			if(!isset($mediaServerConfig['authenticate']) || !isset($mediaServerConfig['authenticate'][$app]) || !$mediaServerConfig['authenticate'][$app])
			{
				KalturaLog::log("Application [$app] doesn't require authentication");
				return;
			}
			
			$entry = $this->authenticate($e, $t);
		}
		catch(Exception $e){
			$this->exitWithHttpError($e, 401); // Unauthorized
		}
	}
	
	private function transcode($entryId, $mediaServerIndex, $name, $url, $app)
	{
		$json = null;
		
		try{
			$broadcastConfig = kConf::getMap('broadcast');
			if(!$broadcastConfig)
			{
				throw new Exception('Broadcast configuration not found');
			}
			if(!isset($broadcastConfig[$mediaServerIndex]))
			{
				throw new Exception("Broadcast configuration not found for media-server index [$mediaServerIndex]");
			}
		
			$mediaServerConfig = $broadcastConfig[$mediaServerIndex];
			$domain = $mediaServerConfig['domain'];
			$domain = $mediaServerConfig['domain'];
			$outputUrl = "rtmp://$domain:1935/kPublish";
			if(isset($mediaServerConfig['transcode']) && isset($mediaServerConfig['transcode'][$app]))
			{
				$outputUrl = $mediaServerConfig['transcode'][$app];
			}
			
			$entry = $this->getCoreEntry($entryId);
			$message = $this->getTranscodeMessage($entry, $url, $outputUrl, $name, $mediaServerIndex);
			KalturaLog::debug("Transcode message: " . print_r($message, true));
			$json = json_encode($message);
		}
		catch(Exception $e){
			$this->exitWithHttpError($e, 404); // Not Found
		}
		
		$queueKey = kConf::get('cloud_transcode_queue', 'local', 'cloud-transcode');
        $queueProvider = QueueProvider::getInstance();
        $queueProvider->send($queueKey, $json);
	}
	
	/**
	 * @param LiveEntry $entry
	 * @param string $url
	 * @param string $streamName
	 * @param KalturaMediaServerIndex $mediaServerIndex
	 * @return string
	 */
	private function getTranscodeMessage(LiveEntry $entry, $inputUrl, $outputUrl, $streamName, $mediaServerIndex)
	{
		$entryId = $entry->getId();
		if (!$entry || $entry->getType() != KalturaEntryType::LIVE_STREAM || !in_array($entry->getSource(), array(KalturaSourceType::LIVE_STREAM, KalturaSourceType::LIVE_STREAM_ONTEXTDATA_CAPTIONS))) 
		{
			$e = new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			$this->exitWithHttpError($e, 404); // Not Found
		}
			
		$conversionProfileId = $entry->getConversionProfileId();
		$liveParams = assetParamsPeer::retrieveByProfile($conversionProfileId);
		
		$data = array(
			'entryId' => $entry->getId(),
			'partnerId' => $entry->getPartnerId(),
			'mediaServerIndex' => $mediaServerIndex
		);
		foreach($liveParams as $liveParamsItem)
		{
			/* @var $liveParamsItem liveParams */
			$data['liveParamsId'] = $liveParamsItem->getId();
			$suffix = $liveParamsItem->getStreamSuffix() . '?' . http_build_query($data);
			$liveParamsItem->setStreamSuffix($suffix);
		}
		
		$command = KDLWrap::CDLGenerateTargetLiveFlavors($liveParams);
		$command = str_replace(KDLCmdlinePlaceholders::InFileName, "$inputUrl/$streamName", $command);
		$command = str_replace(KDLCmdlinePlaceholders::OutFileName, "$outputUrl/$entryId", $command);
		
		$data = array(
			'streamName' => $streamName,
			'command' => $command,
		);
		return $data;
	}
	
	/**
	 * Initiate transcoding process
	 * 
	 * @action transcode
	 * @param string $name Live stream name
	 * @param string $hostname Media server host name
	 * @param string $tcurl Media server url
	 * @param string $app Application name
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	public function transcodeAction($name, $hostname, $tcurl, $app)
	{
		list($entryId, $suffix) = $this->parseStreamName($name);
		list($partnerId, $entryId, $mediaServerIndex, $token) = $this->parseStreamUrl($tcurl);
		myPartnerUtils::reapplyPartnerFilters($partnerId);
		
		$this->registerMediaServerAction($entryId, $hostname, $mediaServerIndex, $app, KalturaLiveEntryStatus::BROADCASTING);
		$this->transcode($entryId, $mediaServerIndex, $name, $tcurl, $app);
	}
	
	/**
	 * Validate that entry still exists and can keep streaming
	 * 
	 * @action shouldKeepAlive
	 * @param string $name Live stream name
	 * @param string $hostname Media server host name
	 * @param string $tcurl Media server url
	 * @param string $app Application name
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	public function shouldKeepAliveAction($name, $hostname, $tcurl, $app)
	{
		list($entryId, $suffix) = $this->parseStreamName($name);
		list($partnerId, $entryId, $mediaServerIndex, $token) = $this->parseStreamUrl($tcurl);
		myPartnerUtils::reapplyPartnerFilters($partnerId);
		
		$this->transcode($entryId, $mediaServerIndex, $name, $tcurl, $app);
	}

	/**
	 * Reports that stream is publishing
	 * 
	 * @action registerPlayback
	 * @param string $entryId Live entry id
	 * @param int $liveParamsId Live params id
	 * @param KalturaMediaServerIndex $mediaServerIndex Live params id
	 * @param string $hostname Media server host name
	 * @param string $app Application name
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	public function registerPlaybackAction($entryId, $liveParamsId, $mediaServerIndex, $hostname, $app)
	{
		try{
			$entry = $this->getCoreEntry($entryId);
		}
		catch(Exception $e){
			$this->exitWithHttpError($e, 404); // Not Found
		}
		
		$this->registerMediaServerAction($entryId, $hostname, $mediaServerIndex, $app, KalturaLiveEntryStatus::PLAYABLE);
	}
	
	
	/**
	 * Reports that stream unpublishing
	 * 
	 * @action unregister
	 * @param string $entryId Live entry id
	 * @param int $liveParamsId Live params id
	 * @param KalturaMediaServerIndex $mediaServerIndex Live params id
	 * @param string $hostname Media server host name
	 * @param string $app Application name
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	public function unregisterAction($entryId, $liveParamsId, $mediaServerIndex, $hostname, $app)
	{
		try{
			$entry = $this->getCoreEntry($entryId);
		}
		catch(Exception $e){
			$this->exitWithHttpError($e, 404); // Not Found
		}
		
		$this->unregisterMediaServerAction($entryId, $hostname, $mediaServerIndex);
	}
	
	/**
	 * Enter description here ...
	 * @param unknown_type $url
	 * @throws KalturaAPIException
	 * @return array ($partnerId, $entryId, $mediaServerIndex, $token)
	 */
	private function parseStreamUrl($url)
	{
		$parts = explode('?', $url);
		if(count($parts) < 2)
			throw new KalturaAPIException(NginxErrors::INVALID_STREAM_NAME, $url);

		$p = null;
		$e = null;
		$i = null;
		$t = null;
		parse_str($parts[1]);
			
		if(is_null($p) || !is_numeric($p))
			throw new KalturaAPIException(NginxErrors::INVALID_STREAM_NAME, $url);
			
		if(is_null($e))
			throw new KalturaAPIException(NginxErrors::INVALID_STREAM_NAME, $url);
			
		if(is_null($i) || !is_numeric($i))
			throw new KalturaAPIException(NginxErrors::INVALID_STREAM_NAME, $url);
			
		return array($p, $e, $i, $t);
	}
	
	private function parseStreamName($streamName)
	{
		$matches = null;
		if(!preg_match('/^(\d_.{8})_(.+)$/', $streamName, $matches))
			throw new KalturaAPIException(NginxErrors::INVALID_STREAM_NAME, $streamName);
			
		$entryId = $matches[1];
		$suffix = $matches[2];
		
		return array($entryId, $suffix);
	}
	
	/**
	 * @param string $entryId
	 * @return LiveEntrys
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	private function getCoreEntry($entryId)
	{
		$entry = null;
		if (!kCurrentContext::$ks)
		{
			kEntitlementUtils::initEntitlementEnforcement(null, false);
			$entry = kCurrentContext::initPartnerByEntryId($entryId);
			
			if (!$entry || $entry->getStatus() == entryStatus::DELETED)
				throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
				
			// enforce entitlement
			$this->setPartnerFilters(kCurrentContext::getCurrentPartnerId());
		}
		else 
		{	
			$entry = entryPeer::retrieveByPK($entryId);
		}
		
		return $entry;
	}
	
}
