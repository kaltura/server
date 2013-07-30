<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kStorageDeleteJobData extends kStorageJobData
{
    /**
     * @return kStorageDeleteJobData
     */
    public static function getInstance($protocol)
    {
        $data = null;

        $data = KalturaPluginManager::loadObject('kStorageDeleteJobData', $protocol);
        
        if (!$data)
            $data = new kStorageDeleteJobData();
        
        return $data;
    }
    /**
     * @var StorageProfile $storage
     * @var FileSync $fileSync
     */
    public function setJobData (StorageProfile $storage, FileSync $filesync)
    {
        $this->setServerUrl($storage->getStorageUrl()); 
        $this->setServerUsername($storage->getStorageUsername()); 
        $this->setServerPassword($storage->getStoragePassword());
        $this->setFtpPassiveMode($storage->getStorageFtpPassiveMode());

        $this->setSrcFileSyncId($fileSync->getId());
        $this->setDestFileSyncStoredPath($storage->getStorageBaseDir() . '/' . $fileSync->getFilePath());
    }
    
}