<?php
/**
 * @package api
 * @subpackage services
 */
class KalturaEntryService extends KalturaBaseService 
{
	
	/* (non-PHPdoc)
	 * @see KalturaBaseService::globalPartnerAllowed()
	 */
	protected function globalPartnerAllowed($actionName)
	{
		if($actionName == 'get')
			return true;
		
		return parent::globalPartnerAllowed($actionName);
	}
	
	public function initService($serviceId, $serviceName, $actionName)
	{
		$ks = kCurrentContext::$ks_object ? kCurrentContext::$ks_object : null;
		
		if (($actionName == 'list' || $actionName == 'count' || $actionName == 'listByReferenceId') &&
		  (!$ks || (!$ks->isAdmin() && !$ks->verifyPrivileges(ks::PRIVILEGE_LIST, ks::PRIVILEGE_WILDCARD))))
		{			
			KalturaCriterion::enableTag(KalturaCriterion::TAG_WIDGET_SESSION);
			entryPeer::setUserContentOnly(true);
		}
		
		
/*		//to support list categories with entitlmenet for user that is a member of more then 100 large categories
 		//large category is a category with > 10 members or > 100 entries. 				
  		if ($actionName == 'list' && kEntitlementUtils::getEntitlementEnforcement())
		{
			$dispatcher = KalturaDispatcher::getInstance();
			$arguments = $dispatcher->getArguments();
			
			$categoriesIds = array();
			$categories = array();
			foreach($arguments as $argument)
			{
				if ($argument instanceof KalturaBaseEntryFilter)
				{
					if(isset($argument->categoriesMatchAnd))
						$categories = array_merge($categories, explode(',', $argument->categoriesMatchAnd));
						
					if(isset($argument->categoriesMatchOr))
						$categories = array_merge($categories, explode(',', $argument->categoriesMatchOr));
					
					if(isset($argument->categoriesFullNameIn))
						$categories = array_merge($categories, explode(',', $argument->categoriesFullNameIn));
						
					if(count($categories))
					{
						$categories = categoryPeer::getByFullNamesExactMatch($categories);
						
						foreach ($categories as $category)
							$categoriesIds[] = $category->getId();
					}
										
					if(isset($argument->categoriesIdsMatchAnd))
						$categoriesIds = array_merge($categoriesIds, explode(',', $argument->categoriesIdsMatchAnd));
					
					if(isset($argument->categoriesIdsMatchOr))
						$categoriesIds = array_merge($categoriesIds, explode(',', $argument->categoriesIdsMatchOr));
					
					if(isset($argument->categoryAncestorIdIn))
						$categoriesIds = array_merge($categoriesIds, explode(',', $argument->categoryAncestorIdIn));
				}
			}
			
			foreach($categoriesIds as $key => $categoryId)
			{
				if(!$categoryId)
				{
					unset($categoriesIds[$key]);
				}
			}
			
			if(count($categoriesIds))
				entryPeer::setFilterdCategoriesIds($categoriesIds);
		}*/
		
		parent::initService($serviceId, $serviceName, $actionName);
		$this->applyPartnerFilterForClass('ConversionProfile');
		$this->applyPartnerFilterForClass('conversionProfile2');
	}
	
	/**
	 * @param kResource $resource
	 * @param entry $dbEntry
	 * @param asset $asset
	 * @return asset
	 * @throws KalturaErrors::ENTRY_TYPE_NOT_SUPPORTED
	 */
	protected function attachResource(kResource $resource, entry $dbEntry, asset $asset = null)
	{
		throw new KalturaAPIException(KalturaErrors::ENTRY_TYPE_NOT_SUPPORTED, $dbEntry->getType());
	}
	
	/**
	 * @param KalturaResource $resource
	 * @param entry $dbEntry
	 */
	protected function replaceResource(KalturaResource $resource, entry $dbEntry)
	{
		throw new KalturaAPIException(KalturaErrors::ENTRY_TYPE_NOT_SUPPORTED, $dbEntry->getType());
	}
	
	/**
	 * General code that replaces given entry resource with a given resource, and mark the original
	 * entry as replaced
	 * @param KalturaEntry $dbEntry The original entry we'd like to replace
	 * @param KalturaResource $resource The resource we'd like to attach
	 * @param KalturaEntry $tempMediaEntry The replacing entry
	 * @throws KalturaAPIException
	 */
	protected function replaceResourceByEntry($dbEntry, $resource, $tempMediaEntry) 
	{
		$partner = $this->getPartner();
		if(!$partner->getEnabledService(PermissionName::FEATURE_ENTRY_REPLACEMENT))
		{
			KalturaLog::notice("Replacement is not allowed to the partner permission [FEATURE_ENTRY_REPLACEMENT] is needed");
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, PermissionName::FEATURE_ENTRY_REPLACEMENT);
		}
		
		if($dbEntry->getReplacingEntryId())
			throw new KalturaAPIException(KalturaErrors::ENTRY_REPLACEMENT_ALREADY_EXISTS);
		
		$resource->validateEntry($dbEntry);
		
		$tempDbEntry = $this->prepareEntryForInsert($tempMediaEntry);
		$tempDbEntry->setDisplayInSearch(mySearchUtils::DISPLAY_IN_SEARCH_SYSTEM);
		$tempDbEntry->setPartnerId($dbEntry->getPartnerId());
		$tempDbEntry->setReplacedEntryId($dbEntry->getId());
		$tempDbEntry->save();
		
		$dbEntry->setReplacingEntryId($tempDbEntry->getId());
		$dbEntry->setReplacementStatus(entryReplacementStatus::NOT_READY_AND_NOT_APPROVED);
		if(!$partner->getEnabledService(PermissionName::FEATURE_ENTRY_REPLACEMENT_APPROVAL))
			$dbEntry->setReplacementStatus(entryReplacementStatus::APPROVED_BUT_NOT_READY);
		$dbEntry->save();
		
		$kResource = $resource->toObject();
		$this->attachResource($kResource, $tempDbEntry);
	}
	
	/**
	 * Approves entry replacement
	 *
	 * @param string $entryId entry id to replace
	 * @param KalturaEntryType $entryType the entry type
	 * @return KalturaMediaEntry The replaced media entry
	 *
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	protected function approveReplace($entryId, $entryType)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
	
		if (!$dbEntry || $dbEntry->getType() != $entryType)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
	
		switch($dbEntry->getReplacementStatus())
		{
			case entryReplacementStatus::APPROVED_BUT_NOT_READY:
				break;
	
			case entryReplacementStatus::READY_BUT_NOT_APPROVED:
				kBusinessConvertDL::replaceEntry($dbEntry);
				break;
	
			case entryReplacementStatus::NOT_READY_AND_NOT_APPROVED:
				$dbEntry->setReplacementStatus(entryReplacementStatus::APPROVED_BUT_NOT_READY);
				$dbEntry->save();
	
				//preventing race conditions of temp entry being ready just as you approve the replacement
				$dbReplacingEntry = entryPeer::retrieveByPK($dbEntry->getReplacingEntryId());
				if ($dbReplacingEntry && $dbReplacingEntry->getStatus() == entryStatus::READY)
					kBusinessConvertDL::replaceEntry($dbEntry);
				break;
	
			case entryReplacementStatus::NONE:
			default:
				throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_REPLACED, $entryId);
				break;
		}
	
		return $this->getEntry($entryId, -1, $entryType);
	}
	
	/**
	 * Cancels media replacement
	 *
	 * @param string $entryId Media entry id to cancel
	 * @param KalturaEntryType $entryType the entry type
	 * @return KalturaMediaEntry The canceled media entry
	 *
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	protected function cancelReplace($entryId, $entryType)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
	
		if (!$dbEntry || $dbEntry->getType() != $entryType)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
	
		if($dbEntry->getReplacingEntryId())
		{
			$dbTempEntry = entryPeer::retrieveByPK($dbEntry->getReplacingEntryId());
			if($dbTempEntry)
				myEntryUtils::deleteEntry($dbTempEntry);
		}
	
		$dbEntry->setReplacingEntryId(null);
		$dbEntry->setReplacementStatus(entryReplacementStatus::NONE);
		$dbEntry->save();
	
		return $this->getEntry($entryId, -1, $entryType);
	}
	
	/**
	 * @param kFileSyncResource $resource
	 * @param entry $dbEntry
	 * @param asset $dbAsset
	 * @return asset
	 * @throws KalturaErrors::UPLOAD_ERROR
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */
	protected function attachFileSyncResource(kFileSyncResource $resource, entry $dbEntry, asset $dbAsset = null)
	{
		$dbEntry->setSource(entry::ENTRY_MEDIA_SOURCE_KALTURA);
		$dbEntry->save();
		
		try{
			$syncable = kFileSyncObjectManager::retrieveObject($resource->getFileSyncObjectType(), $resource->getObjectId());
		}
		catch(kFileSyncException $e){
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $resource->getObjectId());
		}
		
		$srcSyncKey = $syncable->getSyncKey($resource->getObjectSubType(), $resource->getVersion());
		$dbAsset = $this->attachFileSync($srcSyncKey, $dbEntry, $dbAsset);
		
		// Copy the media info from the old asset to the new one
		if($syncable instanceof asset && $resource->getObjectSubType() == asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET)
		{
			$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($syncable->getId());
			if($mediaInfo)
			{
				$newMediaInfo = $mediaInfo->copy();
				$newMediaInfo->setFlavorAssetId($dbAsset->getId());
				$newMediaInfo->save();
			}
			
			if ($dbAsset->getStatus() == asset::ASSET_STATUS_READY)
			{
				$dbEntry->syncFlavorParamsIds();
				$dbEntry->save();
			}
		}
		
		return $dbAsset;
	}
	
	/**
	 * @param kLocalFileResource $resource
	 * @param entry $dbEntry
	 * @param asset $dbAsset
	 * @return asset
	 */
	protected function attachLocalFileResource(kLocalFileResource $resource, entry $dbEntry, asset $dbAsset = null)
	{
		$dbEntry->setSource($resource->getSourceType());
		$dbEntry->save();
		
		if($resource->getIsReady())
			return $this->attachFile($resource->getLocalFilePath(), $dbEntry, $dbAsset, $resource->getKeepOriginalFile());
	
		$lowerStatuses = array(
			entryStatus::ERROR_CONVERTING,
			entryStatus::ERROR_IMPORTING,
			entryStatus::PENDING,
			entryStatus::NO_CONTENT,
		);
		
		if(in_array($dbEntry->getStatus(), $lowerStatuses))
		{
			$dbEntry->setStatus(entryStatus::IMPORT);
			$dbEntry->save();
		}
			
		// TODO - move image handling to media service
		if($dbEntry->getMediaType() == KalturaMediaType::IMAGE)
		{
			$resource->attachCreatedObject($dbEntry);
			return null;
		}
		
		$isNewAsset = false;
		if(!$dbAsset)
		{
			$isNewAsset = true;
 			KalturaLog::debug("Creating original flavor asset for unready local file");
			$dbAsset = kFlowHelper::createOriginalFlavorAsset($this->getPartnerId(), $dbEntry->getId());
		}
		
		if(!$dbAsset)
		{
			if($dbEntry->getStatus() == entryStatus::NO_CONTENT)
			{
				$dbEntry->setStatus(entryStatus::ERROR_CONVERTING);
				$dbEntry->save();
			}
			
			return null;
		}
		
		$dbAsset->setStatus(asset::FLAVOR_ASSET_STATUS_IMPORTING);
		$dbAsset->save();
		
		$resource->attachCreatedObject($dbAsset);
		
		return $dbAsset;
	}
	
	/**
	 * @param string $entryFullPath
	 * @param entry $dbEntry
	 * @param asset $dbAsset
	 * @return asset
	 * @throws KalturaErrors::UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY
	 * @throws KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN
	 */
	protected function attachFile($entryFullPath, entry $dbEntry, asset $dbAsset = null, $copyOnly = false)
	{
		// TODO - move image handling to media service
		if($dbEntry->getMediaType() == KalturaMediaType::IMAGE)
		{
			$exifImageType = @exif_imagetype($entryFullPath);
			$validTypes = array(
				IMAGETYPE_JPEG,
				IMAGETYPE_TIFF_II,
				IMAGETYPE_TIFF_MM,
				IMAGETYPE_IFF,
				IMAGETYPE_PNG
			);
			
			if(in_array($exifImageType, $validTypes))
			{
				$exifData = @exif_read_data($entryFullPath);
				if ($exifData && isset($exifData["DateTimeOriginal"]) && $exifData["DateTimeOriginal"])
				{
					$mediaDate = $exifData["DateTimeOriginal"];
					$ts = strtotime($mediaDate);
					
					// handle invalid dates either due to bad format or out of range
					if ($ts === -1 || $ts === false || $ts < strtotime('2000-01-01') || $ts > strtotime('2015-01-01'))
						$mediaDate = null;
					
					$dbEntry->setMediaDate($mediaDate);
				}
			}
			
			list($width, $height, $type, $attr) = getimagesize($entryFullPath);
			$dbEntry->setDimensions($width, $height);
			$dbEntry->setData(".jpg"); // this will increase the data version
			$dbEntry->save();
			$syncKey = $dbEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
			try
			{
				kFileSyncUtils::moveFromFile($entryFullPath, $syncKey, true, $copyOnly);
			}
			catch (Exception $e) {
				if($dbEntry->getStatus() == entryStatus::NO_CONTENT)
				{
					$dbEntry->setStatus(entryStatus::ERROR_CONVERTING);
					$dbEntry->save();
				}											
				throw $e;
			}
			
			$dbEntry->setStatus(entryStatus::READY);
			$dbEntry->save();	
				
			return null;
		}
		
		$isNewAsset = false;
		if(!$dbAsset)
		{
			$isNewAsset = true;
 			KalturaLog::debug("Creating original flavor asset for local file");
			$dbAsset = kFlowHelper::createOriginalFlavorAsset($this->getPartnerId(), $dbEntry->getId());
		}
		
		if(!$dbAsset && $dbEntry->getStatus() == entryStatus::NO_CONTENT)
		{
			$dbEntry->setStatus(entryStatus::ERROR_CONVERTING);
			$dbEntry->save();
		}
		
		$ext = pathinfo($entryFullPath, PATHINFO_EXTENSION);
		$dbAsset->setFileExt($ext);
		$dbAsset->save();
		
		if($dbAsset && ($dbAsset instanceof thumbAsset))
		{
			list($width, $height, $type, $attr) = getimagesize($entryFullPath);
			$dbAsset->setWidth($width);
			$dbAsset->setHeight($height);
			$dbAsset->save();
		}
		
		$syncKey = $dbAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		
		try {
			kFileSyncUtils::moveFromFile($entryFullPath, $syncKey, true, $copyOnly);
		}
		catch (Exception $e) {
			
			if($dbEntry->getStatus() == entryStatus::NO_CONTENT)
			{
				$dbEntry->setStatus(entryStatus::ERROR_CONVERTING);
				$dbEntry->save();
			}
			
			$dbAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
			$dbAsset->save();												
			throw $e;
		}
		
		if($dbAsset && !($dbAsset instanceof flavorAsset))
		{
		    $dbAsset->setStatusLocalReady();
				
			if($dbAsset->getFlavorParamsId())
			{
				$dbFlavorParams = assetParamsPeer::retrieveByPK($dbAsset->getFlavorParamsId());
				if($dbFlavorParams)
					$dbAsset->setTags($dbFlavorParams->getTags());
			}
			$dbAsset->save();
		}
		
		if($isNewAsset)
			kEventsManager::raiseEvent(new kObjectAddedEvent($dbAsset));
		kEventsManager::raiseEvent(new kObjectDataChangedEvent($dbAsset));
			
		return $dbAsset;
	}
	
	/**
	 * @param FileSyncKey $srcSyncKey
	 * @param entry $dbEntry
	 * @param asset $dbAsset
	 * @return asset
	 * @throws KalturaErrors::ORIGINAL_FLAVOR_ASSET_NOT_CREATED
	 */
	protected function attachFileSync(FileSyncKey $srcSyncKey, entry $dbEntry, asset $dbAsset = null)
	{
		// TODO - move image handling to media service
		if($dbEntry->getMediaType() == KalturaMediaType::IMAGE)
		{
			$syncKey = $dbEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
	   		kFileSyncUtils::createSyncFileLinkForKey($syncKey, $srcSyncKey);
	   		
			$dbEntry->setStatus(entryStatus::READY);
			$dbEntry->save();	
				
			return null;
		}
		
	  	$isNewAsset = false;
	  	if(!$dbAsset)
	  	{
	  		$isNewAsset = true;
			$dbAsset = kFlowHelper::createOriginalFlavorAsset($this->getPartnerId(), $dbEntry->getId());
	  	}
	  	
		if(!$dbAsset)
		{
			KalturaLog::err("Flavor asset not created for entry [" . $dbEntry->getId() . "]");
			
			if($dbEntry->getStatus() == entryStatus::NO_CONTENT)
			{
				$dbEntry->setStatus(entryStatus::ERROR_CONVERTING);
				$dbEntry->save();
			}
			
			throw new KalturaAPIException(KalturaErrors::ORIGINAL_FLAVOR_ASSET_NOT_CREATED);
		}
				
		$newSyncKey = $dbAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		kFileSyncUtils::createSyncFileLinkForKey($newSyncKey, $srcSyncKey);

		if($isNewAsset)
			kEventsManager::raiseEvent(new kObjectAddedEvent($dbAsset));
		kEventsManager::raiseEvent(new kObjectDataChangedEvent($dbAsset));
			
		return $dbAsset;
	}
	
	/**
	 * @param kOperationResource $resource
	 * @param entry $dbEntry
	 * @param asset $dbAsset
	 * @return asset
	 */
	protected function attachOperationResource(kOperationResource $resource, entry $dbEntry, asset $dbAsset = null)
	{
		$isNewAsset = false;
		$isSource = false;
		if($dbAsset)
		{
			if($dbAsset instanceof flavorAsset)
				$isSource = $dbAsset->getIsOriginal();
		}
		else
		{
			$isNewAsset = true;
			$isSource = true;
 			KalturaLog::debug("Creating original flavor asset");
			$dbAsset = kFlowHelper::createOriginalFlavorAsset($this->getPartnerId(), $dbEntry->getId());
		}
		
		if(!$dbAsset && $dbEntry->getStatus() == entryStatus::NO_CONTENT)
		{
			$dbEntry->setStatus(entryStatus::ERROR_CONVERTING);
			$dbEntry->save();
		}
		
		$internalResource = $resource->getResource();
		$dbAsset = $this->attachResource($internalResource, $dbEntry, $dbAsset);
		
		$errDescription = '';
		kBusinessPreConvertDL::decideAddEntryFlavor(null, $dbEntry->getId(), $resource->getAssetParamsId(), $errDescription, $dbAsset->getId(), $resource->getOperationAttributes());
		
		if($isNewAsset)
			kEventsManager::raiseEvent(new kObjectAddedEvent($dbAsset));
		kEventsManager::raiseEvent(new kObjectDataChangedEvent($dbAsset));
			
		if($isSource && $internalResource instanceof kFileSyncResource)
		{
			$srcEntryId = $internalResource->getEntryId();
			if($srcEntryId)
			{
				$srcEntry = entryPeer::retrieveByPKNoFilter($srcEntryId);
				if($srcEntry)
					$dbEntry->setRootEntryId($srcEntry->getRootEntryId(true));
			}
			
			$dbEntry->setOperationAttributes($resource->getOperationAttributes());
			$dbEntry->save();
		}
		
		return $dbAsset;
	}
	
	/**
	 * @param IRemoteStorageResource $resource
	 * @param entry $dbEntry
	 * @param asset $dbAsset
	 * @return asset
	 * @throws KalturaErrors::ORIGINAL_FLAVOR_ASSET_NOT_CREATED
	 * @throws KalturaErrors::STORAGE_PROFILE_ID_NOT_FOUND
	 */
	protected function attachRemoteStorageResource(IRemoteStorageResource $resource, entry $dbEntry, asset $dbAsset = null)
	{
		$resources = $resource->getResources();
		$fileExt = $resource->getFileExt();
		$dbEntry->setSource(KalturaSourceType::URL);
	
		// TODO - move image handling to media service
		if($dbEntry->getMediaType() == KalturaMediaType::IMAGE)
		{
			$syncKey = $dbEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
			foreach($resources as $currentResource)
			{
				$storageProfile = StorageProfilePeer::retrieveByPK($currentResource->getStorageProfileId());
				$fileSync = kFileSyncUtils::createReadyExternalSyncFileForKey($syncKey, $currentResource->getUrl(), $storageProfile);
			}
			
			$dbEntry->setStatus(entryStatus::READY);
			$dbEntry->save();
				
			return null;
		}
		$dbEntry->save();
		
	  	$isNewAsset = false;
	  	if(!$dbAsset)
	  	{
	  		$isNewAsset = true;
			$dbAsset = kFlowHelper::createOriginalFlavorAsset($this->getPartnerId(), $dbEntry->getId());
	  	}
	  	
		if(!$dbAsset)
		{
			KalturaLog::err("Flavor asset not created for entry [" . $dbEntry->getId() . "]");
			
			if($dbEntry->getStatus() == entryStatus::NO_CONTENT)
			{
				$dbEntry->setStatus(entryStatus::ERROR_CONVERTING);
				$dbEntry->save();
			}
			
			throw new KalturaAPIException(KalturaErrors::ORIGINAL_FLAVOR_ASSET_NOT_CREATED);
		}
				
		$syncKey = $dbAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
	
		foreach($resources as $currentResource)
		{
			$storageProfile = StorageProfilePeer::retrieveByPK($currentResource->getStorageProfileId());
			$fileSync = kFileSyncUtils::createReadyExternalSyncFileForKey($syncKey, $currentResource->getUrl(), $storageProfile);
		}

		$dbAsset->setFileExt($fileExt);
				
		if($dbAsset instanceof flavorAsset && !$dbAsset->getIsOriginal())
			$dbAsset->setStatus(asset::FLAVOR_ASSET_STATUS_READY);
			
		$dbAsset->save();
		
		if($isNewAsset)
			kEventsManager::raiseEvent(new kObjectAddedEvent($dbAsset));
		kEventsManager::raiseEvent(new kObjectDataChangedEvent($dbAsset));
			
		if($dbAsset instanceof flavorAsset && !$dbAsset->getIsOriginal())
			kBusinessPostConvertDL::handleConvertFinished(null, $dbAsset);
		
		return $dbAsset;
	}
	
	/**
	 * @param kUrlResource $resource
	 * @param entry $dbEntry
	 * @param asset $dbAsset
	 * @return asset
	 */
	protected function attachUrlResource(kUrlResource $resource, entry $dbEntry, asset $dbAsset = null)
	{
		if($dbAsset instanceof flavorAsset)
		{
			$dbEntry->setSource(KalturaSourceType::URL);
			$dbEntry->save();
		}
		
		$url = $resource->getUrl();
		
		if (!$resource->forceAsyncDownload())
		{
			// TODO - move image handling to media service
    		if($dbEntry->getMediaType() == KalturaMediaType::IMAGE)
    		{
    			$entryFullPath = myContentStorage::getFSUploadsPath() . '/' . $dbEntry->getId() . '.jpg';
    			if (KCurlWrapper::getDataFromFile($url, $entryFullPath))
    				return $this->attachFile($entryFullPath, $dbEntry, $dbAsset);
    			
    			KalturaLog::err("Failed downloading file[$url]");
    			$dbEntry->setStatus(entryStatus::ERROR_IMPORTING);
    			$dbEntry->save();
    			
    			return null;
    		}
    	
    		if($dbAsset && !($dbAsset instanceof flavorAsset))
    		{
    			$ext = pathinfo($url, PATHINFO_EXTENSION);
    			$entryFullPath = myContentStorage::getFSUploadsPath() . '/' . $dbEntry->getId() . '.' . $ext;
    			if (KCurlWrapper::getDataFromFile($url, $entryFullPath))
    			{
    				$dbAsset = $this->attachFile($entryFullPath, $dbEntry, $dbAsset);
    				return $dbAsset;
    			}
    			
    			KalturaLog::err("Failed downloading file[$url]");
    			$dbAsset->setStatus(asset::FLAVOR_ASSET_STATUS_ERROR);
    			$dbAsset->save();
    			
    			return null;
    		}
		}
		
		kJobsManager::addImportJob(null, $dbEntry->getId(), $this->getPartnerId(), $url, $dbAsset, null, $resource->getImportJobData());
		
		return $dbAsset;
	}
	
	/**
	 * @param kAssetsParamsResourceContainers $resource
	 * @param entry $dbEntry
	 * @return asset
	 */
	protected function attachAssetsParamsResourceContainers(kAssetsParamsResourceContainers $resource, entry $dbEntry)
	{
		KalturaLog::debug("Resources [" . count($resource->getResources()) . "]");
		
		$ret = null;
		foreach($resource->getResources() as $assetParamsResourceContainer)
		{
			KalturaLog::debug("Resource asset params id [" . $assetParamsResourceContainer->getAssetParamsId() . "]");
			$dbAsset = $this->attachAssetParamsResourceContainer($assetParamsResourceContainer, $dbEntry);
			if(!$dbAsset)
				continue;
				
			KalturaLog::debug("Resource asset id [" . $dbAsset->getId() . "]");
			
			if($dbAsset->getIsOriginal())
				$ret = $dbAsset;
		}
		$dbEntry->save();
		
		return $ret;
	}
	
	/**
	 * @param kAssetParamsResourceContainer $resource
	 * @param entry $dbEntry
	 * @param asset $dbAsset
	 * @return asset
	 * @throws KalturaErrors::FLAVOR_PARAMS_ID_NOT_FOUND
	 */
	protected function attachAssetParamsResourceContainer(kAssetParamsResourceContainer $resource, entry $dbEntry, asset $dbAsset = null)
	{
		$assetParams = assetParamsPeer::retrieveByPK($resource->getAssetParamsId());
		if(!$assetParams)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_ID_NOT_FOUND, $resource->getAssetParamsId());
			
		if(!$dbAsset)
			$dbAsset = assetPeer::retrieveByEntryIdAndParams($dbEntry->getId(), $resource->getAssetParamsId());
			
		$isNewAsset = false;
		if(!$dbAsset)
		{
			$isNewAsset = true;
			$dbAsset = assetPeer::getNewAsset($assetParams->getType());
			$dbAsset->setPartnerId($dbEntry->getPartnerId());
			$dbAsset->setEntryId($dbEntry->getId());
			$dbAsset->setStatus(asset::FLAVOR_ASSET_STATUS_QUEUED);
			
			$dbAsset->setFlavorParamsId($resource->getAssetParamsId());
			$dbAsset->setFromAssetParams($assetParams);
			if($assetParams->hasTag(assetParams::TAG_SOURCE))
				$dbAsset->setIsOriginal(true);
		}
		$dbAsset->incrementVersion();
		$dbAsset->save();
		
		$dbAsset = $this->attachResource($resource->getResource(), $dbEntry, $dbAsset);
		
		if($dbAsset && $isNewAsset && $dbAsset->getStatus() != asset::FLAVOR_ASSET_STATUS_IMPORTING)
			kEventsManager::raiseEvent(new kObjectAddedEvent($dbAsset));
		
		return $dbAsset;
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
			
		if ($entry->licenseType === null)
			$entry->licenseType = KalturaLicenseType::UNKNOWN;
		
		// first copy all the properties to the db entry, then we'll check for security stuff
		if(!$dbEntry)
		{
			$coreType = kPluginableEnumsManager::apiToCore('entryType', $entry->type);
			$class = KalturaPluginManager::getObjectClass(entryPeer::OM_CLASS, $coreType);
			if(!$class)
				$class = entryPeer::OM_CLASS;
				
			KalturaLog::debug("Creating new entry of API type [$entry->type] core type [$coreType] class [$class]");
			$dbEntry = new $class();
		}
			
		$dbEntry = $entry->toInsertableObject($dbEntry);
//		KalturaLog::debug("Inserted entry id [" . $dbEntry->getId() . "] data [" . print_r($dbEntry->toArray(), true) . "]");

		$this->checkAndSetValidUserInsert($entry, $dbEntry);
		$this->checkAdminOnlyInsertProperties($entry);
		$this->validateAccessControlId($entry);
		$this->validateEntryScheduleDates($entry, $dbEntry);
			
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
			if($templateEntry)
			{
				$dbEntry = $templateEntry->copyTemplate(true);
				$dbEntry->save();
			}
			else
			{
				KalturaLog::err("Template entry id [" . $conversionProfile->getDefaultEntryId() . "] not found");
			}
		}
		
		return $this->prepareEntryForInsert($entry, $dbEntry);
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
			
		
			
		$srcFlavorAsset = assetPeer::retrieveOriginalByEntryId($entryId);
		if(!$srcFlavorAsset)
			throw new KalturaAPIException(KalturaErrors::ORIGINAL_FLAVOR_ASSET_IS_MISSING);
		
		if(is_null($conversionProfileId) || $conversionProfileId <= 0)
		{
			$conversionProfile = myPartnerUtils::getConversionProfile2ForEntry($entryId);
			if(!$conversionProfile)
				throw new KalturaAPIException(KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND, $conversionProfileId);
			
			$conversionProfileId = $conversionProfile->getId();
		} 

		else {
			//The search is with the entry's partnerId. so if conversion profile wasn't found it means that the 
			//conversionId is not exist or the conversion profileId does'nt belong to this partner.
			$conversionProfile = conversionProfile2Peer::retrieveByPK ( $conversionProfileId );
			if (is_null ( $conversionProfile )) {
				throw new KalturaAPIException ( KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND, $conversionProfileId );
			}
		}
		
		$srcSyncKey = $srcFlavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		
		// if the file sync isn't local (wasn't synced yet) proxy request to other datacenter
		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($srcSyncKey, true, false);
		if(!$fileSync)
		{
			throw new KalturaAPIException(KalturaErrors::FILE_DOESNT_EXIST);
		}
		else if(!$local)
		{
			kFileUtils::dumpApiRequest(kDataCenterMgr::getRemoteDcExternalUrl($fileSync));
		}
		
		// even if it null
		$entry->setConversionQuality($conversionProfileId);
		$entry->save();
		
		if($dynamicConversionAttributes)
		{
			$flavors = assetParamsPeer::retrieveByProfile($conversionProfileId);
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
			
			if($dbEntry->getStatus() == entryStatus::NO_CONTENT)
			{
				$dbEntry->setStatus(entryStatus::ERROR_CONVERTING);
				$dbEntry->save();
			}
			
			throw new KalturaAPIException(KalturaErrors::ORIGINAL_FLAVOR_ASSET_NOT_CREATED, $msg);
		}
				
		$srcSyncKey = $srcFlavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$newSyncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		kFileSyncUtils::createSyncFileLinkForKey($newSyncKey, $srcSyncKey);

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

	protected function getRemotePaths($entryId, $entryType = null)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);

		if (!$dbEntry || ($entryType !== null && $dbEntry->getType() != $entryType))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		if ($dbEntry->getStatus() != entryStatus::READY)
			throw new KalturaAPIException(KalturaErrors::ENTRY_NOT_READY, $entryId);

		$c = new Criteria();
		$c->add(FileSyncPeer::OBJECT_TYPE, FileSyncObjectType::ENTRY);
		$c->add(FileSyncPeer::OBJECT_SUB_TYPE, entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
		$c->add(FileSyncPeer::OBJECT_ID, $entryId);
		$c->add(FileSyncPeer::VERSION, $dbEntry->getVersion());
		$c->add(FileSyncPeer::PARTNER_ID, $dbEntry->getPartnerId());
		$c->add(FileSyncPeer::STATUS, FileSync::FILE_SYNC_STATUS_READY);
		$c->add(FileSyncPeer::FILE_TYPE, FileSync::FILE_SYNC_FILE_TYPE_URL);
		$fileSyncs = FileSyncPeer::doSelect($c);

		$listResponse = new KalturaRemotePathListResponse();
		$listResponse->objects = KalturaRemotePathArray::fromFileSyncArray($fileSyncs);
		$listResponse->totalCount = count($listResponse->objects);
		return $listResponse;
	}
	
	/**
	 * @param KalturaBaseEntryFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaCriteria
	 */
	protected function prepareEntriesCriteriaFilter(KalturaBaseEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaBaseEntryFilter();

		// because by default we will display only READY entries, and when deleted status is requested, we don't want this to disturb
		entryPeer::allowDeletedInCriteriaFilter(); 
	
		$this->setDefaultStatus($filter);
		$this->setDefaultModerationStatus($filter);
		$this->fixFilterUserId($filter);
		$this->fixFilterDuration($filter);
		
		$entryFilter = new entryFilter();
		$entryFilter->setPartnerSearchScope(baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE);
		
		$filter->toObject($entryFilter);

		$c = KalturaCriteria::create(entryPeer::OM_CLASS);
		
		if($pager)
			$pager->attachToCriteria($c);
			
		$entryFilter->attachToCriteria($c);
		
		$c->add(entryPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_SYSTEM, Criteria::NOT_EQUAL);
		
		return $c;
	}
	
	protected function listEntriesByFilter(KalturaBaseEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;

		$disableWidgetSessionFilters = false;
		if ($filter &&
			($filter->idEqual != null ||
			$filter->idIn != null ||
			$filter->referenceIdEqual != null ||
			$filter->referenceIdIn != null))
			$disableWidgetSessionFilters = true;
			
		if (!$pager)
			$pager = new KalturaFilterPager();
		
		$c = $this->prepareEntriesCriteriaFilter($filter, $pager);
		
		if ($disableWidgetSessionFilters)
			KalturaCriterion::disableTag(KalturaCriterion::TAG_WIDGET_SESSION);
			
		$list = entryPeer::doSelect($c);
		$totalCount = $c->getRecordsCount();
		
		if ($disableWidgetSessionFilters)
			KalturaCriterion::restoreTag(KalturaCriterion::TAG_WIDGET_SESSION);

		return array($list, $totalCount);		
	}
	
	protected function countEntriesByFilter(KalturaBaseEntryFilter $filter = null)
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;

		$c = $this->prepareEntriesCriteriaFilter($filter, null);
		$c->applyFilters();
		$totalCount = $c->getRecordsCount();
		
		return $totalCount;
	}
	
	/*
	 	The following table shows the behavior of the checkAndSetValidUser functions:
	 	
	 	 otheruser - any user that is not the user specified in the ks
	  
	 	Input	 	 											Result	 
		Action			API entry user		DB entry user		Admin KS			User KS
		----------------------------------------------------------------------------------------
		entry.add		null / ksuser		N/A					ksuser				ksuser
 						otheruser			N/A					otheruser			exception
		entry.update	null / ksuser		ksuser				stays ksuser		stays ksuser
 						otheruser			ksuser				otheruser			exception
 						ksuser				otheruser			ksuser				exception
 						null / otheruser	otheruser			stays otheruser		if has edit privilege on entry => stays otheruser (checked by checkIfUserAllowedToUpdateEntry), 
 																					otherwise exception
	 */
	
   	/**
   	 * Sets the valid user for the entry 
   	 * Throws an error if the session user is trying to add entry to another user and not using an admin session 
   	 *
   	 * @param KalturaBaseEntry $entry
   	 * @param entry $dbEntry
   	 */
	protected function checkAndSetValidUserInsert(KalturaBaseEntry $entry, entry $dbEntry)
	{
		KalturaLog::debug("Entry puser id [" . $entry->userId . "]");

		// for new entry, puser ID is null - set it from service scope
		if ($entry->userId === null)
		{
			KalturaLog::debug("Set kuser id [" . $this->getKuser()->getId() . "] line [" . __LINE__ . "]");
			$dbEntry->setPuserId($this->getKuser()->getPuserId());
			$dbEntry->setKuserId($this->getKuser()->getId());
			$dbEntry->setCreatorKuserId($this->getKuser()->getId());
			$dbEntry->setCreatorPuserId($this->getKuser()->getPuserId());
			return;
		}
		
		if ((!$this->getKs() || !$this->getKs()->isAdmin()))
		{
			// non admin cannot specify a different user on the entry other than himself
			$ksPuser = $this->getKuser()->getPuserId();
			if (strtolower($entry->userId) != strtolower($ksPuser))
			{
				throw new KalturaAPIException(KalturaErrors::INVALID_KS, "", ks::INVALID_TYPE, ks::getErrorStr(ks::INVALID_TYPE));
			}
		}
		
		// need to create kuser if this is an admin creating the entry on a different user
		$kuser = kuserPeer::createKuserForPartner($this->getPartnerId(), $entry->userId);
		$creator = kuserPeer::createKuserForPartner($this->getPartnerId(), $entry->creatorId);  

		KalturaLog::debug("Set kuser id [" . $kuser->getId() . "] line [" . __LINE__ . "]");
		$dbEntry->setKuserId($kuser->getId());
		$dbEntry->setCreatorKuserId($creator->getId());
		$dbEntry->setCreatorPuserId($creator->getPuserId());
	}
	
   	/**
   	 * Sets the valid user for the entry 
   	 * Throws an error if the session user is trying to update entry to another user and not using an admin session 
   	 *
   	 * @param KalturaBaseEntry $entry
   	 * @param entry $dbEntry
   	 */
	protected function checkAndSetValidUserUpdate(KalturaBaseEntry $entry, entry $dbEntry)
	{
		KalturaLog::debug("DB puser id [" . $dbEntry->getPuserId() . "] kuser id [" . $dbEntry->getKuserId() . "]");

		// user id not being changed
		if ($entry->userId === null)
		{
			KalturaLog::debug("entry->userId is null, not changing user");
			return;
		}
		
		if ((!$this->getKs() || !$this->getKs()->isAdmin()))
		{
			$entryPuserId = $dbEntry->getPuserId();
			
			// non admin cannot change the owner of an existing entry
			if (strtolower($entry->userId) != strtolower($entryPuserId))
			{
				KalturaLog::debug('API entry userId ['.$entry->userId.'], DB entry userId ['.$entryPuserId.'] - change required but KS is not admin');
				throw new KalturaAPIException(KalturaErrors::INVALID_KS, "", ks::INVALID_TYPE, ks::getErrorStr(ks::INVALID_TYPE));
			}
		}
		
		// need to create kuser if this is an admin changing the owner of the entry to a different user
		$kuser = kuserPeer::createKuserForPartner($dbEntry->getPartnerId(), $entry->userId); 

		KalturaLog::debug("Set kuser id [" . $kuser->getId() . "] line [" . __LINE__ . "]");
		$dbEntry->setKuserId($kuser->getId());
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
			$this->applyPartnerFilterForClass('accessControl'); 
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
	protected function validateEntryScheduleDates(KalturaBaseEntry $entry, entry $dbEntry)
	{
		if(is_null($entry->startDate) && is_null($entry->endDate))
			return; // no update

		if($entry->startDate instanceof KalturaNullField)
			$entry->startDate = -1;
		if($entry->endDate instanceof KalturaNullField)
			$entry->endDate = -1;
			
		// if input is null and this is an update pick the current db value 
		$startDate = is_null($entry->startDate) ?  $dbEntry->getStartDate(null) : $entry->startDate;
		$endDate = is_null($entry->endDate) ?  $dbEntry->getEndDate(null) : $entry->endDate;
		
		// normalize values for valid comparison later 
		if ($startDate < 0)
			$startDate = null;
		
		if ($endDate < 0)
			$endDate = null;
		
		if ($startDate && $endDate && $startDate >= $endDate)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_SCHEDULE_DATES);
		}
	}
	

	protected function createDummyKShow()
	{
		$kshow = new kshow();
		$kshow->setName(kshow::DUMMY_KSHOW_NAME);
		$kshow->setProducerId($this->getKuser()->getId());
		$kshow->setPartnerId($this->getPartnerId());
		$kshow->setSubpId($this->getPartnerId() * 100);
		$kshow->setViewPermissions(kshow::KSHOW_PERMISSION_EVERYONE);
		$kshow->setPermissions(kshow::PERMISSIONS_PUBLIC);
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
		
		
		$this->checkAndSetValidUserUpdate($entry, $dbEntry);
		$this->checkAdminOnlyUpdateProperties($entry);
		$this->validateAccessControlId($entry);
		$this->validateEntryScheduleDates($entry, $dbEntry); 
		
		$dbEntry = $entry->toUpdatableObject($dbEntry);
		/* @var $dbEntry entry */
		
		$dbEntry->save();
		$entry->fromObject($dbEntry);
		
		try 
		{
			$wrapper = objectWrapperBase::getWrapperClass($dbEntry);
			$wrapper->removeFromCache("entry", $dbEntry->getId());
		}
		catch(Exception $e)
		{
			KalturaLog::err($e);
		}
		
		myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE, $dbEntry);
		
		return $entry;
	}
	
	protected function deleteEntry($entryId, $entryType = null)
	{
		$entryToDelete = entryPeer::retrieveByPK($entryId);

		if (!$entryToDelete || ($entryType !== null && $entryToDelete->getType() != $entryType))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		
		
		myEntryUtils::deleteEntry($entryToDelete);
		
		try
		{
			$wrapper = objectWrapperBase::getWrapperClass($entryToDelete);
			$wrapper->removeFromCache("entry", $entryToDelete->getId());
		}
		catch(Exception $e)
		{
			KalturaLog::err($e);
		}
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
		
		myEntryUtils::updateThumbnailFromFile($dbEntry, $url, $fileSyncType);
		
		$entry = KalturaEntryFactory::getInstanceByType($dbEntry->getType());
		$entry->fromObject($dbEntry);
		
		return $entry;
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
		
		myEntryUtils::updateThumbnailFromFile($dbEntry, $fileData["tmp_name"], $fileSyncType);
		
		$entry = KalturaEntryFactory::getInstanceByType($dbEntry->getType());
		$entry->fromObject($dbEntry);
		
		return $entry;
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
			if (strtolower($dbEntry->getPuserId()) !== strtolower($this->getKs()->user))
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
		
		try
		{
			$wrapper = objectWrapperBase::getWrapperClass($dbEntry);
			$wrapper->removeFromCache("entry", $dbEntry->getId());
		}
		catch(Exception $e)
		{
			KalturaLog::err($e);
		}
		
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
		myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_ENTRY_REPORT, $oldModerationObj ,$dbEntry->getPartnerId());
				
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

			//When translating from seconds to msec need to subtract 500 msec since entries greater than 5500 msec are considered as entries with 6 sec
			if ($filter->durationGreaterThanOrEqual !== null)
				$filter->durationGreaterThanOrEqual = $filter->durationGreaterThanOrEqual * 1000 - 500;
				
			if ($filter->durationLessThan !== null)
				$filter->durationLessThan = $filter->durationLessThan * 1000;
				
			//When translating from seconds to msec need to add 499 msec since entries less than 5499 msec are considered as entries with 5 sec
			if ($filter->durationLessThanOrEqual !== null)
				$filter->durationLessThanOrEqual = $filter->durationLessThanOrEqual * 1000 + 499;
		}
	}
	
	// hack due to KCW of version  from KMC
	protected function getConversionQualityFromRequest () 
	{
		if(isset($_REQUEST["conversionquality"]))
			return $_REQUEST["conversionquality"];
		return null;
	}
}
