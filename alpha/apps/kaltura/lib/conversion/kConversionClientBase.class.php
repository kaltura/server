<?php
/**
 * Will be incharge of 
 * 1. envoking one of the kConversionServers by setting kConversionCommand in the correct place 
 * 	on disk in a specific directory or in the DB to be fetched by a direct query or dedicated service (phase 2).
 * 2. fetching the kConversionResult from the server (depending on the server's command-result mechanism).
 * Each Client can be triggered by different events in the system and update status of objects accordingly.
 */
abstract class kConversionClientBase extends myBatchBase
{
	public $in_path ;//, $out_path;
	protected $server_cmd_path , $server_res_path ;
	protected $commercial_server_cmd_path; 
	protected $client_id;
	
	protected $mode ; 
	
	/**
	 * @var kConversionCommand
	 */
	protected $conv_cmd;


	public function kConversionClientBase ( $script_name ,  $in_path , $server_cmd_path , $server_res_path , $commercial_server_cmd_path = null , $mode = 3 )
	{
		$this->script_name = $script_name;
		if ( $script_name )	$this->register( $script_name , $in_path , $server_res_path , $mode );
		
		$this->in_path = realpath($in_path);
//		$this->out_path = realpath($out_path);
		$this->server_cmd_path = realpath($server_cmd_path);	
		$this->server_res_path = realpath($server_res_path);
		$this->commercial_server_cmd_path = realpath ( $commercial_server_cmd_path );
		TRACE ( "------------------- kConversionClient [$mode]----------------------");
		TRACE ( "--- in_path: [" . $this->in_path . "] ---" );
		TRACE ( "--- server_cmd_path: [" . $this->server_cmd_path . "] ---" );
		TRACE ( "--- server_res_path: [" . $this->server_res_path . "] ---" );
		TRACE ( "--- commercial_server_cmd_path: [" . $this->commercial_server_cmd_path . "] ---" );
		
		$this->mode = $mode;
//echo "<br>".__METHOD__ .":[$in_path][$server_cmd_path][$server_res_path]<br>"; 		
//echo "<br>".__METHOD__ .":[$this->in_path][$this->server_cmd_path][$this->server_res_path]<br>";
	}

	public static function getBatchStatus( $args )	
	{	
//print_r ( $args );		
		$mode = $args[3];
		$batch_status = new batchStatus();
		$batch_status->batch_name = $args[0] ;
		$batch_status->addToPending( "Disk:" . $args[1] . "*" . kConversionHelper::INDICATOR_SUFFIX , $batch_status->getDiskStatsCount( $args[0] , $args[1] ,  "*" . kConversionHelper::INDICATOR_SUFFIX ) );
		
		// bellow - from the fact this is infact a kConversionClientBase
		$batch_status->addToInProc( "Disk:" . $args[1] . "*" . kConversionHelper::INPROC_SUFFIX , $batch_status->getDiskStatsCount( $args[0] , $args[1] ,  "*" . kConversionHelper::INPROC_SUFFIX ) );
		$batch_status->addToInProc( "Disk:" . $args[2] . "*" . kConversionHelper::INDICATOR_SUFFIX , $batch_status->getDiskStatsCount( $args[0] , $args[2] ,  "*" . kConversionHelper::INDICATOR_SUFFIX ) );
		$batch_status->addToInProc( "Disk:" . $args[2] . "*" . kConversionHelper::INPROC_SUFFIX , $batch_status->getDiskStatsCount( $args[0] , $args[2] ,  "*" . kConversionHelper::INPROC_SUFFIX ) );

		list ( $a, $batch_status->last_log_time  ) =  $batch_status->getLogData( $args[0] );
		 
		return $batch_status; 
	}
		
	public function createConversionCommandFromConverionProfileId( $source_file , $target_file , $conv_profile_id , $entry = null )
	{
		$conv_profile = ConversionProfilePeer::retrieveByPK ( $conv_profile_id );
		return $this->createConversionCommandFromConverionProfile ( $source_file , $target_file , $conv_profile , $entry );
	}
	
	// TODO - this will determine if flv + bypass transcoding...
	public function createConversionCommandFromConverionProfile ( $source_file , $target_file , $conv_profile , $entry = null  )
	{
		$conv_cmd = new kConversionCommand();
		$conv_cmd->source_file = $source_file;
		$conv_cmd->target_file = $target_file ;
		$conv_cmd->result_path = $this->server_res_path; // in the command itself - set the result path
		$conv_cmd->entry_id = $entry ? $entry->getId() : null ; // can be null - in this case it might be a conversion not related to a specific entry

		if ( $conv_profile == null )
		{
			throw new kConversionException ( "Cannot convert [$source_file] using a null ConversionProfile" );
		}
		
		TRACE ( "ConversionProfile: " . print_r ( $conv_profile , true ));
		
		$fallback_mode = array();
		$conv_params_list_from_db = $conv_profile->getConversionParams( $fallback_mode );
		
		TRACE ( "ConversionParams chosen by fallback_mode [" . print_r ( $fallback_mode, true ) . "]" );
		
		if ( ! $conv_params_list_from_db || count ( $conv_params_list_from_db ) == 0 )
		{
			throw new kConversionException( "ConversionProfile [" .$conv_profile->getId() . "] has no ConversionParams");
		}
		
		$conv_cmd->commercial_transcoder = $conv_profile->getCommercialTranscoder();
 
		$conv_params_list = array ( );
		foreach ( $conv_params_list_from_db as $conv_param_from_db )
		{
			if ( ! $conv_param_from_db->getEnabled() ) 
			{
				continue;
			}
			
			// TODO - for now override properties from the ConvProf over the ConvParams...
			// width , height & aspect ratio.
			// wherever we'll have more properties to override, we should use a ConvParams object for the profile and merge the 2 objects 
			// copy the relevan parameters to the kConversionParams from the ConversioParams 
//			$conv_param_from_db  = new ConversionParams; 
			$conv_params = new kConversionParams();
			$conv_params->enable = $conv_param_from_db->getEnabled();
			if ( $entry )
			{
				$conv_params->audio = $conv_param_from_db->getAudio();
				$conv_params->video = $entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_VIDEO ;  // expect video only when a video type
			}
			else
			{
				$conv_params->audio = $conv_param_from_db->getAudio();
				$conv_params->video = $conv_param_from_db->getVideo();
			}
			
			$conv_params->width = $conv_param_from_db->getName();
			$conv_params->width = self::choose ( $conv_profile->getWidth() , $conv_param_from_db->getWidth() );
			$conv_params->height = self::choose ( $conv_profile->getHeight() , $conv_param_from_db->getHeight() );
			$conv_params->aspect_ratio = self::choose ( $conv_profile->getAspectRatio() , $conv_param_from_db->getAspectRatio() );
			$conv_params->gop_size = $conv_param_from_db->getGopSize();
			$conv_params->bitrate = $conv_param_from_db->getBitrate();
			$conv_params->qscale = $conv_param_from_db->getQscale();
			$conv_params->file_suffix = $conv_param_from_db->getFileSuffix();
			$conv_params->ffmpeg_params = $conv_param_from_db->getFfmpegParams();
			$conv_params->mencoder_params = $conv_param_from_db->getMencoderParams();
			$conv_params->flix_params = $conv_param_from_db->getFlixParams();
			$conv_params->comercial_transcoder = $conv_param_from_db->getCommercialTranscoder(); // is not really used today per ConvParams
			$conv_params->framerate = $conv_param_from_db->getFramerate();
			$conv_params->audio_bitrate = $conv_param_from_db->getAudioBitrate();
			$conv_params->audio_sampling_rate = $conv_param_from_db->getAudioSamplingRate();
			$conv_params->audio_channels = $conv_param_from_db->getAudioChannels();			
			// TODO - move this to the server, fillConversionParams requires ffmpeg to determine the dimensions of the video 
			// for ascpet ration 
//			kConversionHelper::fillConversionParams ( $source_file , $conv_params );
			$conv_params_list[] = $conv_params;
		}
		if($conv_profile->getPartnerId() == 38050 || $conv_profile->getPartnerId() == 27121)
		{
			$conv_cmd->forceOn2 = true;
		}		
		$conv_cmd->conversion_params_list = $conv_params_list;
		$conv_cmd->log_file = $conv_cmd->target_file . ".log";
		
		$this->conv_cmd = $conv_cmd;

		return $conv_cmd;
	}
	
	protected function saveConversionCommand ()
	{
		if( ! $this->conv_cmd )
		{
			throw new kConversionException ( "ConversionCommand not yet created" );
		}
		
		// depending on the transcoder - decide on the server's path
		$server_path = $this->server_cmd_path;
		if (  $this->conv_cmd->commercial_transcoder && $this->commercial_server_cmd_path )
			$server_path =  $this->commercial_server_cmd_path;
		
		$cmd_file = $server_path . "/" . basename( $this->conv_cmd->source_file );
		
		$this->conv_cmd->toFile( $cmd_file , true ) ;	
		return 	$cmd_file;
	}
	
	
	protected function createConversionInDb ( $entry_id , $full_file_name , $conv_cmd = null )
	{
		try
		{
			$conversion = new conversion();
			$entry_id = self::getEntryIdFromFileName ( $full_file_name );
			$conversion->setEntryId ( $entry_id );
			$conversion->setInFileName( $full_file_name );
			$conversion->setInFileExt( pathinfo ($full_file_name, PATHINFO_EXTENSION ) );
			$conversion->setInFileSize( filesize( $full_file_name ) );
			$conversion->setStatus( conversion::CONVERSION_STATUS_PRECONVERT );
			if ( $conv_cmd )
			{
				// TODO - find a better way to serialize the params !
				$conversion->setConversionParams( print_r ( $conv_cmd->conversion_params_list , true ) );
			}
			$conversion->save();
		}
		catch ( Exception $ex )
		{
			// Do NOT fail the actual conversion
			TRACE ( "Problem reporting conversion details to DB (part I) " . $ex->getTraceAsString() );
		}
	}
	
	protected function updateConversionInDb ( entry $entry , kConversionResult $conv_res )
	{
		try
		{
			$c = new Criteria();
			$entry_id = $entry->getId();
			$c->add ( conversionPeer::ENTRY_ID , $entry_id );
			// theoretically there can be more than one entry - fetch the last one which is in a non-complet status
			// assuming only one can be in such a status per entry
			$c->add ( conversionPeer::STATUS , conversion::CONVERSION_STATUS_COMPLETED , Criteria::NOT_EQUAL );
			$c->addDescendingOrderByColumn(  conversionPeer::ID );
			$conversion = conversionPeer::doSelectOne( $c );
			if ( $conversion )
			{
				$end = time();
				//$tParsedTime = strtotime($sGMTMySqlString . " GMT");
				$start =  $conversion->getCreatedAt( null );
				//$start = (int)strtotime($raw_start . " GMT");
				$conversion->setTotalProcessTime( ( $end - $start ) );
				
				$info = $conv_res->getResultInfo();
				if ( $info )
				{
					$params = "";
					$time = 0;
					foreach ( $info as $info_for_conversion )
					{
						$params .= "[" . $info_for_conversion->engine . "] : " . $info_for_conversion->conv_params_name . " | " . $info_for_conversion->conv_str . " | ";
						if (is_numeric(  $info_for_conversion->duration) ) $time += $info_for_conversion->duration  ; // increment the duration
					}  
					
					list ( $name1 , $size1 ) = $this->getNameAndSizeFromInfo ( $info , 0 );
					@list ( $name2 , $size2 ) = $this->getNameAndSizeFromInfo ( $info , 1 );
					$conversion->setOutFileName( $name1 );
					$conversion->setOutFileSize( $size1);
					$conversion->setOutFileName2( $name2 );
					$conversion->setOutFileSize2( $size2 );
					$conversion->setConversionParams( $params );
					$conversion->setConversionTime( $time );
				}
				if ( $conv_res->status_ok == true )
					$conversion->setStatus ( conversion::CONVERSION_STATUS_COMPLETED );
				else
					$conversion->setStatus ( conversion::CONVERSION_STATUS_ERROR );
				$conversion->save();
			}
			else
			{
				TRACE ( "Cannot find conversion details for entry $entry_id" );
			}
		}
		catch ( Exception $ex )
		{
			TRACE ( "Error reporting conversion details to DB (part II) " . $ex->getTraceAsString() );
		}		
	}
	
	
	protected function getNameAndSizeFromInfo ( $info , $index )
	{
		$result_info = @$info[$index];
		if ( $result_info )
		{
			$file = $result_info->target;
			$size = @filesize( $file );
			if ( $size < 100 ) 
			{
				sleep ( 1 );
				$size = @filesize( $file ); // sometimes - because different threads create the result - it's worth waiting a seconds to synch stuff
			}
			return array ( basename( $file ) , $size ); 	
		}
		return array ( null , null );
	}
	
	
	// will return a full file_name 
	// monior the in_path 
	// the files will be real data files to conver 
 	protected function getFileToConvert(  $write_to_log = true )
	{
		return kConversionHelper::getExclusiveFile( $this->in_path , $this->client_id , $write_to_log );
	}

	protected function setFileToReConvert ( $original_file_path , $file_name )
	{
		return kConversionHelper::createFileIndicator ( $original_file_path );
	}
	
	// monitor the res_path fro mthe server
	// the files will be of type kConversionResult
	protected function getFileFromConvertion (  $write_to_log = true )
	{
		return kConversionHelper::getExclusiveFile( $this->server_res_path , $this->client_id , $write_to_log  );
	}

	protected function removeInProc ( $in_proc )
	{
		kConversionHelper::removeInProc( $in_proc );
	}
	
	protected static function getEntryIdFromFileName ( $file_name )
	{
		return kFile::getFileNameNoExtension( $file_name );
	}
	
	protected static function getArchiveDir ()
	{
		return myContentStorage::getFSContentRootPath (). "/archive/data/";		
	}
	
	protected function archiveFile ( $file_name  )
	{
		TRACE ( "Archiving file [" . $file_name . "]" );
		$id = self::getEntryIdFromFileName ( $file_name );
		$entry = entryPeer::retrieveByPKNoFilter($id);
		$entry->setArchiveExtension(pathinfo ( $file_name , PATHINFO_EXTENSION ));
		$sync_key = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ARCHIVE);
		$file_sync = kFileSyncUtils::createSyncFileForKey($sync_key, false, false);
		$target = kFileSyncUtils::getLocalFilePathForKey($sync_key, false);
		TRACE ( "Archiving file [" . $file_name . "] to [" . $target . "]}"  );
		// MOVE - there is no need to copy because we the ConvCommand will include the file path anyway
		if ( $file_name == $target )
		{
			TRACE ( "File [$file_name] already archived" );
			return $file_sync;
		}
		
		// there is a file in the archive but the current file does not exist  
		if ( ! file_exists ( $file_name ) && file_exists( $target ) ) 
			return $file_sync;
			
		if ( file_exists( $target ) && ( filesize( $target ) == filesize ( $file_name ) ) )
		{
			return $file_sync;
		}
		else
		{
			// move to archive and override if exists
			myContentStorage::moveFile(  $file_name  ,  $target , true , false );
		}
		
		kFileSyncUtils::markLocalFileSyncAsReady($sync_key);
		
		return $file_sync;
	}
	
	public static function createFilePath ( $base_path , $file_name )
	{
		$id = self::getEntryIdFromFileName ( $file_name );
		// create a new path with the file name
		$entry = entryPeer::retrieveByPK( $id );
		if ( $entry )
		{
			TRACE  ("Found entry for file_name [$file_name] -> entry_id [$id]");
			$int_id = $entry->getIntId();
			$path_name = myContentStorage::dirForId ( $int_id , $id ). "." . pathinfo ( $file_name , PATHINFO_EXTENSION );	
		}
		else
		{
			TRACE  ("Did NOT find entry for file_name [$file_name] -> entry_id [$id]");
			$path_name = "AZ/" . pathinfo ( $file_name , PATHINFO_BASENAME );
			
		}

		// make sure the separator exists between the 2 paths
//		if ( ! kString::endsWith( $base_path , "/" ) ) $base_path .= "/";
		kFile::fullMkdir( $base_path . "/" . $path_name );
		return  $base_path . "/" . $path_name ;
	}	
	
	
	protected function createFlvWrappersForTargets ( kConversionResult $conv_res )
	{
		if ( ! $conv_res ) return;
		$res_info_list = $conv_res->result_info;
		if ( ! $res_info_list ) return;
		foreach ( $res_info_list as $res_info )
		{
			if ( $res_info->res )
			{
				// TODO -enough to return the helpers ??
				myFlvStaticHandler::createHelpers ( $res_info->target );
			}
		}
	}
	
	protected static function choose ( $opt1 , $opt2 )
	{
		
		if ( $opt1 ) return $opt1;
		return $opt2; 
	}
}
?>