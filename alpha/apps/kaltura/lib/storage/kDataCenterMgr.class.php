<?php
class kDataCenterMgr
{
	private static $s_current_dc;

	/**
	 * @var StorageProfile
	 */
	private static $currentStorageProfile = null;
	
	/**
	 * @return StorageProfile
	 */
	public static function getCurrentStorageProfile()
	{
		if(self::$currentStorageProfile)
			return self::$currentStorageProfile;
			
		self::$currentStorageProfile = StorageProfilePeer::retrieveByPK(self::getCurrentDcId());
		return self::$currentStorageProfile;
	}
	
	// TODO - remove ! this is ony a way to test multiple datacenters on the same machine
	public static function setCurrentDc( $current_dc)
	{
		self::$s_current_dc = $current_dc;
	}
	
	public static function getCurrentDcId () 
	{
		$dc = self::getCurrentDc();
		return $dc["id"];
	}
	
	public static function getCurrentDc () 
	{
		$dc_config = kConf::get ( "dc_config" );
		// find the current
		if ( self::$s_current_dc )
			return self::getDcById( self::$s_current_dc );
		return self::getDcById( $dc_config["current"] );
	}

	// returns a tupple with the id and the DC's properties
	public static function getDcById ( $dc_id ) 
	{
		$dc_config = kConf::get ( "dc_config" );
		// find the dc with the desired id
		$dc_list = $dc_config["list"];
		if ( isset( $dc_list[$dc_id] ) )		
			$dc = $dc_list[$dc_id];
		else
			throw new Exception ( "Cannot find DC with id [$dc_id]" );
		
		$dc["id"]=$dc_id;
		return $dc;
//		return array ( $dc_id , $dc );
	}
		
	public static function getAllDcs( $include_current = false )
	{
		$dc_config = kConf::get ( "dc_config" );
		
		$dc_list = $dc_config["list"];
		
		if ( $include_current == false )
		{
			unset ( $dc_list[$dc_config["current"]]);
		}
		
		$fixed_list = array();
		foreach ( $dc_list as $dc_id => $dc_props  )
		{
			$dc_props["id"]=$dc_id;
			$fixed_list[] = $dc_props;
		}
		return $fixed_list;
	}
	
	public static function getRemoteDcExternalUrl ( FileSync $file_sync )
	{
		KalturaLog::log(__METHOD__." - file_sync [{$file_sync->getId()}]]");
		$dc_id = $file_sync->getDc();
		$dc = self::getDcById ( $dc_id );
		$external_url = $dc["external_url"];
		return $external_url;
	}

	public static function getRemoteDcExternalUrlByDcId ( $dc_id )
	{
		KalturaLog::log(__METHOD__." - dc_id [{$dc_id}]]");
		$dc = self::getDcById ( $dc_id );
		$external_url = $dc["external_url"];
		return $external_url;
	}
	
	public static function getRedirectExternalUrl ( FileSync $file_sync , $additional_url = null )
	{
		$remote_external_url = self::getRemoteDcExternalUrl ( $file_sync );
		$remote_url =  $remote_external_url . $_SERVER['REQUEST_URI'];
		KalturaLog::log ( __METHOD__ . ": URL to redirect to [$remote_url]" );
		
		return $remote_url;
	}
	
	public static function createCmdForRemoteDataCenter(FileSync $fileSync)
	{
		KalturaLog::log(__METHOD__." - fileSync [{$fileSync->getId()}]");
		$remoteUrl = self::getInternalRemoteUrl($fileSync); 
		$locaFilePath = self::getLocalTempPathForFileSync($fileSync);
		$cmdLine = kConf::get( "bin_path_curl" ) . ' -L -o"'.$locaFilePath.'" "'.$remoteUrl.'"';
		return $cmdLine;
	}
	
	public static function getLocalTempPathForFileSync(FileSync $fileSync) 
	{
		return DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "file_sync-" .  $fileSync->getId();
	}
	
	public static function getInternalRemoteUrl(FileSync $file_sync)
	{
		KalturaLog::log(__METHOD__." - file_sync [{$file_sync->getId()}]");
		// LOG retrieval

		$dc =  self::getDcById ( $file_sync->getDc() );
		
		$file_sync_id = $file_sync->getId();
		$file_hash = md5( $dc["secret" ] .  $file_sync_id );	// will be verified on the other side to make sure not some attack or external invalid request  
		
		$build_remote_url = $dc["url"] . "/index.php/extwidget/servefile/id/{$file_sync_id}/hash/{$file_hash}"; // or something similar 
		
		return $build_remote_url;
	}
		
	/**
	 * Will fetch the content of the $file_sync.
	 * If the $local_file_path is specifid, will place the cotnet there
	 * @param FileSync $file_sync
	 * @return string
	 */
	public static function retrieveFileFromRemoteDataCenter ( FileSync $file_sync )
	{
		KalturaLog::log(__METHOD__." - file_sync [{$file_sync->getId()}]");
		// LOG retrieval

		$cmd_line = self::createCmdForRemoteDataCenter($file_sync);
		$local_file_path = self::getLocalTempPathForFileSync($file_sync);
		
		if (!file_exists($local_file_path)) // don't need to fetch twice 
		{ 
			KalturaLog::log(__METHOD__." - executing " . $cmd_line);
			exec($cmd_line);
		}
		else {
			KalturaLog::log(__METHOD__." - already exists in temp folder [{$local_file_path}]");
		}

		return file_get_contents( $local_file_path );
	}

	/*
	 * will handle the serving of the file assuming a remote DC (other than the current) requested it
	 */
	public static function serveFileToRemoteDataCenter ( $file_sync_id , $file_hash, $file_name )
	{
		KalturaLog::log(__METHOD__." - file_sync_id [$file_sync_id], file_hash [$file_hash], file_name [$file_name]");
		// TODO - verify security
		
		$current_dc = self::getCurrentDc();
		$current_dc_id = $current_dc["id"];
		// retrieve the object
		$file_sync = FileSyncPeer::retrieveByPk ( $file_sync_id );
		if ( ! $file_sync )
		{
			$error = "DC[$current_dc_id]: Cannot find FileSync with id [$file_sync_id]";
			KalturaLog::log(__METHOD__." - $error");
			throw new Exception ($error);
		}
		
		if ( $file_sync->getDc() != $current_dc_id )
		{
			$error = "DC[$current_dc_id]: FileSync with id [$file_sync_id] does not belong to this DC";
			KalturaLog::log(__METHOD__." - $error"); 
			throw new Exception ( $error );
		}
		
		// resolve if file_sync is link
		$file_sync_resolved = $file_sync;
		if($file_sync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_LINK)
		{
			$file_sync_resolved = kFileSyncUtils::resolve($file_sync);
		}
		
		// check if file sync path leads to a file or a directory
		$resolvedPath = $file_sync_resolved->getFullPath();
		$fileSyncIsDir = is_dir($resolvedPath);
		if ($fileSyncIsDir && $file_name) {
			$resolvedPath .= '/'.$file_name;
		}
		
		if (!file_exists($resolvedPath))
		{
			$file_name_msg = $file_name ? "file name [$file_name] " : '';
			$error = "DC[$current_dc_id]: Path for fileSync id [$file_sync_id] ".$file_name_msg."does not exist";
			KalturaLog::log(__METHOD__." - $error"); 
			throw new Exception ( $error );	
		}
		
		// validate the hash
		$expected_file_hash = md5( $current_dc["secret" ] .  $file_sync_id );	// will be verified on the other side to make sure not some attack or external invalid request
		if ( $file_hash != $expected_file_hash )  
		{
			$error = "DC[$current_dc_id]: FileSync with id [$file_sync_id] - invalid hash";
			KalturaLog::log(__METHOD__." - $error"); 
			throw new Exception ( $error );			
		}
				
		if ($fileSyncIsDir && is_dir($resolvedPath))
		{
			KalturaLog::log(__METHOD__." - serving directory content from [".$resolvedPath."]");
			$contents = kFile::listDir($resolvedPath);
			sort($contents, SORT_STRING);
			$contents = serialize($contents);
			header("file-sync-type: dir");
			echo $contents;
			die();
		}
		else
		{
			KalturaLog::log(__METHOD__." - serving file from [".$resolvedPath."]");
			kFile::dumpFile( $resolvedPath );
		}
		
	}
}
?>