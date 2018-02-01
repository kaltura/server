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
	public function objectCreated(BaseObject $object, BatchJob $raisedJob = null)
	{
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
	
	public static function addEntryVendorTaskByObjectIds($entryId, $vendorCatalogItemId, $vendorProfileId)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		$vendorProfile = VendorProfilePeer::retrieveByPK($vendorProfileId);
		$vendorCatalogItem = VendorCatalogItemPeer::retrieveByPK($vendorCatalogItemId);
		
		if(!kReachUtils::isEnoughCreditLeft($entry, $vendorCatalogItem, $vendorProfile))
		{
			KalturaLog::err("Exceeded max credit allowed, Task could not be added for entry [$entryId] and catalog item [$vendorCatalogItemId]");
			return;
		}
		
		$entryVendorTask = self::addEntryVendorTask($entry, $vendorProfile, $vendorCatalogItem);
		return $entryVendorTask;
	}
	
	public static function addEntryVendorTask(entry $entry, VendorProfile $vendorProfile, VendorCatalogItem $vendorCatalogItem)
	{
		//Create new entry vendor task object
		$entryVendorTask = new EntryVendorTask();
		
		//Assign default parameters
		$entryVendorTask->setEntryId($entry->getId());
		$entryVendorTask->setCatalogItemId($vendorCatalogItem->getId());
		$entryVendorTask->setVendorProfileId($vendorProfile->getId());
		$entryVendorTask->setPartnerId($entry->getPartnerId());
		$entryVendorTask->setUserId(kCurrentContext::$ks_uid);
		$entryVendorTask->setVendorPartnerId($vendorCatalogItem->getVendorPartnerId());
		
		//Set calcualted values
		$entryVendorTask->setAccessKey(kReachUtils::generateReachVendorKs($entryVendorTask->getEntryId()));
		$entryVendorTask->setPrice(kReachUtils::calculateTaskPrice($entry, $vendorCatalogItem));
		
		$status = EntryVendorTaskStatus::PENDING;
		if($vendorProfile->shouldModerate($vendorCatalogItem->getServiceType()))
			$status = EntryVendorTaskStatus::PENDING_MODERATION;
		
		$entryVendorTask->setStatus($status);
		$entryVendorTask->save();
		
		return $entryVendorTask;
	}
}