<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 */
class KalturaLinkedScheduleEvent extends KalturaObject
{
	/**
	 * The time between the end of the event which it's id is in $eventId and the start of the event holding this object
	 * @var int
	 */
	public $offset;
	
	/**
	 * The id of the event influencing the start of the event holding this object
	 * @var int
	 */
	public $eventId;
	
	private static $map_between_objects = array
	(
		'offset',
		'eventId'
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new kLinkedScheduleEvent();
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}
	
	public function setOffset(int $offset)
	{
		$this->offset = $offset;
	}
	
	public function setEventId(int $eventId)
	{
		$this->eventId = $eventId;
	}
	
	public function getOffset()
	{
		return $this->offset;
	}
	
	public function getEventId()
	{
		return $this->eventId;
	}

}