<?php
/**
 * @service metadataBatch
 * @package api
 * @subpackage services
 */
class MetadataBatchService extends BatchService 
{
	
// --------------------------------- ImportMetadataJob functions 	--------------------------------- //
	
	/**
	 * batch getExclusiveImportMetadataJob action allows to get a BatchJob of type METADATA_IMPORT 
	 * 
	 * @action getExclusiveImportMetadataJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaMetadataBatchJobArray 
	 */
	function getExclusiveImportMetadataJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		return $this->getExclusiveJobsAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJob::BATCHJOB_TYPE_METADATA_IMPORT );
	}

	
	/**
	 * batch updateExclusiveImportMetadataJob action updates a BatchJob of type METADATA_IMPORT that was claimed using the getExclusiveImportMetadataJobs
	 * 
	 * @action updateExclusiveImportMetadataJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param KalturaMetadataBatchJob $job
	 * @return KalturaMetadataBatchJob 
	 */
	function updateExclusiveImportMetadataJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaMetadataBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		
		// verifies that the job is of the right type
		if($dbBatchJob->getJobType() != KalturaBatchJobType::METADATA_IMPORT)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), serialize($job));
	
		$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));
				
		$batchJob = new KalturaMetadataBatchJob(); // start from blank
		return $batchJob->fromObject($dbBatchJob);
	}

	
	/**
	 * batch freeExclusiveImportMetadataJob action frees a BatchJob of type ImportMetadata that was claimed using the getExclusiveImportMetadataJobs
	 * 
	 * @action freeExclusiveImportMetadataJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism
	 * @param bool $resetExecutionAttempts Resets the job execution attampts to zero  
	 * @return KalturaFreeJobResponse 
	 */
	function freeExclusiveImportMetadataJobAction($id ,KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		return $this->freeExclusiveJobAction($id ,$lockKey, KalturaBatchJobType::METADATA_IMPORT, $resetExecutionAttempts);
	}	
// --------------------------------- ImportMetadataJob functions 	--------------------------------- //

	
// --------------------------------- TransformMetadataJob functions 	--------------------------------- //
	
	/**
	 * batch getExclusiveTransformMetadataJob action allows to get a BatchJob of type METADATA_TRANSFORM 
	 * 
	 * @action getExclusiveTransformMetadataJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @return KalturaMetadataBatchJobArray 
	 */
	function getExclusiveTransformMetadataJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null)
	{
		return $this->getExclusiveJobsAction($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJob::BATCHJOB_TYPE_METADATA_TRANSFORM );
	}

	
	/**
	 * batch updateExclusiveTransformMetadataJob action updates a BatchJob of type METADATA_TRANSFORM that was claimed using the getExclusiveTransformMetadataJobs
	 * 
	 * @action updateExclusiveTransformMetadataJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param KalturaMetadataBatchJob $job
	 * @return KalturaMetadataBatchJob 
	 */
	function updateExclusiveTransformMetadataJobAction($id ,KalturaExclusiveLockKey $lockKey, KalturaMetadataBatchJob $job)
	{
		$dbBatchJob = BatchJobPeer::retrieveByPK($id);
		
		// verifies that the job is of the right type
		if($dbBatchJob->getJobType() != KalturaBatchJobType::METADATA_TRANSFORM)
			throw new KalturaAPIException(APIErrors::UPDATE_EXCLUSIVE_JOB_WRONG_TYPE, $id, serialize($lockKey), serialize($job));
	
		$dbBatchJob = kBatchManager::updateExclusiveBatchJob($id, $lockKey->toObject(), $job->toObject($dbBatchJob));
				
		$batchJob = new KalturaMetadataBatchJob(); // start from blank
		return $batchJob->fromObject($dbBatchJob);
	}

	
	/**
	 * batch freeExclusiveTransformMetadataJob action frees a BatchJob of type TransformMetadata that was claimed using the getExclusiveTransformMetadataJobs
	 * 
	 * @action freeExclusiveTransformMetadataJob
	 * @param int $id The id of the job to free
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism
	 * @param bool $resetExecutionAttempts Resets the job execution attampts to zero  
	 * @return KalturaFreeJobResponse 
	 */
	function freeExclusiveTransformMetadataJobAction($id ,KalturaExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		return $this->freeExclusiveJobAction($id ,$lockKey, KalturaBatchJobType::METADATA_TRANSFORM, $resetExecutionAttempts);
	}

	
	/**
	 * batch getTransformMetadataObjects action retrieve all metadata objects that requires upgrade and the total count 
	 * 
	 * @action getTransformMetadataObjects
	 * @param int $metadataProfileId The id of the metadata profile
	 * @param int $srcVersion The old metadata profile version
	 * @param int $destVersion The new metadata profile version
	 * @param KalturaFilterPager $pager
	 * @return KalturaTransformMetadataResponse
	 */
	function getTransformMetadataObjectsAction($metadataProfileId, $srcVersion, $destVersion, KalturaFilterPager $pager = null)
	{
		$response = new KalturaTransformMetadataResponse();
		
		$c = new Criteria();
		$c->add(MetadataPeer::METADATA_PROFILE_ID, $metadataProfileId);
		$c->add(MetadataPeer::METADATA_PROFILE_VERSION, $srcVersion, Criteria::LESS_THAN);
		$c->add(MetadataPeer::STATUS, KalturaMetadataStatus::VALID);
		$response->lowerVersionCount = MetadataPeer::doCount($c);
		
		$c = new Criteria();
		$c->add(MetadataPeer::METADATA_PROFILE_ID, $metadataProfileId);
		$c->add(MetadataPeer::METADATA_PROFILE_VERSION, $srcVersion);
		$c->add(MetadataPeer::STATUS, KalturaMetadataStatus::VALID);
		$response->totalCount = MetadataPeer::doCount($c);
		
		if ($pager)
			$pager->attachToCriteria($c);
			
		$list = MetadataPeer::doSelect($c);
		$response->objects = KalturaMetadataArray::fromDbArray($list);
		
		return $response;
	}

	
	/**
	 * batch getTransformMetadataObjects action retrieve all metadata objects that requires upgrade and the total count 
	 * 
	 * @action upgradeMetadataObjects
	 * @param int $metadataProfileId The id of the metadata profile
	 * @param int $srcVersion The old metadata profile version
	 * @param int $destVersion The new metadata profile version
	 * @return KalturaUpgradeMetadataResponse
	 */
	function upgradeMetadataObjectsAction($metadataProfileId, $srcVersion, $destVersion, KalturaFilterPager $pager = null)
	{
		$response = new KalturaUpgradeMetadataResponse();
		
		$c = new Criteria();
		$c->add(MetadataPeer::METADATA_PROFILE_ID, $metadataProfileId);
		$c->add(MetadataPeer::METADATA_PROFILE_VERSION, $srcVersion, Criteria::LESS_THAN);
		$c->add(MetadataPeer::STATUS, KalturaMetadataStatus::VALID);
		$response->lowerVersionCount = MetadataPeer::doCount($c);
		
		$c = new Criteria();
		$c->add(MetadataPeer::METADATA_PROFILE_ID, $metadataProfileId);
		$c->add(MetadataPeer::METADATA_PROFILE_VERSION, $srcVersion);
		$c->add(MetadataPeer::STATUS, KalturaMetadataStatus::VALID);
		
		$update = new Criteria();
		$update->add(MetadataPeer::METADATA_PROFILE_VERSION, $destVersion);
			
		$con = Propel::getConnection(MetadataPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		$response->totalCount = BasePeer::doUpdate($c, $update, $con);
		
		return $response;
	}
// --------------------------------- TransformMetadataJob functions 	--------------------------------- //

	
	

	/**
	 * batch getExclusiveJobsAction action allows to get a BatchJob 
	 * 
	 * @action getExclusiveJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism  
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return. 
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs  
	 * @param int $jobType The type of the job - could be a custom extended type
	 * @return KalturaMetadataBatchJobArray 
	 */
	function getExclusiveJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, $jobType)
	{
		$jobs = $this->getExclusiveJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, $jobType);
		return KalturaMetadataBatchJobArray::fromBatchJobArray($jobs);
	}	
}
?>