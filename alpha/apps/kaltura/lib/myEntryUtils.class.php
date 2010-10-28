<?php

class myEntryUtils
{
	public static function updateThumbnailFromFile($dbEntry, $filePath, $fileSyncType = entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB)
	{
		$dbEntry->setThumbnail(".jpg"); // this will increase the thumbnail version
		$dbEntry->save();
		
		$fileSyncKey = $dbEntry->getSyncKey($fileSyncType);
		$fileSync = FileSync::createForFileSyncKey($fileSyncKey);
		kFileSyncUtils::file_put_contents($fileSyncKey, file_get_contents($filePath));
		
		$wrapper = objectWrapperBase::getWrapperClass($dbEntry);
		$wrapper->removeFromCache("entry", $dbEntry->getId());
		
		myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE_THUMBNAIL, $dbEntry);
		
		$mediaEntry = KalturaEntryFactory::getInstanceByType($dbEntry->getType());
		$mediaEntry->fromObject($dbEntry);
		
		self::disableAutoThumbnailCreation($dbEntry->getId());
		
		return $mediaEntry;
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

	// same as the deepCopy only modifies the content of the XML according to the $entry_map
	// all relevant entries can be fetched from the entry_cache rather than hit the DB
	// does not return any value - work on the $target_show_entry  which is assumed to already exist
	// DEPRECATED NO NEED TO CHECK AGAIN - ONLY CALLED FROM SYSTEM / CLONEKSHOW
	public static function old_deepCloneShowEntry ( entry $source_show_entry , entry $target_show_entry , array $entry_map , array $entry_cache )
	{
		$target_show_entry->setComments ( 0 );
		$target_show_entry->setTotalRank ( 0 );
		$target_show_entry->setRank ( 0 );
		$target_show_entry->setViews ( 0 );
		$target_show_entry->setVotes ( 0 );
		$target_show_entry->setFavorites ( 0 );
		$target_show_entry->save();

		if ( myContentStorage::isTemplate($source_entry->getData()))
		{
			if ($echo)
				echo ( "source thumbnail same as target. skipping file: " . $content . $source_thumbnail_path . "\n");
		}
		else
		{
			if ($echo)
				echo ( "Copying file: " . $content . $source_thumbnail_path . " -> " .  $content . $target_thumbnail_path ."\n");

			$sourceThumbFileKey = $source_show_entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB);
			if(kFileSyncUtils::file_exists($sourceThumbFileKey))
			{
				$targetThumbFileKey = $target_show_entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB);
				kFileSyncUtils::softCopy($sourceThumbFileKey, $targetThumbFileKey);
			}
			//myContentStorage::moveFile( $content . $source_thumbnail_path , $content . $target_thumbnail_path , false , true );
		}

		if ( myContentStorage::isTemplate($source_entry->getData()))
		{
			if ($echo)
				echo ( "source same as target. skipping file: " . $content . $source_data_path . "\n");
		}
		else
		{
			// fix metadata

			$source_show_entry_content = kFileSyncUtils::file_get_contents( $sourceDataFileKey  );
			// fix the ShowVersion
			$source_show_version = $source_show_entry->getData();
			$target_show_version = $target_show_entry->getData();
			// <ShowVersion>100016</ShowVersion>
			$source_show_entry_content = str_replace( "<ShowVersion>$source_show_version</ShowVersion>" , "<ShowVersion>$target_show_version</ShowVersion>" , $source_show_entry_content );

			// now replace entries
			foreach ( $entry_map as $source_entry_id => $target_entry_id )
			{
				$source_entry = $entry_cache [$source_entry_id];
				$target_entry = $entry_cache [$target_entry_id];
				$source_file_name = $source_entry->getDataPath(); // replaced__getDataPath
				$target_file_name = $target_entry->getDataPath(); // replaced__getDataPath

				// k_id="11758"
				$source_show_entry_content = str_replace( "k_id=\"$source_entry_id\"" , "k_id=\"$target_entry_id\"" , $source_show_entry_content );
				// file_name="/content/entry/data/0/11/11787_100000.jpg"
				//$source_show_entry_content = str_replace( "file_name=\"$source_file_name\"" , "file_name=\"$target_file_name\"" , $source_show_entry_content );
				// a more general search - will fix file_name= & url=
				$source_show_entry_content = str_replace( "$source_file_name\"" , "$target_file_name\"" , $source_show_entry_content );
			}
			$sourceDataFileKey = $source_show_entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
			$targetDataFileKey = $target_show_entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
			kFileSyncUtils::file_put_contents($targetDataFileKey, $source_show_entry_content);
		}

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

		$wrapper = objectWrapperBase::getWrapperClass( $target , objectWrapperBase::DETAIL_LEVEL_REGULAR );
		
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
		$sourceFlavorAssets = flavorAssetPeer::retrieveByEntryId($source_entry_id);
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

		myContentStorage::fullMkdir($path);

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
	public static function deleteEntry ( entry $entry , $partner_id = null )
	{
		if ( $entry->getStatus() == entry::ENTRY_STATUS_DELETED || $entry->getStatus() == entry::ENTRY_STATUS_BLOCKED  )
			return ; // don't do this twice !

		KalturaLog::log("myEntryUtils::delete Entry [" . $entry->getId() . "] Partner [" . $entry->getPartnerId() . "]");
		
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
				
			case entry::ENTRY_MEDIA_TYPE_LIVE_STREAM_FLASH:
			case entry::ENTRY_MEDIA_TYPE_LIVE_STREAM_WINDOWS_MEDIA:
			case entry::ENTRY_MEDIA_TYPE_LIVE_STREAM_REAL_MEDIA:
			case entry::ENTRY_MEDIA_TYPE_LIVE_STREAM_QUICKTIME:
				kJobsManager::addProvisionDeleteJob(null, $entry);
				break;
				
			case entry::ENTRY_MEDIA_TYPE_SHOW:				
			default:
				$template_file = "&deleted_rc.xml";
				$need_to_fix_roughcut = false;
				break;
		}

		// in this case we'll need some batch job to fix all related roughcuts for this entry
		// use the batch_job mechanism to indicate there is a deleted entry to handle
		if ( $need_to_fix_roughcut )
		{
			BatchJob::createDeleteEntryJob ( $entry );
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
		
		$entry->setStatus ( entry::ENTRY_STATUS_DELETED ); 
		
		$entry->setCategories("");
		
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
		if ( $entry->getStatus() != entry::ENTRY_STATUS_DELETED )
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
		
		$entry->setStatusReady( true );
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
	
	/**
	 * Disable any automatic thumbnail creation by the conversion jobs
	 * 
	 * @param string $entryId
	 */
	protected static function disableAutoThumbnailCreation($entryId)
	{
		$convertProfileJobs = BatchJobPeer::retrieveByEntryIdAndType($entryId, BatchJob::BATCHJOB_TYPE_CONVERT_PROFILE);
		foreach($convertProfileJobs as $convertProfileJob)
		{
			$convertProfileJobData = $convertProfileJob->getData();
			if($convertProfileJobData instanceof kConvertProfileJobData && $convertProfileJobData->getCreateThumb())
			{
				$convertProfileJobData->setCreateThumb(false);
				$convertProfileJob->setData($convertProfileJobData);
				$convertProfileJob->save();
			}
		}
	}
	
	public static function createThumbnailFromEntry ( entry $entry , entry $source_entry, $time_offset, $flavorParamsId = null)
	{
		$media_type = $source_entry->getMediaType();
		
		// should capture thumbnail from video
		if ($media_type == entry::ENTRY_MEDIA_TYPE_VIDEO && $time_offset != -1)
		{
			$flavorAsset = null;
			if($flavorParamsId)
				$flavorAsset = flavorAssetPeer::retrieveByEntryIdAndFlavorParams($source_entry->getId(), $flavorParamsId);
				
			if(is_null($flavorAsset) || $flavorAsset->getStatus() != flavorAsset::FLAVOR_ASSET_STATUS_READY)
				$flavorAsset = flavorAssetPeer::retrieveOriginalByEntryId($source_entry->getId());
				
			if (is_null($flavorAsset))
				$flavorAsset = flavorAssetPeer::retrieveHighestBitrateByEntryId($source_entry->getId());
			
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
			$entry->save();
			
			// create new thumb file for entry
			$newThumbKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB);
			kFileSyncUtils::moveFromFile($thumbBigFullPath, $newThumbKey);
		}
		else if ($media_type == entry::ENTRY_MEDIA_TYPE_VIDEO && $time_offset == -1 ||
			$media_type == entry::ENTRY_MEDIA_TYPE_SHOW) // not time offset - copying existing thumb
		{
			$thumbBigFullKey = $source_entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB);
			if($media_type == entry::ENTRY_MEDIA_TYPE_SHOW &&
			   !kFileSyncUtils::getLocalFileSyncForKey($thumbBigFullKey, false))
			{
				return false;
			}

			$entry->setThumbnail ( ".jpg");
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
			$entry->save();
			// copy existing thumb
			$newThumbKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB);
			kFileSyncUtils::copyFromFile($thumb_path, $newThumbKey);
		}
		else
		{
			return false;
		}
		self::disableAutoThumbnailCreation($entry->getId());
		return true;
	}
	
	
	public static function resizeEntryImage ( entry $entry, $version , $width , $height , $type , $bgcolor ="ffffff" , $crop_provider=null, $quality = 0,
		$src_x = 0, $src_y = 0, $src_w = 0, $src_h = 0, $vid_sec = -1, $vid_slice = 0, $vid_slices = -1, $orig_image_path = null)
	{
		$contentPath = myContentStorage::getFSContentRootPath();
			
		$entry_status = $entry->getStatus();
		$tempThumbName = $entry->getId()."_{$width}_{$height}_{$type}_{$crop_provider}_{$bgcolor}_{$quality}_{$src_x}_{$src_y}_{$src_w}_{$src_h}_{$vid_sec}_{$vid_slice}_{$vid_slices}_{$entry_status}";
		
		$entryThumbFilename = ($entry->getThumbnail() ? $entry->getThumbnail() : "0.jpg");
		if ($entry->getStatus() != entry::ENTRY_STATUS_READY || @$entryThumbFilename[0] == '&')
			$tempThumbName .= "_NOCACHE_";
		
		// we remove the & from the template thumb otherwise getGeneralEntityPath will drop $tempThumbName from the final path
		$entryThumbFilename = str_replace("&", "", $entryThumbFilename);
		$basePath = myContentStorage::getGeneralEntityPath("entry/tempthumb", $entry->getIntId(), $tempThumbName, $entryThumbFilename , $version );
		$tempThumbPath = $contentPath.$basePath;
		
		$cachedTempThumbPath = myContentStorage::getFSCacheRootPath().$basePath;
		if (file_exists($cachedTempThumbPath))
		{
			header("X-Kaltura:cached-local-thumb-exists,".md5($cachedTempThumbPath));
			return $cachedTempThumbPath;
		}

		if (file_exists($tempThumbPath))
		{
			header("X-Kaltura:cached-thumb-exists,".md5($tempThumbPath));
			return $tempThumbPath;
		}
		
		if($orig_image_path === null || !file_exists($orig_image_path))
		{
			$sub_type = $entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_IMAGE ? entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA : entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB;
			$orig_image_key = $entry->getSyncKey($sub_type, $version);
			$orig_image_path = kFileSyncUtils::getReadyLocalFilePathForKey($orig_image_key);
		}
		
		
		// remark added so ffmpeg will try to load the thumbnail from the original source
		//if (!file_exists($orig_image_path))
		//	KExternalErrors::dieError(KExternalErrors::FILE_NOT_FOUND);
		
		// check a request for animated thumbs without a concrete vid_slice
		// in which case we'll create all the frames as one wide image
		$multi = $vid_slice == -1 && $vid_slices != -1;
		$count = $multi ? $vid_slices : 1;
		$im = null;
		if ($multi)
			$vid_slice = 0;  
			
		while($count--)
		{
			if (
				// need to create a thumb if either:
				// 1. entry is a video and a specific second was requested OR a slices were requested
				// 3. the actual thumbnail doesnt exist on disk
				($entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_VIDEO && ($vid_sec != -1 || $vid_slices != -1))
				||
				(!file_exists($orig_image_path))
				)
			{
				if ($vid_sec != -1) // a specific second was requested
				{
					$calc_vid_sec = min($vid_sec, floor($entry->getLengthInMsecs() / 1000));
				}
				else if ($vid_slices != -1) // need to create a thumbnail at a specific slice
				{
					$calc_vid_sec = floor($entry->getLengthInMsecs() / $vid_slices * min($vid_slice, $vid_slices) / 1000);
				}
				else if ($entry->getStatus() != entry::ENTRY_STATUS_READY && $entry->getLengthInMsecs() == 0) // when entry is not ready and we don't know its duration
				{
					$calc_vid_sec = ($entry->getPartner() && $entry->getPartner()->getDefThumbOffset()) ? $entry->getPartner()->getDefThumbOffset() : 3;
				}
				else // default thumbnail wasnt created yet
				{
					$calc_vid_sec = $entry->getBestThumbOffset();
				}
					
				$capturedThumbName = $entry->getId()."_sec_{$calc_vid_sec}";
				$capturedThumbPath = $contentPath.myContentStorage::getGeneralEntityPath("entry/tempthumb", $entry->getIntId(), $capturedThumbName, $entry->getThumbnail() , $version );
	
				$orig_image_path = $capturedThumbPath."temp_1.jpg";
	
				// if we already captured the frame at that second, dont recapture, just use the existing file
				if (!file_exists($orig_image_path))
				{
					// creating the thumbnail is a very heavy operation
					// prevent calling it in parallel for the same thubmnail for 5 minutes
					$cache = new myCache("thumb-processing", 5 * 60); // 5 minutes
					$processing = $cache->get($orig_image_path);
					if ($processing)
						KExternalErrors::dieError(KExternalErrors::PROCESSING_CAPTURE_THUMBNAIL);
						
					$cache->put($orig_image_path, true);
					
					$flavorAsset = flavorAssetPeer::retrieveOriginalReadyByEntryId($entry->getId());
					if(is_null($flavorAsset) || !($flavorAsset->hasTag(flavorParams::TAG_MBR) || $flavorAsset->hasTag(flavorParams::TAG_WEB)))
					{
						// try the best playable
						$flavorAsset = flavorAssetPeer::retrieveHighestBitrateByEntryId($entry->getId());
					}
					
					if (is_null($flavorAsset))
						KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);

					$flavorSyncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
					$entry_data_path = kFileSyncUtils::getReadyLocalFilePathForKey($flavorSyncKey);
					if(!$entry_data_path) // fileSync is not ready locally, throw exception
					{
						// since this is not really being processed on this server, and will probably cause redirect in thumbnailAction
						// remove from cache so later requests will still get redirected and will not fail on PROCESSING_CAPTURE_THUMBNAIL
						$cache->remove($orig_image_path);
						throw new kFileSyncException('no ready filesync on current DC', kFileSyncException::FILE_DOES_NOT_EXIST_ON_CURRENT_DC);
					}
					myFileConverter::autoCaptureFrame($entry_data_path, $capturedThumbPath."temp_", $calc_vid_sec, -1, -1);
					
					$cache->remove($orig_image_path);
				}
			}

			kFile::fullMkdir($tempThumbPath);
			if ($crop_provider)
			{
				$convertedImagePath = myFileConverter::convertImageUsingCropProvider($orig_image_path, $tempThumbPath, $width, $height, $type, $crop_provider, $bgcolor, true, $quality, $src_x, $src_y, $src_w, $src_h);
			}
			else
			{
				$convertedImagePath = myFileConverter::convertImage($orig_image_path, $tempThumbPath, $width, $height, $type, $bgcolor, true, $quality, $src_x, $src_y, $src_w, $src_h);
			}
			
			// die if resize operation failed
			if ($convertedImagePath === null)
					KExternalErrors::dieError(KExternalErrors::IMAGE_RESIZE_FAILED);
			
			if ($multi)
			{
				list($w, $h, $type, $attr, $srcIm) = myFileConverter::createImageByFile($tempThumbPath);
				if (!$im)
					$im = imagecreatetruecolor($w * $vid_slices, $h);
					
				imagecopy($im, $srcIm, $w * $vid_slice, 0, 0, 0, $w, $h);
				imagedestroy($srcIm);
					
				++$vid_slice;
			}
		}
		
		if ($multi)
		{
			imagejpeg($im, $tempThumbPath);
			imagedestroy($im);
		}		
		return $tempThumbPath;
	}
	
	//
	// sets the type and media_type of an entry according to the file extension
	// in case the media_type is entry::ENTRY_MEDIA_TYPE_AUTOMATIC we find the media_type from the extension
	// in case the type is entry::ENTRY_TYPE_AUTOMATIC we set the type according to the media_type found before
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
		if ($media_type == entry::ENTRY_MEDIA_TYPE_AUTOMATIC)
		{
			$media_type = myFileUploadService::getMediaTypeFromFileExt(pathinfo($entry_full_path, PATHINFO_EXTENSION));
			$entry->setMediaType($media_type);
		}
		
		// we'll set the type according to the media_type - either a media_clip or a document
		if ($entry->getType() == entry::ENTRY_TYPE_AUTOMATIC)
		{
			if ($media_type == entry::ENTRY_MEDIA_TYPE_IMAGE ||	$media_type == entry::ENTRY_MEDIA_TYPE_VIDEO ||
				$media_type == entry::ENTRY_MEDIA_TYPE_AUDIO)
				$entry->setType(entry::ENTRY_TYPE_MEDIACLIP);
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
		if ($entry->getStatus() == entry::ENTRY_STATUS_DELETED)
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
		
		$flavorAssets = flavorAssetPeer::retrieveByEntryId($entry_id);
		foreach($flavorAssets as $flavorAsset)
		{
			$entrySyncKeys[] = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$entrySyncKeys[] = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG);
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
	
	public static function copyEntry(entry $entry, Partner $toPartner = null)
 	{
 		KalturaLog::log("copyEntry - Copying entry [".$entry->getId()."] to partner [".$toPartner->getId()."]");
 		$newEntry = $entry->copy();
 		$newEntry->setIntId(null);
 		if ($toPartner instanceof Partner)
 		{
 			$newEntry->setPartnerId($toPartner->getId());
 			$newEntry->setSubpId($toPartner->getId() * 100);
			$newEntry->setAccessControlId($toPartner->getDefaultAccessControlId());
			
			$flavorParamsStr = $entry->getFlavorParamsIds();
			$flavorParams = explode(',', $flavorParamsStr);
			$newFlavorParams = array();
			foreach($flavorParams as $flavorParamsId)
			{
				$newFlavorParamsId = kObjectCopyHandler::getMappedId('flavorParams', $flavorParamsId);
				if(is_null($newFlavorParamsId))
					$newFlavorParamsId = $flavorParamsId;
					
				$newFlavorParams[] = $newFlavorParamsId;
			}
			$newEntry->setFlavorParamsIds(implode(',', $newFlavorParams));
 		}
 		
 		// copy the kuser (if the same puser id exists its kuser will be used) 
 		kuserPeer::setUseCriteriaFilter(false);
 		$kuser = $entry->getKuser();
 		$newKuser = kuserPeer::createKuserForPartner($newEntry->getPartnerId(), $kuser->getPuserId());
 		$newEntry->setKuserId($newKuser->getId());
 		kuserPeer::setUseCriteriaFilter(true);
 		
 		// copy the kshow
 		kshowPeer::setUseCriteriaFilter(false);
 		$kshow = $entry->getKshow();
 		if ($kshow)
 		{
 			$newKshow = $kshow->copy();
 			$newKshow->setIntId(null);
 			$newKshow->setPartnerId($toPartner->getId());
 			$newKshow->setSubpId($toPartner->getId() * 100);
 			$newKshow->setProducerId($newKuser->getId());
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
 		
 		// save the entry
 		$newEntry->save();
 		
 		// restore the original partner id in the default category criteria filter
		$defaultCategoryFilter->remove(categoryPeer::PARTNER_ID);
 		$defaultCategoryFilter->addAnd(categoryPeer::PARTNER_ID, $oldPartnerId);
 		
 		KalturaLog::log("copyEntry - New entry [".$newEntry->getId()."] was created");
		
		// for any type that does not require assets:
		$shouldCopyDataForNonClip = ($entry->getType() != entry::ENTRY_TYPE_MEDIACLIP);
		$shouldCopyDataForClip = false;
		// only images get their data copied
		if($entry->getType() == entry::ENTRY_TYPE_MEDIACLIP)
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
	 		$to = $newEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA); // replaced__getDataPath
	 		KalturaLog::log("copyEntriesByType - copying entry data [".$from."] to [".$to."]");
			kFileSyncUtils::softCopy($from, $to);
		}
		
		$ismFrom = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM);		
		if(kFileSyncUtils::fileSync_exists($ismFrom))
		{
			$ismTo = $newEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM);
			KalturaLog::log("copying entry ism [".$ismFrom."] to [".$ismTo."]");
			kFileSyncUtils::softCopy($ismFrom, $ismTo);
		}
		
		$ismcFrom = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC);		
		if(kFileSyncUtils::fileSync_exists($ismcFrom))
		{
			$ismcTo = $newEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC);
			KalturaLog::log("copying entry ism [".$ismcFrom."] to [".$ismcTo."]");
			kFileSyncUtils::softCopy($ismcFrom, $ismcTo);
		}
 		
		$from = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB); // replaced__getThumbnailPath
		$considerCopyThumb = true;
		// if entry is image - data is thumbnail, and it was copied
		if($entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_IMAGE) $considerCopyThumb = false;
		// if entry is not clip, and there is no file in both DCs - nothing to copy
		if($entry->getType() != entry::ENTRY_TYPE_MEDIACLIP && !kFileSyncUtils::file_exists($from, true)) $considerCopyThumb = false;
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
				$to = $newEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB); // replaced__getThumbnailPath
				KalturaLog::log("copyEntriesByType - copying entry thumbnail [".$from."] to [".$to."]");
				kFileSyncUtils::softCopy($from, $to);
			}
		}

		// added by Tan-Tan 12/01/2010 to support falvors copy
		$sourceFlavorAssets = flavorAssetPeer::retrieveByEntryId($entry->getId());
		foreach($sourceFlavorAssets as $sourceFlavorAsset)
			$sourceFlavorAsset->copyToEntry($newEntry->getId(), $newEntry->getPartnerId());
		
 	}
}
?>
