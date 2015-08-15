<?php

/**
 * Auto-generated class.
 * 
 * Used to search KalturaLiveChannel attributes. Use KalturaLiveChannelMatchAttribute enum to provide attribute name.
*/
class KalturaLiveChannelMatchAttributeCondition extends KalturaSearchMatchAttributeCondition
{
	/**
	 * @var KalturaLiveChannelMatchAttribute
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

