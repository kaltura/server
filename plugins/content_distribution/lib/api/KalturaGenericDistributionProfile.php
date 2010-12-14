<?php
class KalturaGenericDistributionProfile extends KalturaDistributionProfile
{
	/**
	 * @var int
	 */
	public $genericProviderId;	
	
	/**
	 * @var KalturaDistributionProtocol
	 */
	public $protocol;
	
	/**
	 * @var string
	 */
	public $serverUrl;
	
	/**
	 * @var string
	 */
	public $serverPath;
	
	/**
	 * @var string
	 */
	public $username;
	
	/**
	 * @var string
	 */
	public $password;
	
	/**
	 * @var bool
	 */
	public $ftpPassiveMode;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'genericProviderId',	
		'protocol',
		'serverUrl',
		'serverPath',
		'username',
		'password',
		'ftpPassiveMode',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}