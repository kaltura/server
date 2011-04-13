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
	 * @param file $fileData CSV File
	 * @param KalturaBulkUploadType $bulkUploadType
	 * @return KalturaBulkUpload
	 */
	function addAction($conversionProfileId, $fileData, $bulkUploadType = null)
	{
		$coreBulkUploadType = kPluginableEnumsManager::apiToCore('BulkUploadType', $bulkUploadType);
		
		kBulkUploadManager::setKuser($this->getKuser());
		kBulkUploadManager::setpartner($this->getpartner());
		$dbJob = kBulkUploadManager::add($fileData["tmp_name"], $fileData["name"], $conversionProfileId, $bulkUploadType);
		
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