<?php
/**
 * Plugin enabling the permissions level of a specific user for a specific entry
 * @package plugins.entryPermissionLevel
 */
class EntryPermissionLevelPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaObjectLoader
{
	const PLUGIN_NAME = 'entryPermissionLevel';

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
		if (is_null($baseEnumName) || $baseEnumName === 'UserEntryType')
		{
			return array('PermissionLevelUserEntryType');
		}
		return array();
	}

	public static function getPermissionLevelUserEntryTypeCoreValue ($valueName)
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
		if ( ($baseClass === 'KalturaUserEntry') && ($enumValue == self::getPermissionLevelUserEntryTypeCoreValue(PermissionLevelUserEntryType::PERMISSION_LEVEL)))
		{
			return new KalturaPermissionLevelUserEntry();
		}
		if ( ($baseClass === 'UserEntry') && ($enumValue == self::getPermissionLevelUserEntryTypeCoreValue(PermissionLevelUserEntryType::PERMISSION_LEVEL)))
		{
			return new PermissionLevelUserEntry();
		}
		return null;
	}

	public static function getObjectClass($baseClass, $enumValue)
	{
		if ($baseClass === 'UserEntry' && $enumValue == self::getPermissionLevelUserEntryTypeCoreValue(PermissionLevelUserEntryType::PERMISSION_LEVEL))
		{
			return PermissionLevelUserEntry::PERMISSION_LEVEL_OM_CLASS;
		}
		return null;
	}
}