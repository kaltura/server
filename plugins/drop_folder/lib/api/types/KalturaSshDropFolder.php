<?php

/**
 * @package plugins.dropFolder
 * @subpackage api.objects
 * @abstract
 */
class KalturaSshDropFolder extends KalturaDropFolder
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
	
    /**
	 * @var string
	 */
	public $privateKey;
	
    /**
	 * @var string
	 */
	public $publicKey;
	
	
	private static $map_between_objects = array(
		'host' => 'sshHost',
    	'port' => 'sshPort',
    	'username' => 'sshUsername',
    	'password' => 'sshPassword',
    	'privateKey' => 'sshPrivateKey',
    	'publicKey' => 'sshPublicKey',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	//TODO: add toInsertableObject & toUpdatableObject
	
	
}

