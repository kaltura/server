<?php


/**
 * @package plugins.eventCuePoint
 * @subpackage model
 */
class EventCuePoint extends CuePoint
{
	const CUSTOM_DATA_FIELD_EVENT_TYPE = 'eventType';

	public function __construct() 
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or equivalent initialization method).
	 * @see __construct()
	 */
	public function applyDefaultValues()
	{
		$this->setType(EventCuePointPlugin::getCuePointTypeCoreValue(EventCuePointType::EVENT));
	}
	
	public function getEventType()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_EVENT_TYPE);}	

	public function setEventType($v)		{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_EVENT_TYPE, (int)$v);}
	
}
