<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCountryCondition extends KalturaMatchCondition
{
	/**
	 * The ip geo coder engine to be used
	 * 
	 * @var KalturaGeoCoderType
	 */
	public $geoCoderType;

	private static $mapBetweenObjects = array
	(
		'geoCoderType',
	);

	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = ConditionType::COUNTRY;
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
			$dbObject = new kCountryCondition();
			
		return parent::toObject($dbObject, $skip);
	}
}
