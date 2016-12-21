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






	CONST TIME_IN_CACHE_FOR_LOCK = 5;
	CONST SLEEP_TIME_WHEN_LOCKED = 1;

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
	public static function getExclusive2($c, $lockKey, $number_of_objects, $jobType, $maxOffset, $maxJobToPull)
	{
		$workerId = $lockKey->getWorkerId();
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_BATCH_JOBS);

		if (!$maxJobToPull || $maxOffset || !$cache) //skip cache and get jobs from DB
			return kBatchExclusiveLock::getExclusive($c, $number_of_objects, $jobType, $maxOffset);

		KalturaLog::info("Using cache mechanism for worker [$workerId]");
		$allocated = array();
		for($attempt = 1; $attempt < 20; $attempt++)
		{
			$job = self::getJob($cache, $workerId, $c, $maxJobToPull, $jobType);
			if (!$job) {
				if (!empty($allocated))
					return $allocated;
				sleep(self::SLEEP_TIME_WHEN_LOCKED);
				continue;
			}
			$allocated[] = $job;
			if (count($allocated) >= $number_of_objects)
				break;
		}
		return $allocated;
	}

	/**
	 * will return BatchJob.
	 * @param kBaseCacheWrapper $cache
	 * @param int $workerId
	 * @param Criteria $c
	 * @param int $maxJobToPull
	 * @param int $jobType
	 *
	 * @return BatchJob
	 */
	private static function getJob($cache, $workerId, $c, $maxJobToPull, $jobType)
	{
		$workerKey = "jobs_cache_worker_$workerId";
		$indexKey = "jobs_cache_worker_$workerId-index";
		$workerLockKey = "jobs_cache_worker_$workerId-Lock";
		$cache->add($indexKey, 0, self::TIME_IN_CACHE); //just init index - ignore if exist

		$jobs = $cache->get($workerKey);
		if ($jobs)
		{
			$index = $cache->increment($indexKey);
			if ($index < count($jobs))
				return $jobs[$index];
		}

		KalturaLog::info("Cannot get job from cache for workerId [$workerId] when index [$index] and number of job is " .count($jobs));
		if (!$cache->add($workerLockKey, true, self::TIME_IN_CACHE_FOR_LOCK))
			return null;
		$objects = kBatchExclusiveLock::getExclusive($c, $maxJobToPull, $jobType, null);
		$cache->set($workerKey, $objects, self::TIME_IN_CACHE);
		$numOfObj = count($objects);
		KalturaLog::info("Got $numOfObj jobs to insert to cache for workerId [$workerId]");
		if ($numOfObj == 0)
			return null;
		$cache->set($indexKey, 0, self::TIME_IN_CACHE);
		$cache->delete($workerLockKey);
		return $objects[0];
	}

}