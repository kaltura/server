<?php
/**
 * @package plugins.room
 */
class RoomPlugin extends KalturaPlugin implements IKalturaServices, IKalturaEnumerator, IKalturaObjectLoader, IKalturaSearchDataContributor
{

	const PLUGIN_NAME = 'room';
	const SEARCH_TEXT_SUFFIX = 'rend';

	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}

	public static function getServicesMap()
	{
		$map = array(
			'room' => 'RoomService',
		);

		return $map;
	}

	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}

	public static function getEntryTypeCoreValue($valueName)
	{
		return kPluginableEnumsManager::apiToCore('entryType', self::getApiValue($valueName));
	}

	public static function getEnums($baseEnumName = null)
	{
		if (is_null($baseEnumName)) // for install plugins
		{
			return array('RoomEntryType');
		}

		if ($baseEnumName == 'entryType')
		{
			return array('RoomEntryType');
		}

		return array();
	}

	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		$class = self::getObjectClass($baseClass, $enumValue);
		if ($class)
		{
			return new $class();
		}

		return null;
	}

	public static function getObjectClass($baseClass, $enumValue)
	{
		if ($baseClass == 'entry' && $enumValue == RoomPlugin::getEntryTypeCoreValue(RoomEntryType::ROOM))
		{
			return 'RoomEntry';
		}

		if ($baseClass == 'KalturaBaseEntry' && $enumValue == RoomPlugin::getEntryTypeCoreValue(RoomEntryType::ROOM))
		{
			return 'KalturaRoomEntry';
		}

		return null;
	}

	public static function getRoomTypeSearchData($partnerId, $roomType)
	{
		return self::getPluginName() . '_' . $partnerId . 'rty' . $roomType . self::SEARCH_TEXT_SUFFIX;
	}

	public static function getSearchData(BaseObject $object)
	{
		if ($object instanceof RoomEntry)
		{
			return array('plugins_data' => self::getRoomTypeSearchData($object->getPartnerId(), $object->getRoomType()));
		}

		return null;
	}

}