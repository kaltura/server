<?php
/**
 * Plugin enabling the storage of entries that relates to a user attending a session
 * @package plugins.rsvp
 */

class RsvpPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaObjectLoader
{
	const PLUGIN_NAME = 'rsvp';

	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}

	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId)
	{
		return true;
	}

	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if (is_null($baseEnumName))
		{
			return array('RsvpUserEntryType');
		}
		if ($baseEnumName === 'UserEntryType')
		{
			return array('RsvpUserEntryType');
		}
		return array();
	}

	public static function getRsvpUserEntryTypeCoreValue($valueName)
	{
		$value = self::getApiValue($valueName);
		return kPluginableEnumsManager::apiToCore('UserEntryType', $value);
	}

	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}

	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if (($baseClass === 'KalturaUserEntry') && ($enumValue == self::getRsvpUserEntryTypeCoreValue(RsvpUserEntryType::RSVP)))
		{
			return new KalturaRsvpUserEntry();
		}
		if (($baseClass === 'UserEntry') && ($enumValue == self::getRsvpUserEntryTypeCoreValue(RsvpUserEntryType::RSVP)))
		{
			return new RsvpUserEntry();
		}
		return null;
	}

	public static function getObjectClass($baseClass, $enumValue)
	{
		if ($baseClass === 'UserEntry' && $enumValue == self::getRsvpUserEntryTypeCoreValue(RsvpUserEntryType::RSVP))
		{
			return RsvpUserEntry::RSVP_OM_CLASS;
		}
		return null;
	}
}
