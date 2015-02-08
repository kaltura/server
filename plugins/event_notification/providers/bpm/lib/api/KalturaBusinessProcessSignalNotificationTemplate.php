<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage api.objects
 */
class KalturaBusinessProcessSignalNotificationTemplate extends KalturaBusinessProcessNotificationTemplate
{	
	/**
	 * Define the message to be sent
	 * @var string
	 * @requiresPermission update
	 */
	public $message;
	
	/**
	 * Define the event that waiting to the signal
	 * @var string
	 * @requiresPermission update
	 */
	public $eventId;
	
	/**
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'message',
		'eventId',
	);
		 
	public function __construct()
	{
		$this->type = BusinessProcessNotificationPlugin::getApiValue(BusinessProcessNotificationTemplateType::BPM_SIGNAL);
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
			$dbObject = new BusinessProcessSignalNotificationTemplate();
			
		return parent::toObject($dbObject, $propertiesToSkip);
	}
}