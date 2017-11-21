<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaCatalogItemPricing extends KalturaObject
{
	
	/**
	 * @var int
	 */
	public $pricePerUnit;
	
	/**
	 * @var KalturaCatalogItemPriceFunction
	 */
	public $priceFunction;
	
	private static $map_between_objects = array
	(
		'pricePerUnit',
		'priceFunction',
	);
	
	/* (non-PHPdoc)
 	 * @see KalturaObject::getMapBetweenObjects()
 	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new kCatalogItemPricing();
		}
		
		return parent::toObject($dbObject, $propsToSkip);
	}
}
