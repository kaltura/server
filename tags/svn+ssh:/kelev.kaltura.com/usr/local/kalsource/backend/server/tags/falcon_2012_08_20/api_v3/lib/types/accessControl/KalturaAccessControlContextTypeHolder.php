<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAccessControlContextTypeHolder extends KalturaObject
{
	/**
	 * The type of the access control condition context
	 * 
	 * @var KalturaAccessControlContextType
	 */
	public $type;
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		return $this->type;
	}
	
	private static $mapBetweenObjects = array
	(
		'type',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}