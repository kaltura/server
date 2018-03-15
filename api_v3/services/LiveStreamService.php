<?php

/**
 * Live Stream service lets you manage live stream entries
 *
 * @service liveStream
 * @package api
 * @subpackage services
 */
class LiveStreamService extends KalturaLiveEntryService
{
	const ISLIVE_ACTION_CACHE_EXPIRY_WHEN_NOT_LIVE = 10;
	const ISLIVE_ACTION_CACHE_EXPIRY_WHEN_LIVE = 30;
	const ISLIVE_ACTION_NON_KALTURA_LIVE_CONDITIONAL_CACHE_EXPIRY = 10;
	const HLS_LIVE_STREAM_CONTENT_TYPE = 'application/vnd.apple.mpegurl';

	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		if($this->getPartnerId() > 0 && !PermissionPeer::isValidForPartner(PermissionName::FEATURE_LIVE_STREAM, $this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
	}
	
	
	protected function partnerRequired($actionName)
	{
		if ($actionName === 'isLive') {
			return false;
		}
		return parent::partnerRequired($actionName);
	}
	
	/**
	 * Adds new live stream entry.
	 * The entry will be queued for provision.
	 * 
	 * @action add
	 * @param KalturaLiveStreamEntry $liveStreamEntry Live stream entry metadata  
	 * @param KalturaSourceType $sourceType  Live stream source type
	 * @return KalturaLiveStreamEntry The new live stream entry
	 * 
	 * @throws KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL
	 */
	function addAction(KalturaLiveStreamEntry $liveStreamEntry, $sourceType = null)
	{
		if($sourceType) {
			$liveStreamEntry->sourceType = $sourceType;	
		}
		elseif(is_null($liveStreamEntry->sourceType)) {
			// default sourceType is AKAMAI_LIVE
			$liveStreamEntry->sourceType = kPluginableEnumsManager::coreToApi('EntrySourceType', $this->getPartner()->getDefaultLiveStreamEntrySourceType());
		}
	
		$conversionProfileId = null;
		if(in_array($liveStreamEntry->sourceType, array(KalturaSourceType::LIVE_STREAM, KalturaSourceType::LIVE_STREAM_ONTEXTDATA_CAPTIONS)))
		{
			$conversionProfileId = $liveStreamEntry->conversionProfileId;
			if(!$conversionProfileId)
			{
				$partner = $this->getPartner();
				if($partner)
					$conversionProfileId = $partner->getDefaultLiveConversionProfileId();
			}
		}
	
		$dbEntry = $this->duplicateTemplateEntry($conversionProfileId, $liveStreamEntry->templateEntryId, new LiveStreamEntry());
		$dbEntry = $this->prepareEntryForInsert($liveStreamEntry, $dbEntry);
		$dbEntry->save();
		
		$te = new TrackEntry();
		$te->setEntryId($dbEntry->getId());
		$te->setTrackEventTypeId(TrackEntry::TRACK_ENTRY_EVENT_TYPE_ADD_ENTRY);
		$te->setDescription(__METHOD__ . ":" . __LINE__ . "::" . $dbEntry->getSource());
		TrackEntry::addTrackEntry($te);
		
		//If a jobData can be created for entry sourceType, add provision job. Otherwise, just save the entry.
		$jobData = kProvisionJobData::getInstance($dbEntry->getSource());
		if ($jobData)
		{
			/* @var $data kProvisionJobData */
			$jobData->populateFromPartner($dbEntry->getPartner());
			$jobData->populateFromEntry($dbEntry);
			kJobsManager::addProvisionProvideJob(null, $dbEntry, $jobData);
		}
		else
		{
			$dbEntry->setStatus(entryStatus::READY);
			$dbEntry->save();
		
			$liveAssets = assetPeer::retrieveByEntryId($dbEntry->getId(),array(assetType::LIVE));
			foreach ($liveAssets as $liveAsset){
				/* @var $liveAsset liveAsset */
				$liveAsset->setStatus(asset::ASSET_STATUS_READY);
				$liveAsset->save();
			}
		}
		
		myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_ENTRY_ADD, $dbEntry, $this->getPartnerId(), null, null, null, $dbEntry->getId());

		$liveStreamEntry->fromObject($dbEntry, $this->getResponseProfile());
		return $liveStreamEntry;
	}

	protected function prepareEntryForInsert(KalturaBaseEntry $entry, entry $dbEntry = null)
	{
		$dbEntry = parent::prepareEntryForInsert($entry, $dbEntry);
		/* @var $dbEntry LiveStreamEntry */
				
		if(in_array($entry->sourceType, array(KalturaSourceType::LIVE_STREAM, KalturaSourceType::LIVE_STREAM_ONTEXTDATA_CAPTIONS)))
		{
			if(!$entry->conversionProfileId)
			{
				$partner = $dbEntry->getPartner();
				if($partner)
					$dbEntry->setConversionProfileId($partner->getDefaultLiveConversionProfileId());
			}
		}
		
		return $dbEntry;
	}
	
	protected function getTemplateEntry($conversionProfileId, $templateEntryId)
	{
		if(!$templateEntryId && $conversionProfileId)
		{
			$conversionProfile = conversionProfile2Peer::retrieveByPk($conversionProfileId);
			if($conversionProfile)
				$templateEntryId = $conversionProfile->getDefaultEntryId();
				
		}
		if($templateEntryId)
		{
			$templateEntry = entryPeer::retrieveByPKNoFilter($templateEntryId, null, false);
			return $templateEntry;
		}
		
		return null;
	}
	
	/**
	 * Get live stream entry by ID.
	 * 
	 * @action get
	 * @param string $entryId Live stream entry id
	 * @param int $version Desired version of the data
	 * @return KalturaLiveStreamEntry The requested live stream entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	function getAction($entryId, $version = -1)
	{
		return $this->getEntry($entryId, $version, KalturaEntryType::LIVE_STREAM);
	}
	
	/**
	 * Authenticate live-stream entry against stream token and partner limitations
	 * 
	 * @action authenticate
	 * @param string $entryId Live stream entry id
	 * @param string $token Live stream broadcasting token
	 * @param string $hostname Media server host name
	 * @param KalturaEntryServerNodeType $mediaServerIndex Media server index primary / secondary
	 * @param string $applicationName the application to which entry is being broadcast
	 * @return KalturaLiveStreamEntry The authenticated live stream entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::LIVE_STREAM_INVALID_TOKEN
	 */
	function authenticateAction($entryId, $token, $hostname = null, $mediaServerIndex = null, $applicationName = null)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry || $dbEntry->getType() != entryType::LIVE_STREAM)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		
		/* @var $dbEntry LiveStreamEntry */
		if ($dbEntry->getStreamPassword() != $token)
			throw new KalturaAPIException(KalturaErrors::LIVE_STREAM_INVALID_TOKEN, $entryId);

		/*
		Patch for autenticate error while performing an immidiate stop/start. Checkup for duplicate streams moved to
		media-server for the moment. 
		if($dbEntry->isStreamAlreadyBroadcasting())
			throw new KalturaAPIException(KalturaErrors::LIVE_STREAM_ALREADY_BROADCASTING, $entryId, $mediaServer->getHostname());
		*/
		
		if($hostname && isset($mediaServerIndex))
			$this->setMediaServerWrapper($dbEntry, $mediaServerIndex, $hostname, KalturaEntryServerNodeStatus::AUTHENTICATED, $applicationName);
		
		$this->validateMaxStreamsNotReached($dbEntry);
		
		$entry = KalturaEntryFactory::getInstanceByType($dbEntry->getType());
		$entry->fromObject($dbEntry, $this->getResponseProfile());
		return $entry;
	}
	
	private function validateMaxStreamsNotReached(LiveEntry $liveEntry)
	{
		//Fetch all entries currently being streamed by partner
		$liveEntries = $this->getLiveEntriesForPartner($liveEntry);
		
		$maxPassthroughStreams = $this->getPartner()->getMaxLiveStreamInputs();
		if(!$maxPassthroughStreams)
			$maxPassthroughStreams = kConf::get('partner_max_live_stream_inputs', 'local', 10);
		KalturaLog::debug("Max Passthrough streams [$maxPassthroughStreams]");
		
		$maxTranscodedStreams = 0;
		if(PermissionPeer::isValidForPartner(PermissionName::FEATURE_KALTURA_LIVE_STREAM_TRANSCODE, $this->getPartnerId()))
		{
			$maxTranscodedStreams = $this->getPartner()->getMaxLiveStreamOutputs();
			if(!$maxTranscodedStreams)
				$maxTranscodedStreams = kConf::get('partner_max_live_stream_outputs', 'local', 10);
		}
		KalturaLog::debug("Max transcoded streams [$maxTranscodedStreams]");
		
		$entryConversionProfiles = array();
		$entryConversionProfiles[$liveEntry->getConversionProfileId()][] = $liveEntry->getId();
		foreach($liveEntries as $entry)
		{
			/* @var $entry LiveEntry */
			$entryConversionProfiles[$entry->getConversionProfileId()][] = $entry->getId();
		}
		
		$passthroughEntriesCount = 0;
		$transcodedEntriesCount = 0;
		foreach($entryConversionProfiles as $conversionProfileId => $entriesArray)
		{
			$isCloud = false;
			$flavorParamsConversionProfile = flavorParamsConversionProfilePeer::retrieveByConversionProfile($conversionProfileId);
			foreach($flavorParamsConversionProfile as $flavorParamConversionProfile)
			{
				/* @var $flavorParamConversionProfile flavorParamsConversionProfile */
				if($flavorParamConversionProfile->getOrigin() == KalturaAssetParamsOrigin::CONVERT)
				{
					$isCloud = true;
					break;
				}
			}
			
			if($isCloud)
				$transcodedEntriesCount += count($entriesArray);
			else
				$passthroughEntriesCount += count($entriesArray);
		}
		
		KalturaLog::debug("Live transcoded entries [$transcodedEntriesCount], max live transcoded streams [$maxTranscodedStreams]");
		if($transcodedEntriesCount > $maxTranscodedStreams)
			throw new KalturaAPIException(KalturaErrors::LIVE_STREAM_EXCEEDED_MAX_TRANSCODED, $liveEntry->getId());
		
		KalturaLog::debug("Live Passthrough entries [$passthroughEntriesCount], max live Passthrough streams [$maxPassthroughStreams]");
		if($passthroughEntriesCount > $maxPassthroughStreams)
			throw new KalturaAPIException(KalturaErrors::LIVE_STREAM_EXCEEDED_MAX_PASSTHRU, $liveEntry->getId());
	}
	
	private function getLiveEntriesForPartner(LiveEntry $liveEntry)
	{
		//Fetch all entries currently being streamed by partner
		$connectedEntryServerNodes  = EntryServerNodePeer::retrieveConnectedEntryServerNodesByPartner($liveEntry->getPartnerId(), $liveEntry->getId());
		
		if(!count($connectedEntryServerNodes))
			return array();
		
		$connectedLiveEntryIds = array();
		foreach($connectedEntryServerNodes as $connectedEntryServerNode)
			$connectedLiveEntryIds[] = $connectedEntryServerNode->getEntryId();
		
		return entryPeer::retrieveByPKs($connectedLiveEntryIds);
	}

	/**
	 * Update live stream entry. Only the properties that were set will be updated.
	 * 
	 * @action update
	 * @param string $entryId Live stream entry id to update
	 * @param KalturaLiveStreamEntry $liveStreamEntry Live stream entry metadata to update
	 * @return KalturaLiveStreamEntry The updated live stream entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @validateUser entry entryId edit
	 */
	function updateAction($entryId, KalturaLiveStreamEntry $liveStreamEntry)
	{
		$this->dumpApiRequest($entryId);
		return $this->updateEntry($entryId, $liveStreamEntry, KalturaEntryType::LIVE_STREAM);
	}

	/**
	 * Delete a live stream entry.
	 *
	 * @action delete
	 * @param string $entryId Live stream entry id to delete
	 * 
 	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
 	 * @validateUser entry entryId edit
	 */
	function deleteAction($entryId)
	{
		$this->deleteEntry($entryId, KalturaEntryType::LIVE_STREAM);
	}
	
	/**
	 * List live stream entries by filter with paging support.
	 * 
	 * @action list
     * @param KalturaLiveStreamEntryFilter $filter live stream entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @return KalturaLiveStreamListResponse Wrapper for array of live stream entries and total count
	 */
	function listAction(KalturaLiveStreamEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
	    if (!$filter)
			$filter = new KalturaLiveStreamEntryFilter();
			
	    $filter->typeEqual = KalturaEntryType::LIVE_STREAM;
	    list($list, $totalCount) = parent::listEntriesByFilter($filter, $pager);
	    
	    $newList = KalturaLiveStreamEntryArray::fromDbArray($list, $this->getResponseProfile());
		$response = new KalturaLiveStreamListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		return $response;
	}
	


	/**
	 * Update live stream entry thumbnail using a raw jpeg file
	 * 
	 * @action updateOfflineThumbnailJpeg
	 * @param string $entryId live stream entry id
	 * @param file $fileData Jpeg file data
	 * @return KalturaLiveStreamEntry The live stream entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::PERMISSION_DENIED_TO_UPDATE_ENTRY
	 */
	function updateOfflineThumbnailJpegAction($entryId, $fileData)
	{
		return parent::updateThumbnailJpegForEntry($entryId, $fileData, KalturaEntryType::LIVE_STREAM, entry::FILE_SYNC_ENTRY_SUB_TYPE_OFFLINE_THUMB);
	}
	
	/**
	 * Update entry thumbnail using url
	 * 
	 * @action updateOfflineThumbnailFromUrl
	 * @param string $entryId live stream entry id
	 * @param string $url file url
	 * @return KalturaLiveStreamEntry The live stream entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::PERMISSION_DENIED_TO_UPDATE_ENTRY
	 */
	function updateOfflineThumbnailFromUrlAction($entryId, $url)
	{
		return parent::updateThumbnailForEntryFromUrl($entryId, $url, KalturaEntryType::LIVE_STREAM, entry::FILE_SYNC_ENTRY_SUB_TYPE_OFFLINE_THUMB);
	}
	
	/**
	 * Delivering the status of a live stream (on-air/offline) if it is possible
	 * 
	 * @action isLive
	 * @param string $id ID of the live stream
	 * @param KalturaPlaybackProtocol $protocol protocol of the stream to test.
	 * @return bool
	 * @ksOptional
	 * 
	 * @throws KalturaErrors::LIVE_STREAM_STATUS_CANNOT_BE_DETERMINED
	 * @throws KalturaErrors::INVALID_ENTRY_ID
	 */
	public function isLiveAction ($id, $protocol)
	{
		if (!kCurrentContext::$ks)
		{
			kEntitlementUtils::initEntitlementEnforcement(null, false);
			$liveStreamEntry = kCurrentContext::initPartnerByEntryId($id);
			if (!$liveStreamEntry || $liveStreamEntry->getStatus() == entryStatus::DELETED)
				throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_ID, $id);

			// enforce entitlement
			$this->setPartnerFilters(kCurrentContext::getCurrentPartnerId());
		}
		else
		{
			$liveStreamEntry = entryPeer::retrieveByPK($id);
		}
		
		if (!$liveStreamEntry || ($liveStreamEntry->getType() != entryType::LIVE_STREAM))
			throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_ID, $id);

		if (!in_array($liveStreamEntry->getSource(), LiveEntry::$kalturaLiveSourceTypes))
			KalturaResponseCacher::setConditionalCacheExpiry(self::ISLIVE_ACTION_NON_KALTURA_LIVE_CONDITIONAL_CACHE_EXPIRY);

		/* @var $liveStreamEntry LiveStreamEntry */
	
		if(in_array($liveStreamEntry->getSource(), array(KalturaSourceType::LIVE_STREAM, KalturaSourceType::LIVE_STREAM_ONTEXTDATA_CAPTIONS)))
		{
			return $this->responseHandlingIsLive($liveStreamEntry->isCurrentlyLive());
		}
		
		$dpda= new DeliveryProfileDynamicAttributes();
		$dpda->setEntryId($id);
		$dpda->setFormat($protocol);
		
		switch ($protocol)
		{
			case KalturaPlaybackProtocol::HLS:
			case KalturaPlaybackProtocol::APPLE_HTTP:
				$url = $liveStreamEntry->getHlsStreamUrl('http');
				if($protocol == KalturaPlaybackProtocol::HLS)
					$hlsProtocols = array(KalturaPlaybackProtocol::HLS, KalturaPlaybackProtocol::APPLE_HTTP);
				else
					$hlsProtocols = array(KalturaPlaybackProtocol::APPLE_HTTP, KalturaPlaybackProtocol::HLS);

				foreach ($hlsProtocols as $hlsProtocol){
					$config = $liveStreamEntry->getLiveStreamConfigurationByProtocol($hlsProtocol, requestUtils::getProtocol());
					if ($config){
						$url = $config->getUrl();
						$protocol = $hlsProtocol;
						$dpda->setFormat($protocol);
						break;
					}
				}

				KalturaLog::info('Determining status of live stream URL [' .$url. ']');
				$urlManager = DeliveryProfilePeer::getLiveDeliveryProfileByHostName(parse_url($url, PHP_URL_HOST), $dpda);
				if($urlManager)
					return $this->responseHandlingIsLive($urlManager->isLive($url));

				break;
			case KalturaPlaybackProtocol::HDS:
			case KalturaPlaybackProtocol::AKAMAI_HDS:
				$config = $liveStreamEntry->getLiveStreamConfigurationByProtocol($protocol, requestUtils::getProtocol());
				if ($config)
				{
					$url = $config->getUrl();
					KalturaLog::info('Determining status of live stream URL [' .$url . ']');
					$urlManager = DeliveryProfilePeer::getLiveDeliveryProfileByHostName(parse_url($url, PHP_URL_HOST), $dpda);
					if($urlManager)
						return $this->responseHandlingIsLive($urlManager->isLive($url));
				}
				break;
		}
		
		throw new KalturaAPIException(KalturaErrors::LIVE_STREAM_STATUS_CANNOT_BE_DETERMINED, $protocol);
	}

	private function responseHandlingIsLive($isLive)
	{
		if (!$isLive){
			KalturaResponseCacher::setExpiry(self::ISLIVE_ACTION_CACHE_EXPIRY_WHEN_NOT_LIVE);
			KalturaResponseCacher::setHeadersCacheExpiry(self::ISLIVE_ACTION_CACHE_EXPIRY_WHEN_NOT_LIVE);
		} else {
			KalturaResponseCacher::setExpiry(self::ISLIVE_ACTION_CACHE_EXPIRY_WHEN_LIVE);
			KalturaResponseCacher::setHeadersCacheExpiry(self::ISLIVE_ACTION_CACHE_EXPIRY_WHEN_LIVE);
		}

		return $isLive;
	}


	/**
	 * Add new pushPublish configuration to entry
	 * 
	 * @action addLiveStreamPushPublishConfiguration
	 * @param string $entryId
	 * @param KalturaPlaybackProtocol $protocol
	 * @param string $url
	 * @param KalturaLiveStreamConfiguration $liveStreamConfiguration
	 * @return KalturaLiveStreamEntry
	 * @throws KalturaErrors::INVALID_ENTRY_ID
	 */
	public function addLiveStreamPushPublishConfigurationAction ($entryId, $protocol, $url = null, KalturaLiveStreamConfiguration $liveStreamConfiguration = null)
	{
		$this->dumpApiRequest($entryId);
		
		$entry = entryPeer::retrieveByPK($entryId);
		if (!$entry || $entry->getType() != entryType::LIVE_STREAM)
			throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_ID);
		
		//Should not allow usage of both $url and $liveStreamConfiguration
		if ($url && !is_null($liveStreamConfiguration))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN);
			
		/* @var $entry LiveEntry */
		$pushPublishConfigurations = $entry->getPushPublishPlaybackConfigurations();

		$configuration = null;
		if ($url)
		{
			$configuration = new kLiveStreamConfiguration();
			$configuration->setProtocol($protocol);
			$configuration->setUrl($url);
		}
		elseif (!is_null($liveStreamConfiguration))
		{
			$configuration = $liveStreamConfiguration->toInsertableObject();
			$configuration->setProtocol($protocol);
		}
		
		if ($configuration)
		{
			$pushPublishConfigurations[] = $configuration;
			$entry->setPushPublishPlaybackConfigurations($pushPublishConfigurations);
			$entry->save();
		}
		
		$apiEntry = KalturaEntryFactory::getInstanceByType($entry->getType());
		$apiEntry->fromObject($entry, $this->getResponseProfile());
		return $apiEntry;
	}
	
	/**
	 *Remove push publish configuration from entry
	 * 
	 * @action removeLiveStreamPushPublishConfiguration
	 * @param string $entryId
	 * @param KalturaPlaybackProtocol $protocol
	 * @return KalturaLiveStreamEntry
	 * @throws KalturaErrors::INVALID_ENTRY_ID
	 */
	public function removeLiveStreamPushPublishConfigurationAction ($entryId, $protocol)
	{
		$this->dumpApiRequest($entryId);
		
		$entry = entryPeer::retrieveByPK($entryId);
		if (!$entry || $entry->getType() != entryType::LIVE_STREAM)
			throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_ID);
		
		/* @var $entry LiveEntry */
		$pushPublishConfigurations = $entry->getPushPublishPlaybackConfigurations();
		foreach ($pushPublishConfigurations as $index => $config)
		{
			/* @var $config kLiveStreamConfiguration */
			if ($config->getProtocol() == $protocol)
			{
				unset ($pushPublishConfigurations[$index]);
			}
		}

		$entry->setPushPublishPlaybackConfigurations($pushPublishConfigurations);
		$entry->save();
		
		$apiEntry = KalturaEntryFactory::getInstanceByType($entry->getType());
		$apiEntry->fromObject($entry, $this->getResponseProfile());
		return $apiEntry;
	}
	
	/**
	 * Regenerate new secure token for liveStream
	 * 
	 * @action regenerateStreamToken
	 * @param string $entryId Live stream entry id to regenerate secure token for
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @validateUser entry entryId edit
	 */
	public function regenerateStreamTokenAction($entryId)
	{
		
		$this->dumpApiRequest($entryId);
	
		$liveEntry = entryPeer::retrieveByPK($entryId);
		if (!$liveEntry || $liveEntry->getType() != entryType::LIVE_STREAM)
			throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_ID);
		
		if (!in_array($liveEntry->getSourceType(), LiveEntry::$kalturaLiveSourceTypes))
			throw new KalturaAPIException(KalturaErrors::CANNOT_REGENERATE_STREAM_TOKEN_FOR_EXTERNAL_LIVE_STREAMS, $liveEntry->getSourceType());
		
		$password = sha1(md5(uniqid(rand(), true)));
		$password = substr($password, rand(0, strlen($password) - 8), 8);
		$liveEntry->setStreamPassword($password);

		$broadcastUrlManager = kBroadcastUrlManager::getInstance($liveEntry->getPartnerId());
		$broadcastUrlManager->setEntryBroadcastingUrls($liveEntry);

		$liveEntry->save();

		$entry = KalturaEntryFactory::getInstanceByType($liveEntry->getType());
		$entry->fromObject($liveEntry, $this->getResponseProfile());
		return $entry;
	}
}
