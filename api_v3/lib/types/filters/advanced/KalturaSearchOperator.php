<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaSearchOperator extends KalturaSearchItem
{
	/**
	 * @var KalturaSearchOperatorType
	 */
	public $type;
	
	/**
	 * @var KalturaSearchItemArray
	 */
	public $items;

	private static $map_between_objects = array
	(
		"type",
		"items",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new AdvancedSearchFilterOperator();
			
		$object_to_fill = parent::toObject($object_to_fill, $props_to_skip);
			
		if($this->items)
			$object_to_fill->setItems($this->items->toObjectsArray());
		
		return $object_to_fill;		
	}
}
