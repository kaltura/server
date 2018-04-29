<?php
/**
 * Enable transcript assets management for entry objects
 * @package plugins.transcript
 */
class TranscriptPlugin extends KalturaPlugin implements IKalturaEnumerator, IKalturaObjectLoader, IKalturaPending, IKalturaTypeExtender, IKalturaSearchDataContributor
{
	const PLUGIN_NAME = 'transcript';
	const ENTRY_TRANSCRIPT_PREFIX = 'tr_pref';
	const ENTRY_TRANSCRIPT_SUFFIX = 'tr_suf';
	const SEARCH_TEXT_SUFFIX = 'trend';
	const PLUGINS_DATA = 'plugins_data';
	
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

	/* (non-PHPdoc)
 * @see IKalturaSearchDataContributor::getSearchData()
 */
	public static function getSearchData(BaseObject $object)
	{
		if($object instanceof entry)
			return self::getTranscriptSearchData($object);

		return null;
	}

	public static function getTranscriptSearchData(entry $entry)
	{
		$transcriptAssets = assetPeer::retrieveByEntryId($entry->getId(), array(TranscriptPlugin::getAssetTypeCoreValue(TranscriptAssetType::TRANSCRIPT)));
		if(!$transcriptAssets || !count($transcriptAssets))
			return null;

		$data = array();
		foreach($transcriptAssets as $transcriptAsset)
		{
			/* @var $transcriptAsset TranscriptAsset */

			if($transcriptAsset->getContainerFormat() != AttachmentType::TEXT)
				continue;
			
			$syncKey = $transcriptAsset->getSyncKey(asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$content = kFileSyncUtils::file_get_contents($syncKey, true, false);
			if(!$content)
				continue;

			$content = trim(preg_replace('/\s+/', ' ', $content));
			if(!$content)
				continue;

			$data[] = $transcriptAsset->getId() . ' ' . self::ENTRY_TRANSCRIPT_PREFIX  . ' ' . $content . ' ' . self::ENTRY_TRANSCRIPT_SUFFIX;
		}

		$searchValues = array(
			self::PLUGINS_DATA => self::PLUGIN_NAME . ' ' . implode(' ', $data) . ' ' . self::SEARCH_TEXT_SUFFIX
		);

		return $searchValues;
	}
	
}
