<?php
class kSipEventsConsumer implements kObjectDeletedEventConsumer
{	

	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::objectDeleted()
	 */
	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null) 
	{
		try 
		{
			$pexipConfig = kPexipUtils::initAndValidateConfig();
			kPexipHandler::deleteCallObjects($object, $pexipConfig);
		}
		catch(Exception $e)
		{
			KalturaLog::err('Failed to process Sip objectDeleted for liveEntry ['.$object->getId().'] - '.$e->getMessage());
		}
		return true;
	}

	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::shouldConsumeDeletedEvent()
	 */
	public function shouldConsumeDeletedEvent(BaseObject $object)
	{
		if ($object instanceof LiveStreamEntry && $object->getIsSipEnabled())
		{
			return true;
		}
		return false;
	}
	
}