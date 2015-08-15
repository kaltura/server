<?php

/**
 * Auto-generated class.
 * 
 * Used to search KalturaPlaylist attributes. Use KalturaPlaylistMatchAttribute enum to provide attribute name.
*/
class KalturaPlaylistMatchAttributeCondition extends KalturaSearchMatchAttributeCondition
{
	/**
	 * @var KalturaPlaylistMatchAttribute
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

