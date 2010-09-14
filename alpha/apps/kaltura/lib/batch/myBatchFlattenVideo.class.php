<?php
require_once( 'myBatchBase.class.php');
require_once( 'myContentStorage.class.php');
require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'lib/model/BatchJob.php');

class myBatchFlattenClient
{
	/**
	 * @param string $puser_id
	 * @param string $entry
	 * @param string $version
	 * @param string $file_format
	 * @return BatchJob
	 */
	public static function addJob($puser_id, $entry, $version, $file_format)
	{
		$entryId = $entry->getId();
		$entryIntId = $entry->getIntId();
		$entryVersion = $version ? $version : $entry->getVersion();

		if ( $entry )
		{
			$partner = $entry->getPartner ();
			$email = $partner->getAdminEmail();
		}
		
		$data = json_encode(array(
		'puserId' => $puser_id,
		'entryId' => $entryId,
		'entryIntId' => $entryIntId,
		'entryVersion' => $entryVersion,
		'fileFormat' => $file_format,
		'email' => $email,
		//'serverUrl' => "http://xp/final/$entryId_$entryVersion.avi",
		//'deleteUrl' => "http://xp:1234/DeleteMovie/$entryId_$entryVersion.avi"
		));
		
		$job = new BatchJob();
		$job->setJobType(BatchJob::BATCHJOB_TYPE_FLATTEN);
		$job->setData($data, true);
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
		
		return $job;
	}
}

class myBatchFlattenServer extends myBatchBase
{
	const KALTURAS_FLATTEN_READY = 60;
	
	public static function getBatchStatus( $args )	
	{	
		$batch_status = new batchStatus();
		$batch_status->batch_name = $args[0];
		$stats = $batch_status->getDbStats( $batch_status->batch_name , BatchJob::BATCHJOB_TYPE_FLATTEN );
		$batch_status->addToPending( "DB:batch_job, type=" . BatchJob::BATCHJOB_TYPE_FLATTEN . " status=" . BatchJob::BATCHJOB_STATUS_PENDING , @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_PENDING]["count"]);
		$batch_status->addToInProc( "DB:batch_job, type=" . BatchJob::BATCHJOB_TYPE_FLATTEN . " status=" . BatchJob::BATCHJOB_STATUS_PROCESSING , @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_PROCESSING]["count"] );
		
		$batch_status->succeedded_in_period = @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_FINISHED]["count"];
		$batch_status->failed_in_period = @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_FAILED]["count"];
		
		$batch_status->last_log_time  = @$stats["log_timestamp"];
		return $batch_status; 
	}
		
	public function myBatchFlattenServer( $script_name )
	{
		$this->script_name = $script_name;
		$this->register( $script_name );
		
		SET_CONTEXT ( "FS");
		
		$MAX_ITERATIONS_DUE_TO_PROPEL_MEMORY_LEAK = 10000000;
		
		self::initDb();

		list ( $sleep_between_cycles ,
		$number_of_times_to_skip_writing_sleeping ) = self::getSleepParams( 'app_flatten_' );

		$last_worker_count = 0;
		$iteration = 0;
		
		$c = new Criteria();
		$currentDc = kDataCenterMgr::getCurrentDc();
		$c->add(BatchJobPeer::DC, kDataCenterMgr::getCurrentDcId() );
		$c->add(BatchJobPeer::JOB_TYPE, BatchJob::BATCHJOB_TYPE_FLATTEN);
		$c->add(BatchJobPeer::STATUS, BatchJob::BATCHJOB_STATUS_PROCESSED);

		$temp_count = 0;
		while(1)
		{
			self::exitIfDone();
			try 
			{
				sleep($sleep_between_cycles);
				
				$jobs = BatchJobPeer::doSelect($c);
				
				foreach($jobs as $job)
				{
					$data = json_decode($job->getData(true), true);
					
					$entry_id = $data['entryId'];
					$entry_int_id = $data['entryIntId'];
					$entry_version = $data['entryVersion'];
					$file_format = $data['fileFormat'];

					$entry = entryPeer::retrieveByPK($entry_id);
					if(!$entry)
					{
						// entry is probably deleted if it is not returned from retrieveByPK
						// close job as failed
						$job->setStatus(BatchJob::BATCHJOB_STATUS_FAILED);
						$job->setDescription("could not retrieve entry, probably deleted");
						TRACE("could not retrieve entry $entry_id , probably deleted");
						$job->save();
						continue;
					}
					
					$fileSyncKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DOWNLOAD, $file_format);
					$fullFinalPath = kFileSyncUtils::getLocalFilePathForKey($fileSyncKey);
					$finalPathNoExt = substr($fullFinalPath, 0 , strlen($fullFinalPath)-strlen($file_format));

					myContentStorage::fullMkdir($fullFinalPath);
					
					$wildcardFinalPath = $finalPathNoExt."*";
					$older_files = glob($wildcardFinalPath);
					foreach($older_files as $older_file)
					{
						TRACE("removing old file: [$older_file]");
						@unlink($older_file);
					}
					
					TRACE("Downloading: $fullFinalPath");
					kFile::downloadUrlToFile($data["serverUrl"], $fullFinalPath);
					if (!file_exists($fullFinalPath))
					{
						TRACE("file doesnt exist: ". $data["serverUrl"]);
						$job->setDescription("file doesnt exist: ". $data["serverUrl"]);
						$job->setStatus(BatchJob::BATCHJOB_STATUS_FAILED);
					}
					else if (filesize($fullFinalPath) < 100000)
					{
						@unlink($fullFinalPath);
						TRACE("file too small: ". $data["serverUrl"]);
						$job->setDescription("file too small: ". $data["serverUrl"]);
						$job->setStatus(BatchJob::BATCHJOB_STATUS_FAILED);
					}
					else
					{
						if ($data['email'])
						{
							$downloadLink = $entry->getDownloadUrl().'/format/'.$file_format;
							kJobsManager::addMailJob(
								null, 
								$entry_id,
								$entry->getPartnerId(),
								self::KALTURAS_FLATTEN_READY, 
								kMailJobData::MAIL_PRIORITY_NORMAL, 
								kConf::get ( "batch_flatten_video_sender_email" ), 
								kConf::get ( "batch_flatten_video_sender_name" ), 
								$data['email'], 
								array($data['email'] , $downloadLink));
						}
						
						TRACE("Deleting: ".$data["deleteUrl"]);
						kFile::downloadUrlToString($data["deleteUrl"]);
						
						myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE, $entry );
						
						$job->setStatus(BatchJob::BATCHJOB_STATUS_FINISHED);
						
						$filePath = kFileSyncUtils::getLocalFilePathForKey($fileSyncKey);
						if (file_exists($filePath))
						{
							try {
								kFileSyncUtils::createSyncFileForKey($fileSyncKey);
							}
							catch(Exception $ex) // hack for the case where the file sync already exists and we re-flattened a mix
							{
								TRACE ( "ignore ERROR: " . $ex->getMessage() );
							}
						}							
						else
							TRACE("The file [$filePath] doesn't exists, not creating FileSync");
					}
					$job->save();
				}
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
		
		}
	}
}

?>