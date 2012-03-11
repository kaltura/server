<?php
/**
 * @package api
 * @subpackage objects
 * @abstract
 */
class KalturaMatchCondition extends KalturaCondition
{
	/**
	 * @var KalturaStringValueArray
	 */
	public $values;
	
	private static $mapBetweenObjects = array
	(
		'values',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}
