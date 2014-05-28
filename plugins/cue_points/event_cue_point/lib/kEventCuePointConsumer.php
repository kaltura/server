<?php 
class kEventCuePointConsumer implements kObjectChangedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	*/
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		$customData = $object->getColumnsOldValue(entryPeer::CUSTOM_DATA);
		$oldMediaServers = $customData->get(null, 'mediaServers');
		$newMediaServers = $object->getMediaServers();
		
		if(is_empty($oldMediaServers) && !is_empty($newMediaServers)) {
			KalturaLog::err("@_!! Add Start Stream event cue point");
		}
		
		if(is_empty($newMediaServers) && !is_empty($oldMediaServers)) {
			KalturaLog::err("@_!! Add End Stream event cue point");
		}
	}
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	*/
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if(($object instanceof LiveEntry) && in_array(entryPeer::CUSTOM_DATA, $modifiedColumns)) {
			
			if($object->isCustomDataModified(null, 'mediaServers'))
				return true;
		}
		
		return false;
	}
}