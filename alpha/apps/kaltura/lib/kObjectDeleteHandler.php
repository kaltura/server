<?php

class kObjectDeleteHandler implements kObjectDeletedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::shouldConsumeDeletedEvent()
	 */
	public function shouldConsumeDeletedEvent(BaseObject $object)
	{
		if($object instanceof entry)
			return true;
			
		if($object instanceof category)
			return true;
			
		if($object instanceof uiConf)
			return true;
			
		if($object instanceof BatchJob)
			return true;
			
		if($object instanceof asset)
			return true;
		
		if($object instanceof assetParams)
			return true;
			
		if($object instanceof syndicationFeed)
			return true;
			
		if($object instanceof conversionProfile2)
			return true;
			
		if($object instanceof kuser)
			return true;

		if($object instanceof FileSync)
			return true;
			
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::objectDeleted()
	 */
	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null) 
	{
		if($object instanceof entry)
			$this->entryDeleted($object);
			
		if($object instanceof category)
			$this->categoryDeleted($object);
			
		if($object instanceof uiConf)
			$this->uiConfDeleted($object);
			
		if($object instanceof BatchJob)
			$this->batchJobDeleted($object);
			
		if($object instanceof asset)
			$this->assetDeleted($object);
			
		if($object instanceof assetParams)
			$this->assetParamDeleted($object);
			
		if($object instanceof syndicationFeed)
			$this->syndicationFeedDeleted($object);
			
		if($object instanceof conversionProfile2)
			$this->conversionProfileDeleted($object);
			
		if($object instanceof kuser)
			$this->kuserDelete($object);

		if($object instanceof FileSync)
			$this->fileSyncDelete($object, $raisedJob);
			
		return true;
	}

	/**
	 * @param string $id
	 * @param int $type
	 */
	protected function syncableDeleted($id, $type) 
	{
		$c = new Criteria();
		$c->add(FileSyncPeer::OBJECT_ID, $id);
		$c->add(FileSyncPeer::OBJECT_TYPE, $type);
		$c->add(FileSyncPeer::STATUS, array(FileSync::FILE_SYNC_STATUS_PURGED, FileSync::FILE_SYNC_STATUS_DELETED), Criteria::NOT_IN);
		
		$fileSyncs = FileSyncPeer::doSelect($c);
		foreach($fileSyncs as $fileSync)
		{
			$key = kFileSyncUtils::getKeyForFileSync($fileSync);
			kFileSyncUtils::deleteSyncFileForKey($key);
		}
	}

	/**
	 * @param entry $entry
	 */
	protected function entryDeleted(entry $entry) 
	{
		$this->syncableDeleted($entry->getId(), FileSyncObjectType::ENTRY);
		
		// delete flavor assets
		$c = new Criteria();
		$c->add(assetPeer::ENTRY_ID, $entry->getId());
		$c->add(assetPeer::STATUS, asset::FLAVOR_ASSET_STATUS_DELETED, Criteria::NOT_EQUAL);
		$c->add(assetPeer::DELETED_AT, null, Criteria::ISNULL);
		
		$assets = assetPeer::doSelect($c);
		foreach($assets as $asset)
		{
			$asset->setStatus(asset::FLAVOR_ASSET_STATUS_DELETED);
			$asset->setDeletedAt(time());
			$asset->save();
		}
	
		$c = new Criteria();
		$c->add(assetParamsOutputPeer::ENTRY_ID, $entry->getId());
		$c->add(assetParamsOutputPeer::DELETED_AT, null, Criteria::ISNULL);
		$flavorParamsOutputs = assetParamsOutputPeer::doSelect($c);
		foreach($flavorParamsOutputs as $flavorParamsOutput)
		{
			$flavorParamsOutput->setDeletedAt(time());
			$flavorParamsOutput->save();
		}
		
		EntryServerNodePeer::deleteByEntryId($entry->getId());
		
		$filter = new categoryEntryFilter();
		$filter->setEntryIdEqual($entry->getId());
		
		$c = new Criteria();
		$c->add(categoryEntryPeer::ENTRY_ID, $entry->getId());
		if(categoryEntryPeer::doSelectOne($c)) {
			kJobsManager::addDeleteJob($entry->getPartnerId(), DeleteObjectType::CATEGORY_ENTRY, $filter);
		}
		
		$userEntryFilter = new UserEntryFilter();
		$userEntryFilter->set("_eq_entry_id", $entry->getId());
		
		$c = new Criteria();
		$c->add(UserEntryPeer::ENTRY_ID, $entry->getId());
		if(!UserEntryPeer::doSelectOne($c)) {
			return;
		}
		
		kJobsManager::addDeleteJob($entry->getPartnerId(), DeleteObjectType::USER_ENTRY, $userEntryFilter);
	}
	
	protected function kuserDelete(kuser $kuser)
	{
		$filter = new categoryKuserFilter();
		$filter->setUserIdEqual($kuser->getPuserId());
		
		$c = new Criteria();
		$c->add(categoryKuserPeer::PUSER_ID, $kuser->getPuserId());
		if(categoryKuserPeer::doSelectOne($c)) {
			kJobsManager::addDeleteJob($kuser->getPartnerId(), DeleteObjectType::CATEGORY_USER, $filter);
		}

		if ($kuser->getType() == KuserType::USER){
			// remove user from groups
			KuserKgroupPeer::deleteByKuserId($kuser->getId());
		}
		elseif 	($kuser->getType() == KuserType::GROUP){
			// remove users from group
			$filter = new KuserKgroupFilter();
			$filter->setGroupIdEqual($kuser->getPuserId());

			$c = new Criteria();
			$c->add(KuserKgroupPeer::PGROUP_ID, $kuser->getPuserId());
			if(KuserKgroupPeer::doSelectOne($c)) {
				kJobsManager::addDeleteJob($kuser->getPartnerId(), DeleteObjectType::GROUP_USER, $filter);
			}
		}
		
		$userEntryFilter = new UserEntryFilter();
		$userEntryFilter->set("_eq_user_id", $kuser->getId());
		
		$c = new Criteria();
		$c->add(UserEntryPeer::KUSER_ID, $kuser->getId());
		if(!UserEntryPeer::doSelectOne($c)) {
			return;
		}
		
		kJobsManager::addDeleteJob($kuser->getPartnerId(), DeleteObjectType::USER_ENTRY, $userEntryFilter);
	}
	
	/**
	 * @param category $category
	 */
	protected function categoryDeleted(category $category)
	{
		//TODO - ADD JOB TO DELETE ALL CategoryKusers.
	}
	
	/**
	 * @param uiConf $uiConf
	 */
	protected function uiConfDeleted(uiConf $uiConf) 
	{
		$this->syncableDeleted($uiConf->getId(), FileSyncObjectType::UICONF);
	}

	/**
	 * @param BatchJob $batchJob
	 */
	protected function batchJobDeleted(BatchJob $batchJob) 
	{
		$this->syncableDeleted($batchJob->getId(), FileSyncObjectType::BATCHJOB);
	}

	/**
	 * @param asset $asset
	 */
	protected function assetDeleted(asset $asset) 
	{
		$this->syncableDeleted($asset->getId(), FileSyncObjectType::FLAVOR_ASSET);
	}
	
	/**
	 * @param assetParams $assetParam
	 */
	protected function assetParamDeleted(assetParams $assetParam) 
	{
		//In Case Flavor Deleted Belongs To Partner 0 Exit Without Deleteing
		if($assetParam->getPartnerId() == 0) 
		{
			KalturaLog::log("Deleting Flavor Param Of Partner 0 Is Restricted");
			return;
		}
		
		$c = new Criteria();
		$c->setLimit(100);
		$c->add(flavorParamsConversionProfilePeer::FLAVOR_PARAMS_ID, $assetParam->getId());
		
		for(;;)
		{
			$flavorParamsConversionProfiles = flavorParamsConversionProfilePeer::doSelect($c);
			
			foreach($flavorParamsConversionProfiles as $flavorParamsConversionProfile)
			{
				/* @var $flavorParamsConversionProfile flavorParamsConversionProfile */ 
				$flavorParamsConversionProfile->delete();
			}
			
			if(count($flavorParamsConversionProfiles) < 100)
				break;	
			
			flavorParamsConversionProfilePeer::clearInstancePool();
		}
		
		KalturaLog::info("Flavor Params Conversion Profile Relations were deleted for flavor param id [" . $assetParam->getId() . "]");
	}
	
	/**
	 * @param syndicationFeed $syndicationFeed
	 */
	protected function syndicationFeedDeleted(syndicationFeed $syndicationFeed)
	{
		if($syndicationFeed->getType() == syndicationFeedType::KALTURA_XSLT)
			$this->syncableDeleted($syndicationFeed->getId(), FileSyncObjectType::SYNDICATION_FEED);
	}
	
	/**
	 * @param conversionProfile2 $conversionProfile
	 */
	protected function conversionProfileDeleted(conversionProfile2 $conversionProfile)
	{
		$this->syncableDeleted($conversionProfile->getId(), FileSyncObjectType::CONVERSION_PROFILE);
	}

	/**
	 * @param FileSync $fileSync
	 */
	protected function fileSyncDelete(FileSync $fileSync, BatchJob $raisedJob = null)
	{
		$partnerId = $fileSync->getPartnerId();
		$purgePermission = PermissionPeer::isValidForPartner('PURGE_FILES_ON_DELETE', $partnerId);
		if ($purgePermission)
		{
			$syncKey = kFileSyncUtils::getKeyForFileSync($fileSync);
			kJobsManager::addDeleteFileJob($raisedJob, null, $partnerId, $syncKey, $fileSync->getFullPath(), $fileSync->getDc());
		}
	}

}
