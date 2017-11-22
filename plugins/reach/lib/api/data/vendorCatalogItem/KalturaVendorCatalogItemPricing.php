<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorCatalogItemPricing extends KalturaObject
{
	/**
	 * @var int
	 */
	public $pricePerUnit;
	
	/**
	 * @var KalturaVendorCatalogItemPriceFunction
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
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new kVendorCatalogItemPricing();
		}
		
		return parent::toObject($dbObject, $propsToSkip);
	}
}