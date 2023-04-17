<?php
/**
 * @package plugins.searchHistory
 */
class SearchHistoryPlugin extends KalturaPlugin implements IKalturaPending, IKalturaServices, IKalturaEventConsumers, IKalturaObjectLoader, IKalturaEnumerator
{

	const PLUGIN_NAME = 'searchHistory';
	const SEARCH_HISTORY_MANAGER = 'kESearchHistoryManager';

	/**
	 * @return string the name of the plugin
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}

	/**
	 * @return array
	 */
	public static function getEventConsumers()
	{
		return array(
			self::SEARCH_HISTORY_MANAGER,
		);
	}

	/* (non-PHPdoc)
	* @see IKalturaPending::dependsOn()
	*/
	public static function dependsOn()
	{
		$rabbitMqDependency = new KalturaDependency(RabbitMQPlugin::getPluginName());
		$elasticSearchDependency = new KalturaDependency(ElasticSearchPlugin::getPluginName());
		return array($rabbitMqDependency, $elasticSearchDependency);
	}

	public static function getServicesMap()
	{
		$map = array(
			'SearchHistory' => 'ESearchHistoryService',
		);
		return $map;
	}

	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getExportTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('ExportObjectType', $value);
	}

	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}

	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getBatchJobTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('BatchJobType', $value);
	}

	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{

		if ($baseClass == 'kJobData')
		{
			if ($enumValue == self::getBatchJobTypeCoreValue(SearchHistorySearchTermCsvBatchType::SEARCH_TERMS_CSV))
			{
				return new KalturaSearchHistoryCsvJobData();
			}
		}

		if ($baseClass == 'KalturaJobData')
		{
			if ($enumValue == self::getApiValue(SearchHistorySearchTermCsvBatchType::SEARCH_TERMS_CSV) ||
				$enumValue == self::getBatchJobTypeCoreValue(SearchHistorySearchTermCsvBatchType::SEARCH_TERMS_CSV))
			{
				return new KalturaSearchHistoryCsvJobData();
			}
		}

		if($baseClass == 'KalturaJobData' && $enumValue == BatchJobType::EXPORT_CSV && (isset($constructorArgs['coreJobSubType']) &&  $constructorArgs['coreJobSubType']== self::getExportTypeCoreValue(SearchHistoryExportObjectType::SEARCH_TERM)))
		{
			return new KalturaSearchHistoryCsvJobData();
		}

		if ($baseClass == 'KObjectExportEngine' && $enumValue == KalturaExportObjectType::SEARCH_TERM)
		{
			return new KExportSearchHistoryEngine($constructorArgs);
		}


		return null;
	}

	public static function getObjectClass($baseClass, $enumValue)
	{

		if ($baseClass == 'kJobData')
		{
			if ($enumValue == self::getBatchJobTypeCoreValue(SearchHistorySearchTermCsvBatchType::SEARCH_TERMS_CSV))
			{
				return 'kSearchHistoryCsvJobData';
			}
		}

		if ($baseClass == 'KalturaJobData')
		{
			if ($enumValue == self::getApiValue(SearchHistorySearchTermCsvBatchType::SEARCH_TERMS_CSV))
			{
				return 'KalturaSearchHistoryCsvJobData';
			}
		}

		return null;

	}

	public static function getEnums($baseEnumName = null)
	{
		if (is_null($baseEnumName))
			return array('SearchHistorySearchTermCsvBatchType', 'SearchHistoryExportObjectType');

		if ($baseEnumName == 'BatchJobType')
			return array('SearchHistorySearchTermCsvBatchType');

		if ($baseEnumName == 'ExportObjectType')
			return array('SearchHistoryExportObjectType');

		return array();
	}

}
