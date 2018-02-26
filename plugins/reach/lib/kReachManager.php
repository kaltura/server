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
			&& $object->getColumnsOldValue(EntryVendorTaskPeer::STATUS) == EntryVendorTaskStatus::PENDING_MODERATION
		)
			return true;
		
		if($object instanceof EntryVendorTask
			&& in_array(EntryVendorTaskPeer::STATUS, $modifiedColumns)
			&& $object->getStatus() == EntryVendorTaskStatus::ERROR
		)
			return true;
		
		if($object instanceof entry && $object->getType() == entryType::MEDIA_CLIP &&
			in_array(entryPeer::LENGTH_IN_MSECS, $modifiedColumns)
		)
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
		if($object instanceof EntryVendorTask && in_array(EntryVendorTaskPeer::STATUS, $modifiedColumns)
			&& $object->getStatus() == EntryVendorTaskStatus::PENDING
			&& $object->getColumnsOldValue(EntryVendorTaskPeer::STATUS) != EntryVendorTaskStatus::PENDING_MODERATION
		)
			return $this->updateVendorProfileCreditUsage($object);
		
		if($object instanceof EntryVendorTask
			&& in_array(EntryVendorTaskPeer::STATUS, $modifiedColumns)
			&& $object->getStatus() == EntryVendorTaskStatus::ERROR
			&& in_array($object->getColumnsOldValue(EntryVendorTaskPeer::STATUS), array(EntryVendorTaskStatus::PENDING, EntryVendorTaskStatus::PROCESSING))
		)
			return $this->handleErrorTask($object);

		if($object instanceof entry && $object->getType() == entryType::MEDIA_CLIP &&
			in_array(entryPeer::LENGTH_IN_MSECS, $modifiedColumns)
		)
			return $this->handleEntryDurationChanged($object);
		
		return true;
	}
	
	private function updateVendorProfileCreditUsage(EntryVendorTask $entryVendorTask)
	{
		VendorProfilePeer::updateUsedCredit($entryVendorTask->getVendorProfileId(), $entryVendorTask->getPrice());
	}
	
	private function handleErrorTask(EntryVendorTask $entryVendorTask)
	{
		VendorProfilePeer::updateUsedCredit($entryVendorTask->getVendorProfileId(), -$entryVendorTask->getPrice());
	}

	private function handleEntryDurationChanged(entry $entry)
	{
		$pendingEntryVendorTasks = EntryVendorTaskPeer::retrievePendingByEntryId($entry->getId());
		$addedCostByProfileId = array();
		foreach ($pendingEntryVendorTasks as $pendingEntryVendorTask)
		{
			/* @var $pendingEntryVendorTask EntryVendorTask */
			$oldPrice = $pendingEntryVendorTask->getPrice();
			$newPrice = kReachUtils::calculateTaskPrice($entry, $pendingEntryVendorTask->getCatalogItem());
			$priceDiff = $newPrice - $oldPrice;
			$pendingEntryVendorTask->setPrice($newPrice);
			
			if(!isset($addedCostByProfileId[$pendingEntryVendorTask->getVendorProfileId()]))
				$addedCostByProfileId[$pendingEntryVendorTask->getVendorProfileId()] = 0;
			
			if(kReachUtils::checkPriceAddon($pendingEntryVendorTask, $priceDiff))
			{
				$pendingEntryVendorTask->save();
				$addedCostByProfileId[$pendingEntryVendorTask->getVendorProfileId()] += $priceDiff;
			}
			else
			{
				$pendingEntryVendorTask->setStatus(EntryVendorTaskStatus::ABORTED);
				$pendingEntryVendorTask->setPrice($newPrice);
				$pendingEntryVendorTask->setErrDescription("Current task price exceeded credit allowed, task was aborted");
				$pendingEntryVendorTask->save();
				$addedCostByProfileId[$pendingEntryVendorTask->getVendorProfileId()] -= $oldPrice;
			}
		}
		
		foreach($addedCostByProfileId as $vendorProfileId => $addedCost)
		{
			VendorProfilePeer::updateUsedCredit($vendorProfileId, $addedCost);
		}
		
		return true;
	}
	
	public static function addEntryVendorTaskByObjectIds($entryId, $vendorCatalogItemId, $vendorProfileId)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		$vendorProfile = VendorProfilePeer::retrieveByPK($vendorProfileId);
		$vendorCatalogItem = VendorCatalogItemPeer::retrieveByPK($vendorCatalogItemId);

		$sourceFlavor = assetPeer::retrieveOriginalByEntryId($entry->getId());
		$sourceFlavorVersion = $sourceFlavor != null ? $sourceFlavor->getVersion() : 0;

		if( EntryVendorTaskPeer::retrieveEntryIdAndCatalogItemIdAndEntryVersion($entryId, $vendorCatalogItemId, $entry->getPartnerId(), $sourceFlavorVersion))
		{
			KalturaLog::err("Trying to insert a duplicate entry vendor task for entry [$entryId], catalog item [$vendorCatalogItemId] and entry version [$sourceFlavorVersion]");
			return true;
		}

		if(!kReachUtils::isEnoughCreditLeft($entry, $vendorCatalogItem, $vendorProfile))
		{
			KalturaLog::err("Exceeded max credit allowed, Task could not be added for entry [$entryId] and catalog item [$vendorCatalogItemId]");
			return true;
		}
		
		$entryVendorTask = self::addEntryVendorTask($entry, $vendorProfile, $vendorCatalogItem, false, $sourceFlavorVersion);
		return $entryVendorTask;
	}
	
	public static function addEntryVendorTask(entry $entry, VendorProfile $vendorProfile, VendorCatalogItem $vendorCatalogItem, $validateModeration = true, $version = 0)
	{
		//Create new entry vendor task object
		$entryVendorTask = new EntryVendorTask();
		
		//Assign default parameters
		$entryVendorTask->setEntryId($entry->getId());
		$entryVendorTask->setCatalogItemId($vendorCatalogItem->getId());
		$entryVendorTask->setVendorProfileId($vendorProfile->getId());
		$entryVendorTask->setPartnerId($entry->getPartnerId());
		$entryVendorTask->setKuserId(kCurrentContext::getCurrentKsKuserId());
		$entryVendorTask->setUserId(kCurrentContext::$ks_uid);
		$entryVendorTask->setVendorPartnerId($vendorCatalogItem->getVendorPartnerId());
		$entryVendorTask->setVersion($version);
		
		//Set calculated values
		$entryVendorTask->setAccessKey(kReachUtils::generateReachVendorKs($entryVendorTask->getEntryId()));
		$entryVendorTask->setPrice(kReachUtils::calculateTaskPrice($entry, $vendorCatalogItem));
		
		$status = EntryVendorTaskStatus::PENDING;
		if($validateModeration && $vendorProfile->shouldModerate($vendorCatalogItem->getServiceType()))
			$status = EntryVendorTaskStatus::PENDING_MODERATION;

		foreach ($vendorProfile->getDictionariesArray() as $dictionary)
		{
			/* @var kDictionary $dictionary*/
			if ($dictionary->getLanguage() == $vendorCatalogItem->getSourceLanguage())
			{
				$entryVendorTask->setDictionary($dictionary->getData());
				break;
			}
		}

		$entryVendorTask->setStatus($status);
		$entryVendorTask->save();
		
		return $entryVendorTask;
	}
}