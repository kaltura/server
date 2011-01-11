<?php

/**
 * A Mix is an XML unique format invented by Kaltura, it allows the user to create a mix of videos and images, in and out points, transitions, text overlays, soundtrack, effects and much more...
 * Mixing service lets you create a new mix, manage its metadata and make basic manipulations.   
 *
 * @service mixing
 * @package api
 * @subpackage services
 */
class MixingService extends KalturaEntryService
{
	
	protected function kalturaNetworkAllowed($actionName)
	{
		if ($actionName === 'get') {
			return true;
		}
		return parent::kalturaNetworkAllowed($actionName);
	}
	
	/**
	 * Adds a new mix.
	 * If the dataContent is null, a default timeline will be created.
	 * 
	 * @action add
	 * @param KalturaMixEntry $mixEntry Mix entry metadata
	 * @return KalturaMixEntry The new mix entry
	 */
	function addAction(KalturaMixEntry $mixEntry)
	{
		$mixEntry->validatePropertyMinLength("name", 1);
		$mixEntry->validatePropertyNotNull("editorType");
		
		$dbEntry = $mixEntry->toObject(new entry());
		
		$this->checkAndSetValidUser($mixEntry, $dbEntry);
		$this->checkAdminOnlyInsertProperties($mixEntry);
		$this->validateAccessControlId($mixEntry);
		$this->validateEntryScheduleDates($mixEntry);
		
		$kshow = $this->createDummyKShow();

		$dbEntry->setKshowId($kshow->getId());
		$dbEntry->setPartnerId($this->getPartnerId());
		$dbEntry->setSubpId($this->getPartnerId() * 100);
		$dbEntry->setStatus(KalturaEntryStatus::READY);
		$dbEntry->setMediaType(entry::ENTRY_MEDIA_TYPE_SHOW); // for backward compatibility

		if (!$dbEntry->getThumbnail())
			$dbEntry->setThumbnail("&auto_edit.jpg");
			
		$dbEntry->save(); // we need the id for setDataContent
		
		// set default data if no data given
		if ($mixEntry->dataContent === null)
		{
			myEntryUtils::modifyEntryMetadataWithText($dbEntry, "", 0);
		}
		else
		{ 
			$dbEntry->setDataContent($mixEntry->dataContent, true, true);
			$dbEntry->save();
		}
		
		$kshow->setShowEntry($dbEntry);
		$kshow->save();
		$mixEntry->fromObject($dbEntry);
		
		myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_ENTRY_ADD, $dbEntry);
		
		return $mixEntry;
	}
	
	/**
	 * Get mix entry by id.
	 * 
	 * @action get
	 * @param string $entryId Mix entry id
	 * @param int $version Desired version of the data
	 * @return KalturaMixEntry The requested mix entry
	 */
	function getAction($entryId, $version = -1)
	{
		return $this->getEntry($entryId, $version, KalturaEntryType::MIX);
	}
	
	/**
	 * Update mix entry. Only the properties that were set will be updated.
	 * 
	 * @action update
	 * @param string $entryId Mix entry id to update
	 * @param KalturaMixEntry $mixEntry Mix entry metadata to update
	 * @return KalturaMixEntry The updated mix entry
	 */
	function updateAction($entryId, KalturaMixEntry $mixEntry)
	{
		$mixEntry->type = null; // because it was set in the constructor, but cannot be updated
		
		$dbEntry = entryPeer::retrieveByPK($entryId);

		if (!$dbEntry || $dbEntry->getType() != KalturaEntryType::MIX)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		$this->checkIfUserAllowedToUpdateEntry($dbEntry);
		$this->checkAndSetValidUser($mixEntry, $dbEntry);
		$this->checkAdminOnlyUpdateProperties($mixEntry);
		$this->validateAccessControlId($mixEntry);
		$this->validateEntryScheduleDates($mixEntry);
		
		$dbEntry = $mixEntry->toUpdatableObject($dbEntry);
		
		if ($mixEntry->dataContent !== null) // dataContent need special handling
			$dbEntry->setDataContent($mixEntry->dataContent, true, true);
			
		$dbEntry->save();
		$mixEntry->fromObject($dbEntry);
		
		$wrapper = objectWrapperBase::getWrapperClass($dbEntry);
		$wrapper->removeFromCache("entry", $dbEntry->getId());
		
		myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE, $dbEntry);
		
		return $mixEntry;
	}
	
	/**
	 * Delete a mix entry.
	 *
	 * @action delete
	 * @param string $entryId Mix entry id to delete
	 */
	function deleteAction($entryId)
	{
		$this->deleteEntry($entryId, KalturaEntryType::MIX);
	}
	
	/**
	 * List entries by filter with paging support.
	 * Return parameter is an array of mix entries.
	 * 
	 * @action list
	 * @param KalturaMixEntryFilter $filter Mix entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @return KalturaMixListResponse Wrapper for array of media entries and total count
	 */
	function listAction(KalturaMixEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
	    if (!$filter)
			$filter = new KalturaMixEntryFilter();
			
		$filter->typeEqual = KalturaEntryType::MIX; 
	    list($list, $totalCount) = parent::listEntriesByFilter($filter, $pager);
	    
	    $newList = KalturaMixEntryArray::fromEntryArray($list);
		$response = new KalturaMixListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		return $response;
	}
	
	/**
	 * Count mix entries by filter.
	 * 
	 * @action count
     * @param KalturaMediaEntryFilter $filter Media entry filter
	 * @return int
	 */
	function countAction(KalturaMediaEntryFilter $filter = null)
	{
	    if (!$filter)
			$filter = new KalturaMediaEntryFilter();
			
		$filter->typeEqual = KalturaEntryType::MIX;
		
		return parent::countEntriesByFilter($filter);
	}
	
	/**
	 * Clones an existing mix.
	 *
	 * @action clone
	 * @param string $entryId Mix entry id to clone
	 * @return KalturaMixEntry The new mix entry
	 */
	function cloneAction($entryId)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);

		if (!$dbEntry || $dbEntry->getType() != KalturaEntryType::MIX)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		$kshowId = $dbEntry->getKshowId();
		$kshow = $dbEntry->getKshow();
		
		if (!$kshow)
		{
			KalturaLog::CRIT("Kshow was not found for mix id [".$entryId."]");
			throw new KalturaAPIException(KalturaErrors::INTERNAL_SERVERL_ERROR);
		}
		
		$newKshow = myKshowUtils::shalowCloneById($kshowId, $this->getKuser()->getId());
	
		if (!$newKshow)
		{
			KalturaLog::ERR("Failed to clone kshow for mix id [".$entryId."]");
			throw new KalturaAPIException(KalturaErrors::INTERNAL_SERVERL_ERROR);
		}
		$newEntry = $newKshow->getShowEntry();
		
		$newMixEntry = new KalturaMixEntry();
		$newMixEntry->fromObject($newEntry);
		
		myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_ENTRY_ADD, $newEntry);
		
		return $newMixEntry;
	}
	
	/**
	 * Appends a media entry to a the end of the mix timeline, this will save the mix timeline as a new version.
	 * 
	 * @action appendMediaEntry
	 * @param string $mixEntryId Mix entry to append to its timeline
	 * @param string $mediaEntryId Media entry to append to the timeline
	 * @return KalturaMixEntry The mix entry
	 */
	function appendMediaEntryAction($mixEntryId, $mediaEntryId)
	{
		$dbMixEntry = entryPeer::retrieveByPK($mixEntryId);

		if (!$dbMixEntry || $dbMixEntry->getType() != KalturaEntryType::MIX)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $mixEntryId);
			
		$dbMediaEntry = entryPeer::retrieveByPK($mediaEntryId);

		if (!$dbMediaEntry || $dbMediaEntry->getType() != KalturaEntryType::MEDIA_CLIP)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $mediaEntryId);
			
		$kshow = $dbMixEntry->getkshow();		
		if (!$kshow)
		{
			KalturaLog::CRIT("Kshow was not found for mix id [".$mixEntryId."]");
			throw new KalturaAPIException(KalturaErrors::INTERNAL_SERVERL_ERROR);
		}
		
		// FIXME: temp hack  - when kshow doesn't have a roughcut, and the media entry is not ready, it cannob be queued for append upon import/conversion completion 
		if ($dbMediaEntry->getStatus() != entryStatus::READY)
		{
			$kshow->setShowEntryId($mixEntryId);
			$kshow->save();
			$dbMediaEntry->setKshowId($kshow->getId());
			$dbMediaEntry->save();
		}
		
		$metadata = $kshow->getMetadata();
		
		$relevantKshowVersion = 1 + $kshow->getVersion(); // the next metadata will be the first relevant version for this new entry
		
		$newMetadata = myMetadataUtils::addEntryToMetadata($metadata, $dbMediaEntry, $relevantKshowVersion, array());
		
		$dbMediaEntry->save(); // FIXME: should be removed, needed for the prev hack
		
		if ($newMetadata)
		{
			// TODO - add thumbnail only for entries that are worthy - check they are not moderated !
			$thumbModified = myKshowUtils::updateThumbnail($kshow, $dbMediaEntry, false);
			
			if ($thumbModified)
			{
			    $newMetadata = myMetadataUtils::updateThumbUrlFromMetadata($newMetadata, $dbMixEntry->getThumbnailUrl());
			}
			
			// it is very important to increment the version count because even if the entry is deferred
			// it will be added on the next version
			
			if (!$kshow->getHasRoughcut())
			{
				// make sure the kshow now does have a roughcut
				$kshow->setHasRoughcut(true);	
				$kshow->save();
			}
	
			$kshow->setMetadata($newMetadata, true);
		}
		
		$mixEntry = new KalturaMixEntry();
		$mixEntry->fromObject($dbMixEntry);
		
		return $mixEntry;
	}
	
	/**
	 * Request a new flattening job, flattening is used to convert a video mix to a video file. 
	 * 
	 * @action requestFlattening
	 * @param string $entryId Mix entry id
	 * @param string $fileFormat Format to convert
	 * @param int $version Version to flatten (If not set, latest will be used)
	 * @return int The queued job id
	 */
	public function requestFlatteningAction($entryId, $fileFormat, $version = -1)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);

		if (!$dbEntry || $dbEntry->getType() != KalturaEntryType::MIX)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		// sending -1 to the flatten job causes problems
		if ( $version <= 0 ) $version = null;
		
		$syncKey = $dbEntry->getSyncKey ( entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA , $version );
		
		$fileSync = kFileSyncUtils::getReadyFileSyncForKey( $syncKey ,false , false); 		
		if (!$fileSync)
			throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_VERSION, "entry", $entryId, $version);
		
		$job = myBatchFlattenClient::addJob($this->getKuser()->getPuserId(), $dbEntry, $version, $fileFormat);
		
		return $job->getId();
	}
	
	/**
	 * Get the mixes in which the media entry is included
	 *
	 * @action getMixesByMediaId
	 * @param string $mediaEntryId
	 * @return KalturaMixEntryArray
	 */
	public function getMixesByMediaIdAction($mediaEntryId)
	{
		$dbMediaEntry = entryPeer::retrieveByPK($mediaEntryId);

		if (!$dbMediaEntry || $dbMediaEntry->getType() != KalturaEntryType::MEDIA_CLIP)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $mediaEntryId);
			
		 $list = roughcutEntry::getAllRoughcuts($mediaEntryId);
		 $newList = KalturaMixEntryArray::fromEntryArray($list);
		 return $newList;
	}
	
	/**
	 * Get all ready media entries that exist in the given mix id
	 *
	 * @action getReadyMediaEntries
	 * @param string $mixId
	 * @param int $version Desired version to get the data from
	 * @return KalturaMediaEntryArray
	 */
	public function getReadyMediaEntriesAction($mixId, $version = -1)
	{
		$dbEntry = entryPeer::retrieveByPK($mixId);

		if (!$dbEntry || $dbEntry->getType() != KalturaEntryType::MIX)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $mixId);
		
		$dataSyncKey = $dbEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
		$mixFileName = kFileSyncUtils::getReadyLocalFilePathForKey($dataSyncKey, false);
		if(!$mixFileName)
			KExternalErrors::dieError(KExternalErrors::FILE_NOT_FOUND);

		$entryDataFromMix = myFlvStreamer::getAllAssetsData($mixFileName);

		$ids = array();
		foreach($entryDataFromMix as $data)
			$ids[] = $data["id"];

		$c = KalturaCriteria::create(entryPeer::OM_CLASS);
		$c->addAnd(entryPeer::ID, $ids, Criteria::IN);
		$c->addAnd(entryPeer::TYPE, entryType::MEDIA_CLIP);					
		
		$dbEntries = entryPeer::doSelect($c);

		$mediaEntries = KalturaMediaEntryArray::fromEntryArray($dbEntries);
		
		return $mediaEntries;
	}
	
	/**
	 * Anonymously rank a mix entry, no validation is done on duplicate rankings
	 *  
	 * @action anonymousRank
	 * @param string $entryId
	 * @param int $rank
	 */
	public function anonymousRankAction($entryId, $rank)
	{
		return parent::anonymousRankEntry($entryId, KalturaEntryType::MIX, $rank);
	}
}