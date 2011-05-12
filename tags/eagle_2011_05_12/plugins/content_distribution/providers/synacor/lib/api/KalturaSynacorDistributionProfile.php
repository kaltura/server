<?php
/**
 * @package plugins.synacorDistribution
 * @subpackage api.objects
 */
class KalturaSynacorDistributionProfile extends KalturaDistributionProfile
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
	 * @var string
	 */
	public $domain;
	
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
		'domain',
		'metadataProfileId'
	);
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}