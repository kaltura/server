<?php
/**
 * @package plugins.codeCuePoint
 * @subpackage api.objects
 */
class KalturaCodeCuePoint extends KalturaCuePoint
{
	/**
	 * @var string
	 */
	public $code;
	
	/**
	 * @var string 
	 */
	public $description;

	public function __construct()
	{
		$this->cuePointType = CodeCuePointPlugin::getApiValue(CodeCuePointType::CODE);
	}
	
	private static $map_between_objects = array
	(
		"code" => "name",
		"description" => "text",
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
	}
}
