<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaSearchMatchCondition extends KalturaSearchCondition
{
	/**
	 * @var bool
	 */
	public $not;
	
	private static $map_between_objects = array
	(
		"not",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new AdvancedSearchFilterMatchCondition();
			
		return parent::toObject($object_to_fill, $props_to_skip);		
	}
}
