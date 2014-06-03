<?php


/**
 * @package plugins.eventCuePoint
 * @subpackage model
 */
class EventCuePoint extends CuePoint
{
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
	
}
