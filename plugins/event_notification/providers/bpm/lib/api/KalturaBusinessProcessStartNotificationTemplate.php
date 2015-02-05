<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage api.objects
 */
class KalturaBusinessProcessStartNotificationTemplate extends KalturaBusinessProcessNotificationTemplate
{	
	/**
	 * Abort the process automatically if the triggering object deleted
	 * @var bool
	 * @requiresPermission update
	 */
	public $abortOnDeletion;
	
	public function __construct()
	{
		$this->type = BusinessProcessNotificationPlugin::getApiValue(BusinessProcessNotificationTemplateType::BPM_START);
	}

	/**
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'abortOnDeletion',
	);
		 
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
			$dbObject = new BusinessProcessStartNotificationTemplate();
			
		return parent::toObject($dbObject, $propertiesToSkip);
	}
}