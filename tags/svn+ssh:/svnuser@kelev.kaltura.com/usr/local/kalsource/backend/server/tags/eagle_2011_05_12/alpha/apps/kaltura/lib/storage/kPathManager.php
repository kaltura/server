<?php
class kPathManager
{
	static $sessionCache = array();
	
	protected static function getStorageProfile($storageProfileId = null)
	{
		if(is_null($storageProfileId))
			return kDataCenterMgr::getCurrentStorageProfile();
			
		return StorageProfilePeer::retrieveByPK($storageProfileId);
	}
	
	/**
	 * will return a pair of file_root and file_path
	 * This is the only function that should be extended for building a different path
	 *
	 * @param ISyncableFile $object
	 * @param int $subType
	 * @param $version
	 */
	public function generateFilePathArr(ISyncableFile $object, $subType, $version = null)
	{
//		$traces = debug_backtrace(false);
//		foreach($traces as $i => $trace)
//		{
//			$file = $trace['file'];
//			$line = $trace['line'];
//			$class = $trace['class'];
//			$function = $trace['function'];
//			KalturaLog::debug("#$i Called from function [$class::$function] file[$file] line[$line]");
//		}
			
		list($root, $path) = $object->generateFilePathArr($subType, $version);
		$root = str_replace('//', '/', $root);
		$path = str_replace('//', '/', $path);
		
		if(!kConf::hasParam('volumes'))
		{
			KalturaLog::debug("Path [{$root}{$path}]");
			return array($root, $path);
		}
		
		if(isset(self::$sessionCache[$path]))
			return array($root, self::$sessionCache[$path]);
			
		$volumes = kConf::get('volumes');
		$volume = $volumes[rand(0, count($volumes) - 1)];
		$newPath = str_replace('/content/', "/content/$volume/", $path);
		self::$sessionCache[$path] = $newPath;
		$path = $newPath;
		
		KalturaLog::debug("Path [{$root}{$path}]");
		return array($root, $path);
	}
	
	/**
	 * will return a pair of file_root and file_path
	 *
	 * @param ISyncableFile $object
	 * @param int $subType
	 * @param int $storageProfileId
	 * @param $version
	 */
	public static function getFilePathArr(FileSyncKey $key, $storageProfileId = null)
	{
		KalturaLog::log(__METHOD__." - key [$key], storageProfileId [$storageProfileId]");
		
		$storageProfile = self::getStorageProfile($storageProfileId);
		if(is_null($storageProfile))
			throw new Exception("Storage Profile [$storageProfileId] not found");

		$pathManager = $storageProfile->getPathManager();
		
		$object = kFileSyncUtils::retrieveObjectForSyncKey($key);
		return $pathManager->generateFilePathArr($object, $key->object_sub_type, $key->version);
	}
	
	/**
	 * will return a pair of file_root and file_path
	 *
	 * @param ISyncableFile $object
	 * @param int $subType
	 * @param int $storageProfileId
	 * @param $version
	 */
	public static function getFilePath(FileSyncKey $key, $storageProfileId = null)
	{
		return implode('', self::getFilePathArr($key, $storageProfileId));
	}
}