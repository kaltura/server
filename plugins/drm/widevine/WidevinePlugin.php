<?php
/**
 * @package plugins.widevine
 */
class WidevinePlugin extends KalturaPlugin implements IKalturaEnumerator, /*IKalturaServices , IKalturaPermissions, IKalturaConfigurator,*/ IKalturaObjectLoader/*, IKalturaEventConsumers*/
{
	const PLUGIN_NAME = 'widevine';
	
	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
		
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{	
		if(is_null($baseEnumName))
			return array(/*'WidevinePermissionName',*/'WidevineConversionEngineType');		
//		if($baseEnumName == 'PermissionName')
//			return array('WidevinePermissionName');
		if($baseEnumName == 'conversionEngineType')
			return array('WidevineConversionEngineType');
		
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		$objectClass = self::getObjectClass($baseClass, $enumValue);
		
		if (is_null($objectClass)) {
			return null;
		}
		
		if($objectClass == 'KDLOperatorWidevine')
		{
			return new KDLOperatorWidevine($enumValue);
		}
		
		if (!is_null($constructorArgs))
		{
			$reflect = new ReflectionClass($objectClass);
			return $reflect->newInstanceArgs($constructorArgs);
		}
		else
		{
			return new $objectClass();
		}
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'KOperationEngine' && $enumValue == self::getApiValue(KalturaConversionEngineType::WIDEVINE))
			return 'KWidevineOperationEngine';
			
		if($baseClass == 'KDLOperatorBase' && $enumValue == self::getApiValue(WidevineConversionEngineType::WIDEVINE))
			return 'KDLOperatorWidevine';
		
		return null;
	}
	
//	/* (non-PHPdoc)
//	 * @see IKalturaConfigurator::getConfig()
//	 */
//	public static function getConfig($configName)
//	{
//		if($configName == 'generator')
//			return new Zend_Config_Ini(dirname(__FILE__) . '/config/generator.ini');
//			
//		return null;
//	}
	

	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}

	public static function getConversionEngineCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('conversionEngineType', $value);
	}
	
//	/* (non-PHPdoc)
//	 * @see IKalturaEventConsumers::getEventConsumers()
//	 */
//	public static function getEventConsumers() {
//		
//		
//	}
//
//
//	/* (non-PHPdoc)
//	 * @see IKalturaServices::getServicesMap()
//	 */
//	public static function getServicesMap() {
//		$map = array(
//			'widevineDrm' => 'WidevineDrmService',
//		);
//		return $map;	
//	}
//
//	/* (non-PHPdoc)
//	 * @see IKalturaPermissions::isAllowedPartner()
//	 */
//	public static function isAllowedPartner($partnerId) {
//		if (in_array($partnerId, array(Partner::ADMIN_CONSOLE_PARTNER_ID, Partner::BATCH_PARTNER_ID)))
//			return true;
//		
//		$partner = PartnerPeer::retrieveByPK($partnerId);
//		return $partner->getPluginEnabled(self::PLUGIN_NAME);			
//	}
}
