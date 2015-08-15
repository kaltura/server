<?php

/**
 * Auto-generated class.
 * 
 * Used to search KalturaPlaylist attributes. Use KalturaPlaylistCompareAttribute enum to provide attribute name.
*/
class KalturaPlaylistCompareAttributeCondition extends KalturaSearchComparableAttributeCondition
{
	/**
	 * @var KalturaPlaylistCompareAttribute
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

