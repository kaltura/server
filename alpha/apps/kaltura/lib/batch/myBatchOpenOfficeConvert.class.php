<?php
require_once( 'myBatchBase.class.php');
require_once( 'myContentStorage.class.php');
require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'lib/model/BatchJob.php');

class myBatchOpenOfficeConvert extends kConversionClientBase
{
	const SECONDS_BETWEEN_ADD_SIMILAR_JOB  =  3; //3600 ;// TODO - change to be 3600
	
	const STATUS_CONVERSION_OK = 1;
	const STATUS_FILE_EXISTS = 2;
	const STATUS_ERROR_CONVERTING = 3;
	
	
	public static function getBatchStatus( $args )	
	{	
		$batch_status = new batchStatus();
		$batch_status->batch_name = $args[0];
		$stats = $batch_status->getDbStats( $batch_status->batch_name , BatchJob::BATCHJOB_TYPE_OOCONVERT );
		$batch_status->addToPending( "DB:batch_job, type=" . BatchJob::BATCHJOB_TYPE_OOCONVERT . " status=" . BatchJob::BATCHJOB_STATUS_PENDING , @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_PENDING]["count"]); 
		
		// bellow - from the fact this is infact a kConversionClientBase
		$batch_status->addToInProc( "DB:batch_job, type=" . BatchJob::BATCHJOB_TYPE_OOCONVERT . " status=" . BatchJob::BATCHJOB_STATUS_PROCESSING , @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_PROCESSING]["count"] );
		//$batch_status->addToInProc( "Disk:" . $args[2] . "*" . kConversionHelper::INDICATOR_SUFFIX , $batch_status->getDiskStatsCount( $args[0] , $args[2] ,  "*" . kConversionHelper::INDICATOR_SUFFIX ) );
		//$batch_status->addToInProc( "Disk:" . $args[2] . "*" . kConversionHelper::INPROC_SUFFIX , $batch_status->getDiskStatsCount( $args[0] , $args[2] ,  "*" . kConversionHelper::INPROC_SUFFIX ) );
		
		$batch_status->succeedded_in_period = @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_FINISHED]["count"];
		$batch_status->failed_in_period = @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_FAILED]["count"];
		
		$batch_status->last_log_time  = @$stats["log_timestamp"];
		
		return $batch_status; 
	}

	
	// if the file format is empty or equal to DOWNLOAD_VIDEO_FORMAT_ORIGINAL - the RAW version of the file isused - simply send an email with the raw URL 
	public static function addJob($puser_id, $entry, $version, $file_format)
	{
		$entryId = $entry->getId();
		$entryIntId = $entry->getIntId();
		$entryVersion = $version ? $version : $entry->getVersion();

		$partner = $entry->getPartner ();
		$email = $partner->getAdminEmail();
		$admin_name = $partner->getAdminName();
		
		/* Was: original verification, sending mail if original requested.
		  Will always be SWF, since hardcoded in 'adddownload'
		*/
		
		// see if there is a similar job - either a finished one or one that started in the past 3600 seconds
		$similar_job = self::findSimilarJobs( $entryId , $entryVersion, $file_format , self::SECONDS_BETWEEN_ADD_SIMILAR_JOB );
		if ( $similar_job )
		{
			/* Was: sending mail if similar job ended.
			  send mail removed from entier process since irrelevant
			*/
			return $similar_job;
		}
		
		
		$job = new BatchJob();
		$currentDc = kDataCenterMgr::getCurrentDc();
		$job->setDc($currentDc["name"]);
		$job->setJobType(BatchJob::BATCHJOB_TYPE_OOCONVERT);
		$job->setData(json_encode(array(
			'puserId' => $puser_id,
			'entryId' => $entryId,
			'entryIntId' => $entryIntId,
			'entryVersion' => $entryVersion,
			'fileFormat' => $file_format,
			'email' => $email,
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
		$download_client = new myBatchOpenOfficeConvert( "" , "" , $server_res_path , $server_res_path , $commercial_server_cmd_path );
		SET_CONTEXT ( null ); // this is to prevent writing TRACEs to the output
		
		list ( $status , $res , $download_path ) = $download_client->sentToCenversion ( $entry , $file_format );
		
		if ( $status == self::STATUS_ERROR_CONVERTING )
		{
			// error finding file in archive...
			$job->setStatus( BatchJob::BATCHJOB_STATUS_FAILED );
			$job->setMessage('Error');
			$job->setDescription("Cannot find source file locally");
			$job->save();
			throw new APIException( APIErrors::DOWNLOAD_ERROR , $res ); 
		}
		elseif ( $status == self::STATUS_FILE_EXISTS )
		{
			/* Was: sending mail if file already exists.
			  send mail removed from entier process since irrelevant
			*/
			$job->setData(json_encode(array(
				'puserId' => $puser_id,
				'entryId' => $entryId,
				'entryIntId' => $entryIntId,
				'entryVersion' => $entryVersion,
				'fileFormat' => $file_format,
				'email' => $email ,
				'archivedFile' => $res,
				'downoladPath' => $download_path ,
				//'serverUrl' => "http://xp/final/$entryId_$entryVersion.avi",
				//'deleteUrl' => "http://xp:1234/DeleteMovie/$entryId_$entryVersion.avi"
			)));			
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
		$currentDc = kDataCenterMgr::getCurrentDc();
		$c->add(BatchJobPeer::DC, kDataCenterMgr::getCurrentDcId() );
		$c->add ( BatchJobPeer::ENTRY_ID , $entryId );
		$c->add ( BatchJobPeer::JOB_TYPE , BatchJob::BATCHJOB_TYPE_OOCONVERT );
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
	
	
	/*
	 * Will send the ConvCmd to the conversion server
	 */
	private function sentToCenversion ( entry $entry , $file_format )
	{
		$sync_key = null;
		$sync_key = $entry->getSyncKey ( entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA );
			
		if ( kFileSyncUtils::file_exists( $sync_key ) )
		{
			$source_path = kFileSyncUtils::getLocalFilePathForKey($sync_key, true);
		}
		else
		{
			return array ( self::STATUS_ERROR_CONVERTING, $source_path , null );	
		}

		$download_path = $entry->getDownloadPathForFormat( null , $file_format );

		// TODO - check to see if file already exists to prevent unneded conversions
		if ( file_exists ( $download_path ) )
		{
			return array ( self::STATUS_FILE_EXISTS , $source_path , $download_path ) ;
		}
		// TODO - use the profileType - maybe per partner

		return array ( self::STATUS_CONVERSION_OK , $source_path , $download_path ) ;
	}
	
	
	public function doConvert ()
	{
		$MAX_ITERATIONS_DUE_TO_PROPEL_MEMORY_LEAK = 10000000;
		
		self::initDb();

		list ( $sleep_between_cycles ,
		$number_of_times_to_skip_writing_sleeping ) = self::getSleepParams( 'app_ooconvert_' );	

		$temp_count = 0;
		while(1)
		{
			try
			{
				$this->findAndConvert( $temp_count == 0 );
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
	 * findAndConvert - pulls jobs from DB, send conversion command, check return value, update job in DB
	 */
	private function findAndConvert( $write_to_log )
	{
		$c = new Criteria();
		$currentDc = kDataCenterMgr::getCurrentDc();
		$c->add(BatchJobPeer::DC, kDataCenterMgr::getCurrentDcId() );
		$c->add(BatchJobPeer::JOB_TYPE, BatchJob::BATCHJOB_TYPE_OOCONVERT);
		$c->add(BatchJobPeer::STATUS, BatchJob::BATCHJOB_STATUS_PROCESSING);
		
                // Gonen 27/01/2010
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

		// do the conversion, check result, update job
		foreach($jobs as $job)
		{
			$data = json_decode($job->getData(), true);
			$source_file = $data['archivedFile'];
			$target_file = $data['downoladPath'];
			$file_format = $data['fileFormat'];

			exec( 'php /opt/kaltura/ppt2swf/ppt2swf.php '.$source_file.' '.$target_file, $output, $error);

			$all_output = implode("\n", $output);
			TRACE("conversion script output: ".$all_output);
			TRACE("command : ".'php /opt/kaltura/ppt2swf/ppt2swf.php '.$source_file.' '.$target_file);
			TRACE("error: ".print_r($error,true));
			if (substr_count($all_output, "ConvertFailed"))
			{
				$job->setDescription( 'Error converting to swf: '.$all_output);
				TRACE('entry '.$job->getEntryId().' failed to convert. output: '.$all_output);
				$job->setStatus(BatchJob::BATCHJOB_STATUS_FAILED);
			}
			elseif(substr_count($all_output, "RetryConversion"))
			{
				TRACE("soffice found to be down, restarted by conversion script. entry: ".$job->getEntryId());
				$job->setDescription('ooffice daemon was probably down ['.time().'] resetting status on pending');
				$job->setStatus(BatchJob::BATCHJOB_STATUS_PROCESSING);
			}
			else
			{
				$job->setMessage( 'Converted: '.print_r($all_output, true));
				TRACE('entry '.$job->getEntryId().' converted. output: '.$all_output);
				$job->setDescription( 'target: '.$target_file);
				$job->setStatus(BatchJob::BATCHJOB_STATUS_FINISHED);
				
				// FileSync - sync the download file for the converted swf
				$entry = entryPeer::retrieveByPKNoFilter($job->getEntryId());
				$sync_key = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DOWNLOAD, $file_format);
				
				if(!kFileSyncUtils::fileSync_exists($sync_key))
					kFileSyncUtils::createSyncFileForKey($sync_key, true, false);
			}
			$job->save();
		}       
	}
}

?>
