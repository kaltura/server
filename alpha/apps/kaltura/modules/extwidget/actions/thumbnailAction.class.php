<?php

class thumbnailAction extends sfAction
{
	private static function notifyProxy($msg)
	{
        $server = kConf::get ( "image_proxy_url" ); 
        
        if ($server && (requestUtils::getRemoteAddress() != $server ))
        {
			$sock = socket_create(AF_INET,SOCK_DGRAM,SOL_UDP);
	        if ($sock)
	        {
	                $secret = kConf::get ( "image_proxy_secret" );
	                $port = kConf::get ( "image_proxy_port" );
	                $data = md5($secret.$msg).$msg;
	                socket_sendto($sock, $data, strlen($data),0 , $server, $port);
	                socket_close($sock);
	        }
        }
	}	
	
	/**
	 * Will forward to the regular swf player according to the widget_id 
	 */
	public function execute()
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL2;
		
		requestUtils::handleConditionalGet();
		
		ignore_user_abort();
		
		$entry_id = $this->getRequestParameter( "entry_id" );
		$widget_id = $this->getRequestParameter( "widget_id", 0 );
		$upload_token_id = $this->getRequestParameter( "upload_token_id" );
		$version = $this->getRequestParameter( "version", null );
		$width = $this->getRequestParameter( "width", -1 );
		$height = $this->getRequestParameter( "height", -1 );
		$type = $this->getRequestParameter( "type" , 1);
		$crop_provider = $this->getRequestParameter( "crop_provider", null);
		$quality = $this->getRequestParameter( "quality" , 0);
		$src_x = $this->getRequestParameter( "src_x" , 0);
		$src_y = $this->getRequestParameter( "src_y" , 0);
		$src_w = $this->getRequestParameter( "src_w" , 0);
		$src_h = $this->getRequestParameter( "src_h" , 0);
		$vid_sec = $this->getRequestParameter( "vid_sec" , -1);
		$vid_slice = $this->getRequestParameter( "vid_slice" , -1);
		$vid_slices = $this->getRequestParameter( "vid_slices" , -1);
		
		// actual width and height of image from which the src_* values were taken.
		// these will be used to multiply the src_* parameters to make them relate to the original image size.
		$rel_width  = $this->getRequestParameter( "rel_width", -1 );
		$rel_height = $this->getRequestParameter( "rel_height", -1 );
				
		if ($width == -1 && $height == -1) // for sake of backward compatibility if no dimensions where specified create 120x90 thumbnail
		{
			$width = 120;
			$height = 90;
		}
		else if ($width == -1) // if only either width or height is missing reset them to zero, and convertImage will handle them
			$width = 0;
		else if ($height == -1)
			$height = 0;
			
				
		$bgcolor = $this->getRequestParameter( "bgcolor", "ffffff" );
		
		if ($upload_token_id)
		{
			$upload_token = UploadTokenPeer::retrieveByPK($upload_token_id);
			if ($upload_token)
			{
				$thumb_full_path =  myContentStorage::getFSCacheRootPath() . myContentStorage::getGeneralEntityPath("uploadtokenthumb", $upload_token->getIntId(), $upload_token->getId(), $upload_token->getId() . ".jpg");
				kFile::fullMkdir($thumb_full_path);
				if (file_exists($upload_token->getUploadTempPath()))
				{
					// capture full frame
					myFileConverter::captureFrame($upload_token->getUploadTempPath(), $thumb_full_path, 1, "image2", -1, -1, 3 );
					if (!file_exists($thumb_full_path))
						myFileConverter::captureFrame($upload_token->getUploadTempPath(), $thumb_full_path, 1, "image2", -1, -1, 0);
						
					// and resize it
					myFileConverter::convertImage($thumb_full_path, $thumb_full_path, $width, $height, $type, $bgcolor, true, $quality, $src_x, $src_y, $src_w, $src_h);
					kFile::dumpFile($thumb_full_path);
				}
			}
		}
		
		$entry = entryPeer::retrieveByPKNoFilter( $entry_id );
		
		// multiply the passed $src_* values so that they will relate to the original image size, according to $src_display_*
		if ($rel_width != -1) {
			$widthRatio  = $entry->getWidth() / $rel_width;
			$src_x = $src_x * $widthRatio;
			$src_w = $src_w * $widthRatio;
		}
		
		if ($rel_height != -1) {
			$heightRatio  = $entry->getHeight() / $rel_height;
			$src_y  = $src_y * $heightRatio;
			$src_h  = $src_h * $heightRatio;
		}						
		
		if ( ! $entry )
		{	
			// get the widget
			$widget = widgetPeer::retrieveByPK( $widget_id );
			if ( !$widget )
			{
				KExternalErrors::dieError(KExternalErrors::ENTRY_AND_WIDGET_NOT_FOUND);
			}
			
			// get the kshow
			$kshow_id= $widget->getKshowId();
			$kshow = kshowPeer::retrieveByPK($kshow_id);
			if ( $kshow )
			{
				$entry_id = $kshow->getShowEntryId();
			}
			else
			{
				$entry_id = $widget->getEntryId();
			}
			
			$entry = entryPeer::retrieveByPKNoFilter( $entry_id );
			if ( ! $entry )
			{
				KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_FOUND);
			}
		}
		
		$subType = entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB;
		if($entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_IMAGE)
			$subType = entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA;
			
		$dataKey = $entry->getSyncKey($subType);
		list ( $file_sync , $local ) = kFileSyncUtils::getReadyFileSyncForKey( $dataKey ,true , false );
		
		$tempThumbPath = null;
		$entry_status = $entry->getStatus();
		
		// both 640x480 and 0x0 requests are probably coming from the kdp
		// 640x480 - old kdp version requesting thumbnail
		// 0x0 - new kdp version requesting the thumbnail of an unready entry
		// we need to distinguish between calls from the kdp and calls from a browser: <img src=...> 
		// that can't handle swf input
		if (($width == 640 && $height == 480 || $width == 0 && $height == 0) &&
			($entry_status == entry::ENTRY_STATUS_PRECONVERT || $entry_status == entry::ENTRY_STATUS_IMPORT ||
			$entry_status == entry::ENTRY_STATUS_ERROR_CONVERTING || $entry_status == entry::ENTRY_STATUS_DELETED))
		{
			$contentPath = myContentStorage::getFSContentRootPath();
			$msgPath = $contentPath."content/templates/entry/bigthumbnail/";
			if ($entry_status == entry::ENTRY_STATUS_DELETED)
			{
				$msgPath .= $entry->getModerationStatus() == moderation::MODERATION_STATUS_BLOCK ?
							"entry_blocked.swf" : "entry_deleted.swf";
			}
			else
			{
				$msgPath .= $entry_status == entry::ENTRY_STATUS_ERROR_CONVERTING ?
							"entry_error.swf" : "entry_converting.swf";
			}
						
			kFile::dumpFile($msgPath, null, 0);
		}
			
		if ( ! $file_sync ) 
		{
			// if entry type is audio - serve generic thumb:
			if($entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_AUDIO)
			{
				if($entry->getStatus() == entry::ENTRY_STATUS_DELETED && $entry->getModerationStatus() == moderation::MODERATION_STATUS_BLOCK)
				{
					KalturaLog::log("rejected audio entry - not serving thumbnail");
					KExternalErrors::dieError(KExternalErrors::ENTRY_DELETED_MODERATED);
				}
				$contentPath = myContentStorage::getFSContentRootPath();
				$msgPath = $contentPath."content/templates/entry/thumbnail/audio_thumb.jpg";
				$tempThumbPath = myEntryUtils::resizeEntryImage( $entry, $version , $width , $height , $type , $bgcolor , $crop_provider, $quality, $src_x, $src_y, $src_w, $src_h, $vid_sec, $vid_slice, $vid_slices, $msgPath);
				//kFile::dumpFile($tempThumbPath, null, 0);
			}
			elseif($entry->getType() == entry::ENTRY_TYPE_LIVE_STREAM)
			{
				if($entry->getStatus() == entry::ENTRY_STATUS_DELETED && $entry->getModerationStatus() == moderation::MODERATION_STATUS_BLOCK)
				{
					KalturaLog::log("rejected live stream entry - not serving thumbnail");
					KExternalErrors::dieError(KExternalErrors::ENTRY_DELETED_MODERATED);
				}
				$contentPath = myContentStorage::getFSContentRootPath();
				$msgPath = $contentPath."content/templates/entry/thumbnail/live_thumb.jpg";
				$tempThumbPath = myEntryUtils::resizeEntryImage( $entry, $version , $width , $height , $type , $bgcolor , $crop_provider, $quality, $src_x, $src_y, $src_w, $src_h, $vid_sec, $vid_slice, $vid_slices, $msgPath);
			}
			elseif($entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_SHOW) // roughcut without any thumbnail, probably just created
			{
				$contentPath = myContentStorage::getFSContentRootPath();
				$msgPath = $contentPath."content/templates/entry/thumbnail/auto_edit.jpg";
				$tempThumbPath = myEntryUtils::resizeEntryImage( $entry, $version , $width , $height , $type , $bgcolor , $crop_provider, $quality, $src_x, $src_y, $src_w, $src_h, $vid_sec, $vid_slice, $vid_slices, $msgPath);
				//kFile::dumpFile($tempThumbPath, null, 0);
			}
			//elseif($entry->getType() == entry::ENTRY_TYPE_MEDIACLIP && ($entry->getStatus() == entry::ENTRY_STATUS_PRECONVERT || $entry->getStatus() == entry::ENTRY_STATUS_IMPORT))
			elseif($entry->getType() == entry::ENTRY_TYPE_MEDIACLIP)
			{
				// commenting out the new behavior, in this case the thumbnail will be created in resizeEntryImage
				//$contentPath = myContentStorage::getFSContentRootPath();
				//$msgPath = $contentPath."content/templates/entry/thumbnail/broken_thumb.jpg";
				//header("Xkaltura-app: entry [$entry_id] in conversion, returning template broken thumb");
				//KalturaLog::log( "Entry in conversion, no thumbnail yet [$entry_id], created dynamic 1x1 jpg");
				//kFile::dumpFile($msgPath, null, 0);
				try
				{
					$tempThumbPath = myEntryUtils::resizeEntryImage( $entry, $version , $width , $height , $type , $bgcolor , $crop_provider, $quality, $src_x, $src_y, $src_w, $src_h, $vid_sec, $vid_slice, $vid_slices);
				}
				catch(Exception $ex)
				{
					if($ex->getCode() == kFileSyncException::FILE_DOES_NOT_EXIST_ON_CURRENT_DC)
					{
						// get original flavor asset
						$origFlavorAsset = flavorAssetPeer::retrieveOriginalByEntryId($entry_id);
						if($origFlavorAsset)
						{
							$syncKey = $origFlavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
							$remoteFileSync = kFileSyncUtils::getOriginFileSyncForKey($syncKey, false);
							if(!$remoteFileSync)
							{
								// file does not exist on any DC - die
								KalturaLog::log( "Error - no FileSync for entry [$entry_id]");
								KExternalErrors::dieError(KExternalErrors::MISSING_THUMBNAIL_FILESYNC);
							}
							
							if($remoteFileSync->getDc() == kDataCenterMgr::getCurrentDcId())
							{
								KalturaLog::log("ERROR - Trying to redirect to myself - stop here.");
								KExternalErrors::dieError(KExternalErrors::MISSING_THUMBNAIL_FILESYNC);
							}
							$remote_url = kDataCenterMgr::getRedirectExternalUrl($remoteFileSync, $_SERVER['REQUEST_URI']);
							KalturaLog::log( __METHOD__.": redirecting to [$remote_url]" );
							$this->redirect($remote_url);
						}
					}
				}
			} 
			else
			{
				// file does not exist on any DC - die
				KalturaLog::log( "Error - no FileSync for entry [$entry_id]");
				KExternalErrors::dieError(KExternalErrors::MISSING_THUMBNAIL_FILESYNC);
			}
		}
		
		if ( !$local && !$tempThumbPath)
		{
			$remote_url = kDataCenterMgr::getRedirectExternalUrl ( $file_sync , $_SERVER['REQUEST_URI'] );
			KalturaLog::log ( __METHOD__ . ": redirecting to [$remote_url]" );
			$this->redirect($remote_url);
		}
		
		// if we didnt return a template for the player die and dont return the original deleted thumb
		if ($entry_status == entry::ENTRY_STATUS_DELETED)
		{
			KExternalErrors::dieError(KExternalErrors::ENTRY_DELETED_MODERATED);
		}

		if (!$tempThumbPath)
		{
			$tempThumbPath = myEntryUtils::resizeEntryImage( $entry, $version , $width , $height , $type , $bgcolor , $crop_provider, $quality,
			$src_x, $src_y, $src_w, $src_h, $vid_sec, $vid_slice, $vid_slices  );
		}
		
		$nocache = strpos($tempThumbPath, "_NOCACHE_") !== false;

		// notify external proxy, so it'll cache this url
		if (!$nocache && requestUtils::getHost() == kConf::get ( "apphome_url" )  && file_exists($tempThumbPath))
		{
			self::notifyProxy($_SERVER["REQUEST_URI"]);
		}
		
		// cache result
		if (!$nocache)
		{
			$requestKey = $_SERVER["REQUEST_URI"];
			$cache = new myCache("thumb", 86400 * 30); // 30 days
			$cache->put($requestKey, $tempThumbPath);
		}
		
		kFile::dumpFile($tempThumbPath, null, $nocache ? 0 : null);
		
		// TODO - can delete from disk assuming we caneasily recreate it and it will anyway be cached in the CDN
		// however dumpfile dies at the end so we cant just write it here (maybe register a shutdown callback)
	}
}
?>
