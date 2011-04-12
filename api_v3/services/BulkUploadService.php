<?php

/**
 * Bulk upload service is used to upload & manage bulk uploads using CSV files
 *
 * @service bulkUpload
 * @package api
 * @subpackage services
 */
class BulkUploadService extends KalturaBaseService
{
	// TODO
	// add listResults action
	// add listResultsWithEntries action
	// add listResultsWithImportJobs action
	
	/**
	 * Add new bulk upload batch job
	 * Conversion profile id can be specified in the API or in the CSV file, the one in the CSV file will be stronger.
	 * If no conversion profile was specified, partner's default will be used
	 * 
	 * @action add
	 * @param int $conversionProfileId Convertion profile id to use for converting the current bulk (-1 to use partner's default)
	 * @param file $csvFileData CSV File
	 * @return KalturaBulkUpload
	 */
	function addAction($conversionProfileId, $csvFileData, $bulkUploadType = null)
	{
		// first we copy the file to "content/batchfiles/[partner_id]/"
		$origFilename = $csvFileData["name"];
		$fileInfo = pathinfo($origFilename);
		$extension = strtolower($fileInfo["extension"]);
			
		//TODO: Roni - Ask TanTan about the int type and the changes needed
		$job = new BatchJob();
		$job->setPartnerId($this->getPartnerId());
		$job->setJobSubType($bulkUploadType);
		$job->save();
		//TODO: Roni - add default BulkEngine plugin interface return CSV.
		
		$syncKey = $job->getSyncKey(BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADCSV);
//		kFileSyncUtils::file_put_contents($syncKey, file_get_contents($csvFileData["tmp_name"]));
		try{
			kFileSyncUtils::moveFromFile($csvFileData["tmp_name"], $syncKey, true);
		}
		catch(Exception $e)
		{
			throw new KalturaAPIException(KalturaErrors::BULK_UPLOAD_CREATE_CSV_FILE_SYNC_ERROR);
		}
		$csvPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
		
		$data = KalturaPluginManager::loadObject('KalturaBulkUploadJobData', $bulkUploadType, array());
		
		$data = new KalturaBulkUploadJobData();
		$data->csvFilePath = $csvPath;
		$data->userId = $this->getKuser()->getPuserId();
		$data->uploadedBy = $this->getKuser()->getScreenName();
		if ($conversionProfileId === -1)
			$conversionProfileId = $this->getPartner()->getDefaultConversionProfileId();
			
		$kmcVersion = $this->getPartner()->getKmcVersion();
		$check = null;
		if($kmcVersion < 2)
		{
			$check = ConversionProfilePeer::retrieveByPK($conversionProfileId);
		}
		else
		{
			$check = conversionProfile2Peer::retrieveByPK($conversionProfileId);
		}
		if(!$check)
			throw new KalturaAPIException(KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND, $conversionProfileId);
		
		$data->conversionProfileId = $conversionProfileId;
			
		$dbJob = kJobsManager::addJob($job, $data->toObject(), KalturaBatchJobType::BULKUPLOAD);
		
		$bulkUpload = new KalturaBulkUpload();
		$bulkUpload->fromObject($dbJob);
		return $bulkUpload;
	}
	
	/**
	 * Get bulk upload batch job by id
	 *
	 * @action get
	 * @param int $id
	 * @return KalturaBulkUpload
	 */
	function getAction($id)
	{
	    $c = new Criteria();
	    $c->addAnd(BatchJobPeer::ID, $id);
		$c->addAnd(BatchJobPeer::PARTNER_ID, $this->getPartnerId());
		$c->addAnd(BatchJobPeer::JOB_TYPE, BatchJobType::BULKUPLOAD);
		$batchJob = BatchJobPeer::doSelectOne($c);
		
		if (!$batchJob)
		    throw new KalturaAPIException(KalturaErrors::BULK_UPLOAD_NOT_FOUND, $id);
		    
		$ret = new KalturaBulkUpload();
		$ret->fromObject($batchJob);
		return $ret;
	}
	
	/**
	 * List bulk upload batch jobs
	 *
	 * @action list
	 * @param KalturaFilterPager $pager
	 * @return KalturaBulkUploadListResponse
	 */
	function listAction(KalturaFilterPager $pager = null)
	{
	    if (!$pager)
			$pager = new KalturaFilterPager();
			
	    $c = new Criteria();
		$c->addAnd(BatchJobPeer::PARTNER_ID, $this->getPartnerId());
		$c->addAnd(BatchJobPeer::JOB_TYPE, BatchJobType::BULKUPLOAD);
		$c->addDescendingOrderByColumn(BatchJobPeer::ID);
		
		$count = BatchJobPeer::doCount($c);
		$pager->attachToCriteria($c);
		$jobs = BatchJobPeer::doSelect($c);
		
		$response = new KalturaBulkUploadListResponse();
		$response->objects = KalturaBulkUploads::fromBatchJobArray($jobs);
		$response->totalCount = $count; 
		
		return $response;
	}
}