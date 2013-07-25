<?php
/**
 * Kontiki job data
 * @package plugins.kontiki
 * @subpackage model
 * */
class kKontikiStorageExportJobData extends kStorageExportJobData
{
	/**
	 * Holds the id of the exported asset
	 * @var string
	 */
 	protected $flavorAssetId;

	/**
	 * Unique Kontiki MOID for the content uploaded to Kontiki
	 * @var string
	 */
	protected $contentMoid;

	/**
	 * @var string
	 */
	protected $serviceToken;


    public function setContentMoid($contentMoid) 
    {
        $this->contentMoid = $contentMoid;
    }

    public function getContentMoid() 
    {
        return $this->contentMoid;
    }
	
	public function setFlavorAssetId($flavorAssetId) 
    {
        $this->flavorAssetId = $flavorAssetId;
    }

    public function getFlavorAssetId() 
    {
        return $this->flavorAssetId;
    }
	
	public function setServiceToken($v) 
    {
        $this->serviceToken = $v;
    }

    public function getServiceToken() 
    {
        return $this->serviceToken;
    }
    
    public function setStorageExportJobData(StorageProfile $externalStorage, FileSync $fileSync, $srcFileSyncLocalPath, $force = false)
    {
        /* @var $externalStorage KontikiStorageProfile */
        $this->setServerUrl($externalStorage->getStorageUrl());
        $this->setServiceToken($externalStorage->getServiceToken()); 
        $this->setSrcFileSyncId($fileSync->getId());
        if ($fileSync->getObjectType() != FileSyncObjectType::ASSET)
            throw new kCoreException("Incompatible filesync type", kCoreException::INTERNAL_SERVER_ERROR);
        
        $this->setFlavorAssetId($fileSync->getObjectId());
    }
}