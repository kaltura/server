<?php
/**
 * A representation to return an array of values
 * 
 * @package api
 * @subpackage objects
 * @abstract
 */
abstract class KalturaValue extends KalturaObject
{
	/**
	 * @var string
	 */
    public $description;
    
	private static $mapBetweenObjects = array
	(
		"value",
		"description",
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}