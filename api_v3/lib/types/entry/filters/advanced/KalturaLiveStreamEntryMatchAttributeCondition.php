<?php

/**
 * Auto-generated class.
 * 
 * Used to search KalturaLiveStreamEntry attributes. Use KalturaLiveStreamEntryMatchAttribute enum to provide attribute name.
*/
class KalturaLiveStreamEntryMatchAttributeCondition extends KalturaSearchMatchAttributeCondition
{
	/**
	 * @var KalturaLiveStreamEntryMatchAttribute
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

