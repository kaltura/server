<?php
/**
 * batch service lets you handle different batch process from remote machines.
 * As oppesed to other ojects in the system, locking mechanism is critical in this case.
 * For this reason the GetExclusiveXX, UpdateExclusiveXX and FreeExclusiveXX actions are important for the system's intergity.
 * In general - updating batch object should be done only using the UpdateExclusiveXX which in turn can be called only after 
 * acuiring a batch objet properly (using  GetExclusiveXX).
 * If an object was aquired and should be returned to the pool in it's initial state - use the FreeExclusiveXX action 
 *
 *	Terminology:
 *		LocationId
 *		ServerID
 *		ParternGroups 
 * 
 * @service jobs
 * @package api
 * @subpackage services
 */
class JobsService extends KalturaBaseService 
{
	// use initService to add a peer to the partner filter
	/**
	 * @ignore
	 */
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		if($this->getPartnerId() != Partner::ADMIN_CONSOLE_PARTNER_ID)
			parent::applyPartnerFilterForClass ( new BatchJobPeer() ); 	
	}
	
	
// --------------------------------- ImportJob functions 	--------------------------------- //
	
	
	/**
	 * batch getImportStatusAction returns the status of import task
	 * 
	 * @action getImportStatus
	 * @param int $jobId the id of the import job  
	 * @return KalturaBatchJobResponse 
	 */
	function getImportStatusAction($jobId)
	{
		return $this->getStatusAction($jobId, KalturaBatchJobType::IMPORT);
	}
	
	
	/**
	 * batch deleteImportAction deletes and returns the status of import task
	 * 
	 * @action deleteImport
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function deleteImportAction($jobId)
	{
		return $this->deleteJobAction($jobId, KalturaBatchJobType::IMPORT);
	}
	
	
	/**
	 * batch abortImportAction aborts and returns the status of import task
	 * 
	 * @action abortImport
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function abortImportAction($jobId)
	{
		return $this->abortJobAction($jobId, KalturaBatchJobType::IMPORT);
	}
	
	
	/**
	 * batch retryImportAction retrys and returns the status of import task
	 * 
	 * @action retryImport
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function retryImportAction($jobId)
	{
		return $this->retryJobAction($jobId, KalturaBatchJobType::IMPORT);
	}
	
	/**
// --------------------------------- ImportJob functions 	--------------------------------- //

	
	
	
// --------------------------------- ProvisionProvideJob functions 	--------------------------------- //
	
	
	/**
	 * batch getProvisionProvideStatusAction returns the status of ProvisionProvide task
	 * 
	 * @action getProvisionProvideStatus
	 * @param int $jobId the id of the ProvisionProvide job  
	 * @return KalturaBatchJobResponse 
	 */
	function getProvisionProvideStatusAction($jobId)
	{
		return $this->getStatusAction($jobId, KalturaBatchJobType::PROVISION_PROVIDE);
	}
	
	
	/**
	 * batch deleteProvisionProvideAction deletes and returns the status of ProvisionProvide task
	 * 
	 * @action deleteProvisionProvide
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function deleteProvisionProvideAction($jobId)
	{
		return $this->deleteJobAction($jobId, KalturaBatchJobType::PROVISION_PROVIDE);
	}
	
	
	/**
	 * batch abortProvisionProvideAction aborts and returns the status of ProvisionProvide task
	 * 
	 * @action abortProvisionProvide
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function abortProvisionProvideAction($jobId)
	{
		return $this->abortJobAction($jobId, KalturaBatchJobType::PROVISION_PROVIDE);
	}
	
	
	/**
	 * batch retryProvisionProvideAction retrys and returns the status of ProvisionProvide task
	 * 
	 * @action retryProvisionProvide
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function retryProvisionProvideAction($jobId)
	{
		return $this->retryJobAction($jobId, KalturaBatchJobType::PROVISION_PROVIDE);
	}
	
	/**
// --------------------------------- ProvisionProvideJob functions 	--------------------------------- //

	
	
// --------------------------------- ProvisionDeleteJob functions 	--------------------------------- //
	
	
	/**
	 * batch getProvisionDeleteStatusAction returns the status of ProvisionDelete task
	 * 
	 * @action getProvisionDeleteStatus
	 * @param int $jobId the id of the ProvisionDelete job  
	 * @return KalturaBatchJobResponse 
	 */
	function getProvisionDeleteStatusAction($jobId)
	{
		return $this->getStatusAction($jobId, KalturaBatchJobType::PROVISION_DELETE);
	}
	
	
	/**
	 * batch deleteProvisionDeleteAction deletes and returns the status of ProvisionDelete task
	 * 
	 * @action deleteProvisionDelete
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function deleteProvisionDeleteAction($jobId)
	{
		return $this->deleteJobAction($jobId, KalturaBatchJobType::PROVISION_DELETE);
	}
	
	
	/**
	 * batch abortProvisionDeleteAction aborts and returns the status of ProvisionDelete task
	 * 
	 * @action abortProvisionDelete
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function abortProvisionDeleteAction($jobId)
	{
		return $this->abortJobAction($jobId, KalturaBatchJobType::PROVISION_DELETE);
	}
	
	
	/**
	 * batch retryProvisionDeleteAction retrys and returns the status of ProvisionDelete task
	 * 
	 * @action retryProvisionDelete
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function retryProvisionDeleteAction($jobId)
	{
		return $this->retryJobAction($jobId, KalturaBatchJobType::PROVISION_DELETE);
	}
	
	/**
// --------------------------------- ProvisionDeleteJob functions 	--------------------------------- //

	
	
// --------------------------------- BulkUploadJob functions 	--------------------------------- //
	
	
	/**
	 * batch getBulkUploadStatusAction returns the status of bulk upload task
	 * 
	 * @action getBulkUploadStatus
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function getBulkUploadStatusAction($jobId)
	{
		return $this->getStatusAction($jobId, KalturaBatchJobType::BULKUPLOAD);
	}
	
	
	/**
	 * batch deleteBulkUploadAction deletes and returns the status of bulk upload task
	 * 
	 * @action deleteBulkUpload
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function deleteBulkUploadAction($jobId)
	{
		return $this->deleteJobAction($jobId, KalturaBatchJobType::BULKUPLOAD);
	}
	
	
	/**
	 * batch abortBulkUploadAction aborts and returns the status of bulk upload task
	 * 
	 * @action abortBulkUpload
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function abortBulkUploadAction($jobId)
	{
		return $this->abortJobAction($jobId, KalturaBatchJobType::BULKUPLOAD);
	}
	
	
	/**
	 * batch retryBulkUploadAction retrys and returns the status of bulk upload task
	 * 
	 * @action retryBulkUpload
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function retryBulkUploadAction($jobId)
	{
		return $this->retryJobAction($jobId, KalturaBatchJobType::BULKUPLOAD);
	}
	

	
// --------------------------------- BulkUploadJob functions 	--------------------------------- //

	
	
// --------------------------------- ConvertJob functions 	--------------------------------- //

	
	
	/**
	 * batch getConvertStatusAction returns the status of convert task
	 * 
	 * @action getConvertStatus
	 * @param int $jobId the id of the convert job  
	 * @return KalturaBatchJobResponse 
	 */
	function getConvertStatusAction($jobId)
	{
		return $this->getStatusAction($jobId, KalturaBatchJobType::CONVERT);
	}
	
	
	
	/**
	 * batch getConvertCollectionStatusAction returns the status of convert task
	 * 
	 * @action getConvertCollectionStatus
	 * @param int $jobId the id of the convert profile job  
	 * @return KalturaBatchJobResponse 
	 */
	function getConvertCollectionStatusAction($jobId)
	{
		return $this->getStatusAction($jobId, KalturaBatchJobType::CONVERT_COLLECTION);
	}
	
	
	
	/**
	 * batch getConvertProfileStatusAction returns the status of convert task
	 * 
	 * @action getConvertProfileStatus
	 * @param int $jobId the id of the convert profile job  
	 * @return KalturaBatchJobResponse 
	 */
	function getConvertProfileStatusAction($jobId)
	{
		return $this->getStatusAction($jobId, KalturaBatchJobType::CONVERT_PROFILE);
	}
	
	
	
	/**
	 * batch addConvertProfileJobAction creates a new convert profile job
	 * 
	 * @action addConvertProfileJob
	 * @param string $entryId the id of the entry to be reconverted  
	 * @return KalturaBatchJobResponse 
	 */
	function addConvertProfileJobAction($entryId)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry)
			throw new KalturaAPIException(APIErrors::INVALID_ENTRY_ID, 'entry', $entryId);
			
		$flavorAsset = flavorAssetPeer::retrieveOriginalByEntryId($entryId);
		if(!$flavorAsset)
			throw new KalturaAPIException(APIErrors::INVALID_FLAVOR_ASSET_ID);
		
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		if(!kFileSyncUtils::file_exists($syncKey, true))
			throw new KalturaAPIException(APIErrors::NO_FILES_RECEIVED);
			
		$inputFileSyncLocalPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
		
		$batchJob = kJobsManager::addConvertProfileJob(null, $entry, $flavorAsset->getId(), $inputFileSyncLocalPath);
		
		return $this->getStatusAction($batchJob->getId(), KalturaBatchJobType::CONVERT_PROFILE);
	}
	
	
	/**
	 * batch getRemoteConvertStatusAction returns the status of convert task
	 * 
	 * @action getRemoteConvertStatus
	 * @param int $jobId the id of the remote convert job  
	 * @return KalturaBatchJobResponse 
	 */
	function getRemoteConvertStatusAction($jobId)
	{
		return $this->getStatusAction($jobId, KalturaBatchJobType::REMOTE_CONVERT);
	}

	
	/**
	 * batch deleteConvertAction deletes and returns the status of convert task
	 * 
	 * @action deleteConvert
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function deleteConvertAction($jobId)
	{
		return $this->deleteJobAction($jobId, KalturaBatchJobType::CONVERT);
	}

	
	/**
	 * batch abortConvertAction aborts and returns the status of convert task
	 * 
	 * @action abortConvert
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function abortConvertAction($jobId)
	{
		return $this->abortJobAction($jobId, KalturaBatchJobType::CONVERT);
	}

	
	/**
	 * batch retryConvertAction retrys and returns the status of convert task
	 * 
	 * @action retryConvert
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function retryConvertAction($jobId)
	{
		return $this->retryJobAction($jobId, KalturaBatchJobType::CONVERT);
	}

	
	/**
	 * batch deleteRemoteConvertAction deletes and returns the status of remote convert task
	 * 
	 * @action deleteRemoteConvert
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function deleteRemoteConvertAction($jobId)
	{
		return $this->deleteJobAction($jobId, KalturaBatchJobType::REMOTE_CONVERT);
	}

	
	/**
	 * batch abortRemoteConvertAction aborts and returns the status of remote convert task
	 * 
	 * @action abortRemoteConvert
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function abortRemoteConvertAction($jobId)
	{
		return $this->abortJobAction($jobId, KalturaBatchJobType::REMOTE_CONVERT);
	}

	
	/**
	 * batch retryRemoteConvertAction retrys and returns the status of remote convert task
	 * 
	 * @action retryRemoteConvert
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function retryRemoteConvertAction($jobId)
	{
		return $this->retryJobAction($jobId, KalturaBatchJobType::REMOTE_CONVERT);
	}

	
	/**
	 * batch deleteConvertCollectionAction deletes and returns the status of convert profile task
	 * 
	 * @action deleteConvertCollection
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function deleteConvertCollectionAction($jobId)
	{
		return $this->deleteJobAction($jobId, KalturaBatchJobType::CONVERT_COLLECTION);
	}

	
	/**
	 * batch deleteConvertProfileAction deletes and returns the status of convert profile task
	 * 
	 * @action deleteConvertProfile
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function deleteConvertProfileAction($jobId)
	{
		return $this->deleteJobAction($jobId, KalturaBatchJobType::CONVERT_PROFILE);
	}

	
	/**
	 * batch abortConvertCollectionAction aborts and returns the status of convert profile task
	 * 
	 * @action abortConvertCollection
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function abortConvertCollectionAction($jobId)
	{
		return $this->abortJobAction($jobId, KalturaBatchJobType::CONVERT_COLLECTION);
	}

	
	/**
	 * batch abortConvertProfileAction aborts and returns the status of convert profile task
	 * 
	 * @action abortConvertProfile
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function abortConvertProfileAction($jobId)
	{
		return $this->abortJobAction($jobId, KalturaBatchJobType::CONVERT_PROFILE);
	}

	
	/**
	 * batch retryConvertCollectionAction retrys and returns the status of convert profile task
	 * 
	 * @action retryConvertCollection
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function retryConvertCollectionAction($jobId)
	{
		return $this->retryJobAction($jobId, KalturaBatchJobType::CONVERT_COLLECTION);
	}

	
	/**
	 * batch retryConvertProfileAction retrys and returns the status of convert profile task
	 * 
	 * @action retryConvertProfile
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function retryConvertProfileAction($jobId)
	{
		return $this->retryJobAction($jobId, KalturaBatchJobType::CONVERT_PROFILE);
	}
	
// --------------------------------- ConvertJob functions 	--------------------------------- //

	
	
// --------------------------------- PostConvertJob functions 	--------------------------------- //

	
	/**
	 * batch getPostConvertStatusAction returns the status of post convert task
	 * 
	 * @action getPostConvertStatus
	 * @param int $jobId the id of the post convert job  
	 * @return KalturaBatchJobResponse 
	 */
	function getPostConvertStatusAction($jobId)
	{
		return $this->getStatusAction($jobId, KalturaBatchJobType::POSTCONVERT);
	}
	
	
	/**
	 * batch deletePostConvertAction deletes and returns the status of post convert task
	 * 
	 * @action deletePostConvert
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function deletePostConvertAction($jobId)
	{
		return $this->deleteJobAction($jobId, KalturaBatchJobType::POSTCONVERT);
	}
	
	
	/**
	 * batch abortPostConvertAction aborts and returns the status of post convert task
	 * 
	 * @action abortPostConvert
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function abortPostConvertAction($jobId)
	{
		return $this->abortJobAction($jobId, KalturaBatchJobType::POSTCONVERT);
	}
	
	
	/**
	 * batch retryPostConvertAction retrys and returns the status of post convert task
	 * 
	 * @action retryPostConvert
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function retryPostConvertAction($jobId)
	{
		return $this->retryJobAction($jobId, KalturaBatchJobType::POSTCONVERT);
	}
	

// --------------------------------- PostConvertJob functions 	--------------------------------- //

// --------------------------------- CaptureThumbJob functions 	--------------------------------- //

	
	/**
	 * batch getCaptureThumbStatusAction returns the status of capture thumbnail task
	 * 
	 * @action getCaptureThumbStatus
	 * @param int $jobId the id of the capture thumbnail job  
	 * @return KalturaBatchJobResponse 
	 */
	function getCaptureThumbStatusAction($jobId)
	{
		return $this->getStatusAction($jobId, KalturaBatchJobType::CAPTURE_THUMB);
	}
	
	
	/**
	 * batch deleteCaptureThumbAction deletes and returns the status of capture thumbnail task
	 * 
	 * @action deleteCaptureThumb
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function deleteCaptureThumbAction($jobId)
	{
		return $this->deleteJobAction($jobId, KalturaBatchJobType::CAPTURE_THUMB);
	}
	
	
	/**
	 * batch abortCaptureThumbAction aborts and returns the status of capture thumbnail task
	 * 
	 * @action abortCaptureThumb
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function abortCaptureThumbAction($jobId)
	{
		return $this->abortJobAction($jobId, KalturaBatchJobType::CAPTURE_THUMB);
	}
	
	
	/**
	 * batch retryCaptureThumbAction retrys and returns the status of capture thumbnail task
	 * 
	 * @action retryCaptureThumb
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function retryCaptureThumbAction($jobId)
	{
		return $this->retryJobAction($jobId, KalturaBatchJobType::CAPTURE_THUMB);
	}
	

// --------------------------------- CaptureThumbJob functions 	--------------------------------- //
	
	
// --------------------------------- PullJob functions 	--------------------------------- //

	
	
	/**
	 * batch getPullStatusAction returns the status of pull task
	 * 
	 * @action getPullStatus
	 * @param int $jobId the id of the pull job  
	 * @return KalturaBatchJobResponse 
	 */
	function getPullStatusAction($jobId)
	{
		return $this->getStatusAction($jobId, KalturaBatchJobType::PULL);
	}
	
	
	/**
	 * batch deletePullAction deletes and returns the status of pull task
	 * 
	 * @action deletePull
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function deletePullAction($jobId)
	{
		return $this->deleteJobAction($jobId, KalturaBatchJobType::PULL);
	}
	
	
	/**
	 * batch abortPullAction aborts and returns the status of pull task
	 * 
	 * @action abortPull
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function abortPullAction($jobId)
	{
		return $this->abortJobAction($jobId, KalturaBatchJobType::PULL);
	}
	
	
	/**
	 * batch retryPullAction retrys and returns the status of pull task
	 * 
	 * @action retryPull
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function retryPullAction($jobId)
	{
		return $this->retryJobAction($jobId, KalturaBatchJobType::PULL);
	}
	

// --------------------------------- PullJob functions 	--------------------------------- //
	
	
// --------------------------------- ExtractMediaJob functions 	--------------------------------- //
	
	
	/**
	 * batch getExtractMediaStatusAction returns the status of extract media task
	 * 
	 * @action getExtractMediaStatus
	 * @param int $jobId the id of the extract media job  
	 * @return KalturaBatchJobResponse 
	 */
	function getExtractMediaStatusAction($jobId)
	{
		return $this->getStatusAction($jobId, KalturaBatchJobType::EXTRACT_MEDIA);
	}
	
	
	/**
	 * batch deleteExtractMediaAction deletes and returns the status of extract media task
	 * 
	 * @action deleteExtractMedia
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function deleteExtractMediaAction($jobId)
	{
		return $this->deleteJobAction($jobId, KalturaBatchJobType::EXTRACT_MEDIA);
	}
	
	
	/**
	 * batch abortExtractMediaAction aborts and returns the status of extract media task
	 * 
	 * @action abortExtractMedia
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function abortExtractMediaAction($jobId)
	{
		return $this->abortJobAction($jobId, KalturaBatchJobType::EXTRACT_MEDIA);
	}
	
	
	/**
	 * batch retryExtractMediaAction retrys and returns the status of extract media task
	 * 
	 * @action retryExtractMedia
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function retryExtractMediaAction($jobId)
	{
		return $this->retryJobAction($jobId, KalturaBatchJobType::EXTRACT_MEDIA);
	}
	

	
	
// --------------------------------- ExtractMediaJob functions 	--------------------------------- //
	
// --------------------------------- StorageExportJob functions 	--------------------------------- //
	
	
	/**
	 * batch getStorageExportStatusAction returns the status of export task
	 * 
	 * @action getStorageExportStatus
	 * @param int $jobId the id of the export job  
	 * @return KalturaBatchJobResponse 
	 */
	function getStorageExportStatusAction($jobId)
	{
		return $this->getStatusAction($jobId, KalturaBatchJobType::STORAGE_EXPORT);
	}
	
	
	/**
	 * batch deleteStorageExportAction deletes and returns the status of export task
	 * 
	 * @action deleteStorageExport
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function deleteStorageExportAction($jobId)
	{
		return $this->deleteJobAction($jobId, KalturaBatchJobType::STORAGE_EXPORT);
	}
	
	
	/**
	 * batch abortStorageExportAction aborts and returns the status of export task
	 * 
	 * @action abortStorageExport
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function abortStorageExportAction($jobId)
	{
		return $this->abortJobAction($jobId, KalturaBatchJobType::STORAGE_EXPORT);
	}
	
	
	/**
	 * batch retryStorageExportAction retrys and returns the status of export task
	 * 
	 * @action retryStorageExport
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function retryStorageExportAction($jobId)
	{
		return $this->retryJobAction($jobId, KalturaBatchJobType::STORAGE_EXPORT);
	}
	

	
	
// --------------------------------- StorageExportJob functions 	--------------------------------- //
	
// --------------------------------- StorageDeleteJob functions 	--------------------------------- //
	
	
	/**
	 * batch getStorageDeleteStatusAction returns the status of export task
	 * 
	 * @action getStorageDeleteStatus
	 * @param int $jobId the id of the export job  
	 * @return KalturaBatchJobResponse 
	 */
	function getStorageDeleteStatusAction($jobId)
	{
		return $this->getStatusAction($jobId, KalturaBatchJobType::STORAGE_DELETE);
	}
	
	
	/**
	 * batch deleteStorageDeleteAction deletes and returns the status of export task
	 * 
	 * @action deleteStorageDelete
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function deleteStorageDeleteAction($jobId)
	{
		return $this->deleteJobAction($jobId, KalturaBatchJobType::STORAGE_DELETE);
	}
	
	
	/**
	 * batch abortStorageDeleteAction aborts and returns the status of export task
	 * 
	 * @action abortStorageDelete
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function abortStorageDeleteAction($jobId)
	{
		return $this->abortJobAction($jobId, KalturaBatchJobType::STORAGE_DELETE);
	}
	
	
	/**
	 * batch retryStorageDeleteAction retrys and returns the status of export task
	 * 
	 * @action retryStorageDelete
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function retryStorageDeleteAction($jobId)
	{
		return $this->retryJobAction($jobId, KalturaBatchJobType::STORAGE_DELETE);
	}
	

	
	
// --------------------------------- StorageDeleteJob functions 	--------------------------------- //
	
// --------------------------------- ImportJob functions 	--------------------------------- //
	
	/**
	 * batch getNotificationStatusAction returns the status of Notification task
	 * 
	 * @action getNotificationStatus
	 * @param int $jobId the id of the Notification job  
	 * @return KalturaBatchJobResponse 
	 */
	function getNotificationStatusAction($jobId)
	{
		return $this->getStatusAction($jobId, KalturaBatchJobType::NOTIFICATION);
	}
	
	
	/**
	 * batch deleteNotificationAction deletes and returns the status of notification task
	 * 
	 * @action deleteNotification
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function deleteNotificationAction($jobId)
	{
		return $this->deleteJobAction($jobId, KalturaBatchJobType::NOTIFICATION);
	}
	
	
	/**
	 * batch abortNotificationAction aborts and returns the status of notification task
	 * 
	 * @action abortNotification
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function abortNotificationAction($jobId)
	{
		return $this->abortJobAction($jobId, KalturaBatchJobType::NOTIFICATION);
	}
	
	
	/**
	 * batch retryNotificationAction retrys and returns the status of notification task
	 * 
	 * @action retryNotification
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function retryNotificationAction($jobId)
	{
		return $this->retryJobAction($jobId, KalturaBatchJobType::NOTIFICATION);
	}
	
	
// --------------------------------- Notification functions 	--------------------------------- //


	
// --------------------------------- MailJob functions 	--------------------------------- //	
	
	
	/**
	 * batch getMailStatusAction returns the status of mail task
	 * 
	 * @action getMailStatus
	 * @param int $jobId the id of the mail job  
	 * @return KalturaBatchJobResponse 
	 */
	function getMailStatusAction($jobId)
	{
		return $this->getStatusAction($jobId, KalturaBatchJobType::MAIL);
	}
	
	
	/**
	 * batch deleteMailAction deletes and returns the status of mail task
	 * 
	 * @action deleteMail
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function deleteMailAction($jobId)
	{
		return $this->deleteJobAction($jobId, KalturaBatchJobType::MAIL);
	}
	
	
	/**
	 * batch abortMailAction aborts and returns the status of mail task
	 * 
	 * @action abortMail
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function abortMailAction($jobId)
	{
		return $this->abortJobAction($jobId, KalturaBatchJobType::MAIL);
	}
	
	
	/**
	 * batch retryMailAction retrys and returns the status of mail task
	 * 
	 * @action retryMail
	 * @param int $jobId the id of the bulk upload job  
	 * @return KalturaBatchJobResponse 
	 */
	function retryMailAction($jobId)
	{
		return $this->retryJobAction($jobId, KalturaBatchJobType::MAIL);
	}
	
	/**
	 * Adds new mail job
	 * 
	 * @action addMailJob
	 * @param KalturaMailJobData $mailJobData
	 */
	function addMailJobAction(KalturaMailJobData $mailJobData)
	{
		$mailJobData->validatePropertyNotNull("mailType");
		$mailJobData->validatePropertyNotNull("recipientEmail");
		
		if (is_null($mailJobData->mailPriority))
			$mailJobData->mailPriority = kMailJobData::MAIL_PRIORITY_NORMAL;
			
		if (is_null($mailJobData->fromEmail))
			$mailJobData->fromEmail = kConf::get("default_email");

		if (is_null($mailJobData->fromName))
			$mailJobData->fromName = kConf::get("default_email_name");
			
		$batchJob = new BatchJob();
		$batchJob->setPartnerId($this->getPartnerId());
		
		$mailJobDataDb = $mailJobData->toObject(new kMailJobData());
			
		kJobsManager::addJob($batchJob, $mailJobDataDb, BatchJobType::MAIL, $mailJobDataDb->getMailType());
	}
	
// --------------------------------- MailJob functions 	--------------------------------- //
	
		
// --------------------------------- generic functions 	--------------------------------- //
	
	
	/**
	 * batch addBatchJob action allows to add a generic BatchJob 
	 * 
	 * @action addBatchJob
	 * @param KalturaBatchJob $batchJob  
	 * @return KalturaBatchJob 
	 */
	function addBatchJobAction(KalturaBatchJob $batchJob)
	{
		kJobsManager::addJob($batchJob->toObject(), $batchJob->data, $batchJob->jobType, $batchJob->jobSubType);	
	}

	
	
	/**
	 * batch getStatusAction returns the status of task
	 * 
	 * @action getStatus
	 * @param int $jobId the id of the job  
	 * @param KalturaBatchJobType $jobType the type of the job  
	 * @return KalturaBatchJobResponse 
	 */
	function getStatusAction($jobId, $jobType)
	{
		$dbJobType = kPluginableEnumsManager::apiToCore('BatchJobType', $jobType);
		
		$dbBatchJob = BatchJobPeer::retrieveByPK($jobId);
		if($dbBatchJob->getJobType() != $dbJobType)
			throw new KalturaAPIException(APIErrors::GET_EXCLUSIVE_JOB_WRONG_TYPE, $jobType, $dbBatchJob->getId());
		
		$job = new KalturaBatchJob();
		$job->fromObject($dbBatchJob);
		
		$batchJobResponse = new KalturaBatchJobResponse();
		$batchJobResponse->batchJob = $job;
		
		$childBatchJobs = $dbBatchJob->getChildJobs();
		$batchJobResponse->childBatchJobs = KalturaBatchJobArray::fromBatchJobArray($childBatchJobs);
		
		return $batchJobResponse;
	}

	
	
	/**
	 * batch deleteJobAction deletes and returns the status of task
	 * 
	 * @action deleteJob
	 * @param int $jobId the id of the job  
	 * @param KalturaBatchJobType $jobType the type of the job  
	 * @return KalturaBatchJobResponse 
	 */
	function deleteJobAction($jobId, $jobType)
	{
		$dbJobType = kPluginableEnumsManager::apiToCore('BatchJobType', $jobType);
		kJobsManager::deleteJob($jobId, $dbJobType);
		return $this->getStatusAction($jobId, $jobType);
	}

	
	
	/**
	 * batch abortJobAction aborts and returns the status of task
	 * 
	 * @action abortJob
	 * @param int $jobId the id of the job  
	 * @param KalturaBatchJobType $jobType the type of the job  
	 * @return KalturaBatchJobResponse 
	 */
	function abortJobAction($jobId, $jobType)
	{
		$dbJobType = kPluginableEnumsManager::apiToCore('BatchJobType', $jobType);
		kJobsManager::abortJob($jobId, $dbJobType);
		return $this->getStatusAction($jobId, $jobType);
	}

	
	
	/**
	 * batch retryJobAction aborts and returns the status of task
	 * 
	 * @action retryJob
	 * @param int $jobId the id of the job  
	 * @param KalturaBatchJobType $jobType the type of the job  
	 * @return KalturaBatchJobResponse 
	 */
	function retryJobAction($jobId, $jobType)
	{
		$dbJobType = kPluginableEnumsManager::apiToCore('BatchJobType', $jobType);
		kJobsManager::retryJob($jobId, $dbJobType);
		return $this->getStatusAction($jobId, $jobType);
	}
	

	/**
	 * list Batch Jobs 
	 * 
	 * @action listBatchJobs
	 * @param KalturaBatchJobFilterExt $filter
	 * @param KalturaFilterPager $pager  
	 * @return KalturaBatchJobListResponse
	 */
	function listBatchJobsAction(KalturaBatchJobFilterExt $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaBatchJobFilter();
			
		$batchJobFilter = new BatchJobFilter ();
		$filter->toObject($batchJobFilter );
		
		$c = new Criteria();
//		$c->add(BatchJobPeer::DELETED_AT, null);
		
		$batchJobFilter->attachToCriteria($c);
		
		if ($pager )	
			$pager->attachToCriteria($c);
		
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL2;
		$list = BatchJobPeer::doSelect($c);
		
		$c->setLimit(false);
		$count = BatchJobPeer::doCount($c);

		$newList = KalturaBatchJobArray::fromStatisticsBatchJobArray($list );
		
		$response = new KalturaBatchJobListResponse();
		$response->objects = $newList;
		$response->totalCount = $count;
		
		return $response;
	}
	
// --------------------------------- generic functions 	--------------------------------- //	
	
	
	
}
?>