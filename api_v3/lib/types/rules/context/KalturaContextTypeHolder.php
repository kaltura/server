<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaContextTypeHolder extends KalturaObject
{
	/**
	 * The type of the condition context
	 * 
	 * @var KalturaContextType
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