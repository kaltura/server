<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.objects
 */
class KalturaFtpDropFolder extends KalturaRemoteDropFolder
{

    /**
	 * @var string
	 */
	public $host;
	
	/**
	 * @var int
	 */
	public $port;
	
    /**
	 * @var string
	 */
	public $username;
	
    /**
	 * @var string
	 */
	public $password;
	
	
	private static $map_between_objects = array(
		'host' => 'ftpHost',
    	'port' => 'ftpPort',
    	'username' => 'ftpUsername',
    	'password' => 'ftpPassword',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new FtpDropFolder();
			
		parent::toObject($dbObject, $skip);
					
		return $dbObject;
	}
	
}

