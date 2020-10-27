<?php
/**
 * @package plugins.smilManifest
 */
class SmilManifestPlugin extends KalturaPlugin implements IKalturaObjectLoader, IKalturaEnumerator, IKalturaBatchJobDataContributor
{
	const PLUGIN_NAME = 'smilManifest';

	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}

	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'KOperationEngine' && $enumValue == KalturaConversionEngineType::SMIL_MANIFEST)
		{
			if(!isset($constructorArgs['params']) || !isset($constructorArgs['outFilePath']))
				return null;
				
			return new KOperationEngineSmilManifest(null, $constructorArgs['outFilePath']);
		}
	
		if($baseClass == 'KDLOperatorBase' && $enumValue == self::getApiValue(SmilManifestConversionEngineType::SMIL_MANIFEST))
		{
			return new KDLOperatorSmilManifest($enumValue);
		}
		
		return null;
	}

	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @return string
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'KOperationEngine' && $enumValue == self::getApiValue(SmilManifestConversionEngineType::SMIL_MANIFEST))
			return 'KOperationEngineSmilManifest';
	
		if($baseClass == 'KDLOperatorBase' && $enumValue == self::getConversionEngineCoreValue(SmilManifestConversionEngineType::SMIL_MANIFEST))
			return 'KDLOperatorSmilManifest';

		return null;
	}
	
	/**
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('SmilManifestConversionEngineType');
	
		if($baseEnumName == 'conversionEngineType')
			return array('SmilManifestConversionEngineType');
			
		return array();
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getConversionEngineCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('conversionEngineType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}

	public static function contributeToJobData ($jobType, $jobSubType, kJobData $jobData)
	{
		if($jobType == BatchJobType::CONVERT &&
			$jobSubType == self::getApiValue(SmilManifestConversionEngineType::SMIL_MANIFEST) &&
			$jobData instanceof kConvertJobData
		)
			return self::addFlavorParamsOutputForSourceAssets($jobData);
		else
			return $jobData;
	}

	public static function addFlavorParamsOutputForSourceAssets(kConvertJobData $jobData)
	{
		$assetsData = array();
		foreach($jobData->getSrcFileSyncs() as $srcFileSyncDesc)
		{
			/** @var $srcFileSyncDesc kSourceFileSyncDescriptor */
			$assetId = $srcFileSyncDesc->getAssetId();
			$flavorAsset = assetPeer::retrieveById($assetId);
			$assetsData['asset_'.$assetId.'_bitrate'] = $flavorAsset->getBitrate();
		}
		$pluginData = $jobData->getPluginData();
		if (!$pluginData)
			$pluginData = array();
		$jobData->setPluginData(array_merge($pluginData, $assetsData));
		return $jobData;
	}
}
