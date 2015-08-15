<?php

/**
 * Auto-generated class.
 * 
 * Used to search KalturaPlayableEntry attributes. Use KalturaPlayableEntryMatchAttribute enum to provide attribute name.
*/
class KalturaPlayableEntryMatchAttributeCondition extends KalturaSearchMatchAttributeCondition
{
	/**
	 * @var KalturaPlayableEntryMatchAttribute
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

