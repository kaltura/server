<?php
/**
 * @package Core
 * @subpackage storage
 */
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
	public function generateFilePathArr(ISyncableFile $object, $subType, $version = null, $storageProfileId = null)
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
		
		if(!kConf::hasParam('volumes') && !kConf::hasParam('local_volumes'))
		{
			KalturaLog::debug("Path [{$root}{$path}]");
			return array($root, $path);
		}
		
		if(isset(self::$sessionCache[$path]))
			return array($root, self::$sessionCache[$path]);
			
		$partnerId = $object->getPartnerId();
		$volumes = kConf::hasParam('local_volumes') ? kConf::get('local_volumes') : kConf::get('volumes');
		$partnerVolumes = kConf::get('partner_volumes', 'local', array());
		$volume = isset($partnerVolumes[$partnerId]) ? $partnerVolumes[$partnerId] : $volumes[rand(0, count($volumes) - 1)];
		
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
		
		$storageProfileId = self::getStorageProfileIdForKey($key, $storageProfileId);
		$storageProfile = self::getStorageProfile($storageProfileId);
		if(is_null($storageProfile))
			throw new Exception("Storage Profile [$storageProfileId] not found");

		$pathManager = $storageProfile->getPathManager();
		
		$object = kFileSyncUtils::retrieveObjectForSyncKey($key);
		
		return $pathManager->generateFilePathArr($object, $key->object_sub_type, $key->version, $storageProfileId);
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
	
	public static function getStorageProfileIdForKey(FileSyncKey $key, $storageProfileId = null)
	{
		$object = kFileSyncUtils::retrieveObjectForSyncKey($key);
		$class = get_class($object);
		
		$objectKeys = array (
			$class . ':' . $key->getObjectType() . ':' . $key->getObjectSubType(),
			$class . ':' . $key->getObjectType() . ':' . '*',
			'*' // WildCard
		);
		
		$cloudStorageObjectMap = kConf::get('cloud_storage_object_map', 'cloud_storage', array());
		if( array_intersect($objectKeys, $cloudStorageObjectMap) && myCloudUtils::isCloudDc(kDataCenterMgr::getCurrentDcId()) )
		{
			$storageProfileIds = kDataCenterMgr::getSharedStorageProfileIds();
			$storageProfileId = reset($storageProfileIds);
		}
		
		return $storageProfileId;
	}

	public static function getStorageProfileIdForObject($objcetClass, $objectType, $storageProfileId = null )
	{
		$objectKeys = array (
			$objcetClass . ':' . $objectType . ':' . '*',
			'*' // WildCard
		);

		$cloudStorageObjectMap = kConf::get('cloud_storage_object_map', 'cloud_storage', array());
		if( array_intersect($objectKeys, $cloudStorageObjectMap) && myCloudUtils::isCloudDc(kDataCenterMgr::getCurrentDcId()) )
		{
			$storageProfileIds = kDataCenterMgr::getSharedStorageProfileIds();
			$storageProfileId = reset($storageProfileIds);
		}

		return $storageProfileId;
	}
}
