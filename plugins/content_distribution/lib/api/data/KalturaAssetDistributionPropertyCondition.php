<?php

/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 */
class KalturaAssetDistributionPropertyCondition extends KalturaAssetDistributionCondition
{
	/**
	 * @var string
	 */
	public $propertyName;
	
	/**
	 * @var string
	 */
	public $propertyValue;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)
	*/
	private static $map_between_objects = array
	(
		'propertyName',
		'propertyValue',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kAssetDistributionPropertyCondition();
			
		return parent::toObject($dbObject, $skip);
	}
}