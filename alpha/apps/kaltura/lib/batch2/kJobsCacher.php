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

		if (!$maxObject || $maxOffset)
			return kBatchExclusiveLock::getExclusive($c, $number_of_objects, $jobType, $maxOffset);

		$key = self::getCacheKey($workerId);

		$cache = self::getCache();
		$jobsList = $cache->get($key);
		if ($jobsList)
			return array_slice($jobsList, 0, $number_of_objects);

		$objects = kBatchExclusiveLock::getExclusive($c, $maxObject, $jobType, $maxOffset);
		$cache->set($key, $objects, self::TIME_IN_CACHE);
		return array_slice($objects, 0, $number_of_objects);
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