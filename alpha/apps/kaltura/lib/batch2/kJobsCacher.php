<?php

class kJobsCacher
{
	CONST TIME_IN_CACHE = 10;
	CONST TIME_IN_CACHE_FOR_QUEUE = 3;
	CONST TIME_IN_CACHE_FOR_LOCK = 5;
	CONST GET_JOB_FROM_CACHE_ATTEMPTS = 10;
	CONST TIME_TO_USLEEP_BETWEEN_DB_PULL_ATTEMPTS = 20000;

	/**
	 * will return cache-key for worker
	 * @param int $workerId
	 * @return string
	 */
	private static function getCacheKeyForWorkerJobs($workerId)
	{
		$batchVersion = BatchJobLockPeer::getBatchVersion();
		return "jobs_cache_jobs_worker_$workerId-$batchVersion";
	}

	/**
	 * will return cache-key for worker queue
	 * @param int $workerId
	 * @return string
	 */
	private static function getCacheKeyForWorkerQueue($workerId)
	{
		$batchVersion = BatchJobLockPeer::getBatchVersion();
		return "jobs_cache_queue_worker_$workerId-$batchVersion";
	}

	/**
	 * will return cache-key for index by worker
	 * @param int $workerId
	 * @return string
	 */
	private static function getCacheKeyForIndex($workerId)
	{
		$batchVersion = BatchJobLockPeer::getBatchVersion();
		return "jobs_cache_worker_$workerId-$batchVersion-index";
	}

	/**
	 * will return cache-key for lock by worker
	 * @param int $workerId
	 * @return string
	 */
	private static function getCacheKeyForDBLock($workerId)
	{
		$batchVersion = BatchJobLockPeer::getBatchVersion();
		return "jobs_cache_worker_$workerId-$batchVersion-Lock";
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

		for($i = 0; $i < self::GET_JOB_FROM_CACHE_ATTEMPTS; $i++)
		{
			$jobsFromCache = self::getJobsFromCache($cache, $workerId, $numOfJobsToPull);
			if (count($jobsFromCache) > 0)
				return $jobsFromCache;

			if ($cache->add($workerLockKey, true, self::TIME_IN_CACHE_FOR_LOCK))
			{
				$jobsFromDB = self::getJobsFromDB($cache, $workerId, clone($c), $maxJobToPull, $jobType, $numOfJobsToPull, $workerLockKey);
				return $jobsFromDB;
			}
			KalturaMonitorClient::usleep(self::TIME_TO_USLEEP_BETWEEN_DB_PULL_ATTEMPTS);
		}
		return array();

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
		$workerKey = self::getCacheKeyForWorkerJobs($workerId);
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
	 * check if there are available jobs on cache
	 * @param kBaseCacheWrapper $cache
	 * @param int $workerId
	 *
	 * @return int
	 */
	private static function getNumberOfAvailableJobsInCache($cache, $workerId)
	{
		$workerKey = self::getCacheKeyForWorkerJobs($workerId);
		$indexKey = self::getCacheKeyForIndex($workerId);

		$jobs = $cache->get($workerKey);
		if (!$jobs || empty($jobs))
			return 0;

		$indexForNextJob = $cache->get($indexKey) + 1;
		$numOfJobs = count($jobs);
		if ($indexForNextJob < $numOfJobs)
			return ($numOfJobs - $indexForNextJob);

		return 0;
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
		$workerKey = self::getCacheKeyForWorkerJobs($workerId);
		$indexKey = self::getCacheKeyForIndex($workerId);

		$jobsFromDB = kBatchExclusiveLock::getJobs($c, $maxJobToPull, $jobType);
		$cache->add($indexKey, 0, self::TIME_IN_CACHE); //init as 0 if key is not exist
		$cache->set($workerKey, $jobsFromDB, self::TIME_IN_CACHE);

		$numOfJobsFromDB = count($jobsFromDB);
		KalturaLog::info("Got $numOfJobsFromDB jobs to insert to cache for workerId [$workerId]");
		if ($numOfJobsFromDB == 0)
			return array(); // without delete the lock key to avoid calling the DB again for the next TIME_IN_CACHE_FOR_LOCK seconds

		$numOfJobsToPull = min($numOfJobsToPull, $numOfJobsFromDB);
		$cache->set($indexKey, $numOfJobsToPull - 1, self::TIME_IN_CACHE);
		$cache->delete($workerLockKey);
		return array_slice($jobsFromDB, 0, $numOfJobsToPull);
	}

	/**
	 * will return queue size for the worker
	 * @param Criteria $c
	 * @param int $workerId
	 * @param int $max_exe_attempts
	 *
	 * @return int
	 */
	public static function getQueue($c, $workerId, $max_exe_attempts)
	{
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_BATCH_JOBS);
		if (!$cache) //skip cache and get jobs from DB
			return kBatchExclusiveLock::getQueue($c, $max_exe_attempts);

		kApiCache::disableConditionalCache();

		$NumberOfJobsInCache = self::getNumberOfAvailableJobsInCache($cache, $workerId);
		if ($NumberOfJobsInCache)
			return $NumberOfJobsInCache; //if there're jobs waiting in the job-cache no need the check queue size

		$workerKey = self::getCacheKeyForWorkerQueue($workerId);
		$queueSizeFromCache = $cache->get($workerKey);
		if ($queueSizeFromCache !== false)
		{
			KalturaLog::info("Got cached queue size for worker Id [$workerId] as [$queueSizeFromCache]");
			return $queueSizeFromCache;
		}
		KalturaLog::info("No cached queue for worker Id [$workerId]");
		return self::getQueueFromDB($cache, $workerKey, $c, $max_exe_attempts);
	}

	/**
	 * will return BatchJob and insert bulk of jobs to the cache
	 * @param kBaseCacheWrapper $cache
	 * @param string $workerKey
	 * @param Criteria $c
	 * @param int $max_exe_attempts
	 *
	 * @return int
	 */
	private static function getQueueFromDB($cache, $workerKey, $c, $max_exe_attempts)
	{
		$queueSize = kBatchExclusiveLock::getQueue($c, $max_exe_attempts);
		$cache->set($workerKey, $queueSize, self::TIME_IN_CACHE_FOR_QUEUE);
		return $queueSize;
	}

}
