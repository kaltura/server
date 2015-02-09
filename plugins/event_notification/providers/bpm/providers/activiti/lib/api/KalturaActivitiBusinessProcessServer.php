<?php
/**
 * @package plugins.activitiBusinessProcessNotification
 * @subpackage api.objects
 */
class KalturaActivitiBusinessProcessServer extends KalturaBusinessProcessServer
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
	 * @var KalturaActivitiBusinessProcessServerProtocol
	 */
	public $protocol;

	/**
	 * @var string
	 */
	public $username;

	/**
	 * @var string
	 */
	public $password;
	
	/**
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'host',
		'port',
		'protocol',
		'username',
		'password',
	);
		 
	public function __construct()
	{
		$this->type = ActivitiBusinessProcessNotificationPlugin::getApiValue(ActivitiBusinessProcessProvider::ACTIVITI);
	}
		 
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $propertiesToSkip = array())
	{
		if(is_null($dbObject))
			$dbObject = new ActivitiBusinessProcessServer();
			
		return parent::toObject($dbObject, $propertiesToSkip);
	}
}