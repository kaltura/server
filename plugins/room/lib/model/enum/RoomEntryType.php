<?php
/**
 * @package plugins.room
 * @subpackage model.enum
 */
class RoomEntryType implements IKalturaPluginEnum, entryType
{
	const ROOM = 'room';

	public static function getAdditionalValues()
	{
		return array(
			'ROOM' => self::ROOM,
		);
	}

	public static function getAdditionalDescriptions()
	{
		return array(
			RoomPlugin::getApiValue(self::ROOM) => 'Room',
		);
	}
}