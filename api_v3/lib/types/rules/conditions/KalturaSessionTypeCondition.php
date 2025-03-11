<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaSessionTypeCondition extends KalturaCondition
{
	/**
	 * The privelege needed to remove the restriction
	 * 
	 * @var KalturaSessionType
	 */
	public $sessionType;
	
	private static $mapBetweenObjects = array
	(
		'sessionType',
	);
	
	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = ConditionType::SESSION_TYPE;
	}
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(!$object_to_fill)
			$object_to_fill = new kSessionTypeCondition();
			
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
