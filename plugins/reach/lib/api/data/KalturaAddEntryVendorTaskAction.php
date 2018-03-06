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
	 * @var int
	 */
	public $catalogItemId;

	private static $mapBetweenObjects = array
	(
		'catalogItemId',
	);

	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = ReachRuleActionType::ADD_ENTRY_VENDOR_TASK;
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
