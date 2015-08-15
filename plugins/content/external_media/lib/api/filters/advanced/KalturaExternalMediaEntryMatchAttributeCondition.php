<?php

/**
 * Auto-generated class.
 * 
 * Used to search KalturaExternalMediaEntry attributes. Use KalturaExternalMediaEntryMatchAttribute enum to provide attribute name.
*/
class KalturaExternalMediaEntryMatchAttributeCondition extends KalturaSearchMatchAttributeCondition
{
	/**
	 * @var KalturaExternalMediaEntryMatchAttribute
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

