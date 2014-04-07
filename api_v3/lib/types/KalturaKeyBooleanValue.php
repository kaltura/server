<?php
/**
 * A key (boolean) value pair representation to return an array of key-(boolean)value pairs (associative array)
 * 
 * @see KalturaKeyBooleanValueArray
 * @package api
 * @subpackage objects
 */
class KalturaKeyBooleanValue extends KalturaObject
{
	/**
	 * @var string
	 */
	public $key;
    
	/**
	 * @var bool
	 */
	public $value;
    
	private static $mapBetweenObjects = array
	(
		"key", "value",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}