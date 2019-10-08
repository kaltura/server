<?php
/**
 * Plugin enabling the storage of entries that relates to a user to watch later
 * @package plugins.watchLater
 */
 class WatchLaterPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaObjectLoader
 {
	 const PLUGIN_NAME = 'watchLater';

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
			 return array('WatchLaterUserEntryType');
		 }
		 if ($baseEnumName === 'UserEntryType')
		 {
			 return array('WatchLaterUserEntryType');
		 }
		 return array();
	 }

	 public static function getWatchLaterUserEntryTypeCoreValue ($valueName)
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
		 if ( ($baseClass === 'KalturaUserEntry') && ($enumValue == self::getWatchLaterUserEntryTypeCoreValue(WatchLaterUserEntryType::WATCH_LATER)))
		 {
			 return new KalturaWatchLaterUserEntry();
		 }
		 if ( ($baseClass === 'UserEntry') && ($enumValue == self::getWatchLaterUserEntryTypeCoreValue(WatchLaterUserEntryType::WATCH_LATER)))
		 {
			 return new WatchLaterUserEntry();
		 }
		 return null;
	 }

	 public static function getObjectClass($baseClass, $enumValue)
	 {
		 if ($baseClass === 'UserEntry' && $enumValue == self::getWatchLaterUserEntryTypeCoreValue(WatchLaterUserEntryType::WATCH_LATER))
		 {
			 return WatchLaterUserEntry::WATCH_LATER_OM_CLASS;
		 }
		 return null;
	 }
 }