<?php
/**
 * @package plugins.freewheelDistribution
 * @subpackage api.objects
 */
class KalturaFreewheelDistributionProfile extends KalturaDistributionProfile
{
	/**
	 * @var string
	 */
	public $apikey;
	
	/**
	 * @var string
	 */
	public $email;
	
	/**
	 * @var string
	 */
	public $sftpPass;
	
	/**
	 * 
	 * @var string
	 */
	public $sftpLogin;
	
		/**
	 * @var string
	 */
	public $accountId;
	
	/**
	 * @var int
	 */
	public $metadataProfileId;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'apikey',
		'email',
		'metadataProfileId',
		'sftpPass',
		'sftpLogin'
	);
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}