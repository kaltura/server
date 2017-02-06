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
	//amount of time for attempting to grab kLock
	const KLOCK_CREATE_RECORDED_ENTRY_GRAB_TIMEOUT = 0.1;
	
	//amount of time for holding kLock
	const KLOCK_CREATE_RECORDED_ENTRY_HOLD_TIMEOUT = 3;
  
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		// KAsyncValidateLiveMediaServers lists all live entries of all partners
		if($this->getPartnerId() == Partner::BATCH_PARTNER_ID && $actionName == 'list')
			myPartnerUtils::resetPartnerFilter('entry');
			
		if (in_array($this->getPartner()->getStatus(), array (Partner::PARTNER_STATUS_CONTENT_BLOCK, Partner::PARTNER_STATUS_FULL_BLOCK)))
		{
			throw new kCoreException("Partner blocked", kCoreException::PARTNER_BLOCKED);
		}
	}
	
	
	protected function partnerRequired($actionName)
	{
		if ($actionName === 'isLive') {
			return false;
		}
		return parent::partnerRequired($actionName);
	}

	function dumpApiRequest($entryId, $onlyIfAvailable = true)
	{
		$entryDc = substr($entryId, 0, 1);
		if($entryDc != kDataCenterMgr::getCurrentDcId())
		{
			$remoteDCHost = kDataCenterMgr::getRemoteDcExternalUrlByDcId($entryDc);
			kFileUtils::dumpApiRequest($remoteDCHost, $onlyIfAvailable);
		}		
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
	 * @param bool $isLastChunk Is this the last recorded chunk in the current session (i.e. following a stream stop event)
	 * @return KalturaLiveEntry The updated live entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	function appendRecordingAction($entryId, $assetId, $mediaServerIndex, KalturaDataCenterContentResource $resource, $duration, $isLastChunk = false)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry || !($dbEntry instanceof LiveEntry))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		$dbAsset = assetPeer::retrieveById($assetId);
		if (!$dbAsset || !($dbAsset instanceof liveAsset))
			throw new KalturaAPIException(KalturaErrors::ASSET_ID_NOT_FOUND, $assetId);
			
		$lastDuration = $dbEntry->getLengthInMsecs();
		if(!$lastDuration)
			$lastDuration = 0;

		$liveSegmentDurationInMsec = (int)($duration * 1000);
		$currentDuration = $lastDuration + $liveSegmentDurationInMsec;
		
		$maxRecordingDuration = (kConf::get('max_live_recording_duration_hours') + 1) * 60 * 60 * 1000;
		if($currentDuration > $maxRecordingDuration)
		{
			KalturaLog::err("Entry [$entryId] duration [" . $dbEntry->getLengthInMsecs() . "] and current duration [$currentDuration] is more than max allwoed duration [$maxRecordingDuration]");
			throw new KalturaAPIException(KalturaErrors::LIVE_STREAM_EXCEEDED_MAX_RECORDED_DURATION, $entryId);
		}
			
		$kResource = $resource->toObject();
		$filename = $kResource->getLocalFilePath();
		if (!($resource instanceof KalturaServerFileResource))
		{
			$filename = kConf::get('uploaded_segment_destination') . basename($kResource->getLocalFilePath());
			kFile::moveFile($kResource->getLocalFilePath(), $filename);
			chgrp($filename, kConf::get('content_group'));
			chmod($filename, 0640);
		}

		if($dbAsset->hasTag(assetParams::TAG_RECORDING_ANCHOR) && $mediaServerIndex == KalturaMediaServerIndex::PRIMARY)
		{
			$dbEntry->setLengthInMsecs($currentDuration);

			// Extract the exact video segment duration from the recorded file
			$mediaInfoParser = new KMediaInfoMediaParser($filename, kConf::get('bin_path_mediainfo'));
			$recordedSegmentDurationInMS = $mediaInfoParser->getMediaInfo()->videoDuration;

			KalturaLog::debug("about to call amfParser->getMediaInfo()");
			// Extract AMF data from all data frames in the segment
			$amfParser = new KAMFMediaInfoParser($filename, kConf::get('bin_path_ffprobe'));
			$AMFs = $amfParser->getAMFInfo();

			$recordedSegmentsInfo = $dbEntry->getRecordedSegmentsInfo();
			$recordedSegmentsInfo->addSegment( $recordedSegmentDurationInMS, $AMFs);
			$dbEntry->setRecordedSegmentsInfo( $recordedSegmentsInfo );

			if ( $isLastChunk )
			{
				// Save last elapsed recording time
				$dbEntry->setLastElapsedRecordingTime( $currentDuration );
			}

			$dbEntry->save();
		}

		kJobsManager::addConvertLiveSegmentJob(null, $dbAsset, $mediaServerIndex, $filename, $currentDuration);
		
		if($mediaServerIndex == KalturaMediaServerIndex::PRIMARY)
		{
			if(!$dbEntry->getRecordedEntryId())
			{
				$this->createRecordedEntry($dbEntry, $mediaServerIndex);
			}
			
			$recordedEntry = entryPeer::retrieveByPK($dbEntry->getRecordedEntryId());
			if($recordedEntry)
			{
				$this->ingestAsset($recordedEntry, $dbAsset, $filename);
			}
		}
		
		$entry = KalturaEntryFactory::getInstanceByType($dbEntry->getType());
		$entry->fromObject($dbEntry, $this->getResponseProfile());
		return $entry;
	}

	private function ingestAsset(entry $entry, $dbAsset, $filename)
	{
		$flavorParamsId = $dbAsset->getFlavorParamsId();
		$flavorParams = assetParamsPeer::retrieveByPKNoFilter($flavorParamsId);
		
		// is first chunk
		$recordedAsset = assetPeer::retrieveByEntryIdAndParams($entry->getId(), $flavorParamsId);
		if($recordedAsset)
		{
			KalturaLog::info("Asset [" . $recordedAsset->getId() . "] of flavor params id [$flavorParamsId] already exists");
			return;
		}
		
		// create asset
		$recordedAsset = assetPeer::getNewAsset(assetType::FLAVOR);
		$recordedAsset->setPartnerId($entry->getPartnerId());
		$recordedAsset->setEntryId($entry->getId());
		$recordedAsset->setStatus(asset::FLAVOR_ASSET_STATUS_QUEUED);
		$recordedAsset->setFlavorParamsId($flavorParams->getId());
		$recordedAsset->setFromAssetParams($flavorParams);
		if ( $dbAsset->hasTag(assetParams::TAG_RECORDING_ANCHOR) )
		{
			$recordedAsset->addTags(array(assetParams::TAG_RECORDING_ANCHOR));
		}
		
		if($flavorParams->hasTag(assetParams::TAG_SOURCE))
		{
			$recordedAsset->setIsOriginal(true);
		}
		
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		if($ext)
		{
			$recordedAsset->setFileExt($ext);
		}
		
		$recordedAsset->save();
		
		// create file sync
		$recordedAssetKey = $recordedAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		kFileSyncUtils::moveFromFile($filename, $recordedAssetKey, true, true);
		
		kEventsManager::raiseEvent(new kObjectAddedEvent($recordedAsset));
	}

	/**
	 * Register media server to live entry
	 * 
	 * @action registerMediaServer
	 * @param string $entryId Live entry id
	 * @param string $hostname Media server host name
	 * @param KalturaMediaServerIndex $mediaServerIndex Media server index primary / secondary
	 * @param string $applicationName the application to which entry is being broadcast
	 * @return KalturaLiveEntry The updated live entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::MEDIA_SERVER_NOT_FOUND
	 */
	function registerMediaServerAction($entryId, $hostname, $mediaServerIndex, $applicationName = null)
	{
		$this->dumpApiRequest($entryId);
		
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry || !($dbEntry instanceof LiveEntry))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		
		$dbEntry->setMediaServer($mediaServerIndex, $hostname, $applicationName);
		$dbEntry->setRedirectEntryId(null);
		
		if($dbEntry->save())
		{
			if($mediaServerIndex == MediaServerIndex::PRIMARY && $dbEntry->getRecordStatus())
			{
				$createRecordedEntry = false;
				if(!$dbEntry->getRecordedEntryId())
				{
					$createRecordedEntry = true;
				}
				elseif($dbEntry->getRecordStatus() == RecordStatus::PER_SESSION && ($dbEntry->getLastBroadcastEndTime() + kConf::get('live_session_reconnect_timeout', 'local', 180) < time()))
				{
					$createRecordedEntry = true;
				}
				
				if($createRecordedEntry)
					$this->createRecordedEntry($dbEntry, $mediaServerIndex);
			}
		}
		
		$entry = KalturaEntryFactory::getInstanceByType($dbEntry->getType());
		$entry->fromObject($dbEntry, $this->getResponseProfile());
		return $entry;
	}

	/**
	 * @param LiveEntry $dbEntry
	 * @return entry
	 */
	private function createRecordedEntry(LiveEntry $dbEntry, $mediaServerIndex)
	{
		$lock = kLock::create("live_record_" . $dbEntry->getId());
		
	    if ($lock && !$lock->lock(self::KLOCK_CREATE_RECORDED_ENTRY_GRAB_TIMEOUT , self::KLOCK_CREATE_RECORDED_ENTRY_HOLD_TIMEOUT))
	    {
	    	return;
	    }
	    
	    // If while we were waiting for the lock, someone has updated the recorded entry id - we should use it.
	    $dbEntry->reload();
	    if(($dbEntry->getRecordStatus() != RecordStatus::PER_SESSION) && ($dbEntry->getRecordedEntryId())) {
	    	$lock->unlock();
	    	$recordedEntry = entryPeer::retrieveByPK($dbEntry->getRecordedEntryId()); 
	    	return $recordedEntry;
	    }
	    
	    $recordedEntry = null;
     	try{		
			$recordedEntryName = $dbEntry->getName();
			if($dbEntry->getRecordStatus() == RecordStatus::PER_SESSION)
				$recordedEntryName .= ' ' . ($dbEntry->getRecordedEntryIndex() + 1);
				
			$recordedEntry = new entry();
			$recordedEntry->setType(entryType::MEDIA_CLIP);
			$recordedEntry->setMediaType(entry::ENTRY_MEDIA_TYPE_VIDEO);
			$recordedEntry->setRootEntryId($dbEntry->getId());
			$recordedEntry->setName($recordedEntryName);
			$recordedEntry->setDescription($dbEntry->getDescription());
			$recordedEntry->setSourceType(EntrySourceType::RECORDED_LIVE);
			$recordedEntry->setAccessControlId($dbEntry->getAccessControlId());
			$recordedEntry->setConversionProfileId($dbEntry->getConversionProfileId());
			$recordedEntry->setKuserId($dbEntry->getKuserId());
			$recordedEntry->setPartnerId($dbEntry->getPartnerId());
			$recordedEntry->setModerationStatus($dbEntry->getModerationStatus());
			$recordedEntry->setIsRecordedEntry(true);
			$recordedEntry->setTags($dbEntry->getTags());

			$recordedEntry->save();

			$dbEntry->setRecordedEntryId($recordedEntry->getId());
			$dbEntry->save();
			
			$assets = assetPeer::retrieveByEntryId($dbEntry->getId(), array(assetType::LIVE));
			foreach($assets as $asset)
			{
				/* @var $asset liveAsset */
				$asset->incLiveSegmentVersion($mediaServerIndex);
				$asset->save();
			}
		}
		catch(Exception $e){
       		$lock->unlock();
       		throw $e;
		}
		
		$lock->unlock();
		
		return $recordedEntry;
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
		$this->dumpApiRequest($entryId);
		
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry || !($dbEntry instanceof LiveEntry))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		
		$dbEntry->unsetMediaServer($mediaServerIndex, $hostname);
		
		if(!$dbEntry->hasMediaServer() && $dbEntry->getRecordedEntryId())
		{
			$dbEntry->setRedirectEntryId($dbEntry->getRecordedEntryId());
		}

		if ( count( $dbEntry->getMediaServers() ) == 0 )
		{
			// Reset currentBroadcastStartTime
			// Note that this value is set in the media-server, at KalturaLiveManager::onPublish()
			if ( $dbEntry->getCurrentBroadcastStartTime() )
			{
				$dbEntry->setCurrentBroadcastStartTime( 0 );
			}
		}

		$dbEntry->save();
		
		$entry = KalturaEntryFactory::getInstanceByType($dbEntry->getType());
		$entry->fromObject($dbEntry, $this->getResponseProfile());
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
		KalturaResponseCacher::disableCache();
		$this->dumpApiRequest($entryId, false);
		
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry || !($dbEntry instanceof LiveEntry))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		
		/* @var $dbEntry LiveEntry */
		if($dbEntry->validateMediaServers())
			$dbEntry->save();	
	}
}