<?php
/**
 * will replace the previous natchConversioClient.
 * Use the kConverionProfile per entry to create akConversionCommand for the new server.
 * The kConversionResult will include data to set the status for the entry, create thumbnails and helpers for the flvWrapper
 * 
 * @package Core
 * @subpackage Conversion
 * @deprecated
 */
class kConversionClient extends kConversionClientBase 
{
	public function convert ()
	{
		SET_CONTEXT ( "CC {$this->mode}");

		list ( $sleep_between_cycles ,
		$number_of_times_to_skip_writing_sleeping ) = self::getSleepParams( 'app_conversion_client_' );

		self::initDb();

		$temp_count = 0;
		while ( true )
		{
			self::exitIfDone();
			try
			{
				$this->sentToCenversion ( $temp_count == 0 );
				self::succeeded();				
			}
			catch ( Exception $ex )
			{
				// try to recover !!
				echo ( $ex );

				self::initDb( true );
				self::failed();
			}

			try
			{
				$this->pollConverted ( $temp_count == 0 );
			}
			catch ( Exception $ex )
			{
				// TODO - log exceptions !!!
				// try to recover !!
				echo ( $ex );

				self::initDb( true );
				self::failed();
			}

			if ( $temp_count == 0 )
			{
				TRACE ( "Ended conversion. sleeping for a while (" . $sleep_between_cycles .
				" seconds). Will write to the log in (" . ( $sleep_between_cycles * $number_of_times_to_skip_writing_sleeping ) . ") seconds" );
			}

			$temp_count++;
			if ($temp_count >= $number_of_times_to_skip_writing_sleeping ) $temp_count = 0;

			sleep ( $sleep_between_cycles );
		}
	}	
	
	private function sentToCenversion (  $write_to_log = true )
	{
		try
		{
			$debug = array( "before getFileToConvert");
				list ( $before_archiving_file_path , $file_name , $in_proc ) = $this->getFileToConvert( $write_to_log );
			$debug [] =  "after getFileToConvert [$before_archiving_file_path] [$file_name]";
				if ( ! $before_archiving_file_path )
				{
					return;
				}
				// TODO - check if this file failed too many times ... 
				//if ( !$this->shouldHandleFile ( $file_name ) ) 
		
				$entry_id = self::getEntryIdFromFileName ( $file_name );
			$debug [] = "entry_id [$entry_id]";
				// we have to retrieve the path of the entry - do so by setting the data to the file path (now rather than at the end)
				$entry = entryPeer::retrieveByPK( $entry_id );
				$conv_profile = myPartnerUtils::getConversionProfileForEntry ( $entry_id  );
				$debug [] = "conversion profile of class [" . get_class ( $conv_profile ) . "]" ;
			
				if ( !$entry )
				{
					TRACE("entry id [$entry_id] not found!");
					return;
				}
				
				// the conversion target should be the entry's dataPath
				$flv_file_name = kConversionHelper::flvFileName ( $before_archiving_file_path );
		$debug [] = "flv_file_name [$flv_file_name]";	
				$entry->setData ( null );						
				$entry->setData( $flv_file_name ); // we assume the output will be of type FLV
				$entry->save();
				
				$archive_file_sync = $this->archiveFile ( $before_archiving_file_path );
				$archived_file_path = $archive_file_sync->getFullPath();
		$debug [] = "archived_file_path [$archived_file_path]";
			
				if ( $conv_profile->getBypassFlv() && kConversionHelper::isFlv( $archived_file_path ) )
				{
					$conv_cmd = $this->createConversionCommandFromConverionProfile( $archived_file_path , $archived_file_path , $conv_profile , $entry );
			
			$debug [] = "before createConversionInDb[$entry_id] [$archived_file_path]";
					// first update the DB
					$this->createConversionInDb( $entry_id , $archived_file_path , $conv_cmd );
					
					
					// TODO - check if there is a set of convParams for this FLV profile and manye some conversion should be done
					// for the edit version ??
					TRACE ( "Bypassing conversion for entry_id [$entry_id] file [$file_name]" );
					$conv_res = new kConversionResult( $conv_cmd );
					
					$conv_res_info =  new kConvResInfo();
					$conv_res_info->target = $archived_file_path;
					$start = microtime(true);

					// FileSync - soft copy
					$archived_sync_key = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ARCHIVE);
					$data_sync_key = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
					kFileSyncUtils::softCopy($archived_sync_key, $data_sync_key);
		
					$end = microtime(true);
					$conv_res_info->duration = ( $end - $start );
					$conv_res_info->conv_str = "NO CONVERT";
					
					$conv_res->appendResInfo( $conv_res_info );
					$this->updateConvertedEntry( true , $entry , $conv_res );
					
					$this->removeInProc( $in_proc );
					
					return;
				}
				
				// FileSync - create file sync for the future place of the converted data
				$data_sync_key = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
				kFileSyncUtils::createSyncFileForKey($data_sync_key, false, false);
				$full_target_path = kFileSyncUtils::getLocalFilePathForKey($data_sync_key, true);
				
				if ($conv_profile->getProfileTypeSuffix() == "edit")
				{
					// FileSync - create file sync for the future place of the converted data in edit flavor
					$data_edit_sync_key = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA_EDIT);
					kFileSyncUtils::createSyncFileForKey($data_edit_sync_key, false, false);
				}
				

			$debug [] = "full_target_path [$full_target_path]";		
				$conv_cmd = $this->createConversionCommandFromConverionProfile( $archived_file_path , $full_target_path , $conv_profile , $entry );
			
			$debug [] = "before createConversionInDb[$entry_id] [$archived_file_path]";
			
				// first update the DB
				$this->createConversionInDb( $entry_id , $archived_file_path , $conv_cmd );
				
				TRACE ( "Setting ConversionCommand for file [$file_name]\n" . print_r ($conv_cmd , true ) );	
				
			$debug [] = "before saveConversionCommand";			
				// then save the conversion command
				$cmd_file_path = $this->saveConversionCommand();
				$this->removeInProc( $in_proc );
				TRACE ( "Set ConversionCommand for file [$file_name] in [$cmd_file_path]" );
			$debug [] = "end";
		}
		catch ( kConversionException $kcoe )
		{
			$this->removeInProc( $in_proc );
			TRACE ( "Error:\n" . $kcoe->getMessage() . "\n" . $kcoe->getTraceAsString(). "\n" . print_r ( $debug ) );
			// update the entry with the error sttus and the error message to the conversion result
			$conv_res = new kConversionResult( $conv_cmd );
			$conv_res->appendResult( $kcoe->getMessage() );
			$this->updateConvertedEntry( false , $entry , $conv_res );
			
		}
		catch ( Exception $ex )
		{
			$this->removeInProc( $in_proc );
			TRACE ( "Error:\n" . $ex->getMessage() . "\n" . $ex->getTraceAsString(). "\n" . print_r ( $debug ) );
			// if this failed for some unknown reason - set it for reconversion 
			$indicator = $this->setFileToReConvert ( $before_archiving_file_path , $file_name );
			TRACE ( "... will reconvert [" . print_r ( $indicator , true ) . "]" );
			throw $ex; 
		}
	}
	
	
	private  function pollConverted (  $write_to_log = true )
	{
		list ( $full_conv_res_path , $file_name , $in_proc ) = $this->getFileFromConvertion( $write_to_log );
		if ( ! $full_conv_res_path )
		{
			return;
		}
		$entry_id = self::getEntryIdFromFileName ( $file_name );

		TRACE ( "Updating entry [" . $entry_id ."]" );
		entryPeer::setUseCriteriaFilter( false ); // update the entry even if it's deleted
//		$c = new Criteria();
//		$c->add(entryPeer::ID, $entry_id);
//		$entry = entryPeer::doSelectOne( $c );
		$entry = entryPeer::retrieveByPK( $entry_id );

		// fetch file from the conversion server and store it in the correct place - content/entry/data/...
		// using the ame logic as in contribute/insertEntryAction & myContentStorage...

		$this->removeInProc( $in_proc );
		if ( $entry == NULL )
		{
			// TODO - entry does not exist in DB - what to do ?
			// move file to some directory 
			return ;
		}

		// the target of the entry was already set at time of sentToCenversion 

		$conv_res = kConversionResult::fromFile( $full_conv_res_path );

		TRACE ( print_r ( $conv_res , true ) ) ;

		// sleep a while for synching data on the disk
		sleep ( 3 );
		$this->markFileSyncAsReady($entry);

		$this->updateConvertedEntry ( $conv_res->status_ok , $entry, $conv_res );
		
		// flag a success to break the row of faliures (is any)
		self::succeeded();
	}
	
	
	private function updateConvertedEntry ( $ok , $entry , kConversionResult $conv_res )
	{
		$file_before_conversion =$conv_res->conv_cmd->source_file;
		$file_after_conversion =$conv_res->conv_cmd->target_file; // TODO -get all targets
		if ( $ok == true )
		{
			// TODO - write all targets not only primary one
			TRACE ( "File [$file_before_conversion] converted OK to [$file_after_conversion]" );
			try 
			{
				// TODO - do we need to create the helpers eagerly ??
//				$this->createFlvWrappersForTargets( $conv_res );
			}
			catch ( Exception $ex)
			{
				TRACE ( "Error while creating helper files for [$file_after_conversion]" );
			} 
			$entry->setStatusReady();
		}
		else
		{
			TRACE ( "Problem converting file [$file_before_conversion]" );
			$entry->setStatus ( entryStatus::ERROR_CONVERTING );
		}	

		$this->updateConversionInDb( $entry , $conv_res );

		// loop until the file is really ready - sometimes the size of the file or the mtime is wrong
		for ( $i=0;$i<15 ;$i++)
		{
			clearstatcache  ( );
			
			if ( !file_exists( $file_after_conversion ) || filesize ($file_after_conversion ) == 0 )
			{
//				TRACE ( "Entry id [" . $entry->getId() . "] printing file stats: " . print_r( stat ($file_after_conversion ) , true ) );
				TRACE ( "Entry id [" . $entry->getId() . "]. no such file [$file_after_conversion]. Sleeping for 1 second for the [$i] time." );
				sleep ( 2 ) ;
			}
			else
			{
				break;
			}
		}
		
		TRACE ( "Entry id [" . $entry->getId() . "] setting duration" );
		$entry->setLengthInMsecs ( kConversionHelper::getFlvDuration ( $file_after_conversion ) );
		TRACE ( "Entry id [" . $entry->getId() . "] duration [" . $entry->getLengthInMsecs() . "]" );

		// how could it be otherwise ??
		if ( $entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_VIDEO )
		{
			// TODO - move out of this function if the partner is required for more configurations 
			$partner = PartnerPeer::retrieveByPK( $entry->getPartnerId() );

			// TODO - make sure the width & height of the target are part of the kConversionReulst
	//			if ( $conversion_info ) $entry->setDimensions ( $conversion_info->video_width , $conversion_info->video_height );
			$offset = $entry->getBestThumbOffset( $partner->getDefThumbOffset() );
			TRACE ( "Entry id [" . $entry->getId() . "] Thumb offset: [$offset]" );
			// first create the thumb for the entry
			
			myEntryUtils::createThumbnailFromEntry ( $entry , $entry , $offset );
			// 	then make sure it will propage to the roughcut if needed
			myEntryUtils::createRoughcutThumbnailFromEntry ( $entry , false );

			$entry->updateVideoDimensions();
			TRACE ( "Entry id [" . $entry->getId() . "] dimensions: [" . $entry->getWidth() . "x" . $entry->getHeight() . "]" );
			
		}
		
		// send notification - regardless its status
		myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE , $entry );	

		
		$entry->save();
				
	}
	
	private function markFileSyncAsReady(entry $entry)
	{
		// FileSync - mark file sync as ready for converted data file
		$fileSyncKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
		kFileSyncUtils::markLocalFileSyncAsReady($fileSyncKey);
					
		// FileSync - mark file sync as ready for converted data file in edit flavor
		$fileSyncKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA_EDIT);
		kFileSyncUtils::markLocalFileSyncAsReady($fileSyncKey, $strict = false); // do not be strict, not all entries have EDIT file
	}
}
?>