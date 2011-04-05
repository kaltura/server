<?php

class KalturaEntryService extends KalturaBaseService 
{
    /**
     * @param KalturaResource $resource
     * @param entry $dbEntry
     * @param asset $asset
     * @throws KalturaErrors::ENTRY_TYPE_NOT_SUPPORTED
     */
    protected function attachResource(KalturaResource $resource, entry $dbEntry, asset $asset = null)
    {
    	throw new KalturaAPIException(KalturaErrors::ENTRY_TYPE_NOT_SUPPORTED, $dbEntry->getType());
    }
    
	/**
	 * @param KalturaBaseEntry $entry
	 * @param entry $dbEntry
	 * @return entry
	 */
	protected function prepareEntryForInsert(KalturaBaseEntry $entry, entry $dbEntry = null)
	{
		// create a default name if none was given
		if (!$entry->name)
			$entry->name = $this->getPartnerId().'_'.time();
		
		// first copy all the properties to the db entry, then we'll check for security stuff
		if(!$dbEntry)
		{
			KalturaLog::debug("Creating new entry");
			$dbEntry = new entry();
		}
			
		$dbEntry = $entry->toInsertableObject($dbEntry);
//		KalturaLog::debug("Inserted entry id [" . $dbEntry->getId() . "] data [" . print_r($dbEntry->toArray(), true) . "]");

		$this->checkAndSetValidUser($entry, $dbEntry);
		$this->checkAdminOnlyInsertProperties($entry);
		$this->validateAccessControlId($entry);
		$this->validateEntryScheduleDates($entry);
			
		$dbEntry->setPartnerId($this->getPartnerId());
		$dbEntry->setSubpId($this->getPartnerId() * 100);
		$dbEntry->setDefaultModerationStatus();
				
		return $dbEntry;
	}
	
	/**
	 * Adds entry
	 * 
	 * @param KalturaBaseEntry $entry
	 * @return entry
	 */
	protected function add(KalturaBaseEntry $entry, $conversionProfileId = null)
	{
		$dbEntry = null;
		$conversionProfile = myPartnerUtils::getConversionProfile2ForPartner($this->getPartnerId(), $conversionProfileId);
		if($conversionProfile && $conversionProfile->getDefaultEntryId())
		{
			$templateEntry = entryPeer::retrieveByPK($conversionProfile->getDefaultEntryId());
//			KalturaLog::debug("Template entry found id [" . $templateEntry->getId() . "] data [" . print_r($templateEntry->toArray(), true) . "]");
			$dbEntry = $templateEntry->copy();
			$dbEntry->save();
			
//			KalturaLog::debug("Copied entry id [" . $dbEntry->getId() . "] data [" . print_r($dbEntry->toArray(), true) . "]");
		}
		
		$dbEntry = $this->prepareEntryForInsert($entry, $dbEntry);
//		KalturaLog::debug("Prepared entry id [" . $dbEntry->getId() . "] data [" . print_r($dbEntry->toArray(), true) . "]");
		
		return $dbEntry;
	}
	
	/**
	 * Convert entry
	 * 
	 * @param string $entryId Media entry id
	 * @param int $conversionProfileId
	 * @param KalturaConversionAttributeArray $dynamicConversionAttributes
	 * @return int job id
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND
	 * @throws KalturaErrors::FLAVOR_PARAMS_NOT_FOUND
	 */
	protected function convert($entryId, $conversionProfileId = null, KalturaConversionAttributeArray $dynamicConversionAttributes = null)
	{
		$entry = entryPeer::retrieveByPK($entryId);

		if (!$entry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		$srcFlavorAsset = flavorAssetPeer::retrieveOriginalByEntryId($entryId);
		if(!$srcFlavorAsset)
			throw new KalturaAPIException(KalturaErrors::ORIGINAL_FLAVOR_ASSET_IS_MISSING);
		
		if(is_null($conversionProfileId) || $conversionProfileId <= 0)
		{
			$conversionProfile = myPartnerUtils::getConversionProfile2ForEntry($entryId);
			if(!$conversionProfile)
				throw new KalturaAPIException(KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND, $conversionProfileId);
			
			$conversionProfileId = $conversionProfile->getId();
		}
			
		// even if it null
		$entry->setConversionQuality($conversionProfileId);
		$entry->setConversionProfileId($conversionProfileId);
		$entry->save();
		
		if($dynamicConversionAttributes)
		{
			$flavors = flavorParamsPeer::retrieveByProfile($conversionProfileId);
			if(!count($flavors))
				throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_NOT_FOUND);
		
			$srcFlavorParamsId = null;
			$flavorParams = $entry->getDynamicFlavorAttributes();
			foreach($flavors as $flavor)
			{
				if($flavor->hasTag(flavorParams::TAG_SOURCE))
					$srcFlavorParamsId = $flavor->getId();
					
				$flavorParams[$flavor->getId()] = $flavor;
			}
			
			$dynamicAttributes = array();
			foreach($dynamicConversionAttributes as $dynamicConversionAttribute)
			{
				if(is_null($dynamicConversionAttribute->flavorParamsId))
					$dynamicConversionAttribute->flavorParamsId = $srcFlavorParamsId;
					
				if(is_null($dynamicConversionAttribute->flavorParamsId))
					continue;
					
				$dynamicAttributes[$dynamicConversionAttribute->flavorParamsId][trim($dynamicConversionAttribute->name)] = trim($dynamicConversionAttribute->value);
			}
			
			if(count($dynamicAttributes))
			{
				$entry->setDynamicFlavorAttributes($dynamicAttributes);
				$entry->save();
			}
		}
		
		$srcSyncKey = $srcFlavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
        $srcFilePath = kFileSyncUtils::getLocalFilePathForKey($srcSyncKey);
        
		$job = kJobsManager::addConvertProfileJob(null, $entry, $srcFlavorAsset->getId(), $srcFilePath);
		if(!$job)
			return null;
			
		return $job->getId();
	}
	
	protected function addEntryFromFlavorAsset(KalturaBaseEntry $newEntry, entry $srcEntry, flavorAsset $srcFlavorAsset)
	{
      	$newEntry->type = $srcEntry->getType();
      		
		if ($newEntry->name === null)
			$newEntry->name = $srcEntry->getName();
			
        if ($newEntry->description === null)
        	$newEntry->description = $srcEntry->getDescription();
        
        if ($newEntry->creditUrl === null)
        	$newEntry->creditUrl = $srcEntry->getSourceLink();
        	
       	if ($newEntry->creditUserName === null)
       		$newEntry->creditUserName = $srcEntry->getCredit();
       		
     	if ($newEntry->tags === null)
      		$newEntry->tags = $srcEntry->getTags();
       		
    	$newEntry->sourceType = KalturaSourceType::SEARCH_PROVIDER;
     	$newEntry->searchProviderType = KalturaSearchProviderType::KALTURA;
     	
		$dbEntry = $this->prepareEntryForInsert($newEntry);
      	$dbEntry->setSourceId( $srcEntry->getId() );
      	
     	$kshow = $this->createDummyKShow();
        $kshowId = $kshow->getId();
        
        $msg = null;
        $flavorAsset = kFlowHelper::createOriginalFlavorAsset($this->getPartnerId(), $dbEntry->getId(), $msg);
        if(!$flavorAsset)
        {
			KalturaLog::err("Flavor asset not created for entry [" . $dbEntry->getId() . "] reason [$msg]");
			
			$dbEntry->setStatus(entryStatus::ERROR_CONVERTING);
			$dbEntry->save();
			
			throw new KalturaAPIException(KalturaErrors::ORIGINAL_FLAVOR_ASSET_NOT_CREATED, $msg);
        }
                
        $srcSyncKey = $srcFlavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
        $newSyncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
        kFileSyncUtils::createSyncFileLinkForKey($newSyncKey, $srcSyncKey, false);

		kEventsManager::raiseEvent(new kObjectAddedEvent($flavorAsset));
				
		myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_ENTRY_ADD, $dbEntry);

		$newEntry->fromObject($dbEntry);
		return $newEntry;
	}
	
	protected function getEntry($entryId, $version = -1, $entryType = null)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);

		if (!$dbEntry || ($entryType !== null && $dbEntry->getType() != $entryType))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		if ($version !== -1)
			$dbEntry->setDesiredVersion($version);

		$ks = $this->getKs();
		$isAdmin = false;
		if($ks)
			$isAdmin = $ks->isAdmin();
		
	    $entry = KalturaEntryFactory::getInstanceByType($dbEntry->getType(), $isAdmin);
	    
		$entry->fromObject($dbEntry);

		return $entry;
	}
	
	/**
	 * @param KalturaBaseEntryFilter $filter
	 * @param KalturaFilterPager $pager
	 * @param string $partnerIdForScope
	 * @return KalturaCriteria
	 */
	protected function prepareEntriesCriteriaFilter(KalturaBaseEntryFilter $filter = null, KalturaFilterPager $pager = null, $partnerIdForScope)
	{
		if (!$filter)
			$filter = new KalturaBaseEntryFilter();

		// because by default we will display only READY entries, and when deleted status is requested, we don't want this to disturb
		entryPeer::allowDeletedInCriteriaFilter(); 
		
		// when session is not admin, allow access to user entries only
		if (!$this->getKs() || !$this->getKs()->isAdmin())
		{
			$filter->userIdEqual = $this->getKuser()->getPuserId();
		}
		
		$this->setDefaultStatus($filter);
		$this->setDefaultModerationStatus($filter);
		$this->fixFilterUserId($filter);
		$this->fixFilterDuration($filter);
		
		$entryFilter = new entryFilter();
		if(is_null($partnerIdForScope))
		{
			$entryFilter->setPartnerSearchScope ( $this->getPartnerId() );
		}
		else
		{
			$entryFilter->setPartnerSearchScope ( $partnerIdForScope );
		}
		
		$filter->toObject($entryFilter);

		$c = KalturaCriteria::create(entryPeer::OM_CLASS);
		
		if($pager)
			$pager->attachToCriteria($c);
			
		$entryFilter->attachToCriteria($c);
			
		return $c;
	}
	
	protected function listEntriesByFilter(KalturaBaseEntryFilter $filter = null, KalturaFilterPager $pager = null, $partnerIdForScope = null)
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;

		if (!$pager)
			$pager = new KalturaFilterPager();
		
		$c = $this->prepareEntriesCriteriaFilter($filter, $pager, $partnerIdForScope);
		
		$list = entryPeer::doSelect($c);
		$totalCount = $c->getRecordsCount();
		
		return array($list, $totalCount);        
	}
	
	protected function countEntriesByFilter(KalturaBaseEntryFilter $filter = null)
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;

		$c = $this->prepareEntriesCriteriaFilter($filter, null, null);
		$c->applyFilters();
		$totalCount = $c->getRecordsCount();
		
		return $totalCount;
	}
    
   	/**
   	 * Sets the valid user for the entry 
   	 * Throws an error if the session user is trying to add entry to another user and not using an admin session 
   	 *
   	 * @param KalturaBaseEntry $entry
   	 * @param entry $dbEntry
   	 */
	protected function checkAndSetValidUser(KalturaBaseEntry $entry, entry $dbEntry)
	{
		KalturaLog::debug("DB puser id [" . $dbEntry->getPuserId() . "] kuser id [" . $dbEntry->getKuserId() . "]");
		// for new entry, puser ID is null - set it from service scope
		if($dbEntry->getPuserId() === null)
		{
			KalturaLog::debug("Set kuser id [" . $this->getKuser()->getId() . "] line [" . __LINE__ . "]");
			$dbEntry->setKuserId($this->getKuser()->getId());
			return;
		}
		
		// get puser ID from entry to compare to userId on the updated entry object
		$entryPuserId = $dbEntry->getPuserId();
		
		$kuser = null;
		if($dbEntry->getKuserId())
		{
			// get kuser object from entry kuserId
			$kuser = kuserPeer::retrieveByPK($dbEntry->getKuserId());
			if(!$kuser || $kuser->getPuserId() != $dbEntry->getPuserId())
				$kuser = null;
		}
		else
		{
			// get kuser object from entry puserId
			$kuser = kuserPeer::getKuserByPartnerAndUid($dbEntry->getPartnerId(), $dbEntry->getPuserId());
		}
		
		if($kuser)
		{
			KalturaLog::debug("Set kuser id [" . $kuser->getId() . "] line [" . __LINE__ . "]");
			$dbEntry->setKuserId($kuser->getId());
			return;
		}
		
		// userID doesn't require change (it is null or the same as the db entry) - do nothing
		if($entry->userId === null || $entry->userId === $entryPuserId) {
			KalturaLog::debug('API entry userId ['.$entry->userId.'], DB entry userId ['.$entryPuserId.'] - no need to change - quitting');
			return;
		}
			
		
		// db user is going to be changed, only admin allowed - otherwise, throw exception
		if(!$this->getKs() || !$this->getKs()->isAdmin())
		{
			KalturaLog::debug('API entry userId ['.$entry->userId.'], DB entry userId ['.$entryPuserId.'] - change required but KS is not admin');
			throw new KalturaAPIException(KalturaErrors::INVALID_KS, "", ks::INVALID_TYPE, ks::getErrorStr(ks::INVALID_TYPE));
		}
		
		// passed previous conditions, need to change userID on entry
		// first step is to make sure the user exists
		$puserKuser = PuserKuserPeer::createPuserKuser($this->getPartnerId(), $this->getPartnerId() * 100, $dbEntry->getPuserId(), $dbEntry->getPuserId(), $dbEntry->getPuserId(), true);
		// second step is simply changing the userID on the entry
		$dbEntry->setKuserId($puserKuser->getKuserId());	
		KalturaLog::debug("Set kuser id [" . $puserKuser->getKuserId() . "] line [" . __LINE__ . "]");	
	}
	
	/**
	 * Throws an error if the user is trying to update entry that doesn't belong to him and the session is not admin
	 *
	 * @param entry $dbEntry
	 */
	protected function checkIfUserAllowedToUpdateEntry(entry $dbEntry)
	{
		// if session is not admin, but privileges are
		// edit:* or edit:ENTRY_ID or editplaylist:PLAYLIST_ID
		// edit is allowed
		if (!$this->getKs() || !$this->getKs()->isAdmin() )
		{
			// check if wildcard on 'edit'
			if ($this->getKs()->verifyPrivileges(ks::PRIVILEGE_EDIT, ks::PRIVILEGE_WILDCARD))
				return;
				
			// check if entryID on 'edit'
			if ($this->getKs()->verifyPrivileges(ks::PRIVILEGE_EDIT, $dbEntry->getId()))
				return;

			//
			if ($this->getKs()->verifyPlaylistPrivileges(ks::PRIVILEGE_EDIT_ENTRY_OF_PLAYLIST, $dbEntry->getId(), $this->getPartnerId()))
				return;
		}
		
		// if user is not the entry owner, and the KS is user type - do not allow update
		if ($dbEntry->getKuserId() != $this->getKuser()->getId() && (!$this->getKs() || !$this->getKs()->isAdmin()))
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_KS, "", ks::INVALID_TYPE, ks::getErrorStr(ks::INVALID_TYPE));
		}
	}
	
	/**
	 * Throws an error if trying to update admin only properties with normal user session
	 *
	 * @param KalturaBaseEntry $entry
	 */
	protected function checkAdminOnlyUpdateProperties(KalturaBaseEntry $entry)
	{
		if ($entry->adminTags !== null)
			$this->validateAdminSession("adminTags");
			
		if ($entry->categories !== null)
		{
			$cats = explode(entry::ENTRY_CATEGORY_SEPARATOR, $entry->categories);
			foreach($cats as $cat)
			{
				if(!categoryPeer::getByFullNameExactMatch($cat))
					$this->validateAdminSession("categories");
			}
		}
			
		if ($entry->startDate !== null)
			$this->validateAdminSession("startDate");
			
		if  ($entry->endDate !== null)
			$this->validateAdminSession("endDate");
			
		if ($entry->accessControlId !== null) 
			$this->validateAdminSession("accessControlId");
	}
	
	/**
	 * Throws an error if trying to update admin only properties with normal user session
	 *
	 * @param KalturaBaseEntry $entry
	 */
	protected function checkAdminOnlyInsertProperties(KalturaBaseEntry $entry)
	{
		if ($entry->adminTags !== null)
			$this->validateAdminSession("adminTags");
			
		if ($entry->categories !== null)
		{
			$cats = explode(entry::ENTRY_CATEGORY_SEPARATOR, $entry->categories);
			foreach($cats as $cat)
			{
				if(!categoryPeer::getByFullNameExactMatch($cat))
					$this->validateAdminSession("categories");
			}
		}
			
		if ($entry->startDate !== null)
			$this->validateAdminSession("startDate");
			
		if  ($entry->endDate !== null)
			$this->validateAdminSession("endDate");
			
		if ($entry->accessControlId !== null) 
			$this->validateAdminSession("accessControlId");
	}
	
	/**
	 * Validates that current session is an admin session 
	 */
	protected function validateAdminSession($property)
	{
		if (!$this->getKs() || !$this->getKs()->isAdmin())
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_ADMIN_PROPERTY, $property);	
	}
	
	/**
	 * Throws an error if trying to set invalid Access Control Profile
	 * 
	 * @param KalturaBaseEntry $entry
	 */
	protected function validateAccessControlId(KalturaBaseEntry $entry)
	{
		if ($entry->accessControlId !== null) // trying to update
		{
			parent::applyPartnerFilterForClass(new accessControlPeer()); 
			$accessControl = accessControlPeer::retrieveByPK($entry->accessControlId);
			if (!$accessControl)
				throw new KalturaAPIException(KalturaErrors::ACCESS_CONTROL_ID_NOT_FOUND, $entry->accessControlId);
		}
	}
	
	/**
	 * Throws an error if trying to set invalid entry schedule date
	 * 
	 * @param KalturaBaseEntry $entry
	 */
	protected function validateEntryScheduleDates(KalturaBaseEntry $entry)
	{
		if ($entry->startDate <= -1)
			$entry->startDate = -1;
			
		if ($entry->endDate <= -1)
			$entry->endDate = -1;
		
		if ($entry->startDate !== -1 && $entry->endDate !== -1)
		{
			if ($entry->startDate >= $entry->endDate)
			{
				throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_SCHEDULE_DATES);
			}
		}
	}
	
	protected function createDummyKShow()
	{
		$kshow = new kshow();
		$kshow->setName("DUMMY KSHOW FOR API V3");
		$kshow->setProducerId($this->getKuser()->getId());
		$kshow->setPartnerId($this->getPartnerId());
		$kshow->setSubpId($this->getPartnerId() * 100);
		$kshow->setViewPermissions(kshow::KSHOW_PERMISSION_EVERYONE);
		$kshow->setPermissions(myPrivilegesMgr::PERMISSIONS_PUBLIC);
		$kshow->setAllowQuickEdit(true);
		$kshow->save();
		
		return $kshow;
	}
	
	protected function updateEntry($entryId, KalturaBaseEntry $entry, $entryType = null)
	{
		$entry->type = null; // because it was set in the constructor, but cannot be updated
		
		$dbEntry = entryPeer::retrieveByPK($entryId);

		if (!$dbEntry || ($entryType !== null && $dbEntry->getType() != $entryType))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		
		$this->checkIfUserAllowedToUpdateEntry($dbEntry);
		$this->checkAndSetValidUser($entry, $dbEntry);
		$this->checkAdminOnlyUpdateProperties($entry);
		$this->validateAccessControlId($entry);
		$this->validateEntryScheduleDates($entry);
		
		$dbEntry = $entry->toUpdatableObject($dbEntry);
		
		$dbEntry->save();
		$entry->fromObject($dbEntry);
		
		$wrapper = objectWrapperBase::getWrapperClass($dbEntry);
		$wrapper->removeFromCache("entry", $dbEntry->getId());
		
		myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE, $dbEntry);
		
		return $entry;
	}
	
	protected function deleteEntry($entryId, $entryType = null)
	{
		$entryToDelete = entryPeer::retrieveByPK($entryId);

		if (!$entryToDelete || ($entryType !== null && $entryToDelete->getType() != $entryType))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		$this->checkIfUserAllowedToUpdateEntry($entryToDelete);
		
		myEntryUtils::deleteEntry($entryToDelete);
		
		/*
			All move into myEntryUtils::deleteEntry
		
			$entryToDelete->setStatus(entryStatus::DELETED); 
			KalturaLog::log("KalturaEntryService::delete Entry [$entryId] Partner [" . $entryToDelete->getPartnerId() . "]");
			
			// make sure the moderation_status is set to moderation::MODERATION_STATUS_DELETE
			$entryToDelete->setModerationStatus(moderation::MODERATION_STATUS_DELETE); 
			$entryToDelete->setModifiedAt(time());
			$entryToDelete->save();
			
			myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_ENTRY_DELETE, $entryToDelete);
		*/
		
		$wrapper = objectWrapperBase::getWrapperClass($entryToDelete);
		$wrapper->removeFromCache("entry", $entryToDelete->getId());
	}
	
	protected function updateThumbnailForEntryFromUrl($entryId, $url, $entryType = null, $fileSyncType = entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);

		if (!$dbEntry || ($entryType !== null && $dbEntry->getType() != $entryType))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		// if session is not admin, we should check that the user that is updating the thumbnail is the one created the entry
		// FIXME: Temporary disabled because update thumbnail feature (in app studio) is working with anonymous ks
		/*if (!$this->getKs()->isAdmin())
		{
			if ($dbEntry->getPuserId() !== $this->getKs()->user)
			{
				throw new KalturaAPIException(KalturaErrors::PERMISSION_DENIED_TO_UPDATE_ENTRY);
			}
		}*/
		
		return myEntryUtils::updateThumbnailFromFile($dbEntry, $url, $fileSyncType);
	}
	
	protected function updateThumbnailJpegForEntry($entryId, $fileData, $entryType = null, $fileSyncType = entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);

		if (!$dbEntry || ($entryType !== null && $dbEntry->getType() != $entryType))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		// if session is not admin, we should check that the user that is updating the thumbnail is the one created the entry
		// FIXME: Temporary disabled because update thumbnail feature (in app studio) is working with anonymous ks
		/*if (!$this->getKs()->isAdmin())
		{
			if ($dbEntry->getPuserId() !== $this->getKs()->user)
			{
				throw new KalturaAPIException(KalturaErrors::PERMISSION_DENIED_TO_UPDATE_ENTRY);
			}
		}*/
		
		return myEntryUtils::updateThumbnailFromFile($dbEntry, $fileData["tmp_name"], $fileSyncType);
	}
	
	protected function updateThumbnailForEntryFromSourceEntry($entryId, $sourceEntryId, $timeOffset, $entryType = null, $flavorParamsId = null)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);

		if (!$dbEntry || ($entryType !== null && $dbEntry->getType() != $entryType))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		$sourceDbEntry = entryPeer::retrieveByPK($sourceEntryId);
		if (!$sourceDbEntry || $sourceDbEntry->getType() != KalturaEntryType::MEDIA_CLIP)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $sourceDbEntry);
			
		// if session is not admin, we should check that the user that is updating the thumbnail is the one created the entry
		if (!$this->getKs() || !$this->getKs()->isAdmin())
		{
			if ($dbEntry->getPuserId() !== $this->getKs()->user)
			{
				throw new KalturaAPIException(KalturaErrors::PERMISSION_DENIED_TO_UPDATE_ENTRY);
			}
		}
		
		$updateThumbnailResult = myEntryUtils::createThumbnailFromEntry($dbEntry, $sourceDbEntry, $timeOffset, $flavorParamsId);
		
		if (!$updateThumbnailResult)
		{
			KalturaLog::CRIT("An unknwon error occured while trying to update thumbnail");
			throw new KalturaAPIException(KalturaErrors::INTERNAL_SERVERL_ERROR);
		}
		
		$wrapper = objectWrapperBase::getWrapperClass($dbEntry);
		$wrapper->removeFromCache("entry", $dbEntry->getId());
		
		myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE_THUMBNAIL, $dbEntry, $dbEntry->getPartnerId(), $dbEntry->getPuserId(), null, null, $entryId);

		$ks = $this->getKs();
		$isAdmin = false;
		if($ks)
			$isAdmin = $ks->isAdmin();
			
		$mediaEntry = KalturaEntryFactory::getInstanceByType($dbEntry->getType(), $isAdmin);
		$mediaEntry->fromObject($dbEntry);
		
		return $mediaEntry;
	}
	
	protected function flagEntry(KalturaModerationFlag $moderationFlag, $entryType = null)
	{
		$moderationFlag->validatePropertyNotNull("flaggedEntryId");

		$entryId = $moderationFlag->flaggedEntryId;
		$dbEntry = entryPeer::retrieveByPKNoFilter($entryId);

		if (!$dbEntry || ($entryType !== null && $dbEntry->getType() != $entryType))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		$validModerationStatuses = array(
			KalturaEntryModerationStatus::APPROVED,
			KalturaEntryModerationStatus::AUTO_APPROVED,
			KalturaEntryModerationStatus::FLAGGED_FOR_REVIEW,
		);
		if (!in_array($dbEntry->getModerationStatus(), $validModerationStatuses))
			throw new KalturaAPIException(KalturaErrors::ENTRY_CANNOT_BE_FLAGGED);
			
		$dbModerationFlag = new moderationFlag();
		$dbModerationFlag->setPartnerId($dbEntry->getPartnerId());
		$dbModerationFlag->setKuserId($this->getKuser()->getId());
		$dbModerationFlag->setFlaggedEntryId($dbEntry->getId());
		$dbModerationFlag->setObjectType(KalturaModerationObjectType::ENTRY);
		$dbModerationFlag->setStatus(KalturaModerationFlagStatus::PENDING);
		$dbModerationFlag->setFlagType($moderationFlag->flagType);
		$dbModerationFlag->setComments($moderationFlag->comments);
		$dbModerationFlag->save();
		
		$dbEntry->setModerationStatus(KalturaEntryModerationStatus::FLAGGED_FOR_REVIEW);
		$dbEntry->incModerationCount();
		$dbEntry->save();
		
		$moderationFlag = new KalturaModerationFlag();
		$moderationFlag->fromObject($dbModerationFlag);
		
		// need to notify the partner that an entry was flagged - use the OLD moderation onject that is required for the 
		// NOTIFICATION_TYPE_ENTRY_REPORT notification
		// TODO - change to moderationFlag object to implement the interface for the notification:
		// it should have "objectId", "comments" , "reportCode" as getters
		$oldModerationObj = new moderation();
		$oldModerationObj->setPartnerId($dbEntry->getPartnerId());
		$oldModerationObj->setComments( $moderationFlag->comments);
		$oldModerationObj->setObjectId( $dbEntry->getId() );
		$oldModerationObj->setObjectType( moderation::MODERATION_OBJECT_TYPE_ENTRY );
		$oldModerationObj->setReportCode( "" );
		myNotificationMgr::createNotification( notification::NOTIFICATION_TYPE_ENTRY_REPORT, $oldModerationObj ,$dbEntry->getPartnerId());
				
		return $moderationFlag;
	}
	
	protected function rejectEntry($entryId, $entryType = null)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry || ($entryType !== null && $dbEntry->getType() != $entryType))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		$dbEntry->setModerationStatus(KalturaEntryModerationStatus::REJECTED);
		$dbEntry->setModerationCount(0);
		$dbEntry->save();
		
		myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE , $dbEntry, null, null, null, null, $dbEntry->getId() );
//		myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_ENTRY_BLOCK , $dbEntry->getId());
		
		moderationFlagPeer::markAsModeratedByEntryId($this->getPartnerId(), $dbEntry->getId());
	}
	
	protected function approveEntry($entryId, $entryType = null)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry || ($entryType !== null && $dbEntry->getType() != $entryType))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		$dbEntry->setModerationStatus(KalturaEntryModerationStatus::APPROVED);
		$dbEntry->setModerationCount(0);
		$dbEntry->save();
		
		myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE , $dbEntry, null, null, null, null, $dbEntry->getId() );
//		myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_ENTRY_BLOCK , $dbEntry->getId());
		
		moderationFlagPeer::markAsModeratedByEntryId($this->getPartnerId(), $dbEntry->getId());
	}
	
	protected function listFlagsForEntry($entryId, KalturaFilterPager $pager = null)
	{
		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$c = new Criteria();
		$c->addAnd(moderationFlagPeer::PARTNER_ID, $this->getPartnerId());
		$c->addAnd(moderationFlagPeer::FLAGGED_ENTRY_ID, $entryId);
		$c->addAnd(moderationFlagPeer::OBJECT_TYPE, KalturaModerationObjectType::ENTRY);
		$c->addAnd(moderationFlagPeer::STATUS, KalturaModerationFlagStatus::PENDING);
		
		$totalCount = moderationFlagPeer::doCount($c);
		$pager->attachToCriteria($c);
		$list = moderationFlagPeer::doSelect($c);
		
		$newList = KalturaModerationFlagArray::fromModerationFlagArray($list);
		$response = new KalturaModerationFlagListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		return $response;
	}
	
	protected function anonymousRankEntry($entryId, $entryType = null, $rank)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry || ($entryType !== null && $dbEntry->getType() != $entryType))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		if ($rank <= 0 || $rank > 5)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_RANK_VALUE);
		}

		$kvote = new kvote();
		$kvote->setEntryId($entryId);
		$kvote->setKuserId($this->getKuser()->getId());
		$kvote->setRank($rank);
		$kvote->save();
	}
	
	/**
	 * Set the default status to ready if other status filters are not specified
	 * 
	 * @param KalturaBaseEntryFilter $filter
	 */
	private function setDefaultStatus(KalturaBaseEntryFilter $filter)
	{
		if ($filter->statusEqual === null && 
			$filter->statusIn === null &&
			$filter->statusNotEqual === null &&
			$filter->statusNotIn === null)
		{
			$filter->statusEqual = KalturaEntryStatus::READY;
		}
	}
	
	/**
	 * Set the default moderation status to ready if other moderation status filters are not specified
	 * 
	 * @param KalturaBaseEntryFilter $filter
	 */
	private function setDefaultModerationStatus(KalturaBaseEntryFilter $filter)
	{
		if ($filter->moderationStatusEqual === null && 
			$filter->moderationStatusIn === null && 
			$filter->moderationStatusNotEqual === null && 
			$filter->moderationStatusNotIn === null)
		{
			$moderationStatusesNotIn = array(
				KalturaEntryModerationStatus::PENDING_MODERATION, 
				KalturaEntryModerationStatus::REJECTED);
			$filter->moderationStatusNotIn = implode(",", $moderationStatusesNotIn); 
		}
	}
	
	/**
	 * The user_id is infact a puser_id and the kuser_id should be retrieved
	 * 
	 * @param KalturaBaseEntryFilter $filter
	 */
	private function fixFilterUserId(KalturaBaseEntryFilter $filter)
	{
		if ($filter->userIdEqual !== null)
		{
			$kuser = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $filter->userIdEqual);
			if ($kuser)
				$filter->userIdEqual = $kuser->getId();
			else 
				$filter->userIdEqual = -1; // no result will be returned when the user is missing
		}
	}
	
	/**
	 * Convert duration in seconds to msecs (because the duration field is mapped to length_in_msec)
	 * 
	 * @param KalturaBaseEntryFilter $filter
	 */
	private function fixFilterDuration(KalturaBaseEntryFilter $filter)
	{
		if ($filter instanceof KalturaPlayableEntryFilter) // because duration filter should be supported in baseEntryService
		{
			if ($filter->durationGreaterThan !== null)
				$filter->durationGreaterThan = $filter->durationGreaterThan * 1000;

			if ($filter->durationGreaterThanOrEqual !== null)
				$filter->durationGreaterThanOrEqual = $filter->durationGreaterThanOrEqual * 1000;
				
			if ($filter->durationLessThan !== null)
				$filter->durationLessThan = $filter->durationLessThan * 1000;
				
			if ($filter->durationLessThanOrEqual !== null)
				$filter->durationLessThanOrEqual = $filter->durationLessThanOrEqual * 1000;
		}
	}
}
