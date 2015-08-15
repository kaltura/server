<?php

/**
 * Auto-generated class.
 * 
 * Used to search KalturaDocumentEntry attributes. Use KalturaDocumentEntryCompareAttribute enum to provide attribute name.
*/
class KalturaDocumentEntryCompareAttributeCondition extends KalturaSearchComparableAttributeCondition
{
	/**
	 * @var KalturaDocumentEntryCompareAttribute
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

