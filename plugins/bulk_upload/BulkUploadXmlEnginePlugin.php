<?php
/**
 * @package plugins.bulkUploadXmlEngine
 */
class BulkUploadXmlEnginePlugin extends KalturaPlugin implements IKalturaEnumerator, IKalturaObjectLoader
{
	const PLUGIN_NAME = 'bulkUploadXmlEngine';
	
	/**
	 * 
	 * Returns the plugin name
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	
	/**
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('BulkUploadXmlType');
	
		if($baseEnumName == 'BulkUploadType')
			return array('BulkUploadXmlType');
		
		if($baseEnumName == 'KalturaBulkUploadType')
			return array('BulkUploadXmlType');
			
		return array();
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		// bulk upload does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;
			
		//Returns the right job for the engine
		if($baseClass == 'kJobData')
		{
			if($enumValue == self::getBulkUploadTypeCoreValue(BulkUploadType::XML))
				return new kBulkUploadXmlJobData();
		}
		
		if($baseClass == 'KalturaBulkUploadJobData')
		{
			if($enumValue == self::getApiValue(BulkUploadType::XML))
				return new kBulkUploadXmlJobData();
		}
		
		//Returns the right bulk upload type for the engine
		if($baseClass == 'BulkUploadType')
		{
			if($enumValue == self::getBulkUploadTypeCoreValue(BulkUploadType::XML))
				return new kBulkUploadXmlJobData();
		}
		
		if($baseClass == 'KalturaBulkUploadType')
		{
			if($enumValue == self::getApiValue(BulkUploadType::XML))
				return new kBulkUploadXmlJobData();
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
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;
			
		//Returns the right class name for the engine
		if($baseClass == 'kJobData')
		{
			if($enumValue == self::getBulkUploadTypeCoreValue(BulkUploadType::XML))
				return 'kBulkUploadXmlJobData';
		}
		
		if($baseClass == 'KalturaBulkUploadJobData')
		{
			if($enumValue == self::getApiValue(BulkUploadType::XML))
				return 'kBulkUploadXmlJobData';
		}
		
		//Returns the right bulk upload type for the engine
		if($baseClass == 'BulkUploadType')
		{
			if($enumValue == self::getBulkUploadTypeCoreValue(BulkUploadType::XML))
				return 'kBulkUploadXmlJobData';
		}
		
		if($baseClass == 'KalturaBulkUploadType')
		{
			if($enumValue == self::getApiValue(BulkUploadType::XML))
				return 'kBulkUploadXmlJobData';
		}
//		if($baseClass == 'kJobData')
//		{
//			if($enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT))
//				return 'kDistributionSubmitJobData';
//				
//			if($enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE))
//				return 'kDistributionUpdateJobData';
//				
//			if($enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE))
//				return 'kDistributionDeleteJobData';
//				
//			if($enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT))
//				return 'kDistributionFetchReportJobData';
//		}
//	
//		if($baseClass == 'KalturaJobData')
//		{
//			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT) || $enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT))
//				return 'KalturaDistributionSubmitJobData';
//				
//			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE) || $enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE))
//				return 'KalturaDistributionUpdateJobData';
//				
//			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE) || $enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE))
//				return 'KalturaDistributionDeleteJobData';
//				
//			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT) || $enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT))
//				return 'KalturaDistributionFetchReportJobData';
//		}
		
		return null;
	}
		
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getBulkUploadTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('BulkUploadType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
}
