<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib
 */
class kScheduledProfileTaskAllocator extends kAllocator
{
	const OBJECT_NAME = 'scheduled_profile_task';
	const KCONF_TIME_TO_LAST = 'scheduled_task_profiles_ttl';
	const NUM_OF_OBJECTS_TO_HANDLE = 'scheduled_task_profiles_limit';
	
	/**
	 * Insert bulk of scheduled profile tasks to the cache from DB
	 * @param string $objectName
	 * @param kBaseCacheWrapper $cache
	 * @param string $tag
	 * @return array
	 * @throws Exception
	 */
	public static function refreshObjectListFromDB($objectName, $cache, $tag)
	{
		$tagLockKey = self::getCacheKeyForDBLock($objectName, $tag);
		if (!$cache->add($tagLockKey, true, self::TIME_IN_CACHE_FOR_LOCK))
		{
			return array();
		}
		
		$ttlForList = kConf::get(self::KCONF_TIME_TO_LAST);
		$scheduledProfileTasksFromDB = self::retrieveScheduledTaskProfilesToHandle($tag);
		self::refreshObjectsListInCache($cache, $objectName, $tag, $scheduledProfileTasksFromDB, $ttlForList);
		
		$cache->delete($tagLockKey);
		
		return $scheduledProfileTasksFromDB;
	}
	
	/**
	 * @param string $tag
	 * @return array
	 * @throws PropelException
	 */
	protected static function retrieveScheduledTaskProfilesToHandle($tag)
	{
		$criteria = new Criteria();
		$criteria->add(ScheduledTaskProfilePeer::STATUS, KalturaScheduledTaskProfileStatus::ACTIVE);
		$criteria->add(ScheduledTaskProfilePeer::LAST_EXECUTION_STARTED_AT, kTimeZoneUtils::midnightTimezoneDateTime(time(),'UTC')->getTimestamp(), Criteria::LESS_EQUAL);
		if ($tag && $tag != '*')
		{
			$criteria->add(ScheduledTaskProfilePeer::OBJECT_FILTER_ENGINE_TYPE, $tag);
		}
		$criteria->addAscendingOrderByColumn(ScheduledTaskProfilePeer::LAST_EXECUTION_STARTED_AT);
		$amount = kConf::get(self::NUM_OF_OBJECTS_TO_HANDLE);
		if (!$amount)
		{
			$amount = 100;
		}
		$criteria->setLimit($amount);
		ScheduledTaskProfilePeer::setDefaultCriteriaFilter(false);
		$scheduledProfileTasksFromDB = ScheduledTaskProfilePeer::doSelect($criteria);
		ScheduledTaskProfilePeer::setDefaultCriteriaFilter(true);
		
		return $scheduledProfileTasksFromDB;
	}
	
	/**
	 * @param string $objectName
	 * @param $objectToAllocate
	 * @return bool
	 * @throws PropelException
	 */
	protected static function verifyAllocatedObject($objectName, $objectToAllocate)
	{
		/** @var $objectToAllocate ScheduledTaskProfile */
		$criteria = new Criteria();
		$criteria->add(ScheduledTaskProfilePeer::ID, $objectToAllocate->getId());
		$objectToAllocate = ScheduledTaskProfilePeer::doSelectOne($criteria);
		$lastExecutionTime = strtotime($objectToAllocate->getLastExecutionStartedAt());
		if ($lastExecutionTime < time() - kTimeConversion::DAY)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
