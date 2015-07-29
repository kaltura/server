<?php
/**
 * Enable transcript assets management for entry objects
 * @package plugins.transcript
 */
class TranscriptPlugin extends KalturaPlugin implements IKalturaEnumerator, IKalturaObjectLoader, IKalturaPending, IKalturaTypeExtender
{
	const PLUGIN_NAME = 'transcript';
	
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
			return array('TranscriptAssetType');
	
		if($baseEnumName == 'assetType')
			return array('TranscriptAssetType');
			
		return array();
	}
	
    /* (non-PHPdoc)
     * @see IKalturaTypeExtender::getExtendedTypes()
     */
     public static function getExtendedTypes($baseClass, $enumValue)
     {
        if($baseClass == assetPeer::OM_CLASS && $enumValue == AttachmentPlugin::getAssetTypeCoreValue(AttachmentAssetType::ATTACHMENT))
        {
            return array(
                self::getAssetTypeCoreValue(TranscriptAssetType::TRANSCRIPT)
            );
        }

        return null;
    }

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'KalturaAsset' && $enumValue == self::getAssetTypeCoreValue(TranscriptAssetType::TRANSCRIPT))
			return new KalturaTranscriptAsset();
	
		return null;
	}

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'asset' && $enumValue == self::getAssetTypeCoreValue(TranscriptAssetType::TRANSCRIPT))
			return 'TranscriptAsset';
	
		return null;
	}

	/* (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn()
	{
		$dependency = new KalturaDependency(AttachmentPlugin::getPluginName());
		return array($dependency);
	}

	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getAssetTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('assetType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
	
}
