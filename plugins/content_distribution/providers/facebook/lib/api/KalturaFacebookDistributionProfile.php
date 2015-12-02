<?php
/**
 * @package plugins.facebookDistribution
 * @subpackage api.objects
 */
class KalturaFacebookDistributionProfile extends KalturaConfigurableDistributionProfile
{
	/**
	 * @var string
	 */
	public $pageAccessToken;

	/**
	 * 
	 * @var string
	 */
	public $userAccessToken;
		
	/**
	 * 
	 * @var string
	 */
	public $permissions;
	
	/**
	 * 
	 * @var string
	 */
	public $pageId;
	
	/**
	 * 
	 * @var int
	 */
	public $reRequestPermissions;
	
	/**
	 * @var string
	 */
	public $apiAuthorizeUrl;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
        'apiAuthorizeUrl',
        'pageId',

	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}