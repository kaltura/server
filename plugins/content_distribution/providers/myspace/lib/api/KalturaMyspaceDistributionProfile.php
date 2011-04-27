<?php
/**
 * @package plugins.myspaceDistribution
 * @subpackage api.objects
 */
class KalturaMyspaceDistributionProfile extends KalturaDistributionProfile
{
	/**
	 * @var string
	 */
	public $username;
	
	/**
	 * @var string
	 */
	public $password;
	
	/**
	 * @var string
	 */
	public $domain;

	/**
	 * @var int
	 */
	public $metadataProfileId;

	/**
	 * @var int
	 */
	public $myspFlavorParamsId;
	
	/**
	 * @var string
	 */
	public $feedTitle;

	/**
	 * @var string
	 */
	public $feedDescription;

	/**
	 * @var string
	 */
	public $feedContact;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'username',
		'password',
		'domain',
		'myspFlavorParamsId',		
		'feedTitle',
		'feedDescription',
		'feedContact',		
		'metadataProfileId',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}