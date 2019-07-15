<?php
/**
 * Enable entry objects management that relates to user object...?
 * @package plugins.watchLaterPlaylist
 */
 class WatchLaterPlaylistPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaObjectLoader
 {
	 const PLUGIN_NAME = "watchLaterPlaylist";

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
		 if (in_array($partnerId, array(Partner::ADMIN_CONSOLE_PARTNER_ID, Partner::BATCH_PARTNER_ID)))
		 {
			 return true;
		 }

		 $partner = PartnerPeer::retrieveByPK($partnerId);
		 return $partner->getPluginEnabled(self::PLUGIN_NAME);
	 }

	 /* (non-PHPdoc)
	  * @see IKalturaEnumerator::getEnums()
	  */
	 public static function getEnums($baseEnumName = null)
	 {
		 if (is_null($baseEnumName))
		 {
			 return array('WatchLaterPlaylistUserEntryType');
		 }
		 if ($baseEnumName == 'UserEntryType')
		 {
			 return array('WatchLaterPlaylistUserEntryType');
		 }
		 return array();
	 }

	 public static function getWatchLaterPlaylistUserEntryTypeCoreValue ($valueName)
	 {
		 $value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
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
		 if ( ($baseClass == "KalturaUserEntry") && ($enumValue == self::getWatchLaterPlaylistUserEntryTypeCoreValue(WatchLaterPlaylistUserEntryType::WATCH_LATER_PLAYLIST)))
		 {
			 return new KalturaWatchLaterPlaylistUserEntry();
		 }
		 if ( ($baseClass == "UserEntry") && ($enumValue == self::getWatchLaterPlaylistUserEntryTypeCoreValue(WatchLaterPlaylistUserEntryType::WATCH_LATER_PLAYLIST)))
		 {
			 return new WatchLaterPlaylistUserEntry();
		 }
		 return null;
	 }

	 public static function getObjectClass($baseClass, $enumValue)
	 {
		 if ($baseClass == 'UserEntry' && $enumValue == self::getWatchLaterPlaylistUserEntryTypeCoreValue(WatchLaterPlaylistUserEntryType::WATCH_LATER_PLAYLIST))
		 {
			 return WatchLaterPlaylistUserEntry::WATCH_LATER_PLAYLIST_OM_CLASS;
		 }
		 return null;
	 }
 }