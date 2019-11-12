<?php
/**
 * @package Core
 * @subpackage storage
 */
class kFileSyncUtils implements kObjectChangedEventConsumer, kObjectAddedEventConsumer
{
	const MAX_FILES_IN_CATEGORY = 5000;
	const MAX_CACHED_FILE_SIZE = 2097152;		// 2MB
	const CACHE_KEY_PREFIX = 'fileSyncContent_';
	const FILE_SYNC_CACHE_EXPIRY = 2592000;		// 30 days
	
	//File sync Insert limitation consts
	const FILE_SYNC_MIN_VERSION_VALIDATE = 10000;

	/**
	 * Contain all object types and sub types that should not be synced
	 * @var array
	 */
	static protected $excludedSyncFileFromDcSynchronization = null;

	protected static $uncachedObjectTypes = array(
		FileSyncObjectType::ASSET,				// should not cache conversion logs since they can change (batch.logConversion)
		);

	/**
	 * @var array<int order, int storageId>
	 */
	private static $storageProfilesOrder = null;

	public static function file_exists ( FileSyncKey $key , $fetch_from_remote_if_no_local = false )
	{
		KalturaLog::debug("key [$key], fetch_from_remote_if_no_local [$fetch_from_remote_if_no_local]");
		list ( $file_sync , $local ) = self::getReadyFileSyncForKey( $key , $fetch_from_remote_if_no_local , false  );
		if ( ! $file_sync )
		{
			KalturaLog::info("FileSync not found");
			return false;
		}
		else
		{
			$file_sync = self::resolve($file_sync);
		}

		$startTime = microtime(true);

		$file_exists = file_exists ( $file_sync->getFullPath() );

		KalturaLog::info("file_exists? [$file_exists] took [".(microtime(true)-$startTime)."] path [".$file_sync->getFullPath()."]");

		return $file_exists;
	}

	public static function fileSync_exists ( FileSyncKey $key )
	{
		KalturaLog::debug("key [$key]");
		list ( $file_sync , $local ) = self::getReadyFileSyncForKey( $key , true , false  );
		if ( ! $file_sync )
		{
			KalturaLog::info("FileSync not found");
			return false;
		}
		return true;
	}

	public static function getLocalContentsByFileSync(FileSync $file_sync, $use_include_path = false, $context = null, $offset = 0, $maxlen = null)
	{
		$full_path = $file_sync->getFullPath();
		$real_path = realpath( $full_path );
		if ( file_exists ( $real_path ) )
		{
			$startTime = microtime(true);
			if (!$maxlen)
				$contents = file_get_contents( $real_path);
			else
				$contents = file_get_contents( $real_path, $use_include_path, $context, $offset, $maxlen);
			KalturaLog::info("file was found locally at [$real_path] fgc took [".(microtime(true) - $startTime)."]");
			if ($file_sync->isEncrypted())
			{
				$key = $file_sync->getEncryptionKey();
				$iv = $file_sync->getIv();
				$contents = kEncryptFileUtils::decryptData($contents, $key,$iv);
			}
			return $contents;
		}
		else
		{
			KalturaLog::info("file was not found locally [$full_path]");
			throw new kFileSyncException("Cannot find file on local disk [$full_path] for file sync [" . $file_sync->getId() . "]", kFileSyncException::FILE_DOES_NOT_EXIST_ON_DISK);
		}
	}

	public static function getContentsByFileSync ( FileSync $file_sync , $local = true , $fetch_from_remote_if_no_local = true , $strict = true )
	{
		if ( $local )
			return self::getLocalContentsByFileSync($file_sync);
		

		if ( $fetch_from_remote_if_no_local )
		{
			if (!in_array($file_sync->getDc(), kDataCenterMgr::getDcIds()))
			{
				if ( $strict )
				{
					throw new Exception ( "File sync is remote - cannot get contents, id = [" . $file_sync->getId() . "]" );
				}
				else
				{
					return null;
				}
			}
			// if $fetch_from_remote_if_no_local is false - $file_sync shoule be null , this if is in fact redundant
			// TODO - curl to the remote
			$content = kDataCenterMgr::retrieveFileFromRemoteDataCenter( $file_sync );
			return $content;
		}
	}
	
	/**
	 * @param FileSyncKey $key
	 * @param boolean $fetch_from_remote_if_no_local
	 * @param boolean $strict
	 * @throws kFileSyncException
	 * @throws Exception
	 * @return array
	 */
	public static function dir_get_files(FileSyncKey $key, $strict = true)
	{
		KalturaLog::debug("key [$key], strict [$strict]");
		list($file_sync, $local) = self::getReadyFileSyncForKey($key, false, $strict);
		if($file_sync)
		{
			$file_sync = self::resolve($file_sync);
		}

		if(!$file_sync || !$local)
		{
			if($strict)
				throw new kFileSyncException("Cannot find directory file sync for key [$key]", kFileSyncException::FILE_SYNC_DOES_NOT_EXIST);
				
			KalturaLog::err("FileSync not found");
			return null;
		}

		$real_path = realpath($file_sync->getFullPath());
		if(!is_dir($real_path))
		{
			KalturaLog::info("directory was not found locally [$real_path]");
			throw new kFileSyncException("Cannot find directory on local disk [$real_path] for file sync [" . $file_sync->getId() . "]", kFileSyncException::FILE_DOES_NOT_EXIST_ON_DISK);
		}
		
		KalturaLog::info("directory was found locally at [$real_path]");
		
		$dir = dir($real_path);
		$files = array();
		while (false !== ($entry = $dir->read())) 
		{
			if($entry != '.' && $entry != '..')
				$files[] = realpath("$real_path/$entry");
		}
		$dir->close();
		
		return $files;
	}

	public static function file_get_contents ( FileSyncKey $key , $fetch_from_remote_if_no_local = true , $strict = true , $max_file_size = 0 )
	{
		$cacheStore = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_FILE_SYNC);
		if ($cacheStore)
		{
			$cacheKey = self::CACHE_KEY_PREFIX . "{$key->object_id}_{$key->object_type}_{$key->object_sub_type}_{$key->version}";
			$result = $cacheStore->get($cacheKey);
			if ($result)
			{
				KalturaLog::info("returning from cache, key [$cacheKey] size [".strlen($result)."]");
				return $result;
			}
		}

		KalturaLog::debug("key [$key], fetch_from_remote_if_no_local [$fetch_from_remote_if_no_local], strict [$strict]");
		list ( $file_sync , $local ) = self::getReadyFileSyncForKey( $key , $fetch_from_remote_if_no_local , $strict );
		if($file_sync)
		{
			$file_sync = self::resolve($file_sync);
		}

		if($file_sync)
		{
			if ($max_file_size && $file_sync->getFileSize() > $max_file_size)
			{
				KalturaLog::err('FileSync size [' . $file_sync->getFileSize() . '] exceeds the limit [' . $max_file_size . ']');
				return null;
			}
			
			$result = self::getContentsByFileSync ( $file_sync , $local , $fetch_from_remote_if_no_local , $strict );
			if ($cacheStore && $result && strlen($result) < self::MAX_CACHED_FILE_SIZE &&
				!in_array($key->object_type, self::$uncachedObjectTypes))
			{
				KalturaLog::info("saving to cache, key [$cacheKey] size [".strlen($result)."]");
				$cacheStore->set($cacheKey, $result, self::FILE_SYNC_CACHE_EXPIRY);
			}
			return $result;
		}

		KalturaLog::info("FileSync not found");
		return null;
	}

	/**
	 *
	 * @param FileSyncKey $key
	 * @param $content
	 * @param $strict - default true. use false if need to override an existing file.
	 */
	public static function file_put_contents ( FileSyncKey $key , $content , $strict = true )
	{
		KalturaLog::debug("key [$key], strict [$strict]");

		// make sure that there is not yet a record for the key
		$c = FileSyncPeer::getCriteriaForFileSyncKey( $key );
		$res = FileSyncPeer::doSelectOne( $c );
		if($res)
		{
			if($strict)
				throw new kFileSyncException("key $key already exists", kFileSyncException::FILE_SYNC_ALREADY_EXISTS);

			KalturaLog::err("File Sync key $key already exists");
		}
		else
		{
			KalturaLog::info("File Sync doesn't exist");
		}

		list($rootPath, $filePath) = self::getLocalFilePathArrForKey($key);
		$fullPath = $rootPath . $filePath; 
		$fullPath = str_replace(array('/', '\\'), array(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR), $fullPath);

		if ( !file_exists( dirname( $fullPath )))
		{
			self::fullMkdir($fullPath);
		}

		// create a file path for the current key - the fileSyncKey should already include the file path
		// place the content there
		file_put_contents ( $fullPath , $content );
		self::setPermissions($fullPath);
		self::createSyncFileForKey($rootPath, $filePath,  $key , $strict , !is_null($res), false, md5($content));
		self::encryptByFileSyncKey($key);
	}

	protected static function setPermissions($filePath)
	{

		$contentGroup = kConf::get('content_group');
		if(is_numeric($contentGroup))
			$contentGroup = intval($contentGroup);
			
		@chgrp($filePath, $contentGroup);
		
		if(is_dir($filePath))
		{
			@chmod($filePath, 0770);
			$dir = dir($filePath);
			while (false !== ($file = $dir->read()))
			{
				if($file[0] != '.')
					self::setPermissions($filePath . DIRECTORY_SEPARATOR . $file);
			}
			$dir->close();
		}
		else
		{
			@chmod($filePath, 0640);
		}
	}

	protected static function fullMkdir($filePath)
	{
		$filePath = str_replace(array('/', '\\'), array(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR), $filePath);
	
		if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
			return kFile::fullMkdir($filePath, 0770);
			
		$contentGroup = kConf::get('content_group');
		if(is_numeric($contentGroup))
			$contentGroup = intval($contentGroup);
				
	    $dirs = explode(DIRECTORY_SEPARATOR , dirname($filePath));
	    $path = '';
	    foreach ($dirs as $dir)
	    {
	        $path .= DIRECTORY_SEPARATOR . $dir;
	        if (is_dir($path))
	        	continue;
	        	
	        if(!kFile::fullMkfileDir($path, 0770))
	        	return false;
	        	
	        chgrp($path, $contentGroup);
	    }
	    return true;
	}

	public static function moveToFile ( FileSyncKey $source_key , $target_file_path, $delete_source = true , $overwrite = true)
	{
		$target_file_path = str_replace(array('/', '\\'), array(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR), $target_file_path);
	
		try
		{
			list ($fileSync, $local) = self::getReadyFileSyncForKey ( $source_key, false , true );
		}
		catch(Exception $ex)
		{
			KalturaLog::notice('could not load ready file sync for key '.$source_key);
			return false;
		}
		$file_path = $fileSync->getFullPath();

		if(file_exists($target_file_path))
		{
			KalturaLog::debug("Target file [$target_file_path] exists");
			if(!$overwrite)
			{
				KalturaLog::notice("target [$target_file_path] exists, not overwriting");
				return false;
			}
			elseif($target_file_path != $fileSync->getFullPath())
			{
				@unlink($target_file_path);
			}
			else
			{
				// target and source are the same, we do not want to delete the existing target
				// to avoid cases where copy fails and current file is lost
			}
		}
		else
		{
			KalturaLog::info("$target_file_path file doesnt exist");
		}

		// make sure folder exists
		self::fullMkdir($target_file_path);

		$copyResult = copy($file_path, $target_file_path);
		if($copyResult)
		{
			self::setPermissions($target_file_path);

			// if root in original fileSync also exists in new path (common root)
			// remove it from the new path
			if(substr_count($target_file_path, $fileSync->getFileRoot()))
			{
				$target_file_path = str_replace($fileSync->getFileRoot(), '', $target_file_path);
			}
			else
			{
				// old & new paths doesn't share root, set new root to empty string
				$fileSync->setFileRoot('');
			}
			// new path will be set either to full path or relative after common root
			$fileSync->setFilePath($target_file_path);
			$fileSync->save();
			// delete source
			if($delete_source)
			{
				// delete the source file, if fails - do not output error
				@unlink($file_path);
			}
			KalturaLog::info("successfully copied file to [$target_file_path] and updated fileSync");
			return true;
		}
		else
		{
			KalturaLog::info("copy failed - not changing filesync");
			return false;
		}
	}

	public static function moveFromFileToDirectory(FileSyncKey $directory_key, $temp_file_path, $base_file_name = null)
	{
		KalturaLog::debug("move file to directory: [$temp_file_path] to key [$directory_key]");

		$c = FileSyncPeer::getCriteriaForFileSyncKey( $directory_key );
		$c->add(FileSyncPeer::FILE_TYPE, array(FileSync::FILE_SYNC_FILE_TYPE_FILE, FileSync::FILE_SYNC_FILE_TYPE_LINK), Criteria::IN);
		$c->add(FileSyncPeer::DC, kDataCenterMgr::getCurrentDcId());

		$fileSync = FileSyncPeer::doSelectOne( $c );
		$dirFullPath = null;
		if($fileSync)
		{
			$dirFullPath = $fileSync->getFullPath();
		}
		else
		{
			list($rootPath, $filePath) = self::getLocalFilePathArrForKey($directory_key);
			$dirFullPath = $rootPath . $filePath; 
			if(!$dirFullPath)
			{
				$dirFullPath = kPathManager::getFilePath($directory_key);
				KalturaLog::info("Generated new path [$dirFullPath]");
			}
			
			$dirFullPath = str_replace(array('/', '\\'), array(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR), $dirFullPath);
	
			if (file_exists($dirFullPath))
			{
				$time = time(); 
				$dirFullPath .= $time;
				$filePath .= $time; 
			}
			else
			{
				KalturaLog::info("Creating directory [$dirFullPath] for file");
				kFile::fullMkfileDir($dirFullPath);
			}
			self::createSyncFileForKey($rootPath, $filePath, $directory_key);
		}
		
		$existing_files = glob($dirFullPath . DIRECTORY_SEPARATOR . '*');
		if(count($existing_files) >= self::MAX_FILES_IN_CATEGORY)
			throw new kFileSyncException("Exceeded max number of files [" . self::MAX_FILES_IN_CATEGORY . "] in category [$dirFullPath]");
			
		if($base_file_name)
		{
			$filesCount = 0;
			$files = glob($dirFullPath . DIRECTORY_SEPARATOR . '*');
			if ($files)
			{
				$filesCount = count($files);
			}
			$base_file_name = str_pad($filesCount, 4, 0, STR_PAD_LEFT) . '.' . $base_file_name;
		}
		else
		{
			$base_file_name = basename($temp_file_path);
		}
		
		$destination_file_path = $dirFullPath . DIRECTORY_SEPARATOR . $base_file_name;
		$success = kFile::moveFile($temp_file_path, $destination_file_path);
		self::setPermissions($dirFullPath);
		KalturaLog::debug("temp_file_path [$temp_file_path](" . filesize($temp_file_path) . ") destination_file_path [$destination_file_path](" . filesize($destination_file_path) . ")");

		if(!$success)
			throw new kFileSyncException("Could not move file from [$temp_file_path] to [{$destination_file_path}]");
	}

	public static function moveFromFile ( $temp_file_path , FileSyncKey $target_key , $strict = true, $copyOnly = false, $cacheOnly = false)
	{
		KalturaLog::debug("move file: [$temp_file_path] to key [$target_key], ");

		$c = FileSyncPeer::getCriteriaForFileSyncKey( $target_key );

		if($cacheOnly)
			$c->add(FileSyncPeer::FILE_TYPE, FileSync::FILE_SYNC_FILE_TYPE_CACHE);
		else
			$c->add(FileSyncPeer::FILE_TYPE, array(FileSync::FILE_SYNC_FILE_TYPE_FILE, FileSync::FILE_SYNC_FILE_TYPE_LINK), Criteria::IN);

		$existsFileSync = FileSyncPeer::doSelectOne( $c );
		if($existsFileSync)
		{
			if($strict)
			{
				throw new Exception ( "key [" . $target_key . "] already exists");
			}
			else
			{
				KalturaLog::err("file already exists");
			}
		}

		list($rootPath, $filePath) = self::getLocalFilePathArrForKey($target_key);
		$targetFullPath = $rootPath . $filePath; 
		if(!$targetFullPath)
		{
			$targetFullPath = kPathManager::getFilePath($target_key);
			KalturaLog::info("Generated new path [$targetFullPath]");
		}
		
		$targetFullPath = str_replace(array('/', '\\'), array(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR), $targetFullPath);

		if ( !file_exists( dirname( $targetFullPath )))
		{
			self::fullMkdir($targetFullPath);
		}

		if ( file_exists( $temp_file_path ))
		{
			KalturaLog::info("$temp_file_path file exists");
		}
		else
		{
			KalturaLog::info("$temp_file_path file doesnt exist");
		}
		
		if (file_exists($targetFullPath))
		{
			$time = time(); 
			$targetFullPath .= $time;
			$filePath .= $time; 
		}

		if($copyOnly)
		{
			$success = copy($temp_file_path, $targetFullPath);
		}
		else
		{
			$success = kFile::moveFile($temp_file_path, $targetFullPath);
		}

		if($success)
		{
			self::setPermissions($targetFullPath);
			if(!$existsFileSync)
				self::createSyncFileForKey($rootPath, $filePath, $target_key, $strict, false, $cacheOnly);
			self::encryptByFileSyncKey($target_key);
		}
		else
		{
			KalturaLog::err("could not move file from [$temp_file_path] to [{$targetFullPath}]");
			throw new Exception ( "Could not move file from [$temp_file_path] to [{$targetFullPath}]");
		}

	}

	public static function copyFromFile ($temp_file_path , FileSyncKey $target_key , $strict = true)
	{
		KalturaLog::debug("copy file: [$temp_file_path] to key [$target_key], ");
		kFileSyncUtils::moveFromFile($temp_file_path, $target_key, $strict, true);
	}


	/**
	 *
	 * @param FileSyncKey $source_key
	 * @param FileSyncKey $target_key
	 * @param boolean $fetch_from_remote_if_no_local
	 * @param boolean $strict  - will throw exception if not found
	 */
	public static function hardCopy ( FileSyncKey $source_key , FileSyncKey $target_key , $fetch_from_remote_if_no_local = true , $strict = true  )
	{
		// TODO - this implementation is the NAIVE one and can cause problems with big files
		// a better implementation will be to copy the files on disk incase of local files
		// BETTER - use the link feature (not yet implemented)
		$content = self::file_get_contents( $source_key , $fetch_from_remote_if_no_local , $strict );
		self::file_put_contents( $target_key , $content , $strict );
	}

	/**
	 * resolve the source filesync when a FileSync input is a LINK
	 *
	 * @param FileSync $file
	 * @return FileSync
	 */
	public static function resolve(FileSync $file)
	{
		$parent = null;
		if($file->getLinkedId())
		{
			$source_file_sync = FileSyncPeer::retrieveByPK($file->getLinkedId());
			if(!$source_file_sync)
				return $file;

			$parent = self::resolve($source_file_sync);
		}
		if(!$parent)
		{
			return $file;
		}
		else
		{
			return $parent;
		}
	}

	/**
	 *
	 * @param FileSyncKey $source_key
	 * @param FileSyncKey $target_key
	 */
	public static function softCopy ( FileSyncKey $source_key , FileSyncKey $target_key )
	{
		# create new rows in table - type FILE_SYNC_FILE_TYPE_LINK , links to existing objects in table
		# each row links to the source in the same DC
		self::createSyncFileLinkForKey($target_key, $source_key);
	}

	/**
	 * Get the FileSyncKey object by its file sync object
	 *
	 * @param FileSync $fileSync
	 * @return FileSyncKey
	 */
	public static function getKeyForFileSync(FileSync $fileSync)
	{
		$key = new FileSyncKey();

		$key->object_type = $fileSync->getObjectType();
		$key->object_id = $fileSync->getObjectId();
		$key->version = $fileSync->getVersion();
		$key->object_sub_type = $fileSync->getObjectSubType();
		$key->partner_id = $fileSync->getPartnerId();

		return $key;
	}

	/**
	 * Get the local FileSync object by its key
	 *
	 * @param FileSyncKey $key
	 * @param boolean $strict - will throw exception if not found
	 * @return FileSync
	 * @throws Exception
	 */
	public static function getLocalFileSyncForKey ( FileSyncKey $key , $strict = true )
	{
		$dc = kDataCenterMgr::getCurrentDc();
		$dc_id = $dc["id"];
		$c = new Criteria();
		$c = FileSyncPeer::getCriteriaForFileSyncKey( $key );
		$c->addAnd ( FileSyncPeer::DC , $dc_id );

		$file_sync_list = FileSyncPeer::doSelect( $c );
		if ( $file_sync_list == null )
		{
			if ( $strict )
				throw new Exception ( "Cannot find ANY FileSync for " . ( $key ) );
			else
				return false;
		}
		if ( count($file_sync_list) > 1 )
		{
			// something bad happened! on one DC, FileSyncKey should be unique
		}
		return $file_sync_list[0];
	}

	/**
	 * Get all the external FileSync objects by its key
	 *
	 * @param FileSyncKey $key
	 * @return array<FileSync>
	 * @throws PropelException
	 * @throws kFileSyncException
	 */
	public static function getAllReadyExternalFileSyncsForKey(FileSyncKey $key)
	{
		if(is_null($key->partner_id))
			throw new kFileSyncException("partner id not defined for key [$key]", kFileSyncException::FILE_SYNC_PARTNER_ID_NOT_DEFINED);

		self::prepareStorageProfilesForSort($key->partner_id);

		$c = new Criteria();
		$c = FileSyncPeer::getCriteriaForFileSyncKey( $key );
		$c->add(FileSyncPeer::FILE_TYPE, FileSync::FILE_SYNC_FILE_TYPE_URL);
		$c->add(FileSyncPeer::STATUS, FileSync::FILE_SYNC_STATUS_READY);
		$c->add(FileSyncPeer::DC, self::$storageProfilesOrder, Criteria::IN);

		$fileSyncs = FileSyncPeer::doSelect($c);
		if(
			count($fileSyncs) > 1
			&&
			PermissionPeer::isValidForPartner(PermissionName::FEATURE_REMOTE_STORAGE_DELIVERY_PRIORITY, $key->partner_id)
		)
		{
			uasort($fileSyncs, array('self', 'compareStorageProfiles'));
		}
		return $fileSyncs;
	}

	/**
	 * Get the READY external FileSync object by its key
	 *
	 * @param FileSyncKey $key
	 * @param int $externalStorageId
	 * @return FileSync
	 */
	public static function getReadyExternalFileSyncForKey(FileSyncKey $key, $externalStorageId = null)
	{
		return self::getExternalFileSyncForKeyByStatus($key, $externalStorageId, array(FileSync::FILE_SYNC_STATUS_READY));
	}

	/**
	 * Get the READY/PENDING external FileSync object by its key
	 *
	 * @param FileSyncKey $key
	 * @param int $externalStorageId
	 * @return FileSync
	 */
	public static function getReadyPendingExternalFileSyncForKey(FileSyncKey $key, $externalStorageId = null)
	{
		return self::getExternalFileSyncForKeyByStatus($key, $externalStorageId, array(FileSync::FILE_SYNC_STATUS_READY, FileSync::FILE_SYNC_STATUS_PENDING));
	}


/**
	 * Get the external FileSync object by its key and statuses
	 *
	 * @param FileSyncKey $key
	 * @param int $externalStorageId
	 * @param array $statuses an array of required status values
	 * @return FileSync
	 */
	protected static function getExternalFileSyncForKeyByStatus(FileSyncKey $key, $externalStorageId = null, $statuses = array())
	{
		if(is_null($key->partner_id))
			throw new kFileSyncException("partner id not defined for key [$key]", kFileSyncException::FILE_SYNC_PARTNER_ID_NOT_DEFINED);

		self::prepareStorageProfilesForSort($key->partner_id);

		$c = new Criteria();
		$c = FileSyncPeer::getCriteriaForFileSyncKey( $key );

		if(is_null($externalStorageId))
		{
			$c->addAnd ( FileSyncPeer::FILE_TYPE , FileSync::FILE_SYNC_FILE_TYPE_URL ); // any external
			$c->addAnd ( FileSyncPeer::DC , self::$storageProfilesOrder, Criteria::IN );
		}
		else
		{
			$c->addAnd ( FileSyncPeer::DC , $externalStorageId );
		}

		if (!empty($statuses)) {
		    $c->addAnd ( FileSyncPeer::STATUS , $statuses, Criteria::IN );
		}


		if(!PermissionPeer::isValidForPartner(PermissionName::FEATURE_REMOTE_STORAGE_DELIVERY_PRIORITY, $key->partner_id))
			return FileSyncPeer::doSelectOne($c);

		$fileSyncs = FileSyncPeer::doSelect($c);
		if(count($fileSyncs) > 1)
			uasort($fileSyncs, array('self', 'compareStorageProfiles'));

		return reset($fileSyncs);
	}

	/**
	 * @param FileSync $fileSyncA
	 * @param FileSync $fileSyncB
	 * @return number
	 */
	public static function compareStorageProfiles($fileSyncA, $fileSyncB)
	{
		if(!is_array(self::$storageProfilesOrder) || !count(self::$storageProfilesOrder))
			return 0;

		$a = array_search($fileSyncA->getDc(), self::$storageProfilesOrder);
		$b = array_search($fileSyncB->getDc(), self::$storageProfilesOrder);

		if ($a == $b)
			return 0;

		return ($a < $b) ? -1 : 1;
	}

	/**
	 * Prepare storage profiles array for sorting
	 *
	 * @param int $partnerId
	 * @throws PropelException
	 */
	protected static function prepareStorageProfilesForSort($partnerId)
	{
		if(!is_null(self::$storageProfilesOrder))
		{
			return;
		}

		$criteria = new Criteria();
		$criteria->add(StorageProfilePeer::PARTNER_ID, $partnerId);
		$criteria->add(StorageProfilePeer::DELIVERY_STATUS, StorageProfileDeliveryStatus::BLOCKED, Criteria::NOT_EQUAL);
		$criteria->addAscendingOrderByColumn(StorageProfilePeer::DELIVERY_PRIORITY);

		// Using doSelect instead of doSelectStmt for the ID column so that we can take adavntage of the query cache
		self::$storageProfilesOrder = array();
		$results = StorageProfilePeer::doSelect($criteria);
		foreach ($results as $result)
		{
			self::$storageProfilesOrder[] = $result->getId();
		}
	}

	/**
	 * Get the internal from kaltura data centers only FileSync object by its key
	 *
	 * @param FileSyncKey $key
	 * @return FileSync
	 */
	public static function getReadyInternalFileSyncForKey(FileSyncKey $key)
	{
		$c = new Criteria();
		$c = FileSyncPeer::getCriteriaForFileSyncKey( $key );
		$c->addAnd ( FileSyncPeer::FILE_TYPE , FileSync::FILE_SYNC_FILE_TYPE_URL, Criteria::NOT_EQUAL);
		$c->addAnd ( FileSyncPeer::STATUS , FileSync::FILE_SYNC_STATUS_READY );

		return FileSyncPeer::doSelectOne( $c );
	}

	/**
	 * Get the internal from kaltura data centers only FileSync object by its key
	 *
	 * @param FileSyncKey $key
	 * @return FileSync
	 */
	public static function getReadyInternalFileSyncsForKey(FileSyncKey $key)
	{
		$c = new Criteria();
		$c = FileSyncPeer::getCriteriaForFileSyncKey( $key );
		$c->addAnd ( FileSyncPeer::FILE_TYPE , FileSync::FILE_SYNC_FILE_TYPE_URL, Criteria::NOT_EQUAL);
		$c->addAnd ( FileSyncPeer::STATUS , FileSync::FILE_SYNC_STATUS_READY );

		return FileSyncPeer::doSelect( $c );
	}

	/**
	 * Create a path on disk for the LOCAL FileSync that is coupled with the key.
	 * Returns the NON-SAVED FileSync populated with the fileRoot and filePath
	 *
	 * @param FileSyncKey $key
	 * @param boolean $strict  - will throw exception if not found
	 * @return FileSync
	 */
	public static function createLocalPathForKey ( FileSyncKey $key , $strict = true )
	{
		$file_sync = self::getLocalFileSyncForKey ( $key , $strict );
		if ( $file_sync )
		{
			list($file_root, $real_path) = kPathManager::getFilePathArr($key);
			$file_sync->setFileRoot ( $file_root );
			$file_sync->setFilePath ( $real_path );
		}
		else
		{
			$error = "Cannot find object type [" . $key->getObjectType() . "] with object_id [" . $key->getObjectId() . "] for FileSync id [" . $key->getId() . "]";
			KalturaLog::err($error);
			throw new Exception ( $error );
		}

		return $file_sync;
	}

	/**
	 *
	 * @param FileSyncKey $key
	 * @param boolean $strict  - will throw exception if not found
	 * @return FileSync
	 */
	public static function getOriginFileSyncForKey ( FileSyncKey $key , $strict = true )
	{
		$c = FileSyncPeer::getCriteriaForFileSyncKey( $key );
		$c->addAnd ( FileSyncPeer::ORIGINAL , 1 );

		$file_sync_list = FileSyncPeer::doSelect( $c );
		if ( $file_sync_list == null )
		{
			if ( $strict )
				throw new Exception ( "Cannot find ANY FileSync for " . ( $key ) );
			else
				return false;
		}
		if ( count($file_sync_list) > 1 )
		{
			// something bad happened! on one DC, FileSyncKey should be unique
		}

		return $file_sync_list[0];
	}

	/**
	 *
	 * @param FileSyncKey $key
	 * @param boolean $fetch_from_remote_if_no_local
	 * @param boolean $strict - will throw exception if not found
	 * @param boolean $resolve - will resolve the file sync
	 * @return array
	 * @throws PropelException
	 */
	public static function getReadyFileSyncForKey ( FileSyncKey $key , $fetch_from_remote_if_no_local = false , $strict = true , $resolve = true )
	{
		KalturaLog::debug("key [$key], fetch_from_remote_if_no_local [$fetch_from_remote_if_no_local], strict [$strict]");
		$dc = kDataCenterMgr::getCurrentDc();
		$dc_id = $dc["id"];
		$c = new Criteria();
		$c = FileSyncPeer::getCriteriaForFileSyncKey( $key );
		if ( ! $fetch_from_remote_if_no_local )
		{
			// if $fetch_from_remote_if_no_local is true - don't restrict to the current DC - this will save an extra hit to the DB in case the file is not present
			$c->addAnd ( FileSyncPeer::DC , $dc_id );
		}
		// search only for ready
		$c->addAnd ( FileSyncPeer::STATUS , FileSync::FILE_SYNC_STATUS_READY );
		$c->addAscendingOrderByColumn(FileSyncPeer::DC); // favor local data centers instead of remote storage locations

		$file_sync_list = FileSyncPeer::doSelect( $c );
		if ( $file_sync_list == null )
		{
			if ( $strict )
			{
				throw new Exception ( "Cannot find ANY FileSync for " . ( $key ) );
			}
			else
			{
				KalturaLog::notice("FileSync was not found");
				return array ( null , false );
			}
		}

		$desired_file_sync = null;
		$local = false;
		foreach ( $file_sync_list as $file_sync )
		{
			$tmp_file_sync = $file_sync;
			// make sure not link and work on original
			
			if($resolve)
				$tmp_file_sync = self::resolve($file_sync);
				
			if ($tmp_file_sync->getStatus() != FileSync::FILE_SYNC_STATUS_READY)
				continue;
			

			// always prefer the current dc
			if ( $tmp_file_sync->getDc() == $dc_id)
			{
				$desired_file_sync = $tmp_file_sync;
				$local = true;
				break;
			}
			else if ( $fetch_from_remote_if_no_local == true &&
					($desired_file_sync == null || $tmp_file_sync->getDc() < $desired_file_sync->getDc()) )			// prefer local file syncs if they exist
			{
				$desired_file_sync = $tmp_file_sync;
			}
		}

		if ( $desired_file_sync )
		{
			if ($local)
				KalturaLog::info("FileSync was found locally");
			else
				KalturaLog::info("FileSync was found but doesn't exists locally");

			return array ( $desired_file_sync , $local );
		}

		if ( $strict )
		{
			throw new Exception ( "Cannot find EXACT FileSync for " . ( $key ) );
		}
		else
		{
			KalturaLog::info("exact FileSync was not found");
			return array ( null , false );
		}
	}

	/**
	 *
	 * @param FileSyncKey $key
	 * @return string
	 */
	public static function getLocalFilePathForKey ( FileSyncKey $key , $strict = false )
	{
		$path = implode('', self::getLocalFilePathArrForKey ($key, $strict));
		KalturaLog::info("path [$path]");
		return $path; 
	}
	
	/**
	 *
	 * @param FileSyncKey $key
	 * @return array
	 */
	public static function getLocalFilePathArrForKey ( FileSyncKey $key , $strict = false )
	{
		KalturaLog::debug("key [$key], strict [$strict]");
		$file_sync = self::getLocalFileSyncForKey( $key , $strict );
		if ( $file_sync )
		{
			$parent_file_sync = self::resolve($file_sync);
			$pathArr = array($parent_file_sync->getFileRoot() , $parent_file_sync->getFilePath());
			return $pathArr;
		}

		// TODO - should return null if doesn't exists
		return kPathManager::getFilePathArr($key);
	}

	/**
	 *
	 * @param FileSyncKey $key
	 * @return string
	 * @throws Exception
	 */
	public static function getRelativeFilePathForKey ( FileSyncKey $key , $strict = false )
	{
		KalturaLog::debug("key [$key], strict [$strict]");
		$file_sync = self::getLocalFileSyncForKey( $key , $strict );
		if ( $file_sync )
		{
			$parent_file_sync = self::resolve($file_sync);
			$path = $parent_file_sync->getFilePath();
			KalturaLog::info("path [$path]");
			return $path;
		}
	}

	public static function getReadyLocalFilePathForKey( FileSyncKey $key , $strict = false )
	{
		KalturaLog::debug("key [$key], strict [$strict]");
		list ( $file_sync , $local )= self::getReadyFileSyncForKey( $key , false , $strict );
		if ( $file_sync )
		{
			$parent_file_sync = self::resolve($file_sync);
			$path = $parent_file_sync->getFullPath();
			KalturaLog::info("path [$path]");
			return $path;
		}
	}


	/**
	 * @param FileSyncKey $key
	 * @param $file_root
	 * @param $real_path
	 * @param $strict
	 * @return SyncFile
	 */
	public static function createSyncFileForKey ( $rootPath, $filePath, FileSyncKey $key , $strict = true , $alreadyExists = false, $cacheOnly = false, $md5 = null)
	{
		KalturaLog::debug("key [$key], strict[$strict], already_exists[$alreadyExists]");
		// TODO - see that if in strict mode - there are no duplicate keys -> update existing records AND set the other DC's records to PENDING
		$dc = kDataCenterMgr::getCurrentDc();
		$dcId = $dc["id"];

		// create a FileSync for the current DC with status READY
		if ( $alreadyExists )
		{
			$c = FileSyncPeer::getCriteriaForFileSyncKey( $key );
			$c->add (FileSyncPeer::DC, $dcId);
			if($cacheOnly)
				$c->add(FileSyncPeer::FILE_TYPE, FileSync::FILE_SYNC_FILE_TYPE_CACHE);

			$currentDCFileSync = FileSyncPeer::doSelectOne( $c );
		}
		else
		{
			$currentDCFileSync = FileSync::createForFileSyncKey( $key );
			$currentDCFileSync->setDc( $dcId );
			$currentDCFileSync->setFileRoot ( $rootPath );
			$currentDCFileSync->setFilePath ( $filePath );
			$currentDCFileSync->setPartnerId ( $key->partner_id);
			$currentDCFileSync->setOriginal ( 1 );
			if (!is_null($md5))
				$currentDCFileSync->setContentMd5($md5);
		}

		$fullPath = $currentDCFileSync->getFullPath();
		$isDir = is_dir($fullPath);
		
		if ( file_exists( $fullPath ) )
		{
			$currentDCFileSync->setFileSizeFromPath ( $fullPath );
			$currentDCFileSync->setStatus( FileSync::FILE_SYNC_STATUS_READY );
		}
		else
		{
			$currentDCFileSync->setFileSize ( -1 );

			if ($strict)
				$currentDCFileSync->setStatus( FileSync::FILE_SYNC_STATUS_ERROR );
			else
				$currentDCFileSync->setStatus( FileSync::FILE_SYNC_STATUS_PENDING );
		}
		if($cacheOnly)
			$currentDCFileSync->setFileType ( FileSync::FILE_SYNC_FILE_TYPE_CACHE );
		else
			$currentDCFileSync->setFileType ( FileSync::FILE_SYNC_FILE_TYPE_FILE );
		
		$currentDCFileSync->setIsDir($isDir);
		$currentDCFileSync->save();

		if($cacheOnly)
			return $currentDCFileSync;

		// create records for all other DCs with status PENDING
		if ( $alreadyExists )
		{
			$c = FileSyncPeer::getCriteriaForFileSyncKey( $key );
			$c->add ( FileSyncPeer::DC , $dcId , Criteria::NOT_IN );
			$remoteDCFileSyncList  = FileSyncPeer::doSelect( $c );

			foreach ( $remoteDCFileSyncList as $remoteDCFileSync )
			{
				$remoteDCFileSync->setStatus( FileSync::FILE_SYNC_STATUS_PENDING );
				$remoteDCFileSync->setPartnerID ( $key->partner_id );
				$remoteDCFileSync->setIsDir($isDir);
				$remoteDCFileSync->save();
			}
		}
		else
		{
			if (self::shouldSyncFileObjectType($currentDCFileSync))
			{
				$otherDCs = kDataCenterMgr::getAllDcs( );
				foreach ( $otherDCs as $remoteDC )
				{
					$remoteDCFileSync = FileSync::createForFileSyncKey( $key );
					$remoteDCFileSync->setDc( $remoteDC["id"] );
					$remoteDCFileSync->setStatus( FileSync::FILE_SYNC_STATUS_PENDING );
					$remoteDCFileSync->setFileType( FileSync::FILE_SYNC_FILE_TYPE_FILE );
					$remoteDCFileSync->setOriginal ( 0 );
					$remoteDCFileSync->setPartnerId ( $key->partner_id );
					$remoteDCFileSync->setIsDir($isDir);
					$remoteDCFileSync->setFileSize($currentDCFileSync->getFileSize());
					$remoteDCFileSync->setOriginalId($currentDCFileSync->getId());
					$remoteDCFileSync->setOriginalDc($currentDCFileSync->getDc());
					$remoteDCFileSync->save();

					kEventsManager::raiseEvent(new kObjectAddedEvent($remoteDCFileSync));
				}
			}
			kEventsManager::raiseEvent(new kObjectAddedEvent($currentDCFileSync));
		}

		return $currentDCFileSync;
	}

	/**
	 * @param FileSyncKey $key
	 * @param StorageProfile $externalStorage
	 * @return FileSync
	 */
	public static function createPendingExternalSyncFileForKey(FileSyncKey $key, StorageProfile $externalStorage, $isDir = false)
	{
		$externalStorageId = $externalStorage->getId();
		KalturaLog::debug("key [$key], externalStorage [$externalStorageId]");

		list($fileRoot, $realPath) = kPathManager::getFilePathArr($key, $externalStorageId);

		$c = FileSyncPeer::getCriteriaForFileSyncKey( $key );
		$c->add(FileSyncPeer::DC, $externalStorageId);
		$fileSync = FileSyncPeer::doSelectOne($c);

		if(!$fileSync)
			$fileSync = FileSync::createForFileSyncKey($key);

		$fileSync->setDc( $externalStorageId );
		$fileSync->setFileRoot ( $fileRoot );
		$fileSync->setFilePath ( $realPath );
		$fileSync->setFileSize ( -1 );
		$fileSync->setStatus( FileSync::FILE_SYNC_STATUS_PENDING );
		$fileSync->setOriginal ( false );
		$fileSync->setIsDir($isDir);

		if($externalStorage->getProtocol() == StorageProfile::STORAGE_KALTURA_DC)
		{
			$fileSync->setFileType ( FileSync::FILE_SYNC_FILE_TYPE_FILE );
		}
		else
		{
			$fileSync->setFileType ( FileSync::FILE_SYNC_FILE_TYPE_URL );
		}
		$fileSync->save();

		kEventsManager::raiseEvent(new kObjectAddedEvent($fileSync));

		return $fileSync;
	}

	/**
	 * @param FileSyncKey $key
	 * @param string $url
	 * @param StorageProfile $externalStorage
	 * @return SyncFile
	 */
	public static function createReadyExternalSyncFileForKey(FileSyncKey $key, $url, StorageProfile $externalStorage)
	{
		$externalStorageId = $externalStorage->getId();
		KalturaLog::debug("key [$key], externalStorage [$externalStorageId]");

		$fileRoot = '';
		$deliveryProfile = DeliveryProfilePeer::getRemoteDeliveryByStorageId(DeliveryProfileDynamicAttributes::init($externalStorageId, ''));
		if($deliveryProfile)
			$fileRoot = $deliveryProfile->getUrl();
			
		$filePath = str_replace($fileRoot, '', $url);

		$c = FileSyncPeer::getCriteriaForFileSyncKey( $key );
		$c->add(FileSyncPeer::DC, $externalStorageId);
		$fileSync = FileSyncPeer::doSelectOne($c);

		if(!$fileSync)
			$fileSync = FileSync::createForFileSyncKey($key);

		$fileSync->setDc		( $externalStorageId );
		$fileSync->setFileRoot	( $fileRoot );
		$fileSync->setFilePath	( $filePath );
		$fileSync->setFileSize	( -1 );
		$fileSync->setStatus	( FileSync::FILE_SYNC_STATUS_READY );
		$fileSync->setOriginal	( false );
		$fileSync->setFileType	( FileSync::FILE_SYNC_FILE_TYPE_URL );
		$fileSync->save();

		kEventsManager::raiseEvent(new kObjectAddedEvent($fileSync));

		return $fileSync;
	}

	/**
	 * @param FileSyncKey $key
	 * @param $file_root
	 * @param $real_path
	 * @return SyncFile
	 */
	public static function createSyncFileLinkForKey ( FileSyncKey $target_key , FileSyncKey $source_key )
	{
		KalturaLog::debug("target_key [$target_key], source_key [$source_key]");
		// TODO - see that if in strict mode - there are no duplicate keys -> update existing records AND set the other DC's records to PENDING
		$dc = kDataCenterMgr::getCurrentDc();
		$dc_id = $dc["id"];

		// load all source file syncs
		$c = new Criteria();
		$c = FileSyncPeer::getCriteriaForFileSyncKey( $source_key );
		$file_sync_list = FileSyncPeer::doSelect( $c );
		if (!$file_sync_list)
		{
			KalturaLog::notice("Warning: no source. target_key [$target_key], source_key [$source_key] ");
			return null;
		}

		$source_file_syncs = array();
		foreach($file_sync_list as $file_sync)
		{
			$file_sync = self::resolve($file_sync); // we only want to link to a source and not to a link.
			$source_file_syncs[] = $file_sync;
		}

		// find the current dc file sync
		$current_dc_source_file = null;
		foreach ( $source_file_syncs as $source_file_sync )
		{
			if ($source_file_sync->getDc() == $dc_id)
				$current_dc_source_file = $source_file_sync;
		}
		if (!$current_dc_source_file)
			$current_dc_source_file = reset($source_file_syncs);

		// create the remote file syncs
		foreach ( $source_file_syncs as $source_file_sync )
		{
			$remote_dc_file_sync = FileSync::createForFileSyncKey( $target_key );
			$remote_dc_file_sync->setDc( $source_file_sync->getDc() );
			$remote_dc_file_sync->setStatus( $source_file_sync->getStatus() );
			$remote_dc_file_sync->setOriginal ( $current_dc_source_file == $source_file_sync );
			$remote_dc_file_sync->setFileSize ( -1 );

			if($source_file_sync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_URL)
			{
				$remote_dc_file_sync->setFileType ( FileSync::FILE_SYNC_FILE_TYPE_URL );
				$remote_dc_file_sync->setFileRoot ( $source_file_sync->getFileRoot() );
				$remote_dc_file_sync->setFilePath ( $source_file_sync->getFilePath() );
			}
			else
			{
				$remote_dc_file_sync->setFileType ( FileSync::FILE_SYNC_FILE_TYPE_LINK );
			}
			
			$remote_dc_file_sync->setLinkedId ( $source_file_sync->getId() );
			self::incrementLinkCountForFileSync($source_file_sync);
			$remote_dc_file_sync->setPartnerID ( $target_key->partner_id );
			$remote_dc_file_sync->save();

			if ($current_dc_source_file == $source_file_sync)
				$current_dc_target_file = $remote_dc_file_sync;		// throw the added event for the current dc last
			else
				kEventsManager::raiseEvent(new kObjectAddedEvent($remote_dc_file_sync));
		}
		kEventsManager::raiseEvent(new kObjectAddedEvent($current_dc_target_file));
	}

	/**
	 * increment the link_count field on a source file_sync record
	 *
	 * @param FileSync $fileSync
	 * @return void
	 */
	public static function incrementLinkCountForFileSync(FileSync $fileSync)
	{
		$current_count = (((int)$fileSync->getLinkCount())? $fileSync->getLinkCount(): 0) + 1;
		$fileSync->setLinkCount($current_count);
		$fileSync->save();
	}
	
	/**
	 * decrement the link_count field on a source file_sync record
	 *
	 * @param FileSync $fileSync
	 * @return void
	 */
	public static function decrementLinkCountForFileSync(FileSync $fileSync = null)
	{
		if(!$fileSync)
			return;
		
		$current_count = (((int)$fileSync->getLinkCount()) ? $fileSync->getLinkCount()-1 : 0);
		$fileSync->setLinkCount($current_count);
		$fileSync->save();
	}

	/**
	 * mark file as deleted, return deleted version
	 * @param FileSyncKey $key
	 * @param bool $strict
	 * @param bool $fromKalturaDcsOnly
	 * @return string
	 */
	public static function deleteSyncFileForKey( FileSyncKey $key , $strict = false , $fromKalturaDcsOnly = false)
	{
		if ( !$key )
		{
			if ( $strict )
				throw new Exception ( "Empty key");
			return null;
		}

		//Retrieve all file sync for key
		$c = new Criteria();
		$c = FileSyncPeer::getCriteriaForFileSyncKey( $key );
		if($fromKalturaDcsOnly)
			$c->add(FileSyncPeer::FILE_TYPE, FileSync::FILE_SYNC_FILE_TYPE_URL, Criteria::NOT_EQUAL);
		$file_sync_list = FileSyncPeer::doSelect( $c );
		
		foreach($file_sync_list as $file_sync)
		{
			/* @var $fileSync FileSync */
			if($file_sync->getLinkedId())
			{
				$newStatus = FileSync::FILE_SYNC_STATUS_PURGED;
				self::decrementLinkCountForFileSync(FileSyncPeer::retrieveByPK($file_sync->getLinkedId()));
			}
			else
			{
				if($file_sync->getLinkCount() == 0)
				{
					$newStatus = FileSync::FILE_SYNC_STATUS_DELETED;
				}
				elseif($file_sync->getLinkCount() > 100)
				{
					KalturaLog::notice("The file sync [" . $file_sync->getId() . "] is associated with [" . $file_sync->getLinkCount() . "] links and won't be deleted");
					return null;
				}
				else
				{
					$newStatus = FileSync::FILE_SYNC_STATUS_PURGED;
					self::convertLinksToFiles($file_sync);
				}
			}
			
			$file_sync->setStatus($newStatus);
			$file_sync->save();
		}
	}

	/**
	 * gets a source file of current DC, will make sure all links points to that source
	 * are converted to files on all DCs
	 *
	 * @param FileSyncKey $key
	 * @return void
	 */
	protected static function convertLinksToFiles(FileSync $fileSync)
	{
		$linkTotalCount = 0;
		/* @var $fileSync FileSync */

		// for each source, find its links and fix them
		$c = new Criteria();
		$c->add(FileSyncPeer::DC, $fileSync->getDc());
		$c->add(FileSyncPeer::FILE_TYPE, array(FileSync::FILE_SYNC_FILE_TYPE_LINK, FileSync::FILE_SYNC_FILE_TYPE_URL), Criteria::IN);
		$c->add(FileSyncPeer::LINKED_ID, $fileSync->getId());
		$c->addAscendingOrderByColumn(FileSyncPeer::PARTNER_ID);

		//relink the links into groups of 100 links
		$c->setLimit(100);

		$links = FileSyncPeer::doSelect($c);
		
		//check if any links were returned in the do select if not no need to continue
		if(!count($links))
			return;
		
		// choose the first link and convert it to file
		$firstLink = array_shift($links);
		/* @var $firstLink FileSync */
		if($firstLink)
		{
			$firstLink->setStatus($fileSync->getStatus());
			$firstLink->setFileSize($fileSync->getFileSize());
			$firstLink->setFileRoot($fileSync->getFileRoot());
			$firstLink->setFilePath($fileSync->getFilePath());
			$firstLink->setFileType($fileSync->getFileType());
			$firstLink->setEncryptionKey($fileSync->getEncryptionKey());
			$firstLink->setLinkedId(0); // keep it zero instead of null, that's the only way to know it used to be a link.
			$firstLink->setIsDir($fileSync->getIsDir());
			if (!is_null($fileSync->getOriginalDc()))
			{
				$firstLink->setOriginalDc($fileSync->getOriginalDc());
				$firstLink->unsetOriginalId();		// recalculate the original id when importing the file sync
			}
			$firstLink->save();
		}
		
		while(count($links))
		{
			// change all the rest of the links to point on the new file sync
			foreach($links as $link)
			{
				$linkTotalCount += count($links);
				/* @var $link FileSync */
				$link->setStatus($fileSync->getStatus());
				$link->setLinkedId($firstLink->getId());
				$link->save();
			}
			
			FileSyncPeer::clearInstancePool();
			$links = FileSyncPeer::doSelect($c);
		}
		
		if($firstLink)
		{
			$firstLink->setLinkCount($linkTotalCount);
			$firstLink->save();
		}
	}

	/**
	 * mark file as undeleted, return ?
	 * @param FileSyncKey $key
	 * @return unknown_type
	 */
	public static function undeleteSyncFile( FileSyncKey $key )
	{
		// TODO - implement undelete, remember to undelete all DC's
	}

	/**
	 *
	 * @param FileSync $key
	 * @return ISyncableFile
	 */
	public static function retrieveObjectForFileSync ( FileSync $file_sync )
	{
		KalturaLog::debug("FileSync id [" . $file_sync->getId() . "]" );
		return kFileSyncObjectManager::retrieveObject( $file_sync->getObjectType(), $file_sync->getObjectId() );
	}

	/**
	 *
	 * @param FileSyncKey $sync_key
	 * @return ISyncableFile
	 */
	public static function retrieveObjectForSyncKey ( FileSyncKey  $sync_key )
	{
		return kFileSyncObjectManager::retrieveObject( $sync_key->object_type, $sync_key->object_id );
	}
	
	public static function calcObjectNewVersion($object_id, $version, $object_type, $object_sub_type)
	{
		if(self::wasFileSyncLimitationReached($object_id, $version, $object_type, $object_sub_type))
		{
			throw new kCoreException("File sync limitation per single object per day was reached for object id " . $object_id
									, kCoreException::MAX_FILE_SYNCS_FOR_OBJECT_PER_DAY_REACHED, $object_id);
		}

		return kDataCenterMgr::incrementVersion($version);
	}
	
	public static function wasFileSyncLimitationReached($object_id, $version, $object_type, $object_sub_type)
	{		
		if($version > self::FILE_SYNC_MIN_VERSION_VALIDATE)
		{			
			$queryVersion = $version-self::FILE_SYNC_MIN_VERSION_VALIDATE;		
			$c = new Criteria();
			$c->add(FileSyncPeer::OBJECT_ID, $object_id);
			$c->add(FileSyncPeer::OBJECT_TYPE, $object_type);
			$c->add(FileSyncPeer::OBJECT_SUB_TYPE, $object_sub_type);
			//Version in the db is of type varchar so to avoid string compare we need to add this as custom 
			$c->add(FileSyncPeer::VERSION, FileSyncPeer::VERSION . " <= $queryVersion", Criteria::CUSTOM);
			$c->addDescendingOrderByColumn(FileSyncPeer::CREATED_AT);
			
			FileSyncPeer::setUseCriteriaFilter(false);
			$res = FileSyncPeer::doSelectOne($c);
			FileSyncPeer::setUseCriteriaFilter(true);
			if($res)
			{
				if($res->getCreatedAt(null) > (time()-86400))
					return true;
			}
		}
		
		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		/* @var $object FileSync */
		$c = new Criteria();
		$c->add(FileSyncPeer::DC, $object->getDc());
		$c->add(FileSyncPeer::FILE_TYPE, array(FileSync::FILE_SYNC_FILE_TYPE_LINK, FileSync::FILE_SYNC_FILE_TYPE_URL), Criteria::IN);
		$c->add(FileSyncPeer::LINKED_ID, $object->getId());
		$c->addAscendingOrderByColumn(FileSyncPeer::ID);
		$c->setLimit(100);

		$offset = 0;
		$links = FileSyncPeer::doSelect($c);
		while($links)
		{
			$offset += count($links);
			foreach($links as $link)
			{
				$link->setStatus($object->getStatus());
				$link->save();
			}
			$c->setOffset($offset);
			$links = FileSyncPeer::doSelect($c);
		}	
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		$noneValidStatuses = array(
			FileSync::FILE_SYNC_STATUS_DELETED,
			FileSync::FILE_SYNC_STATUS_PURGED,
		);

		if(	$object instanceof FileSync
			&& $object->getLinkCount()
			&& in_array(FileSyncPeer::STATUS, $modifiedColumns)
			&& !in_array($object->getStatus(), $noneValidStatuses)
		)
			return true;

		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::objectAdded()
	 */
	public function objectAdded(BaseObject $object, BatchJob $raisedJob = null)
	{
		$this->deleteOldFileSyncVersions($object);
		return true;
	}

	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::shouldConsumeAddedEvent()
	 */
	public function shouldConsumeAddedEvent(BaseObject $object) {
		if(	$object instanceof FileSync && $this->hasOldVersionsForDelete($object) )
			return true;
		else
			return false;
	}

	private function hasOldVersionsForDelete(FileSync $fileSync)
	{
		if(!is_numeric($fileSync->getVersion()))
			return false;
		if (kConf::hasParam('num_of_old_file_sync_versions_to_keep'))
		{
			$keepCount = kConf::get('num_of_old_file_sync_versions_to_keep');
			$intVersion = intval($fileSync->getVersion());
			if($intVersion - $keepCount > 0)
				return true;
		}
		return false;
	}

	private function deleteOldFileSyncVersions(FileSync $newFileSync)
	{
		KalturaLog::debug('Deleting old file_sync versions for ['.$newFileSync->getId().']');
		if (kConf::hasParam('num_of_old_file_sync_versions_to_keep'))
		{
			$keepCount = kConf::get('num_of_old_file_sync_versions_to_keep');
			if(!is_numeric($newFileSync->getVersion()))
				return;
			$intVersion = intval($newFileSync->getVersion());
			$c = new Criteria();
			$c->add ( FileSyncPeer::OBJECT_ID , $newFileSync->getObjectId() );
			$c->add ( FileSyncPeer::OBJECT_TYPE , $newFileSync->getObjectType() );
			$c->add ( FileSyncPeer::OBJECT_SUB_TYPE , $newFileSync->getObjectSubType() );
			$c->add ( FileSyncPeer::STATUS, array(FileSync::FILE_SYNC_STATUS_PURGED, FileSync::FILE_SYNC_STATUS_DELETED), Criteria::NOT_IN);
			$c->addAnd ( FileSyncPeer::VERSION, $intVersion - $keepCount, Criteria::LESS_THAN);
			//Get oldest 5 version's (10 is the jump offset between versions)
			//we limit the number of files to delete in one run so there will be no out of memory issues
			$c->addAnd ( FileSyncPeer::VERSION, $intVersion - $keepCount - (5*10), Criteria::GREATER_EQUAL);
			$c->addAscendingOrderByColumn(FileSyncPeer::VERSION);
			$fileSyncs = FileSyncPeer::doSelect($c);
			foreach ($fileSyncs as $fileSync)
			{
				$key = kFileSyncUtils::getKeyForFileSync($fileSync);
				self::deleteSyncFileForKey($key);
			}
		}
	}
	
	/**
	 * @param FileSyncKey $syncKey
	 * @param string $contentMd5
	 * @param bool $isFile
	 * @return bool
	 */
	public static function compareContent ($syncKey, $contentMd5, $isFile = false)
	{
		list ($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, false, false);
		if (!$fileSync || !$fileSync->getContentMd5())
		{
			return false;
		}
		if ($isFile)
		{
			return ($fileSync->getContentMd5() == md5_file($contentMd5));
		}
		else
		{
			return ($fileSync->getContentMd5() == md5($contentMd5));
		}
	}

	public static function dumpFileByFileSync( FileSync $fileSync)
	{
		$resolveFileSync = self::resolve($fileSync);
		$path = $resolveFileSync->getFullPath();
		KalturaLog::info("Resolve path [$path]");
		kFileUtils::dumpFile($path, null, null, 0, $fileSync->getEncryptionKey(), $fileSync->getIv(), $fileSync->getFileSize());
	}

	public static function dumpFileByFileSyncKey( FileSyncKey $key , $strict = false )
	{
		KalturaLog::debug("Dumping File: key [$key], strict [$strict]");
		list ( $file_sync , $local )= self::getReadyFileSyncForKey( $key , false , $strict );
		if ( $file_sync )
			self::dumpFileByFileSync($file_sync);
	}
	
	public static function encryptByFileSyncKey(FileSyncKey $key)
	{
		$fileSync = self::getLocalFileSyncForKey($key);
		return $fileSync->encrypt();
	}

	public static function getResolveLocalFileSyncForKey(FileSyncKey $key)
	{
		$fileSync = self::getLocalFileSyncForKey($key);
		return self::resolve($fileSync);
	}

	/**
	 * Check if specific file sync that belong to object type and sub type should be synced
	 *
	 * @param FileSync $fileSync
	 * @return bool
	 */
	public static function shouldSyncFileObjectType($fileSync)
	{
		if(is_null(self::$excludedSyncFileFromDcSynchronization))
		{
			self::$excludedSyncFileFromDcSynchronization = array();
			$dcConfig = kConf::getMap("dc_config");
			if(isset($dcConfig['sync_exclude_types']))
			{
				foreach($dcConfig['sync_exclude_types'] as $syncExcludeType)
				{
					$configObjectType = $syncExcludeType;
					$configObjectSubType = null;

					if(strpos($syncExcludeType, ':') > 0)
						list($configObjectType, $configObjectSubType) = explode(':', $syncExcludeType, 2);

					// translate api dynamic enum, such as contentDistribution.EntryDistribution - {plugin name}.{object name}
					if(!is_numeric($configObjectType))
						$configObjectType = kPluginableEnumsManager::apiToCore('FileSyncObjectType', $configObjectType);

					// translate api dynamic enum, including the enum type, such as conversionEngineType.mp4box.Mp4box - {enum class name}.{plugin name}.{object name}
					if(!is_null($configObjectSubType) && !is_numeric($configObjectSubType))
					{
						list($enumType, $configObjectSubType) = explode('.', $configObjectSubType);
						$configObjectSubType = kPluginableEnumsManager::apiToCore($enumType, $configObjectSubType);
					}

					if(!isset(self::$excludedSyncFileFromDcSynchronization[$configObjectType]))
						self::$excludedSyncFileFromDcSynchronization[$configObjectType] = array();

					if(!is_null($configObjectSubType))
						self::$excludedSyncFileFromDcSynchronization[$configObjectType][] = $configObjectSubType;
				}
			}
		}

		if(!isset(self::$excludedSyncFileFromDcSynchronization[$fileSync->getObjectType()]))
			return true;

		if(count(self::$excludedSyncFileFromDcSynchronization[$fileSync->getObjectType()]) &&
			!in_array($fileSync->getObjectSubType(), self::$excludedSyncFileFromDcSynchronization[$fileSync->getObjectType()]))
			return true;

		return false;
	}


}
