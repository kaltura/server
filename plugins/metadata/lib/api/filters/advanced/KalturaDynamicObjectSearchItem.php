<?php

/**
 * @package plugins.metadata
 * @subpackage api.filters
 */
class KalturaDynamicObjectSearchItem extends KalturaSearchOperator
{
	/**
	 * @var string
	 */
	public $field;

	private static $mapBetweenObjects = array
	(
		"field",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}

	public function toObject($objectToFill = null, $propsToSkip = array())
	{
		if (!$objectToFill)
			$objectToFill = new DynamicObjectSearchFilter();

		return parent::toObject($objectToFill, $propsToSkip);
	}
}