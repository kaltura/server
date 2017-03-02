<?php
/**
 * @package api
 * @subpackage objects
 * @abstract
 */
abstract class KalturaMatchCondition extends KalturaCondition
{
	/**
	 * @var KalturaStringValueArray
	 */
	public $values;
	
	/**
	 * @var bool
	 */
	public $restrictEmptyFieldValues;
	
	private static $mapBetweenObjects = array
	(
		'values',
		'restrictEmptyFieldValues'
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}
