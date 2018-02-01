<?php
/**
 * @package plugins.reach
 */
class kReachManager implements kObjectChangedEventConsumer, kObjectCreatedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::shouldConsumeAddedEvent()
	 */
	public function shouldConsumeCreatedEvent(BaseObject $object)
	{
		if($object instanceof EntryVendorTask)
			return true;
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	*/
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if($object instanceof EntryVendorTask 
			&& in_array(EntryVendorTaskPeer::STATUS, $modifiedColumns) 
			&& $object->getStatus() == EntryVendorTaskStatus::PENDING 
			&& $object->getColumnsOldValue(EntryVendorTaskPeer::STATUS) != EntryVendorTaskStatus::PENDING_MODERATION)
			return true;
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::objectAdded()
	 */
	public function objectCreated(BaseObject $object, BatchJob $raisedJob = null)
	{
		$this->addJobData($object);
		if($object->getStatus() == EntryVendorTaskStatus::PENDING) 
			$this->updateVendorProfileCreditUsage($object);
		
		return true;
	}
	
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		if($object instanceof EntryVendorTask
			&& in_array(EntryVendorTaskPeer::STATUS, $modifiedColumns)
			&& $object->getStatus() == EntryVendorTaskStatus::PENDING
			&& $object->getColumnsOldValue(EntryVendorTaskPeer::STATUS) != EntryVendorTaskStatus::PENDING_MODERATION)
			return $this->updateVendorProfileCreditUsage($object);
		
		return true;
	}
	
	public function updateVendorProfileCreditUsage(EntryVendorTask $entryVendorTask)
	{
		$vendorProfile = VendorProfilePeer::retrieveByPK($entryVendorTask->getVendorProfileId());
		$vendorProfile->setUsedCredit($vendorProfile->getUsedCredit() + $entryVendorTask->getPrice());
		$vendorProfile->save();
	}
	
	public static function addEntryVendorTask($entryId, $catalogItemId, $vendorProfileId)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		$vendorCatalogItem = VendorCatalogItemPeer::retrieveByPK($catalogItemId);
		$vendorProfile = VendorProfilePeer::retrieveByPK($vendorProfileId);
		
		$status = EntryVendorTaskStatus::PENDING;
		if($vendorProfile->shouldModerate($vendorCatalogItem->getServiceType()))
			$status = EntryVendorTaskStatus::PENDING_MODERATION;
		
		$entryVendorTask = new EntryVendorTask();
		$entryVendorTask->setEntryId($entryId);
		$entryVendorTask->setCatalogItemId($catalogItemId);
		$entryVendorTask->setVendorPartnerId($vendorProfileId);
		$entryVendorTask->setPartnerId($entry->getPartnerId());
		$entryVendorTask->setStatus($status);
		$entryVendorTask->save();
	}
	
	private function addJobData(EntryVendorTask $entryVendorTask)
	{
		$entryVendorTask->setAccessKey(kReachUtils::generateReachVendorKs($entryVendorTask->getEntryId()));
		$entryVendorTask->setPrice(kReachUtils::calculateTaskPrice($entryVendorTask->getEntry(), $entryVendorTask->getCatalogItem()));
		$entryVendorTask->setUserId(kCurrentContext::$ks_uid);
		$entryVendorTask->setVendorPartnerId($entryVendorTask->getCatalogItem()->getVendorPartnerId());
		$entryVendorTask->save();
	}
}