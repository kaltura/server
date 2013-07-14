<?php
/**
 * @package plugins.timeWarnerDistribution
 * @subpackage api.objects
 */
class KalturaTimeWarnerDistributionProfile extends KalturaConfigurableDistributionProfile
{	
	
	/**
	 * @readonly
	 * @var string
	 */
	public $feedUrl;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'feedUrl',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
		
	public function toObject($object = null, $skip = array())
	{
		/* @var $object TimeWarnerDistributionProfile */
		if(is_null($object))
			$object = new TimeWarnerDistributionProfile();
			
		$object = parent::toObject($object, $skip);
			
		return $object;
	}
}