<?php

/**
 * Auto-generated class.
 * 
 * Used to search KalturaLiveChannel attributes. Use KalturaLiveChannelCompareAttribute enum to provide attribute name.
*/
class KalturaLiveChannelCompareAttributeCondition extends KalturaSearchComparableAttributeCondition
{
	/**
	 * @var KalturaLiveChannelCompareAttribute
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

