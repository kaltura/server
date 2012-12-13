<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 * @abstract
 */
abstract class KalturaConfigurableDistributionProfile extends KalturaDistributionProfile
{

	/**
	 * @var KalturaDistributionFieldConfigArray
	 */
	public $fieldConfigArray;
	
	/**
	 * @var KalturaExtendingItemMrssParameterArray
	 */
	public $itemXpathsToExtend;
	
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	 (
		'fieldConfigArray',
	 	'itemXpathsToExtend',
	 );
	 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}	
}