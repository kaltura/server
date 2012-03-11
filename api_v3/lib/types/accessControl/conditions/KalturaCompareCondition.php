<?php
/**
 * @package api
 * @subpackage objects
 * @abstract
 */
class KalturaCompareCondition extends KalturaCondition
{
	/**
	 * Value to evaluate against the field and operator
	 * @var KalturaIntegerValue
	 */
	public $value;
	
	/**
	 * Comparing operator
	 * @var KalturaSearchConditionComparison
	 */
	public $comparison;
	
	private static $mapBetweenObjects = array
	(
		'value',
		'comparison',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}
