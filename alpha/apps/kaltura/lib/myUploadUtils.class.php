<?php
class myUploadUtils
{

	const EICAR_MD5 = '44d88612fea8a8f36de82e1278abb02f';
	const EICAR_MIN_FILE_SIZE = 68;
	const EICAR_MAX_FILE_SIZE = 128;
	const BAT_FILE_EXTENSION = 'bat';
	const TEXT_PLAIN_FILE_TYPE = 'text/plain';

	public static function uploadFile ( $file_data , $id , $filename , $hash , $extra_id = null )
	{
		$realHash = myContentStorage::getTempUploadHash($filename, $id);
		
		// TODO - what if there is  an error while uploading ?

		// filename is OK?
		if($realHash == $hash && $hash != "")
		{
			//create the directory if doesn't exists (should have write permissons)
			// if(!is_dir("./files")) mkdir("./files", 0755);
			//move the uploaded file

			$origFilename = $file_data['name'];
			$parts = pathinfo($origFilename);
			$extension = strtolower($parts['extension']);

			$filename = $id.'_'.$filename;
			// add the file extension after the "." character
			$fullPath = myContentStorage::getFSUploadsPath().$filename . ( $extra_id ? "_" . $extra_id : "" ) .".".$extension;

			kFile::fullMkdir($fullPath);
			if ( ! move_uploaded_file($file_data['tmp_name'], $fullPath) )
			{
				KalturaLog::log ( "Error while uploading [$id] [$filename] [$hash] [$extra_id] " . print_r ( $file_data ,true ) ."\n->[$fullPath]" );
				return false;
			}
			@chmod ( $fullPath , 0777 );

			return true;
		}
		
		return false;
	}

	public static function uploadFileByToken ( $file_data , $token , $filename , $extra_id = null , $create_thumb = false )
	{
		KalturaLog::log( "Trace while uploading1 [$filename] [$token] [$extra_id] " . print_r ( $file_data ,true ) );
		
		$origFilename = @$file_data['name'];
		if ( ! $origFilename )
		{
			KalturaLog::log ( "Error while uploading, file does not have a name. [$filename] [$token] [$extra_id] " . print_r ( $file_data ,true ) . 
				"\nerror: [" . @$file_data["error"] . "]" );			
			return;
		}
		$parts = pathinfo($origFilename);
		$extension = @strtolower($parts['extension']);
/*
		$filename = $token .'_'. $filename;
		// add the file extension after the "." character
		$fullPath = myContentStorage::getFSUploadsPath().$filename . ( $extra_id ? "_" . $extra_id : "" ) .".".$extension;
*/
		list ( $fullPath , $fullUrl )  = self::getUploadPathAndUrl ( $token , $filename , $extra_id , $extension );
		
		KalturaLog::log ( "Trace while uploading2 [$filename] [$token] [$extra_id] " . print_r ( $file_data ,true ) ."\n->[$fullPath]" );

		// start tracking what will hopefully become an entry 
		$te = new TrackEntry();
		$te->setTrackEventTypeId( TrackEntry::TRACK_ENTRY_EVENT_TYPE_UPLOADED_FILE );
		$te->setParam1Str( $token );
		$te->setParam2Str( $filename );
		$te->setParam3Str( $fullPath );
		$te->setDescription(  __METHOD__ . ":" . __LINE__ );
		TrackEntry::addTrackEntry( $te );
		
		kFile::fullMkdir($fullPath);
		if ( ! move_uploaded_file($file_data['tmp_name'], $fullPath) )
		{
			$err =  array ( 	"token" => $token , 
						"filename" => $filename , 
						"origFilename" => $origFilename ,
						"error" => @$file_data["error"] , );

			KalturaLog::log ( "Error while uploading [$token] [$filename] [$extra_id] [$create_thumb] " . print_r ( $file_data ,true ) ."\n->[$fullPath]" . "\n" 
				. print_r ( $err , true ) );			
			return $err;
		}
		chmod ( $fullPath , 0777 );

		if (myUploadUtils::isFileTypeRestricted($fullPath))
		{
			KalturaLog::log ( "Error: File type is not allowed for [$token] [$filename] [$extra_id] [$create_thumb] \n->[$fullPath]" . "\n");
			return;
		}
		
		$upload_server_header = isset($_SERVER["HTTP_X_KALTURA_SERVER"]) ? $_SERVER["HTTP_X_KALTURA_SERVER"] : null;
		
		$thumb_created = false;
		
		// if the file originated from a kaltura upload server we dont need a thumbnail (kuploader)
		if ( $create_thumb && !$upload_server_header)
		{
			$thumbFullPath = self::getThumbnailPath ( $fullPath , ".jpg" );
			kFile::fullMkdir( $thumbFullPath );
			KalturaLog::log("Thumbnail full path [$thumbFullPath]");
			
			if(myContentStorage::fileExtAccepted ( $extension ))
			{
				myFileConverter::createImageThumbnail($fullPath, $thumbFullPath, "image2" );
				$thumb_url = self::getThumbnailPath ( $fullUrl , ".jpg" ); 
				$thumb_created = file_exists( $thumbFullPath );
			}
			elseif(myContentStorage::fileExtNeedConversion ( $extension ))
			{
				myFileConverter::captureFrame($fullPath, $thumbFullPath, 1, "image2", -1, -1, 3 );
				if (!file_exists($thumbFullPath))
					myFileConverter::captureFrame($fullPath, $thumbFullPath, 1, "image2", -1, -1, 0 );
			}
		}
		
		if(!$thumb_created)
		{
			KalturaLog::log("Thumbnail not generated");
			// in this case no thumbnail was created - don't extract false data 
			$thumb_url = ""; 
		}
		
		return array ( 	"token" => $token , 
						"filename" => $filename , 
						"origFilename" => $origFilename ,
						"thumb_url" => $thumb_url , 
						"thumb_created" => $thumb_created ,
//						"extra_data" => @$file_data, 
						);
	}
	
	
	private static function getThumbnailPath ( $path , $new_extension = '' )
	{
		$fixed = str_replace ( "uploads/" , "uploads/thumbnail/thumb_" , $path ) ;
		return kFile::getFileNameNoExtension( $fixed , true ). $new_extension;
	}
	
	public static function uploadJpeg ( $data, $id , $filename , $hash , $extra_id = null , $return_extended_data=false)
	{
		//$realHash = myContentStorage::getTempUploadHash($filename, $id);

		$origFilename = $filename; 
		// filename is OK?
		if( true /*$realHash == $hash && $hash != ""*/)
		{
			$filename = $id.'_'.$filename;
			// add the file extension after the "." character
			$fullPath = myContentStorage::getFSUploadsPath().$filename . ( $extra_id ? "_" . $extra_id : "" ) .".jpg";

			kFile::setFileContent($fullPath, $data);
			chmod ( $fullPath , 0777 );

			if ( $return_extended_data )
			{
				return array ( 	"token" => $id , 
					"filename" => $filename , 
					"origFilename" => $origFilename ,
					"thumb_url" => null , 
					"thumb_created" => false);
			}
			return true;
		}
		
		return false;
	}
	
	// return the file path WITHOUT the extension
	// if the extension is known - pass it as the 4rt parameter
	public static function getUploadPath ($token , $file_alias , $extra_id = null , $extension = "" )
	{
//		$extension = ""; // strtolower($parts['extension']);
		if (strpos($token, "token-") === 0) // starts with "token_", means that the file was uploaded with upload.getUploadTokenId
		{
			$files = glob(myContentStorage::getFSUploadsPath().$token."*");
			if (count($files) > 0)
			{
				$token = strtolower(pathinfo($files[0], PATHINFO_BASENAME));
				$extension = strtolower(pathinfo($token, PATHINFO_EXTENSION));
				$token = str_replace("_.".$extension, "", $token);
			}
		}
		$filename = $token .'_'. $file_alias;
		// add the file extension after the "." character
		$fullPath = myContentStorage::getFSUploadsPath().$filename . ( $extra_id ? "_" . $extra_id : "" ) .".".$extension;

		return $fullPath;
	}
	
	public static function getUploadPathAndUrl ($token , $file_alias , $extra_id = null , $extension = "" )
	{
//		$extension = ""; // strtolower($parts['extension']);
		
		$filename = $token .'_'. $file_alias;
		// add the file extension after the "." character
		$suffix = $filename . ( $extra_id ? "_" . $extra_id : "" ) .".".$extension;
		$fullPath = myContentStorage::getFSUploadsPath().$suffix;
		$fullUrl = requestUtils::getRequestHost()."/". myContentStorage::getFSUploadsPath( false ).$suffix;
		return array ( $fullPath , $fullUrl );
	}


	public static function checkIfFileIsAllowed($uploadFilePath)
	{
		$fileType = kFileUtils::getMimeType($uploadFilePath);

		if ($fileType == self::TEXT_PLAIN_FILE_TYPE)
		{
			if ( strtolower(pathinfo($uploadFilePath, PATHINFO_EXTENSION)) == self::BAT_FILE_EXTENSION)
			{
				return false;
			}
			else
			{
				$fileSize = kFile::fileSize($uploadFilePath);
				if ( $fileSize >= self::EICAR_MIN_FILE_SIZE && $fileSize <= self::EICAR_MAX_FILE_SIZE)
				{
					$content = file_get_contents($uploadFilePath);
					if (md5(trim($content)) === self::EICAR_MD5)
					{
						return false;
					}
				}
			}
		}
		$fileTypes = kConf::get('file_type');
		return in_array($fileType, $fileTypes['allowed']);
	}

	public static function isFileTypeRestricted($fullPath, $partnerId = null)
	{
		if(!$partnerId)
		{
			$partnerId = kCurrentContext::getCurrentPartnerId();
		}

		if (PermissionPeer::isValidForPartner(PermissionName::FEATURE_FILE_TYPE_RESTRICTION_PERMISSION, $partnerId)
			&& !myUploadUtils::checkIfFileIsAllowed($fullPath))
		{
			return true;
		}
		return false;
	}
	
}
?>