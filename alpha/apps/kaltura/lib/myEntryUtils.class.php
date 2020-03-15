<?php

class myEntryUtils
{

	const TEMP_FILE_POSTFIX = "temp_1.jpg";
	const MP4_FILENAME_PARAMETER = "/name/a.mp4";
	const DEFAULT_THUMB_SEC_LIVE = 1;
	const ENTRY_ID_REGEX = "/\d_[A-Za-z0-9]{8}/";

	static private $liveSourceType = array
	(
		EntrySourceType::RECORDED_LIVE,
		EntrySourceType::KALTURA_RECORDED_LIVE,
		EntrySourceType::LECTURE_CAPTURE,
	);

	public static function updateThumbnailFromContent(entry $dbEntry, $content, $fileSyncType = entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB)
	{
		$dbEntry->setThumbnail(".jpg"); // this will increase the thumbnail version
		$dbEntry->setCreateThumb(false);
		$dbEntry->save();

		$dbEntry->reload();
		$fileSyncKey = $dbEntry->getSyncKey($fileSyncType);
		kFileSyncUtils::file_put_contents($fileSyncKey, $content);

		try
		{
			$wrapper = objectWrapperBase::getWrapperClass($dbEntry);
			$wrapper->removeFromCache("entry", $dbEntry->getId());
		}
		catch(Exception $e)
		{
			KalturaLog::err($e);
		}

		myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE_THUMBNAIL, $dbEntry);
	}
	
	public static function createThumbnailAssetFromFile(entry $entry, $filePath)
	{	
		$fileLocation = tempnam(sys_get_temp_dir(), $entry->getId());
		$res = KCurlWrapper::getDataFromFile($filePath, $fileLocation, kConf::get('thumb_size_limit'));
		if (!$res){
			throw new Exception("thumbnail cannot be created from $filePath " . error_get_last());
		}	
		
		$thumbAsset = new thumbAsset();
		$thumbAsset->setPartnerId($entry->getPartnerId());
		$thumbAsset->setEntryId($entry->getId());
		$thumbAsset->setStatus(thumbAsset::ASSET_STATUS_QUEUED);
		$thumbAsset->incrementVersion();
		$thumbAsset->save();

		//getting the data for the thumbnail before moving to SyncKey in case of encryption
		list($width, $height, $type, $attr) = getimagesize($fileLocation);
		$ext = pathinfo($fileLocation, PATHINFO_EXTENSION);
		$size = filesize($fileLocation);
		
		$fileSyncKey = $thumbAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		kFileSyncUtils::moveFromFile($fileLocation, $fileSyncKey);

		$thumbAsset->setWidth($width);
		$thumbAsset->setHeight($height);
		$thumbAsset->setFileExt($ext);
		$thumbAsset->setSize($size);
		$thumbAsset->setStatus(thumbAsset::ASSET_STATUS_READY);
		$thumbAsset->save();
		
		kBusinessConvertDL::setAsDefaultThumbAsset($thumbAsset);
		myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE_THUMBNAIL, $entry);
	}
	
	public static function deepClone ( entry $source , $kshow_id , $override_fields, $echo = false)
	{
		if ($echo)
			echo "Copying entry: " . $source->getId() . "\n";

		// create a copy in the DB
		$target = $source->copy() ;
		// save first time to retrieve id

		if ( $override_fields != NULL )
		{
			// use the $override_fields object
			baseObjectUtils::fillObjectFromObject ( entryPeer::getFieldNames(BasePeer::TYPE_FIELDNAME) ,
				$override_fields , $target , baseObjectUtils::CLONE_POLICY_PREFER_NEW , array  ("id") );
		}

		$target->setKshowId ( $kshow_id );
		// set all statistics to 0
		$target->setComments ( 0 );
		$target->setTotalRank ( 0 );
		$target->setRank ( 0 );
		$target->setViews ( 0 );
		$target->setVotes ( 0 );
		$target->setFavorites ( 0 );
		$target->save(); 

		$content = null;
		$source_thumbnail_path = null;
		$target_thumbnail_path = null;
		$source_data_path = null;
		$target_data_path = null;
		
		if ($echo)
			echo "Copied " . $source->getId() . " (from kshow [" . $source->getKshowId() . "]) -> " . $target->getId() . "\n";

		if ( myContentStorage::isTemplate($source->getData()))
		{
			if ($echo)
				echo ( "source thumbnail same as target. skipping file: " . $content . $source_thumbnail_path . "\n");
		}
		else
		{
			if ($echo)
				echo ( "Copying file: " . $content . $source_thumbnail_path . " -> " .  $content . $target_thumbnail_path ."\n");
			$sourceThumbFileKey = $source->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB);
			if(kFileSyncUtils::file_exists($sourceThumbFileKey))
			{
				$targetThumbFileKey = $target->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB);
				kFileSyncUtils::softCopy($sourceThumbFileKey, $targetThumbFileKey);
			}
			//myContentStorage::moveFile( $content . $source_thumbnail_path , $content . $target_thumbnail_path , false , true );
		}

		if ( myContentStorage::isTemplate($source->getData()))
		{
			if ($echo)
				echo ( "source same as target. skipping file: " . $content . $source_data_path . "\n");
		}
		else
		{
			if ($echo)
				echo ( "Copying file: " . $content . $source_data_path . " -> " .  $content . $target_data_path . "\n");
			$sourceDataFileKey = $source->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
			$targetDataFileKey = $target->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
			kFileSyncUtils::softCopy($sourceDataFileKey, $targetDataFileKey);
			//myContentStorage::moveFile( $content . $source_data_path , $content . $target_data_path , false , true );
		}
		// save second time to
		//$target->save();

		return $target;

	}

	// both paths can hold URLs from which the path should be extracted
	public static function copyData ( $source_entry_id , entry $target )
	{
		// the source_entry can be from any partner - not only of the current context
		entryPeer::getCriteriaFilter()->disable();  // TODO - should not be switched of - it sohuld work ok with the new ks/kn mechanism and only public entries should be copied

		$source_entry = entryPeer::retrieveByPK( $source_entry_id );
		if ( ! $source_entry ) return false;

		$exclude_fields = array(
			"id" , 
			"comments" , 
			"total_rank" , 
			"views" , 
			"votes" , 
			"favorites" , 
			"conversion_profile_id" , 
			"access_control_id" , 
			"categories" , 
			"categories_ids" , 
			"start_date" , 
			"end_date" , 
		);
		
		baseObjectUtils::fillObjectFromObject ( entryPeer::getFieldNames(BasePeer::TYPE_FIELDNAME) ,
				$source_entry , $target , baseObjectUtils::CLONE_POLICY_PREFER_EXISTING , $exclude_fields );

		$target->setDimensions ( $source_entry->getWidth() , $source_entry->getHeight() );

		$target->getCustomDataObj ( );	
		

//		$target->setLengthInMsecs( $source_entry->getLengthInMsecs() );
//		$target->setMediaType( $source_entry->getMediaType() );
//		$target->setTags ( $source_entry->getTags () );

		$sourceThumbKey = $source_entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB); //replaced__getThumbnailPat
		$sourceDataKey = $source_entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);  //replaced__getDataPath
		$sourceDataEditKey = $source_entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA_EDIT); //replaced__getDataPathEdit

//		$target->setThumbnail ( $source_thumbnail_path );
//		$target->setData ( $source_data_path );

		$targetThumbKey = $target->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB); 		//replaced__getThumbnailPath
		$targetDataKey = $target->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA); 		//replaced__getDataPath
		$targetDataEditKey = $target->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA_EDIT); 	//replaced__getDataPathEdit

		$content = myContentStorage::getFSContentRootPath();

//		echo "[$content] [$source_thumbnail_path]->[$target_thumbnail_path] [$source_data_path]->[$target_data_path]";

		if(kFileSyncUtils::file_exists($sourceDataKey, true))
			kFileSyncUtils::softCopy($sourceDataKey, $targetDataKey);
			
		if(kFileSyncUtils::file_exists($sourceThumbKey, true))
			kFileSyncUtils::softCopy($sourceThumbKey, $targetThumbKey);
			
		if(kFileSyncUtils::file_exists($sourceDataEditKey, true))
			kFileSyncUtils::softCopy($sourceDataEditKey, $targetDataEditKey);
			
		
		// added by Tan-Tan 12/01/2010 to support falvors copy
		$sourceFlavorAssets = assetPeer::retrieveByEntryId($source_entry_id);
		foreach($sourceFlavorAssets as $sourceFlavorAsset)
			$sourceFlavorAsset->copyToEntry($target->getId(), $target->getPartnerId());
		

		return true;
	}

	public static function createWidgetImage($entry, $create)
	{
		$contentPath = myContentStorage::getFSContentRootPath();
		$path = kFile::fixPath( $contentPath.$entry->getWidgetImagePath() );

		// if the create flag is not set and the file doesnt exist exit
		// e.g. the roughcut name has change, we update the image only if it was already in some widget
		if (!$create && !file_exists($path))
			return;

		$im = imagecreatetruecolor(400,30);

		$color = imagecolorallocate($im, 188, 230, 99);
		$font = SF_ROOT_DIR.'/web/ttf/arial.ttf';

		imagettftext($im, 12, 0, 10, 21, $color, $font, $entry->getName());

		kFile::fullMkdir($path);

		imagegif($im, $path);
		imagedestroy($im);

	}


	public static function modifyEntryMetadataWithText ( $entry , $text , $duration=6 , $override=false)
	{
		KalturaLog::log ( "modifyEntryMetadataWithText:\n$text");
		$content = myContentStorage::getFSContentRootPath() ;
		if ( ! $override )
		{
			// this will reset the data and increment the count
			$entry->setData( ".xml" );
		}
		$targetFileSyncKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
		
		// doesn't require kFileSyncUtils change - this is a static template that should not be represented as row in the table
		$source = $content . myContentStorage::getGeneralEntityPath ( "entry/data" , 0, 0 , "&metadata_text.xml" );

		if ( $override || !kFileSyncUtils::file_exists($targetFileSyncKey))
		{
//			KalturaLog::log ( "modifyEntryMetadataWithText\n$str_before\n$str_after\n" );

			$template_str = file_get_contents( $source ) ;
			$template_str = str_replace(
				array ( "__TEXT_PLACEHOLDER__" , "__SLIDE_LENGTH_IN_SECS_PLACEHOLDER__"  ) ,
				array ( $text , $duration  ) ,
				$template_str );
			kFileSyncUtils::file_put_contents($targetFileSyncKey , $template_str);

//	 		KalturaLog::log ( "modifyEntryMetadataWithText:\n$text");

			$entry->save();
		}
	}


	// remove strange characters and multiple spaces
	public static function clearUnwantedText( $dict_before ,  $text , $dict_sfter )
	{

		if ( $dict_before != null )
		{
			$from = array();
			$to = array();
			foreach ( $dict_before as $dict_from => $dict_to )
			{
				$from[] = $dict_from;
				$to[] = $dict_to;
			}
			$text = str_replace( $from , $to , $text );
		}

		$text = preg_replace ( "/<script[^<]+?<\\/script>/s"  , " " , $text ); // remove the html script tag and it's content
		$text = preg_replace ( "/<[^>]+?[>]/s"  , " " , $text ); // remove html tags
		$text = preg_replace ( "/[^a-zA-Z0-9\\-_\\n \\']/s" , " " , $text ) ; // get rid of all kind of strange characters - allow single quotes
		$text = preg_replace ( '/[ \r\t]{2,}/s' , " " , $text ) ; // get rid of multiple spaces
		$new_text = "";

		$new_text = $text;

/*
		$len = strlen ( $text );
		for ($i =0 ; $i < $len ;++$i  )
		{
			$c = substr ( $text , $i , 1 );
			$ord = ord ( $c );
			echo "[$c|$ord]";
		}
*/

		if ( $dict_sfter != null )
		{
			$from = array();
			$to = array();
			foreach ( $dict_sfter as $dict_from => $dict_to )
			{
				$from[] = $dict_from;
				$to[] = $dict_to;
			}
			$text = str_replace( $from , $to , $new_text );
		}

		return $new_text;
	}

	// will handle deletion of entries -
	// 1. change status to ENTRY_STATUS_DELETED
	// 2. set data to be the "deleted_entry" depending on the media_type of the entry - point to the partner's template if exists
	// 3. add the entry to the delete_entry table to be handled in a batch way
	// 4. move the file so none of it's versions can be accessed via the web (there is usually only one version for a media_clip)
	public static function deleteEntry ( entry $entry , $partner_id = null , $onlyIfAllJobsDone = false)
	{
		if ( $entry->getStatus() == entryStatus::DELETED || $entry->getStatus() == entryStatus::BLOCKED  )
			return ; // don't do this twice !
			
		 if ($onlyIfAllJobsDone) {
			$dbEntryBatchJobLocks = BatchJobLockPeer::retrieveByEntryId($entry->getId());
			foreach($dbEntryBatchJobLocks as $jobLock) {
				/* @var $jobLock BatchJobLock */
				KalturaLog::info("Entry [". $entry->getId() ."] still has an unhandled batchjob [". $jobLock->getId()."] with status [". $jobLock->getStatus()."] - aborting deletion process.");
				//mark entry for later deletion
				$entry->setMarkedForDeletion(true);
				$entry->save();
				return;
			}
		}
		
		if($entry->getType() === entryType::LIVE_STREAM)
		{
			$connectedEntryServerNodes = EntryServerNodePeer::retrieveByEntryIdAndStatuses($entry->getId(), EntryServerNodePeer::$connectedServerNodeStatuses);
			if(count($connectedEntryServerNodes))
			{
				KalturaLog::info("Live Entry [". $entry->getId() ."] cannot be deleted, while streaming");
				throw new KalturaAPIException(KalturaErrors::CANNOT_DELETE_LIVE_ENTRY_WHILE_STREAMING, $entry->getId());
			}
			
			if($entry->getRecordedEntryId())
			{
				$recordedEntry = entryPeer::retrieveByPK($entry->getRecordedEntryId());
				if($recordedEntry)
				{
					//If entry is pending for recording to finish for more than 7 days than it will probably never happen 
					if($recordedEntry->isInsideDeleteGracePeriod())
					{
						if(in_array($recordedEntry->getStatus(), array(entryStatus::PENDING, entryStatus::NO_CONTENT, entryStatus::PRECONVERT)))
						{
							KalturaLog::info("Live Entry [". $entry->getId() ."] cannot be deleted, associated VOD entry still not in ready status");
							throw new KalturaAPIException(KalturaErrors::RECORDED_NOT_READY, $entry->getId());
						}
						
						if(myEntryUtils::shouldServeVodFromLive($recordedEntry))
						{
							KalturaLog::info("Live Entry [". $entry->getId() ."] cannot be deleted, entry still beeing handled by recordign engien");
							throw new KalturaAPIException(KalturaErrors::RECORDING_FLOW_NOT_COMPLETE, $entry->getId());
						}
					}
				}	
			}
		}
		
		if($entry->getSourceType() == EntrySourceType::KALTURA_RECORDED_LIVE)
		{
			//Check if recorded entry flavors are still not ready to be played, this means set recorded content was not yet called
			if($entry->isInsideDeleteGracePeriod() && myEntryUtils::shouldServeVodFromLive($entry, false) && !$entry->getIsTemporary())
			{
				KalturaLog::info("Recorded Entry [". $entry->getId() ."] cannot be deleted until recorded content is set");
				throw new KalturaAPIException(KalturaErrors::RECORDING_CONTENT_NOT_YET_SET, $entry->getId());
			}
			
			//Check if the recorded entry is the current recorded entry of the live, in that case validate there are not any active server nodes
			$liveEntryId = $entry->getCustomDataRootEntryId();
			$liveEntry = $liveEntryId ? entryPeer::retrieveByPK($liveEntryId) : null;
			if($liveEntry && $liveEntry->getType() == entryType::LIVE_STREAM && $liveEntry->getRecordedEntryId() &&  $liveEntry->getRecordedEntryId() == $entry->getId())
			{
				$connectedEntryServerNodes = EntryServerNodePeer::retrieveByEntryIdAndStatuses($liveEntry->getId(), EntryServerNodePeer::$connectedServerNodeStatuses);
				if(count($connectedEntryServerNodes))
				{
					KalturaLog::info("Recorded Entry [". $entry->getId() ."] cannot be deleted, active server nodes detected");
					throw new KalturaAPIException(KalturaErrors::RECORDING_CONTENT_NOT_YET_SET, $entry->getId());
				} 
			}
		}

		KalturaLog::log("delete Entry [" . $entry->getId() . "] Partner [" . $entry->getPartnerId() . "]");

		kJobsManager::abortEntryJobs($entry->getId());
		
		$media_type = $entry->getMediaType();
		$need_to_fix_roughcut = false;
		$thumb_template_file = "&deleted_image.jpg";
		KalturaLog::log("media type [$media_type]");
		switch ( $media_type )
		{
			case entry::ENTRY_MEDIA_TYPE_AUDIO:
				$template_file = "&deleted_audio.flv";
				$need_to_fix_roughcut = true;
				break;
				
			case entry::ENTRY_MEDIA_TYPE_IMAGE:
				$template_file = "&deleted_image.jpg";
				$need_to_fix_roughcut = false ; // no need to add a batch job for images
				break;
				
			case entry::ENTRY_MEDIA_TYPE_VIDEO:
				$template_file = "&deleted_video.flv";
				$need_to_fix_roughcut = true;
				break;
				
			case entry::ENTRY_MEDIA_TYPE_SHOW:				
			default:
				$template_file = "&deleted_rc.xml";
				$need_to_fix_roughcut = false;
				break;
		}

		if ($entry->getType() == entryType::LIVE_STREAM)
			kJobsManager::addProvisionDeleteJob(null, $entry);
			
		// in this case we'll need some batch job to fix all related roughcuts for this entry
		// use the batch_job mechanism to indicate there is a deleted entry to handle
		if ( $need_to_fix_roughcut )
		{
//			Should use a different job type
//			BatchJob::createDeleteEntryJob ( $entry );
		}

		$entry->putInCustomData( "deleted_original_data" , $entry->getData() ) ;
		$entry->putInCustomData( "deleted_original_thumb" , $entry->getThumbnail() ) ;		

		$content_path = myContentStorage::getFSContentRootPath();

//		Remarked by Tan-Tan 27/09/2010
//		Handled by kObjectDeleteHandler
//		$currentDataKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA); // replaced__getDataPath
//		$currentDataEditKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA_EDIT); // replaced__getDataPathEdit
//		$currentThumbKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB); // replaced__getThumbnailPath

		$entry->setData( $entry->getData() ); 				// once to increment the verions
		$entry->setData( $template_file ); 					// the other to set the template
		$entry->setThumbnail( $entry->getThumbnail() );		// once to increment the verions
		$entry->setThumbnail( $thumb_template_file );		// the other to set the template
		
//		Remarked by Tan-Tan 27/09/2010
//		Handled by kObjectDeleteHandler
//		// move file so there will be no access to it
//		$deleted_content = kFileSyncUtils::deleteSyncFileForKey($currentDataKey);
//		$deleted_content .= "|" . kFileSyncUtils::deleteSyncFileForKey($currentDataEditKey,false); // for some entries there may not be an edit version
//		$deleted_content .= "|" . kFileSyncUtils::deleteSyncFileForKey($currentThumbKey,false); // for some entries (empty mix / audio) there may not be a thumb FileSync
		
//		Remarked by Tan-Tan 27/09/2010
//		$deleted_content is always null anyway
//		$entry->putInCustomData( "deleted_file_path" , $deleted_content ? $deleted_content : serialize($currentDataKey) ) ;
		
		$entry->setStatus ( entryStatus::DELETED ); 
		
		//$entry->setCategories("");
		
		// make sure the moderation_status is set to moderation::MODERATION_STATUS_DELETE
		$entry->setModerationStatus ( moderation::MODERATION_STATUS_DELETE ); 
		$entry->setModifiedAt( time() ) ;
		$entry->save();
		
		myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_ENTRY_DELETE , $entry, null , null , null , null, $entry->getId());
	}

	// will handle deletion of entries -
	// 1. change status to ENTRY_STATUS_DELETED
	// 2. set data to be the "deleted_entry" depending on the media_type of the entry - point to the partner's template if exists
	// 3. add the entry to the delete_entry table to be handled in a batch way
	// 4. move the file so none of it's versions can be accessed via the web (there is usually only one version for a media_clip)
	public static function undeleteEntry ( entry $entry , $partner_id = null )
	{
		if ( $entry->getStatus() != entryStatus::DELETED )
		{
			return;
		}

		$data = $entry->getData();
		$original_play = "";
		
		$parts = explode ( "&" , $data );
		if ( count ( $parts ) < 2 )
			$original_play = $data;
		else
		{
			$original_play = $parts[0];
		}
		
		$deleted_file_path = $entry->getFromCustomData( "deleted_file_path" );

//		echo $deleted_file_path . "\n";
		$deleted_paths = explode ( "|" , $deleted_file_path );

		if ( $deleted_paths )
		{
			$original_play = @$deleted_paths[0];
			$dataKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA, @$deleted_paths[0]);
			kFileSyncUtils::undeleteSyncFile($dataKey);
			//$original = myContentStorage::moveFromDeleted ( @$deleted_paths[0] );
			$dataEditKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA_EDIT, @$deleted_paths[1]);
			kFileSyncUtils::undeleteSyncFile($dataEditKey);
			//$original = myContentStorage::moveFromDeleted ( @$deleted_paths[1] );
			
			//figure out the thumb's path from the deleted path  and the property deleted_original_thumb
			$entry->setData ( null );
			$entry->setData ( $entry->getFromCustomData( "deleted_original_data" ) , true ) ; // force the value that was set beforehand 
			// the data is supposed to point to a delete template 100000.flv&deleted_video.flv

			$orig_thumb = $entry->getFromCustomData( "deleted_original_thumb" );
			if ( myContentStorage::isTemplate( $orig_thumb ) )
			{
				$entry->setThumbnail( $orig_thumb , true ); //  the thumbnail wat a template- use it as it was
			}
			else
			{
				$entry->setThumbnail( null ); // reset the thumb before setting - it won't increment the version count
				$entry->setThumbnail( $entry->getFromCustomData( "deleted_original_thumb" ) , true ); // force the value that was set beforehand
				$thumbKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB, @$deleted_paths[2]);
				kFileSyncUtils::undeleteSyncFile($thumbKey);
				//$original = myContentStorage::moveFromDeleted ( @$deleted_paths[2] ); // 
			}	
		}
		else
		{
			// error
		}
		
		$entry->setStatusReady();
	}


	public static function createRoughcutThumbnailFromEntry ( $source_entry , $should_force = false )
	{
		$kshow = kshowPeer::retrieveByPK( $source_entry->getKshowId() );
		if ( ! $kshow )
		{
			KalturaLog::log( "Error: entry [" . $source_entry->getId() . "] does not have a kshow" );	
			return false;
		}
	
		if ( $kshow )
		{
			$roughcut = $kshow->getShowEntry();
			if ( ! $roughcut )
			{
				KalturaLog::log( "Error: entry [" . $source_entry->getId() . "] from kshow " . $kshow->getId() . "] does not have a roughcut " );
				return false;	
			}
			
			return self::createRoughcutThumbnail ( $roughcut , $source_entry , $should_force )	;
		}
		else
		{
			return false;
		}
		
	}
	
	public static function createRoughcutThumbnail ( $roughcut, $source_entry , $should_force = false )
	{
		if ( ! $roughcut )
		{
			return false;
		} 
		
		$res = self::createThumbnail( $roughcut, $source_entry , $should_force );
		if ( $res ) 
		{
			$content = $roughcut->getDataContent();
			if ( $content )
			{			
				$new_metadata = myMetadataUtils::updateThumbUrlFromMetadata ($content , $source_entry->getThumbnailUrl() );
				$roughcut->setMediaType ( entry::ENTRY_MEDIA_TYPE_SHOW );
				$roughcut->setDataContent($new_metadata, false ,true ) ;
				$roughcut->save();
				return $res;
			}
		}
		return false;
	}
	
	public static function createThumbnail ( $entry , $source_entry , $should_force = false )
	{
		// empty or template
		$empty_path = $entry->getThumbnail() == null  || strpos ( $entry->getThumbnail() , "&" ) !== false ;

		if  ( $should_force || $empty_path )
		{
			return self::createThumbnailFromEntry($entry, $source_entry, -1);
		}

		return false;
	}
	
	public static function createThumbnailFromEntry ( entry $entry , entry $source_entry, $time_offset, $flavorParamsId = null)
	{
		$media_type = $source_entry->getMediaType();
		
		// should capture thumbnail from video
		if ($media_type == entry::ENTRY_MEDIA_TYPE_VIDEO && $time_offset != -1)
		{
			$flavorAsset = null;
			if($flavorParamsId)
				$flavorAsset = assetPeer::retrieveByEntryIdAndParams($source_entry->getId(), $flavorParamsId);
				
			if(is_null($flavorAsset) || !$flavorAsset->isLocalReadyStatus())
				$flavorAsset = assetPeer::retrieveOriginalByEntryId($source_entry->getId());
				
			if (is_null($flavorAsset))
				$flavorAsset = assetPeer::retrieveHighestBitrateByEntryId($source_entry->getId());
			
			if (is_null($flavorAsset))
				throw new Exception("Flavor asset not found");
			
			$flavorSyncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			if (!$flavorSyncKey)
				return false;
			$dataPath = kFileSyncUtils::getReadyLocalFilePathForKey($flavorSyncKey);
			
			$tempPath = myContentStorage::getFSUploadsPath();

			$tempThumbPrefix = $tempPath."temp_thumb".microtime(true);
			$thumbBigFullPath = $tempThumbPrefix."big_1.jpg";
			$thumbFullPath = $tempThumbPrefix.'1.jpg';

			myFileConverter::autoCaptureFrame($dataPath, $tempThumbPrefix."big_", $time_offset, -1, -1);

			// removed creation of "small thumb" - not in use
			myFileConverter::convertImage($thumbBigFullPath, $thumbFullPath);
			
			$bigThumbExists = file_exists($thumbBigFullPath) && filesize($thumbBigFullPath);
			if (!$bigThumbExists)
			{
				return false;
			}			
			$entry->setThumbnail ( ".jpg");
			$entry->setCreateThumb(false);
			$entry->save();
			
			// create new thumb file for entry
			$newThumbKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB);
			kFileSyncUtils::moveFromFile($thumbBigFullPath, $newThumbKey);
		}
		else if ($media_type == entry::ENTRY_MEDIA_TYPE_VIDEO && $time_offset == -1 ||
			$media_type == entry::ENTRY_MEDIA_TYPE_SHOW) // not time offset - copying existing thumb
		{
			$thumbBigFullKey = $source_entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB);
			if(!kFileSyncUtils::fileSync_exists($thumbBigFullKey))
			{
				return false;
			}

			$entry->setThumbnail ( ".jpg");
			$entry->setCreateThumb(false);
			$entry->save();
			// copy existing thumb
			$newThumbKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB);
			kFileSyncUtils::softCopy($thumbBigFullKey, $newThumbKey);
		}
		elseif($media_type == entry::ENTRY_MEDIA_TYPE_IMAGE)
		{
			$thumb_key = $source_entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
			$thumb_path = kFileSyncUtils::getLocalFilePathForKey($thumb_key);
			$entry->setThumbnail ( ".jpg");
			$entry->setCreateThumb(false);
			$entry->save();
			// copy existing thumb
			$newThumbKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB);
			kFileSyncUtils::copyFromFile($thumb_path, $newThumbKey);
		}
		else
		{
			return false;
		}
		return true;
	}
	
	
	public static function resizeEntryImage( entry $entry, $version , $width , $height , $type , $bgcolor ="ffffff" , $crop_provider=null, $quality = 0,
		$src_x = 0, $src_y = 0, $src_w = 0, $src_h = 0, $vid_sec = -1, $vid_slice = 0, $vid_slices = -1, $orig_image_path = null, $density = 0, $stripProfiles = false, $thumbParams = null, $format = null, $fileSync = null,
		$start_sec = -1, $end_sec = -1)
	{
		if (is_null($thumbParams) || !($thumbParams instanceof kThumbnailParameters))
			$thumbParams = new kThumbnailParameters();

		$contentPath = myContentStorage::getFSContentRootPath();
			
		$entry_status = $entry->getStatus();

		$servingVODfromLive = self::shouldServeVodFromLive($entry);
		if ($servingVODfromLive)
		{
			$dc = self::getLiveEntryDcId($entry->getRootEntryId(), EntryServerNodeType::LIVE_PRIMARY);
			if ($dc != kDataCenterMgr::getCurrentDcId ())
				kFileUtils::dumpApiRequest ( kDataCenterMgr::getRemoteDcExternalUrlByDcId ( $dc ) );
		}
		$isStaticPlaylist = ($entry->getType() == entryType::PLAYLIST && $entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_TEXT);
		if ($isStaticPlaylist)
		{
			list($entryIds, $durations, $mediaEntry, $captionFiles) = myPlaylistUtils::executeStitchedPlaylist($entry);
			$entryLengthInMsec = array_sum($durations);
		}
		else
		{
			$entryLengthInMsec = $servingVODfromLive ? $entry->getRecordedLengthInMsecs() : $entry->getLengthInMsecs();
		}
		 
		$thumbName = $entry->getId()."_{$width}_{$height}_{$type}_{$crop_provider}_{$bgcolor}_{$quality}_{$src_x}_{$src_y}_{$src_w}_{$src_h}_{$vid_sec}_{$vid_slice}_{$vid_slices}_{$entry_status}";
		if ($servingVODfromLive && $vid_slices > 0)
			$thumbName .= "_duration_" . $entryLengthInMsec;

		if ($orig_image_path)
			$thumbName.= '_oip_'.basename($orig_image_path);
		if ($density)
			$thumbName.= "_dns_{$density}";
		if($stripProfiles)
			$thumbName .= "_stp_{$stripProfiles}";

		if($start_sec != -1)
			$thumbName .= "_ssec_{$start_sec}";
		if($end_sec != -1)
			$thumbName .= "_esec_{$end_sec}";
				
		$entryThumbFilename = $entry->getThumbnail();
		if(!$entryThumbFilename)
		{
			if($entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_IMAGE)
				$entryThumbFilename = $entry->getVersion().".jpg";
			else
				$entryThumbFilename = "0.jpg";
		}
		if ($entry->getStatus() != entryStatus::READY || @$entryThumbFilename[0] == '&')
			$thumbName .= "_NOCACHE_";
		
		// we remove the & from the template thumb otherwise getGeneralEntityPath will drop $tempThumbName from the final path
		$entryThumbFilename = str_replace("&", "", $entryThumbFilename);
		
		$thumbDirs = kConf::get('thumb_path', 'local', array('0' => 'tempthumb'));
		
		//create final path for thumbnail created
		$finalThumbPath = $contentPath . myContentStorage::getGeneralEntityPath("entry/".$thumbDirs[0], $entry->getIntId(), $thumbName, $entryThumbFilename , $version );;
		
		//Add unique id to the processing file path to avoid file being overwritten when several identical (with same parameters) calls are made before the final thumbnail is created
		$uniqueThumbName = $thumbName . "_" . uniqid() . "_";
		
		//create path for processing thumbnail request
		$processingThumbPath = $contentPath . myContentStorage::getGeneralEntityPath("entry/".$thumbDirs[0], $entry->getIntId(), $uniqueThumbName, $entryThumbFilename , $version );;
		
		if(!is_null($format))
		{
			$finalThumbPath = kFile::replaceExt($finalThumbPath, $format);
			$processingThumbPath = kFile::replaceExt($processingThumbPath, $format);
		}
		
		foreach ($thumbDirs as $thumbDir)
		{
			$currPath = $contentPath . myContentStorage::getGeneralEntityPath("entry/".$thumbDir, $entry->getIntId(), $thumbName, $entryThumbFilename , $version );
			if (file_exists($currPath) && @filesize($currPath))
			{
				if($currPath != $finalThumbPath)
				{
					$moveFileSuccess = kFile::moveFile($currPath, $finalThumbPath);
					if(!$moveFileSuccess)
					{
						KalturaLog::debug("Failed to move thumbnail from [$currPath] to [$finalThumbPath], will return oldPath");
						header("X-Kaltura:cached-thumb-exists,".md5($currPath));
						return $currPath;
					}
				}
				
				header("X-Kaltura:cached-thumb-exists,".md5($finalThumbPath));
				return $finalThumbPath;
			}
		}
		
		/* @var  $fileSync FileSync*/
		if ($fileSync)
			$orig_image_path = $fileSync->getFullPath();
		
		if ($orig_image_path === null || !file_exists($orig_image_path))
		{
			$fileSync = self::getEntryLocalImageFileSync($entry, $version);
			$orig_image_path = self::getLocalImageFilePathByEntry( $entry, $version );
		}
		$isEncryptionNeeded = ($fileSync && $fileSync->isEncrypted());
		
		
		// remark added so ffmpeg will try to load the thumbnail from the original source
		if ($entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_IMAGE && !file_exists($orig_image_path))
			throw new kFileSyncException('no ready filesync on current DC', kFileSyncException::FILE_DOES_NOT_EXIST_ON_CURRENT_DC);
		
		// check a request for animated thumbs without a concrete vid_slice
		// in which case we'll create all the frames as one wide image
		$multi = $vid_slice == -1 && $vid_slices != -1;
		$count = $multi ? $vid_slices : 1;
		$im = null;
		
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_PS2);
		
		if ($multi)
			$vid_slice = 0;
		
		$cacheLockKey = "thumb-processing-resize".$finalThumbPath;
		// creating the thumbnail is a very heavy operation prevent calling it in parallel for the same thumbnail for 5 minutes
		if ($cache && !$cache->add($cacheLockKey, true, 5 * 60))
			KExternalErrors::dieError(KExternalErrors::PROCESSING_CAPTURE_THUMBNAIL);

		// limit creation of more than XX Imagemagick processes
		if (kConf::hasParam("resize_thumb_max_processes_imagemagick") &&
			trim(exec("ps -e -ocmd|awk '{print $1}'|grep -c ".kConf::get("bin_path_imagemagick") )) > kConf::get("resize_thumb_max_processes_imagemagick"))
			KExternalErrors::dieError(KExternalErrors::TOO_MANY_PROCESSES);
								    
		$flavorAssetId = null;
		$packagerRetries = 3;

		if ($entry->getType() == entryType::PLAYLIST)
			myPlaylistUtils::updatePlaylistStatistics($entry->getPartnerId(), $entry);

		if ($servingVODfromLive)
			$orig_image_path = null;

		if(($end_sec != -1 && (($end_sec * 1000) > $entryLengthInMsec)) || ($start_sec != -1 && $end_sec == -1))
		{
			$end_sec = $entryLengthInMsec / 1000;
		}
		if($start_sec == -1 && $end_sec != -1)
		{
			$start_sec = 0;
		}

		while($count--)
		{
			$thumbCaptureByPackager = false;
			$forceRotation = ($vid_slices > -1) ? self::getRotate($flavorAssetId) : 0;
			$params = array($density, $quality, $forceRotation, $src_x, $src_y, $src_w, $src_h, $stripProfiles);
			$shouldResizeByPackager = KThumbnailCapture::shouldResizeByPackager($params, $type, array($width, $height));
			if (
				// need to create a thumb if either:
				// 1. entry is a video and a specific second was requested OR a slices were requested
				// 3. the actual thumbnail doesnt exist on disk
				(($entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_VIDEO || $entry->getType() == entryType::PLAYLIST) && ($vid_sec != -1 || $vid_slices != -1))
				||
				(!file_exists($orig_image_path))
				)
			{
				if ($vid_sec != -1) // a specific second was requested
				{
					$calc_vid_sec = min($vid_sec, floor($entryLengthInMsec / 1000));
				}
				else if ($vid_slices != -1) // need to create a thumbnail at a specific slice
				{
					if($start_sec != -1 && $end_sec != -1)
					{
						$calc_vid_sec = $start_sec + (($end_sec - $start_sec) / $vid_slices) * min($vid_slice, $vid_slices);
					}
					else
					{
						$calc_vid_sec = floor($entryLengthInMsec / $vid_slices * min($vid_slice, $vid_slices) / 1000);
					}
				}
				else if ($entry->getStatus() != entryStatus::READY && $entry->getLengthInMsecs() == 0) // when entry is not ready and we don't know its duration
				{
					$calc_vid_sec = ($entry->getPartner() && $entry->getPartner()->getDefThumbOffset()) ? $entry->getPartner()->getDefThumbOffset() : 3;
				}
				else // default thumbnail was not created yet
				{
					$calc_vid_sec = $servingVODfromLive ? self::DEFAULT_THUMB_SEC_LIVE : $entry->getBestThumbOffset();
				}
					
				$capturedThumbName = $entry->getId()."_sec_{$calc_vid_sec}";
				$capturedThumbPath = $contentPath.myContentStorage::getGeneralEntityPath("entry/".$thumbDirs[0], $entry->getIntId(), $capturedThumbName, $entry->getThumbnail() , $version );
	
				$orig_image_path = $capturedThumbPath.self::TEMP_FILE_POSTFIX;
				
				// if we already captured the frame at that second, do not recapture, just use the existing file
				if (!file_exists($orig_image_path))
				{
					// creating the thumbnail is a very heavy operation
					// prevent calling it in parallel for the same thumbnail for 5 minutes

					$cacheLockKeyProcessing = "thumb-processing".$orig_image_path;
					if ($cache && !$cache->add($cacheLockKeyProcessing, true, 5 * 60))
					{
						KExternalErrors::dieError(KExternalErrors::PROCESSING_CAPTURE_THUMBNAIL);
					}

					$success = false;
					if( ($multi || $servingVODfromLive || $isStaticPlaylist) && $packagerRetries)
					{
						list($picWidth, $picHeight) = $shouldResizeByPackager ? array($width, $height) : array(null, null);
						$destPath = $shouldResizeByPackager ? $capturedThumbPath . uniqid() : $capturedThumbPath;
						$success = myPackagerUtils::captureThumbUsingPackager($entry, $destPath, $calc_vid_sec, $flavorAssetId, $picWidth, $picHeight);
						$packagerResizeFullPath = $destPath . self::TEMP_FILE_POSTFIX;
						KalturaLog::debug("Packager capture is [$success] with dimension [$picWidth,$picHeight] and packagerResize [$shouldResizeByPackager] in path [$packagerResizeFullPath]");
						if(!$success)
						{
							$packagerRetries--;
						}

						$thumbCaptureByPackager = $success;
					}

					if (!$success)
					{
						if ($entry->getType() == entryType::PLAYLIST)
						{
							$success = self::capturePlaylistThumbUsingFirstEntry($entry, $capturedThumbPath, $calc_vid_sec, $cache, $cacheLockKey, $cacheLockKeyProcessing, $orig_image_path);
						}

						if (!$success)
						{
							$success = self::captureLocalThumb($entry, $capturedThumbPath, $calc_vid_sec, $cache, $cacheLockKey, $cacheLockKeyProcessing, $flavorAssetId) ||
								myPackagerUtils::captureRemoteThumbUsingPackager($entry, $orig_image_path, $calc_vid_sec, $flavorAssetId);
						}
					}

					if ($cache)
					{
						$cache->delete($cacheLockKeyProcessing);
					}
					
					if (!$success)
					{
						// since this is not really being processed on this server, and will probably cause redirect in thumbnailAction
						// remove from cache so later requests will still get redirected and will not fail on PROCESSING_CAPTURE_THUMBNAIL
						throw new kFileSyncException('no ready filesync on current DC', kFileSyncException::FILE_DOES_NOT_EXIST_ON_CURRENT_DC);
					}
				}
			}

			// close db connections as we won't be requiring the database anymore and image manipulation may take a long time
			kFile::closeDbConnections();

			if (!self::isTempFile($orig_image_path) && $isEncryptionNeeded)
			{
				$orig_image_path = $fileSync->createTempClear(); //will be deleted after the conversion
				KalturaLog::debug("Creating Clear file at [$orig_image_path] for image conversion");
			}

			kFile::fullMkdir($processingThumbPath);

			if ($thumbCaptureByPackager && $shouldResizeByPackager)
			{
				$processingThumbPath = $packagerResizeFullPath;
				$convertedImagePath = $packagerResizeFullPath;
				KalturaLog::debug("Image was resize in the packager -  setting path [$processingThumbPath]");
			}
			else //need to crop the image
			{
				if ($crop_provider)
				{
					$convertedImagePath = myFileConverter::convertImageUsingCropProvider($orig_image_path, $processingThumbPath, $width, $height, $type, $crop_provider, $bgcolor, true, $quality, $src_x, $src_y, $src_w, $src_h, $density, $stripProfiles,$forceRotation);
				}
				else
				{
					if (!file_exists($orig_image_path) || !filesize($orig_image_path))
						KExternalErrors::dieError(KExternalErrors::IMAGE_RESIZE_FAILED);

					$imageSizeArray = getimagesize($orig_image_path);
					if ($thumbParams->getSupportAnimatedThumbnail() && is_array($imageSizeArray) && $imageSizeArray[2] === IMAGETYPE_GIF)
					{
						$processingThumbPath = kFile::replaceExt($processingThumbPath, "gif");
						$finalThumbPath = kFile::replaceExt($finalThumbPath, "gif");
					}

					$convertedImagePath = myFileConverter::convertImage($orig_image_path, $processingThumbPath, $width, $height, $type, $bgcolor, true, $quality, $src_x, $src_y, $src_w, $src_h, $density, $stripProfiles, $thumbParams, $format,$forceRotation);
				}
				if ($thumbCaptureByPackager && file_exists($packagerResizeFullPath))
				{
					unlink($packagerResizeFullPath);
				}
			}



			// die if resize operation failed
			if ($convertedImagePath === null || !@filesize($convertedImagePath)) {
				KExternalErrors::dieError(KExternalErrors::IMAGE_RESIZE_FAILED);
			}
			
			if ($multi)
			{
				//please notice the 3rd parameter - image type, is ignored. 
				list($w, $h, , $attr, $srcIm) = myFileConverter::createImageByFile($processingThumbPath);
				if (!$im)
					$im = imagecreatetruecolor($w * $vid_slices, $h);
					
				imagecopy($im, $srcIm, $w * $vid_slice, 0, 0, 0, $w, $h);
				imagedestroy($srcIm);
				++$vid_slice;
			}

			if ($thumbCaptureByPackager && $shouldResizeByPackager && $multi && file_exists($packagerResizeFullPath))
			{
				unlink($packagerResizeFullPath);
			}

			if ($isEncryptionNeeded)
			{
				$fileSync->deleteTempClear();
				if (self::isTempFile($orig_image_path) && file_exists($orig_image_path))
				{
					unlink($orig_image_path);
				}
			}
		}
		
		if ($multi)
		{
			imagejpeg($im, $processingThumbPath);
			imagedestroy($im);
		}
		
		kFile::fullMkdir($finalThumbPath);
		kFile::moveFile($processingThumbPath, $finalThumbPath);
		
		if ($cache)
			$cache->delete($cacheLockKey);

		if ($isEncryptionNeeded)
		{
			$finalThumbPath = self::encryptThumb($finalThumbPath, $entry->getGeneralEncryptionKey(), $entry->getEncryptionIv());
		}
				
		return $finalThumbPath;
	}

	private static function isTempFile($filePath)
	{
		return kString::endsWith($filePath, self::TEMP_FILE_POSTFIX);
	}
	
	private static function encryptThumb($thumbPath, $key, $iv)
	{
		$encryptedPath = kFileUtils::addEncryptToFileName($thumbPath);
		if (!kEncryptFileUtils::encryptFile($thumbPath, $key, $iv, $encryptedPath))
			return $thumbPath;
		KalturaLog::debug("Data for entry should encrypted. Encrypted data at [$encryptedPath] with key [$key] and iv [$iv]");
		return $encryptedPath;
	}


	public static function capturePlaylistThumbUsingFirstEntry($playlist, $capturedThumbPath, $calc_vid_sec, $cache, $cacheLockKey, $cacheLockKeyProcessing,$orig_image_path)
	{
		$entry = myPlaylistUtils::getFirstEntryFromPlaylist($playlist);
		if (!$entry)
			KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_FOUND);
		$success = self::captureLocalThumb($entry, $capturedThumbPath, $calc_vid_sec, $cache, $cacheLockKey, $cacheLockKeyProcessing, $flavorAssetId) ||
			myPackagerUtils::captureRemoteThumbUsingPackager($entry, $orig_image_path, $calc_vid_sec, $flavorAssetId);
		return $success;
	}

	public static function getLiveEntryDcId($entryId, $type)
	{
		$entryServerNode = EntryServerNodePeer::retrieveByEntryIdAndServerType($entryId, $type);
		if (!$entryServerNode)
			return null;
		return $entryServerNode->getDCId();
	}

	public static function captureLocalThumb($entry, $capturedThumbPath, $calc_vid_sec, $cache, $cacheLockKey, $cacheLockKeyProcessing, &$flavorAssetId)
	{
		$flavorAsset = assetPeer::retrieveHighestBitrateByEntryId($entry->getId(), flavorParams::TAG_THUMBSOURCE);
		if(is_null($flavorAsset))
		{
			$flavorAsset = assetPeer::retrieveOriginalReadyByEntryId($entry->getId());
			if($flavorAsset)
			{
				$flavorSyncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
				list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($flavorSyncKey,false,false);
				if (!$fileSync)
				{
					$flavorAsset = null;
				}
			}

			if(is_null($flavorAsset) || !($flavorAsset->hasTag(flavorParams::TAG_MBR) || $flavorAsset->hasTag(flavorParams::TAG_WEB)))
			{
				// try the best playable
				$flavorAsset = assetPeer::retrieveHighestBitrateByEntryId($entry->getId(), null, flavorParams::TAG_SAVE_SOURCE);
			}

			if (is_null($flavorAsset))
			{
				// if no READY ORIGINAL entry is available, try to retrieve a non-READY ORIGINAL entry
				$flavorAsset = assetPeer::retrieveOriginalByEntryId($entry->getId());
			}
		}

		if (is_null($flavorAsset))
			KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);

		$flavorAssetId = $flavorAsset->getId();
		$flavorSyncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$entry_data_path = kFileSyncUtils::getReadyLocalFilePathForKey($flavorSyncKey);
		
		if (!$entry_data_path)
			return false;

		// limit creation of more than XX ffmpeg image extraction processes
		if (kConf::hasParam("resize_thumb_max_processes_ffmpeg") &&
			trim(exec("ps -e -ocmd|awk '{print $1}'|grep -c ".kConf::get("bin_path_ffmpeg") )) > kConf::get("resize_thumb_max_processes_ffmpeg"))
		{
			if ($cache)
			{
				$cache->delete($cacheLockKey);
				$cache->delete($cacheLockKeyProcessing);
			}
			
			KExternalErrors::dieError(KExternalErrors::TOO_MANY_PROCESSES);
		}
		
		// close db connections as we won't be requiring the database anymore and capturing a thumbnail may take a long time
		kFile::closeDbConnections();
		$decryptionKey = $flavorAsset->getEncryptionKey() ? bin2hex(base64_decode($flavorAsset->getEncryptionKey())) : null;
		myFileConverter::autoCaptureFrame($entry_data_path, $capturedThumbPath."temp_", $calc_vid_sec, -1, -1, false, $decryptionKey);
		return true;
	}

	public static function isSupportedContainerFormat($flavorAsset){
		if ($flavorAsset->getContainerFormat() == assetParams::CONTAINER_FORMAT_MP42)
			return true;
		if (strpos($flavorAsset->getContainerFormat(), assetParams::CONTAINER_FORMAT_ISOM) !== false)
			return true;
		return false;
	}

	public static function getRotate($flavorAssetId)
	{
		$mediaInfo = $flavorAssetId ? mediaInfoPeer::retrieveByFlavorAssetId($flavorAssetId) : null;
		$videoRotation = 0;
		if($mediaInfo)
			$videoRotation = $mediaInfo->getVideoRotation();

		return $videoRotation;
	}

	public static function getEntryLocalImageFileSync(entry $entry, $version = null)
	{
		$sub_type = $entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_IMAGE ? entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA : entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB;
		$entryImageKey = $entry->getSyncKey($sub_type, $version);
		list ( $file_sync , $local )= kFileSyncUtils::getReadyFileSyncForKey($entryImageKey, false, false);
		return ($local ? $file_sync : null);
	}
	
	public static function getLocalImageFilePathByEntry( $entry, $version = null )
	{
		$sub_type = $entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_IMAGE ? entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA : entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB;
		$entry_image_key = $entry->getSyncKey($sub_type, $version);
		$entry_image_path = kFileSyncUtils::getReadyLocalFilePathForKey($entry_image_key);
		if (!$entry_image_path && $version == 100000)
		{
			$entry_image_key = $entry->getSyncKey($sub_type);
			$entry_image_path = kFileSyncUtils::getReadyLocalFilePathForKey($entry_image_key);
		}
		
		return $entry_image_path;
	} 
	
	//
	// sets the type and media_type of an entry according to the file extension
	// in case the media_type is entry::ENTRY_MEDIA_TYPE_AUTOMATIC we find the media_type from the extension
	// in case the type is entryType::AUTOMATIC we set the type according to the media_type found before
	//
	// two use cases:
	// 1. TYPE set to DOCUMENT and MEDIA_TYPE to AUTOMATIC : the media_type will be set to DOCUMENT no matter what the file ext. is
	// 2. TYPE set to MEDIA_CLIP and MEDIA_TYPE to AUTOMATIC : the correct media_type will be set or remain on AUTOMATIC 
	//		to be handled outside this function 
	// 3. TYPE set to AUTOMATIC and MEDIA_TYPE to AUTOMATIC : the media_type will be detected.
	//		if its found TYPE will be set to MEDIA_CLIP otherwise to DOCUMENT
	//
	static public function setEntryTypeAndMediaTypeFromFile(entry $entry, $entry_full_path)
	{
		$media_type = $entry->getMediaType();
		if ($media_type == entry::ENTRY_MEDIA_TYPE_AUTOMATIC && $entry->getType() != entryType::DATA)
		{
			$media_type = myFileUploadService::getMediaTypeFromFileExt(pathinfo($entry_full_path, PATHINFO_EXTENSION));
			$entry->setMediaType($media_type);
		}
		
		// we'll set the type according to the media_type - either a media_clip or a document
		if ($entry->getType() == entryType::AUTOMATIC)
		{
			if ($media_type == entry::ENTRY_MEDIA_TYPE_IMAGE ||	$media_type == entry::ENTRY_MEDIA_TYPE_VIDEO ||
				$media_type == entry::ENTRY_MEDIA_TYPE_AUDIO)
				$entry->setType(entryType::MEDIA_CLIP);
		}
	}
	
	/*
	 * When there is a big list of entries that we know the getPuser will be called - 
	 * Use this to fetch the whole list rather than one-by-on
	 * TODO - not relevant once merge puser_kuser in kuser table
	 */
	public static function updatePuserIdsForEntries ( $entries )
	{
		if ( ! $entries ) return;
		// get the whole list of kuser_ids	
		$partner_kuser_list = array();
kuserPeer::getCriteriaFilter()->disable(); 			
PuserKuserPeer::getCriteriaFilter()->disable();
		foreach ( $entries as &$entry )
		{
			$pid = $entry->getPartnerId() ;
			if (!isset($partner_kuser_list[$pid]))
			{
				 $partner_kuser_ids = array();
			}
			else
			{
				$partner_kuser_ids = $partner_kuser_list[$pid];
			}
//print_r ( $entry );			
			$kuser_id = $entry->getKuserId();

			$partner_kuser_ids[$kuser_id] = $kuser_id;
			$partner_kuser_list[$pid] = $partner_kuser_ids;
		}

		// the kuser_id is unique across partners
		$kuser_list = array();	
		$puser_id = null;	
		foreach ( $partner_kuser_list as $pid => $kuser_ids )
		{
			$puser_kuser_list = PuserKuserPeer::getPuserIdFromKuserIds( $pid , $kuser_ids );
			
			// builf a map where the key is kuser_id for fast fetch 
			foreach ( $puser_kuser_list as $puser_kuser )
			{
				$kuser_id = $puser_kuser->getKuserId();
				$puser_id = $puser_kuser->getPuserId();
				$kuser_list[$kuser_id]=$puser_id;
			}
		}
		foreach ( $entries as $entry )
		{
			$kuser_id = $entry->getKuserId();
			if(isset($kuser_list[$kuser_id]))
				$puser_id = $kuser_list[$kuser_id];
			
			if ( $puser_id )
			{
				$entry->tempSetPuserId ( $puser_id );
			}
		}
		
		kuserPeer::getCriteriaFilter()->enable(); 			
		PuserKuserPeer::getCriteriaFilter()->enable();
	}
	
	//
	// calculate the total storage size of an entry by adding its file size and archive size
	// if the entry status is deleted the returned size is zero since we can remove it
	//
	public static function calcStorageSize(entry $entry)
	{
		if ($entry->getStatus() == entryStatus::DELETED)
			return 0;
		
		$size = 0;
		
		$entry_id = $entry->getId();
		
		$entrySyncKeys = array(
			$entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA),
			$entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB),
			$entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA_EDIT),
			$entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ARCHIVE),
			$entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DOWNLOAD),
			$entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_OFFLINE_THUMB),
			$entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM),
			$entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC),
			$entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_CONVERSION_LOG),
		);
		
		$assets = assetPeer::retrieveByEntryId($entry_id);
		foreach($assets as $asset)
		{
			$entrySyncKeys[] = $asset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$entrySyncKeys[] = $asset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG);
			$entrySyncKeys[] = $asset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISM);
			$entrySyncKeys[] = $asset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISMC);
		}
		
		foreach($entrySyncKeys as $syncKey)
		{
			$fileSync = kFileSyncUtils::getOriginFileSyncForKey($syncKey, false);
			if(!$fileSync || $fileSync->getStatus() != FileSync::FILE_SYNC_STATUS_READY)
				continue;
			
			$fileSize = $fileSync->getFileSize();
			if($fileSize > 0)
				$size += $fileSize;
		}
			
		return $size;
	}
	
	public static function resetEntryStatistics(entry $entry)
	{
		$entry->setPlays(0);
 		$entry->setViews(0);
 		$entry->setVotes(0);
 		$entry->setRank(0);
 		$entry->setTotalRank(0);
	}

	public static function copyEntryData(entry $entry, entry $targetEntry, $copyFlavors = true, $copyCaptions = true)
	{
		// for any type that does not require assets:
		$shouldCopyDataForNonClip = true;
		if ($entry->getType() == entryType::MEDIA_CLIP)
			$shouldCopyDataForNonClip = false;
		if ($entry->getType() == entryType::PLAYLIST)
			$shouldCopyDataForNonClip = false;

		$shouldCopyDataForClip = false;
		// only images get their data copied
		if($entry->getType() == entryType::MEDIA_CLIP)
		{
			if($entry->getMediaType() != entry::ENTRY_MEDIA_TYPE_VIDEO &&
				$entry->getMediaType() != entry::ENTRY_MEDIA_TYPE_AUDIO)
			{
				$shouldCopyDataForClip = true;
			}
		}

		if($shouldCopyDataForNonClip || $shouldCopyDataForClip)
		{
			// copy the data
			$from = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA); // replaced__getDataPath
			$to = $targetEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA); // replaced__getDataPath
			KalturaLog::log("copyEntriesByType - copying entry data [".$from."] to [".$to."]");
			kFileSyncUtils::softCopy($from, $to);
		}

		$ismFrom = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM);
		if(kFileSyncUtils::fileSync_exists($ismFrom))
		{
			$ismTo = $targetEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM);
			KalturaLog::log("copying entry ism [".$ismFrom."] to [".$ismTo."]");
			kFileSyncUtils::softCopy($ismFrom, $ismTo);
		}

		$ismcFrom = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC);
		if(kFileSyncUtils::fileSync_exists($ismcFrom))
		{
			$ismcTo = $targetEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC);
			KalturaLog::log("copying entry ism [".$ismcFrom."] to [".$ismcTo."]");
			kFileSyncUtils::softCopy($ismcFrom, $ismcTo);
		}

		$from = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB); // replaced__getThumbnailPath
		$considerCopyThumb = true;
		// if entry is image - data is thumbnail, and it was copied
		if($entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_IMAGE)
			$considerCopyThumb = false;
		// if entry is not clip, and there is no file in both DCs - nothing to copy
		if($entry->getType() != entryType::MEDIA_CLIP && !kFileSyncUtils::file_exists($from, true))
			$considerCopyThumb = false;
		if ( $considerCopyThumb )
		{
			$skipThumb = false;
			// don't attempt to copy a thumbnail for images - it's the same as the data which was just created
			if($entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_AUDIO)
			{
				// check if audio entry has real thumb, if not - don't copy thumb.
				$originalFileSync = kFileSyncUtils::getOriginFileSyncForKey($from, false);
				if(!$originalFileSync)
				{
					$skipThumb = true;
				}
			}
			if(!$skipThumb)
			{
				$to = $targetEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB); // replaced__getThumbnailPath
				KalturaLog::log("copyEntriesByType - copying entry thumbnail [".$from."] to [".$to."]");
				kFileSyncUtils::softCopy($from, $to);
			}
		}

		// added by Tan-Tan 12/01/2010 to support falvors copy
		$sourceAssets = assetPeer::retrieveByEntryId($entry->getId());
		foreach($sourceAssets as $sourceAsset)
			if (self::shouldCopyAsset($sourceAsset, $copyFlavors, $copyCaptions))
			{
				$sourceAsset->copyToEntry($targetEntry->getId(), $targetEntry->getPartnerId());
			}
	}

	private static function shouldCopyAsset($sourceAsset, $copyFlavors = true, $copyCaptions = true)
	{
		// timedThumbAsset are copied when ThumbCuePoint are copied
			if ($sourceAsset instanceof timedThumbAsset || ( !$copyFlavors && $sourceAsset instanceof flavorAsset)
			|| ( !$copyCaptions && $sourceAsset instanceof captionAsset))
			return false;
		return true;
	}

	/**
	 * @param entry $entry
	 * @param Partner|null $toPartner
	 * @param array kBaseEntryCloneOptionItem $cloneOptions Array - an array of enumerator of a subset of entry properties.
	 * 													For each subset the user sets
	 * 													whether the subset is included or excluded from the copy.
	 * 													The default action for each subset is 'include'.
	 * 													In case an subset was not set by the user at all; i.e. it was
	 * 													not included or excluded, than it is treated as if it is
	 * 													included in the copy
	 * @return entry
	 * @throws Exception
	 * @throws PropelException
	 * @throws kCoreException
     */
	public static function copyEntry(entry $entry, Partner $toPartner = null,
									 array $cloneOptions)
 	{
 		KalturaLog::log("copyEntry - Copying entry [".$entry->getId()."] to partner [".$toPartner->getId().
			" ] with clone options [ ".print_r($cloneOptions, true)." ]");

		$copyUsers = true;
		$copyCategories = true;
		$copyChildren = false;
		$copyAccessControl = true;
		$copyMetaData = true;
		$copyFlavors  = true;
		$copyCaptions  = true;

		/* @var kBaseEntryCloneOptionComponent $cloneOption */
		foreach ($cloneOptions as $cloneOption)
		{
			$currentOption = $cloneOption->getItemType();
			$currentType = $cloneOption->getRule();

			if ($currentOption == BaseEntryCloneOptions::USERS && $currentType == CloneComponentSelectorType::EXCLUDE_COMPONENT)
			{
				$copyUsers = false;
			}

			if ($currentOption == BaseEntryCloneOptions::CATEGORIES && $currentType == CloneComponentSelectorType::EXCLUDE_COMPONENT)
			{
				$copyCategories = false;
			}
			if ($currentOption == BaseEntryCloneOptions::CHILD_ENTRIES && $currentType == CloneComponentSelectorType::INCLUDE_COMPONENT)
			{
				$copyChildren = true;
			}
			if ($currentOption == BaseEntryCloneOptions::ACCESS_CONTROL && $currentType == CloneComponentSelectorType::EXCLUDE_COMPONENT)
			{
				$copyAccessControl = false;
			}
			if ($currentOption == BaseEntryCloneOptions::METADATA && $currentType == CloneComponentSelectorType::EXCLUDE_COMPONENT)
			{
				$copyMetaData = false;
			}
			if ($currentOption == BaseEntryCloneOptions::FLAVORS && $currentType == CloneComponentSelectorType::EXCLUDE_COMPONENT)
			{
				$copyFlavors = false;
			}
			if ($currentOption == BaseEntryCloneOptions::CAPTIONS && $currentType == CloneComponentSelectorType::EXCLUDE_COMPONENT)
			{
				$copyCaptions = false;
			}
		}

 		$newEntry = $entry->copy();
	    $newEntry->setCloneOptions($cloneOptions);
 		$newEntry->setIntId(null);
		$newEntry->setCategories(null);
		$newEntry->setCategoriesIds(null);

	    if (!$copyFlavors)
		{
			$newEntry->setStatus(entryStatus::NO_CONTENT);
			$newEntry->setLengthInMsecs(0);
		}


	    if ($toPartner instanceof Partner)
 		{
 			$newEntry->setPartnerId($toPartner->getId());
 			$newEntry->setSubpId($toPartner->getId() * 100);
		    if ($toPartner->getId() != $entry->getPartnerId() || !$copyAccessControl)
				$newEntry->setAccessControlId($toPartner->getDefaultAccessControlId());
 		}
 		
		$kuserForNewEntry = null;
		if ($copyUsers)
		{
			// copy the kuser (if the same puser id exists its kuser will be used)
			kuserPeer::setUseCriteriaFilter(false);
			$kuser = $entry->getKuser();
			$kuserForNewEntry = kuserPeer::createKuserForPartner($newEntry->getPartnerId(), $kuser->getPuserId());
			kuserPeer::setUseCriteriaFilter(true);
		}
		else
			$kuserForNewEntry = kCurrentContext::getCurrentKsKuser();

		if($kuserForNewEntry)
		{
			$newEntry->setKuserId($kuserForNewEntry->getId());
			$newEntry->setCreatorKuserId($kuserForNewEntry->getId());
		}
 		
 		// copy the kshow
 		kshowPeer::setUseCriteriaFilter(false);
 		$kshow = $entry->getKshow();
 		if ($kshow)
 		{
 			$newKshow = $kshow->copy();
 			$newKshow->setIntId(null);
 			$newKshow->setPartnerId($toPartner->getId());
 			$newKshow->setSubpId($toPartner->getId() * 100);
 			if ($kuserForNewEntry) {
 				$newKshow->setProducerId($kuserForNewEntry->getId());
 			}
 			$newKshow->save();
 			
 			$newEntry->setKshowId($newKshow->getId());
 		}
 		kshowPeer::setUseCriteriaFilter(true);
 		
 		// reset the statistics
 		myEntryUtils::resetEntryStatistics($newEntry);
 		
 		// set the new partner id into the default category criteria filter
 		$defaultCategoryFilter = categoryPeer::getCriteriaFilter()->getFilter();
 		$oldPartnerId = $defaultCategoryFilter->get(categoryPeer::PARTNER_ID);
 		$defaultCategoryFilter->remove(categoryPeer::PARTNER_ID);
 		$defaultCategoryFilter->addAnd(categoryPeer::PARTNER_ID, $newEntry->getPartnerId());

 		if (!$copyMetaData)
		    $newEntry->copyMetaData = false;

		$newEntry->setSourceType(self::getCloneSourceType($entry->getSourceType()),true);
		$newEntry->setConversionProfileId(self::getCloneConversionProfile($entry->getSourceType(),$toPartner,$entry));

		$quizData = $entry->getFromCustomData( QuizPlugin::QUIZ_DATA );
		if ($quizData)
		{
			$newEntry->putInCustomData(QuizPlugin::QUIZ_DATA,$quizData);
			$newEntry->addCapability(QuizPlugin::getCapatabilityCoreValue());
		}

	    	// save the entry
 		$newEntry->save();
 		 		
 		// restore the original partner id in the default category criteria filter
		$defaultCategoryFilter->remove(categoryPeer::PARTNER_ID);
 		$defaultCategoryFilter->addAnd(categoryPeer::PARTNER_ID, $oldPartnerId);
 		
 		KalturaLog::log("copyEntry - New entry [".$newEntry->getId()."] was created");

		if ( $entry->getStatus() != entryStatus::READY ) {
			$entry->addClonePendingEntry($newEntry->getId());
			$entry->save();
		} else {
			self::copyEntryData( $entry, $newEntry, $copyFlavors, $copyCaptions );
		}

 	    //if entry is a static playlist, link between it and its new child entries
		if ($entry->getType() == entryType::PLAYLIST)
		{
		    switch ($entry->getMediaType())
		    {
		        case entry::ENTRY_MEDIA_TYPE_TEXT:
        		    $from = $entry->getDataContent();
        		    KalturaLog::debug("Entries to copy from source static playlist: [$from]");
                    $fromEntryIds = explode(",", $from);
                    $toEntryIds = array();
                    foreach ($fromEntryIds as $fromEntryId)
                    {
                        $toEntryIds[] = kObjectCopyHandler::getMappedId(entryPeer::OM_CLASS, $fromEntryId);
                    }
                    
                    $newEntry->setDataContent(implode(",", $toEntryIds));
                    break;
		        case entry::ENTRY_MEDIA_TYPE_XML:
		            list($totalResults, $fromFiltersList) = myPlaylistUtils::getPlaylistFilterListStruct($entry->getDataContent());
		            $toPlaylistXml = new SimpleXMLElement("<playlist/>");
		            $toPlaylistXml->addChild("total_results", $totalResults);
		            $toFiltersXml = $toPlaylistXml->addChild("filters");
		            foreach ($fromFiltersList as $filterXML)
		            {
		                $entryFilter = new entryFilter();
			            $entryFilter->fillObjectFromXml($filterXML, "_"); 
			            if (isset($entryFilter->fields["_matchand_categories_ids"]))
			            {
			                $categoriesIds = explode(",", $entryFilter->fields["_matchand_categories_ids"]);
			                $newCategoriesIds = array();
			                foreach ($categoriesIds as $categoryId)
			                {
			                    $newCategoriesIds[] = kObjectCopyHandler::getMappedId(categoryPeer::OM_CLASS, $categoryId);
			                }
			                $entryFilter->fields["_matchand_categories_ids"] = implode (",", $newCategoriesIds);
			            }
		                if (isset($entryFilter->fields["_matchor_categories_ids"]))
			            {
			                $categoriesIds = explode(",", $entryFilter->fields["_matchor_categories_ids"]);
			                $newCategoriesIds = array();
			                foreach ($categoriesIds as $categoryId)
			                {
			                    $newCategoriesIds[] = kObjectCopyHandler::getMappedId(categoryPeer::OM_CLASS, $categoryId);
			                }
			                $entryFilter->fields["_matchor_categories_ids"] = implode (",", $newCategoriesIds);
			            }
		                if (isset($entryFilter->fields["_in_category_ancestor_id"]))
			            {
			                $categoriesIds = explode(",", $entryFilter->fields["_in_category_ancestor_id"]);
			                $newCategoriesIds = array();
			                foreach ($categoriesIds as $categoryId)
			                {
			                    $newCategoriesIds[] = kObjectCopyHandler::getMappedId(categoryPeer::OM_CLASS, $categoryId);
			                }
			                $entryFilter->fields["_in_category_ancestor_id"] = implode (",", $newCategoriesIds);
			            }
			            $toEntryFilterXML = $toFiltersXml->addChild("filter");
			            $toEntryFilterXML = $entryFilter->toXml($toEntryFilterXML);
		            }
		            
		            $newEntry->setDataContent($toPlaylistXml->asXML());
		            break;
		    }
		}

		if ($copyCategories)
		{
			// copy relationships to categories
			KalturaLog::debug('Copy relationships to categories from entry [' . $entry->getId() . '] to entry [' . $newEntry->getId() . ']');
			$c = KalturaCriteria::create(categoryEntryPeer::OM_CLASS);
			$c->addAnd(categoryEntryPeer::ENTRY_ID, $entry->getId());
			$c->addAnd(categoryEntryPeer::STATUS, CategoryEntryStatus::ACTIVE, Criteria::EQUAL);
			$c->addAnd(categoryEntryPeer::PARTNER_ID, $entry->getPartnerId());

	        categoryEntryPeer::setUseCriteriaFilter(false);
			$categoryEntries = categoryEntryPeer::doSelect($c);
			categoryEntryPeer::setUseCriteriaFilter(true);

			// Create srcCategoryIdToDstCategoryIdMap - a map of source partner category ids -> dst. partner category ids
			//
			// Build src category IDs set
			$srcCategoryIdSet = array();
			foreach($categoryEntries as $categoryEntry)
			{
				$srcCategoryIdSet[] = $categoryEntry->getCategoryId();
			}

			$illegalCategoryStatus = array( CategoryStatus::DELETED, CategoryStatus::PURGED );

			// Get src category objects
			$c = KalturaCriteria::create(categoryPeer::OM_CLASS);
			$c->add(categoryPeer::ID, $srcCategoryIdSet, Criteria::IN);
			$c->addAnd(categoryPeer::PARTNER_ID, $entry->getPartnerId());
			$c->addAnd(categoryPeer::STATUS, $illegalCategoryStatus, Criteria::NOT_IN);
			categoryPeer::setUseCriteriaFilter(false);
			$srcCategories = categoryPeer::doSelect($c);
			categoryPeer::setUseCriteriaFilter(true);

			// Map the category names to their IDs
			$fullNamesToSrcCategoryIdMap = array();
			foreach ( $srcCategories as $category )
			{
				$fullNamesToSrcCategoryIdMap[ $category->getFullName() ] = $category->getId();
			}

			// Get dst. partner categories based on src. category full-names
			$c = KalturaCriteria::create(categoryPeer::OM_CLASS);
			$c->add(categoryPeer::FULL_NAME, array_keys( $fullNamesToSrcCategoryIdMap ), KalturaCriteria::IN);
			$c->addAnd(categoryPeer::PARTNER_ID, $newEntry->getPartnerId());
			$c->addAnd(categoryPeer::STATUS, $illegalCategoryStatus, Criteria::NOT_IN);
			categoryPeer::setUseCriteriaFilter(false);
			$dstCategories = categoryPeer::doSelect($c);
			categoryPeer::setUseCriteriaFilter(true);

			$srcCategoryIdToDstCategoryIdMap = array();
			foreach ( $dstCategories as $dstCategory )
			{
				$fullName = $dstCategory->getFullName();
				if ( array_key_exists( $fullName, $fullNamesToSrcCategoryIdMap ) )
				{
					$srcCategoryId = $fullNamesToSrcCategoryIdMap[ $fullName ];
					$srcCategoryIdToDstCategoryIdMap[ $srcCategoryId ] = $dstCategory->getId();
				}
			}

			foreach($categoryEntries as $categoryEntry)
			{
				/* @var $categoryEntry categoryEntry */
				$newCategoryEntry = $categoryEntry->copy();
				$newCategoryEntry->setPartnerId($newEntry->getPartnerId());
				$newCategoryEntry->setEntryId($newEntry->getId());

				$srcCategoryId = $categoryEntry->getCategoryId();
				if ( ! array_key_exists( $srcCategoryId, $srcCategoryIdToDstCategoryIdMap ) )
				{
					continue; // Skip the category_entry's creation
				}

				$dstCategoryId = $srcCategoryIdToDstCategoryIdMap[ $srcCategoryId ];
				$newCategoryEntry->setCategoryId( $dstCategoryId );

				categoryPeer::setUseCriteriaFilter(false);
				entryPeer::setUseCriteriaFilter(false);
				$newCategoryEntry->save();
				entryPeer::setUseCriteriaFilter(true);
				categoryPeer::setUseCriteriaFilter(true);
			}
		}


	    if ($copyChildren)
	    {
		    self::cloneFamilyEntries($entry, $toPartner, $cloneOptions, $newEntry);
	    }

	    if($newEntry->getPartnerId() == $newEntry->getPartnerId())
	    {
		    $newEntry->setRootEntryId($entry->getId());
		    $newEntry->save();
	    }
	    return $newEntry;
 	}

	private static function isSourceLive($sourceType)
    	{
        	return in_array($sourceType,self::$liveSourceType);
    	}

	private static function getCloneSourceType($originSourceType)
	{
	        $entrySourceType = array_merge(self::$liveSourceType, array(EntrySourceType::FILE, EntrySourceType::WEBCAM, EntrySourceType::URL, EntrySourceType::CLIP, EntrySourceType::SEARCH_PROVIDER));
        	if (in_array($originSourceType,$entrySourceType))
	            return EntrySourceType::CLIP;

        	return $originSourceType;
	}	

	private static function getCloneConversionProfile($originSourceType,$partner,$sourceEntry)
	{
        	if (self::isSourceLive($originSourceType))
	            return $partner->getDefaultConversionProfileId();

        	return $sourceEntry->getConversionProfileId();
	}

 	/*
 	 * re-index to search index, and recalculate fields.
 	 */
 	public static function index(entry $entry)
 	{
 		$categoriesWithNoPrivacyContext = $entry->getCategoriesWithNoPrivacyContext();
 		
 		$categoriesFullName = array();
 		$categoriesIds = array();
 		
 		foreach($categoriesWithNoPrivacyContext as $category)
 		{
 			$categoriesFullName[] = $category->getFullName();
 			$categoriesIds[] = $category->getId();
 		}
 		
 		$entry->parentSetCategories(implode(',', $categoriesFullName));
		$entry->parentsetCategoriesIds(implode(',', $categoriesIds));
		
		if(!$entry->save())		
			$entry->indexToSearchIndex();
		
		return $entry->getIntId();
 	}

	/*
	 * Delete replacing entry for recorded entry
	 */
	public static function deleteReplacingEntry(entry $recordedEntry,entry $replacingEntry)
	{
		self::deleteEntry($replacingEntry);
		$recordedEntry->setReplacingEntryId(null);
		$recordedEntry->setReplacementStatus(entryReplacementStatus::NONE);
		$recordedEntry->save();
	}
	
	/*
	 * Check if recorded entry should be served from live
	 */
	public static function shouldServeVodFromLive(entry $entry, $validateStatus = true)
	{
		if ($entry->getType() != entryType::MEDIA_CLIP || $entry->getSource() != EntrySourceType::KALTURA_RECORDED_LIVE)
			return false;

		if($validateStatus && $entry->getStatus() != entryStatus::READY)
			return false;

		$readyAssets = assetPeer::retrieveFlavorsByEntryIdAndStatusIn($entry->getId(), array(asset::ASSET_STATUS_READY));
		if(!count($readyAssets))
			return true;

		if (!$validateStatus)
			return false;

		//check if entry is in append mode and currently streaming
		$liveEntry = entryPeer::retrieveByPK($entry->getRootEntryId());
		if ($liveEntry && $liveEntry instanceof LiveEntry && $liveEntry->getSource() == EntrySourceType::LIVE_STREAM
			&& $liveEntry->getRecordStatus() == RecordStatus::APPENDED && $liveEntry->getRecordedEntryId() == $entry->getId())
			return !is_null(EntryServerNodePeer::retrieveByEntryIdAndServerType($liveEntry->getId(), EntryServerNodeType::LIVE_PRIMARY));

		return false;
	}

	public static function isLiveClippingEntry(entry $entry, $validateStatus = true)
	{
		return $entry && $entry->getIsRecordedEntry() && self::shouldServeVodFromLive($entry, $validateStatus);
	}

	public static function isEntryReady($entryId)
	{
		$entry = entryPeer::retrieveByPk($entryId);
		if (!$entry)
			return false;
		return $entry->isReady();
	}

	/*
	 * Builds a manifest request for entry according to relevant flavors, delivery profile type and location of deliveryProfile(local/remote)
	 */
	public static function buildManifestUrl($entry, $protocols, $format, $flavors, $profileId = "")
	{
		$entryId = $entry->getId();
		$partnerId = $entry->getPartnerId();
		$partnerPath = myPartnerUtils::getUrlForPartner($partnerId, $partnerId);

		$protocol = null;
		if ( in_array(infraRequestUtils::PROTOCOL_HTTPS ,$protocols))
			$protocol = infraRequestUtils::PROTOCOL_HTTPS;
		else
			$protocol = infraRequestUtils::PROTOCOL_HTTP;

		$cdnApiHost = null;
		if ($protocol == infraRequestUtils::PROTOCOL_HTTPS && kConf::hasParam('cdn_api_host_https'))
			$cdnApiHost = "$protocol://" . kConf::get('cdn_api_host_https');
		else
			$cdnApiHost =  "$protocol://" . kConf::get('cdn_api_host');

		$flavorIds = array();
		$fileExtension = null;

		foreach ( $flavors as $flavor)
		{
			$flavorIds[] = $flavor->getId();
			$fileExtension = $flavor->getFileExt();
		}

		$flavorIdsAsString = implode(",", $flavorIds);
		$url = $cdnApiHost. "$partnerPath/playManifest/entryId/$entryId/";

		if (count($flavors))
			$url = $url."flavorIds/$flavorIdsAsString/";

		$url = $url."deliveryProfileId/$profileId/protocol/$protocol";

		$url .= "/format/" . self::getFileExtensionByFormat($format, $fileExtension);

		return $url;
	}

	public static function getFileExtensionByFormat($format, $fileExtension = "" )
	{
		switch ($format)
		{
			case PlaybackProtocol::AKAMAI_HD:
				return PlaybackProtocol::AKAMAI_HD . "/manifest.f4m";
			case PlaybackProtocol::MULTICAST_SL:
				return PlaybackProtocol::MULTICAST_SL . "/a.ism";
			case PlaybackProtocol::RTMP:
				return PlaybackProtocol::RTMP . "/a.f4m";
			case PlaybackProtocol::APPLE_HTTP:
				return PlaybackProtocol::APPLE_HTTP . "/a.m3u8";
			case PlaybackProtocol::HDS:
				return PlaybackProtocol::HDS . "/a.f4m";
			case PlaybackProtocol::SILVER_LIGHT:
				return PlaybackProtocol::SILVER_LIGHT . "/a.ism";
			case PlaybackProtocol::HLS:
				return PlaybackProtocol::APPLE_HTTP . "/a.m3u8";
			case PlaybackProtocol::MPEG_DASH:
				return PlaybackProtocol::MPEG_DASH . "/manifest.mpd";
			case PlaybackProtocol::APPLE_HTTP_TO_MC:
				return PlaybackProtocol::APPLE_HTTP_TO_MC . "/a.m3u8";
			case PlaybackProtocol::RTSP:
				return PlaybackProtocol::RTSP . "/name/a.3gp";
			case PlaybackProtocol::AKAMAI_HDS:
				return PlaybackProtocol::AKAMAI_HDS . "/manifest.f4m";
			case PlaybackProtocol::HTTP:
				return PlaybackProtocol::URL . "/name/a.$fileExtension";
			default:
				return "";
		}
	}

	/**
	 * @param entry $entry
	 * @param Partner $toPartner
	 * @param array $cloneOptions
	 * @param $newEntry
	 */
	protected static function cloneFamilyEntries(entry $entry, Partner $toPartner, array $cloneOptions, $newEntry)
	{
		$entry->setInClone(true);
		$entry->save();
		$parentEntry = $entry->getParentEntry();
		if ($parentEntry->getId() != $entry->getId())
		{
			if (!$parentEntry->getInClone())
			{
				$parentClone = self::copyEntry($parentEntry, $toPartner, $cloneOptions);
				$newEntry->setParentEntryId($parentClone->getId());
				$newEntry->save();

			}
		}

		$childEntries = entryPeer::retrieveChildEntriesByEntryIdAndPartnerId($entry->getId(), $entry->getPartnerId());
		foreach ($childEntries as $child)
		{
			/**
			 * @var entry $child
			 */
			if (!$child->getInClone())
			{
				$childCopy = self::copyEntry($child, $toPartner, $cloneOptions);
				$childCopy->setParentEntryId($newEntry->getId());
				$childCopy->setSourceEntryId($child->getId());
				$childCopy->save();
			}
		}
		$entry->setInClone(false);
		$entry->save();
	}

	public static function wasEntryClipped(entry $entry, array $modifiedColumns)
	{
		if (in_array(entryPeer::CUSTOM_DATA, $modifiedColumns)
			&& $entry->isCustomDataModified('operationAttributes')
			&& $entry->isCustomDataModified('sourceEntryId'))
			{
				//clip case
				if(!$entry->getReplacedEntryId())
				return true;
				//trim case
				if($entry->getTempTrimEntry())
				return true;
			}
		return false;
	}
	public static function getRelatedEntries($entry)
	{
		/* @var $entry entry */
		if (!$entry->getParentEntryId())
			return entryPeer::retrieveChildEntriesByEntryIdAndPartnerId($entry->getId(), $entry->getPartnerId());
		$relatedEntries = array(entryPeer::retrieveByPK($entry->getParentEntryId()));
		$childEntries = entryPeer::retrieveChildEntriesByEntryIdAndPartnerId($entry->getParentEntryId(), $entry->getPartnerId());
		foreach($childEntries as $childEntry)
			if ($childEntry->getId() != $entry->getId())
				$relatedEntries[] = $childEntry;
		return $relatedEntries;
	}

	public static function getVolumeMapContent($flavorAsset)
	{
		$flavorId = $flavorAsset->getId();
		$entryId = $flavorAsset->getEntryId();

		$packagerRetries = 3;
		$content = null;
		while ($packagerRetries && !$content)
		{
			$content = self::retrieveVolumeMapFromPackager($flavorAsset);
			$packagerRetries--;
		}
		if(!$content)
			throw new KalturaAPIException(KalturaErrors::RETRIEVE_VOLUME_MAP_FAILED);

		header("Content-Disposition: attachment; filename=".$entryId.'_'.$flavorId."_volumeMap.csv");
		return new kRendererString($content, 'text/csv');
	}

	private static function retrieveVolumeMapFromPackager($flavorAsset)
	{
		if ($flavorAsset->getEncryptionKey())
		{
			return self::retrieveMappedVolumeMapFromPackager($flavorAsset);
		}
		return self::retrieveLocalVolumeMapFromPackager($flavorAsset);
	}

	private static function retrieveMappedVolumeMapFromPackager($flavorAsset)
	{
		$packagerVolumeMapUrlPattern = kConf::get('packager_mapped_volume_map_url', 'local', null);
		if (!$packagerVolumeMapUrlPattern)
		{
			throw new KalturaAPIException(KalturaErrors::VOLUME_MAP_NOT_CONFIGURED);
		}

		$entry = entryPeer::retrieveByPK($flavorAsset->getEntryId());
		if (!$entry)
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND);
		}

		$volumeMapUrl = self::buildVolumeMapPath($entry, $flavorAsset);

		$content = self::curlVolumeMapUrl($volumeMapUrl, $packagerVolumeMapUrlPattern);
		if(!$content)
		{
			return false;
		}

		return $content;
	}

	private static function retrieveLocalVolumeMapFromPackager($flavorAsset)
	{
		$packagerVolumeMapUrlPattern = kConf::get('packager_local_volume_map_url', 'local', null);
		if (!$packagerVolumeMapUrlPattern)
			throw new KalturaAPIException(KalturaErrors::VOLUME_MAP_NOT_CONFIGURED);

		$fileSyncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		$entry_data_path = kFileSyncUtils::getRelativeFilePathForKey($fileSyncKey);
		$entry_data_path = ltrim($entry_data_path, "/");
		if (!$entry_data_path)
			return null;

		$content = self::curlVolumeMapUrl($entry_data_path, $packagerVolumeMapUrlPattern);
		if(!$content)
			return false;

		return $content;
	}

	private static function curlVolumeMapUrl($url, $packagerVolumeMapUrlPattern)
	{
		$packagerVolumeMapUrl = str_replace(array("{url}"), array($url), $packagerVolumeMapUrlPattern);
		kFile::closeDbConnections();
		$content = KCurlWrapper::getDataFromFile($packagerVolumeMapUrl, null, null, true);
		return $content;
	}

	public static function addTrackEntryInfo(entry $entry,$message)
	{
		$trackEntry = new TrackEntry();
		$trackEntry->setEntryId($entry->getId());
		$trackEntry->setDescription($message);
		TrackEntry::addTrackEntry($trackEntry);
	}

	private static function buildVolumeMapPath($entry, $flavorAsset)
	{
		$partnerId = $flavorAsset->getPartnerId();
		$subpId = $entry->getSubpId();
		$partnerPath = myPartnerUtils::getUrlForPartner($partnerId, $subpId);
		$entryVersion = $entry->getVersion();

		$url = "$partnerPath/serveFlavor/entryId/".$entry->getId();
		$url .= ($entryVersion ? "/v/$entryVersion" : '');
		$url .= "/flavorId/".$flavorAsset->getId();
		$url .= self::MP4_FILENAME_PARAMETER;
		return $url;
	}

	public static function verifyEntryType($entry)
	{
		$blockedTypes = array('KalturaPlaylist');
		foreach ($blockedTypes as $type)
		{
			if ($entry instanceof $type)
			{
				KalturaLog::debug("Entry type [$type] is not allowed");
				//throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_TYPE, $type);
			}
		}
	}

	public static function verifyThumbSrcExist($entry, $destThumbParams)
	{
		$srcAsset = kBusinessPreConvertDL::getSourceAssetForGenerateThumbnail(null, $destThumbParams->getSourceParamsId(), $entry->getId());
		if (is_null($srcAsset))
		{
			throw new KalturaAPIException(KalturaErrors::NO_FLAVORS_FOUND);
		}
		$srcSyncKey = $srcAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($srcSyncKey, true, false);
		if ($fileSync && !$local)
		{
			$fileSyncDc = $fileSync->getDc();
			if (in_array($fileSyncDc, kDataCenterMgr::getDcIds()))
			{
				KalturaLog::info("Source file wasn't found on current DC. Dumping the request to DC ID [$fileSyncDc]");
				return kFileUtils::dumpApiRequest(kDataCenterMgr::getRemoteDcExternalUrlByDcId($fileSyncDc), true);
			}
		}
	}

	public static function validateObjectContent($dbObject)
	{
		$syncKey = null;
		if($dbObject instanceof thumbAsset)
		{
			$syncKey = $dbObject->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		}
		else if (($dbObject instanceof entry) && ($dbObject->getMediaType() == KalturaMediaType::IMAGE))
		{
			$syncKey = $dbObject->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
		}

		if($syncKey)
		{
			$filePath = kAssetUtils::getLocalImagePath($syncKey);
			$validContent = myThumbUtils::validateImageContent($filePath);
			if(!$validContent)
			{
				throw new Exception ("content contains potential security risks");
			}
		}

	}

	public static function shouldValidateLocal()
	{
		//if multi request of more than one api call
		return  (kCurrentContext::$multiRequest_index <= 1);
	}

}
