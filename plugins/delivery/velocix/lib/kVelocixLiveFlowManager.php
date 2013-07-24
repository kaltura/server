<?php
/**
 * @package plugins.velocix
 * @subpackage lib
 */
class kVelocixLiveFlowManager implements kObjectCreatedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::objectCreated()
	 */
	public function objectCreated(BaseObject $object)
	{
		/* @var $object entry */
		$partner = $object->getPartner();
		$velocixLiveParamsJSON = json_decode($partner->getLiveStreamProvisionParams());
		if ((!isset($velocixLiveParamsJSON->velocix))
			|| (!isset($velocixLiveParamsJSON->velocix->userName)) 
			|| (!isset($velocixLiveParamsJSON->velocix->password))){
			$object->setStatus(entryStatus::ERROR_IMPORTING);
			$object->save();
			return true;
		}
		if (isset($velocixLiveParamsJSON->velocix->streamNamePrefix))
			$object->setStreamName($velocixLiveParamsJSON->velocix->streamNamePrefix.'_'.$object->getId());
		else
			$object->setStreamName($object->getId());
		$object->save();
		return true;
	}

	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::shouldConsumeCreatedEvent()
	 */
	public function shouldConsumeCreatedEvent(BaseObject $object) 
	{
		if ($object instanceof entry && $object->getSource() == VelocixPlugin::getEntrySourceTypeCoreValue(VelocixLiveEntrySourceType::VELOCIX_LIVE))
		{
			return true;
		}
		return false;
	}
}