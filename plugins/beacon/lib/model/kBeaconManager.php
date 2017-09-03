<?php

/**
 * @package plugins.beacon
 * @subpackage model
 */
class kBeaconManager implements kObjectDeletedEventConsumer
{
	/* (non-PHPdoc)
 	 * @see kObjectDeletedEventConsumer::shouldConsumeDeletedEvent()
 	 */
	public function shouldConsumeDeletedEvent(BaseObject $object)
	{
		if ($object instanceof entry)
			return true;
		
		if ($object instanceof EntryServerNode)
			return true;
		
		if ($object instanceof ScheduleResource)
			return true;
		
		if ($object instanceof ServerNode)
			return true;
		
		return false;
	}
	
	/* (non-PHPdoc)
 	 * @see kObjectDeletedEventConsumer::objectDeleted()
 	 */
	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null)
	{
		if ($object instanceof entry)
			$this->handleObjectDeleted($object, BeaconObjectTypes::ENTRY_BEACON);
		
		if ($object instanceof EntryServerNode)
			$this->handleObjectDeleted($object, BeaconObjectTypes::ENTRY_SERVER_NODE_BEACON);
		
		if ($object instanceof ScheduleResource)
			$this->handleObjectDeleted($object, BeaconObjectTypes::SCHEDULE_RESOURCE_BEACON);
		
		if ($object instanceof ServerNode)
			$this->handleObjectDeleted($object, BeaconObjectTypes::SERVER_NODE_BEACON);
		
		return true;
	}
	
	private function entryDeleted($object, $objectType)
	{
		$jobData = new kClearBeconsJobData();
		$jobData->setObjectId($object->getId());
		$jobData->setRelatedObjectType($objectType);
		
		$jobType = BeaconPlugin::getBatchJobTypeCoreValue(ClearBeaconsBatchType::CLEAR_BEACONS);
		$batchJob = new BatchJob();
		$batchJob->setPartnerId($object->getPartnerId());
		$batchJob->setObjectId($object->getId());
		
		return kJobsManager::addJob($batchJob, $jobData, $jobType);
	}
}
