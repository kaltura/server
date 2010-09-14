<?php
define('SEARCH_FILES_IN_DB', false); 

class migrateEntries extends AndromedaMigration
{
	private static $sub_types = array (
		entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA ,
		entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA_EDIT ,
		entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB ,
		entry::FILE_SYNC_ENTRY_SUB_TYPE_ARCHIVE ,
		entry::FILE_SYNC_ENTRY_SUB_TYPE_DOWNLOAD
	);
	private static $flavor_tags = array(
		entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA => flavorParams::TAG_MBR,
		entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA_EDIT => flavorParams::TAG_EDIT,
		entry::FILE_SYNC_ENTRY_SUB_TYPE_ARCHIVE => flavorParams::TAG_SOURCE,
	);
	
	private static $fileTypes = array (
		entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA => 'data',
		entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA_EDIT  => 'edit',
		entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB  => 'thumb',
		entry::FILE_SYNC_ENTRY_SUB_TYPE_ARCHIVE => 'original',
		entry::FILE_SYNC_ENTRY_SUB_TYPE_DOWNLOAD => 'download',
	);
	
	private static $entryFiles;
	/**
	 * @param entry $entry
	 * @param Partner $partner
	 * @return bool
	 */	
	public static function migrateSingleEntry(entry $entry, $partner = null)
	{
		if(SEARCH_FILES_IN_DB)
		{
			self::$entryFiles = self::findFileSyncKeysForSubTypeFromDB($entry);
		}
		$updated_at = strtotime($entry->getUpdatedAt())+1;
		self::logPartner("    doing entry ".$entry->getId());
		if($partner)
		{
			$entry->setAccessControlId($partner->getDefaultAccessControlId());
		}
		$strAdminTags = $entry->getAdminTags();
		$adminTags = explode(',', $entry->getAdminTags());
		if(count($adminTags) > 8)
		{
			// avoid too much tags on single entry
			$adminTags = array_slice($adminTags, 0, 8);
			$strAdminTags = implode(',', $adminTags);
		}
		$entry->setCategories($strAdminTags);
		$entry->setUpdatedAt($updated_at);
		try
		{
			$entry->save();
		}
		catch(Exception $ex)
		{
			$entry->setCategories(null);
			$entry->setUpdatedAt($updated_at);
			$entry->save();
		}
		$entryMigrationStatus = false;
		foreach ( self::$sub_types as $sub_type )
		{
			$isImage = ($entry->getType() == entry::ENTRY_TYPE_MEDIACLIP && $entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_IMAGE);
			$isNotClip = ($entry->getType() != entry::ENTRY_TYPE_MEDIACLIP);
			$subIsThumb = ($sub_type == entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB);
			if($isImage || $isNotClip || $subIsThumb)
			{
				$res = self::populateFileSyncForEntryBySubType($sub_type, $entry);
				if($res)
				{
					self::logPartner("         populated fileSync for {$entry->getId()}");
					$entryMigrationStatus = self::decideEntryStatus($sub_type, $entry);
				}
			}
			else
			{
				$res = self::populateFlavorForEntrySubType($sub_type, $entry);
				if($res)
				{
					self::logPartner("         populated flavor for {$entry->getId()} is OK.");
					$entryMigrationStatus = self::decideEntryStatus($sub_type, $entry);
				}				
			}
			if($res && $sub_type == entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA)
			{
				$entryMigrationStatus = true;
				self::logPartner("         entry {$entry->getId()} is OK.");
			}
		}
		$entry->setUpdatedAt($updated_at);
		$entry->save();
		unset($entry);
		self::$entryFiles = null;
		return $entryMigrationStatus;
	}
	
	private static function decideEntryStatus($sub_type, $entry)
	{
		if($sub_type == entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA)
		{
			return true;
		}
		return false;
	}
	
	private static $failedIds;
	
	public static function getFailedIds()
	{
		return self::$failedIds;
	}
	
	/**
	 * migrate a list of entries. return value is integer: 0 - all failed, 1 - all OK, 2 - some failed
	 * @param array $arrEntries
	 * @return int
	 */
	public static function migrateEntryList($arrEntries, $partner = null)
	{
		if(!count($arrEntries) || !is_array($arrEntries))
			return FALSE;
		self::$failedIds = array();
		foreach($arrEntries as $key => $entry)
		{
			if($entry->getStatus() == entry::ENTRY_STATUS_DELETED) continue; // ignore deleted entries
			
			$result = self::migrateSingleEntry($entry, $partner);
			if(!$result) self::$failedIds[] = $entry->getId();
			unset($arrEntries[$key]);
		}
		if(count(self::$failedIds) == 0)
			return 1;
		elseif(count(self::$failedIds) == count($arrEntries))
			return 0;
		else
			return 2;
	}
	
	private static function populateFileSyncForEntryBySubType($sub_type, $entry)
	{
		if(SEARCH_FILES_IN_DB)
		{
			$fileSyncKeys = @self::$entryFiles[self::$fileTypes[$sub_type]]; // not all entries have all subtypes
		}
		else
		{
			$fileSyncKeys = self::findFileSyncKeysForSubType($sub_type, $entry);
		}
		if($sub_type == entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB && !count($fileSyncKeys))
		{
			// no glob
			if(!SEARCH_FILES_IN_DB)
			{
				$fileSyncKeys = self::findFileSyncKeysForSubType($sub_type, $entry, 'thumbnail');
			}
		}
		if(!count($fileSyncKeys))
		{
			self::logPartner("     could not find files for entry {$entry->getId()} sub type {$sub_type}", Zend_Log::NOTICE);
			return false;
		}
		$retVal = false;
		foreach ( $fileSyncKeys as $fileSyncKey )
		{
			
			$fileSyncKey->object_id = $entry->getId();
			$fileSyncKey->object_sub_type = $sub_type;
			$fileSyncKey->object_type = FileSync::FILE_SYNC_OBJECT_TYPE_ENTRY;
			// verify that fileSync for key doesn't exist
			if(kFileSyncUtils::file_exists($fileSyncKey))
			{
				self::logPartner("      {$entry->getId()} {$fileSyncKey->version} - subtype [$sub_type] fileSync already exists");
				$retVal = true;
			}
			else
			{
				try
				{
					$currentDcFileSync = kFileSyncUtils::createSyncFileForKey( $fileSyncKey );
					self::logPartner( "     {$entry->getId()} {$fileSyncKey->version} - subtype [$sub_type], created fileSync" );
					$retVal = true;
				}
				catch ( Exception $ex )
				{
					self::logPartner( "    {$entry->getId()} {$fileSyncKey->version} - subtype [$sub_type], failed to create fileSync" , Zend_Log::ERR);
				}
			}
		}
		return $retVal;
	}
	
	private static function populateFlavorForEntrySubType($sub_type, entry $entry)
	{
		$flavor_tag = @self::$flavor_tags[$sub_type];
		// find asset for file
		$flavorAsset = flavorAssetPeer::retreiveReadyByEntryIdAndTag($entry->getId(), $flavor_tag);
		if($flavorAsset)
		{
			return true;
		}
		$createdFlavorAsset = false;
		
		if(SEARCH_FILES_IN_DB)
		{
			$fileSyncKeys = @self::$entryFiles[self::$fileTypes[$sub_type]]; // not all entries have all subtypes
		}
		else
		{
			$fileSyncKeys = self::findFileSyncKeysForSubType ( $sub_type, $entry);
		}
		if(!count($fileSyncKeys))
		{
			self::logPartner("     could not find files for entry {$entry->getId()} subtype {$sub_type} to creat assets ", Zend_Log::NOTICE);
			return false;
		}
		foreach ( $fileSyncKeys as $fileSyncKey )
		{
			$flavorAsset = self::createFlavorAssetForSubTypeAndVersion ( $sub_type, $entry, $fileSyncKey);
			if($flavorAsset)
			{
				// find log & create fileSync for log
				$createdFlavorAsset = self::decideEntryStatus($sub_type, $entry);
				$isEdit = "";
				$fileType = self::$fileTypes[$sub_type].'_log';
				if($sub_type == entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA_EDIT)
				{
					$isEdit = "_edit";
				}
				$ext = $isEdit . ".log";
				$log_full_path = $fileSyncKey->getFullPath() .$ext ;
					 // for edit - the log will be at xxx_edit.log
				if(SEARCH_FILES_IN_DB)
				{
					if ( is_array(@self::$entryFiles[$fileType]) && key_exists($log_full_path, @self::$entryFiles[$fileType]) )
					{
						try{
							$flavorLogSyncKey = @self::$entryFiles[$fileType][$log_full_path];
							$flavorLogSyncKey->object_type = FileSync::FILE_SYNC_OBJECT_TYPE_FLAVOR_ASSET;
							$flavorLogSyncKey->object_sub_type = flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG;
							$flavorLogSyncKey->object_id = $flavorAsset->getId(); // from the flavorAsset
							 
							$currentDcLogFileSync = kFileSyncUtils::createSyncFileForKey( $flavorLogSyncKey );
							self::logPartner( "    {$entry->getId()} {$fileSyncKey->version} - flavor [{$flavorAsset->getId()}] subtype LOG, created fileSync");
						}
						catch ( Exception $ex )
						{
							self::logPartner("    {$entry->getId()} {$fileSyncKey->version} - flavor [{$flavorAsset->getId()}] subtype LOG, did not find file", Zend_Log::WARN);
						}
					}
					else
					{
						self::logPartner("    {$entry->getId()} {$fileSyncKey->version} - flavor subtype LOG, failed to created fileSync", Zend_Log::WARN);
					}
				}
				else
				{
					if ( file_exists ( $log_full_path ) )
					{
						try{
							$flavorLogSyncKey = new FileSyncKey();
							$flavorLogSyncKey->object_type = FileSync::FILE_SYNC_OBJECT_TYPE_FLAVOR_ASSET;
							$flavorLogSyncKey->object_sub_type = flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG;
							$flavorLogSyncKey->file_path = $fileSyncKey->file_path . $ext ;
							$flavorLogSyncKey->file_root = $fileSyncKey->file_root;
							$flavorLogSyncKey->object_id = $flavorAsset->getId(); // from the flavorAsset
							$flavorLogSyncKey->version = $fileSyncKey->version;
							 
							$currentDcLogFileSync = kFileSyncUtils::createSyncFileForKey( $flavorLogSyncKey );
							self::logPartner( "    {$entry->getId()} {$fileSyncKey->version} - flavor [{$flavorAsset->getId()}] subtype LOG, created fileSync");
						}
						catch ( Exception $ex )
						{
							self::logPartner("    {$entry->getId()} {$fileSyncKey->version} - flavor [{$flavorAsset->getId()}] subtype LOG, did not find file", Zend_Log::WARN);
						}
					}
					else
					{
						self::logPartner("    {$entry->getId()} {$fileSyncKey->version} - flavor subtype LOG, failed to created fileSync", Zend_Log::WARN);
					}					
				}
			}
		}

		return $createdFlavorAsset;
	}
	
	private static function createFlavorAssetForSubTypeAndVersion($sub_type, $entry, FileSyncKey &$fileSyncKey )
	{
		$flavor_tag = @self::$flavor_tags[$sub_type];
		$flavorAsset = new flavorAsset();
		$flavorAsset->setTags($flavor_tag);
		$flavorAsset->setPartnerId($entry->getPartnerId());
		$flavorAsset->setEntryId($entry->getId());
		$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_READY);
		$flavorAsset->setVersion($fileSyncKey->version);

		if($sub_type == entry::FILE_SYNC_ENTRY_SUB_TYPE_ARCHIVE)
		{
			$flavorAsset->setFlavorParamsId(0);
			$flavorAsset->setIsOriginal(true);
		}
		$full_path = $fileSyncKey->getFullPath () ;
		$flavorAsset->setFileExt(pathinfo($full_path, PATHINFO_EXTENSION));
		$flavorAsset->save();
		
		$mediaInfo = new mediaInfo();
		$mediaInfoParser = new KMediaInfoMediaParser($full_path);
		$KalturaMediaInfo = new KalturaMediaInfo();
		$KalturaMediaInfo = $mediaInfoParser->getMediaInfo();
		$mediaInfo = $KalturaMediaInfo->toInsertableObject($mediaInfo);
		$mediaInfo->setFlavorAssetId($flavorAsset->getId());
		$mediaInfo = self::addMediaInfo($mediaInfo);
		$mediaInfo->save();
		
		$fileSyncKey->object_id = $flavorAsset->getId();
		$fileSyncKey->object_type = FileSync::FILE_SYNC_OBJECT_TYPE_FLAVOR_ASSET;
		$fileSyncKey->object_sub_type = flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET;
		
		try{
			$currentDcFileSync = kFileSyncUtils::createSyncFileForKey( $fileSyncKey );
			self::logPartner( "    {$entry->getId()} {$fileSyncKey->version} - flavor subtype ASSET, created fileSync" );
			return $flavorAsset;
		}
		catch(Exception $ex)
		{
			self::logPartner( "    {$entry->getId()} {$fileSyncKey->version} - flavor subtype ASSET, failed to created fileSync" , Zend_Log::ERR);
			return false;
		}
	}
	
	private static function findFileSyncKeysForSubType($sub_type, $entry, $thumb_path = 'bigthumbnail')
	{
		$fileSyncKeyArr = array ();
		
		$path_middle = 	myContentStorage::dirForId ( $entry->getIntId() , $entry->getId() , null );
/*
		(intval($int_id / 1000000)).'/'.	
						(intval($int_id / 1000) % 1000) . "/" . 
						$this->getEntryId();
*/
		

		switch ($sub_type  )
		{
			case entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA:
			case entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA_EDIT:
				$path = "/content/entry/data/" . $path_middle . "_*" ;
				break;
			case entry::FILE_SYNC_ENTRY_SUB_TYPE_DOWNLOAD:
				$path = "/content/entry/download/" . $path_middle . "_*" ;
				break;
			case entry::FILE_SYNC_ENTRY_SUB_TYPE_ARCHIVE:
				$path = "/archive/data/" . $path_middle . "*" ;
				break;
			case entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB:
				$path = "/content/entry/".$thumb_path."/" . $path_middle . "*" ;
				break;				
			default:
				// ERROR !
		}
		self::logPartner(" going to look for file subtype[$sub_type] in $path");
		$root = myContentStorage::getFSContentRootPath();

		$files = glob ( $root . $path  );
		foreach ( $files as $webFile )
		{
			if ( substr_count( $webFile ,  "." ) > 1 ) continue; // we aren't looking for the .xxx.yyy files
			if ( $sub_type != entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA_EDIT && strpos ( $webFile  , "_edit" ) ) continue;				// ignore the _edit substrign
			if ( $sub_type == entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA_EDIT && strpos ( $webFile  , "_edit" ) === false  ) continue;  	// MUST find the _edit substring
			
			$found  = preg_match ( "/_([\d]*)\./" , $webFile , $versions );
			
			$fileSyncKey = new FileSyncKey();
			$fileSyncKey->file_path = str_replace ( $root , "" , $webFile );
			$fileSyncKey->file_root = $root;
			$fileSyncKey->object_sub_type = null; // Will have to be set in the caller func
			$fileSyncKey->object_type = null; // Will have to be set in the caller func
			$fileSyncKey->object_id = null ; // will have to be set in the caller func
			if ( $found )
			{
				$fileSyncKey->version = $versions[1];
			}
			else
			{
				$fileSyncKey->version = $entry->getVersion(); // supposed to happen only for data entries
			}
			$fileSyncKeyArr[] = $fileSyncKey;
		}
	
		return $fileSyncKeyArr;
	}
	
	private static function findFileSyncKeysForSubTypeFromDB($entry)
	{
		$fileSyncKeyArr = array ();
		
		self::logPartner(" going to look for files of entry  {$entry->getId()} in files_list table ");
		$root = myContentStorage::getFSContentRootPath();

		$files = self::getFilesFromDb($entry->getId());
		foreach ( $files as $webFileArr )
		{
			if ( substr_count( $webFileArr['file_path'] ,  "." ) > 1 ) continue; // we aren't looking for the .xxx.yyy files
			$file_type = 'none';
			if ( substr_count($webFileArr['file_path'], '.log' ) && !substr_count($webFileArr['file_path'], '_edit' ) &&
			    !substr_count($webFileArr['file_path'], '/download'))
			{
				$file_type = 'data_log';
			}
			elseif( substr_count($webFileArr['file_path'], '.log' ) && substr_count($webFileArr['file_path'], '_edit' ) &&
			       !substr_count($webFileArr['file_path'], '/download'))
			{
				$file_type = 'edit_log';
			}
			elseif( substr_count($webFileArr['file_path'], '/archive') )
			{
				$file_type = 'original';
			}
			elseif( substr_count($webFileArr['file_path'], '/download') && !substr_count($webFileArr['file_path'], '.log' ) )
			{
				$file_type = 'download';
			}
			elseif( substr_count($webFileArr['file_path'], '/download') && substr_count($webFileArr['file_path'], '.log' ) )
			{
				$file_type = 'download_log';
			}
			elseif( substr_count($webFileArr['file_path'], '/content/entry/data') && !substr_count($webFileArr['file_path'], '_edit') )
			{
				$file_type = 'data';
			}
			elseif( substr_count($webFileArr['file_path'], '/content/entry/thumbnail') || substr_count($webFileArr['file_path'], '/content/entry/bigthumbnail') )
			{
				$file_type = 'thumb';
			}
			elseif( substr_count($webFileArr['file_path'], '/content/entry/data') && substr_count($webFileArr['file_path'], '_edit') )
			{
				$file_type = 'edit';
			}
			if(!key_exists($file_type, $fileSyncKeyArr))
			{
				$fileSyncKeyArr[$file_type] = array();
			}
			
			$found  = preg_match ( "/_([\d]*)\./" , $webFileArr['file_path'] , $versions );
			
			$fileSyncKey = new FileSyncKey();
			$fileSyncKey->file_path = str_replace ( $root , "" , $webFileArr['file_path'] );
			$fileSyncKey->file_root = $root;
			$fileSyncKey->object_sub_type = null; // Will have to be set in the caller func
			$fileSyncKey->object_type = null; // Will have to be set in the caller func
			$fileSyncKey->object_id = null ; // will have to be set in the caller func
			$fileSyncKey->file_size = $webFileArr['file_size'];
			if ( $found )
			{
				$fileSyncKey->version = $versions[1];
			}
			else
			{
				$fileSyncKey->version = $entry->getVersion(); // supposed to happen only for data entries
			}
			$fileSyncKeyArr[$file_type][$webFileArr['file_path']] = $fileSyncKey;
		}
	
		return $fileSyncKeyArr;
	}	

	public static function getFilesFromDb($id)
	{
		$q = 'SELECT * FROM files_list WHERE object_id = "'.$id.'"';
		$conn = Propel::getConnection();
		$statement = $conn->prepareStatement($q);
		$resultSet = $statement->executeQuery();
		if ($resultSet->getRecordCount() == 0)
		
		$entryFiles = array();

		while ($resultSet->next())
		{
			$entryFiles[] = array (
				'file_size' => $resultSet->get('file_size'),
				'file_path' => $resultSet->get('file_path'),
				'object_id' => $resultSet->get('object_id'),
			);
		}
		
		return $entryFiles;
	}
	
	/**
	 * addMediaInfo adds a media info and updates the flavor asset 
	 * 
	 * @param mediaInfo $mediaInfoDb  
	 * @return mediaInfo 
	 */
	public static function addMediaInfo(mediaInfo $mediaInfoDb)
	{
		$mediaInfoDb->save();
		
		if(!$mediaInfoDb->getFlavorAssetId())
			return $mediaInfoDb;
			
		$flavorAsset = flavorAssetPeer::retrieveById($mediaInfoDb->getFlavorAssetId());
		if(!$flavorAsset)
			return $mediaInfoDb;

		KDLWrap::ConvertMediainfoCdl2FlavorAsset($mediaInfoDb, $flavorAsset);
		$flavorAsset->save();

		return $mediaInfoDb;
	}
}
