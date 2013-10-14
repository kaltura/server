<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAuthenticatedCondition extends KalturaCondition
{
	/**
	 * The privelege needed to remove the restriction
	 * 
	 * @var KalturaStringValueArray
	 */
	public $privileges;
	
	private static $mapBetweenObjects = array
	(
		'privileges',
	);
	
	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = ConditionType::AUTHENTICATED;
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
			$dbObject = new kAuthenticatedCondition();
			
		return parent::toObject($dbObject, $skip);
	}
}
