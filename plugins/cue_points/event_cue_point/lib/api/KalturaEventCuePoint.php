<?php
/**
 * @package plugins.eventCuePoint
 * @subpackage api.objects
 * @requiresPermission insert,update
 */
class KalturaEventCuePoint extends KalturaCuePoint
{
	/**
	 * @var KalturaEventType
	 * @filter eq,in
	 * @requiresPermission insert,update
	 */
	public $eventType;

	/**
	 * @var string
	 */
	public $data;

	
	public function __construct()
	{
		$this->cuePointType = EventCuePointPlugin::getApiValue(EventCuePointType::EVENT);
	}
	
	private static $map_between_objects = array
	(
		"eventType" => "subType",
		"data" => "text",
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
	
}
