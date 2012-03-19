<?php
/**
 * @package api
 * @subpackage objects
 * @abstract
 */
class KalturaCondition extends KalturaObject
{
	/**
	 * The type of the access control condition
	 * 
	 * @readonly
	 * @var KalturaConditionType
	 */
	public $type;
	
	/**
	 * @var bool
	 */
	public $not;
	
	private static $mapBetweenObjects = array
	(
		'not',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}