<?php
class KalturaGenericDistributionProfileAction extends KalturaObject
{
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
	
	/**
	 * @var string
	 */
	public $httpFieldName;
	
	/**
	 * @var string
	 */
	public $httpFileName;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'protocol',
		'serverUrl',
		'serverPath',
		'username',
		'password',
		'ftpPassiveMode',
		'httpFieldName',
		'httpFileName',
	);
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}