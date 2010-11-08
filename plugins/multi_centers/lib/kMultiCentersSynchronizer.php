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
	 * @return bool true if should continue to the next consumer
	 */
	public function objectCreated(BaseObject $object)
	{
		if(!($object instanceof FileSync) || $object->getStatus() != FileSync::FILE_SYNC_STATUS_PENDING || $object->getFileType() != FileSync::FILE_SYNC_FILE_TYPE_FILE)
			return true;

		$c = new Criteria();
		$c->addAnd(FileSyncPeer::OBJECT_ID, $object->getObjectId());
		$c->addAnd(FileSyncPeer::VERSION, $object->getVersion());
		$c->addAnd(FileSyncPeer::OBJECT_TYPE, $object->getObjectType());
		$c->addAnd(FileSyncPeer::OBJECT_SUB_TYPE, $object->getObjectSubType());
		$c->addAnd(FileSyncPeer::ORIGINAL, '1');
		$original_filesync = FileSyncPeer::doSelectOne($c);
		if (!$original_filesync) {
			KalturaLog::err('Original filesync not found for object_id['.$object->getObjectId().'] version['.$object->getVersion().'] type['.$object->getObjectType().'] subtype['.$object->getObjectSubType().']');
			return true;
		}
		$sourceFileUrl = $original_filesync->getExternalUrl();
		if (!$sourceFileUrl) {
			KalturaLog::err('External URL not found for filesync id [' . $object->getId() . ']');
			return true;
		}				
		
		$job = kMultiCentersManager::addFileSyncImportJob($this->getEntryId($object), $object->getPartnerId(), $object->getId(), $sourceFileUrl);
		
		$job->setDc($object->getDc());
		$job->save();
		
		return true;
	}
}