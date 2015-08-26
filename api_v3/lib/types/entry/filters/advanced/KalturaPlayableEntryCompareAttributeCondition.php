<?php

/**
 * Auto-generated class.
 * 
 * Used to search KalturaPlayableEntry attributes. Use KalturaPlayableEntryCompareAttribute enum to provide attribute name.
*/
class KalturaPlayableEntryCompareAttributeCondition extends KalturaSearchComparableAttributeCondition
{
	/**
	 * @var KalturaPlayableEntryCompareAttribute
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

