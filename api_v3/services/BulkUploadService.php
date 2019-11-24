<?php

/**
 * Bulk upload service is used to upload & manage bulk uploads using CSV files.
 * This service manages only entry bulk uploads.
 *
 * @service bulkUpload
 * @package api
 * @subpackage services
 * @deprecated Use BulkUploadPlugin instead.
 */
class BulkUploadService extends KalturaBaseService
{
	const PARTNER_DEFAULT_CONVERSION_PROFILE_ID = -1;

	/**
	 * Add new bulk upload batch job
	 * Conversion profile id can be specified in the API or in the CSV file, the one in the CSV file will be stronger.
	 * If no conversion profile was specified, partner's default will be used
	 * 
	 * @action add
	 * @param int $conversionProfileId Conversion profile id to use for converting the current bulk (-1 to use partner's default)
	 * @param file $csvFileData bulk upload file
	 * @param KalturaBulkUploadType $bulkUploadType
	 * @param string $uploadedBy
	 * @param string $fileName Friendly name of the file, used to be recognized later in the logs.
	 * @return KalturaBulkUpload
	 */
	public function addAction($conversionProfileId, $csvFileData, $bulkUploadType = null, $uploadedBy = null, $fileName = null)
	{
		if($conversionProfileId == self::PARTNER_DEFAULT_CONVERSION_PROFILE_ID)
			$conversionProfileId = $this->getPartner()->getDefaultConversionProfileId();
			
		$conversionProfile = conversionProfile2Peer::retrieveByPK($conversionProfileId);
		if(!$conversionProfile)
			throw new KalturaAPIException(KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND, $conversionProfileId);
		
		$coreBulkUploadType = kPluginableEnumsManager::apiToCore('BulkUploadType', $bulkUploadType);
		
		if(is_null($uploadedBy))
			$uploadedBy = $this->getKuser()->getPuserId();
		
		if(!$fileName)
			$fileName = $csvFileData["name"];
		
		$data = $this->constructJobData($csvFileData["tmp_name"], $fileName, $this->getPartner(), $this->getKuser()->getPuserId(), $uploadedBy, $conversionProfileId, $coreBulkUploadType);
		
		$dbJob = kJobsManager::addBulkUploadJob($this->getPartner(), $data, $coreBulkUploadType);
		$dbJobLog = BatchJobLogPeer::retrieveByBatchJobId($dbJob->getId());
		$bulkUpload = new KalturaBulkUpload();
		$bulkUpload->fromObject($dbJobLog, $this->getResponseProfile());
		
		return $bulkUpload;
	}
	
	/**
	 * Function constructs a core object of type kBulkUploadJobData
	 * @param int $conversionProfileId
	 * @param string $filePath
	 * @param string $userId
	 * @param int $bulkUploadType
	 * @param string $uploadedBy
	 * @param string $fileName
	 * @throws KalturaErrors::CONVERSION_PROFILE_ID_NOT_FOUND
	 */
	protected function constructJobData ($filePath, $fileName, Partner $partner, $puserId, $uploadedBy, $conversionProfileId = null, $coreBulkUploadType = null)
	{
	   $data = KalturaPluginManager::loadObject('kBulkUploadJobData', $coreBulkUploadType);

		if(is_null($data))
		{
			throw new KalturaAPIException(KalturaErrors::BULK_UPLOAD_BULK_UPLOAD_TYPE_NOT_VALID, $coreBulkUploadType);
		}
		
		$data->setFilePath($filePath);
		$data->setUserId($puserId);
		$data->setUploadedBy($uploadedBy);
		$data->setFileName($fileName);
		$data->handleKsPrivileges();

		if (!$conversionProfileId)
		{
			$conversionProfileId = $partner->getDefaultConversionProfileId();
		}
			
		$kmcVersion = $partner->getKmcVersion();
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
		
		$objectData = new kBulkUploadEntryData();
		$objectData->setConversionProfileId($conversionProfileId);
		$data->setObjectData($objectData);
		
		return $data;
	}
	
	/**
	 * Get bulk upload batch job by id
	 *
	 * @action get
	 * @param bigint $id
	 * @return KalturaBulkUpload
	 */
	public function getAction($id)
	{
	    $c = new Criteria();
	    $c->addAnd(BatchJobLogPeer::JOB_ID, $id);
		$c->addAnd(BatchJobLogPeer::PARTNER_ID, $this->getPartnerId());
		$c->addAnd(BatchJobLogPeer::JOB_TYPE, BatchJobType::BULKUPLOAD);
		$batchJob = BatchJobLogPeer::doSelectOne($c);
		
		if (!$batchJob)
		    throw new KalturaAPIException(KalturaErrors::BULK_UPLOAD_NOT_FOUND, $id);
		    
		$ret = new KalturaBulkUpload();
		$ret->fromObject($batchJob, $this->getResponseProfile());
		return $ret;
	}
	
	/**
	 * List bulk upload batch jobs
	 *
	 * @action list
	 * @param KalturaFilterPager $pager
	 * @return KalturaBulkUploadListResponse
	 */
	public function listAction(KalturaFilterPager $pager = null)
	{
	    if (!$pager)
			$pager = new KalturaFilterPager();
			
	    $c = new Criteria();
		$c->addAnd(BatchJobLogPeer::PARTNER_ID, $this->getPartnerId());
		$c->addAnd(BatchJobLogPeer::JOB_TYPE, BatchJobType::BULKUPLOAD);
		$c->addAnd(BatchJobLogPeer::ABORT, 0);
		$c->addDescendingOrderByColumn(BatchJobLogPeer::ID);
		
		$count = BatchJobLogPeer::doCount($c);
		$pager->attachToCriteria($c);
		$jobs = BatchJobLogPeer::doSelect($c);
		
		$response = new KalturaBulkUploadListResponse();
		$response->objects = KalturaBulkUploads::fromBatchJobArray($jobs);
		$response->totalCount = $count; 
		
		return $response;
	}


	/**
	 * serve action return the original file.
	 * @action serve
	 * @param bigint $id job id
	 * @return file
	 * @throws KalturaAPIException
	 */
	public function serveAction($id)
	{
		$c = new Criteria();
		$c->addAnd(BatchJobPeer::ID, $id);
		$c->addAnd(BatchJobPeer::PARTNER_ID, $this->getPartnerId());
		$c->addAnd(BatchJobPeer::JOB_TYPE, BatchJobType::BULKUPLOAD);
		$batchJob = BatchJobPeer::doSelectOne($c);
		
		if (!$batchJob)
			throw new KalturaAPIException(KalturaErrors::BULK_UPLOAD_NOT_FOUND, $id);
			 
		KalturaLog::info("Batch job found for jobid [$id] bulk upload type [". $batchJob->getJobSubType() . "]");
		
		$syncKey = $batchJob->getSyncKey(BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOAD);
		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, true, false);
		
		if (!$fileSync) {
			throw new KalturaAPIException(KalturaErrors::FILE_DOESNT_EXIST, $id);
		}
		
		header("Content-Type: text/plain; charset=UTF-8");

		if($local)
		{
			$filePath = $fileSync->getFullPath();
			$mimeType = kFile::mimeType($filePath);
			return $this->dumpFile($filePath, $mimeType);
		}
		else
		{
			$remoteUrl = kDataCenterMgr::getRedirectExternalUrl($fileSync);
			KalturaLog::info("Redirecting to [$remoteUrl]");
			header("Location: $remoteUrl");
			die;
		}	
	}
	
	
	/**
	 * serveLog action return the original file.
	 * 
	 * @action serveLog
	 * @param bigint $id job id
	 * @return file
	 * 
	 */
	public function serveLogAction($id)
	{
		$c = new Criteria();
		$c->addAnd(BatchJobPeer::ID, $id);
		$c->addAnd(BatchJobPeer::PARTNER_ID, $this->getPartnerId());
		$c->addAnd(BatchJobPeer::JOB_TYPE, BatchJobType::BULKUPLOAD);
		$batchJob = BatchJobPeer::doSelectOne($c);
		
		if (!$batchJob)
			throw new KalturaAPIException(KalturaErrors::BULK_UPLOAD_NOT_FOUND, $id);
			 
		KalturaLog::info("Batch job found for jobid [$id] bulk upload type [". $batchJob->getJobSubType() . "]");
			
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaBulkUpload');
		foreach($pluginInstances as $pluginInstance)
		{
			/* @var $pluginInstance IKalturaBulkUpload */
			$pluginInstance->writeBulkUploadLogFile($batchJob);
		}	
	}
	
	/**
	 * Aborts the bulk upload and all its child jobs
	 * 
	 * @action abort
	 * @param bigint $id job id
	 * @return KalturaBulkUpload
	 */
	public function abortAction($id)
	{
		$c = new Criteria();
		$c->addAnd(BatchJobPeer::ID, $id);
		$c->addAnd(BatchJobPeer::PARTNER_ID, $this->getPartnerId());
		$c->addAnd(BatchJobPeer::JOB_TYPE, BatchJobType::BULKUPLOAD);
		$batchJob = BatchJobPeer::doSelectOne($c);

		if (!$batchJob) {
			$c = new Criteria();
			$c->addAnd(BatchJobLogPeer::JOB_ID, $id);
			$c->addAnd(BatchJobLogPeer::PARTNER_ID, $this->getPartnerId());
			$c->addAnd(BatchJobLogPeer::JOB_TYPE, BatchJobType::BULKUPLOAD);

			$crit = $c->getNewCriterion(BatchJobLogPeer::ABORT, null);
			$critOr = $c->getNewCriterion(BatchJobLogPeer::ABORT, 0);
			$crit->addOr($critOr);
			$c->add($crit);

			$batchJobLog = BatchJobLogPeer::doSelectOne($c);

			if(!$batchJobLog)
				throw new KalturaAPIException(KalturaErrors::BULK_UPLOAD_NOT_FOUND, $id);

			$batchJobLog->setAbort(BatchJobExecutionStatus::ABORTED);
			$batchJobLog->save();
		}
		else {
			kJobsManager::abortJob($id, BatchJobType::BULKUPLOAD, true);
		}

		$batchJobLog = BatchJobLogPeer::retrieveByBatchJobId($id);
		$ret = new KalturaBulkUpload();
		$ret->fromObject($batchJobLog, $this->getResponseProfile());
		return $ret;
	}
}
