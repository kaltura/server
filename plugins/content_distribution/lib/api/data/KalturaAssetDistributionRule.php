<?php

/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 */
class KalturaAssetDistributionRule extends KalturaObject
{
	/**
	 * @var string
	 */
	public $validationError;

	/**
	 * Assets that should be submitted if ready
	 * @var KalturaAssetDistributionConditionsArray
	 */
	public $assetDistributionConditions;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)
	*/
	private static $map_between_objects = array
	(
		'validationError',
		'assetDistributionConditions',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kAssetDistributionRule();
			
		return parent::toObject($dbObject, $skip);
	}
}