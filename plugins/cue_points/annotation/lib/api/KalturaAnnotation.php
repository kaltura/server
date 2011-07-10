<?php
/**
 * @package plugins.annotation
 * @subpackage api.objects
 */
class KalturaAnnotation extends KalturaCuePoint
{
	/**
	 * @var string
	 * @filter eq,in
	 * @insertonly
	 */
	public $parentId;
	
	/**
	 * @var string
	 */
	public $text;
	
	/**
	 * End time in milliseconds
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

	public function __construct()
	{
		$this->type = AnnotationPlugin::getApiValue(AnnotationCuePointType::ANNOTATION);
	}
	
	private static $map_between_objects = array
	(
		"parentId",
		"text",
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
			$object_to_fill = new Annotation();
			
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		parent::validateForInsert($propertiesToSkip);
		
		if($this->text != null)
			$this->validatePropertyMaxLength("text", CuePointPeer::MAX_TEXT_LENGTH);
			
		$this->validateEndTime();
		$this->validateParentId();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		if($this->parentId !== null)
			$this->validateParentId($sourceObject->getId());
			
		if($this->text !== null)
			$this->validatePropertyMaxLength("text", CuePointPeer::MAX_TEXT_LENGTH);
		
		if($this->endTime !== null)
			$this->validateEndTime($sourceObject->getId());
			
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
}
