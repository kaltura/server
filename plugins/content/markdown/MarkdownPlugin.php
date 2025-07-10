<?php
/**
 * Enable markdown assets management for entry objects
 * @package plugins.markdown
 */
class MarkdownPlugin extends KalturaPlugin implements IKalturaEnumerator, IKalturaObjectLoader, IKalturaPending, IKalturaTypeExtender, IKalturaEventConsumers
{
	const PLUGIN_NAME = 'markdown';
	const ENTRY_MARKDOWN_PREFIX = 'md_pref';
	const ENTRY_MARKDOWN_SUFFIX = 'md_suf';
	const SEARCH_TEXT_SUFFIX = 'mdend';
	const PLUGINS_DATA = 'plugins_data';
	const MARKDOWN_FLOW_MANAGER_CLASS = 'kMarkdownFlowManager';


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
			return array('MarkdownAssetType');
	
		if($baseEnumName == 'assetType')
			return array('MarkdownAssetType');
			
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
                self::getAssetTypeCoreValue(MarkdownAssetType::MARKDOWN)
            );
        }

        return null;
    }

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'KalturaAsset' && $enumValue == self::getAssetTypeCoreValue(MarkdownAssetType::MARKDOWN))
			return new KalturaMarkdownAsset();
	
		return null;
	}

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'asset' && $enumValue == self::getAssetTypeCoreValue(MarkdownAssetType::MARKDOWN))
			return 'MarkdownAsset';
	
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

	public static function getEventConsumers()
	{
		return array(
			self::MARKDOWN_FLOW_MANAGER_CLASS,
		);
	}
}
