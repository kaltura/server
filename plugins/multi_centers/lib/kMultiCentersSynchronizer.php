<?php
class kMultiCentersSynchronizer implements kObjectAddedEventConsumer
{
	public function getEntryId(FileSync $fileSync)
	{
		if($fileSync->getObjectType() == FileSyncObjectType::ENTRY)
			return $fileSync->getObjectId();
			
		if($fileSync->getObjectType() == FileSyncObjectType::BATCHJOB)
		{
			$job = BatchJobPeer::retrieveByPK($fileSync->getObjectId());
			if($job)
				return $job->getEntryId();
		}
			
		if($fileSync->getObjectType() == FileSyncObjectType::FLAVOR_ASSET)
		{
			$flavor = flavorAssetPeer::retrieveById($fileSync->getObjectId());
			if($flavor)
				return $flavor->getEntryId();
		}
			
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::objectAdded()
	 */
	public function objectAdded(BaseObject $object, BatchJob $raisedJob = null)
	{
		if(
			!($object instanceof FileSync) 
			|| 
			$object->getStatus() != FileSync::FILE_SYNC_STATUS_PENDING 
			|| 
			$object->getFileType() != FileSync::FILE_SYNC_FILE_TYPE_FILE 
			|| 
			$object->getDc() == kDataCenterMgr::getCurrentDcId()
		)
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
		
		$job = kMultiCentersManager::addFileSyncImportJob($this->getEntryId($object), $object->getPartnerId(), $object->getId(), $sourceFileUrl, $raisedJob);
		
		$job->setDc($object->getDc());
		$job->save();
		
		return true;
	}
}