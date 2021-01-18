<?php
/**
 * Bulk upload service is used to upload & manage bulk uploads
 *
 * @service bulk
 * @package plugins.bulkUpload
 * @subpackage services
 */
class BulkService extends KalturaBaseService
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
		if(get_class($bulkUploadData) == 'KalturaBulkUploadJobData')
			throw new KalturaAPIException(KalturaErrors::OBJECT_TYPE_ABSTRACT, 'KalturaBulkUploadJobData');

		$validContent = myXmlUtils::validateXmlFileContent($fileData['tmp_name']);
		if(!$validContent)
		{
			throw new KalturaAPIException(KalturaErrors::FILE_CONTENT_NOT_SECURE);
		}


	    if($bulkUploadEntryData->conversionProfileId == self::PARTNER_DEFAULT_CONVERSION_PROFILE_ID)
			$bulkUploadEntryData->conversionProfileId = $this->getPartner()->getDefaultConversionProfileId();
	    
	    if (!$bulkUploadData)
	    {
	       $bulkUploadData = KalturaPluginManager::loadObject('KalturaBulkUploadJobData', null);
	    }
	    
	    if (!$bulkUploadEntryData)
	    {
	        $bulkUploadEntryData = new KalturaBulkUploadEntryData();
	    }
		if(!$bulkUploadData->fileName)
			$bulkUploadData->fileName = $fileData["name"];
		
		$dbBulkUploadJobData = $bulkUploadData->toInsertableObject();
		$bulkUploadCoreType = kPluginableEnumsManager::apiToCore("BulkUploadType", $bulkUploadData->type);
		/* @var $dbBulkUploadJobData kBulkUploadJobData */
		$dbBulkUploadJobData->setBulkUploadObjectType(BulkUploadObjectType::ENTRY);
		$dbBulkUploadJobData->setUserId($this->getKuser()->getPuserId());
		$dbObjectData = $bulkUploadEntryData->toInsertableObject();
		$dbBulkUploadJobData->setObjectData($dbObjectData);
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
	    if (!$bulkUploadData)
	    {
	       $bulkUploadData = KalturaPluginManager::loadObject('KalturaBulkUploadJobData', null);
	    }
	    
	    if (!$bulkUploadCategoryData)
	    {
	        $bulkUploadCategoryData = new KalturaBulkUploadCategoryData();
	    }
	    
		if(!$bulkUploadData->fileName)
			$bulkUploadData->fileName = $fileData["name"];
		
		$dbBulkUploadJobData = $bulkUploadData->toInsertableObject();
		$bulkUploadCoreType = kPluginableEnumsManager::apiToCore("BulkUploadType", $bulkUploadData->type);
		
		$dbBulkUploadJobData->setBulkUploadObjectType(BulkUploadObjectType::CATEGORY);
		$dbBulkUploadJobData->setUserId($this->getKuser()->getPuserId());
		$dbObjectData = $bulkUploadCategoryData->toInsertableObject();
		$dbBulkUploadJobData->setObjectData($dbObjectData);
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
	    if (!$bulkUploadData)
	    {
	       $bulkUploadData = KalturaPluginManager::loadObject('KalturaBulkUploadJobData', null);
	    }
	    
        if (!$bulkUploadCategoryUserData)
        {
            $bulkUploadCategoryUserData = new KalturaBulkUploadCategoryUserData();
        }
		
		if(!$bulkUploadData->fileName)
			$bulkUploadData->fileName = $fileData["name"];
		
		$dbBulkUploadJobData = $bulkUploadData->toInsertableObject();
		$bulkUploadCoreType = kPluginableEnumsManager::apiToCore("BulkUploadType", $bulkUploadData->type);
		$dbBulkUploadJobData->setBulkUploadObjectType(BulkUploadObjectType::CATEGORY_USER);
		$dbBulkUploadJobData->setUserId($this->getKuser()->getPuserId());
		$dbObjectData = $bulkUploadCategoryUserData->toInsertableObject();
		$dbBulkUploadJobData->setObjectData($dbObjectData);
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
	   if (!$bulkUploadData)
	   {
	       $bulkUploadData = KalturaPluginManager::loadObject('KalturaBulkUploadJobData', null);
	   }
	   
	   if (!$bulkUploadUserData)
	   {
	       $bulkUploadUserData = new KalturaBulkUploadUserData();
	   }
		
		if(!$bulkUploadData->fileName)
			$bulkUploadData->fileName = $fileData["name"];
		
		$dbBulkUploadJobData = $bulkUploadData->toInsertableObject();
		$bulkUploadCoreType = kPluginableEnumsManager::apiToCore("BulkUploadType", $bulkUploadData->type);
		$dbBulkUploadJobData->setBulkUploadObjectType(BulkUploadObjectType::USER);
		$dbBulkUploadJobData->setUserId($this->getKuser()->getPuserId());
		$dbObjectData = $bulkUploadUserData->toInsertableObject();
		$dbBulkUploadJobData->setObjectData($dbObjectData);
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
	 * @action addCategoryEntries
	 * @actionAlias categoryEntry.addFromBulkUpload
	 * Action adds active category entries
	 * @param KalturaBulkServiceData $bulkUploadData
	 * @param KalturaBulkUploadCategoryEntryData $bulkUploadCategoryEntryData
	 * @return KalturaBulkUpload
	 */
	public function addCategoryEntriesAction (KalturaBulkServiceData $bulkUploadData, KalturaBulkUploadCategoryEntryData $bulkUploadCategoryEntryData = null)
	{
		if($bulkUploadData instanceof  KalturaBulkServiceFilterData){
			if($bulkUploadData->filter instanceof KalturaBaseEntryFilter){
				if(	$bulkUploadData->filter->idEqual == null &&
					$bulkUploadData->filter->idIn == null &&
					$bulkUploadData->filter->categoriesIdsMatchOr == null &&
					$bulkUploadData->filter->categoriesMatchAnd == null &&
					$bulkUploadData->filter->categoriesMatchOr == null &&
					$bulkUploadData->filter->categoriesIdsMatchAnd == null)
						throw new KalturaAPIException(KalturaErrors::MUST_FILTER_ON_ENTRY_OR_CATEGORY);					
			}
			else if($bulkUploadData->filter instanceof KalturaCategoryEntryFilter){
				if(	$bulkUploadData->filter->entryIdEqual == null &&
					$bulkUploadData->filter->categoryIdIn == null &&
					$bulkUploadData->filter->categoryIdEqual == null )
						throw new KalturaAPIException(KalturaErrors::MUST_FILTER_ON_ENTRY_OR_CATEGORY);				
			}
		}
	   	$bulkUploadJobData = KalturaPluginManager::loadObject('KalturaBulkUploadJobData', $bulkUploadData->getType());
	   	$bulkUploadData->toBulkUploadJobData($bulkUploadJobData);
	    
        if (!$bulkUploadCategoryEntryData)
        {
            $bulkUploadCategoryEntryData = new KalturaBulkUploadCategoryEntryData();
        }
				
		$dbBulkUploadJobData = $bulkUploadJobData->toInsertableObject();
		$bulkUploadCoreType = kPluginableEnumsManager::apiToCore("BulkUploadType", $bulkUploadJobData->type);
		$dbBulkUploadJobData->setBulkUploadObjectType(BulkUploadObjectType::CATEGORY_ENTRY);
		$dbBulkUploadJobData->setUserId($this->getKuser()->getPuserId());
		$dbObjectData = $bulkUploadCategoryEntryData->toInsertableObject();
		$dbBulkUploadJobData->setObjectData($dbObjectData);
		
		$dbJob = kJobsManager::addBulkUploadJob($this->getPartner(), $dbBulkUploadJobData, $bulkUploadCoreType);
		$dbJobLog = BatchJobLogPeer::retrieveByBatchJobId($dbJob->getId());
		if(!$dbJobLog)
			return null;
		
		$bulkUpload = new KalturaBulkUpload();
		$bulkUpload->fromObject($dbJobLog, $this->getResponseProfile());
		
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
	 * @param KalturaBulkUploadFilter $bulkUploadFilter
	 * @param KalturaFilterPager $pager
	 * @return KalturaBulkUploadListResponse
	 */
	function listAction(KalturaBulkUploadFilter $bulkUploadFilter = null, KalturaFilterPager $pager = null)
	{
		if (!$bulkUploadFilter)
		$bulkUploadFilter = new KalturaBulkUploadFilter();
	    
		if (!$pager)
			$pager = new KalturaFilterPager();
		
 		$response = new KalturaBulkUploadListResponse();

		$coreBulkUploadFilter = new BatchJobLogFilter();
        	$bulkUploadFilter->toObject($coreBulkUploadFilter);
			
	    	$c = new Criteria();
		
		// when filtering the last hour logs, limit list to last 30K records in order to constrain query performance
		if ($bulkUploadFilter->uploadedOnGreaterThanOrEqual)// && $bulkUploadFilter->uploadedOnGreaterThanOrEqual > time() - 3600)
		{
			$c2 = new Criteria();
			$c2->addDescendingOrderByColumn(BatchJobLogPeer::ID);
			$lastLog = BatchJobLogPeer::doSelectOne($c2);
			if (!$lastLog)
				return $response;

			$lastId = $lastLog->getId() - 300000;
			$c->addAnd(BatchJobLogPeer::ID, $lastId, Criteria::GREATER_THAN);
		}
		
		$c->addAnd(BatchJobLogPeer::PARTNER_ID, $this->getPartnerId());
		$c->addAnd(BatchJobLogPeer::JOB_TYPE, BatchJobType::BULKUPLOAD);
		
		$crit = $c->getNewCriterion(BatchJobLogPeer::ABORT, null);
		$critOr = $c->getNewCriterion(BatchJobLogPeer::ABORT, 0);
		$crit->addOr($critOr);
		$c->add($crit);
		
		$c->addDescendingOrderByColumn(BatchJobLogPeer::ID);
		
		$coreBulkUploadFilter->attachToCriteria($c);
		$count = BatchJobLogPeer::doCount($c);
		$pager->attachToCriteria($c);
		$jobs = BatchJobLogPeer::doSelect($c);
		
		$response->objects = KalturaBulkUploads::fromBatchJobArray($jobs);
		$response->totalCount = $count; 
		
		return $response;
	}
	
	
	
	
	/**
	 * serve action returns the original file.
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
	 * serveLog action returns the log file for the bulk-upload job.
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
		
		kJobsManager::abortJob($id, BatchJobType::BULKUPLOAD, true);
		
		$batchJobLog = BatchJobLogPeer::retrieveByBatchJobId($id);
		
		$ret = new KalturaBulkUpload();
		if ($batchJobLog)
    		$ret->fromObject($batchJobLog, $this->getResponseProfile());
    	
    	return $ret;
	}

	/**
	 * @action updateCategoryEntriesStatus
	 * @actionAlias categoryEntry.updateStatusFromBulk
	 * Action activate or rejects categoryEntry objects from a bulkupload CSV file
	 * @param file $fileData
	 * @param KalturaBulkUploadJobData $bulkUploadData
	 * @param KalturaBulkUploadCategoryEntryData $bulkUploadCategoryEntryData
	 * @return KalturaBulkUpload
	 */
	public function updateCategoryEntriesStatusAction($fileData, KalturaBulkUploadJobData $bulkUploadData = null, KalturaBulkUploadCategoryEntryData $bulkUploadCategoryEntryData = null)
	{
		if (!$bulkUploadData)
		{
			$bulkUploadData = KalturaPluginManager::loadObject('KalturaBulkUploadJobData', null);
		}

		if (!$bulkUploadCategoryEntryData)
		{
			$bulkUploadCategoryEntryData = new KalturaBulkUploadCategoryEntryData();
		}

		if(!$bulkUploadData->fileName)
		{
			$bulkUploadData->fileName = $fileData["name"];
		}

		$dbBulkUploadJobData = $bulkUploadData->toInsertableObject();
		$bulkUploadCoreType = kPluginableEnumsManager::apiToCore("BulkUploadType", $bulkUploadData->type);
		$dbBulkUploadJobData->setBulkUploadObjectType(BulkUploadObjectType::CATEGORY_ENTRY);
		$dbBulkUploadJobData->setUserId($this->getKuser()->getPuserId());
		$dbObjectData = $bulkUploadCategoryEntryData->toInsertableObject();
		$dbBulkUploadJobData->setObjectData($dbObjectData);
		$dbBulkUploadJobData->setFilePath($fileData["tmp_name"]);

		$dbJob = kJobsManager::addBulkUploadJob($this->getPartner(), $dbBulkUploadJobData, $bulkUploadCoreType);
		$dbJobLog = BatchJobLogPeer::retrieveByBatchJobId($dbJob->getId());
		if(!$dbJobLog)
		{
			return null;
		}

		$bulkUpload = new KalturaBulkUpload();
		$bulkUpload->fromObject($dbJobLog, $this->getResponseProfile());

		return $bulkUpload;
	}

	/**
	 * @action userEntryBulkDelete
	 * @actionAlias userEntry.bulkDelete
	 * Action delete userEntry objects from filter in bulk
	 * @param KalturaUserEntryFilter $filter
	 * @throws KalturaErrors::FAILED_TO_CREATE_BULK_DELETE
	 * @return int
	 */
	public function userEntryBulkDeleteAction(KalturaUserEntryFilter $filter)
	{
		$bulkUploadData = new KalturaBulkServiceFilterDataBase();
		$bulkUploadData->filter = $filter;
		$bulkUploadObjectType = BulkUploadObjectType::USER_ENTRY;
		$bulkUpload = $this->bulkDelete($bulkUploadData, $bulkUploadObjectType);
		return $bulkUpload->id;
	}

	protected function bulkDelete(KalturaBulkServiceFilterDataBase $bulkUploadData, $bulkUploadObjectType)
	{
		$bulkUploadJobData = KalturaPluginManager::loadObject('KalturaBulkUploadJobData', $bulkUploadData->getType());
		$bulkUploadData->toBulkUploadJobData($bulkUploadJobData);

		$dbBulkUploadJobData = $bulkUploadJobData->toInsertableObject();
		$bulkUploadCoreType = kPluginableEnumsManager::apiToCore("BulkUploadType", $bulkUploadJobData->type);
		$dbBulkUploadJobData->setBulkUploadObjectType($bulkUploadObjectType);
		$dbBulkUploadJobData->setUserId($this->getKuser()->getPuserId());

		$dbJob = kJobsManager::addBulkUploadJob($this->getPartner(), $dbBulkUploadJobData, $bulkUploadCoreType);
		$dbJobLog = BatchJobLogPeer::retrieveByBatchJobId($dbJob->getId());
		if(!$dbJobLog)
		{
			throw new KalturaAPIException(KalturaErrors::FAILED_TO_CREATE_BULK_DELETE);
		}

		$bulkUpload = new KalturaBulkUpload();
		$bulkUpload->fromObject($dbJobLog, $this->getResponseProfile());

		return $bulkUpload;
	}


}
