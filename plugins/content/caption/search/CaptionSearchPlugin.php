<?php
/**
 * Enable indexing and searching caption asset objects
 * @package plugins.captionSearch
 */
class CaptionSearchPlugin extends KalturaPlugin implements IKalturaPending, IKalturaPermissions, IKalturaServices, IKalturaEventConsumers, IKalturaEnumerator, IKalturaObjectLoader, IKalturaSearchDataContributor, IKalturaElasticSearchDataContributor
{
	const MAX_CAPTION_FILE_SIZE_FOR_INDEXING = 900000; // limit the size of text which can indexed, the mysql packet size is limited by default to 1M anyway
	const PLUGIN_NAME = 'captionSearch';
	const INDEX_NAME = 'caption_item';
	const SEARCH_FIELD_DATA = 'data';
	const SEARCH_TEXT_SUFFIX = 'csend';
	
	const CAPTION_SEARCH_FLOW_MANAGER_CLASS = 'kCaptionSearchFlowManager';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn()
	{
		$captionDependency = new KalturaDependency(CaptionPlugin::getPluginName());
		
		return array($captionDependency);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId)
	{
		if($partnerId == Partner::BATCH_PARTNER_ID)
			return true;
			
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if(!$partner)
			return false;
		
		return $partner->getPluginEnabled(self::PLUGIN_NAME);
	}

	/* (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
	 */
	public static function getServicesMap()
	{
		$map = array(
			'captionAssetItem' => 'CaptionAssetItemService',
		);
		return $map;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaEventConsumers::getEventConsumers()
	 */
	public static function getEventConsumers()
	{
		return array(
			self::CAPTION_SEARCH_FLOW_MANAGER_CLASS,
		);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('CaptionSearchBatchJobType');
			
		if($baseEnumName == 'BatchJobType')
			return array('CaptionSearchBatchJobType');
			
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'kJobData' && $enumValue == self::getBatchJobTypeCoreValue(CaptionSearchBatchJobType::PARSE_CAPTION_ASSET))
			return new kParseCaptionAssetJobData();
	
		if($baseClass == 'KalturaJobData' && $enumValue == self::getApiValue(CaptionSearchBatchJobType::PARSE_CAPTION_ASSET))
			return new KalturaParseCaptionAssetJobData();
		
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'kJobData' && $enumValue == self::getBatchJobTypeCoreValue(CaptionSearchBatchJobType::PARSE_CAPTION_ASSET))
			return 'kParseCaptionAssetJobData';
	
		if($baseClass == 'KalturaJobData' && $enumValue == self::getApiValue(CaptionSearchBatchJobType::PARSE_CAPTION_ASSET))
			return 'KalturaParseCaptionAssetJobData';
		
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSearchDataContributor::getSearchData()
	 */
	public static function getSearchData(BaseObject $object)
	{
		if($object instanceof entry && self::isAllowedPartner($object->getPartnerId()))
			return self::getCaptionSearchData($object);
			
		return null;
	}
	
	public static function getCaptionSearchData(entry $entry)
	{
		$captionAssets = assetPeer::retrieveByEntryId($entry->getId(), array(CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION)), array(asset::ASSET_STATUS_READY, asset::ASSET_STATUS_EXPORTING));
		if(!$captionAssets || !count($captionAssets))
			return null;
			
		$data = array();
		foreach($captionAssets as $captionAsset)
		{
			/* @var $captionAsset CaptionAsset */
			
			$syncKey = $captionAsset->getSyncKey(asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$content = kFileSyncUtils::file_get_contents($syncKey, true, false, self::MAX_CAPTION_FILE_SIZE_FOR_INDEXING);
			if(!$content)
				continue;
				
	    	$captionsContentManager = kCaptionsContentManager::getCoreContentManager($captionAsset->getContainerFormat());
	    	if(!$captionsContentManager)
	    	{
	    		KalturaLog::err("Captions content manager not found for format [" . $captionAsset->getContainerFormat() . "]");
	    		continue;
	    	}

	    	$content = $captionsContentManager->getContent($content);
	    	if(!$content)
	    		continue;

			$data[] = $captionAsset->getId() . " ca_prefix $content ca_sufix";
		}
		
		$dataField = CaptionSearchPlugin::getSearchFieldName(CaptionSearchPlugin::SEARCH_FIELD_DATA);
		$searchValues = array(
			$dataField => CaptionSearchPlugin::PLUGIN_NAME . ' ' . implode(' ', $data) . ' ' . CaptionSearchPlugin::SEARCH_TEXT_SUFFIX
		);
		
		return $searchValues;
	}

	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getBatchJobTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('BatchJobType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
	
	/**
	 * return field name as appears in index schema
	 * @param string $fieldName
	 */
	public static function getSearchFieldName($fieldName){
		if ($fieldName == self::SEARCH_FIELD_DATA)
			return  'plugins_data';
			
		return CaptionPlugin::getPluginName() . '_' . $fieldName;
	}

	/**
	 * Return textual search data to be associated with the object
	 *
	 * @param BaseObject $object
	 * @return ArrayObject
	 */
	public static function getElasticSearchData(BaseObject $object)
	{
		if($object instanceof entry && self::isAllowedPartner($object->getPartnerId()))
			return self::getCaptionElasticSearchData($object);

		return null;
	}

	public static function filterCaptionsByLangAndLabel($captionAssetsList)
	{
		$accuracies = array();
		$captionAssets = array();
		foreach($captionAssetsList as $captionAsset)
		{
			/* @var $captionAsset CaptionAsset */
			$captionLabel = $captionAsset->getLabel() ? $captionAsset->getLabel() : '';
			$captionLanguage = $captionAsset->getLanguage() ? $captionAsset->getLanguage() : '';
			$captionAccuracy = $captionAsset->getAccuracy() ? $captionAsset->getAccuracy() : 0;

			$key = "{$captionLanguage}_{$captionLabel}";

			if(isset($accuracies[$key]) && ($captionAccuracy <= $accuracies[$key]))
			{
				continue;
			}
			$accuracies[$key] = $captionAccuracy;
			$captionAssets[$key] = $captionAsset;
		}

		return $captionAssets;
	}

	public static function getCaptionElasticSearchData($entry)
	{
		$captionAssets = assetPeer::retrieveByEntryId($entry->getId(), array(CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION)), array(asset::ASSET_STATUS_READY, asset::ASSET_STATUS_EXPORTING));
		if(!$captionAssets || !count($captionAssets))
			return null;

		$captionAssets = self::filterCaptionsByLangAndLabel($captionAssets);

		$data = array();
		$captionData = array();
		$captionRawData = array();
		foreach($captionAssets as $captionAsset)
		{
			/* @var $captionAsset CaptionAsset */
			KalturaLog::info("Caption asset id: {$captionAsset->getId()}");

			$syncKey = $captionAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
			$content = kFileSyncUtils::file_get_contents($syncKey, true, false, self::MAX_CAPTION_FILE_SIZE_FOR_INDEXING);
			if(!$content)
				continue;

			$captionsContentManager = kCaptionsContentManager::getCoreContentManager($captionAsset->getContainerFormat());
			if(!$captionsContentManager)
			{
				KalturaLog::err("Captions content manager not found for format [" . $captionAsset->getContainerFormat() . "]");
				continue;
			}

			$items = $captionsContentManager->parse($content);

			if(!$items)
				continue;

			$language = $captionAsset->getLanguage();
			self::getElasticLines($captionData, $captionRawData, $items, $language, $captionAsset->getId(), $captionAsset->getLabel());
		}
		$data['caption_assets'] = $captionData;
		$captionRawData = array_values(array_unique($captionRawData));
		$data['captions_content'] = $captionRawData;
		return $data;
	}

	protected static function getElasticLines(&$captionData ,&$captionRawData, $items, $language, $assetId, $label = null)
	{
		foreach ($items as $item)
		{
			$line = array(
				'start_time' => $item['startTime'],
				'end_time' => $item['endTime'],
				'language' => $language,
				'caption_asset_id' => $assetId,
			);

			if($label)
				$line['label'] = $label;

			$content = '';
			foreach ($item['content'] as $curChunk)
				$content .= $curChunk['text'];

			$content = kString::stripUtf8InvalidChars($content);
			$content = kXml::stripXMLInvalidChars($content);
			if(strlen($content) > kElasticSearchManager::MAX_LENGTH)
				$content = substr($content, 0, kElasticSearchManager::MAX_LENGTH);
			$line['content'] = $content;
			$captionRawData[]= $content;

			$analyzedFieldName = elasticSearchUtils::getAnalyzedFieldName($language, 'content' ,elasticSearchUtils::UNDERSCORE_FIELD_DELIMITER);
			if($analyzedFieldName)
				$line[$analyzedFieldName] = $content;

			$captionData[] = $line;
		}
	}
}
