<?php
/**
 * @package plugins.limeLight
 * @subpackage lib
 */
class kLimeLightLiveFlowManager implements kObjectCreatedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::objectCreated()
	 */
	public function objectCreated(BaseObject $object)
	{
		/* @var $object entry */
		$partner = $object->getPartner();
		$limeLightLiveParamsJSON = $partner->getLiveStreamProvisionParams();
		$limeLightLiveParams = json_decode($limeLightLiveParamsJSON);
		if ((!isset($limeLightLiveParams->Limelight))
			|| (!isset($limeLightLiveParams->Limelight->limelightPrimaryPublishUrl)) 
			|| (!isset($limeLightLiveParams->Limelight->limelightSecondaryPublishUrl))
			|| (!isset($limeLightLiveParams->Limelight->limelightStreamUrl))){
			$object->setStatus(entryStatus::ERROR_IMPORTING);
			$object->save();
			return true;
		}

		$object->setPrimaryBroadcastingUrl($limeLightLiveParams->Limelight->limelightPrimaryPublishUrl);
		$object->setSecondaryBroadcastingUrl($limeLightLiveParams->Limelight->limelightSecondaryPublishUrl);
		$object->setStreamUrl($limeLightLiveParams->Limelight->limelightStreamUrl);
		$object->setStreamName($object->getId().'_%i');
		$object->setStatus(entryStatus::READY);
		$object->save();
		
		return true;
	}

	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::shouldConsumeCreatedEvent()
	 */
	public function shouldConsumeCreatedEvent(BaseObject $object) 
	{
		if ($object instanceof entry && $object->getSource() == LimeLightPlugin::getEntrySourceTypeCoreValue(LimeLightLiveEntrySourceType::LIMELIGHT_LIVE))
		{
			return true;
		}
		
		return false;
		
	}

	
}