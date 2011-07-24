<?php

/**
 * Retrieve information and invoke actions on attachment Asset
 *
 * @service attachmentAsset
 * @package plugins.attachment
 * @subpackage api.services
 */
class AttachmentAssetService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		parent::applyPartnerFilterForClass(new conversionProfile2Peer());
		parent::applyPartnerFilterForClass(new assetPeer());
	}
	
    /**
     * Add attachment asset
     *
     * @action add
     * @param string $entryId
     * @param KalturaAttachmentAsset $attachmentAsset
     * @return KalturaAttachmentAsset
     * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY
	 * @throws KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN
	 * @throws KalturaErrors::RECORDED_WEBCAM_FILE_NOT_FOUND
	 * @throws KalturaAttachmentErrors::ATTACHMENT_ASSET_ID_NOT_FOUND
	 * @throws KalturaErrors::STORAGE_PROFILE_ID_NOT_FOUND
	 * @throws KalturaErrors::RESOURCE_TYPE_NOT_SUPPORTED
     */
    function addAction($entryId, KalturaAttachmentAsset $attachmentAsset)
    {
    	$dbEntry = entryPeer::retrieveByPK($entryId);
    	if(!$dbEntry || $dbEntry->getType() != KalturaEntryType::MEDIA_CLIP || !in_array($dbEntry->getMediaType(), array(KalturaMediaType::VIDEO, KalturaMediaType::AUDIO)))
    		throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
    	
		$this->checkIfUserAllowedToUpdateEntry($dbEntry);
		
    	$dbAttachmentAsset = new AttachmentAsset();
    	$dbAttachmentAsset = $attachmentAsset->toInsertableObject($dbAttachmentAsset);
    	
		$dbAttachmentAsset->setEntryId($entryId);
		$dbAttachmentAsset->setPartnerId($dbEntry->getPartnerId());
		$dbAttachmentAsset->setStatus(AttachmentAsset::FLAVOR_ASSET_STATUS_QUEUED);
		$dbAttachmentAsset->save();

		$attachmentAsset = new KalturaAttachmentAsset();
		$attachmentAsset->fromObject($dbAttachmentAsset);
		return $attachmentAsset;
    }
    
    /**
     * Update content of attachment asset
     *
     * @action setContent
     * @param string $id
     * @param KalturaContentResource $contentResource
     * @return KalturaAttachmentAsset
     * @throws KalturaAttachmentErrors::ATTACHMENT_ASSET_ID_NOT_FOUND
	 * @throws KalturaErrors::UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY
	 * @throws KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN
	 * @throws KalturaErrors::RECORDED_WEBCAM_FILE_NOT_FOUND
	 * @throws KalturaAttachmentErrors::ATTACHMENT_ASSET_ID_NOT_FOUND
	 * @throws KalturaErrors::STORAGE_PROFILE_ID_NOT_FOUND
	 * @throws KalturaErrors::RESOURCE_TYPE_NOT_SUPPORTED 
     */
    function setContentAction($id, KalturaContentResource $contentResource)
    {
   		$dbAttachmentAsset = assetPeer::retrieveById($id);
   		if(!$dbAttachmentAsset)
   			throw new KalturaAPIException(KalturaAttachmentErrors::ATTACHMENT_ASSET_ID_NOT_FOUND, $id);
    	
		$dbEntry = $dbAttachmentAsset->getentry();
    	if(!$dbEntry || $dbEntry->getType() != KalturaEntryType::MEDIA_CLIP || !in_array($dbEntry->getMediaType(), array(KalturaMediaType::VIDEO, KalturaMediaType::AUDIO)))
    		throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $dbAttachmentAsset->getEntryId());
		$this->checkIfUserAllowedToUpdateEntry($dbEntry);
		
   		$previousStatus = $dbAttachmentAsset->getStatus();
		$contentResource->validateEntry($dbAttachmentAsset->getentry());
		$kContentResource = $contentResource->toObject();
    	$this->attachContentResource($dbAttachmentAsset, $kContentResource);
		$contentResource->entryHandled($dbAttachmentAsset->getentry());
		
    	$newStatuses = array(
    		AttachmentAsset::FLAVOR_ASSET_STATUS_READY,
    		AttachmentAsset::FLAVOR_ASSET_STATUS_VALIDATING,
    		AttachmentAsset::FLAVOR_ASSET_STATUS_TEMP,
    	);
    	
    	if($previousStatus == AttachmentAsset::FLAVOR_ASSET_STATUS_QUEUED && in_array($dbAttachmentAsset->getStatus(), $newStatuses))
   			kEventsManager::raiseEvent(new kObjectAddedEvent($dbAttachmentAsset));
   		
		$attachmentAsset = new KalturaAttachmentAsset();
		$attachmentAsset->fromObject($dbAttachmentAsset);
		return $attachmentAsset;
    }
	
    /**
     * Update attachment asset
     *
     * @action update
     * @param string $id
     * @param KalturaAttachmentAsset $attachmentAsset
     * @return KalturaAttachmentAsset
     * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
     */
    function updateAction($id, KalturaAttachmentAsset $attachmentAsset)
    {
		$dbAttachmentAsset = assetPeer::retrieveById($id);
		if(!$dbAttachmentAsset)
			throw new KalturaAPIException(KalturaAttachmentErrors::ATTACHMENT_ASSET_ID_NOT_FOUND, $id);
    	
		$dbEntry = $dbAttachmentAsset->getentry();
    	if(!$dbEntry || $dbEntry->getType() != KalturaEntryType::MEDIA_CLIP || !in_array($dbEntry->getMediaType(), array(KalturaMediaType::VIDEO, KalturaMediaType::AUDIO)))
    		throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $dbAttachmentAsset->getEntryId());
		$this->checkIfUserAllowedToUpdateEntry($dbEntry);
		
    	$dbAttachmentAsset = $attachmentAsset->toUpdatableObject($dbAttachmentAsset);
    	$dbAttachmentAsset->save();
		
		$attachmentAsset = new KalturaAttachmentAsset();
		$attachmentAsset->fromObject($dbAttachmentAsset);
		return $attachmentAsset;
    }
    
	/**
	 * @param AttachmentAsset $attachmentAsset
	 * @param string $fullPath
	 * @param bool $copyOnly
	 */
	protected function attachFile(AttachmentAsset $attachmentAsset, $fullPath, $copyOnly = false)
	{
		$ext = pathinfo($fullPath, PATHINFO_EXTENSION);
		
		$attachmentAsset->incrementVersion();
		$attachmentAsset->setFileExt($ext);
		$attachmentAsset->setSize(filesize($fullPath));
		$attachmentAsset->save();
		
		$syncKey = $attachmentAsset->getSyncKey(AttachmentAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		
		try {
			kFileSyncUtils::moveFromFile($fullPath, $syncKey, true, $copyOnly);
		}
		catch (Exception $e) {
			
			if($attachmentAsset->getStatus() == AttachmentAsset::FLAVOR_ASSET_STATUS_QUEUED || $attachmentAsset->getStatus() == AttachmentAsset::FLAVOR_ASSET_STATUS_NOT_APPLICABLE)
			{
				$attachmentAsset->setDescription($e->getMessage());
				$attachmentAsset->setStatus(AttachmentAsset::FLAVOR_ASSET_STATUS_ERROR);
				$attachmentAsset->save();
			}												
			throw $e;
		}

		$finalPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
		list($width, $height, $type, $attr) = getimagesize($finalPath);
		
		$attachmentAsset->setWidth($width);
		$attachmentAsset->setHeight($height);
		$attachmentAsset->setSize(filesize($finalPath));
		
		$attachmentAsset->setStatus(AttachmentAsset::FLAVOR_ASSET_STATUS_READY);
		$attachmentAsset->save();
	}
    
	/**
	 * @param AttachmentAsset $attachmentAsset
	 * @param string $url
	 */
	protected function attachUrl(AttachmentAsset $attachmentAsset, $url)
	{
    	$fullPath = myContentStorage::getFSUploadsPath() . '/' . $attachmentAsset->getId() . '.jpg';
		if (kFile::downloadUrlToFile($url, $fullPath))
			return $this->attachFile($attachmentAsset, $fullPath);
			
		if($attachmentAsset->getStatus() == AttachmentAsset::FLAVOR_ASSET_STATUS_QUEUED || $attachmentAsset->getStatus() == AttachmentAsset::FLAVOR_ASSET_STATUS_NOT_APPLICABLE)
		{
			$attachmentAsset->setDescription("Failed downloading file[$url]");
			$attachmentAsset->setStatus(AttachmentAsset::FLAVOR_ASSET_STATUS_ERROR);
			$attachmentAsset->save();
		}
		
		throw new KalturaAPIException(KalturaAttachmentErrors::ATTACHMENT_ASSET_DOWNLOAD_FAILED, $url);
    }
    
	/**
	 * @param AttachmentAsset $attachmentAsset
	 * @param kUrlResource $contentResource
	 */
	protected function attachUrlResource(AttachmentAsset $attachmentAsset, kUrlResource $contentResource)
	{
    	$this->attachUrl($attachmentAsset, $contentResource->getUrl());
    }
    
	/**
	 * @param AttachmentAsset $attachmentAsset
	 * @param kLocalFileResource $contentResource
	 */
	protected function attachLocalFileResource(AttachmentAsset $attachmentAsset, kLocalFileResource $contentResource)
	{
		if($contentResource->getIsReady())
			return $this->attachFile($attachmentAsset, $contentResource->getLocalFilePath(), $contentResource->getKeepOriginalFile());
			
		$attachmentAsset->setStatus(asset::FLAVOR_ASSET_STATUS_IMPORTING);
		$attachmentAsset->save();
		
		$contentResource->attachCreatedObject($attachmentAsset);
    }
    
	/**
	 * @param AttachmentAsset $attachmentAsset
	 * @param FileSyncKey $srcSyncKey
	 */
	protected function attachFileSync(AttachmentAsset $attachmentAsset, FileSyncKey $srcSyncKey)
	{
		$attachmentAsset->incrementVersion();
		$attachmentAsset->save();
		
        $newSyncKey = $attachmentAsset->getSyncKey(AttachmentAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
        kFileSyncUtils::createSyncFileLinkForKey($newSyncKey, $srcSyncKey);
                
		$finalPath = kFileSyncUtils::getLocalFilePathForKey($newSyncKey);
		list($width, $height, $type, $attr) = getimagesize($finalPath);
		
		$attachmentAsset->setWidth($width);
		$attachmentAsset->setHeight($height);
		$attachmentAsset->setSize(filesize($finalPath));
		
		$attachmentAsset->setStatus(AttachmentAsset::FLAVOR_ASSET_STATUS_READY);
		$attachmentAsset->save();
    }
    
	/**
	 * @param AttachmentAsset $attachmentAsset
	 * @param kFileSyncResource $contentResource
	 */
	protected function attachFileSyncResource(AttachmentAsset $attachmentAsset, kFileSyncResource $contentResource)
	{
    	$syncable = kFileSyncObjectManager::retrieveObject($contentResource->getFileSyncObjectType(), $contentResource->getObjectId());
    	$srcSyncKey = $syncable->getSyncKey($contentResource->getObjectSubType(), $contentResource->getVersion());
    	
        return $this->attachFileSync($attachmentAsset, $srcSyncKey);
    }
    
	/**
	 * @param AttachmentAsset $attachmentAsset
	 * @param kRemoteStorageResource $contentResource
	 * @throws KalturaErrors::STORAGE_PROFILE_ID_NOT_FOUND
	 */
	protected function attachRemoteStorageResource(AttachmentAsset $attachmentAsset, kRemoteStorageResource $contentResource)
	{
        $storageProfile = StorageProfilePeer::retrieveByPK($contentResource->getStorageProfileId());
        if(!$storageProfile)
        {
        	if($attachmentAsset->getStatus() == AttachmentAsset::FLAVOR_ASSET_STATUS_QUEUED || $attachmentAsset->getStatus() == AttachmentAsset::FLAVOR_ASSET_STATUS_NOT_APPLICABLE)
        	{
				$attachmentAsset->setDescription("Could not find storage profile id [$contentResource->getStorageProfileId()]");
				$attachmentAsset->setStatus(AttachmentAsset::FLAVOR_ASSET_STATUS_ERROR);
				$attachmentAsset->save();
        	}
			
        	throw new KalturaAPIException(KalturaErrors::STORAGE_PROFILE_ID_NOT_FOUND, $contentResource->getStorageProfileId());
        }
        $attachmentAsset->incrementVersion();
		$attachmentAsset->setStatus(AttachmentAsset::FLAVOR_ASSET_STATUS_READY);
        $attachmentAsset->save();
        	
        $syncKey = $attachmentAsset->getSyncKey(AttachmentAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$fileSync = kFileSyncUtils::createReadyExternalSyncFileForKey($syncKey, $contentResource->getUrl(), $storageProfile);
    }
    
    
	/**
	 * @param AttachmentAsset $attachmentAsset
	 * @param kContentResource $contentResource
	 * @throws KalturaErrors::UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY
	 * @throws KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN
	 * @throws KalturaErrors::RECORDED_WEBCAM_FILE_NOT_FOUND
	 * @throws KalturaAttachmentErrors::ATTACHMENT_ASSET_ID_NOT_FOUND
	 * @throws KalturaErrors::STORAGE_PROFILE_ID_NOT_FOUND
	 * @throws KalturaErrors::RESOURCE_TYPE_NOT_SUPPORTED
	 */
	protected function attachContentResource(AttachmentAsset $attachmentAsset, kContentResource $contentResource)
	{
    	switch($contentResource->getType())
    	{
			case 'kUrlResource':
				return $this->attachUrlResource($attachmentAsset, $contentResource);
				
			case 'kLocalFileResource':
				return $this->attachLocalFileResource($attachmentAsset, $contentResource);
				
			case 'kFileSyncResource':
				return $this->attachFileSyncResource($attachmentAsset, $contentResource);
				
			case 'kRemoteStorageResource':
				return $this->attachRemoteStorageResource($attachmentAsset, $contentResource);
				
			default:
				$msg = "Resource of type [" . get_class($contentResource) . "] is not supported";
				KalturaLog::err($msg);
				
				if($attachmentAsset->getStatus() == AttachmentAsset::FLAVOR_ASSET_STATUS_QUEUED || $attachmentAsset->getStatus() == AttachmentAsset::FLAVOR_ASSET_STATUS_NOT_APPLICABLE)
				{
					$attachmentAsset->setDescription($msg);
					$attachmentAsset->setStatus(asset::FLAVOR_ASSET_STATUS_ERROR);
					$attachmentAsset->save();
				}
				
				throw new KalturaAPIException(KalturaErrors::RESOURCE_TYPE_NOT_SUPPORTED, get_class($contentResource));
    	}
    }
	
	/**
	 * Get download URL for the attachment Asset
	 * 
	 * @action getDownloadUrl
	 * @param string $id
	 * @param bool $useCdn
	 * @return string
	 */
	public function getDownloadUrlAction($id, $useCdn = false)
	{
		$attachmentAssetDb = assetPeer::retrieveById($id);
		if (!$attachmentAssetDb)
			throw new KalturaAPIException(KalturaAttachmentErrors::ATTACHMENT_ASSET_ID_NOT_FOUND, $id);

		if ($attachmentAssetDb->getStatus() != AttachmentAsset::FLAVOR_ASSET_STATUS_READY)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_IS_NOT_READY);

		return $attachmentAssetDb->getDownloadUrl($useCdn);
	}

	/**
	 * Serves attachment by its id
	 *  
	 * @action serve
	 * @param string $attachmentAssetId
	 * @return file
	 *  
	 * @throws KalturaAttachmentErrors::ATTACHMENT_ASSET_ID_NOT_FOUND
	 */
	public function serveAction($attachmentAssetId)
	{
		$attachmentAsset = assetPeer::retrieveById($attachmentAssetId);
		if (!$attachmentAsset)
			throw new KalturaAPIException(KalturaAttachmentErrors::ATTACHMENT_ASSET_ID_NOT_FOUND, $attachmentAssetId);

		$ext = $attachmentAsset->getFileExt();
		if(is_null($ext))
			$ext = 'jpg';
			
		$fileName = $attachmentAsset->getEntryId()."_" . $attachmentAsset->getId() . ".$ext";
		
		return $this->serveFile($attachmentAsset, AttachmentAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET, $fileName);
	}

	/**
	 * @action get
	 * @param string $attachmentAssetId
	 * @return KalturaAttachmentAsset
	 * 
	 * @throws KalturaAttachmentErrors::ATTACHMENT_ASSET_ID_NOT_FOUND
	 */
	public function getAction($attachmentAssetId)
	{
		$attachmentAssetsDb = assetPeer::retrieveById($attachmentAssetId);
		if(!$attachmentAssetsDb)
			throw new KalturaAPIException(KalturaAttachmentErrors::ATTACHMENT_ASSET_ID_NOT_FOUND, $attachmentAssetId);
		
		$attachmentAssets = new KalturaAttachmentAsset();
		$attachmentAssets->fromObject($attachmentAssetsDb);
		return $attachmentAssets;
	}
	
	/**
	 * List attachment Assets by filter and pager
	 * 
	 * @action list
	 * @param KalturaAssetFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaAttachmentAssetListResponse
	 */
	function listAction(KalturaAssetFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaAssetFilter();

		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$attachmentAssetFilter = new AssetFilter();
		
		$filter->toObject($attachmentAssetFilter);

		$c = new Criteria();
		$attachmentAssetFilter->attachToCriteria($c);
		
		$types = KalturaPluginManager::getExtendedTypes(assetPeer::OM_CLASS, AttachmentPlugin::getAssetTypeCoreValue(AttachmentAssetType::ATTACHMENT));
		$c->add(assetPeer::TYPE, $types, Criteria::IN);
		
		$totalCount = assetPeer::doCount($c);
		
		$pager->attachToCriteria($c);
		$dbList = assetPeer::doSelect($c);
		
		$list = KalturaAttachmentAssetArray::fromDbArray($dbList);
		$response = new KalturaAttachmentAssetListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;    
	}
	
	/**
	 * @action delete
	 * @param string $attachmentAssetId
	 * 
	 * @throws KalturaAttachmentErrors::ATTACHMENT_ASSET_ID_NOT_FOUND
	 */
	public function deleteAction($attachmentAssetId)
	{
		$attachmentAssetDb = assetPeer::retrieveById($attachmentAssetId);
		if(!$attachmentAssetDb)
			throw new KalturaAPIException(KalturaAttachmentErrors::ATTACHMENT_ASSET_ID_NOT_FOUND, $attachmentAssetId);
	
		$dbEntry = $attachmentAssetDb->getentry();
    	if(!$dbEntry || $dbEntry->getType() != KalturaEntryType::MEDIA_CLIP || !in_array($dbEntry->getMediaType(), array(KalturaMediaType::VIDEO, KalturaMediaType::AUDIO)))
    		throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $attachmentAssetDb->getEntryId());
		$this->checkIfUserAllowedToUpdateEntry($dbEntry);
		
		$attachmentAssetDb->setStatus(AttachmentAsset::FLAVOR_ASSET_STATUS_DELETED);
		$attachmentAssetDb->setDeletedAt(time());
		$attachmentAssetDb->save();
	}
}
