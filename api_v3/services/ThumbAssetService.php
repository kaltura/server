<?php

/**
 * Retrieve information and invoke actions on Thumb Asset
 *
 * @service thumbAsset
 * @package api
 * @subpackage services
 */
class ThumbAssetService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		parent::applyPartnerFilterForClass(new conversionProfile2Peer());
		parent::applyPartnerFilterForClass(thumbParamsOutputPeer::getInstance());
		parent::applyPartnerFilterForClass(thumbAssetPeer::getInstance());
		
		$partnerGroup = null;
		if(
			$actionName == 'get' ||
			$actionName == 'list' ||
			$actionName == 'getByEntryId' ||
			$actionName == 'getDownloadUrl' ||
			$actionName == 'getWebPlayableByEntryId' ||
			$actionName == 'getFlavorAssetsWithParams' ||
			$actionName == 'generateByEntryId' ||
			$actionName == 'regenerate'
			)
			$partnerGroup = $this->partnerGroup . ',0';
			
		parent::applyPartnerFilterForClass(thumbParamsPeer::getInstance(), $partnerGroup);
	}
	
    /**
     * Add thumbnail asset
     *
     * @action add
     * @param string $entryId
     * @param KalturaThumbAsset $thumbAsset
     * @param KalturaContentResource $contentResource
     * @return KalturaThumbAsset
     * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
     * @throws KalturaErrors::THUMB_ASSET_ALREADY_EXISTS
	 * @throws KalturaErrors::UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY
	 * @throws KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN
	 * @throws KalturaErrors::RECORDED_WEBCAM_FILE_NOT_FOUND
	 * @throws KalturaErrors::THUMB_ASSET_ID_NOT_FOUND
	 * @throws KalturaErrors::STORAGE_PROFILE_ID_NOT_FOUND
	 * @throws KalturaErrors::RESOURCE_TYPE_NOT_SUPPORTED
     */
    function addAction($entryId, KalturaThumbAsset $thumbAsset, KalturaContentResource $contentResource)
    {
    	$dbEntry = entryPeer::retrieveByPK($entryId);
    	if(!$dbEntry || $dbEntry->getType() != KalturaEntryType::MEDIA_CLIP || !in_array($dbEntry->getMediaType(), array(KalturaMediaType::VIDEO, KalturaMediaType::AUDIO)))
    		throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
    	
    	if($thumbAsset->thumbParamsId)
    	{
    		$dbThumbAsset = thumbAssetPeer::retrieveByEntryIdAndParams($entryId, $thumbAsset->thumbParamsId);
    		if($dbThumbAsset)
    			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_ALREADY_EXISTS, $dbThumbAsset->getId(), $thumbAsset->thumbParamsId);
    	}
    	
    	$dbThumbAsset = new thumbAsset();
    	$dbThumbAsset = $thumbAsset->toInsertableObject($dbThumbAsset);
    	
		$dbThumbAsset->setEntryId($entryId);
		$dbThumbAsset->setPartnerId($dbEntry->getPartnerId());
    	
		$contentResource->validateEntry($dbEntry);
		$kContentResource = $contentResource->toObject();
    	$this->attachContentResource($dbThumbAsset, $kContentResource);
				
    	$syncKey = $dbThumbAsset->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
    	$filePath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
    	if($filePath && file_exists($filePath) && filesize($filePath))
    	{
    		list($width, $height, $type, $attr) = getimagesize($filePath);
    		$dbThumbAsset->setWidth($width);
    		$dbThumbAsset->setHeight($height);
    		$dbThumbAsset->setSize(filesize($filePath));
    	}
		$dbThumbAsset->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_READY);
		$dbThumbAsset->save();
		
		if($dbThumbAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB))
			$this->setAsDefaultAction($dbThumbAsset->getId());
		
		$thumbAsset = new KalturaThumbAsset();
		$thumbAsset->fromObject($dbThumbAsset);
		return $thumbAsset;
    }
	
    /**
     * Update thumbnail asset
     *
     * @action update
     * @param string $id
     * @param KalturaThumbAsset $thumbAsset
     * @param KalturaContentResource $contentResource
     * @return KalturaThumbAsset
     * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
     * @throws KalturaErrors::THUMB_ASSET_ALREADY_EXISTS
	 * @throws KalturaErrors::UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY
	 * @throws KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN
	 * @throws KalturaErrors::RECORDED_WEBCAM_FILE_NOT_FOUND
	 * @throws KalturaErrors::THUMB_ASSET_ID_NOT_FOUND
	 * @throws KalturaErrors::STORAGE_PROFILE_ID_NOT_FOUND
	 * @throws KalturaErrors::RESOURCE_TYPE_NOT_SUPPORTED
     */
    function updateAction($id, KalturaThumbAsset $thumbAsset, KalturaContentResource $contentResource = null)
    {
		$dbThumbAsset = thumbAssetPeer::retrieveById($id);
		if(!$dbThumbAsset)
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_ID_NOT_FOUND, $id);
    	
    	$dbThumbAsset = $thumbAsset->toUpdatableObject($dbThumbAsset);
    	
    	if($contentResource)
    	{
			$contentResource->validateEntry($dbThumbAsset->getentry());
			$kContentResource = $contentResource->toObject();
	    	$this->attachContentResource($dbThumbAsset, $kContentResource);
    	}
    	else 
    	{
    		$dbThumbAsset->save();
    	}
		
		if($dbThumbAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB))
			$this->setAsDefaultAction($dbThumbAsset->getId());
			
		$thumbAsset = new KalturaThumbAsset();
		$thumbAsset->fromObject($dbThumbAsset);
		return $thumbAsset;
    }
    
	/**
	 * @param thumbAsset $thumbAsset
	 * @param string $fullPath
	 * @param bool $copyOnly
	 */
	protected function attachFile(thumbAsset $thumbAsset, $fullPath, $copyOnly = false)
	{
		$ext = pathinfo($fullPath, PATHINFO_EXTENSION);
		
		$thumbAsset->incrementVersion();
		$thumbAsset->setFileExt($ext);
		$thumbAsset->setSize(filesize($fullPath));
		$thumbAsset->save();
		
		$syncKey = $thumbAsset->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		
		try {
			kFileSyncUtils::moveFromFile($fullPath, $syncKey, true, $copyOnly);
		}
		catch (Exception $e) {
			$thumbAsset->setDescription($e->getMessage());
			$thumbAsset->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_ERROR);
			$thumbAsset->save();												
			throw $e;
		}
    }
    
	/**
	 * @param thumbAsset $thumbAsset
	 * @param string $url
	 */
	protected function attachUrl(thumbAsset $thumbAsset, $url)
	{
    	$fullPath = myContentStorage::getFSUploadsPath() . '/' . $thumbAsset->getId() . '.jpg';
		if (kFile::downloadUrlToFile($url, $fullPath))
			return $this->attachFile($thumbAsset, $fullPath);
			
		$thumbAsset->setDescription("Failed downloading file[$url]");
		$thumbAsset->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_ERROR);
		$thumbAsset->save();
		
		throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_DOWNLOAD_FAILED, $url);
    }
    
	/**
	 * @param thumbAsset $thumbAsset
	 * @param kUrlResource $contentResource
	 */
	protected function attachUrlResource(thumbAsset $thumbAsset, kUrlResource $contentResource)
	{
    	$this->attachUrl($thumbAsset, $contentResource->getUrl());
    }
    
	/**
	 * @param thumbAsset $thumbAsset
	 * @param KalturaSearchResultsResource $contentResource
	 */
	protected function KalturaSearchResultsResource(thumbAsset $thumbAsset, KalturaSearchResultsResource $contentResource)
	{
    	$contentResource->validatePropertyNotNull('result');
     	$contentResource->result->validatePropertyNotNull("searchSource");
     	
		if ($contentResource->result->searchSource == entry::ENTRY_MEDIA_SOURCE_KALTURA ||
			$contentResource->result->searchSource == entry::ENTRY_MEDIA_SOURCE_KALTURA_PARTNER ||
			$contentResource->result->searchSource == entry::ENTRY_MEDIA_SOURCE_KALTURA_PARTNER_KSHOW ||
			$contentResource->result->searchSource == entry::ENTRY_MEDIA_SOURCE_KALTURA_KSHOW ||
			$contentResource->result->searchSource == entry::ENTRY_MEDIA_SOURCE_KALTURA_USER_CLIPS)
		{
			$srcThumbAsset = assetPeer::retrieveOriginalByEntryId($contentResource->result->id); 
			$this->attachAsset($thumbAsset, $srcThumbAsset);
		}
		else
		{
			$this->attachUrl($thumbAsset, $contentResource->result->url);
		}
    }
    
	/**
	 * @param thumbAsset $thumbAsset
	 * @param kLocalFileResource $contentResource
	 */
	protected function attachLocalFileResource(thumbAsset $thumbAsset, kLocalFileResource $contentResource)
	{
		$this->attachFile($thumbAsset, $contentResource->getLocalFilePath(), $contentResource->getKeepOriginalFile());
    }
    
	/**
	 * @param thumbAsset $thumbAsset
	 * @param FileSyncKey $srcSyncKey
	 */
	protected function attachFileSync(thumbAsset $thumbAsset, FileSyncKey $srcSyncKey)
	{
		$thumbAsset->incrementVersion();
		$thumbAsset->save();
		
        $newSyncKey = $thumbAsset->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
        kFileSyncUtils::createSyncFileLinkForKey($newSyncKey, $srcSyncKey, false);
                
        $fileSync = kFileSyncUtils::getLocalFileSyncForKey($newSyncKey, false);
        $fileSync = kFileSyncUtils::resolve($fileSync);
        
		$thumbAsset->setSize($fileSync->getFileSize());
		$thumbAsset->save();
    }
    
	/**
	 * @param thumbAsset $thumbAsset
	 * @param kFileSyncResource $contentResource
	 */
	protected function attachFileSyncResource(thumbAsset $thumbAsset, kFileSyncResource $contentResource)
	{
    	$syncable = kFileSyncObjectManager::retrieveObject($contentResource->getFileSyncObjectType(), $contentResource->getObjectId());
    	$srcSyncKey = $syncable->getSyncKey($contentResource->getObjectSubType(), $contentResource->getVersion());
    	
        return $this->attachFileSync($thumbAsset, $srcSyncKey);
    }
    
	/**
	 * @param thumbAsset $thumbAsset
	 * @param kRemoteStorageResource $contentResource
	 * @throws KalturaErrors::STORAGE_PROFILE_ID_NOT_FOUND
	 */
	protected function attachRemoteStorageResource(thumbAsset $thumbAsset, kRemoteStorageResource $contentResource)
	{
        $storageProfile = StorageProfilePeer::retrieveByPK($contentResource->getStorageProfileId());
        if(!$storageProfile)
        {
			$thumbAsset->setDescription("Could not find storage profile id [$contentResource->getStorageProfileId()]");
			$thumbAsset->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_ERROR);
			$thumbAsset->save();
			
        	throw new KalturaAPIException(KalturaErrors::STORAGE_PROFILE_ID_NOT_FOUND, $contentResource->getStorageProfileId());
        }
        $thumbAsset->incrementVersion();
        $thumbAsset->save();
        	
        $syncKey = $thumbAsset->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$fileSync = kFileSyncUtils::createReadyExternalSyncFileForKey($syncKey, $contentResource->getUrl(), $storageProfile);
    }
    
    
	/**
	 * @param thumbAsset $thumbAsset
	 * @param kContentResource $contentResource
	 * @throws KalturaErrors::UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY
	 * @throws KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN
	 * @throws KalturaErrors::RECORDED_WEBCAM_FILE_NOT_FOUND
	 * @throws KalturaErrors::THUMB_ASSET_ID_NOT_FOUND
	 * @throws KalturaErrors::STORAGE_PROFILE_ID_NOT_FOUND
	 * @throws KalturaErrors::RESOURCE_TYPE_NOT_SUPPORTED
	 */
	protected function attachContentResource(thumbAsset $thumbAsset, kContentResource $contentResource)
	{
    	switch(get_class($contentResource))
    	{
			case 'kUrlResource':
				return $this->attachUrlResource($thumbAsset, $contentResource);
				
			case 'kLocalFileResource':
				return $this->attachLocalFileResource($thumbAsset, $contentResource);
				
			case 'kFileSyncResource':
				return $this->attachFileSyncResource($thumbAsset, $contentResource);
				
			case 'kRemoteStorageResource':
				return $this->attachRemoteStorageResource($thumbAsset, $contentResource);
				
			default:
				$msg = "Resource of type [" . get_class($contentResource) . "] is not supported";
				KalturaLog::err($msg);
				
				$thumbAsset->setDescription($msg);
				$thumbAsset->setStatus(asset::FLAVOR_ASSET_STATUS_ERROR);
				$thumbAsset->save();
				
				throw new KalturaAPIException(KalturaErrors::RESOURCE_TYPE_NOT_SUPPORTED, get_class($contentResource));
    	}
    }
    
    
	/**
	 * Serves thumbnail by entry id and thumnail params id
	 *  
	 * @action serveByEntryId
	 * @param string $entryId
	 * @param int $thumbParamId if not set, default thumbnail will be used.
	 * @return file
	 * 
	 * @throws KalturaErrors::THUMB_ASSET_IS_NOT_READY
	 * @throws KalturaErrors::THUMB_ASSET_PARAMS_ID_NOT_FOUND
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	public function serveByEntryIdAction($entryId, $thumbParamId = null)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		if (!$entry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		$fileName = $entry->getId() . '.jpg';
		
		if(is_null($thumbParamId))
			return $this->serveFile($entry, entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB, $fileName);
		
		$thumbAsset = thumbAssetPeer::retrieveByEntryIdAndParams($entryId, $thumbParamId);
		if(!$thumbAsset)
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_PARAMS_ID_NOT_FOUND, $thumbParamId);
		
		return $this->serveFile($thumbAsset, thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET, $fileName);
	}

	/**
	 * Serves thumbnail by its id
	 *  
	 * @action serve
	 * @param string $thumbAssetId
	 * @return file
	 *  
	 * @throws KalturaErrors::THUMB_ASSET_IS_NOT_READY
	 * @throws KalturaErrors::THUMB_ASSET_ID_NOT_FOUND
	 */
	public function serveAction($thumbAssetId)
	{
		$thumbAsset = thumbAssetPeer::retrieveById($thumbAssetId);
		if (!$thumbAsset)
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_ID_NOT_FOUND, $thumbAssetId);

		$ext = $thumbAsset->getFileExt();
		if(is_null($ext))
			$ext = 'jpg';
			
		$fileName = $thumbAsset->getEntryId()."_" . $thumbAsset->getId() . ".$ext";
		
		return $this->serveFile($thumbAsset, thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET, $fileName);
	}
	
	/**
	 * Tags the thumbnail as DEFAULT_THUMB and removes that tag from all other thumbnail assets of the entry.
	 * Create a new file sync link on the entry thumbnail that points to the thumbnail asset file sync.
	 *  
	 * @action setAsDefault
	 * @param string $thumbAssetId
	 * @throws KalturaErrors::THUMB_ASSET_ID_NOT_FOUND
	 */
	public function setAsDefaultAction($thumbAssetId)
	{
		$thumbAsset = thumbAssetPeer::retrieveById($thumbAssetId);
		if (!$thumbAsset)
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_ID_NOT_FOUND, $thumbAssetId);
		
		$entry = $thumbAsset->getentry();
		$entryKuserId = $entry->getKuserId();
		$thisKuserId = $this->getKuser()->getId();
		$isNotAdmin = !$this->getKuser()->getIsAdmin();
		
		if(!$entry || ($isNotAdmin && !is_null($entryKuserId) && $entryKuserId != $thisKuserId)) // Check with Eran if $entryKuserId = null is valid (when inserting with default admin user) 
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $thumbAsset->getEntryId());
			
		$entryThumbAssets = thumbAssetPeer::retrieveByEntryId($thumbAsset->getEntryId());
		foreach($entryThumbAssets as $entryThumbAsset)
		{
			if($entryThumbAsset->getId() == $thumbAsset->getId())
				continue;
				
			if(!$entryThumbAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB))
				continue;
				
			$entryThumbAsset->removeTags(array(thumbParams::TAG_DEFAULT_THUMB));
			$entryThumbAsset->save();
		}
		
		if(!$thumbAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB))
		{
			$thumbAsset->addTags(array(thumbParams::TAG_DEFAULT_THUMB));
			$thumbAsset->save();
		}
		
		$entry->setThumbnail(".jpg");
		$entry->save();
		
		$thumbSyncKey = $thumbAsset->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$entrySyncKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB);
		kFileSyncUtils::createSyncFileLinkForKey($entrySyncKey, $thumbSyncKey, false);
	}

	/**
	 * @action generateByEntryId
	 * @param string $entryId
	 * @param int $destThumbParamsId indicate the id of the ThumbParams to be generate this thumbnail by
	 * @return KalturaThumbAsset
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::ENTRY_TYPE_NOT_SUPPORTED
	 * @throws KalturaErrors::ENTRY_MEDIA_TYPE_NOT_SUPPORTED
	 * @throws KalturaErrors::THUMB_ASSET_PARAMS_ID_NOT_FOUND
	 * @throws KalturaErrors::INVALID_ENTRY_STATUS
	 * @throws KalturaErrors::FLAVOR_ASSET_IS_NOT_READY
	 */
	public function generateByEntryIdAction($entryId, $destThumbParamsId)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		if ($entry->getType() != entryType::MEDIA_CLIP)
			throw new KalturaAPIException(KalturaErrors::ENTRY_TYPE_NOT_SUPPORTED, $entry->getType());
		if ($entry->getMediaType() != entry::ENTRY_MEDIA_TYPE_VIDEO)
			throw new KalturaAPIException(KalturaErrors::ENTRY_MEDIA_TYPE_NOT_SUPPORTED, $entry->getMediaType());
			
		$validStatuses = array(
			entryStatus::ERROR_CONVERTING,
			entryStatus::PRECONVERT,
			entryStatus::READY,
		);
		
		if (!in_array($entry->getStatus(), $validStatuses))
			throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_STATUS);
			
		$destThumbParams = thumbParamsPeer::retrieveByPK($destThumbParamsId);
		if(!$destThumbParams)
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_PARAMS_ID_NOT_FOUND, $destThumbParamsId);

		$dbThumbAsset = kBusinessPreConvertDL::decideThumbGenerate($entry, $destThumbParams);
		if(!$dbThumbAsset)
			return null;
			
		$thumbAsset = new KalturaThumbAsset();
		$thumbAsset->fromObject($dbThumbAsset);
		return $thumbAsset;
	}

	/**
	 * @action generate
	 * @param string $entryId
	 * @param KalturaThumbParams $thumbParams
	 * @param string $sourceAssetId id of the source asset (flavor or thumbnail) to be used as source for the thumbnail generation
	 * @return KalturaThumbAsset
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::ENTRY_TYPE_NOT_SUPPORTED
	 * @throws KalturaErrors::ENTRY_MEDIA_TYPE_NOT_SUPPORTED
	 * @throws KalturaErrors::THUMB_ASSET_PARAMS_ID_NOT_FOUND
	 * @throws KalturaErrors::INVALID_ENTRY_STATUS
	 * @throws KalturaErrors::FLAVOR_ASSET_IS_NOT_READY
	 */
	public function generateAction($entryId, KalturaThumbParams $thumbParams, $sourceAssetId = null)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		if ($entry->getType() != entryType::MEDIA_CLIP)
			throw new KalturaAPIException(KalturaErrors::ENTRY_TYPE_NOT_SUPPORTED, $entry->getType());
			
		if ($entry->getMediaType() != entry::ENTRY_MEDIA_TYPE_VIDEO)
			throw new KalturaAPIException(KalturaErrors::ENTRY_MEDIA_TYPE_NOT_SUPPORTED, $entry->getMediaType());
			
		$validStatuses = array(
			entryStatus::ERROR_CONVERTING,
			entryStatus::PRECONVERT,
			entryStatus::READY,
		);
		
		if (!in_array($entry->getStatus(), $validStatuses))
			throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_STATUS);
			
		$destThumbParams = new thumbParams();
		$thumbParams->toUpdatableObject($destThumbParams);

		$dbThumbAsset = kBusinessPreConvertDL::decideThumbGenerate($entry, $destThumbParams, null, $sourceAssetId, true);
		if(!$dbThumbAsset)
			return null;
			
		$thumbAsset = new KalturaThumbAsset();
		$thumbAsset->fromObject($dbThumbAsset);
		return $thumbAsset;
	}

	/**
	 * @action regenerate
	 * @param string $thumbAssetId
	 * @return KalturaThumbAsset
	 * 
	 * @throws KalturaErrors::THUMB_ASSET_ID_NOT_FOUND
	 * @throws KalturaErrors::ENTRY_TYPE_NOT_SUPPORTED
	 * @throws KalturaErrors::ENTRY_MEDIA_TYPE_NOT_SUPPORTED
	 * @throws KalturaErrors::THUMB_ASSET_PARAMS_ID_NOT_FOUND
	 * @throws KalturaErrors::INVALID_ENTRY_STATUS
	 */
	public function regenerateAction($thumbAssetId)
	{
		$thumbAsset = thumbAssetPeer::retrieveById($thumbAssetId);
		if(!$thumbAsset)
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_ID_NOT_FOUND, $thumbAssetId);
			
		if(is_null($thumbAsset->getFlavorParamsId()))
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_PARAMS_ID_NOT_FOUND, null);
			
		$destThumbParams = thumbParamsPeer::retrieveByPK($thumbAsset->getFlavorParamsId());
		if(!$destThumbParams)
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_PARAMS_ID_NOT_FOUND, $thumbAsset->getFlavorParamsId());
			
		$entry = $thumbAsset->getentry();
		if ($entry->getType() != entryType::MEDIA_CLIP)
			throw new KalturaAPIException(KalturaErrors::ENTRY_TYPE_NOT_SUPPORTED, $entry->getType());
		if ($entry->getMediaType() != entry::ENTRY_MEDIA_TYPE_VIDEO)
			throw new KalturaAPIException(KalturaErrors::ENTRY_MEDIA_TYPE_NOT_SUPPORTED, $entry->getMediaType());
			
		$validStatuses = array(
			entryStatus::ERROR_CONVERTING,
			entryStatus::PRECONVERT,
			entryStatus::READY,
		);
		
		if (!in_array($entry->getStatus(), $validStatuses))
			throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_STATUS);

		$dbThumbAsset = kBusinessPreConvertDL::decideThumbGenerate($entry, $destThumbParams);
		if(!$dbThumbAsset)
			return null;
			
		$thumbAsset = new KalturaThumbAsset();
		$thumbAsset->fromObject($dbThumbAsset);
		return $thumbAsset;
	}
	
	/**
	 * @action get
	 * @param string $thumbAssetId
	 * @return KalturaThumbAsset
	 * 
	 * @throws KalturaErrors::THUMB_ASSET_ID_NOT_FOUND
	 */
	public function getAction($thumbAssetId)
	{
		$thumbAssetsDb = thumbAssetPeer::retrieveById($thumbAssetId);
		if(!$thumbAssetsDb)
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_ID_NOT_FOUND, $thumbAssetId);
		
		$thumbAssets = new KalturaThumbAsset();
		$thumbAssets->fromObject($thumbAssetsDb);
		return $thumbAssets;
	}
	
	/**
	 * @action getByEntryId
	 * @param string $entryId
	 * @return KalturaThumbAssetArray
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	public function getByEntryIdAction($entryId)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		// get the thumb assets for this entry
		$c = new Criteria();
		$c->add(thumbAssetPeer::ENTRY_ID, $entryId);
		$thumbAssetsDb = thumbAssetPeer::doSelect($c);
		$thumbAssets = KalturaThumbAssetArray::fromDbArray($thumbAssetsDb);
		return $thumbAssets;
	}
	
	/**
	 * List Thumbnail Assets by filter and pager
	 * 
	 * @action list
	 * @param KalturaAssetFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaThumbAssetListResponse
	 */
	function listAction(KalturaAssetFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaAssetFilter();

		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$thumbAssetFilter = new AssetFilter();
		
		$filter->toObject($thumbAssetFilter);

		$c = new Criteria();
		$thumbAssetFilter->attachToCriteria($c);
		
		$totalCount = thumbAssetPeer::doCount($c);
		
		$pager->attachToCriteria($c);
		$dbList = thumbAssetPeer::doSelect($c);
		
		$list = KalturaThumbAssetArray::fromDbArray($dbList);
		$response = new KalturaThumbAssetListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;    
	}
	
	/**
	 * @action addFromUrl
	 * @param string $entryId
	 * @param string $url
	 * @return KalturaThumbAsset
	 * 
	 * @deprecated use thumbAsset.add instead
	 */
	public function addFromUrlAction($entryId, $url)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		$ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
		
		$dbThumbAsset = new thumbAsset();
		$dbThumbAsset->setPartnerId($dbEntry->getPartnerId());
		$dbThumbAsset->setEntryId($dbEntry->getId());
		$dbThumbAsset->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_QUEUED);
		$dbThumbAsset->setFileExt($ext);
		$dbThumbAsset->incrementVersion();
		$dbThumbAsset->save();
		
		$syncKey = $dbThumbAsset->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		kFileSyncUtils::file_put_contents($syncKey, file_get_contents($url));
		
		$finalPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
		list($width, $height, $type, $attr) = getimagesize($finalPath);
		
		$dbThumbAsset->setWidth($width);
		$dbThumbAsset->setHeight($height);
		$dbThumbAsset->setSize(filesize($finalPath));
		$dbThumbAsset->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_READY);
		$dbThumbAsset->save();
		
		$thumbAssets = new KalturaThumbAsset();
		$thumbAssets->fromObject($dbThumbAsset);
		return $thumbAssets;
	}
	
	/**
	 * @action addFromImage
	 * @param string $entryId
	 * @param file $fileData
	 * @return KalturaThumbAsset
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	public function addFromImageAction($entryId, $fileData)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		$ext = pathinfo($fileData["name"], PATHINFO_EXTENSION);
		
		$dbThumbAsset = new thumbAsset();
		$dbThumbAsset->setPartnerId($dbEntry->getPartnerId());
		$dbThumbAsset->setEntryId($dbEntry->getId());
		$dbThumbAsset->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_QUEUED);
		$dbThumbAsset->setFileExt($ext);
		$dbThumbAsset->incrementVersion();
		$dbThumbAsset->save();
		
		$syncKey = $dbThumbAsset->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		kFileSyncUtils::moveFromFile($fileData["tmp_name"], $syncKey);
		
		$finalPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
		list($width, $height, $type, $attr) = getimagesize($finalPath);
		
		$dbThumbAsset->setWidth($width);
		$dbThumbAsset->setHeight($height);
		$dbThumbAsset->setSize(filesize($finalPath));
		$dbThumbAsset->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_READY);
		$dbThumbAsset->save();
		
		$thumbAssets = new KalturaThumbAsset();
		$thumbAssets->fromObject($dbThumbAsset);
		return $thumbAssets;
	}
	
	/**
	 * @action delete
	 * @param string $thumbAssetId
	 * 
	 * @throws KalturaErrors::THUMB_ASSET_ID_NOT_FOUND
	 */
	public function deleteAction($thumbAssetId)
	{
		$thumbAssetDb = thumbAssetPeer::retrieveById($thumbAssetId);
		if (!$thumbAssetDb)
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_ID_NOT_FOUND, $thumbAssetId);
			
		$thumbAssetDb->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_DELETED);
		$thumbAssetDb->setDeletedAt(time());
		$thumbAssetDb->save();
	}
}
