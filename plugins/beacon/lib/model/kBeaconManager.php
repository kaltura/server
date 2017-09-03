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
		return false;
		
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
	
	private function handleObjectDeleted($object, $objectType)
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
	
	public static function deleteByBeaconId($beaconId, $indexType)
	{
		// get instance of activated queue provider to send message
		$constructorArgs = array();
		$constructorArgs['exchangeName'] = kBeacon::BEACONS_EXCHANGE_NAME;
		
		/* @var $queueProvider RabbitMQProvider */
		$queueProvider = QueueProvider::getInstance(null, $constructorArgs);
		
		$deleteObject = array();
		
		//Set Action Name and Index Name and calculated docuemtn idec-3	
		$deleteObject[kBeacon::ELASTIC_ACTION_KEY] = kBeacon::ELASTIC_DELETE_ACTION_VALUE;
		$deleteObject[kBeacon::ELASTIC_INDEX_KEY] = kBeacon::ELASTIC_BEACONS_INDEX_NAME;
		$deleteObject[kBeacon::ELASTIC_DOCUMENT_ID_KEY] = $beaconId;
		$deleteObject[kBeacon::ELASTIC_INDEX_TYPE_KEY] = $indexType;
		
		$queueProvider->send(kBeacon::BEACONS_QUEUE_NAME, json_encode($deleteObject));
	}
}
