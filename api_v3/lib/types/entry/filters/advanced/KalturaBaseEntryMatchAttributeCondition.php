<?php

/**
 * Auto-generated class.
 * 
 * Used to search KalturaBaseEntry attributes. Use KalturaBaseEntryMatchAttribute enum to provide attribute name.
*/
class KalturaBaseEntryMatchAttributeCondition extends KalturaSearchMatchAttributeCondition
{
	/**
	 * @var KalturaBaseEntryMatchAttribute
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

