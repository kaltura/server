<?php 
class kEventCuePointConsumer implements kObjectChangedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	*/
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		$customData = $object->getCustomDataOldValues();
		$updatedServers = array();
		if(array_key_exists('mediaServers', $customData)) {
			$updatedServers =  array_keys($customData['mediaServers']);
		}

		$mediaServersInfo = $object->getMediaServers();
		if(empty($mediaServersInfo)) { 
			$currentMediaServers = array();
		} else {
			$currentMediaServers = array_keys($mediaServersInfo);
		}
		
		// If currently only one is set, and that's the one that was just changed
		$updatedMediaServers = array_intersect($updatedServers, $currentMediaServers);
		if((count($currentMediaServers) == 1) && (count($updatedMediaServers) == 1)) {
			
			// This hack was made to avoid cases in which someone updates the media server.
			$updatedMediaServer = $updatedMediaServers[0];
			$oldHost = $customData['mediaServers'][$updatedMediaServer];
			if(is_null($oldHost))
				$this->addEventCuePoint($object, EventType::BROADCAST_START);
		}
		
		// If currently no one is set
		if(count($currentMediaServers) == 0) {
			$this->addEventCuePoint($object, EventType::BROADCAST_END);
		}
		
		return true;
	}
	
	protected function addEventCuePoint(LiveEntry $liveEntry, $eventType) {
		$cuePoint = new EventCuePoint();
		$cuePoint->setPartnerId($liveEntry->getPartnerId());
		$cuePoint->setSubType($eventType);
		$cuePoint->setEntryId($liveEntry->getId());
		$cuePoint->setStartTime(time());
		$cuePoint->setStatus(CuePointStatus::READY);
		$cuePoint->save();
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	*/
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		//These cue point are not used by any of our applications or by our internal logic 
		// so until we are sure it is safe to remove the entrie class I will return false to not consum these event
		return false;
		
		if(($object instanceof LiveEntry) && 
				in_array(entryPeer::CUSTOM_DATA, $modifiedColumns) && 
				($object->isCustomDataModified(null, 'mediaServers'))) {
				return true;
		}
		return false;
	}
}
