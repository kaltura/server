<?php
/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaCatalogItemAdvancedFilter extends KalturaSearchItem
{
	/**
	 * @var KalturaVendorServiceType
	 */
	public $serviceTypeEqual;
	
	/**
	 * @var string
	 */
	public $serviceTypeIn;
	
	/**
	 * @var KalturaVendorServiceFeature
	 */
	public $serviceFeatureEqual;
	
	/**
	 * @var string
	 */
	public $serviceFeatureIn;
	
	/**
	 * @var KalturaVendorServiceTurnAroundTime
	 */
	public $turnAroundTimeEqual;
	
	/**
	 * @var string
	 */
	public $turnAroundTimeIn;
	
	/**
	 * @var KalturaCatalogItemLanguage
	 */
	public $sourceLanguageEqual;
	
	
	private static $map_between_objects = array
	(
		'serviceTypeEqual',
		'serviceTypeIn',
		'serviceFeatureEqual',
		'serviceFeatureIn',
		'turnAroundTimeEqual',
		'turnAroundTimeIn',
		'sourceLanguageEqual',
	);
	
	/* (non-PHPdoc)
 	 * @see KalturaCuePoint::getMapBetweenObjects()
 	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new kCatalogItemAdvancedFilter();
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}