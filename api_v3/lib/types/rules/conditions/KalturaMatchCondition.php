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
	public $matchAll;
	
	private static $mapBetweenObjects = array
	(
		'values',
		'matchAll',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}
