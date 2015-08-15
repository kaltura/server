<?php

/**
 * Auto-generated class.
 * 
 * Used to search KalturaDocumentEntry attributes. Use KalturaDocumentEntryMatchAttribute enum to provide attribute name.
*/
class KalturaDocumentEntryMatchAttributeCondition extends KalturaSearchMatchAttributeCondition
{
	/**
	 * @var KalturaDocumentEntryMatchAttribute
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

