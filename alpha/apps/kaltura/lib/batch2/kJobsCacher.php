<?php

/**
 * Created by IntelliJ IDEA.
 * User: David.Winder
 * Date: 12/19/2016
 * Time: 3:21 PM
 */
class kJobsCacher
{
	CONST TIME_IN_CACHE = 30;
	/**
	 * will return BatchJob objects.
	 *@param Criteria $c
	 * @param kExclusiveLockKey $lockKey
	 * @param int $number_of_objects
	 * @param int $jobType
	 * @param int $maxOffset
	 * @param int $maxJobToPull
	 *
	 * @return array
	 */
	public static function getExclusive($c, $lockKey, $number_of_objects, $jobType, $maxOffset, $maxJobToPull)
	{
		$workerId = $lockKey->getWorkerId();
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_BATCH_JOBS);

		if (!$maxJobToPull || $maxOffset || !$cache) //skip cache and get jobs from DB
			return kBatchExclusiveLock::getExclusive($c, $number_of_objects, $jobType, $maxOffset);

		$key = self::getCacheKeyForWorker($workerId);
		$allocated = self::getUnallocatedJobs($key,$number_of_objects, $cache);
		while (empty($allocated)) {
			KalturaLog::info("Job couldn't be allocated for worker [$workerId], searching the DB ");
			$objects = kBatchExclusiveLock::getExclusive($c, $maxJobToPull, $jobType, $maxOffset);
			if (empty($objects))
				return $objects;
			$cache->set($key, $objects, self::TIME_IN_CACHE);
			$allocated = self::getUnallocatedJobs($key, $number_of_objects, $cache);
		}
		return $allocated;

	}

	/**
	 * will return cache-key for worker
	 * @param string $workerKey
	 * @param int $numberOfJobs
	 * @param kBaseCacheWrapper $cache
	 * @return string
	 */
	private static function getUnallocatedJobs($workerKey, $numberOfJobs, $cache)
	{
		$jobs = $cache->get($workerKey);
		if (!$jobs)
			return array();
		$allocatedJob = array();
		$cnt = 0;
		foreach ($jobs as $job)
		{
			$key = self::getCacheKeyForJob($job->getId());
			if ($cache->add($key, true, self::TIME_IN_CACHE)) {
				$allocatedJob[] = $job;
				$cnt++;
			}
			if ($cnt >= $numberOfJobs)
				break;
		}
		return $allocatedJob;
	}

	/**
	 * will return cache-key for worker
	 * @param int $workerId
	 * @return string
	 */
	private static function getCacheKeyForWorker($workerId)
	{
		return "jobs_cache_worker_$workerId";
	}
	/**
	 * will return cache-key for worker
	 * @param int $jobId
	 * @return string
	 */
	private static function getCacheKeyForJob($jobId)
	{
		return "jobs_cache_job_$jobId";
	}

}