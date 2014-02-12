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
	 * @param KalturaMediaServerIndex $mediaServerIndex
	 * @param KalturaDataCenterContentResource $resource
	 * @param float $duration
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	function appendRecordingAction($entryId, $mediaServerIndex, KalturaDataCenterContentResource $resource, $duration)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry || !($dbEntry instanceof LiveEntry))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		$kResource = $resource->toObject();
		$target = $kResource->getLocalFilePath();
		if (!($resource instanceof KalturaServerFileResource))
		{
			$target = kConf::get('uploaded_segment_destination') . basename($kResource->getLocalFilePath());
			kFile::moveFile($kResource->getLocalFilePath(), $target);
			chgrp($target, kConf::get('content_group'));
			chmod($target, 0640);
		}
		kJobsManager::addConvertLiveSegmentJob(null, $dbEntry, $mediaServerIndex, $target, $duration);
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
		$dbEntry->save();
		
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