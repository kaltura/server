<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAccessControlLimitDeliveryProfilesAction extends KalturaRuleAction
{
	/**
	 * Comma separated list of delivery profile ids 
	 * 
	 * @var string
	 */
	public $deliveryProfileIds;
	
	/**
	 * @var bool
	 */
	public $isBlockedList;
	
	private static $mapBetweenObjects = array
	(
		'deliveryProfileIds',
		'isBlockedList',
	);

	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = RuleActionType::LIMIT_DELIVERY_PROFILES;
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
			$dbObject = new kAccessControlLimitDeliveryProfilesAction();
			
		return parent::toObject($dbObject, $skip);
	}
}