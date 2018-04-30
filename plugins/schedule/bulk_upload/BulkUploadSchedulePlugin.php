<?php
/**
 * @package plugins.scheduleBulkUpload
 */
class BulkUploadSchedulePlugin extends KalturaPlugin implements IKalturaBulkUpload, IKalturaPending, IKalturaServices
{
	const PLUGIN_NAME = 'scheduleBulkUpload';
	
	/**
	 * Returns the plugin name
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn()
	{
		$bulkUploadCsvDependency = new KalturaDependency(BulkUploadCsvPlugin::PLUGIN_NAME);
		$scheduleDependency = new KalturaDependency(SchedulePlugin::PLUGIN_NAME);
		
		return array($bulkUploadCsvDependency, $scheduleDependency);
	}
	
	/**
	 *
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('BulkUploadScheduleType', 'BulkUploadScheduleAction', 'BulkUploadObjectScheduleType');
		
		if($baseEnumName == 'BulkUploadType')
			return array('BulkUploadScheduleType');
		
		if($baseEnumName == 'BulkUploadAction')
			return array('BulkUploadScheduleAction');
		
		if($baseEnumName == 'BulkUploadObjectType')
			return array('BulkUploadObjectScheduleType');
		
		return array();
	}
	
	/**
	 *
	 * @param string $baseClass        	
	 * @param string $enumValue        	
	 * @param array $constructorArgs        	
	 * @return object
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'kBulkUploadJobData' && $enumValue == self::getBulkUploadTypeCoreValue(BulkUploadScheduleType::ICAL))
			return new kBulkUploadICalJobData();
		
		if($baseClass == 'KalturaBulkUploadJobData' && $enumValue == self::getBulkUploadTypeCoreValue(BulkUploadScheduleType::ICAL))
			return new KalturaBulkUploadICalJobData();
			
			// Gets the engine (only for clients)
		if($baseClass == 'KBulkUploadEngine' && class_exists('KalturaClient'))
		{	
			list($job) = $constructorArgs;
			if($enumValue == KalturaBulkUploadType::ICAL)
			{
				return new BulkUploadEngineICal($job);
			}
			elseif((!$enumValue || $enumValue == KalturaBulkUploadType::CSV) && $job->data->bulkUploadObjectType == KalturaBulkUploadObjectType::SCHEDULE_RESOURCE)
			{
				return new BulkUploadScheduleResourceEngineCsv($job);
			}
		}
		
		return null;
	}
	
	/**
	 *
	 * @param string $baseClass        	
	 * @param string $enumValue        	
	 * @return string
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'BulkUploadResult' && $enumValue == self::getBulkUploadObjectTypeCoreValue(BulkUploadObjectScheduleType::SCHEDULE_EVENT))
		{
			return 'BulkUploadResultScheduleEvent';
		}
		
		return null;
	}
	
	/**
	 * Returns the correct file extension for bulk upload type
	 *
	 * @param int $enumValue
	 *        	code API value
	 */
	public static function getFileExtension($enumValue)
	{
		if($enumValue == self::getBulkUploadTypeCoreValue(BulkUploadScheduleType::ICAL))
			return 'ics';
	}
	
	/**
	 * Returns the log file for bulk upload job
	 *
	 * @param BatchJob $batchJob
	 *        	bulk upload batchjob
	 */
	public static function writeBulkUploadLogFile($batchJob)
	{
		if($batchJob->getJobSubType() != self::getBulkUploadTypeCoreValue(BulkUploadScheduleType::ICAL))
		{
			return;
		}
		
		self::writeICalBulkUploadLogFile($batchJob);
	}
	
	/**
	 * Returns the log file for bulk upload job
	 *
	 * @param BatchJob $batchJob bulk upload batchjob
	 */
	public static function writeICalBulkUploadLogFile($batchJob)
	{
		header("Content-Type: text/calendar; charset=UTF-8");
		
		$criteria = new Criteria();
		$criteria->add(BulkUploadResultPeer::BULK_UPLOAD_JOB_ID, $batchJob->getId());
		$criteria->addAscendingOrderByColumn(BulkUploadResultPeer::LINE_INDEX);
		$criteria->setLimit(100);
		$bulkUploadResults = BulkUploadResultPeer::doSelect($criteria);
		
		if(!count($bulkUploadResults))
			die("Log file is not ready");
		
		kSchedulingICalComponent::setWriteToStdout(true);
		$calendar = new kSchedulingICalCalendar();
		$calendar->begin();
		
		$handledResults = 0;
		while(count($bulkUploadResults))
		{
			$handledResults += count($bulkUploadResults);
			foreach($bulkUploadResults as $bulkUploadResult)
			{
				/* @var $bulkUploadResult BulkUploadResult */
				$scheduleEvent = $bulkUploadResult->getObject();
				$extraAttributes = array(
					'index' => $bulkUploadResult->getLineIndex(), 
					'ingest-status' => $bulkUploadResult->getStatus(), 
					'ingest-action' => $bulkUploadResult->getAction(),
				);

				if($bulkUploadResult->getErrorDescription())
					$extraAttributes['error-description'] = $bulkUploadResult->getErrorDescription();

				if($bulkUploadResult->getErrorType())
					$extraAttributes['error-type'] = $bulkUploadResult->getErrorType();

				if($bulkUploadResult->getErrorCode())
					$extraAttributes['error-code'] = $bulkUploadResult->getErrorCode();
					
				if($scheduleEvent)
				{
					$scheduleEventObject = KalturaScheduleEvent::getInstance($scheduleEvent);
					/* @var $scheduleEventObject KalturaScheduleEvent */
					$event = kSchedulingICalEvent::fromObject($scheduleEventObject);
				}
				else
				{
					$event = new kSchedulingICalEvent($bulkUploadResult->getRowData());
				}
				$event->addFields($extraAttributes, 'x-kaltura');
				$event->write();
			}
			
			if(count($bulkUploadResults) < $criteria->getLimit())
				break;
			
			kMemoryManager::clearMemory();
			$criteria->setOffset($handledResults);
			$bulkUploadResults = BulkUploadResultPeer::doSelect($criteria);
		}
		$calendar->end();
		
		kFile::closeDbConnections();
		exit();
	}
	
	/**
	 *
	 * @return string[]
	 */
	public static function getServicesMap()
	{
		$map = array(
			'scheduleBulk' => 'ScheduleBulkService'
		);
		return $map;
	}
	
	/**
	 *
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getBulkUploadTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('BulkUploadType', $value);
	}
	
	/**
	 *
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getBulkUploadActionCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('BulkUploadAction', $value);
	}
	
	/**
	 *
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getBulkUploadObjectTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('BulkUploadObjectType', $value);
	}
	
	/**
	 *
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
}
