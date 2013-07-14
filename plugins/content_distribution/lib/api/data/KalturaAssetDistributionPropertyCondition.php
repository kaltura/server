<?php

/**
 * Defines the condition to match a property and value on core asset object (or one if its inherited objects)
 *
 * @package plugins.contentDistribution
 * @subpackage api.objects
 */
class KalturaAssetDistributionPropertyCondition extends KalturaAssetDistributionCondition
{
	/**
	 * The property name to look for, this will match to a getter on the asset object.
	 * Should be camelCase naming convention (defining "myPropertyName" will look for getMyPropertyName())
	 *
	 * @var string
	 */
	public $propertyName;
	
	/**
	 * The value to compare
	 *
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