<?php
/**
 * Represents the current request country context as calculated based on the IP address
 * 
 * @package api
 * @subpackage objects
 */
class KalturaAnonymousIPContextField extends KalturaStringField
{
	/**
	 * The ip geo coder engine to be used
	 * 
	 * @var KalturaGeoCoderType
	 */
	public $geoCoderType = geoCoderType::KALTURA;
	
	static private $map_between_objects = array
	(
		'geoCoderType',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kAnonymousIPContextField();
			
		return parent::toObject($dbObject, $skip);
	}
}