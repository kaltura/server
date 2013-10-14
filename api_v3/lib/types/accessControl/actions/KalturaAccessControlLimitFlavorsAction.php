<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAccessControlLimitFlavorsAction extends KalturaRuleAction
{
	/**
	 * Comma separated list of flavor ids 
	 * 
	 * @var string
	 */
	public $flavorParamsIds;
	
	/**
	 * @var bool
	 */
	public $isBlockedList;
	
	private static $mapBetweenObjects = array
	(
		'flavorParamsIds',
		'isBlockedList',
	);

	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = RuleActionType::LIMIT_FLAVORS;
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
			$dbObject = new kAccessControlLimitFlavorsAction();
			
		return parent::toObject($dbObject, $skip);
	}
}