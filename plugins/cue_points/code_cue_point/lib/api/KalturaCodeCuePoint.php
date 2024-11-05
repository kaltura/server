<?php
/**
 * @package plugins.codeCuePoint
 * @subpackage api.objects
 */
class KalturaCodeCuePoint extends KalturaCuePoint
{
	/**
	 * @var string
	 * @filter like,mlikeor,mlikeand,eq,in
	 */
	public $code;
	
	/**
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 */
	public $description;
	
	/**
	 * @var int 
	 * @filter gte,lte,order
	 * @requiresPermission insert,update
	 */
	public $endTime;
	
	/**
	 * Duration in milliseconds
	 * @var int 
	 * @filter gte,lte,order
	 * @readonly
	 */
	public $duration;

	public function __construct()
	{
		$this->cuePointType = CodeCuePointPlugin::getApiValue(CodeCuePointType::CODE);
	}
	
	private static $map_between_objects = array
	(
		"code" => "name",
		"description" => "text",
		"endTime",
		"duration",
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
			$object_to_fill = new CodeCuePoint();
			
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		parent::validateForInsert($propertiesToSkip);
		
		$this->validatePropertyNotNull("code");
			
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
