<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaHashCondition extends KalturaCondition
{
	/**
	 * hash name
	 * 
	 * @var string
	 */
	public $hashName;
	
	/**
	 * hash secret
	 * 
	 * @var string
	 */
	public $hashSecret;
	
	private static $mapBetweenObjects = array
	(
		'hashName',
		'hashSecret',
	);
	
	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = ConditionType::HASH;
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
			$dbObject = new kHashCondition();
			
		return parent::toObject($dbObject, $skip);
	}
}
