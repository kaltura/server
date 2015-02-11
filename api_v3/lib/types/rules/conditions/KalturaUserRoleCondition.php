<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUserRoleCondition extends KalturaCondition
{
	/**
	 * Comma separated list of role ids
	 * 
	 * @var string
	 */
	public $roleIds;
	
	private static $mapBetweenObjects = array
	(
		'roleIds',
	);
	
	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = ConditionType::USER_ROLE;
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
			$dbObject = new kUserRoleCondition();
			
		return parent::toObject($dbObject, $skip);
	}

	public function fromObject($srcObj, KalturaResponseProfileBase $responseProfile = null)
	{
		/** @var $srcObj kUserRoleCondition */
		parent::fromObject($srcObj, $responseProfile);
	}
}
