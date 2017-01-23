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
	 * @param int $numOfJobsToPull
	 * @param int $jobType
	 * @param int $maxJobToPull
	 *
	 * @return array
	 */
	public static function getJobs($c, $lockKey, $numOfJobsToPull, $jobType, $maxJobToPull)
	{

		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_BATCH_JOBS);
		if (!$maxJobToPull || !$cache) //skip cache and get jobs from DB
			return kBatchExclusiveLock::getJobs($c, $numOfJobsToPull, $jobType);

		kApiCache::disableConditionalCache();
		$workerId = $lockKey->getWorkerId();
		$workerLockKey = self::getCacheKeyForDBLock($workerId);

		$allocated = array();
		for($i = 0; $i < self::GET_JOB_FROM_CACHE_ATTEMPTS; $i++)
		{
			$numOfJobToGet = $numOfJobsToPull - count($allocated);
			$jobsFromCache = self::getJobsFromCache($cache, $workerId, $numOfJobToGet);
			$allocated = array_merge($allocated, $jobsFromCache);
			if (count($allocated) >= $numOfJobsToPull)
				return $allocated;

			if ($cache->add($workerLockKey, true, self::TIME_IN_CACHE_FOR_LOCK))
			{
				$numOfJobToGet = $numOfJobsToPull - count($allocated);
				$jobsFromDB = self::getJobsFromDB($cache, $workerId, clone($c), $maxJobToPull, $jobType, $numOfJobToGet, $workerLockKey);
				return array_merge($allocated, $jobsFromDB);
			}

			usleep(self::TIME_TO_USLEEP_BETWEEN_DB_PULL_ATTEMPTS);
		}
		return $allocated;

	}

	/**
	 * will return BatchJob from cache if exist
	 * @param kBaseCacheWrapper $cache
	 * @param int $workerId
	 * @param int $numOfJobsToPull
	 *
	 * @return array of BatchJob
	 */
	private static function getJobsFromCache($cache, $workerId, $numOfJobsToPull)
	{
		$workerKey = self::getCacheKeyForWorker($workerId);
		$indexKey = self::getCacheKeyForIndex($workerId);

		$jobs = $cache->get($workerKey);

		$allocated = array();
		if ($jobs && !empty($jobs))
		{
			for ($i = 0; $i < $numOfJobsToPull; $i++)
			{
				$index = $cache->increment($indexKey);
				if ($index >= count($jobs))
					break;
				$allocated[] = $jobs[$index];
			}
		}
		KalturaLog::debug("Allocated " .count($allocated). " jobs from cache for workerId [$workerId]");
		return $allocated;
	}

	/**
	 * will return BatchJob and insert bulk of jobs to the cache
	 * @param kBaseCacheWrapper $cache
	 * @param int $workerId
	 * @param Criteria $c
	 * @param int $maxJobToPull
	 * @param int $jobType
	 * @param int $numOfJobsToPull
	 * @param string $workerLockKey
	 *
	 * @return array of BatchJob
	 */
	private static function getJobsFromDB($cache, $workerId, $c, $maxJobToPull, $jobType, $numOfJobsToPull, $workerLockKey)
	{
		$workerKey = self::getCacheKeyForWorker($workerId);
		$indexKey = self::getCacheKeyForIndex($workerId);

		$jobFromDB = kBatchExclusiveLock::getJobs($c, $maxJobToPull, $jobType);
		$cache->add($indexKey, 0, self::TIME_IN_CACHE); //init as 0 if key is not exist
		$cache->set($workerKey, $jobFromDB, self::TIME_IN_CACHE);

		$numOfJobsFromDB = count($jobFromDB);
		KalturaLog::info("Got $numOfJobsFromDB jobs to insert to cache for workerId [$workerId]");
		if ($numOfJobsFromDB == 0)
			return array(); // without delete the lock key to avoid calling the DB again for the next TIME_IN_CACHE_FOR_LOCK seconds

		$numOfJobsToPull = min($numOfJobsToPull, $numOfJobsFromDB);
		$cache->set($indexKey, $numOfJobsToPull - 1, self::TIME_IN_CACHE);
		$cache->delete($workerLockKey);
		return array_slice($jobFromDB, 0, $numOfJobsToPull);
	}

}
