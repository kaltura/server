<?php

/**
 * Live Stream service lets you manage live stream entries
 *
 * @service liveStream
 * @package api
 * @subpackage services
 */
class LiveStreamService extends KalturaEntryService
{
	const ISLIVE_ACTION_CACHE_EXPIRY = 30;
	const HLS_LIVE_STREAM_CONTENT_TYPE = 'application/vnd.apple.mpegurl';

	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		if($this->getPartnerId() > 0 && !PermissionPeer::isValidForPartner(PermissionName::FEATURE_LIVE_STREAM, $this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
			
		// KAsyncValidateLiveMediaServers lists all live entries of all partners
		if($this->getPartnerId() == Partner::BATCH_PARTNER_ID && $actionName == 'list')
			myPartnerUtils::resetPartnerFilter('entry');
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
		if($sourceType)
			$liveStreamEntry->sourceType = $sourceType;
	
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

		$liveStreamEntry->fromObject($dbEntry);
		return $liveStreamEntry;
	}

	protected function prepareEntryForInsert(KalturaBaseEntry $entry, entry $dbEntry = null)
	{
		$dbEntry = parent::prepareEntryForInsert($entry, $dbEntry);
		/* @var $dbEntry LiveStreamEntry */
				
		if($entry->sourceType == KalturaSourceType::LIVE_STREAM)
		{
			if(!$entry->conversionProfileId)
			{
				$partner = $dbEntry->getPartner();
				if($partner)
					$dbEntry->setConversionProfileId($partner->getDefaultLiveConversionProfileId());
			}
				
			$dbEntry->save();
			
			$broadcastUrlManager = kBroadcastUrlManager::getInstance($dbEntry->getPartnerId());
			$dbEntry->setPrimaryBroadcastingUrl($broadcastUrlManager->getBroadcastUrl($dbEntry, kBroadcastUrlManager::PRIMARY_MEDIA_SERVER_INDEX));
			$dbEntry->setSecondaryBroadcastingUrl($broadcastUrlManager->getBroadcastUrl($dbEntry, kBroadcastUrlManager::SECONDARY_MEDIA_SERVER_INDEX));
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
	 * Append recorded video to live stream entry
	 * 
	 * @action appendRecording
	 * @param string $entryId Live stream entry id
	 * @param KalturaMediaServerIndex $mediaServerIndex
	 * @param KalturaServerFileResource $resource
	 * @param float $duration
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	function appendRecordingAction($entryId, $mediaServerIndex, KalturaServerFileResource $resource, $duration)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry || !($dbEntry instanceof LiveEntry))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		$kResource = $resource->toObject();
		kJobsManager::addConvertLiveSegmentJob(null, $dbEntry, $mediaServerIndex, $kResource->getLocalFilePath(), $duration);
	}

	/**
	 * Register media server to live-stream entry
	 * 
	 * @action registerMediaServer
	 * @param string $entryId Live stream entry id
	 * @param string $hostname Media server host name
	 * @param KalturaMediaServerIndex $mediaServerIndex Media server index primary / secondary
	 * @return KalturaLiveStreamEntry The updated live stream entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::MEDIA_SERVER_NOT_FOUND
	 */
	function registerMediaServerAction($entryId, $hostname, $mediaServerIndex)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry || $dbEntry->getType() != entryType::LIVE_STREAM)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		
		$dbMediaServer = MediaServerPeer::retrieveByHostname($hostname);
		if (!$dbMediaServer)
			throw new KalturaAPIException(KalturaErrors::MEDIA_SERVER_NOT_FOUND, $hostname);
			
		$dbEntry->setMediaServer($mediaServerIndex, $dbMediaServer->getId(), $hostname);
		$dbEntry->save();
		
		$entry = KalturaEntryFactory::getInstanceByType($dbEntry->getType());
		$entry->fromObject($dbEntry);
		return $entry;
	}

	/**
	 * Authenticate live-stream entry against stream token and partner limitations
	 * 
	 * @action authenticate
	 * @param string $entryId Live stream entry id
	 * @param string $token Live stream broadcasting token
	 * @return KalturaLiveStreamEntry The authenticated live stream entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::LIVE_STREAM_INVALID_TOKEN
	 */
	function authenticateAction($entryId, $token)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry || $dbEntry->getType() != entryType::LIVE_STREAM)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		
		/* @var $dbEntry LiveStreamEntry */
		if ($dbEntry->getStreamPassword() != $token)
			throw new KalturaAPIException(KalturaErrors::LIVE_STREAM_INVALID_TOKEN, $entryId);

		// fetch current stream live params
		$liveParamsIds = flavorParamsConversionProfilePeer::getFlavorIdsByProfileId($dbEntry->getConversionProfileId());
		$liveParamsCounts = array();
		
		// counting the current stream live params
		foreach($liveParamsIds as $liveParamsId)
			$liveParamsCounts[$liveParamsId] = 1;
		
			
		// fetch all live entries that currently are live
		$baseCriteria = KalturaCriteria::create(entryPeer::OM_CLASS);
		$filter = new entryFilter();
		$filter->setIsLive(true);
		$filter->setPartnerSearchScope(baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE);
		$filter->attachToCriteria($baseCriteria);
		
		$entries = entryPeer::doSelect($baseCriteria);
		$liveParamsInputs = count($entries) + 1;
		$maxLiveStreamInputs = $this->getPartner()->getMaxLiveStreamInputs();
		if(is_null($maxLiveStreamInputs))
			$maxLiveStreamInputs = kConf::get('partner_max_live_stream_inputs', 'local', 10);
			
		KalturaLog::debug("live params inputs [$liveParamsInputs], max live stream inputs [$maxLiveStreamInputs]");
		if($liveParamsInputs > $maxLiveStreamInputs)
			throw new KalturaAPIException(KalturaErrors::LIVE_STREAM_EXCEEDED_MAX_INPUTS, $entryId);
			
		foreach($entries as $liveEntry)
		{
			/* @var $liveEntry LiveEntry */
			$liveParamsIds = explode(',', $liveEntry->getFlavorParamsIds());
			
			foreach($liveParamsIds as $liveParamsId)
			{
				if(isset($liveParamsCounts[$liveParamsId]))
					$liveParamsCounts[$liveParamsId]++;
				else
					$liveParamsCounts[$liveParamsId] = 1;
			}
		}
			
		$liveParams = assetParamsPeer::retrieveByPKs(array_keys($liveParamsCounts));
		$liveParamsOutputs = 0;
		foreach($liveParams as $liveParamsItem)
		{
			/* @var $liveParamsItem LiveParams */
			if(!$liveParamsItem->hasTag(liveParams::TAG_SOURCE))
			{
				KalturaLog::debug("Output live params [" . $liveParamsItem->getId() . "] [" . $liveParamsItem->getSystemName() . "]: " . $liveParamsCounts[$liveParamsItem->getId()]);
				$liveParamsOutputs += $liveParamsCounts[$liveParamsItem->getId()];
			}
		}
		
		$maxLiveStreamOutputs = $this->getPartner()->getMaxLiveStreamOutputs();
		if(is_null($maxLiveStreamOutputs))
			$maxLiveStreamOutputs = kConf::get('partner_max_live_stream_outputs', 'local', 30);
			
		KalturaLog::debug("live params outputs [$liveParamsOutputs], max live stream outputs [$maxLiveStreamOutputs]");
		if($liveParamsOutputs > $maxLiveStreamOutputs)
			throw new KalturaAPIException(KalturaErrors::LIVE_STREAM_EXCEEDED_MAX_OUTPUTS, $entryId);
		
		$entry = KalturaEntryFactory::getInstanceByType($dbEntry->getType());
		$entry->fromObject($dbEntry);
		return $entry;
	}

	/**
	 * Unregister media server from live-stream entry
	 * 
	 * @action unregisterMediaServer
	 * @param string $entryId Live stream entry id
	 * @param string $hostname Media server host name
	 * @param KalturaMediaServerIndex $mediaServerIndex Media server index primary / secondary
	 * @return KalturaLiveStreamEntry The updated live stream entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::MEDIA_SERVER_NOT_FOUND
	 */
	function unregisterMediaServerAction($entryId, $hostname, $mediaServerIndex)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry || $dbEntry->getType() != entryType::LIVE_STREAM)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		
		$dbMediaServer = MediaServerPeer::retrieveByHostname($hostname);
		if (!$dbMediaServer)
			throw new KalturaAPIException(KalturaErrors::MEDIA_SERVER_NOT_FOUND, $hostname);
			
		$dbEntry->unsetMediaServer($mediaServerIndex, $dbMediaServer->getId());
		$dbEntry->save();
		
		$entry = KalturaEntryFactory::getInstanceByType($dbEntry->getType());
		$entry->fromObject($dbEntry);
		return $entry;
	}

	/**
	 * Validates all registered media servers
	 * 
	 * @action validateRegisteredMediaServers
	 * @param string $entryId Live stream entry id
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	function validateRegisteredMediaServersAction($entryId)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry || $dbEntry->getType() != entryType::LIVE_STREAM)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		
		/* @var $dbEntry LiveEntry */
		if($dbEntry->validateMediaServers())
			$dbEntry->save();	
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
	    
	    $newList = KalturaLiveStreamEntryArray::fromEntryArray($list);
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
		KalturaResponseCacher::setExpiry(self::ISLIVE_ACTION_CACHE_EXPIRY);
		kApiCache::disableConditionalCache();
		if (!kCurrentContext::$ks)
		{
			$liveStreamEntry = kCurrentContext::initPartnerByEntryId($id);
			if (!$liveStreamEntry || $liveStreamEntry->getStatus() == entryStatus::DELETED)
				throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_ID, $id);

			// enforce entitlement
			$this->setPartnerFilters(kCurrentContext::getCurrentPartnerId());
			kEntitlementUtils::initEntitlementEnforcement(null, false);
		}
		else
		{
			$liveStreamEntry = entryPeer::retrieveByPK($id);
		}
		
		if (!$liveStreamEntry || ($liveStreamEntry->getType() != entryType::LIVE_STREAM))
			throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_ID, $id);
		
		/* @var $liveStreamEntry LiveStreamEntry */
	
		if($liveStreamEntry->getSource() == KalturaSourceType::LIVE_STREAM)
		{
			return $liveStreamEntry->hasMediaServer();
		}
		
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
				$urlManager = kUrlManager::getUrlManagerByCdn(parse_url($url, PHP_URL_HOST), $id);
				$urlManager->setProtocol($protocol);
				return $urlManager->isLive($url);
				
			case KalturaPlaybackProtocol::HDS:
			case KalturaPlaybackProtocol::AKAMAI_HDS:
				$config = $liveStreamEntry->getLiveStreamConfigurationByProtocol($protocol, requestUtils::getProtocol());
				if ($config)
				{
					$url = $config->getUrl();
					KalturaLog::info('Determining status of live stream URL [' .$url . ']');
					$urlManager = kUrlManager::getUrlManagerByCdn(parse_url($url, PHP_URL_HOST), $id);
					$urlManager->setProtocol($protocol);
					return $urlManager->isLive($url);
				}
				break;
		}
		
		throw new KalturaAPIException(KalturaErrors::LIVE_STREAM_STATUS_CANNOT_BE_DETERMINED, $protocol);
	}
}