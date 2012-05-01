<?php
/**
 * Bulk upload service is used to upload & manage bulk uploads using CSV files
 *
 * @service bulk
 * @package plugins.bulkUpload
 * @subpackage services
 */
class BulkService extends KalturaBaseService implements IAliasService
{
	const PARTNER_DEFAULT_CONVERSION_PROFILE_ID = -1;
	
	const SERVICE_NAME = "bulkUpload";

	/**
	 * Add new bulk upload batch job
	 * Conversion profile id can be specified in the API or in the CSV file, the one in the CSV file will be stronger.
	 * If no conversion profile was specified, partner's default will be used
	 * 
	 * @action addEntries
	 * @actionAlias media.bulkUploadAdd
	 * @param file $fileData
	 * @param KalturaBulkUploadType $bulkUploadType
	 * @param KalturaBulkUploadJobData $bulkUploadData
	 * @return KalturaBulkUpload
	 */
	function addEntriesAction($fileData, KalturaBulkUploadJobData $bulkUploadData = null, KalturaBulkUploadEntryData $bulkUploadEntryData = null)
	{
	    $bulkUploadCoreType = kPluginableEnumsManager::apiToCore("BulkUploadType", $bulkUploadData->type);
		
	    if (!$bulkUploadData)
	    {
	       $bulkUploadData = new KalturaBulkUploadJobData();
	    }
	    
	    if (!$bulkUploadEntryData)
	    {
	        $bulkUploadEntryData = new KalturaBulkUploadEntryData();
	    }
		if(!$bulkUploadData->fileName)
			$bulkUploadEntryData->fileName = $fileData["name"];
		
		$dbBulkUploadJobData = $bulkUploadData->toInsertableObject();
		/* @var $dbBulkUploadJobData kBulkUploadJobData */
		$dbBulkUploadJobData->setBulkUploadObjectType(BulkUploadObjectType::ENTRY);
		$dbBulkUploadJobData->setUserId($this->getKuser()->getPuserId());
		$dbObjectData = $bulkUploadEntryData->toInsertableObject();
		$dbBulkUploadJobData->setObjectData($dbObjectData);
		$dbBulkUploadJobData->setFilePath($fileData["tmp_name"]);
		
		$dbJob = kJobsManager::addBulkUploadJob($this->getPartner(), $dbBulkUploadJobData, $bulkUploadCoreType);
		
		$bulkUpload = new KalturaBulkUpload();
		$bulkUpload->fromObject($dbJob);
		
		return $bulkUpload;
	}
	
	/**
	 * @action addCategories
	 * @actionAlias category.addFromBulkUpload
	 * 
	 * Action adds categories from a bulkupload CSV file
	 * @param file $fileData
	 * @param KalturaBulkUploadJobData $bulkUploadData
	 * @param KalturaBulkUploadCategoryData $bulkUploadCategoryData
	 * @return KalturaBulkUpload
	 */
	public function addCategoriesAction ($fileData, KalturaBulkUploadJobData $bulkUploadData = null, KalturaBulkUploadCategoryData $bulkUploadCategoryData = null)
	{
	    $bulkUploadCoreType = kPluginableEnumsManager::apiToCore("BulkUploadType", $bulkUploadData->type);
	    
	    if (!$bulkUploadData)
	    {
	       $bulkUploadData = new KalturaBulkUploadJobData();
	    }
	    
	    if (!$bulkUploadCategoryData)
	    {
	        $bulkUploadCategoryData = new KalturaBulkUploadCategoryData();
	    }
	    
		if(!$bulkUploadData->fileName)
			$fileName = $fileData["name"];
		
		$dbBulkUploadJobData = $bulkUploadData->toInsertableObject();
		$dbBulkUploadJobData->setBulkUploadObjectType(BulkUploadObjectType::CATEGORY);
		$dbBulkUploadJobData->setUserId($this->getKuser()->getPuserId());
		$dbObjectData = $bulkUploadCategoryData->toInsertableObject();
		$dbBulkUploadJobData->setObjectData($dbObjectData);
		$dbBulkUploadJobData->setFilePath($fileData["tmp_name"]);
		KalturaLog::debug("CSV file path: ". $dbBulkUploadJobData->getFilePath());
		
		$dbJob = kJobsManager::addBulkUploadJob($this->getPartner(), $dbBulkUploadJobData, $bulkUploadCoreType);
		
		$bulkUpload = new KalturaBulkUpload();
		$bulkUpload->fromObject($dbJob);
		
		return $bulkUpload;
	}
	
	/**
	 * @action addCategoryUsers
	 * @actionAlias categoryUser.addFromBulkUpload
	 * Action adds CategoryUsers from a bulkupload CSV file
	 * @param file $fileData
	 * @param KalturaBulkUploadJobData $bulkUploadData
	 * @param KalturaBulkUploadCategoryUserData $bulkUploadCategoryUserData
	 * @return KalturaBulkUpload
	 */
	public function addCategoryUsersAction ($fileData, KalturaBulkUploadJobData $bulkUploadData = null, KalturaBulkUploadCategoryUserData $bulkUploadCategoryUserData = null)
	{
	    $bulkUploadCoreType = kPluginableEnumsManager::apiToCore("BulkUploadType", $bulkUploadData->type);
	    
	    if (!$bulkUploadData)
	    {
	       $bulkUploadData = new KalturaBulkUploadJobData();
	    }
	    
        if ($bulkUploadCategoryUserData)
        {
            $bulkUploadCategoryUserData = new KalturaBulkUploadCategoryUserData();
        }
		
		if(!$bulkUploadData->fileName)
			$fileName = $fileData["name"];
		
		$dbBulkUploadJobData = $bulkUploadData->toInsertableObject();
		$dbBulkUploadJobData->setBulkUploadObjectType(BulkUploadObjectType::CATEGORY_USER);
		$dbBulkUploadJobData->setUserId($this->getKuser()->getPuserId());
		$dbObjectData = $bulkUploadCategoryUserData->toInsertableObject();
		$dbBulkUploadJobData->setObjectData($dbObjectData);
		$dbBulkUploadJobData->setFilePath($fileData["tmp_name"]);
		KalturaLog::debug("CSV file path: ". $dbBulkUploadJobData->getFilePath());
		
		$dbJob = kJobsManager::addBulkUploadJob($this->getPartner(), $dbBulkUploadJobData, $bulkUploadCoreType);
		
		$bulkUpload = new KalturaBulkUpload();
		$bulkUpload->fromObject($dbJob);
		
		return $bulkUpload;
	}
	
	/**
	 * @action addUsers
	 * @actionAlias user.addFromBulkUpload
	 * Action adds users from a bulkupload CSV file
	 * @param file $fileData
	 * @param KalturaBulkUploadJobData $bulkUploadData
	 * @param KalturaBulkUploadUserData $bulkUploadUserData
	 * @return KalturaBulkUpload
	 */
	public function addUsersAction($fileData, KalturaBulkUploadJobData $bulkUploadData = null, KalturaBulkUploadUserData $bulkUploadUserData = null)
	{
	   $bulkUploadCoreType = kPluginableEnumsManager::apiToCore("BulkUploadType", $bulkUploadData->type);
	    
	   if (!$bulkUploadData)
	   {
	       $bulkUploadData = new KalturaBulkUploadJobData();
	   }
	   
	   if (!$bulkUploadUserData)
	   {
	       $bulkUploadUserData = new KalturaBulkUploadUserData();
	   }
		
		if(!$bulkUploadData->fileName)
			$fileName = $fileData["name"];
		
		$dbBulkUploadJobData = $bulkUploadData->toInsertableObject();
		$dbBulkUploadJobData->setBulkUploadObjectType(BulkUploadObjectType::USER);
		$dbBulkUploadJobData->setUserId($this->getKuser()->getPuserId());
		$dbObjectData = $bulkUploadUserData->toInsertableObject();
		$dbBulkUploadJobData->setObjectData($dbObjectData);
		$dbBulkUploadJobData->setFilePath($fileData["tmp_name"]);
		KalturaLog::debug("CSV file path: ". $dbBulkUploadJobData->getFilePath());
		
		$dbJob = kJobsManager::addBulkUploadJob($this->getPartner(), $dbBulkUploadJobData, $bulkUploadCoreType);
		
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
		
		$crit = $c->getNewCriterion(BatchJobPeer::ABORT, null);
		$critOr = $c->getNewCriterion(BatchJobPeer::ABORT, 0);
		$crit->addOr($critOr);
		$c->add($crit);
		
		$c->addDescendingOrderByColumn(BatchJobPeer::ID);
		
		$count = BatchJobPeer::doCount($c);
		$pager->attachToCriteria($c);
		$jobs = BatchJobPeer::doSelect($c);
		
		$response = new KalturaBulkUploadListResponse();
		$response->objects = KalturaBulkUploads::fromBatchJobArray($jobs);
		$response->totalCount = $count; 
		
		return $response;
	}
	
	
	
	
	/**
	 * serve action returan the original file.
	 * 
	 * @action serve
	 * @param int $id job id
	 * @return file
	 * 
	 */
	function serveAction($id)
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
		
		header("Content-Type: text/plain; charset=UTF-8");

		if($local)
		{
			$filePath = $fileSync->getFullPath();
			$mimeType = kFile::mimeType($filePath);
			kFile::dumpFile($filePath, $mimeType);
		}
		else
		{
			$remoteUrl = kDataCenterMgr::getRedirectExternalUrl($fileSync);
			KalturaLog::info("Redirecting to [$remoteUrl]");
			header("Location: $remoteUrl");
		}	
	}
	
	
	/**
	 * serveLog action returan the original file.
	 * 
	 * @action serveLog
	 * @param int $id job id
	 * @return file
	 * 
	 */
	function serveLogAction($id)
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
	 * @param int $id job id
	 * @return KalturaBulkUpload
	 */
	function abortAction($id)
	{
	    $c = new Criteria();
	    $c->addAnd(BatchJobPeer::ID, $id);
		$c->addAnd(BatchJobPeer::PARTNER_ID, $this->getPartnerId());
		$c->addAnd(BatchJobPeer::JOB_TYPE, BatchJobType::BULKUPLOAD);
		$batchJob = BatchJobPeer::doSelectOne($c);
		
	    if (!$batchJob)
		    throw new KalturaAPIException(KalturaErrors::BULK_UPLOAD_NOT_FOUND, $id);
		
		if (!in_array($batchJob->getStatus(), BatchJobPeer::getClosedStatusList()))
		{
		    throw new KalturaAPIException(KalturaErrors::CANNOT_ABORT_BULKUPLOAD_UNFINISHED_JOB, $id);
		}
		    
		kJobsManager::abortJob($id, BatchJobType::BULKUPLOAD, true);
		
		$ret = new KalturaBulkUpload();
		$ret->fromObject($batchJob);
		return $ret;
	}
	
	/* (non-PHPdoc)
     * @see IExtensionService::getPluginName()
     */
    public static function getServiceId ()
    {
        return "bulkUpload_bulkUpload";
    }


}