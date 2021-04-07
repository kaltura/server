<?php

/**
 * @package plugins.schedule
 * @subpackage api.objects
 */

class KalturaMeetingScheduleEvent extends KalturaEntryScheduleEvent
{
	/**
	 * The time relative time before the startTime considered as preStart time
	 * @var int
	 */
	public $preStartTime;
	
	private static $map_between_objects = array
	(
		'preStartTime',
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject ($sourceObject = null, $propertiesToSkip = array())
	{
		if (is_null($sourceObject))
		{
			$sourceObject = new MeetingScheduleEvent();
		}
		
		return parent ::toObject($sourceObject, $propertiesToSkip);
	}
	
	/**
	 * {@inheritDoc}
	 * @see KalturaScheduleEvent::getScheduleEventType()
	 */
	public function getScheduleEventType ()
	{
		return ScheduleEventType::MEETING;
	}
}