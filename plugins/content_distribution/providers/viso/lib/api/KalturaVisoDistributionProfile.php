<?php
/**
 * @package plugins.visoDistribution
 * @subpackage api.objects
 */
class KalturaVisoDistributionProfile extends KalturaConfigurableDistributionProfile
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
	
	/**
	 * @var string
	 */
	public $feedLink;
	
	/**
	 * @var string
	 */
	public $feedDescription;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'feedUrl',
		'feedTitle',
		'feedLink',
		'feedDescription',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
		
	public function toObject($object = null, $skip = array())
	{
		if(is_null($object))
			$object = new VisoDistributionProfile();
		
		return parent::toObject($object, $skip);
	}
}