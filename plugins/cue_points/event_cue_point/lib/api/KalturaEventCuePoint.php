<?php
/**
 * @package plugins.eventCuePoint
 * @subpackage api.objects
 */
class KalturaEventCuePoint extends KalturaCuePoint
{
	/**
	 * @var KalturaEventType 
	 * @requiresPermission insert,update
	 */
	public $eventType;
	
	/**
	 * @var int 
	 * @filter gte,lte,order
	 * @requiresPermission insert,update
	 */
	public $endTime;
	
	public function __construct()
	{
		$this->cuePointType = EventCuePointPlugin::getApiValue(EventCuePointType::EVENT);
	}
	
	private static $map_between_objects = array
	(
		"eventType",
		"endTime",
	);
	
	/* (non-PHPdoc)
	 * @see KalturaCuePoint::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toInsertableObject()
	 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill))
			$object_to_fill = new EventCuePoint();
			
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		parent::validateForInsert($propertiesToSkip);
		
		if(!is_null($this->endTime))
			$this->validateEndTime();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		if(!is_null($this->endTime))
			$this->validateEndTime($sourceObject->getId());
			
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
}
