<?php

class myContentStorage
{
	const MIN_OBFUSCATOR_VALUE = 100000;
	const MAX_OBFUSCATOR_VALUE = 100000; // fixme - while we test generate constant obfuscators
	//const MAX_OBFUSCATOR_VALUE = 900000;

	
	// TODO - IMPROVE ? - may want to include the getFSContentRootPath as the beginning of the path because it's appended to 
	// in the beginning everytime anyway in the caller's code - Eran ?
	
	
	public static function isTemplate ( $file_name )
	{
		return ( strstr ( $file_name, "&") !== FALSE );
	}
	
	
	/**
	 * This function returns the file system path for a requested content entity.
	 * The given file name is of the form last_ugc_version.ext&kaltua_template
	 * last_ugc_version - the last version of ugc the user uploaded
	 * kaltura_template - a kaltura made content available for the user
	 * each one of these two may be omitted.
	 * A user never uploaded his own content : "&kaltura_template"
	 * A user uploaded his own content : "ugc_version"
	 * A user replaced his old content with a template : "last_ugc_version&kaltura_template"
	 * This way we keep the last version the user used. This allows us to force caching of the UGC
	 * on the browser side without even checking for modification on the server side. A new ugc will
	 * simply get another name through the version process.
	 * When we want to set a kaltura template will set the $fileName parameter to '&'.kaltura_template_name.
	 * The path is composed from the entity name (kshow, entry, kuser),
	 * the entity id and it's random obfuscator (which is used also for versioning)
	 * @param string $entityName = the entity object name
	 * @param int $id = the entity id
	 * @param int $fileName = random obfuscator followed by the file extension (.jpg, .flv, .txt, etc...)
	 * @return string the content path
	 */
	public static function getGeneralEntityPath($entityName, $int_id, $id, $fileName , $version = null )
	{
		if( $version != null )
		{
			$ext = pathinfo ($fileName , PATHINFO_EXTENSION);
			$fileName = $version;
		}
				
		$c = strstr($fileName, '^') ?  '^' : '&';
		
		$parts = explode($c, $fileName);
		
		if (count($parts) == 2 && strlen($parts[1]))
		{
			$res =  ($c == '^' ? '/content/templates/' : '/content/templates/').$entityName.'/'.$parts[1];
		}
		else
		{
			$res = '/content/'.$entityName.'/'. self::dirForId ( $int_id, $id ) .'_'.$fileName;
		}
		
		if( $version != null )
		{
			$res .= "." . $ext;
		}
		
		return $res;
		
	}
/*
	public static function dirForId ( $id )
	{
		return (intval($id / 1048576)).'/'.	(intval($id / 1024) % 1024).'/'.$id;
	}
*/
	public static function dirForId ( $int_id, $id , $file_name = NULL )
	{
		return (intval($int_id / 1000000)).'/'.	(intval($int_id / 1000) % 1000).'/'. ( $file_name !== NULL ? $file_name : $id ) ;
	}

	public static function getVersion ($fileName)
	{
		$version = strrchr( $fileName, "_" );
		
		if ($version === FALSE)
			return 0;
		
		return 0 + substr( $version, 1 );
	}

	public static function getAllVersions_deprecated ( $entityName, $int_id, $id, $fileName)
	{
		$c = strstr($fileName, '^') ?  '^' : '&';
		$parts = explode($c, $fileName);
		
		if (count($parts) == 2 && strlen($parts[1]))
		{ 
			// a template has no versions
			$dir = '/content/templates/'.$entityName.'/';
			$file_base = ""; //$parts[1];
			return $dir . $file_base;
		}
		else
		{
//			$dir = '/content/'.$entityName.'/'.
//					(intval($id / 1048576)).'/'.
//					(intval($id / 1024) % 1024).'/';
//
			$dir = '/content/'.$entityName.'/'. self::dirForId ( $int_id, $id , "" ); 
	
		$file_base = $id.'_'; //.$fileName;
		}

		$id_len = strlen ( $id . "_" );
		// iterate the directory and find all the files that start with $file_base
		// the result will be tuples where the first element is the file's name, second is the file size
		// TODO - use glob rather than  dirListExtended
		//$pattern = "|" . self::getFSContentRootPath(). "/" . $dir . "/^{$file_base}.*\.xml$";
		
		$files = kFile::dirListExtended( self::getFSContentRootPath() . "/" . $dir , false , false , '/^' . $file_base . ".*\.xml$/" ) ;
		
		if ( $files == null ) return null;
				
		// from each file - strip the id and the file extension
		// use the refernce to file_tuple - it will be modified 
		foreach ( $files as &$file_tuple )
		{
			//  the file_name includes the id and the _, then the verson and finally the file extension
			$file_version = substr( kFile::getFileNameNoExtension ( $file_tuple[0] ) , $id_len );
			$file_tuple[] = $file_version; // set the version in the forth place of the tuple.
		}

		if ( $files == null ) return null;
		sort($files);
		return $files;
	}
	
	/**
	 * This function generates a random file name consisting of a random number and
	 * a given file extension. If the new filename begins with a '&' or '^' character, the new
	 * file is a kaltura template and it's appended to the previous filename UGC part.
	 * This way the old UGC version is mantained. look above at getGeneralEntityPath documentation.
	 * The random number is in the interval [100000,900000].
	 * The 900000 upper limit provides space for storing 100000 versions
	 * without expanding the file name length.
	 * @param string $fileName = the original fileName from which the extension is cut.
	 * @param string $previousFileName = in case a previous file exists, the old random is incremented
	 * @return string the randomized file name
	 */
	public static function generateRandomFileName($fileName, $previousFileName = NULL )
	{
		if( $fileName == null )
			return null;
		
		
		if ($previousFileName)
		{
			$c = strstr($previousFileName, '^') ?  '^' : '&';
			$parts = explode( $c, $previousFileName);
		}
		else
			$parts = array('');
		
		if (strlen($fileName) && ( $fileName[0] == '&' || $fileName[0] == '^' ) ) // setting to a kaltura template
		{
			return $parts[0].$fileName;
		}
		
		if (strlen($parts[0])) // a previous UGC found, increment version
			$version = pathinfo($parts[0], PATHINFO_BASENAME) + 1;
		else
			$version = rand(myContentStorage::MIN_OBFUSCATOR_VALUE, myContentStorage::MAX_OBFUSCATOR_VALUE);
			
		return $version.'.'.strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
	}

	/**
	 * This function returns the hash used secure the file uploading.
	 * The hash is the SHA1 of the kuser salt, kuser id and given entity
	 * If the user doesnt exist in the database the returned hash is empty - an error.
	 * @param string $entityName = the entity object name
	 * @param int $kuser_id = the uploading kuser id
	 * @return string the hash
	 */
	public static function getTempUploadHash($entityName, $kuser_id, $user = NULL)
	{
		if ( !$user ) $user = kuserPeer::retrieveByPK($kuser_id);

		// found user in db, generate hash
		if($user)
		{
			$salt = ''; // kusers didn't have salt'

			return $hash = sha1($salt.$kuser_id.$entityName);
		}

		return "";
	}

	/**
	 * This function returns the upload url paramters needed to securely upload a file.
	 * The resulting parameters are filename and hash.
	 * The filename is the given entity name (thumbnail, audio, etc...)
	 * The hash is generated using the myContentStorage::getTempUploadHash function.
	 * @param string $entityName = the entity object name
	 * @param int $kuser_id = the uploading kuser id
	 * @return string the url parameters string
	 */
	public static function getTempUploadUrlParams($entityName, $kuser_id, $user = NULL)
	{
		// TODO - i added  this becuase it took me 3 hours to find this bug.
		// it's clear that the module from which this is called should enforce logging in, but i added this extra defence - Eran ??
		if ( $kuser_id == NULL || strlen ( $kuser_id ) == 0 )
		{
			throw new Exception ( "Should not be called when user is not logged in !" );
		}
		
		$hash = myContentStorage::getTempUploadHash($entityName, $kuser_id, $user );

		if ($hash != "")
		return "?id=$kuser_id&filename=$entityName&hash=$hash";

		return "";
	}

	// TODO - maybe move generic file functions to infra/kFile !?!?
	public static function fullMkdir($path, $rights = 0777)
	{
		$folder_path = array(strstr($path, '.') ? dirname($path) : $path);
		$folder_path = str_replace( "\\" , "/" , $folder_path);
		while(!@is_dir(dirname(end($folder_path)))
		&& dirname(end($folder_path)) != '/'
		&& dirname(end($folder_path)) != '.'
		&& dirname(end($folder_path)) != '')
		array_push($folder_path, dirname(end($folder_path)));

		while($parent_folder_path = array_pop($folder_path))
		{
			if ( ! file_exists( $parent_folder_path ))
			{
				if(!@mkdir($parent_folder_path, $rights))
				{
					//user_error("Can't create folder \"$parent_folder_path\".");
				}
				else
				{
					@chmod($parent_folder_path, $rights);
				}
			}
			else
			{
				@chmod($parent_folder_path, $rights);
			}
			
		}
	}


	// TODO - verify changes !!
	public static function moveFile($from, $to, $override_if_exists = false, $copy = false )
	{
		$from = str_replace( "\\" , "/" , $from );
		$to = str_replace( "\\" , "/" , $to );

		if ( $override_if_exists && is_file( $to ) )
		{
			self::deleteFile ( $to );
		}
		
		if ( !is_dir ( dirname ( $to )) )
		{
			myContentStorage::fullMkdir($to);
		}

		KalturaLog::log("myContentStorage::moveFile ($copy): $from to $to");
		
		if ( file_exists( $from ))
		{
			KalturaLog::log(__METHOD__." - $from file exists");	
		}
		else
		{
			KalturaLog::log(__METHOD__." - $from file doesnt exist");	
		}
		
		if ($copy)
			return copy($from, $to);
		else
			return rename($from, $to);
	}

	// make sure the file is closed , then remove it
	public static function deleteFile ( $file_name )
	{
		$fh = fopen($file_name, 'w') or die("can't open file");
		fclose($fh);
		unlink($file_name);
	}
	
	/**
	 * This function returns the FILE SYSTEM path to the root archive folder.
	 * @return string the content folder file system path
	 */
	public static function getFSArchiveRootPath ()
	{
		return realpath(sfConfig::get('sf_root_dir')."/../../").'/archive/';
	}
	
	/**
	 * This function returns the FILE SYSTEM path to the root content folder.
	 * @return string the content folder file system path
	 */
	public static function getFSContentRootPath ()
	{
		$dc = kDataCenterMgr::getCurrentDc();
		return $dc["root"];
		
		//return realpath(sfConfig::get('sf_root_dir')."/../../").'/';
	}

	public static function getFSFlashRootPath ()
	{
		return  "/flash";
	}

	public static function getFSUiconfRootPath ()
	{
		return  "/uiconf";
	}
	
	public static function getFSCacheRootPath ()
	{
		return kConf::get("general_cache_dir");
	}
	
	public static function getFSDeletedContentRootPath ( $original_path )
	{
		// don't delete what is already deleted
		if ( strpos (  $original_path , "deleted_content/") !== false ) return null;
		$deleted_path = str_replace ( "content/" , "deleted_content/" , $original_path );
		return $deleted_path;
	}
	
	public static function moveToDeleted ( $original_path , $copy = false )
	{
		if ( empty ( $original_path ) ) return ""; 
		if ( strpos ( $original_path , "templates/" ) !== false ) return ""; // dont' delete or move template files
		$deleted_path = self::getFSDeletedContentRootPath ( $original_path );
		if ( $deleted_path  == null ) return ""; 
		if ( ! file_exists( $original_path )) return "";
		self::fullMkdir( $deleted_path );
		self::moveFile( $original_path , $deleted_path , true , $copy );
		return $deleted_path;
	}

	public static function moveFromDeleted ( $deleted_path , $copy = false )
	{
		if ( empty ( $deleted_path ) ) return ""; 
		if ( strpos ( $deleted_path , "templates/" ) !== false ) return ""; // dont' undelete or move template files
		$original_path = str_replace (  "deleted_content/" , "content/" , $deleted_path );
		if ( $original_path  == null ) return ""; 
		if ( ! file_exists( $deleted_path )) return "";
		self::moveFile( $deleted_path , $original_path , true , $copy );
		return $original_path;
	}	
	/**
	 * This function returns the FILE SYSTEM path to the uploads folder.
	 * @return string the uploads folder file system path
	 */
	public static function getFSUploadsPath( $add_root = true )
	{
		if ( $add_root )
			return myContentStorage::getFSContentRootPath()."content/uploads/";
		else
			return "content/uploads/";;
	}	

	public static function getFileNameEdit ( $file_name )
	{
		return str_replace( ".flv" , "_edit.flv" , $file_name );
	}
	
	
	public static function removeTempThumbnails($kuser_id)
	{
		$thumbPattern = myContentStorage::getFSUploadsPath().$kuser_id.'_thumbnail_*.*';
		
		foreach (glob($thumbPattern) as $filename)
	      unlink($filename);
	}
	
	public static function removeTempKUserContent($kuser_id)
	{
		$filePattern = myContentStorage::getFSUploadsPath().$kuser_id.'_*.*';
		
		foreach (glob($filePattern) as $filename)
	      unlink($filename);
	}
	
	public static function fileExtAccepted($ext)
	{
		// TODO - support all document types or enable kConf
		$fileExts = array("jpg", "jpeg", "bmp", "png", "gif", "tif", "tiff" );
		return in_array($ext, $fileExts);
	}
	
	// TODO - after solving the conversion issue - FLV 
	public static function fileExtNeedConversion($ext)
	{
		$fileExts = array( "flv" , "asf", "wmv", "qt" , "mov" , "mpg", "mpeg" , "avi" , "mp3", "wav" , "wma" ,
				   "mp4", "m4v", "3gp" , "vob", "f4v", "amr", "mkv" , "3g2" , "rm" , "rv" , "ra" , "rmvb" );
		return in_array($ext, $fileExts);
	}

}

?>