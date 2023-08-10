<?php

class kDropFolderAllocator extends kAllocator
{
	const OBJECT_NAME = 'drop_folder';
	const KCONF_TIME_TO_LAST = 'DropFolderListTTL';
	
	/**
	 * Insert bulk of drop folders to the cache from DB
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
		$dropFoldersFromDB = DropFolderPeer::retrieveByTag($tag, true);
		self::refreshObjectsListInCache($cache, $objectName, $tag, $dropFoldersFromDB, $ttlForList);
		
		$cache->delete($tagLockKey);
		
		return $dropFoldersFromDB;
	}
}
