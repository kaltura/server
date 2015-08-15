<?php

/**
 * Auto-generated class.
 * 
 * Used to search KalturaMixEntry attributes. Use KalturaMixEntryMatchAttribute enum to provide attribute name.
*/
class KalturaMixEntryMatchAttributeCondition extends KalturaSearchMatchAttributeCondition
{
	/**
	 * @var KalturaMixEntryMatchAttribute
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

