<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaSearchCondition extends KalturaSearchItem
{
	/**
	 * @var string
	 */
	public $field;
	
	/**
	 * @var string
	 */
	public $value;
	
	private static $map_between_objects = array
	(
		"field",
		"value",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		KalturaLog::debug("To object: field [$this->field] value [$this->value]");
		
		if(is_null($object_to_fill))
			$object_to_fill = new AdvancedSearchFilterCondition();
			
		return parent::toObject($object_to_fill, $props_to_skip);		
	}
}
