<?php

/**
 * Manage file assets
 *
 * @service fileAsset
 */
class FileAssetService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		$this->applyPartnerFilterForClass('FileAsset');
		$this->applyPartnerFilterForClass('uiConf');
	}
	
	/**
	 * Add new file asset
	 * 
	 * @action add
	 * @param KalturaFileAsset $fileAsset
	 * @return KalturaFileAsset
	 */
	function addAction(KalturaFileAsset $fileAsset)
	{
		$dbFileAsset = $fileAsset->toInsertableObject();
		$dbFileAsset->setPartnerId($this->getPartnerId());
		$dbFileAsset->setStatus(KalturaFileAssetStatus::PENDING);
		$dbFileAsset->save();
		
		$fileAsset = new KalturaFileAsset();
		$fileAsset->fromObject($dbFileAsset, $this->getResponseProfile());
		return $fileAsset;
	}
	
	/**
	 * Get file asset by id
	 * 
	 * @action get
	 * @param bigint $id
	 * @return KalturaFileAsset
	 * @ksIgnored
	 * 
	 * @throws KalturaErrors::FILE_ASSET_ID_NOT_FOUND
	 */
	function getAction($id)
	{
		$dbFileAsset = FileAssetPeer::retrieveByPK($id);
		if (!$dbFileAsset)
			throw new KalturaAPIException(KalturaErrors::FILE_ASSET_ID_NOT_FOUND, $id);
			
		$fileAsset = new KalturaFileAsset();
		$fileAsset->fromObject($dbFileAsset, $this->getResponseProfile());
		return $fileAsset;
	}
	
	/**
	 * Update file asset by id
	 * 
	 * @action update
	 * @param bigint $id
	 * @param KalturaFileAsset $fileAsset
	 * @return KalturaFileAsset
	 * 
	 * @throws KalturaErrors::FILE_ASSET_ID_NOT_FOUND
	 */
	function updateAction($id, KalturaFileAsset $fileAsset)
	{
		$dbFileAsset = FileAssetPeer::retrieveByPK($id);
		if (!$dbFileAsset)
			throw new KalturaAPIException(KalturaErrors::FILE_ASSET_ID_NOT_FOUND, $id);
		
		$fileAsset->toUpdatableObject($dbFileAsset);
		$dbFileAsset->save();
		
		$fileAsset = new KalturaFileAsset();
		$fileAsset->fromObject($dbFileAsset, $this->getResponseProfile());
		return $fileAsset;
	}
	
	/**
	 * Delete file asset by id
	 * 
	 * @action delete
	 * @param bigint $id
	 * 
	 * @throws KalturaErrors::FILE_ASSET_ID_NOT_FOUND
	 */
	function deleteAction($id)
	{
		$dbFileAsset = FileAssetPeer::retrieveByPK($id);
		if (!$dbFileAsset)
			throw new KalturaAPIException(KalturaErrors::FILE_ASSET_ID_NOT_FOUND, $id);

		$dbFileAsset->setStatus(KalturaFileAssetStatus::DELETED);
		$dbFileAsset->save();
	}

	/**
	 * Serve file asset by id
	 *  
	 * @action serve
	 * @param bigint $id
	 * @return file
	 * @ksIgnored
	 *  
	 * @throws KalturaErrors::FILE_ASSET_ID_NOT_FOUND
	 * @throws KalturaErrors::FILE_DOESNT_EXIST
	 */
	public function serveAction($id)
	{
		$dbFileAsset = FileAssetPeer::retrieveByPK($id);
		if (!$dbFileAsset)
			throw new KalturaAPIException(KalturaErrors::FILE_ASSET_ID_NOT_FOUND, $id);
		
		return $this->serveFile($dbFileAsset, FileAsset::FILE_SYNC_ASSET, $dbFileAsset->getName());
	}
	
    /**
     * Set content of file asset
     *
     * @action setContent
     * @param bigint $id
     * @param KalturaContentResource $contentResource
     * @return KalturaFileAsset
	 * @throws KalturaErrors::FILE_ASSET_ID_NOT_FOUND
	 * @throws KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN
	 * @throws KalturaErrors::RECORDED_WEBCAM_FILE_NOT_FOUND
	 * @throws KalturaErrors::RESOURCE_TYPE_NOT_SUPPORTED 
     */
    function setContentAction($id, KalturaContentResource $contentResource)
    {
		$dbFileAsset = FileAssetPeer::retrieveByPK($id);
		if (!$dbFileAsset)
			throw new KalturaAPIException(KalturaErrors::FILE_ASSET_ID_NOT_FOUND, $id);
		
		$kContentResource = $contentResource->toObject();
    	$this->attachContentResource($dbFileAsset, $kContentResource);
		
		$fileAsset = new KalturaFileAsset();
		$fileAsset->fromObject($dbFileAsset, $this->getResponseProfile());
		return $fileAsset;
    }
    
	/**
	 * @param FileAsset $dbFileAsset
	 * @param kContentResource $contentResource
	 * @throws KalturaErrors::UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY
	 * @throws KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN
	 * @throws KalturaErrors::RECORDED_WEBCAM_FILE_NOT_FOUND
	 * @throws KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND
	 * @throws KalturaErrors::STORAGE_PROFILE_ID_NOT_FOUND
	 * @throws KalturaErrors::RESOURCE_TYPE_NOT_SUPPORTED
	 */
	protected function attachContentResource(FileAsset $dbFileAsset, kContentResource $contentResource)
	{
    	switch($contentResource->getType())
    	{
			case 'kLocalFileResource':
				return $this->attachLocalFileResource($dbFileAsset, $contentResource);
				
			case 'kFileSyncResource':
				return $this->attachFileSyncResource($dbFileAsset, $contentResource);
				
			default:
				$msg = "Resource of type [" . get_class($contentResource) . "] is not supported";
				KalturaLog::err($msg);
				
				throw new KalturaAPIException(KalturaErrors::RESOURCE_TYPE_NOT_SUPPORTED, get_class($contentResource));
    	}
    }
    
	/**
	 * @param FileAsset $dbFileAsset
	 * @param kLocalFileResource $contentResource
	 */
	protected function attachLocalFileResource(FileAsset $dbFileAsset, kLocalFileResource $contentResource)
	{
		if($contentResource->getIsReady())
			return $this->attachFile($dbFileAsset, $contentResource->getLocalFilePath(), $contentResource->getKeepOriginalFile());
			
		$dbFileAsset->setStatus(FileAssetStatus::UPLOADING);
		$dbFileAsset->save();
		
		$contentResource->attachCreatedObject($dbFileAsset);
    }
    
	/**
	 * @param FileAsset $dbFileAsset
	 * @param kFileSyncResource $contentResource
	 */
	protected function attachFileSyncResource(FileAsset $dbFileAsset, kFileSyncResource $contentResource)
	{
    	$syncable = kFileSyncObjectManager::retrieveObject($contentResource->getFileSyncObjectType(), $contentResource->getObjectId());
    	$srcSyncKey = $syncable->getSyncKey($contentResource->getObjectSubType(), $contentResource->getVersion());
    	
        return $this->attachFileSync($dbFileAsset, $srcSyncKey);
    }
    
	/**
	 * @param FileAsset $dbFileAsset
	 * @param string $fullPath
	 * @param bool $copyOnly
	 */
	protected function attachFile(FileAsset $dbFileAsset, $fullPath, $copyOnly = false)
	{
		if(!$dbFileAsset->getFileExt())
		{
			$ext = pathinfo($fullPath, PATHINFO_EXTENSION);
			$dbFileAsset->setFileExt($ext);
		}
		$dbFileAsset->setSize(kFile::fileSize($fullPath));
		$dbFileAsset->incrementVersion();
		$dbFileAsset->save();
		
		$syncKey = $dbFileAsset->getSyncKey(FileAsset::FILE_SYNC_ASSET);
		
		kFileSyncUtils::moveFromFile($fullPath, $syncKey, true, $copyOnly);
		
		$dbFileAsset->setStatus(FileAssetStatus::READY);
		$dbFileAsset->save();
    }
    
	/**
	 * @param FileAsset $dbFileAsset
	 * @param FileSyncKey $srcSyncKey
	 */
	protected function attachFileSync(FileAsset $dbFileAsset, FileSyncKey $srcSyncKey)
	{
		$dbFileAsset->incrementVersion();
		$dbFileAsset->save();
		
        $newSyncKey = $dbFileAsset->getSyncKey(FileAsset::FILE_SYNC_ASSET);
        kFileSyncUtils::createSyncFileLinkForKey($newSyncKey, $srcSyncKey);
                
        $fileSync = kFileSyncUtils::getLocalFileSyncForKey($newSyncKey, false);
        $fileSync = kFileSyncUtils::resolve($fileSync);
        
		$dbFileAsset->setStatus(FileAssetStatus::READY);
		$dbFileAsset->setSize($fileSync->getFileSize());
		$dbFileAsset->save();
    }
    
	/**
	 * List file assets by filter and pager
	 * 
	 * @action list
	 * @param KalturaFilterPager $filter
	 * @param KalturaFileAssetFilter $pager
	 * @return KalturaFileAssetListResponse
	 * @ksIgnored
	 */
	function listAction(KalturaFileAssetFilter $filter, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaFileAssetFilter();
			
		if(!$pager)
			$pager = new KalturaFilterPager();
			
		return $filter->getListResponse($pager, $this->getResponseProfile());   
	}
}