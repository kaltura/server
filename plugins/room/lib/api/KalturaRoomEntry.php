<?php

/**
 * @package plugins.room
 * @subpackage api.objects
 */
class KalturaRoomEntry extends KalturaBaseEntry
{

	/**
	 * @filter eq
	 * @var KalturaRoomType
	 */
	public $roomType;

	private static $map_between_objects = array(
		'roomType',
	);

	public function __construct()
	{
			$this->type = RoomPlugin::getApiValue(RoomEntryType::ROOM);
	}

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new RoomEntry();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('roomType');

		return parent::validateForInsert($propertiesToSkip);
	}

}