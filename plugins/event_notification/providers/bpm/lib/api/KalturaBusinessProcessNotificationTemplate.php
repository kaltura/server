<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage api.objects
 */
abstract class KalturaBusinessProcessNotificationTemplate extends KalturaEventNotificationTemplate
{	
	/**
	 * Define the integrated BPM server id
	 * @var int
	 * @requiresPermission update
	 */
	public $serverId;
	
	/**
	 * Define the integrated BPM process id
	 * @var string
	 * @requiresPermission update
	 */
	public $processId;
	
	/**
	 * Abort the process automatically if the triggering object deleted
	 * @var bool
	 * @requiresPermission update
	 */
	public $abortOnDeletion;
	
	/**
	 * Code to load the main triggering object
	 * @var string
	 * @requiresPermission update
	 */
	public $mainObjectCode;
	
	/**
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'serverId',
		'processId',
		'abortOnDeletion',
		'mainObjectCode',
	);
		 
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$propertiesToSkip[] = 'type';
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $propertiesToSkip = array())
	{
		if(is_null($dbObject))
			$dbObject = new BusinessProcessNotificationTemplate();
			
		return parent::toObject($dbObject, $propertiesToSkip);
	}
}