<?php
/**
 * @package plugins.bulkUploadXml
 */
class BulkUploadXmlPlugin extends KalturaPlugin implements IKalturaBulkUpload, IKalturaVersion, IKalturaConfigurator, IKalturaSchemaDefiner
{
	const PLUGIN_NAME = 'bulkUploadXml';
	const PLUGIN_VERSION_MAJOR = 1;
	const PLUGIN_VERSION_MINOR = 1;
	const PLUGIN_VERSION_BUILD = 0;
	
	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaVersion::getVersion()
	 */
	public static function getVersion()
	{
		return new KalturaVersion(
			self::PLUGIN_VERSION_MAJOR,
			self::PLUGIN_VERSION_MINOR,
			self::PLUGIN_VERSION_BUILD
		);
	}
		
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('BulkUploadXmlType', 'XmlSchemaType');
		
		if($baseEnumName == 'BulkUploadType')
			return array('BulkUploadXmlType');
			
		if($baseEnumName == 'SchemaType')
			return array('XmlSchemaType');
			
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		//Gets the right job for the engine	
		if($baseClass == 'kBulkUploadJobData' && $enumValue == self::getBulkUploadTypeCoreValue(BulkUploadXmlType::XML))
			return new kBulkUploadXmlJobData();
		
		 //Gets the right job for the engine	
		if($baseClass == 'KalturaBulkUploadJobData' && $enumValue == self::getBulkUploadTypeCoreValue(BulkUploadXmlType::XML))
			return new KalturaBulkUploadXmlJobData();
		
		//Gets the engine (only for clients)
		if($baseClass == 'KBulkUploadEngine' && class_exists('KalturaClient') && $enumValue == KalturaBulkUploadType::XML)
		{
			list($taskConfig, $kClient, $job) = $constructorArgs;
			return new BulkUploadEngineXml($taskConfig, $kClient, $job);
		}
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		return null;
	}
	

	/**
	 * Returns the log file for bulk upload job
	 * @param BatchJob $batchJob bulk upload batchjob
	 */
	public static function writeBulkUploadLogFile($batchJob)
	{
		$bulkUploadResults = BulkUploadResultPeer::retrieveByBulkUploadId($batchJob->getId());
		if(!count($bulkUploadResults)){
			echo "Log file is not ready";
			KalturaLog::info("Log file is not ready");
			return;
		}

		KalturaLog::info("generating xml");
			
		$data = $batchJob->getData();
	
		
		$xmlElement = new SimpleXMLElement('<bulkUploadLog/>');
		$xmlElement->addAttribute('version', '2.0');
		$xmlElement->addAttribute('xmlns:content', 'http://www.w3.org/2001/XMLSchema-instance');
		
		$channel = $xmlElement->addChild('channel');
		
//		insert all entries to instance pool
		$pks = array();
		foreach($bulkUploadResults as $bulkUploadResult){
			/* @var $bulkUploadResult BulkUploadResult */
			$pks[] = $bulkUploadResult->getEntryId();
		}
		entryPeer::retrieveByPKs($pks);
		
		foreach($bulkUploadResults as $bulkUploadResult){
			/* @var $bulkUploadResult BulkUploadResult */
			$item = $channel->addChild('item');
			
			if($data instanceof kBulkUploadCsvJobData && $data->getCsvVersion() > 1){
				$item->addChild('conversionProfileId', self::stringToSafeXml($bulkUploadResult->getConversionProfileId()));
				$item->addChild('accessControlId', self::stringToSafeXml($bulkUploadResult->getAccessControlProfileId()));
				$item->addChild('startDate', self::stringToSafeXml($bulkUploadResult->getScheduleStartDate('Y-m-d\TH:i:s')));
				$item->addChild('endDate', self::stringToSafeXml($bulkUploadResult->getScheduleEndDate('Y-m-d\TH:i:s')));
				$item->addChild('partnerData', self::stringToSafeXml($bulkUploadResult->getPartnerData()));
			}
			$result = $item->addChild('result');
			$result->addChild('errorDescription', self::stringToSafeXml($bulkUploadResult->getErrorDescription()));
			$result->addChild('entryStatus', self::stringToSafeXml($bulkUploadResult->getEntryStatus()));
//			$result->addChild('entryStatusName', self::stringToSafeXml($title));
			$result->addChild('entryId', self::stringToSafeXml($bulkUploadResult->getEntryId()));
			
//			TODO - add action column to the bulk_upload_result table
//			maybe also...
//			 - add custom_data and bulk_upload_type
//			 - and change the entry_id to object_id and add object_type
//			 - and change the line_index to item_index
//			
//			$item->addChild('action', self::stringToSafeXml($title));
			
			$entry = $bulkUploadResult->getEntry();
			if(!$entry)
				continue;
				
			kMrssManager::getEntryMrssXml($entry, $item);
		}
		
		echo $xmlElement->asXML();
		KalturaLog::info($xmlElement->asXML());
		exit;
		
	}
	
    /**
	 * @param string $string
	 * @return string
	 */
	private static function stringToSafeXml($string)
	{
		$string = @iconv('utf-8', 'utf-8', $string);
		$partially_safe = kString::xmlEncode($string);
		$safe = str_replace(array('*', '/', '[', ']'), '',$partially_safe);
		
		return $safe;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaBulkUpload::getFileExtension()
	 */
	public static function getFileExtension($enumValue)
	{
		if($enumValue == self::getBulkUploadTypeCoreValue(BulkUploadXmlType::XML))
			return 'xml';
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaConfigurator::getConfig()
	 */
	public static function getConfig($configName)
	{
		if($configName == 'generator')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/generator.ini');
			
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::contributeToSchema()
	 */
	public static function getPluginSchema($type)
	{
		$coreType = kPluginableEnumsManager::apiToCore('SchemaType', $type);
		if($coreType != self::getSchemaTypeCoreValue(XmlSchemaType::BULK_UPLOAD_XML))
			return null;
			
		return new SimpleXMLElement(dirname(__FILE__) . '/xml/ingestion.xsd', null, true);
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
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getSchemaTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('SchemaType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
}
