<?php
/**
 * @package plugins.doubleClickDistribution
 * @subpackage api.objects
 */
class KalturaDoubleClickDistributionProfile extends KalturaConfigurableDistributionProfile
{
	/**
	 * @var string
	 */
	public $channelTitle;
	
	/**
	 * @var string
	 */
	public $channelLink;
	
	/**
	 * @var string
	 */
	public $channelDescription;
	
	/**
	 * @readonly
	 * @var string
	 */
	public $feedUrl;
	
	/**
	 * @var string
	 */
	public $cuePointsProvider;
	
	/**
	 * @var string
	 */
	public $itemsPerPage;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'channelTitle',
		'channelLink',
		'channelDescription',
		'feedUrl',
		'cuePointsProvider',
		'itemsPerPage',
	);
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}