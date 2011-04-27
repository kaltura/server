<?php
/**
 * A key value pair representation to return an array of key-value pairs (associative array)
 * 
 * @see KalturaKeyValueArray
 * @package api
 * @subpackage objects
 */
class KalturaKeyValue extends KalturaObject
{
	/**
	 * @var string
	 */
	public $key;
    
	/**
	 * @var string
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