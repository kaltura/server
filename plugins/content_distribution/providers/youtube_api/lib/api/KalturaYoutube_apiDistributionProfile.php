<?php
/**
 * @package plugins.youtube_apiDistribution
 * @subpackage api.objects
 */
class KalturaYoutube_apiDistributionProfile extends KalturaDistributionProfile
{
	/**
	 * @var string
	 */
	public $user;
	
	/**
	 * @var string
	 */
	public $password;
		
	/**
	 * @var int
	 */
	public $metadataProfileId;
	
			
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'user',
		'password',
		'metadataProfileId'
	);
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}