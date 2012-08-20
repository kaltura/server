<?php
/**
 * @package plugins.attUverseDistribution
 * @subpackage api.objects
 */
class KalturaAttUverseDistributionProfile extends KalturaConfigurableDistributionProfile
{
	
	/**
	 * @readonly
	 * @var string
	 */
	public $feedUrl;
	
	/**
	 * @var string
	 */
	public $ftpHost;
	
	/**
	 * @var string
	 */
	public $ftpUsername;
	
	/**
	 * 
	 * @var string
	 */
	public $ftpPassword;
	
	/**
	 * @var string
	 */
	public $ftpPath;
	
	/**
	 * @var string
	 */			
	public $channelTitle;	
	
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'feedUrl',
		'ftpHost',
		'ftpUsername',
		'ftpPassword',
		'ftpPath',	
		'channelTitle',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}