<?php

/**
 * Auto-generated class.
 * 
 * Used to search KalturaBaseEntry attributes. Use KalturaBaseEntryCompareAttribute enum to provide attribute name.
*/
class KalturaBaseEntryCompareAttributeCondition extends KalturaSearchComparableAttributeCondition
{
	/**
	 * @var KalturaBaseEntryCompareAttribute
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

