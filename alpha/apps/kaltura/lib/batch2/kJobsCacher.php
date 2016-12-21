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
	CONST TIME_IN_CACHE_FOR_LOCK = 5;

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

		KalturaLog::info("Using cache mechanism for worker [$workerId]");
		$allocated = array();
		for($i = 0; $i < $number_of_objects; $i++)
		{
			$job = self::getJobFromCache($cache, $workerId);
			if ($job)
				$allocated[] = $job;
			else
				$allocated[] = self::getJobsFromDB($cache, $workerId, $c, $maxJobToPull, $jobType);
		}
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
		$workerKey = self::getCacheKeyForWorker($workerId);
		$indexKey = self::getCacheKeyForIndex($workerId);
		$cache->add($indexKey, 0, self::TIME_IN_CACHE); //just init index - ignore if exist

		$jobs = $cache->get($workerKey);
		if (!$jobs)
		{
			KalturaLog::info("No job in cache for workerId [$workerId]");
			return null;
		}
		$index = $cache->increment($indexKey);
		if ($index < count($jobs))
			return $jobs[$index];

		KalturaLog::info("Cannot get job from cache for workerId [$workerId] when index [$index] and number of job is " .count($jobs));
		return null;

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
		$workerLockKey = "jobs_cache_worker_$workerId-Lock";
		if (!$cache->add($workerLockKey, true, self::TIME_IN_CACHE_FOR_LOCK))
			return null;

		$workerKey = self::getCacheKeyForWorker($workerId);
		$indexKey = self::getCacheKeyForIndex($workerId);

		$objects = kBatchExclusiveLock::getExclusive($c, $maxJobToPull, $jobType, null);
		$cache->set($workerKey, $objects, self::TIME_IN_CACHE);

		$numOfObj = count($objects);
		KalturaLog::info("Got $numOfObj jobs to insert to cache for workerId [$workerId]");
		if ($numOfObj == 0)
			return null; // without delete to lock to avoid DB calls in the next TIME_IN_CACHE_FOR_LOCK

		$cache->set($indexKey, 0, self::TIME_IN_CACHE);
		$cache->delete($workerLockKey);
		return $objects[0];
	}

}