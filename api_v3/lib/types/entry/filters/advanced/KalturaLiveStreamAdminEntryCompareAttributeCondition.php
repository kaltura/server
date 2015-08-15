<?php

/**
 * Auto-generated class.
 * 
 * Used to search KalturaLiveStreamAdminEntry attributes. Use KalturaLiveStreamAdminEntryCompareAttribute enum to provide attribute name.
*/
class KalturaLiveStreamAdminEntryCompareAttributeCondition extends KalturaSearchComparableAttributeCondition
{
	/**
	 * @var KalturaLiveStreamAdminEntryCompareAttribute
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

