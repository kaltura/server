<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAccessControlAddRequestHostPrefixAction extends KalturaRuleAction
{
	/**
	 * Request host prefix to add to player calls
	 * 
	 * @var string
	 */
	public $requestHostPrefix;
	
	private static $mapBetweenObjects = array
	(
		'requestHostPrefix',
	);
	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = RuleActionType::ADD_REQUEST_HOST_PREFIX;
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
			$dbObject = new kAccessControlAddRequestHostPrefixAction();
			
		return parent::toObject($dbObject, $skip);
	}
}