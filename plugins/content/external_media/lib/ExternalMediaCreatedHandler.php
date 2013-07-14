<?php
/**
 * @package plugins.externalMedia
 * @subpackage lib
 */
class ExternalMediaCreatedHandler implements kObjectAddedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::shouldConsumeAddedEvent()
	 */
	public function shouldConsumeAddedEvent(BaseObject $object)
	{
		if($object instanceof ExternalMediaEntry)
			return true;
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::objectAdded()
	 */
	public function objectAdded(BaseObject $object, BatchJob $raisedJob = null)
	{
		/* @var $object ExternalMediaEntry */
		$object->setStatus(entryStatus::READY);
		$object->save();
		
		return true;
	}
}