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
		"type"
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function fromObject ( $source_object  )
	{
		parent::fromObject($source_object);
		
		$this->items = KalturaSearchItemArray::fromSearchItemArray($source_object->getItems());
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
//		KalturaLog::debug("To object: type [$this->type] items [" . count($this->items) . "]");
		if(!$object_to_fill)
			$object_to_fill = new AdvancedSearchFilterOperator();
			
		$object_to_fill = parent::toObject($object_to_fill, $props_to_skip);
			
		if($this->items)
			$object_to_fill->setItems($this->items->toObjectsArray());
		
		return $object_to_fill;		
	}
}
