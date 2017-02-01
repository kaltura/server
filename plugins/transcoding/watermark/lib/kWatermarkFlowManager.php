<?php

/**
 * @package plugins.watermark
 * @subpackage lib
 */
class kWatermarkFlowManager implements kObjectAddedEventConsumer
{
	/* (non-PHPdoc)
 	* @see kObjectAddedEventConsumer::shouldConsumeAddedEvent()
 	*/
	public function shouldConsumeAddedEvent(BaseObject $object)
	{
		if($object instanceof entry && $object->getReplacedEntryId() && $object->getIsTemporary())
			return true;
		
		return false;
	}
	
	/* (non-PHPdoc)
 	* @see kObjectAddedEventConsumer::objectAdded()
 	*/
	public function objectAdded(BaseObject $object, BatchJob $raisedJob = null)
	{
		if($object instanceof entry && $object->getReplacedEntryId() && $object->getIsTemporary())
		{
			$this->copyWatermarkData($object);
		}
		
		return true;
	}
	
	protected function copyWatermarkData(entry $entry)
	{
		$originalEntryId = $entry->getReplacedEntryId();
		$originalEntry = entryPeer::retrieveByPK($originalEntryId);
		if(!$originalEntry)
		{
			KalturaLog::debug("Original entry with id [$originalEntryId], not found");
			return;
		}
		
		KalturaLog::debug("Original entry id $originalEntryId");
		KalturaLog::debug("Replacing entry id [{$entry->getId()}]");
		
		$watermarkMetadata = kWatermarkManager::getWatermarkMetadata($originalEntry);
		if(!$watermarkMetadata)
		{
			KalturaLog::debug("Watermark data not found for entry [$originalEntryId]");
			return true;
		}
		
		kWatermarkManager::copyWatermarkData($watermarkMetadata, $originalEntry, $entry);
	}
}