<?php

/**
 * Auto-generated class.
 * 
 * Used to search KalturaExternalMediaEntry attributes. Use KalturaExternalMediaEntryCompareAttribute enum to provide attribute name.
*/
class KalturaExternalMediaEntryCompareAttributeCondition extends KalturaSearchComparableAttributeCondition
{
	/**
	 * @var KalturaExternalMediaEntryCompareAttribute
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

