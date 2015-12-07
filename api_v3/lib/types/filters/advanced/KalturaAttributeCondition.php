<?php

/**
 * @package api
 * @subpackage filters
 */
abstract class KalturaAttributeCondition extends KalturaSearchItem
{
	/**
	 * @var string
	 */
	public $value;

	private static $mapBetweenObjects = array
	(
		'value',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}

	protected abstract function getIndexClass();

	public function toObject($objectToFill = null, $propsToSkip = array())
	{
		if (is_null($objectToFill))
			$objectToFill = new AdvancedSearchFilterAttributeCondition();

		return parent::toObject($objectToFill, $propsToSkip);
	}
}
