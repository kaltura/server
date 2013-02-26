<?php
/**
 * @package plugins.widevine
 */
class WidevinePlugin extends KalturaPlugin implements IKalturaEnumerator, IKalturaServices , IKalturaPermissions, IKalturaObjectLoader/*, IKalturaEventConsumers*/, IKalturaTypeExtender
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
			return array('WidevineConversionEngineType', 'WidevineAssetType', 'WidevinePermissionName');		
		if($baseEnumName == 'conversionEngineType')
			return array('WidevineConversionEngineType');
		if($baseEnumName == 'assetType')
			return array('WidevineAssetType');
		if($baseEnumName == 'PermissionName')
			return array('WidevinePermissionName');
			
			
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'KalturaFlavorParams' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return new KalturaWidevineFlavorParams();
	
		if($baseClass == 'KalturaFlavorParamsOutput' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return new KalturaWidevineFlavorParamsOutput();
		
		if($baseClass == 'KalturaFlavorAsset' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return new KalturaWidevineFlavorAsset();
			
		if($baseClass == 'assetParams' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return new WidevineFlavorParams();
	
		if($baseClass == 'assetParamsOutput' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return new WidevineFlavorParamsOutput();
			
		if($baseClass == 'asset' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return new WidevineFlavorAsset();
			
		if($baseClass == 'flavorAsset' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return new WidevineFlavorAsset();
		
		if($baseClass == 'KOperationEngine' && $enumValue == KalturaConversionEngineType::WIDEVINE)
			return new KWidevineOperationEngine($constructorArgs['params'], $constructorArgs['outFilePath']);
			
		if($baseClass == 'KDLOperatorBase' && $enumValue == self::getApiValue(WidevineConversionEngineType::WIDEVINE))
			return new KDLOperatorWidevine($enumValue);

		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{			
		if($baseClass == 'KalturaFlavorParams' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return 'KalturaWidevineFlavorParams';
	
		if($baseClass == 'KalturaFlavorParamsOutput' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return 'KalturaWidevineFlavorParamsOutput';
		
		if($baseClass == 'KalturaFlavorAsset' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return 'KalturaWidevineFlavorAsset';

		if($baseClass == 'assetParams' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return 'WidevineFlavorParams';
	
		if($baseClass == 'assetParamsOutput' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return 'WidevineFlavorParamsOutput';
			
		if($baseClass == 'asset' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return 'WidevineFlavorAsset';
			
		if($baseClass == 'flavorAsset' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return 'WidevineFlavorAsset';			
		
		if($baseClass == 'KOperationEngine' && $enumValue == KalturaConversionEngineType::WIDEVINE)
			return 'KWidevineOperationEngine';
			
		if($baseClass == 'KDLOperatorBase' && $enumValue == self::getApiValue(WidevineConversionEngineType::WIDEVINE))
			return 'KDLOperatorWidevine';
		
		return null;
	}
	
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
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getAssetTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('assetType', $value);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaTypeExtender::getExtendedTypes()
	 */
	public static function getExtendedTypes($baseClass, $enumValue) {
		$supportedBaseClasses = array(
			assetPeer::OM_CLASS,
			assetParamsPeer::OM_CLASS,
			assetParamsOutputPeer::OM_CLASS,
		);
		
		if(in_array($baseClass, $supportedBaseClasses) && $enumValue == assetType::FLAVOR)
		{
			return array(
				WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR),
			);
		}
		
		return null;		
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
	/* (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
	 */
	public static function getServicesMap() {
		$map = array(
			'widevineDrm' => 'WidevineDrmService',
		);
		return $map;	
	}

	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId) {	
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if(!$partner)
			return false;
		return $partner->getPluginEnabled(self::PLUGIN_NAME);			
	}
	
	public static function getWidevineConfigParam($key)
	{
		$widevineConfig = kConf::get('widevine');
		if (!is_array($widevineConfig))
		{
			KalturaLog::err('Widevine config section is not defined');
			return null;
		}

		if (!isset($widevineConfig[$key]))
		{
			KalturaLog::err('The key '.$key.' was not found in the widevine config section');
			return null;
		}

		return $widevineConfig[$key];
	}
}
