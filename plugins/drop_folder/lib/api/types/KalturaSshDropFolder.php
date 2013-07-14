<?php

/**
 * @package plugins.dropFolder
 * @subpackage api.objects
 * @abstract
 */
abstract class KalturaSshDropFolder extends KalturaRemoteDropFolder
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
	
	/**
	 * @var string
	 */
	public $passPhrase;
	
	
	private static $map_between_objects = array(
		'host' => 'sshHost',
    	'port' => 'sshPort',
    	'username' => 'sshUsername',
    	'password' => 'sshPassword',
    	'privateKey' => 'sshPrivateKey',
    	'publicKey' => 'sshPublicKey',
		'passPhrase' => 'sshPassPhrase',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
}

