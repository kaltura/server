<?php 
class myInsertEntryHelper
{
	private $action = null;
	private $paramsArray = null;
	private $kuser_id = 0;
	private $kshow_id = 0;
	private $entry_id = 0;
	private $error_msg = null;
	private $entry = null;
	private $prefix = '';
	
	private $partner_id = null;
	
	public function myInsertEntryHelper($action, $kuser_id, $kshow_id, $paramsArray = null)
	{
		$this->paramsArray = $paramsArray;
		$this->action = $action;
		$this->kuser_id = $kuser_id;
		$this->kshow_id = $kshow_id;
	}
	
	public function getEntry() { return $this->entry; }
	public function getEntryId() { return $this->entry_id; }
	
	private function getParam($name, $default = null)
	{
		if ($this->paramsArray)
			return array_key_exists($name, $this->paramsArray) ? $this->paramsArray[$name] : $default;
		else
			return $this->action->getRequestParameter($this->prefix . $name, $default);
	}
	
	public function setPartnerId ( $partner_id, $subp_id )
	{
		$this->partner_id = $partner_id;	
		$this->subp_id = $subp_id;
	}
	
	private function clear($prefix, $entry_id = 0)
	{
		$this->prefix = $prefix;
		$this->entry_id = $entry_id;
		$this->error_msg = null;
		$this->entry = null;
	}
	
	public function createThumb($prefix)
	{
		return $this->handleEntry(true, $prefix, 0, 0);
	}
	
	public function insertEntry($prefix, $type, $entry_id, $name = null, $tags = null , $entry = null )
	{
		return $this->handleEntry(false, $prefix, $type, $entry_id, $name, $tags);
	}
	
	private function handleEntry($onlyExtractThumb, $prefix, $type, $entry_id, $name = null, $tags = null, $entry=null)
	{
		KalturaLog::debug("handleEntry($type, $entry_id, $name)");
		
		$this->clear($prefix, $entry_id);
		$kuser_id = $this->kuser_id;
		$entry_data_prefix = $kuser_id.'_'. ($prefix == '' ? 'data' : rtrim($prefix, '_'));
		
		$uploads = myContentStorage::getFSUploadsPath();
		$content = myContentStorage::getFSContentRootPath();

		$media_source = $this->getParam('entry_media_source');
		$media_type = $this->getParam('entry_media_type');
		$entry_url = $this->getParam('entry_url');
		$entry_source_link = $this->getParam('entry_source_link');
		$entry_fileName = $this->getParam('entry_data');
		$entry_thumbNum = $this->getParam('entry_thumb_num', 0);
		$entry_thumbUrl  = $this->getParam('entry_thumb_url', '');
		$entry_from_time  = $this->getParam('entry_from_time', 0);
		$entry_to_time  = $this->getParam('entry_to_time', 0);
		
		$should_copy = $this->getParam('should_copy' , false );
		$skip_conversion = $this->getParam('skip_conversion' , false );
		$webcam_suffix = $this->getParam('webcam_suffix' , '' );
		
		$entry_fullPath = "";
		$ext = null;
		$duration = null;
		
		$entry = null;
		if ($entry_id)
		{
			$entry = entryPeer::retrieveByPK($entry_id);
		}
		else
		{
			$entry = new entry();
		}
		$this->entry = $entry;	
		
		$entry_status = $entry->getStatus();
		if(is_null($entry_status))
			$entry_status = entry::ENTRY_STATUS_READY;
		
		// by the end of this block of code $entry_fullPath will point to the location of the entry
		// the entry status will be set (IMPORT / PRECONVERT / READY)

		// a background image is always previewed by the user no matter what source he used
		// so the entry is already in the /uploads directory

		// continue tracking the file upload 
		$te = new TrackEntry();
		$te->setEntryId( $entry_id );
		$te->setTrackEventTypeId( TrackEntry::TRACK_ENTRY_EVENT_TYPE_ADD_ENTRY );
	
			
		KalturaLog::debug("handleEntry: media_source: $media_source, prefix: $prefix");
		if ($media_source == entry::ENTRY_MEDIA_SOURCE_FILE || $prefix == 'bg_')
		{
			$full_path = $this->getParam('entry_full_path');
			if ( $full_path ) 
				$entry_fullPath = $full_path;
			else
				$entry_fullPath = $uploads.$entry_data_prefix.strrchr($entry_fileName, '.');
			
			if ($media_type == entry::ENTRY_MEDIA_TYPE_VIDEO || $media_type == entry::ENTRY_MEDIA_TYPE_AUDIO) // video and audio require conversion
			{
				$entry_status = entry::ENTRY_STATUS_PRECONVERT;
			}
		
			$te->setParam3Str( $entry_fullPath );
			$te->setDescription(  __METHOD__ . ":" . __LINE__ . "::ENTRY_MEDIA_SOURCE_FILE" );
			
				
		}
		else if ($media_source == entry::ENTRY_MEDIA_SOURCE_WEBCAM)
		{
			// set $entry_fileName to webcam output file and flag that conversion is not needed
			$webcam_basePath = $content.'/content/webcam/'.($webcam_suffix ? $webcam_suffix : 'my_recorded_stream_'.$kuser_id);
			$entry_fullPath = $webcam_basePath.'.flv';
			
			// webcam should be preconvert until REALLY ready
			$entry_status = entry::ENTRY_STATUS_READY;
			$ext = "flv";
			
			//echo "myInsertEtryHelper:: [$entry_fullPath]";
			
			// for webcams that might have problmes with the metada - run the clipping even if $entry_from_time and $entry_to_time are null

			if ( $entry_to_time == 0 ) $entry_to_time = null; // this will cause the clipper to reach the end of the file
			
			// clip the webcam to some new file
			
			$entry_fixedFullPath = $webcam_basePath.'_fixed.flv';
			myFlvStaticHandler::fixRed5WebcamFlv($entry_fullPath, $entry_fixedFullPath);
			
			$entry_newFullPath = $webcam_basePath.'_clipped.flv';
			myFlvStaticHandler::clipToNewFile( $entry_fixedFullPath, $entry_newFullPath, $entry_from_time, $entry_to_time );
			$entry_fullPath = $entry_newFullPath ;

			// continue tracking the webcam 
			$te->setParam3Str( $entry_fullPath );
			$te->setDescription(  __METHOD__ . ":" . __LINE__ . "::ENTRY_MEDIA_SOURCE_WEBCAM" );
						
			$duration = myFlvStaticHandler::getLastTimestamp($entry_fullPath);
		}
		else
		{
			// if the url ends with .ext, we'll extract it this way
			$urlext = strrchr($entry_url, '.');
			// TODO: fix this patch
			if( strlen( $urlext ) > 4 ) $urlext = '.jpg'; // if we got something wierd, assume we're downloading a jpg
			$entry_fileName = $entry_data_prefix.$urlext;
			
			KalturaLog::debug("handleEntry: media_type: $media_type");
			if ($media_type == entry::ENTRY_MEDIA_TYPE_IMAGE)
			{
				$duration = 0;
				// TODO - if we got to this point, maybe download the file regardless the media_source
/*				if ($media_source == entry::ENTRY_MEDIA_SOURCE_FLICKR ||
					$media_source == entry::ENTRY_MEDIA_SOURCE_PHOTOBUCKET ||
					$media_source == entry::ENTRY_MEDIA_SOURCE_NYPL ||
					$media_source == entry::ENTRY_MEDIA_SOURCE_MEDIA_COMMONS ||
					$media_source == entry::ENTRY_MEDIA_SOURCE_URL || 
					$media_source == entry::ENTRY_MEDIA_SOURCE_KALTURA )
*/
				{
					$entry_fullPath = $uploads.$entry_fileName;
					if (!kFile::downloadUrlToFile($entry_url, $entry_fullPath))
					{
						KalturaLog::debug("Failed downloading file[$entry_url]");
						$entry_status = entry::ENTRY_STATUS_ERROR_IMPORTING;
					}
				}
				
				// track images 
				$te->setParam3Str( $entry_fullPath );
				$te->setDescription(  __METHOD__ . ":" . __LINE__ . "::ENTRY_MEDIA_SOURCE_URL:ENTRY_MEDIA_TYPE_IMAGE" );
				
			}
			else
			{
				if ($media_type == entry::ENTRY_MEDIA_TYPE_VIDEO) //fixme - we can extract during import
					$ext = "flv";
				else
					$ext = "mp3";
					
 				$entry_status = entry::ENTRY_STATUS_IMPORT;
 				
				// track images 
				$te->setParam3Str( $ext );
				$te->setDescription(  __METHOD__ . ":" . __LINE__ . "::ENTRY_MEDIA_SOURCE_URL:ENTRY_MEDIA_TYPE_VIDEO" );
 				
			}
			
		}
		
		if ($ext == null)
			$ext = strtolower(pathinfo($entry_fullPath, PATHINFO_EXTENSION));
		
			// save the Trackentry
		TrackEntry::addTrackEntry( $te );
			
		KalturaLog::debug("handleEntry: ext: $ext");
			
//		We don't want to reject entries based on file extentions anumore
//		Remarked by Tan-Tan
//
//		if ($entry_status == entry::ENTRY_STATUS_PRECONVERT && !myContentStorage::fileExtNeedConversion($ext))
//		{
//			
//			$this->errorMsg = "insertEntryAction Error - PRECONVERT file type not acceptable ($ext)";
//			KalturaLog::debug("handleEntry: err: $this->errorMsg");
//			if(is_null($entry) && $this->entry_id)
//			{
//				$entry = entryPeer::retrieveByPK($this->entry_id);
//			}
//			if($entry)
//			{
//				$entry->setStatus(entry::ENTRY_STATUS_ERROR_CONVERTING);
//				$entry->save();
//			}
//			return false;
//		}
		
		$media_date = null;
		
//		We don't want to reject entries based on file extentions anumore
//		Remarked by Tan-Tan
//
//		// if entry is ready, validate file type (webcam is an exception since we control the file type - flv)
//		if ($entry_status == entry::ENTRY_STATUS_READY &&
//			$media_source != entry::ENTRY_MEDIA_SOURCE_WEBCAM && !myContentStorage::fileExtAccepted($ext))
//		{
//			$this->errorMsg = "insertEntryAction Error - READY file type not acceptable ($ext)";
//			KalturaLog::debug("handleEntry: err: $this->errorMsg");
//			if(is_null($entry) && $this->entry_id)
//			{
//				$entry = entryPeer::retrieveByPK($this->entry_id);
//			}
//			if($entry)
//			{
//				$entry->setStatus(entry::ENTRY_STATUS_ERROR_CONVERTING);
//				$entry->save();
//			}
//			return false;
//		}
		
		if ($entry_status == entry::ENTRY_STATUS_ERROR_IMPORTING)
		{
			$need_thumb = false; // we wont be needing a thumb for an errornous entry
			KalturaLog::debug("handleEntry: error importing, thumb not needed");
		}
		else
		{
			// thumbs are created by one of the following ways:
			// 1. Image - images are already on disk for every selection method, so we can just create a thumb
			// 2. Audio - no thumb is needed
			// 3. Video -
			//		a. uploaded (file / webcam) - file is on disk and the user already selected a thumb
			//		b. imported - the source site had a thumbnail and we'll use it
	
			$thumbTempPrefix = $uploads.$entry_data_prefix.'_thumbnail_';
			$thumbBigFullPath = null;
			
			$need_thumb = ($type == entry::ENTRY_TYPE_MEDIACLIP);
			KalturaLog::debug("handleEntry: handling media $media_type");
			if ($media_type == entry::ENTRY_MEDIA_TYPE_IMAGE)
			{
				// fetch media creation date
	
				$exif_image_type = @exif_imagetype($entry_fullPath);
				if ($exif_image_type == IMAGETYPE_JPEG || $exif_image_type == IMAGETYPE_TIFF_II ||
					$exif_image_type == IMAGETYPE_TIFF_MM || $exif_image_type == IMAGETYPE_IFF || $exif_image_type == IMAGETYPE_PNG)
				{
					$exif_data = @exif_read_data($entry_fullPath);
					if ($exif_data && isset($exif_data["DateTimeOriginal"]) && $exif_data["DateTimeOriginal"])
					{
						$media_date = $exif_data["DateTimeOriginal"];
						$ts = strtotime($media_date);
						// handle invalid dates either due to bad format or out of range
						if ($ts === -1 || $ts === false || $ts < strtotime('2000-01-01') || $ts > strtotime('2015-01-01'))
						{
							$media_date = null;
						}
					}
				}
				
				// create thumb			
				$thumbFullPath = $thumbTempPrefix.'1.jpg';
				$entry_thumbNum = 1;
				$need_thumb = true;
				//copy($entry_fullPath, $thumbFullPath);
				myFileConverter::createImageThumbnail($entry_fullPath, $thumbFullPath, "image2" );
				//$thumbBigFullPath = $thumbFullPath; // no filesync for thumbnail of image
			}
			else if ($media_type == entry::ENTRY_MEDIA_TYPE_VIDEO)
			{
				if ($entry_status == entry::ENTRY_STATUS_IMPORT || $media_source == entry::ENTRY_MEDIA_SOURCE_URL)
				{
					// import thumb and convert to our size
					$thumbFullPath = $thumbTempPrefix.'1.jpg';
					$entry_thumbNum = 1;
					$importedThumbPath = $uploads.$entry_data_prefix.'_temp_thumb'.strrchr($entry_thumbUrl, '.');
					
					if ( kFile::downloadUrlToFile($entry_thumbUrl, $importedThumbPath) )
					{
						myFileConverter::createImageThumbnail($importedThumbPath, $thumbFullPath, "image2" );
						// set thumb as big thumb so fileSync will be created.
						$thumbBigFullPath = $thumbFullPath;
					}
					else
					{
						$need_thumb = false;
					}
				}
				else if ($entry_thumbNum == 0)
				{
					$entry_thumbNum = 1;
					$thumbTime = 3;
					if ($duration && $duration < $thumbTime * 1000)
						$thumbTime = floor($duration / 1000);
						
					// for videos - thumbail should be created in post convert 
					// otherwise this code will fail if the thumbanil wasn't created successfully (roman)
					//myFileConverter::autoCaptureFrame($entry_fullPath, $thumbTempPrefix."big_", $thumbTime, -1, -1);
					$need_thumb = false;
					$thumbBigFullPath = $thumbTempPrefix."big_".$entry_thumbNum.'.jpg';
				}
				//else select existing thumb ($entry_thumbNum already points to the right thumbnail)
			}
			
			$thumbFullPath = $thumbTempPrefix.$entry_thumbNum.'.jpg';
			
			// if we arrived here both entry and thumbnail are valid we can now update the db
			// in order to have the final entry_id and move its data to its final destination
			
			if ($onlyExtractThumb)
			{
				return $thumbFullPath;
			}
		}
			
		$entry->setkshowId($this->kshow_id);
		$entry->setKuserId($kuser_id);
		
		if ( $this->partner_id != null )
		{
			$entry->setPartnerId( $this->partner_id );
			$entry->setSubpId( $this->subp_id );
		}
		$entry->setName($name ? $name : $this->getParam('entry_name'));
//		$entry->setDescription('');//$this->getParam('entry_description'));
		$entry->setType($type);
		$entry->setMediaType($media_type);
		$entry->setTags($tags ? $tags : $this->getParam('entry_tags'));
		$entry->setSource($media_source);
		$entry->setSourceId($this->getParam('entry_media_id'));
		if ($media_date)
			$entry->setMediaDate($media_date);
		
		// if source_link wasnt given use the entry_url HOWEVER, use it only if id doesnt contain @ which suggests the use of a password
		$entry->setSourceLink($entry_source_link ? $entry_source_link : (strstr($entry_url,'@') ? "" : $entry_url) );
		if ($media_source == entry::ENTRY_MEDIA_SOURCE_FILE)
			$entry->setSourceLink("file:$entry_fullPath");
			 
		$entry->setLicenseType($this->getParam('entry_license'));
		$entry->setCredit($this->getParam('entry_credit'));
		$entry->setStatus($entry_status);
		if ($duration !== null )
			$entry->setLengthInMsecs($duration);
		
		if ($this->entry_id == 0) // new entry
		{
			$entry->save();
			$this->entry_id = $entry->getId();
		}
		
		// move thumb to final destination and set db entry
		if ($media_type != entry::ENTRY_MEDIA_TYPE_AUDIO && $entry_thumbNum && $need_thumb )
		{
			KalturaLog::debug("handleEntry: saving none audio thumb [$thumbBigFullPath]");
			
			$entry->setThumbnail('.jpg');
			
			if ($thumbBigFullPath) // if we created a big thumb, copy it and create a small one as well
			{
				if ($media_type != entry::ENTRY_MEDIA_TYPE_IMAGE)
					myFileConverter::convertImage($thumbBigFullPath, $thumbFullPath);
				
				/*$thumbBigFinalPath = $content.$entry->getBigThumbnailPath();
				myContentStorage::moveFile($thumbBigFullPath, $thumbBigFinalPath, true , $should_copy );
				*/
				$entryThumbKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB);
				try
				{
					if(!$should_copy)
					{
						kFileSyncUtils::moveFromFile($thumbBigFullPath, $entryThumbKey);
					}
					else
					{
						kFileSyncUtils::copyFromFile($thumbBigFullPath, $entryThumbKey);
					}
				}
				catch (Exception $e) {
					$entry->setStatus(entry::ENTRY_STATUS_ERROR_CONVERTING);
					$entry->save();											
					throw $e;
				}
			}
		}
		
		// after extracting the thumb we can move the entry to its next destination
		
		KalturaLog::debug("handleEntry: current status [" . $entry->getStatus() . "]");
		// if needed a job will be submitted for importing external media sources
		if ($entry->getStatus() == entry::ENTRY_STATUS_IMPORT)
 		{
 			KalturaLog::debug("handleEntry: creating import job");
 			// changed by Tan-Tan, Nov 09 to support the new batch mechanism
 			kJobsManager::addImportJob(null, $this->entry_id, $this->partner_id, $entry_url);
 			
 			
 			// remarked by Tan-Tan
//			$entry_fullPath = $content.'/content/imports/data/'.$this->entry_id.".".$ext;
//			myContentStorage::fullMkdir($entry_fullPath);
//			
//			$batchClient = new myBatchUrlImportClient();
// 			$batchClient->addJob($this->entry_id, $entry_url, $entry_fullPath);
 		}
		else if ($entry->getStatus() == entry::ENTRY_STATUS_PRECONVERT )
		{
			if ( ! $skip_conversion )
			{
	 			// changed by Tan-Tan, Dec 09 to support the new batch mechanism
	 			
 				KalturaLog::debug("handleEntry: creating original flavor asset for pre convert");
				$flavorAsset = kFlowHelper::createOriginalFlavorAsset($this->partner_id, $this->entry_id);
				if($flavorAsset)
				{
					$flavorAsset->setFileExt($ext);
					$flavorAsset->save();
					
					$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
					
					try {
						kFileSyncUtils::moveFromFile($entry_fullPath, $syncKey);
					}
					catch (Exception $e) {
						$entry->setStatus(entry::ENTRY_STATUS_ERROR_CONVERTING);
						$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
						$entry->save();
						$flavorAsset->save();												
						throw $e;
					}
					
					kEventsManager::raiseEvent(new kObjectAddedEvent($flavorAsset));
				}
				else
				{
					$entry->setStatus(entry::ENTRY_STATUS_ERROR_CONVERTING);
				}
	 			
//				Remarked by Tan-Tan

//				$targetFileName = $this->entry_id.".".$ext;
//				if ( false /* old conversion */)
//				{				
//								// if we need to convert move entry to conversion directory
//								$preConvPath = $content.'/content/preconvert/';
//								myContentStorage::moveFile($entry_fullPath, $preConvPath."data/".$targetFileName, true , $should_copy );
//								
//								$signalFilePath = $preConvPath."files/".$targetFileName;
//								myContentStorage::fullMkdir($signalFilePath);
//								touch($signalFilePath);
//				}
//				else
//				{
//								$preConvPath = myContentStorage::getFSContentRootPath (). "/content/new_preconvert";
//								$to_data = $preConvPath . "/$targetFileName" ;	
//								myContentStorage::moveFile($entry_fullPath, $to_data , true);
//								touch ( $to_data . ".indicator" );	
//				}
			}
		}
		else if ($entry->getStatus() == entry::ENTRY_STATUS_READY)
		{
			$entry->setData($entry_fullPath);
			$entry->save();
						
			if ($media_type == entry::ENTRY_MEDIA_TYPE_VIDEO || $media_type == entry::ENTRY_MEDIA_TYPE_AUDIO)
			{
	 			KalturaLog::debug("handleEntry: creating original flavor asset for ready entry");
				$flavorAsset = kFlowHelper::createOriginalFlavorAsset($this->partner_id, $this->entry_id);
				if($flavorAsset)
				{
					$ext = pathinfo($entry_fullPath, PATHINFO_EXTENSION);
					$flavorAsset->setFileExt($ext);
					$flavorAsset->save();
				
					$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
					try {
						if(!$should_copy)
						{
							kFileSyncUtils::moveFromFile($entry_fullPath, $syncKey);
						}
						else
						{
							// copy & create file sync from $entry_fullPath
							kFileSyncUtils::copyFromFile($entry_fullPath, $syncKey);
						}
					}
					catch (Exception $e) {
						$entry->setStatus(entry::ENTRY_STATUS_ERROR_CONVERTING);
						$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
						$entry->save();
						$flavorAsset->save();												
						throw $e;
					}
					
//					// bypass to conversion
//					kBusinessPreConvertDL::bypassConversion($flavorAsset, $entry);
					
					/**
					 * if this is webcam entry, create mediaInfo for the source flavor asset synchronously
					 * since entry is ready right at the beginning
					 */
					if($media_source == entry::ENTRY_MEDIA_SOURCE_WEBCAM)
					{
						require_once(SF_ROOT_DIR . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "api_v3" . DIRECTORY_SEPARATOR . "bootstrap.php");
						// extract file path
						$sourceFileKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
						$sourceFilePath = kFileSyncUtils::getLocalFilePathForKey($sourceFileKey);
						
						// call mediaInfo for file
						$mediaInfo = new mediaInfo();
						$mediaInfoParser = new KMediaInfoMediaParser($sourceFilePath, kConf::get('bin_path_mediainfo'));
						$KalturaMediaInfo = new KalturaMediaInfo();
						$KalturaMediaInfo = $mediaInfoParser->getMediaInfo();
						$mediaInfo = $KalturaMediaInfo->toInsertableObject($mediaInfo);
						$mediaInfo->setFlavorAssetId($flavorAsset->getId());
						$mediaInfo->save();						
						
						// fix flavor asset according to mediainfo
						KDLWrap::ConvertMediainfoCdl2FlavorAsset($mediaInfo, $flavorAsset);
						$flavorTags = KDLWrap::CDLMediaInfo2Tags($mediaInfo, array(flavorParams::TAG_WEB, flavorParams::TAG_MBR));
						$flavorAsset->setTags(implode(',', $flavorTags));
						$flavorAsset->save();
					}
					
					kEventsManager::raiseEvent(new kObjectAddedEvent($flavorAsset));
				}
				else
				{
					$entry->setStatus(entry::ENTRY_STATUS_ERROR_IMPORTING);
				}
			}
			else if ($entry->getType() == entry::ENTRY_TYPE_DOCUMENT)
			{
				 //TODO: document should be handled by the plugin manager)
				KalturaLog::debug("handleEntry: creating original flavor asset for ready entry");
				$flavorAsset = kFlowHelper::createOriginalFlavorAsset($this->partner_id, $this->entry_id);
				if($flavorAsset)
				{
					$ext = pathinfo($entry_fullPath, PATHINFO_EXTENSION);
					$flavorAsset->setFileExt($ext);
					$flavorAsset->save();
				
					$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
					try {
						if(!$should_copy)
						{
							kFileSyncUtils::moveFromFile($entry_fullPath, $syncKey);
						}
						else
						{
							// copy & create file sync from $entry_fullPath
							kFileSyncUtils::copyFromFile($entry_fullPath, $syncKey);
						}
					}
					catch (Exception $e) {
						$entry->setStatus(entry::ENTRY_STATUS_ERROR_CONVERTING);
						$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
						$entry->save();
						$flavorAsset->save();												
						throw $e;
					}
					
					kEventsManager::raiseEvent(new kObjectAddedEvent($flavorAsset));
				}					 
			}
			else
			{
				KalturaLog::debug("handleEntry: creating data file sync for file [$entry_fullPath]");
				$entryDataKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
				if(!kFileSyncUtils::file_exists($entryDataKey))
				{
					try {
						if(!$should_copy)
						{
							kFileSyncUtils::moveFromFile($entry_fullPath, $entryDataKey);
						}
						else
						{
							// copy & create file sync from $entry_fullPath
							kFileSyncUtils::copyFromFile($entry_fullPath, $entryDataKey);
						}
					}
					catch (Exception $e) {
						$entry->setStatus(entry::ENTRY_STATUS_ERROR_CONVERTING);
						$entry->save();											
						throw $e;
					}
				}
			}
			
//			Remarked by Tan-Tan, the flavor asset should be synced instead of the entry 
//
//			$entryDataKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
//			if(!$should_copy)
//			{
//				kFileSyncUtils::moveFromFile($entry_fullPath, $entryDataKey);
//			}
//			else
//			{
//				// copy & create file sync from $entry_fullPath
//				kFileSyncUtils::copyFromFile($entry_fullPath, $entryDataKey);
//			}
		}
		
		if ($entry->getStatus() == entry::ENTRY_STATUS_READY)
			$entry->updateDimensions();
		
		$entry->save();
		
		return true;
	} 
}
?>
