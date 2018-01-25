<?php
/**
 * @package plugins.reach
 */
class kReachManager implements kObjectChangedEventConsumer, kObjectAddedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::shouldConsumeAddedEvent()
	 */
	public function shouldConsumeAddedEvent(BaseObject $object)
	{
		if($object instanceof EntryVendorTask && $object->getStatus() == EntryVendorTaskStatus::PENDING)
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
	public function objectAdded(BaseObject $object, BatchJob $raisedJob = null)
	{
		if($object instanceof EntryVendorTask && $object->getStatus() == EntryVendorTaskStatus::PENDING)
			return $this->updateVendorProfileCreditUsage($object);
		
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
		
		return true;
	}
	
	public static function addEntryVendorTask($entryId, $catalogItemId, $vendorProfileId)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		$catalogItem = VendorCatalogItemPeer::retrieveByPK($catalogItemId);
		
		$entryVendorTask = new EntryVendorTask();
		
		$entryVendorTask->setEntryId($entryId);
		$entryVendorTask->setCatalogItemId($catalogItemId);
		$entryVendorTask->setVendorProfileId($vendorProfileId);
		
		$entryVendorTask->setPartnerId($entry->getId());
		$entryVendorTask->setVendorPartnerId($catalogItem->getVendorPartnerId());
		$entryVendorTask->setUserId(kCurrentContext::getCurrentKsKuserId());
		
		$entryVendorTask->setAccessKey(kReachUtils::generateReachVendorKs($entryId));
		$entryVendorTask->setPrice(kReachUtils::calculateTaskPrice($entry, $catalogItem));
		
		$entryVendorTask->setStatus(EntryVendorTaskStatus::PENDING);
		
		$entryVendorTask->save();
	}
}