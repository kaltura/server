<?php

/**
 * Auto-generated class.
 * 
 * Used to search KalturaLiveEntry attributes. Use KalturaLiveEntryCompareAttribute enum to provide attribute name.
*/
class KalturaLiveEntryCompareAttributeCondition extends KalturaSearchComparableAttributeCondition
{
	/**
	 * @var KalturaLiveEntryCompareAttribute
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

