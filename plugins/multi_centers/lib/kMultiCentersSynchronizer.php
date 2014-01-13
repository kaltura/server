<?php
/**
 * @package plugins.multiCenters
 * @subpackage lib
 */
class kMultiCentersSynchronizer implements kObjectAddedEventConsumer
{
	/**
	 * Contain all object types and sub types that shouldn't be synced 
	 * @var array
	 */
	protected $excludedSyncFileObjectTypes = null;
	
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
			$flavor = assetPeer::retrieveById($fileSync->getObjectId());
			if($flavor)
				return $flavor->getEntryId();
		}
			
		return null;
	}
	
	/**
	 * Check if specific file sync that belong to object type and sub type should be synced
	 * 
	 * @param int $objectType
	 * @param int $objectSubType
	 * @return bool
	 */
	public function shouldSyncFileObjectType($objectType, $objectSubType)
	{
		if(is_null($this->excludedSyncFileObjectTypes))
		{
			$this->excludedSyncFileObjectTypes = array();
			$dcConfig = kConf::getMap("dc_config");
			if(isset($dcConfig['sync_exclude_types']))
			{
				foreach($dcConfig['sync_exclude_types'] as $syncExcludeType)
				{
					$configObjectType = $syncExcludeType;
					$configObjectSubType = null;
					
					if(strpos($syncExcludeType, ':') > 0)
						list($configObjectType, $configObjectSubType) = explode(':', $syncExcludeType, 2);
						
					// translate api dynamic enum, such as contentDistribution.EntryDistribution - {plugin name}.{object name}
					if(!is_numeric($configObjectType))
						$configObjectType = kPluginableEnumsManager::apiToCore('FileSyncObjectType', $configObjectType);
							
					// translate api dynamic enum, including the enum type, such as conversionEngineType.mp4box.Mp4box - {enum class name}.{plugin name}.{object name}
					if(!is_null($configObjectSubType) && !is_numeric($configObjectSubType))
					{
						list($enumType, $configObjectSubType) = explode('.', $configObjectSubType);
						$configObjectSubType = kPluginableEnumsManager::apiToCore($enumType, $configObjectSubType);
					}
					
					if(!isset($this->excludedSyncFileObjectTypes[$configObjectType]))
						$this->excludedSyncFileObjectTypes[$configObjectType] = array();
						
					if(!is_null($configObjectSubType))
						$this->excludedSyncFileObjectTypes[$configObjectType][] = $configObjectSubType;
				}
			}
		}
		
		if(!isset($this->excludedSyncFileObjectTypes[$objectType]))
			return true;
			
		if(count($this->excludedSyncFileObjectTypes[$objectType]) && !in_array($objectSubType, $this->excludedSyncFileObjectTypes[$objectType]))
			return true;
			
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::shouldConsumeAddedEvent()
	 */
	public function shouldConsumeAddedEvent(BaseObject $object)
	{
		if(
			$object instanceof FileSync 
			&&
			$object->getStatus() == FileSync::FILE_SYNC_STATUS_PENDING 
			&&
			$object->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_FILE 
			&&
			$object->getDc() != kDataCenterMgr::getCurrentDcId()
			&&
			$this->shouldSyncFileObjectType($object->getObjectType(), $object->getObjectSubType())
		)
			return true;
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::objectAdded()
	 */
	public function objectAdded(BaseObject $object, BatchJob $raisedJob = null)
	{
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
		
		$entryId = $this->getEntryId($object);
		
		$sourceFileUrl = $original_filesync->getExternalUrl($entryId);
		if (!$sourceFileUrl) {
			KalturaLog::err('External URL not found for filesync id [' . $object->getId() . ']');
			return true;
		}				
		
		$job = kMultiCentersManager::addFileSyncImportJob($entryId, $object, $sourceFileUrl, $raisedJob, $original_filesync->getFileSize());
		
		$job->save();
		
		return true;
	}
}
