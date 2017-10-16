<?php
/**
 * Plugin enabling the storage of user view history
 * @package plugins.viewHistory
 */
 class ViewHistoryPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaObjectLoader
 {
 	const PLUGIN_NAME = "viewHistory";
	
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
			return true;
		
		$partner = PartnerPeer::retrieveByPK($partnerId);
		return $partner->getPluginEnabled(self::PLUGIN_NAME);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if (is_null($baseEnumName))
			return array('ViewHistoryExtendedStatus','ViewHistoryUserEntryType');
		if ($baseEnumName == 'UserEntryExtendedStatus')
			return array('ViewHistoryExtendedStatus');
		if ($baseEnumName == 'UserEntryType')
			return array('ViewHistoryUserEntryType');
		
		return array();
	}
	
	public static function getViewHistoryUserEntryTypeCoreValue ($valueName)
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
		if ( ($baseClass == "KalturaUserEntry") && ($enumValue == self::getViewHistoryUserEntryTypeCoreValue(ViewHistoryUserEntryType::VIEW_HISTORY)))
		{
			return new KalturaViewHistoryUserEntry();
		}
		if ( ($baseClass == "UserEntry") && ($enumValue == self::getViewHistoryUserEntryTypeCoreValue(ViewHistoryUserEntryType::VIEW_HISTORY)))
		{
			return new ViewHistoryUserEntry();
		}
	}
	
	public static function getObjectClass($baseClass, $enumValue)
	{
		if ($baseClass == 'UserEntry' && $enumValue == self::getViewHistoryUserEntryTypeCoreValue(ViewHistoryUserEntryType::VIEW_HISTORY))
		{
			return 'ViewHistoryUserEntry';
		}
	}
 }