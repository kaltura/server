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
		if((count($currentMediaServers) == 1) && (count(array_intersect($updatedServers, $currentMediaServers)) == 1)) {
			$this->addEventCuePoint($object, EventType::BROADCAST_START);
			if(is_null($object->getFirstBroadcastTime())) {
				$object->setFirstBroadcastTime(time());
				$object->save();
			}
		}
		
		// If currently no one is set
		if(count($currentMediaServers) == 0) {
			$this->addEventCuePoint($object, EventType::BROADCAST_END);
		}
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
		if(($object instanceof LiveEntry) && 
				in_array(entryPeer::CUSTOM_DATA, $modifiedColumns) && 
				($object->isCustomDataModified(null, 'mediaServers'))) {
				return true;
		}
		
		return false;
	}
}
