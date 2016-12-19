<?php

/**
 * Created by IntelliJ IDEA.
 * User: David.Winder
 * Date: 12/19/2016
 * Time: 3:21 PM
 */
class kJobsCacher
{
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
		$cache = self::getCache();
		$jobsList = self::getListFromCache($workerId, $cache);
		if ($jobsList)
			return array_slice($jobsList, 0, $number_of_objects);
		$maxObject = self::getMaxJobToPull($workerId);
		$objects = kBatchExclusiveLock::getExclusive($c, $maxObject, $jobType, $maxOffset);

		$cache->set(self::getCacheKey($workerId), $objects, 30);

		return array_slice($objects, 0, $number_of_objects);
	}

	/**
	 * will return list of job from cache
	 * @param int $workerId
	 * @param kBaseCacheWrapper $cache
	 * @return array
	 */
	private static function getListFromCache($workerId, $cache)
	{
		if (!self::getMaxJobToPull($workerId))
			return null;
		$key = self::getCacheKey($workerId);
		return $cache->get($key);
	}
	
	/**
	 * will return list max Job To Pull To Cache, if not config for worker return 0
	 * @param int $workerId
	 * @return int
	 */
	private static function getMaxJobToPull($workerId)
	{
		try {
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
	 * @param int $workerId
	 * @return string
	 */
	private static function getCacheKey($workerId)
	{
		return "job_cache_$workerId";
	}

	/**
	 * @return kBaseCacheWrapper
	 */
	private static function getCache()
	{
		//$name = kCacheManager::getCacheSectionNames(kCacheManager::CACHE_TYPE_BATCH);
		//$cache = kCacheManager::getCache($name);
		$cache = new kMemcacheCacheWrapper();
		$config = array("host" => "127.0.0.1", "port" => 11211, "serializeData" => true);
		$ret = $cache->init($config);
		KalturaLog::info("back from init as $ret --- Done create cache in kJobsCacher");
		return $cache;
	}
}