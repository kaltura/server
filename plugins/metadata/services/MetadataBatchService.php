<?php
/**
 * @service metadataBatch
 * @package plugins.metadata
 * @subpackage api.services
 */
class MetadataBatchService extends KalturaBatchService
{


// --------------------------------- TransformMetadataJob functions 	--------------------------------- //

	/**
	 * batch getExclusiveTransformMetadataJob action allows to get a BatchJob of type METADATA_TRANSFORM
	 *
	 * @action getExclusiveTransformMetadataJobs
	 * @param KalturaExclusiveLockKey $lockKey The unique lock key from the batch-process. Is used for the locking mechanism
	 * @param int $maxExecutionTime The maximum time in seconds the job reguarly take. Is used for the locking mechanism when determining an unexpected termination of a batch-process.
	 * @param int $numberOfJobs The maximum number of jobs to return.
	 * @param KalturaBatchJobFilter $filter Set of rules to fetch only rartial list of jobs
	 * @param int $maxJobToPull The maximum job we will pull from the DB into the cache
	 * @return KalturaBatchJobArray
	 *
	 * TODO remove the destXsdPath from the job data and get it later using the api, then delete this method
	 */
	function getExclusiveTransformMetadataJobsAction(KalturaExclusiveLockKey $lockKey, $maxExecutionTime, $numberOfJobs, KalturaBatchJobFilter $filter = null, $maxJobToPull = null)
	{
		$jobs = $this->getExclusiveJobs($lockKey, $maxExecutionTime, $numberOfJobs, $filter, BatchJobType::METADATA_TRANSFORM, $maxJobToPull);

		if($jobs)
		{
			foreach ($jobs as &$job)
			{
				$data = $job->getData();
				$metadataProfileId = $data->getMetadataProfileId();
				$metadataProfile = MetadataProfilePeer::retrieveByPK($metadataProfileId);
				if(!$metadataProfile)
					continue;

				$key = $metadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION);
				$xsdPath = kFileSyncUtils::getLocalFilePathForKey($key);
				$data->setDestXsdPath($xsdPath);
				$job->setData($data);
			}
		}

		return KalturaBatchJobArray::fromBatchJobArray($jobs);
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
		$response->objects = KalturaMetadataArray::fromDbArray($list, $this->getResponseProfile());

		return $response;
	}

// --------------------------------- TransformMetadataJob functions 	--------------------------------- //

}
