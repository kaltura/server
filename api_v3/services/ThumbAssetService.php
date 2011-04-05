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
    	$dbThumbAsset = $thumbAsset->toUpdatableObject($dbThumbAsset);
    	
		$dbThumbAsset->setEntryId($entryId);
		$dbThumbAsset->setPartnerId($dbEntry->getPartnerId());
		$dbThumbAsset->incrementVersion();
		$dbThumbAsset->save();
    	
    	$this->attachContentResource($dbThumbAsset, $contentResource);
				
		$dbThumbAsset->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_READY);
		$dbThumbAsset->save();
		
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
		$thumbAsset->setFileExt($ext);
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
	 * @param KalturaUploadedFileTokenResource $contentResource
	 * @throws KalturaErrors::UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY
	 * @throws KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN
	 */
	protected function attachUploadedFileTokenResource(thumbAsset $thumbAsset, KalturaUploadedFileTokenResource $contentResource)
	{
    	$contentResource->validatePropertyNotNull('token');
    	
		try
		{
		    $fullPath = kUploadTokenMgr::getFullPathByUploadTokenId($contentResource->token);
		}
		catch(kCoreException $ex)
		{
			$thumbAsset->setDescription($ex->getMessage());
			$thumbAsset->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_ERROR);
			$thumbAsset->save();
			
		    if ($ex->getCode() == kUploadTokenException::UPLOAD_TOKEN_INVALID_STATUS);
			    throw new KalturaAPIException(KalturaErrors::UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY);
			    
		    throw $ex;
		}
				
		if(!file_exists($fullPath))
		{
			$remoteDCHost = kUploadTokenMgr::getRemoteHostForUploadToken($contentResource->token, kDataCenterMgr::getCurrentDcId());
			if($remoteDCHost)
			{
				kFile::dumpApiRequest($remoteDCHost);
			}
			else
			{
				$thumbAsset->setDescription("Uploaded file token [$contentResource->token] dc not found");
				$thumbAsset->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_ERROR);
				$thumbAsset->save();
				
				throw new KalturaAPIException(KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN);
			}
		}
		
		$this->attachFile($thumbAsset, $fullPath);
		kUploadTokenMgr::closeUploadTokenById($contentResource->token);
    }
    
	/**
	 * @param thumbAsset $thumbAsset
	 * @param thumbAsset $srcThumbAsset
	 */
	protected function attachAsset(thumbAsset $thumbAsset, thumbAsset $srcThumbAsset)
	{
		$thumbAsset->setFlavorParamsId($srcThumbAsset->getFlavorParamsId());
		$thumbAsset->save();
		
		$sourceEntryId = $srcThumbAsset->getEntryId();
		
        $srcSyncKey = $srcThumbAsset->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
      	
        $this->attachFileSync($thumbAsset, $srcSyncKey);
    }
    
	/**
	 * @param thumbAsset $thumbAsset
	 * @param KalturaAssetResource $contentResource
	 * @throws KalturaErrors::THUMB_ASSET_ID_NOT_FOUND
	 */
	protected function attachAssetResource(thumbAsset $thumbAsset, KalturaAssetResource $contentResource)
	{
    	$contentResource->validatePropertyNotNull('assetId');
    	
		$srcThumbAsset = thumbAssetPeer::retrieveById($contentResource->assetId);
		if (!$srcThumbAsset)
		{
			$thumbAsset->setDescription("Source asset [$contentResource->assetId] not found");
			$thumbAsset->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_ERROR);
			$thumbAsset->save();
			
			throw new KalturaAPIException(KalturaErrors::THUMB_ASSET_ID_NOT_FOUND, $contentResource->assetId);
		}
		
		$this->attachAsset($thumbAsset, $srcThumbAsset);
    }
    
	/**
	 * @param thumbAsset $thumbAsset
	 * @param KalturaEntryResource $contentResource
	 * @throws KalturaErrors::THUMB_ASSET_ID_NOT_FOUND
	 */
	protected function attachEntryResource(thumbAsset $thumbAsset, KalturaEntryResource $contentResource)
	{
    	$contentResource->validatePropertyNotNull('entryId');
    	$contentResource->validatePropertyNotNull('flavorParamsId');
    
    	$this->attachEntry($thumbAsset, $contentResource->entryId, $contentResource->flavorParamsId);
    }
    
	/**
	 * @param thumbAsset $thumbAsset
	 * @param string $entryId
	 * @throws KalturaErrors::THUMB_ASSET_ID_NOT_FOUND
	 */
	protected function attachEntry(thumbAsset $thumbAsset, $entryId)
	{
    	$srcEntry = entryPeer::retrieveByPK($entryId);
    	if(!$srcEntry || $srcEntry->getType() != KalturaEntryType::MEDIA_CLIP || $srcEntry->getMediaType() != KalturaMediaType::IMAGE)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
    	
        $srcSyncKey = $srcEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
        $this->attachFileSync($thumbAsset, $srcSyncKey);
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
	 * @param KalturaUrlResource $contentResource
	 */
	protected function attachUrlResource(thumbAsset $thumbAsset, KalturaUrlResource $contentResource)
	{
    	$contentResource->validatePropertyNotNull('url');
    	$this->attachUrl($thumbAsset, $contentResource->url);
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
	 * @param KalturaLocalFileResource $contentResource
	 */
	protected function attachLocalFileResource(thumbAsset $thumbAsset, KalturaLocalFileResource $contentResource)
	{
    	$contentResource->validatePropertyNotNull('localFilePath');
		$this->attachFile($thumbAsset, $contentResource->localFilePath, true);
    }
    
	/**
	 * @param thumbAsset $thumbAsset
	 * @param KalturaUploadedFileResource $contentResource
	 */
	protected function attachUploadedFileResource(thumbAsset $thumbAsset, KalturaUploadedFileResource $contentResource)
	{
    	$contentResource->validatePropertyNotNull('fileData');
		$ext = pathinfo($contentResource->fileData['name'], PATHINFO_EXTENSION);
		
		$uploadPath = $contentResource->fileData['tmp_name'];
		$tempPath = myContentStorage::getFSUploadsPath() . '/' . uniqid(time()) . '.jpg';
		$moved = kFile::moveFile($uploadPath, $tempPath, true);
		if(!$moved)
		{
			$thumbAsset->setDescription("Could not move file from [$uploadPath] to [$tempPath]");
			$thumbAsset->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_ERROR);
			$thumbAsset->save();
			
			throw new KalturaAPIException(KalturaErrors::UPLOAD_ERROR);
		}
			 
		return $this->attachFile($thumbAsset, $tempPath);
    }
    
	/**
	 * @param thumbAsset $thumbAsset
	 * @param FileSyncKey $srcSyncKey
	 */
	protected function attachFileSync(thumbAsset $thumbAsset, FileSyncKey $srcSyncKey)
	{
        $newSyncKey = $thumbAsset->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
        kFileSyncUtils::createSyncFileLinkForKey($newSyncKey, $srcSyncKey, false);
    }
    
	/**
	 * @param thumbAsset $thumbAsset
	 * @param KalturaFileSyncResource $contentResource
	 */
	protected function attachFileSyncResource(thumbAsset $thumbAsset, KalturaFileSyncResource $contentResource)
	{
    	$contentResource->validatePropertyNotNull('fileSyncObjectType');
    	$contentResource->validatePropertyNotNull('objectSubType');
    	$contentResource->validatePropertyNotNull('objectId');
    	
    	$syncable = kFileSyncObjectManager::retrieveObject($contentResource->fileSyncObjectType, $contentResource->objectId);
    	$srcSyncKey = $syncable->getSyncKey($contentResource->objectSubType, $contentResource->version);
    	
        return $this->attachFileSync($thumbAsset, $srcSyncKey);
    }
    
	/**
	 * @param thumbAsset $thumbAsset
	 * @param KalturaRemoteStorageResource $contentResource
	 * @throws KalturaErrors::STORAGE_PROFILE_ID_NOT_FOUND
	 */
	protected function attachRemoteStorageResource(thumbAsset $thumbAsset, KalturaRemoteStorageResource $contentResource)
	{
    	$contentResource->validatePropertyNotNull('url');
    	$contentResource->validatePropertyNotNull('storageProfileId');
    
        $storageProfile = StorageProfilePeer::retrieveByPK($contentResource->storageProfileId);
        if(!$storageProfile)
        {
			$thumbAsset->setDescription("Could not find storage profile id [$contentResource->storageProfileId]");
			$thumbAsset->setStatus(thumbAsset::FLAVOR_ASSET_STATUS_ERROR);
			$thumbAsset->save();
			
        	throw new KalturaAPIException(KalturaErrors::STORAGE_PROFILE_ID_NOT_FOUND, $contentResource->storageProfileId);
        }
        	
        $syncKey = $thumbAsset->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$fileSync = kFileSyncUtils::createReadyExternalSyncFileForKey($syncKey, $contentResource->url, $storageProfile);
    }
    
	/**
	 * @param thumbAsset $thumbAsset
	 * @param KalturaSearchResultsResource $contentResource
	 * @throws KalturaErrors::STORAGE_PROFILE_ID_NOT_FOUND
	 */
	protected function attachSearchResultsResource(thumbAsset $thumbAsset, KalturaSearchResultsResource $contentResource)
    {
    	$contentResource->validatePropertyNotNull('result');
     	$contentResource->result->validatePropertyNotNull("searchSource");
     	
		if ($contentResource->result->searchSource == entry::ENTRY_MEDIA_SOURCE_KALTURA ||
			$contentResource->result->searchSource == entry::ENTRY_MEDIA_SOURCE_KALTURA_PARTNER ||
			$contentResource->result->searchSource == entry::ENTRY_MEDIA_SOURCE_KALTURA_PARTNER_KSHOW ||
			$contentResource->result->searchSource == entry::ENTRY_MEDIA_SOURCE_KALTURA_KSHOW ||
			$contentResource->result->searchSource == entry::ENTRY_MEDIA_SOURCE_KALTURA_USER_CLIPS)
		{
			$this->attachEntry($thumbAsset, $contentResource->result->id);
		}
		else
		{
			$this->attachUrl($thumbAsset, $contentResource->result->url);
		}
    }
    
    
	/**
	 * @param thumbAsset $thumbAsset
	 * @param KalturaContentResource $contentResource
	 * @throws KalturaErrors::UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY
	 * @throws KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN
	 * @throws KalturaErrors::RECORDED_WEBCAM_FILE_NOT_FOUND
	 * @throws KalturaErrors::THUMB_ASSET_ID_NOT_FOUND
	 * @throws KalturaErrors::STORAGE_PROFILE_ID_NOT_FOUND
	 */
	protected function attachContentResource(thumbAsset $thumbAsset, KalturaContentResource $contentResource)
	{
    	switch(get_class($contentResource))
    	{
			case 'KalturaUploadedFileTokenResource':
				return $this->attachUploadedFileTokenResource($thumbAsset, $contentResource);
				
			case 'KalturaAssetResource':
				return $this->attachAssetResource($thumbAsset, $contentResource);
				
			case 'KalturaEntryResource':
				return $this->attachEntryResource($thumbAsset, $contentResource);
				
			case 'KalturaUrlResource':
				return $this->attachUrlResource($thumbAsset, $contentResource);
				
			case 'KalturaSearchResultsResource':
				return $this->attachSearchResultsResource($thumbAsset, $contentResource);
				
			case 'KalturaLocalFileResource':
				return $this->attachLocalFileResource($thumbAsset, $contentResource);
				
			case 'KalturaUploadedFileResource':
				return $this->attachUploadedFileResource($thumbAsset, $contentResource);
				
			case 'KalturaFileSyncResource':
				return $this->attachFileSyncResource($thumbAsset, $contentResource);
				
			case 'KalturaRemoteStorageResource':
				return $this->attachRemoteStorageResource($thumbAsset, $contentResource);
				
			case 'KalturaDropFolderFileResource':
				// TODO after DropFolderFile object creation
				
			default:
				$msg = "Resource of type [" . get_class($contentResource) . "] is not supported";
				KalturaLog::err($msg);
				
				$thumbAsset->setDescription($msg);
				$thumbAsset->setStatus(asset::FLAVOR_ASSET_STATUS_ERROR);
				$thumbAsset->save();
				return null;
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
		if(!$entry)
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
