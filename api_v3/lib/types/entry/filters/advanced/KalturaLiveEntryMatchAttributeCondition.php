<?php

/**
 * Auto-generated class.
 * 
 * Used to search KalturaLiveEntry attributes. Use KalturaLiveEntryMatchAttribute enum to provide attribute name.
*/
class KalturaLiveEntryMatchAttributeCondition extends KalturaSearchMatchAttributeCondition
{
	/**
	 * @var KalturaLiveEntryMatchAttribute
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

