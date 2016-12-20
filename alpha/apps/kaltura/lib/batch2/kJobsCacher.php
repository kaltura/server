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
	 *
	 * @return array
	 */
	public static function getExclusive($c, $lockKey, $number_of_objects, $jobType, $maxOffset)
	{
		$workerId = $lockKey->getWorkerId();
		$maxObject = self::getMaxJobToPull($workerId);
		$key = self::getCacheKeyForWorker($workerId);
		$cache = self::getCache();
		if (!$maxObject || $maxOffset || !$cache) //skip cache and get jobs from DB
			return kBatchExclusiveLock::getExclusive($c, $number_of_objects, $jobType, $maxOffset);

		$jobsList = $cache->get($key);
		if ($jobsList && ($allocated = self::getUnallocatedJobs($jobsList,$number_of_objects, $cache)))
			return $allocated;
		$objects = kBatchExclusiveLock::getExclusive($c, $maxObject, $jobType, $maxOffset);
		$cache->set($key, $objects, self::TIME_IN_CACHE);

		return self::getUnallocatedJobs($objects, $number_of_objects, $cache);
	}
	
	/**
	 * will return list max Job To Pull To Cache, if not config for worker return null
	 * @param int $workerId
	 * @return int
	 */
	private static function getMaxJobToPull($workerId)
	{
		try {
			// in production 'batch/worker'
			$map = kConf::getMap('batch/batch');
			foreach ($map as $value)
				if (array_key_exists('id', $value) && $value['id'] == $workerId &&
						array_key_exists('maxJobToPullToCache', $value))
					return $value['maxJobToPullToCache'];
			return null;
		} catch (Exception $e) {
			return null;
		}
	}

	/**
	 * will return cache-key for worker
	 * @param array $jobs
	 * @param int $numberOfJobs
	 * @param kBaseCacheWrapper $cache
	 * @return string
	 */
	private static function getUnallocatedJobs($jobs, $numberOfJobs, $cache)
	{
		$allocatedJob = array();
		foreach ($jobs as $job)
		{
			$key = self::getCacheKeyForJob($job->getId());
			if ($cache->add($key, true, self::TIME_IN_CACHE)) {
				$allocatedJob[] = $job;
			}
			if (count($allocatedJob) >= $numberOfJobs)
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

	/**
	 * @return kBaseCacheWrapper
	 */
	private static function getCache()
	{
		$name = kCacheManager::getCacheSectionNames(kCacheManager::CACHE_TYPE_BATCH_JOBS);
		$cache = kCacheManager::getCache($name[0]);
		return $cache;
	}
}