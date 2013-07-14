<?php
/**
 * @package plugins.uverseClickToOrderDistribution
 * @subpackage api.objects
 */
class KalturaUverseClickToOrderDistributionProfile extends KalturaConfigurableDistributionProfile
{	
	
	/**
	 * @readonly
	 * @var string
	 */
	public $feedUrl;
	
	/**
	 * @var string
	 */			
	public $backgroundImageWide;

	/**
	 * @var string
	 */			
	public $backgroundImageStandard;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'feedUrl',
		'backgroundImageWide',
		'backgroundImageStandard',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
		
	public function toObject($object = null, $skip = array())
	{
		/* @var $object UverseClickToOrderDistributionProfile */
		if(is_null($object))
			$object = new UverseClickToOrderDistributionProfile();
			
		$object = parent::toObject($object, $skip);
			
		return $object;
	}
}