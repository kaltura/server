<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaIndexAdvancedFilter extends KalturaSearchItem
{
	/**
	 * @var int
	 */
	public $indexIdGreaterThan;
	
	/**
	 * @var string
	 */
	public $idColumnName; 

	/**
	 * @var int
	 */
	public $depthGreaterThanEqual;

	private static $map_between_objects = array
	(
		"indexIdGreaterThan",
		"idColumnName",
		"depthGreaterThanEqual",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new kIndexAdvancedFilter();
			
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
