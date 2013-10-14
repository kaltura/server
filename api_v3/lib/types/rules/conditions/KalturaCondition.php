<?php
/**
 * @package api
 * @subpackage objects
 * @abstract
 */
abstract class KalturaCondition extends KalturaObject
{
	/**
	 * The type of the access control condition
	 * 
	 * @readonly
	 * @var KalturaConditionType
	 */
	public $type;
	
	/**
	 * @var string
	 */
	public $description;
	
	/**
	 * @var bool
	 */
	public $not;
	
	private static $mapBetweenObjects = array
	(
		'description',
		'not',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}