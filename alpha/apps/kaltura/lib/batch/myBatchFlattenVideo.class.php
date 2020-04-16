<?php
require_once( __DIR__ . '/myBatchBase.class.php');
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
		$job->setJobType(BatchJobType::FLATTEN);
		$job->setData($data, true);
		$job->setStatus(BatchJob::BATCHJOB_STATUS_PENDING);
		//$job->setCheckAgainTimeout(time() + 10);
		$job->setMessage('Queued');
		$job->setDescription('Queued, waiting to run');
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
		$stats = $batch_status->getDbStats( $batch_status->batch_name , BatchJobType::FLATTEN );
		$batch_status->addToPending( "DB:batch_job, type=" . BatchJobType::FLATTEN . " status=" . BatchJob::BATCHJOB_STATUS_PENDING , @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_PENDING]["count"]);
		$batch_status->addToInProc( "DB:batch_job, type=" . BatchJobType::FLATTEN . " status=" . BatchJob::BATCHJOB_STATUS_PROCESSING , @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_PROCESSING]["count"] );
		
		$batch_status->succeedded_in_period = @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_FINISHED]["count"];
		$batch_status->failed_in_period = @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_FAILED]["count"];
		
		$batch_status->last_log_time  = @$stats["log_timestamp"];
		return $batch_status; 
	}
		
	public function __construct( $script_name )
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
		$c->add(BatchJobPeer::JOB_TYPE, BatchJobType::FLATTEN);
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
						KalturaLog::debug("could not retrieve entry $entry_id , probably deleted");
						$job->save();
						continue;
					}
					
					$fileSyncKey = $entry->getSyncKey(kEntryFileSyncSubType::DOWNLOAD, $file_format);
					$fullFinalPath = kFileSyncUtils::getLocalFilePathForKey($fileSyncKey);
					$finalPathNoExt = substr($fullFinalPath, 0 , strlen($fullFinalPath)-strlen($file_format));

					kFile::fullMkdir($fullFinalPath);
					
					$wildcardFinalPath = $finalPathNoExt."*";
					$older_files = glob($wildcardFinalPath);
					foreach($older_files as $older_file)
					{
						KalturaLog::debug("removing old file: [$older_file]");
						@unlink($older_file);
					}
					
					KalturaLog::debug("Downloading: $fullFinalPath");
					KCurlWrapper::getDataFromFile($data["serverUrl"], $fullFinalPath);
					if (!file_exists($fullFinalPath))
					{
						KalturaLog::debug("file doesnt exist: ". $data["serverUrl"]);
						$job->setDescription("file doesnt exist: ". $data["serverUrl"]);
						$job->setStatus(BatchJob::BATCHJOB_STATUS_FAILED);
					}
					else if (filesize($fullFinalPath) < 100000)
					{
						@unlink($fullFinalPath);
						KalturaLog::debug("file too small: ". $data["serverUrl"]);
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
						
						KalturaLog::debug("Deleting: ".$data["deleteUrl"]);
						kFile::downloadUrlToString($data["deleteUrl"]);
						
						myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE, $entry );
						
						$job->setStatus(BatchJob::BATCHJOB_STATUS_FINISHED);
						
						list($rootPath, $filePath) = kFileSyncUtils::getLocalFilePathArrForKey($fileSyncKey);
						$fileFullPath = $rootPath . $filePath; 
						if (file_exists($fileFullPath))
						{
							try {
								kFileSyncUtils::createSyncFileForKey($rootPath, $filePath, $fileSyncKey);
							}
							catch(Exception $ex) // hack for the case where the file sync already exists and we re-flattened a mix
							{
								KalturaLog::debug ( "ignore ERROR: " . $ex->getMessage() );
							}
						}							
						else
							KalturaLog::debug("The file [$fileFullPath] doesn't exists, not creating FileSync");
					}
					$job->save();
				}
			}	
			catch ( Exception $ex )
			{
				KalturaLog::debug ( "ERROR: " . $ex->getMessage() );
				self::initDb( true );
				self::failed();
			}
			
			if ( $temp_count == 0 )
			{
				KalturaLog::debug ( "Ended conversion. sleeping for a while (" . $sleep_between_cycles .
				" seconds). Will write to the log in (" . ( $sleep_between_cycles * $number_of_times_to_skip_writing_sleeping ) . ") seconds" );
			}

			$temp_count++;
			if ($temp_count >= $number_of_times_to_skip_writing_sleeping ) $temp_count = 0;
		
		}
	}
}

?>
