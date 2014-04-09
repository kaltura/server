<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaGeoDistanceCondition extends KalturaMatchCondition
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
		$this->type = ConditionType::GEO_DISTANCE;
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
			$dbObject = new kGeoDistanceCondition();
			
		return parent::toObject($dbObject, $skip);
	}
}
