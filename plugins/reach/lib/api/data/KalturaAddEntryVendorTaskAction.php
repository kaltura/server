<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaAddEntryVendorTaskAction extends KalturaRuleAction
{
	/**
	 * Catalog Item Id
	 * 
	 * @var string
	 */
	public $catalogItemIds;


	/**
	 * Boolean Event Notification Id
	 *
	 * @var string
	 */
	public $booleanEventNotificationIds;

	private static $mapBetweenObjects = array
	(
		'catalogItemIds',
		'booleanEventNotificationIds',
	);

	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = ReachPlugin::getApiValue(ReachRuleActionType::ADD_ENTRY_VENDOR_TASK);
	}
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kAddEntryVendorTaskAction();
			
		return parent::toObject($dbObject, $skip);
	}
}
