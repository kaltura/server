<?php

/**
 * Auto-generated class.
 * 
 * Used to search KalturaLiveStreamEntry attributes. Use KalturaLiveStreamEntryCompareAttribute enum to provide attribute name.
*/
class KalturaLiveStreamEntryCompareAttributeCondition extends KalturaSearchComparableAttributeCondition
{
	/**
	 * @var KalturaLiveStreamEntryCompareAttribute
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

