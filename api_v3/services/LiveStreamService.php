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
	
		$dbEntry = $this->prepareEntryForInsert($liveStreamEntry);
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
				
			$dbEntry->save();
			
			$broadcastUrlManager = kBroadcastUrlManager::getInstance($dbEntry->getPartnerId());
			$broadcastUrlManager->setEntryBroadcastingUrls($dbEntry);
		}
		
		return $dbEntry;
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
		
		// fetch current stream live params
		$liveParamsIds = flavorParamsConversionProfilePeer::getFlavorIdsByProfileId($dbEntry->getConversionProfileId());
		$usedLiveParamsIds = array();
		foreach($liveParamsIds as $liveParamsId)
		{
			$usedLiveParamsIds[$liveParamsId] = array($entryId);
		}
			
		// fetch all live entries that currently are live
		$baseCriteria = KalturaCriteria::create(entryPeer::OM_CLASS);
		$filter = new entryFilter();
		$filter->setIsLive(true);
		$filter->setIdNotIn(array($entryId));
		$filter->setPartnerSearchScope(baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE);
		$filter->attachToCriteria($baseCriteria);
		
		$entries = entryPeer::doSelect($baseCriteria);
	
		$maxInputStreams = $this->getPartner()->getMaxLiveStreamInputs();
		if(!$maxInputStreams)
			$maxInputStreams = kConf::get('partner_max_live_stream_inputs', 'local', 10);
		KalturaLog::debug("Max live stream inputs [$maxInputStreams]");
			
		$maxTranscodedStreams = 0;
		if(PermissionPeer::isValidForPartner(PermissionName::FEATURE_KALTURA_LIVE_STREAM_TRANSCODE, $this->getPartnerId()))
		{
			$maxTranscodedStreams = $this->getPartner()->getMaxLiveStreamOutputs();
			if(!$maxTranscodedStreams)
				$maxTranscodedStreams = kConf::get('partner_max_live_stream_outputs', 'local', 10);
		}
		KalturaLog::debug("Max live stream outputs [$maxTranscodedStreams]");
		
		$totalInputStreams = count($entries) + 1;
		if($totalInputStreams > ($maxInputStreams + $maxTranscodedStreams))
		{
			KalturaLog::debug("Live input stream [$totalInputStreams]");
			throw new KalturaAPIException(KalturaErrors::LIVE_STREAM_EXCEEDED_MAX_PASSTHRU, $entryId);
		}
		
		$entryIds = array($entryId);
		foreach($entries as $liveEntry)
		{
			/* @var $liveEntry LiveEntry */
			$entryIds[] = $liveEntry->getId();
			$liveParamsIds = array_map('intval', explode(',', $liveEntry->getFlavorParamsIds()));
			
			foreach($liveParamsIds as $liveParamsId)
			{
				if(isset($usedLiveParamsIds[$liveParamsId]))
				{
					$usedLiveParamsIds[$liveParamsId][] = $liveEntry->getId();
				}
				else
				{
					$usedLiveParamsIds[$liveParamsId] = array($liveEntry->getId());
				}
			}
		}
			
		$liveParams = assetParamsPeer::retrieveByPKs(array_keys($usedLiveParamsIds));
		$passthruEntries = null;
		$transcodedEntries = null;
		foreach($liveParams as $liveParamsItem)
		{
			/* @var $liveParamsItem LiveParams */
			if($liveParamsItem->hasTag(liveParams::TAG_INGEST))
			{
				$passthruEntries = array_intersect(is_array($passthruEntries) ? $passthruEntries : $entryIds, $usedLiveParamsIds[$liveParamsItem->getId()]);
			}
			else
			{
				$transcodedEntries = array_intersect(is_array($transcodedEntries) ? $transcodedEntries : $entryIds, $usedLiveParamsIds[$liveParamsItem->getId()]);
			}
		}
		$passthruEntries = array_diff($passthruEntries, $transcodedEntries);
		
		$passthruEntriesCount = count($passthruEntries);
		$transcodedEntriesCount = count($transcodedEntries);
		
		KalturaLog::debug("Live transcoded entries [$transcodedEntriesCount], max live transcoded streams [$maxTranscodedStreams]");
		if($transcodedEntriesCount > $maxTranscodedStreams)
			throw new KalturaAPIException(KalturaErrors::LIVE_STREAM_EXCEEDED_MAX_TRANSCODED, $entryId);
		
		$maxInputStreams += ($maxTranscodedStreams - $transcodedEntriesCount);
		KalturaLog::debug("Live params inputs [$passthruEntriesCount], max live stream inputs [$maxInputStreams]");
		if($passthruEntriesCount > $maxInputStreams)
			throw new KalturaAPIException(KalturaErrors::LIVE_STREAM_EXCEEDED_MAX_PASSTHRU, $entryId);

		$entry = KalturaEntryFactory::getInstanceByType($dbEntry->getType());
		$entry->fromObject($dbEntry, $this->getResponseProfile());
		return $entry;
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
			return $this->responseHandlingIsLive($liveStreamEntry->hasMediaServer());
		}
		
		$dpda= new DeliveryProfileDynamicAttributes();
		$dpda->setEntryId($id);
		$dpda->setFormat($protocol);
		
		switch ($protocol)
		{
			case KalturaPlaybackProtocol::HLS:
			case KalturaPlaybackProtocol::APPLE_HTTP:
				$url = $liveStreamEntry->getHlsStreamUrl();
				
				foreach (array(KalturaPlaybackProtocol::HLS, KalturaPlaybackProtocol::APPLE_HTTP) as $hlsProtocol){
					$config = $liveStreamEntry->getLiveStreamConfigurationByProtocol($hlsProtocol, requestUtils::getProtocol());
					if ($config){
						$url = $config->getUrl();
						$protocol = $hlsProtocol;
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
}
