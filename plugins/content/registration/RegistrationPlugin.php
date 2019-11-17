<?php
/**
 * Plugin enabling the storage of entries that relates to a user for registration  - TODO CHANGE
 * @package plugins.registration
 */
class RegistrationPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaObjectLoader
{
	const PLUGIN_NAME = 'registration';

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
			return array('RegistrationUserEntryType');
		}
		if ($baseEnumName === 'UserEntryType')
		{
			return array('RegistrationUserEntryType');
		}
		return array();
	}

	public static function getRegistrationUserEntryTypeCoreValue ($valueName)
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
		if ( ($baseClass === 'KalturaUserEntry') && ($enumValue == self::getRegistrationUserEntryTypeCoreValue(RegistrationUserEntryType::REGISTRATION)))
		{
			return new KalturaRegistrationUserEntry();
		}
		if ( ($baseClass === 'UserEntry') && ($enumValue == self::getRegistrationUserEntryTypeCoreValue(RegistrationUserEntryType::REGISTRATION)))
		{
			return new RegistrationUserEntry();
		}
		return null;
	}

	public static function getObjectClass($baseClass, $enumValue)
	{
		if ($baseClass === 'UserEntry' && $enumValue == self::getRegistrationUserEntryTypeCoreValue(RegistrationUserEntryType::REGISTRATION))
		{
			return RegistrationUserEntry::REGISTRATION_OM_CLASS;
		}
		return null;
	}
}