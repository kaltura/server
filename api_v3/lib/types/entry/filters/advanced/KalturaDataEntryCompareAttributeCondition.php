<?php

/**
 * Auto-generated class.
 * 
 * Used to search KalturaDataEntry attributes. Use KalturaDataEntryCompareAttribute enum to provide attribute name.
*/
class KalturaDataEntryCompareAttributeCondition extends KalturaSearchComparableAttributeCondition
{
	/**
	 * @var KalturaDataEntryCompareAttribute
	 */
	public $attribute;

	private static $mapBetweenObjects = array
	(
		"attribute" => "attribute",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects() , self::$mapBetweenObjects);
	}

	protected function getIndexClass()
	{
		return 'entryIndex';
	}
}

