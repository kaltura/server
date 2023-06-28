<?php

/**
 * @package plugins.sessionCuePoint
 * @subpackage api.objects
 */
class KalturaSessionCuePoint extends KalturaCuePoint
{
	/**
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 */
	public $name;
	
	/**
	 * @var int
	 * @filter gte,lte,order
	 */
	public $endTime;
	
	/**
	 * Duration in milliseconds
	 * @var int
	 * @filter gte,lte,order
	 * @readonly
	 */
	public $duration;
	
	/**
	 * @var string
	 */
	public $sessionOwner;
	
	public function __construct()
	{
		$this->cuePointType = SessionCuePointPlugin::getApiValue(SessionCuePointType::SESSION);
	}
	
	private static $map_between_objects = array
	(
		"name",
		"endTime",
		"duration",
		"sessionOwner",
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
		if (is_null($object_to_fill))
		{
			$object_to_fill = new SessionCuePoint();
		}
		
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		parent::validateForInsert($propertiesToSkip);
		
		$this->validateEndTimeAndDuration($this->endTime, $this->duration);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validateEndTimeAndDuration($this->endTime, $this->duration, $sourceObject);
		
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
	
	public function updateEndTimeAndDuration($cuePoint)
	{
		if ($this->isNull('endTime') && (!$cuePoint || is_null($cuePoint->getEndTime())))
		{
			$this->endTime = $this->startTime;
		}
		if ($this->triggeredAt && $this->isNull('duration') && (!$cuePoint || is_null($cuePoint->getDuration())))
		{
			$this->duration = 0;
		}
	}
}
