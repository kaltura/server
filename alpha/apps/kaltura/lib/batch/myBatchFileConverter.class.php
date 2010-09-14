<?php

require_once( 'myContentStorage.class.php');
require_once( 'lib/model/entry.php');
require_once( 'lib/model/conversion.php');
require_once( 'myFileConverter.class.php');

require_once( 'myBatchBase.class.php');





/**
 *
 * will convert a list of files that are assumed to be movies into flash and thumbnails.
 * It can either iterate ofver a list of files in a given directory (one or more)
 * or can iterate the entry table for entries that were created from a give date onwards that are flagged as to_be_converted.
 * TODO- who flags these files ?
 * TODO - what is the part of the upload module ?
 * assume - before conversion, these entries are unaccessable
 * create several tumbnails from second 1 , 2, 5, 10 ,15 of the movie and let the user choose which one to use
 *
 * This batch process should be executed on a periodic basis and should be very efficient in resource usage.
 * Parameters on the conversion should be carefully set to create good video on the one hand and not waist to much time on the other.
 * This proccess can become a bottleneck when there are long movies uploaded !
 */

/*
 * The client runs on the server where the files are uploaded by the user (most probably where the apache runs).
 * Task 1:
 * it takes all files from a directory called CONVERSIONS and sends them to the converterServer
 *  - phase 1: moves from one directory to another
 *  - phase 2: moves from one directory to some shared disk space common to converton servers
 *  - phase 3: FTPs to CDN
 *
 * Task 2:
 * polling from the conversion server. this is the opposite of task 1 - fetch files from he conversion server at place them on the web server.
 * After doing that - should update DB (the status of the entry should now be READY
 *
 */
class myBatchFileConverterClient extends myBatchBase
{
	const DUMMY_PREFIX = "__" ; // if the file_name starts with the dumy prefix - the DB won't be updated

	// handle 10 files at a time
	const MAX_FILES_TO_HANDLE = 100;
	//	const SLEEP_INTERVAL_BETWEEN_CONVERSIONS = 10;

	// a single file should be handled MAX_HANDLE_ATTEMPTS times.
	// is exceeded - something is VERY wrong.
	const  MAX_HANDLE_ATTEMPTS = 10;
	
	private $m_archive_dir = "";

	private $m_source_dir = "";
	private $m_source_indicator_dir = "";
	private $m_errors_directory = "";

	private static $dummy_data_dir;

	public static function getErrorsDir ()
	{
		 return kFile::fixPath( myBatchFileConverterServer::getPreConvertDir() . "../../CLIENT_ERRORS/" );
	}


static public function convert ( $mode=3 )
	{
		SET_CONTEXT ( "CC [m=$mode]");

		myBatchFileConverterServer::init( true );

		list ( $sleep_between_cycles ,
		$number_of_times_to_skip_writing_sleeping ) = self::getSleepParams( 'app_conversion_client_' );

		$converotr_client = new myBatchFileConverterClient ();

		self::initDb();

		$temp_count = 0;
		while ( true )
		{
			self::exitIfDone();
if ( $mode & 1 )
{
			try
			{
				$converotr_client->sentToCenversion ( self::MAX_FILES_TO_HANDLE , $temp_count == 0 );
			}
			catch ( Exception $ex )
			{
				// TODO - log exceptions !!!
				// try to recover !!
				echo ( $ex );

				self::initDb( true );
				self::failed();
			}
}

if ( $mode & 2 )
{

			try
			{
				$converotr_client->pollConverted ( self::MAX_FILES_TO_HANDLE , $temp_count == 0 );
				self::succeeded();
			}
			catch ( Exception $ex )
			{
				// TODO - log exceptions !!!
				// try to recover !!
				echo ( $ex );

				self::initDb( true );
				self::failed();
			}
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




	private function myBatchFileConverterClient ()
	{
		TRACE ( "----------------- Conversion Client ------------------- ");
		$this->m_source_dir = myContentStorage::getFSContentRootPath (). "/content/preconvert/data/";
		$this->m_source_indicator_dir = myContentStorage::getFSContentRootPath (). "/content/preconvert/files/";
		$this->m_archive_dir = myContentStorage::getFSContentRootPath (). "/archive/data/";

		// make sure the directory exists
		myContentStorage::fullMkdir ( $this->m_archive_dir . "test.test");

		TRACE ( "Converting files from [" . $this->m_source_dir . "]" );
		TRACE ( "Indicator files should be placed in [" . $this->m_source_indicator_dir . "]" );
		TRACE ( "Archive directory [" . $this->m_archive_dir . "]" );

		// prepare dir for dummy_data
		self::$dummy_data_dir = myBatchFileConverterServer::getPreConvertDir() . "../dummy_data/";
		myContentStorage::fullMkdir ( self::$dummy_data_dir . "test.test");

		$this->m_errors_directory = kFile::fixPath( myBatchFileConverterServer::getPreConvertDir() . "../../CLIENT_ERRORS/" );
		TRACE ( "Creating [" . $this->m_errors_directory . "] for erroneous files" );
		myContentStorage::fullMkdir ( $this->m_errors_directory . "files/test.test");
		myContentStorage::fullMkdir ( $this->m_errors_directory . "data/test.test");
		myContentStorage::fullMkdir ( $this->m_errors_directory . "log/test.test");

	}

	private function sentToCenversion ( $max_files_to_handle = -1 , $write_to_log = true )
	{

		INFO ( "sentToCenversion");
		// iterate all the indicator
		$file_names = kFile::dirList ( $this->m_source_indicator_dir , false );

		$prefix = "";
		$count = count ( $file_names );
		if ( $count > 0 ) $prefix = "--> ";

		if ( $count > 0 || $write_to_log )
		{
			TRACE ( $prefix . "[" . $count . "] files in the indicator-source directory [" . $this->m_source_indicator_dir . "]" );
		}
		// for each indicator there should be a complete file in the $source_direcotry
		// (as opposed to a partial file that is in the middle of copying)
		$file_count = 0;
		foreach ( $file_names as $file_name )
		{
			if ( $max_files_to_handle > 0 && ( $file_count > $max_files_to_handle ) )
			{
				return;
			}

			try
			{
				if ( !$this->shouldHandleFile ( $file_name ) )
				{
					$from_indicator = $this->sfi ( $file_name );
					$to_indicator = $this->getErrorsDir() . "/files/$file_name" ;
					TRACE ( __METHOD__ . ", Error while handling file [$file_name].  Skipping..." );
					TRACE ( "Moving indicator from [$from_indicator] to [$to_indicator]." );
					// move the file indicator so we'll not attempt this file again.
					@myContentStorage::moveFile( $from_indicator , $to_indicator , true , false );
					
					$from_date = $this->sf ( $file_name );
					$to_data = $this->getErrorsDir() . "/data/$file_name" ;
					TRACE ( "Moving data from [$from_date] to [$to_data]. Skipping..." );
					// move the file indicator so we'll not attempt this file again.
					@myContentStorage::moveFile( $from_date , $to_data , true , false );
					continue;	
				}
			
				$entry_id = self::getEntryIdFromFileName ( $file_name );
				
				list ( $conversion_string , $conversion_profile_id ) = myPartnerUtils::getConversionStringForEntry ( $entry_id , $this->m_source_dir . $file_name );
				
				if ( $conversion_profile_id )
				{
					// NOW - shift to the new conversionClient 
					$from_date = $this->sf ( $file_name );
					$to_data = $this->getNewConversionClientPath() . "/$file_name" ;
					TRACE ( "conversion_profile_id [$conversion_profile_id] -->  Moving data from [$from_date] to [$to_data]. Skipping..." );
					touch ( $to_data . ".indicator" );
					// move the file indicator so we'll not attempt this file again.
					@myContentStorage::moveFile( $from_date , $to_data , true , false );
					
					$from_indicator = $this->sfi ( $file_name );
//					$to_indicator = $this->getErrorsDir() . "/files/$file_name" ;
					TRACE ( "Deleting indicator from [$from_indicator]." );
					// move the file indicator so we'll not attempt this file again.
					@myContentStorage::deleteFile ( $from_indicator );
					
					// assume this file was handled
					$this->removeHandleFile ( $file_name );
					continue;						
				}
				
				$this->archiveFile ( $file_name );
				
	TRACE ( "Setting conversionString for file [$file_name]\n[$conversion_string]" );			
				if ( $conversion_string )
				{
					if ( $conversion_string == myFileConverter::NO_COVERSION )
					{
						TRACE ( "ConversionString " . myFileConverter::NO_COVERSION . " for entry [$entry_id]" );
						$this->updateEntryNoConversion( $entry_id , $file_name );
						continue;
					}
					else
					{
			//			$conversion_string = "-qscale 10 -r 25 -s 400x300 -ar 22050 -ac 1";
					}
				}
							
				try
				{
					
					$conversion = new conversion();
					$entry_id = self::getEntryIdFromFileName ( $file_name );
					$conversion->setEntryId ( $entry_id );
					$conversion->setInFileName( $file_name );
					$conversion->setInFileExt( pathinfo ($file_name, PATHINFO_EXTENSION ) );
					$conversion->setInFileSize( filesize( $this->m_source_dir . $file_name ) );
					$conversion->setStatus( conversion::CONVERSION_STATUS_PRECONVERT );
					$conversion->save();
				}
				catch ( Exception $ex )
				{
					// Do NOT fail the actual conversion
					TRACE ( "Problem reporting conversion details to DB (part I) " . $ex->getTraceAsString() );
				}
				// get transfer the file from the $source_directory to the conversion server
				// see if this entry_id has a specific conversion_string (for it or it's partner)
				// TODO - conversion_strnig - get it !
	
				
	
				$this->transferFile ( $file_name , $conversion_string );
	
				// once handled this file - remove the handle indicator
				$this->removeHandleFile ( $file_name );
				
				//$this->removeLocalFile ( $file_name );
	
				// remove file & indicator
				++$file_count;
			}
			catch ( Exception $ex )
			{
				TRACE ( "Error handling file $file_name\n" . $ex->getMessage() ); 
			}
		}
	}


	private  function pollConverted ( $max_files_to_handle = -1 , $write_to_log = true )
	{
		INFO ( "pollConverted");
		// fetch files from conversion server

		$polling_dir = myBatchFileConverterServer::getPostConvertIndicatorDir();
		$file_names = kFile::dirList( $polling_dir , false );

		$prefix = "";
		$count = count ( $file_names );
		if ( $count > 0 ) $prefix = "<-- ";
			
		if ( $count > 0 || $write_to_log )
		{
			TRACE ( $prefix . "[" . $count . "] files in the polling directory [" . $polling_dir . "]" );
		}

		// foreach file -
		// 	store it using the myContentStorage logic
		// 	update entry in DB - have it's status set to READY (from null or DRAFT)
		//	tell conversion server to remove the file
		$file_count = 0;

		$content_path = myContentStorage::getFSContentRootPath();
		foreach ( $file_names as $file_name )
		{
TRACE ( "Now proccessing [$file_name]" );
			if ( $max_files_to_handle > 0 && ( $file_count > $max_files_to_handle ) )
			{
TRACE ( "returning: max_files_to_handle=[$max_files_to_handle] and file_count=[$file_count]" );
				return;
			}

			if ( kString::beginsWith( $file_name , self::DUMMY_PREFIX ) )
			{
				$finalPath = self::$dummy_data_dir . $file_name;
				// if the file has a DUMMY_PREFIX -
				TRACE ( "Moving DUMMY file [" . $file_name . "] to [" . $finalPath . "]" );
				$conversion_info_str = "";
				$conversion_info = $this->fetchFile ( $file_name , $finalPath , $conversion_info_str );
				kFile::setFileContent ( $finalPath . ".info" , $conversion_info_str );
				continue;
			}


			// don't increment the handle count in this case - it is incremented in the sentToCenversion function
/*			
			if ( !$this->shouldHandleFile ( $file_name ) )
			{
				TRACE ( __METHOD__ . ", Error while handling file [$file_name]. Skipping..." );
				continue;	
			}
	*/		

			$entry_id = self::getEntryIdFromFileName ( $file_name );

			TRACE ( "Updating entry [" . $entry_id ."]" );
			entryPeer::setUseCriteriaFilter( false ); // update the entry even if it's deleted
			$c = new Criteria();
			$c->add(entryPeer::ID, $entry_id);
			$entry = entryPeer::doSelectOne( $c );


			// fetch file from the conversion server and store it in the correct place - content/entry/data/...
			// using the ame logic as in contribute/insertEntryAction & myContentStorage...

TRACE ( "found entry [$entry_id]. Updating..." );
			if ( $entry != NULL )
			{
				//$fullPath = $this->sf ( $file_name );// $uploads.$kuser_id.'_data.'.pathinfo($fileName, PATHINFO_EXTENSION);
				$entry->setData($file_name);
				$finalPath = $content_path.$entry->getDataPath();

				// fetch file from the conversion server and store it in the correct place - content/entry/data/...
				// using the ame logic as in contribute/insertEntryAction & myContentStorage...
				TRACE ( "Moving file [" . $file_name . "] to [" . $finalPath . "]" );
				//$conversion_info_str = $this->fetchFile ( $file_name , $finalPath );
				//$conversion_info = conversionInfo::fromString( $conversion_info_str );
				$conversion_info_str = "";
				$conversion_info = $this->fetchFile ( $file_name , $finalPath , $conversion_info_str);
				
				TRACE ( print_r ( $conversion_info , true ) ) ;

				// set the status
				if ( $conversion_info->status_ok == true )
				{
					TRACE ( "File [$conversion_info->source_file_name] converted OK" );
					//$entry->setStatus ( entry::ENTRY_STATUS_READY );
					try 
					{
						// try createing the flv helpers for this file
						// when doing this lazily - it causes some problems
						myFlvStaticHandler::createHelpers( $finalPath );
						myFlvStaticHandler::createHelpers( myContentStorage::getFileNameEdit( $finalPath ) );
					}
					catch ( Exception $ex)
					{
						TRACE ( "Error while creating helper files for [$finalPath]" );
					} 
					$entry->setStatusReady();
				}
				else
				{
					TRACE ( "Problem converting file [$conversion_info->source_file_name]" );
					$entry->setStatus ( entry::ENTRY_STATUS_ERROR_CONVERTING );
				}

				try
				{
TRACE ( "Updating conversion info for [$entry_id]" );
					$c = new Criteria();
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
						$conversion->setOutFileName( $conversion_info->extra_data->getOutFileName() );
						$conversion->setOutFileSize( $conversion_info->extra_data->getOutFileSize() );
						$conversion->setOutFileName2( $conversion_info->extra_data->getOutFileName2() );
						$conversion->setOutFileSize2( $conversion_info->extra_data->getOutFileSize2() );
						$conversion->setConversionParams( $conversion_info->extra_data->getConversionParams() );
						$conversion->setConversionTime( $conversion_info->extra_data->getConversionTime() );
						if ( $conversion_info->status_ok == true )
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
				//				myContentStorage::moveFile($fullPath, $finalPath);

				$entry->setLengthInMsecs ( $conversion_info->duration );
				
TRACE ( "Set duration for [$entry_id] [{$conversion_info->duration}]" );
				if ( $entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_VIDEO )
				{
					// TODO - move out of this function if the partner is required for more configurations 
					$partner = PartnerPeer::retrieveByPK( $entry->getPartnerId() );

					$entry->updateVideoDimensions();
TRACE ( "Video width [" . $entry->getWidth() . "] height [" . $entry->getHeight() . "}" );
					
//					if ( $conversion_info ) $entry->setDimensions ( $conversion_info->video_width , $conversion_info->video_height );
					$offset = $entry->getBestThumbOffset( $partner->getDefThumbOffset() );
					TRACE ( "Entry id [" . $entry->getId() . "] Thumb offset: [$offset]" );
					// first create the thumb for the entry
					
TRACE ("Before myEntryUtils::createThumbnailFromEntry [$entry_id]" );
					myEntryUtils::createThumbnailFromEntry ( $entry , $entry , $offset );
TRACE ("Before myEntryUtils::createRoughcutThumbnailFromEntry [$entry_id]" );
					// 	then make sure it will propage to the roughcut if needed
					myEntryUtils::createRoughcutThumbnailFromEntry ( $entry , false );
TRACE ("After myEntryUtils::createRoughcutThumbnailFromEntry [$entry_id]" );
				}
				
				// send notification - regardless its status
//				if ( $entry->isReady() ) 				{
TRACE ( "Sending notification for entry [$entry_id]" );
				myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE , $entry );	

				
TRACE ( "Before saving entry [$entry_id]" );
				$entry->save();
TRACE ( "After saving entry [$entry_id]" );
				//entryPeer::doUpdate($entry);

				// flag a success to break the row of faliures (is any)
				self::succeeded();
			}
			else
			{
				// TODO -cannot update entry because it does not exist in the db !
				// error !!
				TRACE ( "Error: entry [$entry_id] does not exist in DB." );

				TRACE ( "Moving file [" . $file_name . "] to [$this->m_errors_directory]"  );
				myContentStorage::moveFile( $this->tf ( $file_name , myBatchFileConverterServer::getPostConvertDir() ) , $this->m_errors_directory . "/data/" . $file_name  , true , false );
				myContentStorage::moveFile( $this->tf ( $file_name , myBatchFileConverterServer::getPostConvertIndicatorDir() ) , $this->m_errors_directory . "/files/" . $file_name  , true , false );
				// no log file in this case
				// 				myContentStorage::moveFile( $this->tf ( $file_name , myBatchFileConverterServer::getPostConvertDir() . "../") , $this->sfl ( $file_name ) , true , $should_copy );

			}

			// TODO - should remove remote files after making sure they were set in the DB
			//$this->removeRemoteFile ( $file_name );
			++$file_count;
		}

	}

	private function updateEntryNoConversion ( $entry_id  , $file_name  )
	{
		$full_file_name = $this->sf ( $file_name , myBatchFileConverterServer::getPreConvertDir());
		
		$content_path = myContentStorage::getFSContentRootPath(); 
		entryPeer::setUseCriteriaFilter( false ); // update the entry even if it's deleted
		$entry = entryPeer::retrieveByPK( $entry_id );

		if ( $entry != NULL )
		{
			try
			{
				//$fullPath = $this->sf ( $file_name );// $uploads.$kuser_id.'_data.'.pathinfo($fileName, PATHINFO_EXTENSION);
				$entry->setData($file_name);
				$finalPath = $content_path.$entry->getDataPath();
	
				$duration = myFlvStaticHandler::getLastTimestamp( $full_file_name );
				$entry->setLengthInMsecs ( $duration );
				
				// move file to the correct place
				myContentStorage::moveFile ( $full_file_name , $finalPath, true , true ); // don't move it yet, use copy - there has to still be a thumbnail
				// remove indicators 
				myContentStorage::deleteFile ( $this->sfi ( $file_name  ) );
				
				if ( $entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_VIDEO )
				{
					$entry->updateVideoDimensions();
TRACE ( "Video width [" . $entry->getWidth() . "] height [" . $entry->getHeight() . "}" );
					
				//	$entry->save(); // save first so the names of the files will be good ones
					$offset = $entry->getBestThumbOffset();
					TRACE ( "Thumb offset for non-converted video: [" . $entry->getDataPath() . "] offset: [$offset]" );
					// first create the thumb for the entry
					myEntryUtils::createThumbnailFromEntry ( $entry , $entry , $offset );
					// then make sure it will propage to the roughcut if needed
					myEntryUtils::createRoughcutThumbnailFromEntry ( $entry , false );
				}

				// set the status
				$entry->setStatusReady();
				
				// send notification
				if ( $entry->isReady() )
				{
					myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE , $entry );	
				}
				
				$entry->save();
						
				// TODO - add to conversion table - with NO_CONVERT
	
				
				TRACE ( "File [$full_file_name] Moved with no conversion to [$finalPath]" );
			}
			catch ( Exception $ex )
			{
				TRACE ( __METHOD__ . ": Error while skipping convertion for [$entry_id] [$file_name]\n" . $ex->getMessage() );
			}
			
		}
	}
	
	// indicate this file was handled - if count exceeds MAX_HANDLE_ATTEMPTS
	private function shouldHandleFile ( $file_name )
	{
		$handled_full_path = myBatchFileConverterServer::getPreConvertDir() . "../../handled/" . $file_name . ".info" ;
		kFile::fullMkdir( $handled_full_path );
		if ( !file_exists ( $handled_full_path ) )
		{
			file_put_contents ( $handled_full_path , "1" ); // first time
			return true;
		}
		else
		{
			$current_count = file_get_contents( $handled_full_path );
			if ( ! is_numeric( $current_count ) ) $current_count = 0;
			if ( $current_count >= self::MAX_HANDLE_ATTEMPTS ) 
			{
				TRACE ( __METHOD__ . ", Error: [$file_name] too many times [$current_count]. Returning false" ); 
				return false;
			}
			
			$current_count++;
			file_put_contents( $handled_full_path , $current_count );
			return true;
		}
	}
	
	private function removeHandleFile ( $file_name )
	{
		$handled_full_path = myBatchFileConverterServer::getPreConvertDir() . "../../handled/" . $file_name . ".info" ;
		if ( file_exists ( $handled_full_path ) ) kFile::deleteFile( $file_name);
	}
	
	// assume the file name without the extension if the entry id
	private static function getEntryIdFromFileName ( $file_name )
	{
		return kFile::getFileNameNoExtension( $file_name );
	}

	// source file
	private function sf ( $file_name )
	{
		return $this->m_source_dir . "/" . $file_name;
	}

	// source file indicator
	private function sfi ( $file_name )
	{
		return $this->m_source_indicator_dir . "/" . $file_name;
	}

	// source file logfile
	private function sfl ( $file_name )
	{
		return $this->m_source_indicator_dir . "../log/" . $file_name;
	}

	// target file
	private function tf ( $file_name , $remote_dir )
	{
		return $remote_dir  . "/" . $file_name;
	}

	private function archiveFile ( $file_name  )
	{
		TRACE ( "Archiving file [" . $file_name . "] to dir [" . $this->m_archive_dir . "]}"  );
		$target = self::createFilePath ( $this->m_archive_dir , $file_name );
		TRACE ( "Archiving file [" . $file_name . "] to [" . $target . "]}"  );
		// copy
		myContentStorage::moveFile( $this->sf ( $file_name ) ,  $target , true , true );
	}

	// phase 1 - move from source to target
	private function transferFile ( $file_name , $conversion_string = null )
	{
		$should_copy = false;
		TRACE ( "Transfering file [" . $file_name . "] to ConversionServer [" .myBatchFileConverterServer::getPreConvertDir() . "]}"  );
		$target =  $this->tf ( $file_name , myBatchFileConverterServer::getPreConvertDir() );
		myContentStorage::moveFile( $this->sf ( $file_name ) , $target, true , $should_copy );
		
		if ( $conversion_string != null )
		{
			// make sure the conversion_string is set BEFORE the file indicator
			myFileConverter::createConversionStringForFile ( $target , $conversion_string );
		}
		// if there is some error transfering the file - the indicators would not move !!
		myContentStorage::moveFile( $this->sfi ( $file_name ) , $this->tf ( $file_name , myBatchFileConverterServer::getPreConvertIndicatorDir()) , true , $should_copy );
	}

	// phase 1 - move from target to source
	private function fetchFile ( $file_name , $full_target_path , &$conversion_info_str)
	{
		$should_copy = false;
		TRACE ( "Fetching file [" . $file_name . "] from ConversionServer [" .myBatchFileConverterServer::getPostConvertDir() . "]}"  );
		$source = $this->tf ( $file_name , myBatchFileConverterServer::getPostConvertDir());
		if ( file_exists( $source ) )
		{
			// the file itself might not exist - this indicates a problem where only the indicator is placed with the converion_info of the failure
			myContentStorage::moveFile( $source , $full_target_path , true , $should_copy );
		}
		// don't move the indicator - delete it !
		// the indicator file contains data about the conversion - return it
		$conversion_info_str = kFile::getFileContent( $this->tf ( $file_name , myBatchFileConverterServer::getPostConvertIndicatorDir() ) );
		myContentStorage::deleteFile( $this->tf ( $file_name , myBatchFileConverterServer::getPostConvertIndicatorDir() ) );//, $this->sfi ( $file_name ) , true , $should_copy );
		$conversion_info = conversionInfo::fromString( $conversion_info_str );
		
		$target_name_2 = $conversion_info->target_file_name_2;
		if ( $target_name_2 )
		{
			myContentStorage::moveFile( $target_name_2 ,myContentStorage::getFileNameEdit( $full_target_path ) , true , $should_copy );
		}
		return $conversion_info;
	}

	private function removeRemoteFile ( $file_name )
	{
		myContentStorage::deleteFile ( $this->tf ( $file_name , myBatchFileConverterServer::getPostConvertDir() ) );
		myContentStorage::deleteFile ( $this->tf ( $file_name , myBatchFileConverterServer::getPostConvertIndicatorDir() ) );
	}


	public static function createFilePath ( $base_path , $file_name )
	{
		$id = self::getEntryIdFromFileName ( $file_name );
			// create a new path with the file name
		$entry = entryPeer::retrieveByPK( $id );
		if ( $entry )
		{
			$int_id = $entry->getIntId();
			$path_name = myContentStorage::dirForId ( $int_id , $id ). "." . pathinfo ( $file_name , PATHINFO_EXTENSION );	
		}
		else
		{
			$path_name = "AZ/" . $file_name;
			kFile::fullMkdir( $path_name );
		}

		// make sure the separator exists between the 2 paths
//		if ( ! kString::endsWith( $base_path , "/" ) ) $base_path .= "/";
			
		return  $base_path . "/" . $path_name ;
	}
	
	public function getNewConversionClientPath ()
	{
		return myContentStorage::getFSContentRootPath (). "/content/new_preconvert";
	}
	
}

/* ------------------------------------------------------------------------------------------------------------------------------ */

class myBatchFileConverterServer extends myBatchBase
{
	const OUTPUT_FILE_SUFFIX = "flv";
	static private $s_pre_convert_dir = null;
	static private $s_post_convert_dir = null;
	static private $s_pre_convert_inicator_dir = null;
	static private $s_in_process_inicator_dir = null;
	static private $s_post_convert_inicator_dir = null;
	static private $s_log_directory = null;
	static private $s_errors_directory = null;


	//  const SLEEP_INTERVAL_BETWEEN_CONVERSIONS = 10;

	static public function convert ( $server_id = 1 )
	{
		SET_CONTEXT ( "CS($server_id)");
		TRACE ( "----------------- Conversion Server ------------------- ");
		self::init();

		list ( $sleep_between_cycles ,
		$number_of_times_to_skip_writing_sleeping ) = self::getSleepParams( 'app_conversion_server_' );

		$temp_count = 0 ;
		while ( true )
		{
			self::exitIfDone();
			// TODO - remove !! - for testing only !!
			//if ( ++$temp_count > 2 ) break;

			try
			{
				self::convertAllFilesInQueue ( $temp_count == 0 ) ;
			}
			catch ( Exception  $ex )
			{
				// TODO - log exceptions !!!
				// try to recover !!
				TRACE ( $ex->getTraceAsString() );
			}

			if ( $temp_count == 0 )
			{
				TRACE ( "Ended conversion of all files in queue. Resting for a while (" . $sleep_between_cycles . ") seconds. " .
				"Will write to the log in (" . ( $sleep_between_cycles * $number_of_times_to_skip_writing_sleeping ) . ") seconds");
			}

			$temp_count++;
			if ($temp_count >= $number_of_times_to_skip_writing_sleeping ) $temp_count = 0;

			sleep ( $sleep_between_cycles );
		}
	}

	public  static function init ( $silent = false )
	{
		$base_dir = sfConfig::get('sf_root_dir')."/../../conversions/";
		self::$s_pre_convert_dir = $base_dir . "preconvert/data/";
		self::$s_pre_convert_inicator_dir = $base_dir . "preconvert/files/";
		self::$s_post_convert_dir = $base_dir . "postconvert/data/";
		self::$s_post_convert_inicator_dir = $base_dir . "postconvert/files/";
		self::$s_in_process_inicator_dir = $base_dir . "preconvert/inprocess_files/";
		if ( ! $silent ) TRACE ( "preconvert dir [" . self::$s_pre_convert_dir . "]" );
		if ( ! $silent ) TRACE ( "postconvert dir [" . self::$s_post_convert_dir . "]" );

		// create the inprocess dir
		myContentStorage::fullMkdir ( self::$s_in_process_inicator_dir . "test.test");

		// create the post_conversion dir if not exists
		myContentStorage::fullMkdir ( self::$s_post_convert_dir . "test.test");
		myContentStorage::fullMkdir ( self::$s_post_convert_inicator_dir . "test.test");


		// create path for the logs
		self::$s_log_directory = self::$s_pre_convert_inicator_dir . "../log";
		if ( ! $silent ) TRACE ( "Creating [" . self::$s_log_directory . "]" );
		myContentStorage::fullMkdir ( self::$s_log_directory . "/" . "test.test");

		self::$s_errors_directory = self::$s_pre_convert_inicator_dir . "../../SERVER_ERRORS/";
		if ( ! $silent ) TRACE ( "Creating [" . self::$s_errors_directory . "] for erroneous files" );
		myContentStorage::fullMkdir ( self::$s_errors_directory . "/files/test.test");
		myContentStorage::fullMkdir ( self::$s_errors_directory . "/data/test.test");
		myContentStorage::fullMkdir ( self::$s_errors_directory . "/log/test.test");

	}

	static public function getPreConvertDir ()
	{
		return self::$s_pre_convert_dir;
	}

	static public function getPreConvertIndicatorDir ()
	{
		return self::$s_pre_convert_inicator_dir;
	}

	static public function getPostConvertDir ()
	{
		return self::$s_post_convert_dir;
	}

	static public function getPostConvertIndicatorDir ()
	{
		return self::$s_post_convert_inicator_dir;
	}


	static public function getErrorsDir ()
	{
		return self::$s_errors_directory;
	}


	private static function convertAllFilesInQueue ( $write_to_log = true )
	{
		INFO ( "convertAllFilesInQueue");

		$file_names = kFile::dirList( self::$s_pre_convert_inicator_dir , false );

		$prefix = "";
		$count = count ( $file_names );
		if ( $count > 0 ) $prefix = "--> ";

		if ( $count > 0 || $write_to_log )
		{
			TRACE ( $prefix . "there are [" . $count . "] in queue waiting to be converted" );
		}

		$should_copy = false;
		foreach ( $file_names as $file_name )
		{
			if ( !file_exists( self::$s_pre_convert_inicator_dir . $file_name ) )
			{
				// the file was initially on the list, but some other server moved it - continue to the next file
				//TRACE ( "** File [" . self::$s_pre_convert_inicator_dir . $file_name . "] no longer exists ! GRABBED by some other server :-(" );
				continue;
			}

			try
			{
				// move the file to the inprocess_files dir so other servers will not convert it too
				$moved_indicator = myContentStorage::moveFile( self::$s_pre_convert_inicator_dir . $file_name ,
				self::$s_in_process_inicator_dir . $file_name ,
				true ,
				false );

				if ( ! $moved_indicator )
				{
					// the file was initially on the list, but some other server moved it - continue to the next file
					TRACE ( "** File [" . self::$s_pre_convert_inicator_dir . $file_name . "] no longer exists ! GRABBED by some other server ;-(" );
					continue;
				}
			}
			catch ( Exception $ex )
			{
				TRACE ( "** Error! File [" . self::$s_pre_convert_inicator_dir . $file_name . "] no longer exists ! GRABBED by some other server :-(" );
				// if moving the files failed - grabbed by some other server
				continue;
			}


			$target_file_name = kFile::getFileNameNoExtension ( $file_name ) . "." . self::OUTPUT_FILE_SUFFIX ;
			TRACE ( "Now converting [" . $file_name . "]->[" . $target_file_name . "]");

			$log_file = self::$s_log_directory . "/" . $file_name . ".txt";
			// use the preconverted/log/file as the output file of the conversion

			$source_path = self::sf ( $file_name );
			$target_path = self::tf ( $target_file_name );
			$start_time = microtime(true);
			TRACE ( "Now converting [" . $source_path . "]->[" . $target_path . "]");
			$convert_result = myFileConverter::convert( $source_path , $target_path , "flv" ,	$log_file );

			$end_time = microtime(true);

			// copy indicators

			$return_value = $convert_result["return_value"];
			$output = $convert_result["output"];
			$conversion_info = $convert_result["conversion_info"];
			$konverted = $convert_result["konverted"];

			$diff = (int)(( $end_time - $start_time ) * 1000);
			TRACE ( "  Result: " . $return_value . " Took " . $diff . " milliseconds");

			if ( $return_value == 0 )
			{
				if ( $konverted )
				{
					TRACE ( "$file_name was KONVERTED. Most probably a file from a webcam" );
				}

				// move the file indicator to the post indicator dir AND rename it to match the converted file name !
				TRACE ( "Convertsion OK. moving file indicator [" . self::$s_pre_convert_inicator_dir . $file_name . "]->["
					. self::$s_post_convert_inicator_dir . $target_file_name . "]");

				// the indicator is placed user the inprocess_files dir
				myContentStorage::moveFile( self::$s_in_process_inicator_dir . $file_name , // self::$s_pre_convert_inicator_dir . $file_name ,
				self::$s_post_convert_inicator_dir . $target_file_name ,
				true ,
				$should_copy );
					
				// create the file indicator with the conversion_info
				kFile::setFileContent( self::$s_post_convert_inicator_dir . $target_file_name , $conversion_info->toString() );
				
				// remove the conversion_string if exists
				myFileConverter::removeConversionStringForFile ( self::sf ( $file_name ) ) ;
			}
			else
			{
				// check if there is already a "ERROR" file - if so - it's an indication of an earlier attemp to convert
				// move the file to some error dir
				if ( file_exists( $log_file . ".ERROR"  ) )
				{
					// create the file indicator with the conversion_info - indicating the problem
					$conversion_info->status_ok = false;
					kFile::setFileContent( self::$s_post_convert_inicator_dir . $target_file_name , $conversion_info->toString() );

					// move indicator from pin_pregress id so there will be no attemp to convert this file any more
					myContentStorage::moveFile( self::$s_in_process_inicator_dir . $file_name ,
					self::$s_errors_directory . "files/" . $file_name ,
					true ,
					false );

					// move file that had a problem
					TRACE ( "!! Copying erroneous file from [" . self::sf ( $file_name ) ."] -> ["  .self::$s_errors_directory .  "data/" . $file_name . "]" );
					myContentStorage::moveFile ( self::sf ( $file_name ) , self::$s_errors_directory .  "data/" . $file_name , true , true  );

					// move log with error
					myContentStorage::moveFile ($log_file . ".ERROR" ,
					self::$s_errors_directory . "log/" . $file_name . ".txt.ERROR" );

					// send email to report this error to ProductionSupport
					// TODO !
					TRACE ( "Reporting error: problem converting file ["  .self::$s_errors_directory .  "data/" . $file_name . "] with log in file at [" .
					self::$s_errors_directory . "log/" . $file_name . ".txt.ERROR]" );
				}
				else
				{
					TRACE ( "Error converting file [$source_path] to [$target_path]. Change log file to .ERROR" );
					// TODO
					//some error -> should log, and attempt this again for once or twice.
					//  The problematic file should NOT stay in the preconvert forever - the converter will spend time on this malformed file
					// for nothing !
					myContentStorage::moveFile ( $log_file , $log_file . ".ERROR" );


					// move the file back from the inprocess_files dir to give it an extra attempt
					myContentStorage::moveFile( self::$s_in_process_inicator_dir . $file_name ,
					self::$s_pre_convert_inicator_dir . $file_name ,
					true ,
					false );
				}
			}
		}
	}


	// source file
	static private function sf ( $file_name )
	{
		return self::$s_pre_convert_dir . "/" . $file_name;
	}

	// target file
	static private function tf ( $file_name )
	{
		return self::$s_post_convert_dir . "/" . $file_name;
	}

	
}

?>