<?php
/**
 * Bulk upload service is used to upload & manage events in bulk
 *
 * @service scheduleBulk
 * @package plugins.scheduleBulkUpload
 * @subpackage services
 */
class ScheduleBulkService extends KalturaBaseService
{
	/**
	 * Add new bulk upload batch job
	 * 
	 * @action addScheduleEvents
	 * @actionAlias schedule_scheduleEvent.addFromBulkUpload
	 * @param file $fileData
	 * @param KalturaBulkUploadICalJobData $bulkUploadData
	 * @return KalturaBulkUpload
	 */
	function addScheduleEventsAction($fileData, KalturaBulkUploadICalJobData $bulkUploadData = null)
	{	    
		$bulkUploadCoreType = BulkUploadSchedulePlugin::getBulkUploadTypeCoreValue(BulkUploadScheduleType::ICAL);
		$bulkUploadObjectCoreType = BulkUploadSchedulePlugin::getBulkUploadObjectTypeCoreValue(BulkUploadObjectScheduleType::SCHEDULE_EVENT);
		
		if(!$bulkUploadData)
	    	$bulkUploadData = KalturaPluginManager::loadObject('KalturaBulkUploadJobData', $bulkUploadCoreType);
		
		if(!$bulkUploadData->fileName)
			$bulkUploadData->fileName = $fileData["name"];
		
		$dbBulkUploadJobData = $bulkUploadData->toInsertableObject();
		/* @var $dbBulkUploadJobData kBulkUploadJobData */
		
		$dbBulkUploadJobData->setBulkUploadObjectType($bulkUploadObjectCoreType);
		$dbBulkUploadJobData->setUserId($this->getKuser()->getPuserId());
		$dbBulkUploadJobData->setFilePath($fileData["tmp_name"]);
		
		$dbJob = kJobsManager::addBulkUploadJob($this->getPartner(), $dbBulkUploadJobData, $bulkUploadCoreType);
		$dbJobLog = BatchJobLogPeer::retrieveByBatchJobId($dbJob->getId());
		if(!$dbJobLog)
			return null;
			
		$bulkUpload = new KalturaBulkUpload();
		$bulkUpload->fromObject($dbJobLog, $this->getResponseProfile());
		
		return $bulkUpload;
	}
	
	/**
	 * Add new bulk upload batch job
	 * 
	 * @action addScheduleResources
	 * @actionAlias schedule_scheduleResource.addFromBulkUpload
	 * @param file $fileData
	 * @param KalturaBulkUploadCsvJobData $bulkUploadData
	 * @return KalturaBulkUpload
	 */
	function addScheduleResourcesAction($fileData, KalturaBulkUploadCsvJobData $bulkUploadData = null)
	{	    
	    if (!$bulkUploadData)
	    {
	       $bulkUploadData = KalturaPluginManager::loadObject('KalturaBulkUploadJobData', null);
	    }
	    
		$bulkUploadObjectCoreType = BulkUploadSchedulePlugin::getBulkUploadObjectTypeCoreValue(BulkUploadObjectScheduleType::SCHEDULE_RESOURCE);
		
		if(!$bulkUploadData->fileName)
			$bulkUploadData->fileName = $fileData["name"];
		
		$dbBulkUploadJobData = $bulkUploadData->toInsertableObject();
		/* @var $dbBulkUploadJobData kBulkUploadJobData */

		$bulkUploadCoreType = kPluginableEnumsManager::apiToCore("BulkUploadType", $bulkUploadData->type);
		
		$dbBulkUploadJobData->setBulkUploadObjectType($bulkUploadObjectCoreType);
		$dbBulkUploadJobData->setUserId($this->getKuser()->getPuserId());
		$dbBulkUploadJobData->setFilePath($fileData["tmp_name"]);
		
		$dbJob = kJobsManager::addBulkUploadJob($this->getPartner(), $dbBulkUploadJobData, $bulkUploadCoreType);
		$dbJobLog = BatchJobLogPeer::retrieveByBatchJobId($dbJob->getId());
		if(!$dbJobLog)
			return null;
			
		$bulkUpload = new KalturaBulkUpload();
		$bulkUpload->fromObject($dbJobLog, $this->getResponseProfile());
		
		return $bulkUpload;
	}
}