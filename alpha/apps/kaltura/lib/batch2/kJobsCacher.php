<?php

class kJobsCacher
{
	CONST TIME_IN_CACHE = 10;
	CONST TIME_IN_CACHE_FOR_QUEUE = 3;
	CONST TIME_IN_CACHE_FOR_LOCK = 5;
	CONST GET_JOB_ATTEMPTS = 10;
	CONST TIME_TO_USLEEP_BETWEEN_DB_PULL_ATTEMPTS = 20000;

	/**
	 * will return cache-key for worker
	 * @param int $workerId
	 * @return string
	 */
	private static function getCacheKeyForWorkerJobs($workerId)
	{
		return "jobs_cache_jobs_worker_$workerId";
	}

	/**
	 * will return cache-key for worker queue
	 * @param int $workerId
	 * @return string
	 */
	private static function getCacheKeyForWorkerQueue($workerId)
	{
		return "jobs_cache_queue_worker_$workerId";
	}

	/**
	 * will return cache-key for worker
	 * @param int $workerId
	 * @return string
	 */
	private static function getCacheKeyForIndex($workerId)
	{
		return "jobs_cache_worker_$workerId-index";
	}

	/**
	 * will return BatchJob objects.
	 *@param Criteria $c
	 * @param kExclusiveLockKey $lockKey
	 * @param int $number_of_objects
	 * @param int $jobType
	 * @param int $maxJobToPull
	 *
	 * @return array
	 */
	public static function getJobs($c, $lockKey, $number_of_objects, $jobType, $maxJobToPull)
	{
		$workerId = $lockKey->getWorkerId();
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_BATCH_JOBS);
		if (!$maxJobToPull || !$cache) //skip cache and get jobs from DB
			return kBatchExclusiveLock::getJobs($c, $number_of_objects, $jobType);

		kApiCache::disableConditionalCache();

		$allocated = array();
		for($i = 0; $i < self::GET_JOB_ATTEMPTS; $i++)
		{
			if (count($allocated) >= $number_of_objects)
				break;
			$job = self::getJobFromCache($cache, $workerId);
			if ($job)
			{
				$allocated[] = $job;
				continue;
			}
			$workerLockKey = "jobs_cache_worker_$workerId-Lock";
			if (!$cache->add($workerLockKey, true, self::TIME_IN_CACHE_FOR_LOCK))
			{
				usleep(self::TIME_TO_USLEEP_BETWEEN_DB_PULL_ATTEMPTS);
				continue;
			}
			$job = self::getJobsFromDB($cache, $workerId, clone($c), $maxJobToPull, $jobType);
			if (!$job)
				break; // without delete lock to avoid DB calls in the next TIME_IN_CACHE_FOR_LOCK sec
			$allocated[] = $job;
			$cache->delete($workerLockKey);
		}
		KalturaLog::debug("Return allocated job with ids: " .print_r(array_map(function($job){return $job->getId();},$allocated), true));
		return $allocated;
	}

	/**
	 * will return BatchJob from cache if exist
	 * @param kBaseCacheWrapper $cache
	 * @param int $workerId
	 *
	 * @return BatchJob or null
	 */
	private static function getJobFromCache($cache, $workerId)
	{
		$workerKey = self::getCacheKeyForWorkerJobs($workerId);
		$indexKey = self::getCacheKeyForIndex($workerId);

		$jobs = $cache->get($workerKey);
		if (!$jobs || empty($jobs))
		{
			KalturaLog::debug("No job in cache for workerId [$workerId]");
			return null;
		}
		$index = $cache->increment($indexKey);
		if ($index < count($jobs))
			return $jobs[$index];

		KalturaLog::debug("Cannot get job from cache for workerId [$workerId] when index [$index] and number of job is " .count($jobs));
		return null;

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
	 *
	 * @return BatchJob or null
	 */
	private static function getJobsFromDB($cache, $workerId, $c, $maxJobToPull, $jobType)
	{
		$workerKey = self::getCacheKeyForWorkerJobs($workerId);
		$indexKey = self::getCacheKeyForIndex($workerId);

		$objects = kBatchExclusiveLock::getJobs($c, $maxJobToPull, $jobType);
		$cache->add($indexKey, 0, self::TIME_IN_CACHE); //init as 0 if key is not exist
		$cache->set($workerKey, $objects, self::TIME_IN_CACHE);

		$numOfObj = count($objects);
		KalturaLog::info("Got $numOfObj jobs to insert to cache for workerId [$workerId]");
		if ($numOfObj == 0)
			return null; 

		$cache->set($indexKey, 0, self::TIME_IN_CACHE);
		return $objects[0];
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
