<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaCategoryEntryAdvancedFilter extends KalturaSearchOperator
{
	/**
	 * @var string
	 */
	public $categoriesMatchOr;
	
	/**
	 * @var string
	 */
	public $categoryEntryStatusIn;
	
	private static $map_between_objects = array
	(
		"categoriesMatchOr",
		"categoryEntryStatusIn",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new kCategoryEntryAdvancedFilter();
			
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
