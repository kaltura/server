<?php
/**
 * @package plugins.avnDistribution
 * @subpackage api.objects
 */
class KalturaAvnDistributionProfile extends KalturaConfigurableDistributionProfile
{	
	/**
	 * @readonly
	 * @var string
	 */
	public $feedUrl;
	
	/**
	 * @var string
	 */
	public $feedTitle;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'feedUrl',
		'feedTitle',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
		
	public function toObject($object = null, $skip = array())
	{
		if(is_null($object))
			$object = new AvnDistributionProfile();
		
		return parent::toObject($object, $skip);
	}
}