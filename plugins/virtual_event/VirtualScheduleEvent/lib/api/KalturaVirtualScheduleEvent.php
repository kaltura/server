<?php
/**
 * @package plugins.virtualEvent
 * @subpackage api.objects
 * @abstract
 * @relatedService ScheduleEventService
 */

class KalturaVirtualScheduleEvent extends KalturaScheduleEvent
{
	const MAX_DURATION_YEARS = 5;
	
	/**
	 * The ID of the virtual event connected to this Schedule Event
	 * @var int
	 */
	public $virtualEventId;
	
	/**
	 * The type of the Virtual Schedule Event
	 * @var KalturaVirtualScheduleEventSubType
	 * @insertonly
	 */
	public $virtualScheduleEventSubType;
	
	private static $map_between_objects = array
	(
		'virtualEventId',
		'virtualScheduleEventSubType',
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
			$sourceObject = new VirtualScheduleEvent();
		}
		
		return parent ::toObject($sourceObject, $propertiesToSkip);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see KalturaScheduleEvent::getScheduleEventType()
	 */
	public function getScheduleEventType ()
	{
		return VirtualScheduleEventType::VIRTUAL;
	}
	
	protected function getScheduleEventMaxDuration()
	{
		return self::MAX_DURATION_YEARS * kTimeConversion::YEARS;
	}
	
	protected function getSingleScheduleEventMaxDuration()
	{
		return $this->getScheduleEventMaxDuration();
	}
	
	public function toUpdatableObject ( $object_to_fill , $props_to_skip = array() )
	{
		$this->validateEventExists();
		return parent::toUpdatableObject($object_to_fill, $props_to_skip);
	}

	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		$this->validateEventExists();
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}

	protected function validateEventExists()
	{
		$dbVirtualEvent = VirtualEventPeer::retrieveByPK($this->virtualEventId);
		if(!$dbVirtualEvent)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $this->virtualEventId);
		}
	}
}
