<?php

class kJobsCacher
{
	CONST TIME_IN_CACHE = 10;
	CONST TIME_IN_CACHE_FOR_LOCK = 5;
	CONST GET_JOB_FROM_CACHE_ATTEMPTS = 10;
	CONST TIME_TO_USLEEP_BETWEEN_DB_PULL_ATTEMPTS = 20000;

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
	 * will return cache-key for index by worker
	 * @param int $workerId
	 * @return string
	 */
	private static function getCacheKeyForIndex($workerId)
	{
		return "jobs_cache_worker_$workerId-index";
	}

	/**
	 * will return cache-key for lock by worker
	 * @param int $workerId
	 * @return string
	 */
	private static function getCacheKeyForDBLock($workerId)
	{
		return "jobs_cache_worker_$workerId-Lock";
	}

	/**
	 * will return BatchJob objects.
	 *@param Criteria $c
	 * @param kExclusiveLockKey $lockKey
	 * @param int $numberOfObjects
	 * @param int $jobType
	 * @param int $maxJobToPull
	 *
	 * @return array
	 */
	public static function getJobs($c, $lockKey, $numberOfObjects, $jobType, $maxJobToPull)
	{

		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_BATCH_JOBS);
		if (!$maxJobToPull || !$cache) //skip cache and get jobs from DB
			return kBatchExclusiveLock::getJobs($c, $numberOfObjects, $jobType);

		kApiCache::disableConditionalCache();
		$workerId = $lockKey->getWorkerId();
		$workerLockKey = self::getCacheKeyForDBLock($workerId);

		$allocated = array();
		for($i = 0; $i < self::GET_JOB_FROM_CACHE_ATTEMPTS; $i++) {
			$allocated = array_merge($allocated, self::getJobsFromCache($cache, $workerId, $numberOfObjects - count($allocated)));
			if (count($allocated) >= $numberOfObjects)
				return $allocated;

			if ($cache->add($workerLockKey, true, self::TIME_IN_CACHE_FOR_LOCK))
				return array_merge($allocated, self::getJobsFromDB($cache, $workerId, clone($c), $maxJobToPull, $jobType,
						$numberOfObjects - count($allocated), $workerLockKey));

			usleep(self::TIME_TO_USLEEP_BETWEEN_DB_PULL_ATTEMPTS);
		}
		return $allocated;

	}

	/**
	 * will return BatchJob from cache if exist
	 * @param kBaseCacheWrapper $cache
	 * @param int $workerId
	 * @param int $numOfJobs
	 *
	 * @return array of BatchJob
	 */
	private static function getJobsFromCache($cache, $workerId, $numOfJobs)
	{
		$workerKey = self::getCacheKeyForWorker($workerId);
		$indexKey = self::getCacheKeyForIndex($workerId);

		$jobs = $cache->get($workerKey);
		if (!$jobs || empty($jobs))
		{
			KalturaLog::debug("No job in cache for workerId [$workerId]");
			return array();
		}
		$allocated = array();
		for($i = 0; $i < $numOfJobs; $i++)
		{
			$index = $cache->increment($indexKey);
			if ($index < count($jobs))
				$allocated[] = $jobs[$index];
		}
		KalturaLog::debug("Was able to allocated $numOfJobs job from cache for workerId [$workerId]. Index was [$index] and number of job in cache was " .count($jobs));
		return $allocated;

	}

	/**
	 * will return BatchJob and insert bulk of jobs to the cache
	 * @param kBaseCacheWrapper $cache
	 * @param int $workerId
	 * @param Criteria $c
	 * @param int $maxJobToPull
	 * @param int $jobType
	 * @param int $numOfJobs
	 * @param string $workerLockKey
	 *
	 * @return array of BatchJob
	 */
	private static function getJobsFromDB($cache, $workerId, $c, $maxJobToPull, $jobType, $numOfJobs, $workerLockKey)
	{
		$workerKey = self::getCacheKeyForWorker($workerId);
		$indexKey = self::getCacheKeyForIndex($workerId);

		$objects = kBatchExclusiveLock::getJobs($c, $maxJobToPull, $jobType);
		$cache->add($indexKey, 0, self::TIME_IN_CACHE); //init as 0 if key is not exist
		$cache->set($workerKey, $objects, self::TIME_IN_CACHE);

		$numOfObj = count($objects);
		KalturaLog::info("Got $numOfObj jobs to insert to cache for workerId [$workerId]");
		if ($numOfObj == 0)
			return array();

		$numOfJobs = min($numOfJobs, $numOfObj);
		$cache->delete($workerLockKey);
		$cache->set($indexKey, $numOfJobs - 1, self::TIME_IN_CACHE);
		return array_slice($objects, 0, $numOfJobs);
	}

}
