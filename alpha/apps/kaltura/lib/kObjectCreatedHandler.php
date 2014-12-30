<?php

class kObjectCreatedHandler implements kObjectCreatedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::shouldConsumeCreatedEvent()
	 */
	public function shouldConsumeCreatedEvent(BaseObject $object)
	{
		if($object instanceof Entry)
		{
			if ($object->getIsRecordedEntry() == true)
				return true;
		}
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::objectCreated()
	 */
	public function objectCreated(BaseObject $object)
	{
		/* @var $object entry */
		$rootEntryId = $object->getRootEntryId();
		if(!$rootEntryId)
			return true; 
		
		$liveEntry = entryPeer::retrieveByPK($rootEntryId);
		if(!$liveEntry)
		{
			KalturaLog::debug("Live entry with id [{$object->getRootEntryId()}] not found, categories will not be copied");
			return true;
		}
		
		/* @var $liveEntry LiveEntry */
		$recordingOptions = $liveEntry->getRecordingOptions();
		/* @var $recordingOptions kLiveEntryRecordingOptions */
		if($recordingOptions->getShouldCopyEntitlement())
		{
			$this->syncEntryEntitlementInfo($object, $liveEntry);
			$this->syncCategoryEntries($object, $liveEntry);
		}
		
		return true;
	}
	
	public function syncCategoryEntries(entry $vodEntry, LiveEntry $liveEntry)
	{
		$liveCategoryEntryArray = categoryEntryPeer::selectByEntryId($liveEntry->getId());
		
		if(!count($liveCategoryEntryArray))
			return;

		foreach($liveCategoryEntryArray as $categoryEntry)
		{
			/* @var $categoryEntry categoryEntry */
			$vodCategoryEntry = $categoryEntry->copy();
			$vodCategoryEntry->setEntryId($vodEntry->getId());
			$vodCategoryEntry->save();
		}
	}
	
	public function syncEntryEntitlementInfo(entry $vodEntry, LiveEntry $liveEntry)
	{
		$entitledPusersEdit = $liveEntry->getEntitledPusersEdit();
		$entitledPusersPublish = $liveEntry->getEntitledPusersPublish();
		
		if(!$entitledPusersEdit && !$entitledPusersPublish)
			return;
		
		if($entitledPusersEdit)
			$vodEntry->setEntitledPusersEdit($entitledPusersEdit);
		
		if($entitledPusersPublish)
			$vodEntry->setEntitledPusersPublish($entitledPusersPublish);
			
		$vodEntry->save();
	}
}
