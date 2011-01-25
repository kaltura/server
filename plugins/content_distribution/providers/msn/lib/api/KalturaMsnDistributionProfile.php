<?php
/**
 * @package plugins.msnDistribution
 * @subpackage api.objects
 */
class KalturaMsnDistributionProfile extends KalturaDistributionProfile
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
	 * @var string
	 */
	public $csId;
	
	/**
	 * @var string
	 */
	public $source;
	
	/**
	 * @var int
	 */
	public $metadataProfileId;
	
	/**
	 * @var int
	 */
	public $movFlavorParamsId;
	
	/**
	 * @var int
	 */
	public $flvFlavorParamsId;
	
	/**
	 * @var int
	 */
	public $wmvFlavorParamsId;
		
			
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'username',
		'password',
		'domain',
		'csId',
		'source',
		'metadataProfileId',
		'movFlavorParamsId',
		'flvFlavorParamsId',
		'wmvFlavorParamsId',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}