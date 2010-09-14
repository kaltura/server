<?php
require_once( 'myBatchBase.class.php');
require_once( 'myContentStorage.class.php');
require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'lib/model/BatchJob.php');

class myBatchDownloadVideoServer extends kConversionClientBase
{
	const SECONDS_BETWEEN_ADD_SIMILAR_JOB  =  3600 ;// TODO - change to be 3600
	
	const DOWNLOAD_VIDEO_FORMAT_ORIGINAL = "original";
	
	const STATUS_CONVERSION_OK = 1;
	const STATUS_FILE_EXISTS = 2;
	const STATUS_ERROR_CONVERTING = 3;
	
	
	public static function getBatchStatus( $args )	
	{	
		$batch_status = new batchStatus();
		$batch_status->batch_name = $args[0];
		$stats = $batch_status->getDbStats( $batch_status->batch_name , BatchJob::BATCHJOB_TYPE_DOWNLOAD );
		$batch_status->addToPending( "DB:batch_job, type=" . BatchJob::BATCHJOB_TYPE_DOWNLOAD . " status=" . BatchJob::BATCHJOB_STATUS_PENDING , @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_PENDING]["count"]); 
		
		// bellow - from the fact this is infact a kConversionClientBase
		$batch_status->addToInProc( "DB:batch_job, type=" . BatchJob::BATCHJOB_TYPE_DOWNLOAD . " status=" . BatchJob::BATCHJOB_STATUS_PROCESSING , @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_PROCESSING]["count"] );
		$batch_status->addToInProc( "Disk:" . $args[2] . "*" . kConversionHelper::INDICATOR_SUFFIX , $batch_status->getDiskStatsCount( $args[0] , $args[2] ,  "*" . kConversionHelper::INDICATOR_SUFFIX ) );
		$batch_status->addToInProc( "Disk:" . $args[2] . "*" . kConversionHelper::INPROC_SUFFIX , $batch_status->getDiskStatsCount( $args[0] , $args[2] ,  "*" . kConversionHelper::INPROC_SUFFIX ) );
		
		$batch_status->succeedded_in_period = @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_FINISHED]["count"];
		$batch_status->failed_in_period = @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_FAILED]["count"];
		
		$batch_status->last_log_time  = @$stats["log_timestamp"];
		
		return $batch_status; 
	}

	
	// if the file format is empty or equal to DOWNLOAD_VIDEO_FORMAT_ORIGINAL - the RAW version of the file isused - simply send an email with the raw URL 
	public static function addJob($puser_id, $entry, $version, $file_format , $conversion_quality = null , $force_download = false )
	{
		$entryId = $entry->getId();
		$entryIntId = $entry->getIntId();
		$entryVersion = $version ? $version : $entry->getVersion();

		$partner = $entry->getPartner ();
		$email = $partner->getAdminEmail();
		$admin_name = $partner->getAdminName();
		if ( $file_format == self::DOWNLOAD_VIDEO_FORMAT_ORIGINAL || $entry->getMediaType()== entry::ENTRY_MEDIA_TYPE_IMAGE )
		{
			self::sendDownloadMail ( $email , $admin_name , $entry , "" );
			return;
		}
		
		if ( ! $force_download )
		{
			$similar_job = self::findSimilarJobs( $entryId , $entryVersion, $file_format , self::SECONDS_BETWEEN_ADD_SIMILAR_JOB );
			if ( $similar_job )
			{
				if ( $similar_job->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED )
				{
					$data = json_decode($similar_job->getData(), true);
			
					$downloadUrl = $data['downloadUrl'];
					myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_BATCH_JOB_SIMILAR_EXISTS, $similar_job , null , null , null , 
						array ( "download_url" => $downloadUrl ) );				
					
					// a similar job has successfylly ended - send an email but don't create a new job
					self::sendDownloadMail ( $email , $admin_name , $entry , $file_format );
				}
				return $similar_job;
			}
		}
		
		$job = new BatchJob();
		$currentDc = kDataCenterMgr::getCurrentDc();
		$job->setDc($currentDc["name"]);
		$job->setJobType(BatchJob::BATCHJOB_TYPE_DOWNLOAD);
		$job->setData(json_encode(array(
			'puserId' => $puser_id,
			'entryId' => $entryId,
			'entryIntId' => $entryIntId,
			'entryVersion' => $entryVersion,
			'fileFormat' => $file_format,
			'email' => $email,
			'conversionQuality' => $conversion_quality ,
			//'serverUrl' => "http://xp/final/$entryId_$entryVersion.avi",
			//'deleteUrl' => "http://xp:1234/DeleteMovie/$entryId_$entryVersion.avi"
		)));
		$job->setStatus(BatchJob::BATCHJOB_STATUS_PENDING);
		$job->setCheckAgainTimeout(time() + 10);
		$job->setProgress(0);
		$job->setMessage('Queued');
		$job->setDescription('Queued, waiting to run');
		$job->setUpdatesCount(0);
		$job->setEntryId( $entryId );
		$job->setPartnerId( $entry->getPartnerId());
		$job->setSubpId ( $entry->getSubpId());
		$job->save();
		
		
		$server_cmd_path = myContentStorage::getFSContentRootPath (). "/conversions/preconvert_cmd/";
		$server_res_path = myContentStorage::getFSContentRootPath (). "/conversions/download_res/";
		$commercial_server_cmd_path = myContentStorage::getFSContentRootPath (). "/conversions/preconvert_commercial_cmd/";
		$download_client = new myBatchDownloadVideoServer( "" , "" , $server_cmd_path , $server_res_path , $commercial_server_cmd_path );
		SET_CONTEXT ( null ); // this is to prevent writing TRACEs to the output
		
		list ( $status , $res , $download_path ) = $download_client->sentToCenversion ( $entry , $file_format , $conversion_quality , $force_download );
		
		if ( $status == self::STATUS_ERROR_CONVERTING )
		{
			// error finding file in archive...
			$job->setStatus( BatchJob::BATCHJOB_STATUS_FAILED );
			$job->setMessage('Error');
			$job->setDescription("Cannot find source file [$res] in archive");
			$job->save();
			throw new APIException( APIErrors::DOWNLOAD_ERROR , $res ); 
		}
		elseif ( $status == self::STATUS_FILE_EXISTS )
		{
			self::sendDownloadMail ( $email , $admin_name , $entry , $file_format );
			// status should be set to finish - there was a file already
			$job->setStatus(BatchJob::BATCHJOB_STATUS_FINISHED );	
		}
		else
		{
			$job->setData(json_encode(array(
				'puserId' => $puser_id,
				'entryId' => $entryId,
				'entryIntId' => $entryIntId,
				'entryVersion' => $entryVersion,
				'fileFormat' => $file_format,
				'email' => $email ,
				'archivedFile' => $res,
				'downoladPath' => $download_path ,
				'conversionQuality' => $conversion_quality ,
				//'serverUrl' => "http://xp/final/$entryId_$entryVersion.avi",
				//'deleteUrl' => "http://xp:1234/DeleteMovie/$entryId_$entryVersion.avi"
			)));	
			$job->setStatus(BatchJob::BATCHJOB_STATUS_PROCESSING );	
		}
		$job->save();
		
		return array ( $job ) ;
	}

	
	
	
	// don't spawn jobs if there are already finished ones or in progress with a reasonable duration 
	private static function findSimilarJobs ( $entryId, $version, $file_format , $time_delta )
	{
		// first check if there are no similar jobs (entry_id,version and file_format) pending
		$c = new Criteria();
		$c->add ( BatchJobPeer::ENTRY_ID , $entryId );
		$similar_jobs = BatchJobPeer::doSelect( $c );
		
		if ( $similar_jobs )
		{
			foreach ( $similar_jobs as $similar_job )
			{
				$data = json_decode($similar_job->getData(), true);
				if ( $data['entryVersion'] == $version && $data['fileFormat'] == $file_format )
				{
					// found similar.. see the progress and created_at
					if ( $similar_job->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED )
					{
						// there is no need to ceraet a new job - a successful one was found
						return $similar_job;
					}
					else
					{
						$now = time();
						$job_time = $similar_job->getUpdatedAt(null);
						if ( $now - $job_time < $time_delta )
						// the job was created in the reasonable time_delta
						return $similar_job;  
					}
				}
			}
		}
		
		return null;
	}
	
	
	const KALTURAS_DOWNLOAD_READY = 62;
	
	/*
	 * Will send the ConvCmd to the conversion server
	 */
	private function sentToCenversion ( entry $entry , $file_format , $conversion_quality = null , $force_download = false )
	{
		$archive_path = self::getArchiveDir();
		$id = $entry->getId();
		$int_id = $entry->getIntId();
		$path_name = myContentStorage::dirForId ( $int_id , $id ). "." . $file_format ; // use the file_format as the extension pathinfo ( $entry->getData() , PATHINFO_EXTENSION );
		
		$source_path =kFile::getFileNameNoExtension(  $archive_path . "/" . $path_name  , true );
		$archived_sources = glob ( $source_path .".*");
		if( $archived_sources )
		{
			$source_path = $archived_sources[0]; // in the archive there supposed to be only one
		}
		else
		{
			// try using the data file
			$data_path = myContentStorage::getFSContentRootPath() . $entry->getDataPath();
			if ( file_exists( $data_path ))
			{
				$source_path = $data_path;
			}
			else
			{
				return array ( self::STATUS_ERROR_CONVERTING, $source_path .".*" , null );
			}
		}
		
//		$data_path = myContentStorage::getFSContentRootPath() . "/" . str_replace( "/data/" , "/download/" , $entry->getDataPath() );  // replaced__getDataPath
//		$download_path = kFile::getFileNameNoExtension( $data_path , true ) . ".$file_format"; // make sure the desired format is the extension of the string
		
		$download_path = $entry->getDownloadPathForFormat( null , $file_format );
//print ( "[$download_path]" );		
		// TODO - check to see if file already exists to prevent unneded conversions
		if ( ! $force_download &&  file_exists ( $download_path ) )
		{
			return array ( self::STATUS_FILE_EXISTS , $source_path , $download_path ) ;
		}
		// TODO - use the profileType - maybe per partner

		$conv_profile = null;
		if ( $conversion_quality )
		{
			$conv_profile = myConversionProfileUtils::getConversionProfile( $entry->getPartnerId() , $conversion_quality );
		}
		if ( ! $conv_profile )
		{
			// use the default DOWNLOAD profile
			$conv_profile = myConversionProfileUtils::getConversionProfile( $entry->getPartnerId() ,  ConversionProfile::DEFAULT_DOWNLOAD_PROFILE_TYPE );
		}
		$conv_cmd = $this->createConversionCommandFromConverionProfile ( $source_path , $download_path , $conv_profile , $entry );
		$this->saveConversionCommand();	

//echo "[$conv_cmd]\n";

		return array ( self::STATUS_CONVERSION_OK , $source_path , $download_path ) ;
	}
	
	
	public function doConvert ()
	{
		$MAX_ITERATIONS_DUE_TO_PROPEL_MEMORY_LEAK = 10000000;
		
		self::initDb();

		list ( $sleep_between_cycles ,
		$number_of_times_to_skip_writing_sleeping ) = self::getSleepParams( 'app_download_' );	

		$temp_count = 0;
		while(1)
		{
			try
			{
				$this->pollConverted( $temp_count == 0);
			}
			catch ( Exception $ex )
			{
				TRACE ( "ERROR: " . $ex->getMessage() );
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
	/**
	 * Will poll the results from the server
	 */
	private function pollConverted ($write_to_log )
	{
		$last_worker_count = 0;
		$iteration = 0;
		
		$c = new Criteria();
		$currentDc = kDataCenterMgr::getCurrentDc();
		$c->add(BatchJobPeer::DC, kDataCenterMgr::getCurrentDcId() );
		$c->add(BatchJobPeer::JOB_TYPE, BatchJob::BATCHJOB_TYPE_DOWNLOAD);
		$c->add(BatchJobPeer::STATUS, BatchJob::BATCHJOB_STATUS_PROCESSING);
	
		list ( $full_conv_res_path , $file_name , $in_proc ) = $this->getFileFromConvertion( $write_to_log );
		if ( ! $full_conv_res_path )
		{
			return;
		}
		$entry_id = self::getEntryIdFromFileName ( $file_name );
		
		// when we don't have the archive file, the file name looks like: eudsigimls_100000.flv
		// and after cleaning extension it still contains the version (eudsigimls_100000)
		if(substr_count($entry_id, '_10')) $entry_id = substr($entry_id, 0, strpos($entry_id, '_10'));
		
		$conv_res = kConversionResult::fromFile( $full_conv_res_path );
TRACE ( __METHOD__ . ":[$entry_id]"  );		
		$c->add ( BatchJobPeer::ENTRY_ID , $entry_id );
                // Gonen 24/11/09
                // trying to identify "MySQL server has gone away" and init the DB again if found
                // mysql timeout can cause this situation, and we want to try and recover from it
                try{
        		$jobs = BatchJobPeer::doSelect($c);
                }
                catch(Exception $e)
                {
                        // recover DB connection and try again
                        self::initDb();
                        $jobs = BatchJobPeer::doSelect($c);
                }
		
		// update the job that fits the resuls
		foreach($jobs as $job)
		{
			$data = json_decode($job->getData(), true);
			
			$job_entry_id = $data['entryId'];
			$job_entry_int_id = $data['entryIntId'];
			$job_entry_version = $data['entryVersion'];
			$job_file_format = $data['fileFormat']; // the extension only
TRACE ( print_r ( $data , true ) ) ;
			
			$target = $conv_res->conv_cmd->target_file;
			$target_ext = pathinfo ( $target , PATHINFO_EXTENSION );

			if ( $target_ext != $job_file_format ) continue; // the same entry_id was formated into several formats with several extensions
			
			if ( ! $conv_res->status_ok )
			{
				TRACE("Error while converting [$entry_id] [$job_file_format]\n" . print_r ( $conv_res , true ));
				
				myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_BATCH_JOB_FAILED, $job , null , null , null , 
					array ( "conversion_error" => "Error while converting [$entry_id] [$job_file_format]" ) );
				
				$job->setDescription("Error while converting [$entry_id] [$job_file_format]\n" . print_r ( $conv_res , true ));
				$job->setStatus(BatchJob::BATCHJOB_STATUS_FAILED);
			}
			else
			{
				// TODO - 
				$entry = entryPeer::retrieveByPKNoFilter($entry_id);
				if ( !$entry )
				{
TRACE ( "Cannot find entry [$entry_id]. ")	;
					$job->setDescription("Cannot find entry [$entry_id]");
					$job->setStatus(BatchJob::BATCHJOB_STATUS_FAILED);
					$job->save();
					continue; // go on to the next job
				}
				if ($data['email'])
				{
					$partner = $entry->getPartner ();
					$email = $partner->getAdminEmail(); 
					$admin_name = $partner->getAdminName();					
					self::sendDownloadMail ( $data['email'] , $admin_name , $entry , $job_file_format );
				}
				
				$downloadUrl =  $entry->getConvertedDownoadUrl ( $target );
				$data["downloadUrl"] = $downloadUrl;
				$job->setData(json_encode($data) );			
				
				$job->setMessage("Converted to [$job_file_format]");
				$job->setDescription("target: $target");
				$job->setProgress ( 100 );
				$job->setStatus(BatchJob::BATCHJOB_STATUS_FINISHED);
TRACE ( "Before sending notification [" . kNotificationJobData::NOTIFICATION_TYPE_BATCH_JOB_SUCCEEDED . "]" );				
				$not_res = myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_BATCH_JOB_SUCCEEDED, $job , null , null , null , 
					array ( "download_url" => $downloadUrl ), $entry_id );				
TRACE ( "After sending notification [" . kNotificationJobData::NOTIFICATION_TYPE_BATCH_JOB_SUCCEEDED . "] notification: " . 
	print_r ( $not_res , true ) );

				// FileSync - add sync for converted download file
				if ($entry)
				{
					$fileSyncKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DOWNLOAD, $job_file_format);
					kFileSyncUtils::createSyncFileForKey($fileSyncKey, true);
				}
			}
			$job->save();
		}
		
		$this->removeInProc( $in_proc );
	}
	
	protected static function sendDownloadMail ( $email , $admin_name , $entry , $format )
	{
		
		$entry_id = $entry->getId();
		// this will be the URL to download from
		// having the format empty - will indicate the RAW file shou8ld be fetched from the archive
		$finalPath = "/downloadUrl?url=" . myPartnerUtils::getUrlForPartner( $entry->getPartnerId() , $entry->getSubpId() ) . 
			"/raw/entry_id/$entry_id/type/download/format/$format";
		
		if ( $format )
		{
			$finalPath .= "/f/$entry_id.$format"; // make sur the URL ends with the file suffix
		}
		$download_url = myPartnerUtils::getCdnHost($entry->getPartnerId()).$finalPath ;
TRACE ( "Sending email to [$admin_name]:[$email] about entry [{$entry->getId()}] with format [$format]->[$download_url]");

		if ( ! $admin_name )
		{
			$admin_name = " ";
		}

		kJobsManager::addMailJob(
			null, 
			$entry_id,
			$entry->getPartnerId(),
			self::KALTURAS_DOWNLOAD_READY, 
			kMailJobData::MAIL_PRIORITY_NORMAL, 
			kConf::get ( "batch_download_video_sender_email" ), 
			kConf::get ( "batch_download_video_sender_name" ), 
			$email, 
			array( $admin_name , $download_url ));	
	}
}

?>