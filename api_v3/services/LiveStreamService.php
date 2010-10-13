<?php

/**
 * Live Stream service lets you manage live stream channels
 *
 * @service liveStream
 * @package api
 * @subpackage services
 */
class LiveStreamService extends KalturaEntryService
{
	const DEFAULT_BITRATE = 300;
	const DEFAULT_WIDTH = 320;
	const DEFAULT_HEIGHT = 240;
	
	/**
	 * Adds new live stream entry.
	 * The entry will be queued for provision.
	 * 
	 * @action add
	 * @param KalturaLiveStreamAdminEntry $liveStreamEntry Live stream entry metadata  
	 * @return KalturaLiveStreamAdminEntry The new live stream entry
	 * 
	 * @throws KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL
	 */
	function addAction(KalturaLiveStreamAdminEntry $liveStreamEntry, KalturaSourceType $sourceType = null)
	{
		//TODO: allow sourceType that belongs to LIVE entries only - same for mediaType
		if ($sourceType) {
			$liveStreamEntry->sourceType = $sourceType;
		}
		else {
			// default sourceType is AKAMAI_LIVE
			$liveStreamEntry->sourceType = KalturaSourceType::AKAMAI_LIVE;
		}
		
		// if the given password is empty, generate a random 8-character string as the new password
		if ( ($liveStreamEntry->streamPassword == null) || (strlen(trim($liveStreamEntry->streamPassword)) <= 0) )
		{
			$tempPassword = sha1(md5(uniqid(rand(), true)));
			$liveStreamEntry->streamPassword = substr($tempPassword, rand(0,strlen($tempPassword)-8), 8);		
		}
		
		// if no bitrate given, add default
		if(is_null($liveStreamEntry->bitrates) || !$liveStreamEntry->bitrates->count)
		{
			$liveStreamBitrate = new KalturaLiveStreamBitrate();
			$liveStreamBitrate->bitrate = self::DEFAULT_BITRATE;
			$liveStreamBitrate->width = self::DEFAULT_WIDTH;
			$liveStreamBitrate->height = self::DEFAULT_HEIGHT;
			
			$liveStreamEntry->bitrates = new KalturaLiveStreamBitrateArray();
			$liveStreamEntry->bitrates[] = $liveStreamBitrate;
		}
		else 
		{
			$bitrates = new KalturaLiveStreamBitrateArray();
			foreach($liveStreamEntry->bitrates as $bitrate)
			{		
				if(is_null($bitrate->bitrate))	$bitrate->bitrate = self::DEFAULT_BITRATE;
				if(is_null($bitrate->width))	$bitrate->bitrate = self::DEFAULT_WIDTH;
				if(is_null($bitrate->height))	$bitrate->bitrate = self::DEFAULT_HEIGHT;
				$bitrates[] = $bitrate;
			}
			$liveStreamEntry->bitrates = $bitrates;
		}
		
		$dbEntry = $this->insertLiveStreamEntry($liveStreamEntry);
		
		myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_ENTRY_ADD, $dbEntry, $this->getPartnerId(), null, null, null, $dbEntry->getId());

		$liveStreamEntry->fromObject($dbEntry);
		return $liveStreamEntry;
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
	 * Update live stream entry. Only the properties that were set will be updated.
	 * 
	 * @action update
	 * @param string $entryId Live stream entry id to update
	 * @param KalturaLiveStreamAdminEntry $liveStreamEntry Live stream entry metadata to update
	 * @return KalturaLiveStreamAdminEntry The updated live stream entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	function updateAction($entryId, KalturaLiveStreamAdminEntry $liveStreamEntry)
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
	
	private function insertLiveStreamEntry(KalturaLiveStreamAdminEntry $liveStreamEntry)
	{
		// first validate the input object
		$liveStreamEntry->validatePropertyNotNull("mediaType");
		$liveStreamEntry->validatePropertyNotNull("sourceType");
		$liveStreamEntry->validatePropertyNotNull("encodingIP1");
		$liveStreamEntry->validatePropertyNotNull("encodingIP2");
		$liveStreamEntry->validatePropertyNotNull("streamPassword");
		
		// create a default name if none was given
		if (!$liveStreamEntry->name)
			$liveStreamEntry->name = $this->getPartnerId().'_'.time();
		
		try
		{
			// first copy all the properties to the db entry, then we'll check for security stuff
			$dbEntry = $liveStreamEntry->toObject(new entry());
		}
		catch(kCoreException $ex)
		{
			$this->handleCoreException($ex, $dbEntry);
		}

		$this->checkAndSetValidUser($liveStreamEntry, $dbEntry);
		$this->checkAdminOnlyInsertProperties($liveStreamEntry);
		$this->validateAccessControlId($liveStreamEntry);
		$this->validateEntryScheduleDates($liveStreamEntry);
		
		$dbEntry->setPartnerId($this->getPartnerId());
		$dbEntry->setSubpId($this->getPartnerId() * 100);
		$dbEntry->setKuserId($this->getKuser()->getId());
		$dbEntry->setStatus(entry::ENTRY_STATUS_IMPORT);
		$dbEntry->save();
		
		$te = new TrackEntry();
		$te->setEntryId( $dbEntry->getId() );
		$te->setTrackEventTypeId( TrackEntry::TRACK_ENTRY_EVENT_TYPE_ADD_ENTRY );
		$te->setDescription(  __METHOD__ . ":" . __LINE__ . "::ENTRY_MEDIA_SOURCE_AKAMAI_LIVE" );
		TrackEntry::addTrackEntry( $te );

		kJobsManager::addProvisionProvideJob(null, $dbEntry);
 			
		return $dbEntry;
	}	
}