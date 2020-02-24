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
		switch($protocol)
		{
			case StorageProfile::STORAGE_PROTOCOL_GCP:
				$data = new kGCPStorageDeleteJobData();
				break;
			default:
				$data = KalturaPluginManager::loadObject('kStorageDeleteJobData', $protocol);
				break;
		}
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
        $this->setServerPrivateKey($storage->getPrivateKey());
        $this->setServerPublicKey($storage->getPublicKey());
        $this->setServerPassPhrase($storage->getPassPhrase());
        $this->setFtpPassiveMode($storage->getStorageFtpPassiveMode());

        $this->setSrcFileSyncId($filesync->getId());
        $this->setDestFileSyncStoredPath($storage->getStorageBaseDir() . '/' . $filesync->getFilePath());
    }
    
}