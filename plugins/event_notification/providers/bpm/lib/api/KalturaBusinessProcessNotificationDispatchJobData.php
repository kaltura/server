<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage api.objects
 */
class KalturaBusinessProcessNotificationDispatchJobData extends KalturaEventNotificationDispatchJobData
{
	/**
	 * @var KalturaBusinessProcessServer
	 */
	public $server;
	
	/**
	 * @var string
	 */
	public $caseId;
	
	private static $map_between_objects = array
	(
		'caseId',
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function fromObject($dbObject)
	{
		/* @var $dbObject kBusinessProcessNotificationDispatchJobData */
		parent::fromObject($dbObject);
		
		$server = $dbObject->getServer();
		$this->server = KalturaBusinessProcessServer::getInstanceByType($server->getType());
		$this->server->fromObject($server);
	}
}
