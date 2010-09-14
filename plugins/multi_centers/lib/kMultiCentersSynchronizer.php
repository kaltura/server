<?php
class kMultiCentersSynchronizer implements kObjectCreatedEventConsumer
{
	public function getEntryId(FileSync $fileSync)
	{
		if($fileSync->getObjectType() == FileSync::FILE_SYNC_OBJECT_TYPE_ENTRY)
			return $fileSync->getObjectId();
			
		if($fileSync->getObjectType() == FileSync::FILE_SYNC_OBJECT_TYPE_BATCHJOB)
		{
			$job = BatchJobPeer::retrieveByPK($fileSync->getObjectId());
			if($job)
				return $job->getEntryId();
		}
			
		if($fileSync->getObjectType() == FileSync::FILE_SYNC_OBJECT_TYPE_FLAVOR_ASSET)
		{
			$flavor = flavorAssetPeer::retrieveById($fileSync->getObjectId());
			if($flavor)
				return $flavor->getEntryId();
		}
			
		return null;
	}
	
	/**
	 * @param BaseObject $object
	 */
	public function objectCreated(BaseObject $object)
	{
		if(!($object instanceof FileSync) || $object->getStatus() != FileSync::FILE_SYNC_STATUS_PENDING || $object->getFileType() != FileSync::FILE_SYNC_FILE_TYPE_FILE)
			return;
			
		$kalturaDc = StorageProfilePeer::retrieveByPK($object->getDc());
		if(!$kalturaDc)
		{
			KalturaLog::err('Kaltura DC [' . $object->getDc() . '] not found');
			return;
		}
			
		$key = kFileSyncUtils::getKeyForFileSync($object);
		$srcFileSyncLocalPath = kFileSyncUtils::getLocalFilePathForKey($key, true);
		$entryId = $this->getEntryId($object);
		$job = kJobsManager::addStorageExportJob(null, $entryId, $object->getPartnerId(), $kalturaDc, $object, $srcFileSyncLocalPath);
	}
}