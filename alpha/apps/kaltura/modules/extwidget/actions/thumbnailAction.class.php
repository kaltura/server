<?php
/**
 * @package Core
 * @subpackage externalWidgets
 */
class thumbnailAction extends sfAction
{
	const DEFAULT_DIMENSION = -1;
	static private $extensions = array(
		'jpg',
		'gif',
		'png',
	);

	/* (non-PHPdoc)
	 * @see /symfony/action/sfComponent#getRequestParameter()
	 *
	 * Needed because some partners add .jpg at the end of the url, it might be added to a real attribute.
	 */
	public function getRequestParameter($name, $default = null)
	{
		$exts = implode('|', self::$extensions);
	
		$val = parent::getRequestParameter($name, $default);
		if(!$val)
			return $val;
			
		return preg_replace("/^(.*)\.($exts)$/", '$1', $val);
	}

	public function getIntRequestParameter($name, $default, $min, $max)
	{
		return min($max, max($min, intval($this->getRequestParameter($name, $default))));
	}

	public function getFloatRequestParameter($name, $default, $min, $max = null)
	{
		$val = max($min, floatval($this->getRequestParameter($name, $default)));
		if(is_null($max))
			return $val;
			
		return min($max, $val);
	}
  
  
	/**
	 * Will forward to the regular swf player according to the widget_id
	 */
	public function execute()
	{
		KExternalErrors::setResponseErrorCode(KExternalErrors::HTTP_STATUS_NOT_FOUND);
		
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL2;
		
		requestUtils::handleConditionalGet();
		
		ignore_user_abort();
		
		$entry_id = $this->getRequestParameter("entry_id");
		$widget_id = $this->getRequestParameter("widget_id", 0);
		$upload_token_id = $this->getRequestParameter("upload_token_id");
		$version = $this->getIntRequestParameter("version", null, 0, 10000000);
		$type = $this->getIntRequestParameter("type", 1, 1, 5);
		//Hack: if KMS sends thumbnail request containing "!" char, the type should be treated as 5.
		
		$width = $this->getRequestParameter("width", self::DEFAULT_DIMENSION);
		$height = $this->getRequestParameter("height", self::DEFAULT_DIMENSION);
		if(strpos($width, "!") || strpos($height, "!"))
			$type = 5;

		list($width, $height) = $this->getDimensions();
		
		$nearest_aspect_ratio = $this->getIntRequestParameter("nearest_aspect_ratio", 0, 0, 1);
		$imageFilePath = null;

		$crop_provider = $this->getRequestParameter("crop_provider", null);
		$quality = $this->getIntRequestParameter("quality", 0, 0, 100);
		$src_x = $this->getFloatRequestParameter("src_x", 0, 0, 10000);
		$src_y = $this->getFloatRequestParameter("src_y", 0, 0, 10000);
		$src_w = $this->getFloatRequestParameter("src_w", 0, 0, 10000);
		$src_h = $this->getFloatRequestParameter("src_h", 0, 0, 10000);
		$vid_sec = $this->getFloatRequestParameter("vid_sec", -1, -1);
		$vid_slice = $this->getRequestParameter("vid_slice", -1);
		$vid_slices = $this->getRequestParameter("vid_slices", -1);
		$density = $this->getFloatRequestParameter("density", 0, 0);
		$stripProfiles = $this->getRequestParameter("strip", null);
		$flavor_id = $this->getRequestParameter("flavor_id", null);
		$file_name = $this->getRequestParameter("file_name", null);
		$file_name = basename($file_name);
		
		// actual width and height of image from which the src_* values were taken.
		// these will be used to multiply the src_* parameters to make them relate to the original image size.
		$rel_width = $this->getFloatRequestParameter("rel_width", -1, -1, 10000);
		$rel_height = $this->getFloatRequestParameter("rel_height", -1, -1, 10000);

		$def_width = $this->getFloatRequestParameter("def_width", -1, -1, 10000);
		$def_height = $this->getFloatRequestParameter("def_height", -1, -1, 10000);
		
		if ($width == self::DEFAULT_DIMENSION && $height == self::DEFAULT_DIMENSION) // for sake of backward compatibility if no dimensions where specified create 120x90 thumbnail
		{
			if ( $def_width == -1 )
				$width = 120;
			else
				$width = $def_width;

			if ( $def_height == -1 )
				$height = 90;
			else
				$height = $def_height;
		}
		else if ($width == self::DEFAULT_DIMENSION) // if only either width or height is missing reset them to zero, and convertImage will handle them
		{
				$width = 0;
		}
		else if ($height == self::DEFAULT_DIMENSION)
		{
				$height = 0;
		}
		
		$bgcolor = $this->getRequestParameter( "bgcolor", "ffffff" );
		$partner = null;
		
		$format = $this->getRequestParameter( "format", null);
		
		// validating the inputs
		if(!is_numeric($quality) || $quality < 0 || $quality > 100)
			KExternalErrors::dieError(KExternalErrors::BAD_QUERY, 'quality must be between 20 and 100');
		
		if(!is_numeric($src_x) || $src_x < 0 || $src_x > 10000)
			KExternalErrors::dieError(KExternalErrors::BAD_QUERY, 'src_x must be between 0 and 10000');
		
		if(!is_numeric($src_y) || $src_y < 0 || $src_y > 10000)
			KExternalErrors::dieError(KExternalErrors::BAD_QUERY, 'src_y must be between 0 and 10000');
			
		if(!is_numeric($src_w) || $src_w < 0 || $src_w > 10000)
			KExternalErrors::dieError(KExternalErrors::BAD_QUERY, 'src_w must be between 0 and 10000');
			
		if(!is_numeric($src_h) || $src_h < 0 || $src_h > 10000)
			KExternalErrors::dieError(KExternalErrors::BAD_QUERY, 'src_h must be between 0 and 10000');
			
		if(!is_numeric($width) || $width < 0 || $width > 10000)
			KExternalErrors::dieError(KExternalErrors::BAD_QUERY, 'width must be between 0 and 10000');
			
		if(!is_numeric($height) || $height < 0 || $height > 10000)
			KExternalErrors::dieError(KExternalErrors::BAD_QUERY, 'height must be between 0 and 10000');
			
		if(!is_numeric($density) || $density < 0)
			KExternalErrors::dieError(KExternalErrors::BAD_QUERY, 'density must be positive');
			
		if(!is_numeric($vid_sec) || $vid_sec < -1)
			KExternalErrors::dieError(KExternalErrors::BAD_QUERY, 'vid_sec must be positive');
			
		if(!preg_match('/^[0-9a-fA-F]{1,6}$/', $bgcolor))
			KExternalErrors::dieError(KExternalErrors::BAD_QUERY, 'bgcolor must be six hexadecimal characters');

		if(($vid_slices != -1 && $vid_slices <= 0) || !is_numeric($vid_slices))
			KExternalErrors::dieError(KExternalErrors::BAD_QUERY, 'vid_slices must be positive');

		if ($upload_token_id)
		{
			$upload_token = UploadTokenPeer::retrieveByPK($upload_token_id);
			if ($upload_token)
			{
				$partnerId = $upload_token->getPartnerId();
				$partner = PartnerPeer::retrieveByPK($partnerId);

				if ($partner)
				{
					KalturaMonitorClient::initApiMonitor(false, 'extwidget.thumbnail', $partner->getId());
					if ($quality == 0)
						$quality = $partner->getDefThumbQuality();

					if($density == 0)
						$density = $partner->getDefThumbDensity();

					if(is_null($stripProfiles))
						$stripProfiles = $partner->getStripThumbProfile();
				}
				
				
				$thumb_full_path =  myContentStorage::getFSCacheRootPath() . myContentStorage::getGeneralEntityPath("uploadtokenthumb", $upload_token->getIntId(), $upload_token->getId(), $upload_token->getId() . ".jpg");
				kFile::fullMkdir($thumb_full_path);
				if (file_exists($upload_token->getUploadTempPath()))
				{
					$src_full_path = $upload_token->getUploadTempPath();
					$valid_image_types = array(
						IMAGETYPE_GIF,
						IMAGETYPE_JPEG,
						IMAGETYPE_PNG,
						IMAGETYPE_BMP,
						IMAGETYPE_WBMP,
					);
					
					$image_type = exif_imagetype($src_full_path);
					if(!in_array($image_type, $valid_image_types))
					{
						// capture full frame
						myFileConverter::captureFrame($src_full_path, $thumb_full_path, 1, "image2", -1, -1, 3 );
						if (!file_exists($thumb_full_path))
							myFileConverter::captureFrame($src_full_path, $thumb_full_path, 1, "image2", -1, -1, 0);
						
						$src_full_path = $thumb_full_path;
					}
						
					// and resize it
					myFileConverter::convertImage($src_full_path, $thumb_full_path, $width, $height, $type, $bgcolor, true, $quality, $src_x, $src_y, $src_w, $src_h, $density, $stripProfiles, null, $format);
					kFileUtils::dumpFile($thumb_full_path);
				} else {
					KalturaLog::info ( "token_id [$upload_token_id] not found in DC [". kDataCenterMgr::getCurrentDcId ()."]. dump url to romote DC");
					$remoteUrl = kDataCenterMgr::getRemoteDcExternalUrlByDcId ( 1 - kDataCenterMgr::getCurrentDcId () ) .$_SERVER['REQUEST_URI'];
					kFileUtils::dumpUrl($remoteUrl);
				}
			}
		}
		
		
		if ($entry_id)
		{
			$entry = entryPeer::retrieveByPKNoFilter( $entry_id );
			
			if ( ! $entry )
			{
				// problem could be due to replication lag
				kFileUtils::dumpApiRequest ( kDataCenterMgr::getRemoteDcExternalUrlByDcId ( 1 - kDataCenterMgr::getCurrentDcId () ) );
			}
		}
		else
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

		KalturaMonitorClient::initApiMonitor(false, 'extwidget.thumbnail', $entry->getPartnerId());
		
		if ( $nearest_aspect_ratio )
		{
			// Get the entry's default thumbnail path (if any)
			$defaultThumbnailPath = myEntryUtils::getLocalImageFilePathByEntry( $entry, $version );
			
			// Get the file path of the thumbnail with the nearest  
			$selectedThumbnailDescriptor = kThumbnailUtils::getNearestAspectRatioThumbnailDescriptorByEntryId( $entry_id, $width, $height, $defaultThumbnailPath );

			if ( $selectedThumbnailDescriptor ) // Note: In case nothing returned, then the entry doesn't have a thumbnail to work with, so we'll do nothing.
			{
				$imageFilePath = $selectedThumbnailDescriptor->getImageFilePath();
				
				$thumbWidth = $selectedThumbnailDescriptor->getWidth();
				$thumbHeight = $selectedThumbnailDescriptor->getHeight();

				// The required width and height will serve as the final crop values
				$src_w = $width;
				$src_h = $height;

				// Base on the thumbnail's dimensions
				kThumbnailUtils::scaleDimensions( $thumbWidth, $thumbHeight, $width, $height, kThumbnailUtils::SCALE_UNIFORM_SMALLER_DIM, $width, $height );

				// Set crop type
				$type = KImageMagickCropper::CROP_AFTER_RESIZE;
			}
		}

		$partner = $entry->getPartner();
		
		//checks whether the thumbnail display should be restricted by KS
		$base64Referrer = $this->getRequestParameter("referrer");
		$referrer = base64_decode($base64Referrer);
		if (!is_string($referrer))
			$referrer = ""; // base64_decode can return binary data
		if (!$referrer)
			$referrer = kApiCache::getHttpReferrer();
		$ksStr = $this->getRequestParameter("ks");
		$secureEntryHelper = new KSecureEntryHelper($entry, $ksStr, $referrer, ContextType::THUMBNAIL);
		$secureEntryHelper->validateForPlay();
		
		// not allow capturing frames if the partner has FEATURE_DISALLOW_FRAME_CAPTURE permission
		if(($vid_sec != -1) || ($vid_slice != -1) || ($vid_slices != -1))
		{
			if ($partner->getEnabledService(PermissionName::FEATURE_BLOCK_THUMBNAIL_CAPTURE))
			{
				KExternalErrors::dieError(KExternalErrors::NOT_ALLOWED_PARAMETER);
			}
			
			$actionList = $secureEntryHelper->getActionList(RuleActionType::LIMIT_THUMBNAIL_CAPTURE);
			if ($actionList)
				KExternalErrors::dieError(KExternalErrors::NOT_ALLOWED_PARAMETER);
		}

		if ($partner)
		{
			if ($quality == 0)
				$quality = $partner->getDefThumbQuality();

			if($density == 0)
				$density = $partner->getDefThumbDensity();
		}

		$thumbParams = new kThumbnailParameters();
		$thumbParams->setSupportAnimatedThumbnail($partner->getSupportAnimatedThumbnails());
		
		
		
		if(is_null($stripProfiles))
			$stripProfiles = $partner->getStripThumbProfile();
		
		// multiply the passed $src_* values so that they will relate to the original image size, according to $src_display_*
		if ($rel_width != -1 && $rel_width) {
			$widthRatio  = $entry->getWidth() / $rel_width;
			$src_x = $src_x * $widthRatio;
			$src_w = $src_w * $widthRatio;
		}
		
		if ($rel_height != -1 && $rel_height) {
			$heightRatio  = $entry->getHeight() / $rel_height;
			$src_y  = $src_y * $heightRatio;
			$src_h  = $src_h * $heightRatio;
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
			($entry_status == entryStatus::PRECONVERT || $entry_status == entryStatus::IMPORT ||
			$entry_status == entryStatus::ERROR_CONVERTING || $entry_status == entryStatus::DELETED))
		{
			$contentPath = myContentStorage::getFSContentRootPath();
			$msgPath = $contentPath."content/templates/entry/bigthumbnail/";
			if ($entry_status == entryStatus::DELETED)
			{
				$msgPath .= $entry->getModerationStatus() == moderation::MODERATION_STATUS_BLOCK ?
							"entry_blocked.swf" : "entry_deleted.swf";
			}
			else
			{
				$msgPath .= $entry_status == entryStatus::ERROR_CONVERTING ?
							"entry_error.swf" : "entry_converting.swf";
			}
						
			kFileUtils::dumpFile($msgPath, null, 0);
		}
			
		if ( ! $file_sync )
		{
			$tempThumbPath = $entry->getLocalThumbFilePath($version, $width, $height, $type, $bgcolor, $crop_provider, $quality, $src_x, $src_y, $src_w, $src_h, $vid_sec, $vid_slice, $vid_slices, $density, $stripProfiles, $flavor_id, $file_name );
			if (!$tempThumbPath ){
				KExternalErrors::dieError ( KExternalErrors::MISSING_THUMBNAIL_FILESYNC );
			}
		}
		
		if ( !$local && !$tempThumbPath && $file_sync )
		{
			if (!in_array($file_sync->getDc(), kDataCenterMgr::getDcIds()))
			{
				$remoteUrl =  $file_sync->getExternalUrl($entry->getId());
   				header("Location: $remoteUrl");
   				KExternalErrors::dieGracefully();
			}
			
			$remoteUrl = kDataCenterMgr::getRedirectExternalUrl ( $file_sync , $_SERVER['REQUEST_URI'] );
			kFileUtils::dumpUrl($remoteUrl);
		}
		
		// if we didnt return a template for the player die and dont return the original deleted thumb
		if ($entry_status == entryStatus::DELETED)
		{
			KExternalErrors::dieError(KExternalErrors::ENTRY_DELETED_MODERATED);
		}

		if (!$tempThumbPath)
		{
			try
			{
				$tempThumbPath = myEntryUtils::resizeEntryImage( $entry, $version , $width , $height , $type , $bgcolor , $crop_provider, $quality,
						$src_x, $src_y, $src_w, $src_h, $vid_sec, $vid_slice, $vid_slices, $imageFilePath, $density, $stripProfiles, $thumbParams, $format);
			}
			catch(Exception $ex)
			{
				if($ex->getCode() != kFileSyncException::FILE_DOES_NOT_EXIST_ON_CURRENT_DC)
				{
					KalturaLog::err( "Resize image failed");
					KExternalErrors::dieError(KExternalErrors::MISSING_THUMBNAIL_FILESYNC);
				}
				
				// get original flavor asset
				$origFlavorAsset = assetPeer::retrieveOriginalByEntryId($entry_id);
				if(!$origFlavorAsset)
				{
					KalturaLog::err( "No original flavor for entry [$entry_id]");
					KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
				}

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
					KalturaLog::err("Trying to redirect to myself - stop here.");
					KExternalErrors::dieError(KExternalErrors::MISSING_THUMBNAIL_FILESYNC);
				}
				
				if (!in_array($remoteFileSync->getDc(), kDataCenterMgr::getDcIds()))
				{
					KExternalErrors::dieError ( KExternalErrors::MISSING_THUMBNAIL_FILESYNC );
				}

				$remoteUrl = kDataCenterMgr::getRedirectExternalUrl($remoteFileSync);
				kFileUtils::dumpUrl($remoteUrl);
			}
		}
		
		$nocache = false;
		if ($secureEntryHelper->shouldDisableCache() || kApiCache::hasExtraFields() ||
			(!$secureEntryHelper->isKsWidget() && $secureEntryHelper->hasRules(ContextType::THUMBNAIL)))
			$nocache = true;

		$cache = null;
		
		if(!is_null($entry->getPartner()))
		      $partnerCacheAge = $entry->getPartner()->getThumbnailCacheAge();
		
		if ($nocache)
		{
			$cacheAge = 0;
		}
		else if($partnerCacheAge)
		{
		    $cacheAge = $partnerCacheAge;
		}
		else if (strpos($tempThumbPath, "_NOCACHE_") !== false)
		{
			$cacheAge = 60;
		}
		else
		{
			$cacheAge = 3600;
				
			$cache = new myCache("thumb", 2592000); // 30 days, the max memcache allows
		}

		$lastModifiedFlavor = assetPeer::retrieveLastModifiedFlavorByEntryId($entry->getId());
		$lastModified = $lastModifiedFlavor ? $lastModifiedFlavor->getUpdatedAt(null) : null;
		
		$entryKey = kFileUtils::isFileEncrypt($tempThumbPath) ? $entry->getGeneralEncryptionKey() : null;
		$renderer = kFileUtils::getDumpFileRenderer($tempThumbPath, null, $cacheAge, 0, $lastModified, $entryKey);
		$renderer->partnerId = $entry->getPartnerId();
		
		if ($cache)
		{
			$invalidationKey = $entry->getCacheInvalidationKeys();
			$invalidationKey = kQueryCache::CACHE_PREFIX_INVALIDATION_KEY . $invalidationKey[0];
			$cacheTime = time() - kQueryCache::CLOCK_SYNC_TIME_MARGIN_SEC;
			$cachedResponse = array($renderer, $invalidationKey, $cacheTime);
			$cache->put($_SERVER["REQUEST_URI"], $cachedResponse);
		}
		
		$renderer->output();
	
		KExternalErrors::dieGracefully();
		
		// TODO - can delete from disk assuming we caneasily recreate it and it will anyway be cached in the CDN
		// however dumpfile dies at the end so we cant just write it here (maybe register a shutdown callback)
	}

	private function getDimensions()
	{
		$width = $this->getFloatRequestParameter("width", self::DEFAULT_DIMENSION, -1, 10000);
		$height = $this->getFloatRequestParameter("height", self::DEFAULT_DIMENSION, -1, 10000);
		if ($width != self::DEFAULT_DIMENSION || $height != self::DEFAULT_DIMENSION)
			return array($width, $height);

		$flavorParamsId = $this->getRequestParameter("flavor_params_id");
		$flavorPrams = assetParamsPeer::retrieveByPK($flavorParamsId);

		if ($flavorPrams)
		{
			$width = $flavorPrams->getWidth();
			$height = $flavorPrams->getHeight();
		}
		return array($width, $height);
	}
}
