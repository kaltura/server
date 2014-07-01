<?php

/**
 * Base class for live streams and live channels
 *
 * @service liveStream
 * @package api
 * @subpackage services
 */
class KalturaLiveEntryService extends KalturaEntryService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

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
	 * Append recorded video to live entry
	 * 
	 * @action appendRecording
	 * @param string $entryId Live entry id
	 * @param string $assetId Live asset id
	 * @param KalturaMediaServerIndex $mediaServerIndex
	 * @param KalturaDataCenterContentResource $resource
	 * @param float $duration in seconds
	 * @return KalturaLiveEntry The updated live entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	function appendRecordingAction($entryId, $assetId, $mediaServerIndex, KalturaDataCenterContentResource $resource, $duration)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry || !($dbEntry instanceof LiveEntry))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		$currentDuration = $dbEntry->getLengthInMsecs();
		if(!$currentDuration)
			$currentDuration = 0;
		$currentDuration += ($duration * 1000);
		
		if($mediaServerIndex == KalturaMediaServerIndex::PRIMARY)
		{
			$dbEntry->setLengthInMsecs($currentDuration);
			$dbEntry->save();
		}
			
		$kResource = $resource->toObject();
		$target = $kResource->getLocalFilePath();
		if (!($resource instanceof KalturaServerFileResource))
		{
			$target = kConf::get('uploaded_segment_destination') . basename($kResource->getLocalFilePath());
			kFile::moveFile($kResource->getLocalFilePath(), $target);
			chgrp($target, kConf::get('content_group'));
			chmod($target, 0640);
		}
		kJobsManager::addConvertLiveSegmentJob(null, $dbEntry, $assetId, $mediaServerIndex, $target, $currentDuration);
		
		$entry = KalturaEntryFactory::getInstanceByType($dbEntry->getType());
		$entry->fromObject($dbEntry);
		return $entry;
	}

	/**
	 * Register media server to live entry
	 * 
	 * @action registerMediaServer
	 * @param string $entryId Live entry id
	 * @param string $hostname Media server host name
	 * @param KalturaMediaServerIndex $mediaServerIndex Media server index primary / secondary
	 * @return KalturaLiveEntry The updated live entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::MEDIA_SERVER_NOT_FOUND
	 */
	function registerMediaServerAction($entryId, $hostname, $mediaServerIndex)
	{
		$entryDc = substr($entryId, 0, 1);
		if($entryDc != kDataCenterMgr::getCurrentDcId())
		{
			$remoteDCHost = kDataCenterMgr::getRemoteDcExternalUrlByDcId($entryDc);
			kFileUtils::dumpApiRequest($remoteDCHost);
		}
		
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry || !($dbEntry instanceof LiveEntry))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		
		$dbEntry->setMediaServer($mediaServerIndex, $hostname);
		$dbEntry->setRedirectEntryId(null);
		
		if(is_null($dbEntry->getFirstBroadcast())) 
				$dbEntry->setFirstBroadcast(time());
		
		$dbEntry->save();
		
		if($mediaServerIndex == MediaServerIndex::PRIMARY && $dbEntry->getRecordStatus() == RecordStatus::ENABLED && !$dbEntry->getRecordedEntryId())
		{
			$recordedEntry = new entry();
			$recordedEntry->setType(entryType::MEDIA_CLIP);
			$recordedEntry->setMediaType(entry::ENTRY_MEDIA_TYPE_VIDEO);
			$recordedEntry->setRootEntryId($entryId);
			$recordedEntry->setName($dbEntry->getName());
			$recordedEntry->setDescription($dbEntry->getDescription());
			$recordedEntry->setSourceType(EntrySourceType::RECORDED_LIVE);
			$recordedEntry->setAccessControlId($dbEntry->getAccessControlId());
			$recordedEntry->setConversionProfileId($dbEntry->getConversionProfileId());
			$recordedEntry->save();
		}
		
		$entry = KalturaEntryFactory::getInstanceByType($dbEntry->getType());
		$entry->fromObject($dbEntry);
		return $entry;
	}

	/**
	 * Unregister media server from live entry
	 * 
	 * @action unregisterMediaServer
	 * @param string $entryId Live entry id
	 * @param string $hostname Media server host name
	 * @param KalturaMediaServerIndex $mediaServerIndex Media server index primary / secondary
	 * @return KalturaLiveEntry The updated live entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::MEDIA_SERVER_NOT_FOUND
	 */
	function unregisterMediaServerAction($entryId, $hostname, $mediaServerIndex)
	{
		$entryDc = substr($entryId, 0, 1);
		if($entryDc != kDataCenterMgr::getCurrentDcId())
		{
			$remoteDCHost = kDataCenterMgr::getRemoteDcExternalUrlByDcId($entryDc);
			kFileUtils::dumpApiRequest($remoteDCHost);
		}
		
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry || !($dbEntry instanceof LiveEntry))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		
		$dbEntry->unsetMediaServer($mediaServerIndex, $hostname);
		
		if(!$dbEntry->hasMediaServer() && $dbEntry->getRecordedEntryId())
		{
			$dbEntry->setRedirectEntryId($dbEntry->getRecordedEntryId());
		}
		
		$dbEntry->save();
		
		$entry = KalturaEntryFactory::getInstanceByType($dbEntry->getType());
		$entry->fromObject($dbEntry);
		return $entry;
	}

	/**
	 * Validates all registered media servers
	 * 
	 * @action validateRegisteredMediaServers
	 * @param string $entryId Live entry id
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	function validateRegisteredMediaServersAction($entryId)
	{
		$entryDc = substr($entryId, 0, 1);
		if($entryDc != kDataCenterMgr::getCurrentDcId())
		{
			$remoteDCHost = kDataCenterMgr::getRemoteDcExternalUrlByDcId($entryDc);
			kFileUtils::dumpApiRequest($remoteDCHost);
		}
		
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry || !($dbEntry instanceof LiveEntry))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		
		/* @var $dbEntry LiveEntry */
		if($dbEntry->validateMediaServers())
			$dbEntry->save();	
	}
}