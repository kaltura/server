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
	 * @var KalturaMatchConditionType
	 */
	public $matchType;
	
	private static $mapBetweenObjects = array
	(
		'values',
		'matchType',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}
