<?php
/**
 * Represents the current time context on Kaltura servers
 * 
 * @package api
 * @subpackage objects
 */
class KalturaTimeContextField extends KalturaIntegerField
{
	/**
	 * Time offset in seconds since current time
	 * @var int
	 */
	public $offset;

	static private $map_between_objects = array
	(
		'offset',
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
			$dbObject = new kTimeContextField();
			
		return parent::toObject($dbObject, $skip);
	}
}