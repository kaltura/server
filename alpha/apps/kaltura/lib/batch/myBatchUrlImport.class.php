<?php
require_once( 'myBatchBase.class.php');
require_once( 'myContentStorage.class.php');
require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'lib/model/BatchJob.php');

class myBatchUrlImportClient
{
	public function myBatchUrlImportClient()
	{
//		$databaseManager = new sfDatabaseManager();
//		$databaseManager->initialize();
	}

	public function addJob($entryId, $sourceUrl, $destFile)
	{
		$job = new BatchJob();
		$currentDc = kDataCenterMgr::getCurrentDc();
		$job->setDc($currentDc["name"]);
		$job->setJobType(BatchJob::BATCHJOB_TYPE_IMPORT);
		$job->setData(serialize(array(
		'entryId' => $entryId,
		'sourceUrl' => $sourceUrl,
		'destFile' => $destFile)));
		$job->setStatus(BatchJob::BATCHJOB_STATUS_PENDING);
		$job->setCheckAgainTimeout(time() + 10);
		$job->setProgress(0);
		$job->setMessage('Queued');
		$job->setDescription('Queued, waiting to run');
		$job->setUpdatesCount(0);
		$job->setEntryId( $entryId );
		
		$entry = entryPeer::retrieveByPK( $entryId);
		if ( $entry )
		{
			$job->setPartnerId( $entry->getPartnerId() );
			$job->setSubpId ( $entry->getSubpId());
		}

		$job->save();
	}
}

class BJImportURL
{
	const HTTP_USER_AGENT = "\"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.6) Gecko/2009011913 Firefox/3.0.6\"";
	
	const PARSE_URL = 1;
	const PARSE_URL_PROGRESS = 2;
	const DOWNLOAD_HEADER = 3;
	const DOWNLOAD_HEADER_PROGRESS = 4;
	const DOWNLOAD_FILE = 5;
	const DOWNLOAD_FILE_PROGRESS = 6;
	const MOVE_FILE = 7;

	public $job;
	public $handle;

	private $state;
	private $fileSize;
	private $data;
	private $entryId;
	private $destFile;
	private $sourceUrl;

	public function BJImportURL($_job)
	{
		register_shutdown_function(array($this, "cleanup"));
		$this->job = $_job;
	}

	public function cleanup()
	{
		if ($this->handle && is_resource($this->handle))
		{
			proc_terminate($this->handle);
			proc_close($this->handle);
			$this->handle = null;
		}
	}
	
	public function __destruct()
	{
		$this->cleanup();
	}
	
	public function spawnProcess($cmdLine)
	{
		$descriptorspec = array(
		0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
		1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
		2 => array("pipe", "w") // stderr is a pipe to write to
		);

		$handle = proc_open($cmdLine, $descriptorspec, $pipes);

		if (is_resource($handle))
		{
			return $handle;
		}
		else
		return false;
	}

	public function runNewJob()
	{
		$this->data = unserialize($this->job->getData());

		$this->entryId = $this->data['entryId'];
		$this->sourceUrl = $this->data['sourceUrl'];
		$this->destFile = $this->data['destFile'];
		$this->state = BJImportURL::PARSE_URL;
		$this->job->setMessage('Parsing url');
		$this->job->setDescription('starting at BJImportURL::PARSE_URL');
		$this->job->save();

		return true;
	}

	function setFailure($job, $description)
	{
		$job->setStatus(BatchJob::BATCHJOB_STATUS_FAILED);
		$job->setMessage('FAILED');
		$job->setDescription($description);
		$job->save();
		BatchJob::addIndicator( $job->getId() );
		
		$entry = $job->getEntry();
		if ($entry)
		{
			$entry->setStatus(entry::ENTRY_STATUS_ERROR_CONVERTING);
			$entry->save();
			myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE , $entry );			
		}
	}
	
	static function safe_unlink($filename)
	{
		if (file_exists($filename))
			unlink($filename);
	}
	
	function monitorProgress()
	{
		$job = BatchJobPeer::retrieveByPK($this->job->getId());

		if ($job->getAbort())
		{
			$job->setStatus(BatchJob::BATCHJOB_STATUS_ABORTED);
			$job->setMessage('ABORTED');
			$job->setDescription($job->getDescription() + ': ABORTED');
			$job->save();
			BatchJob::addIndicator( $job->getId() );
			
			$this->cleanup();
			return true;
		}

		while(true)
		{
			if ($this->handle)
			{
				$status = proc_get_status($this->handle);
				if ($status == false) // process disappeared
				{
					$job->setStatus(BatchJob::BATCHJOB_STATUS_FAILED);
					$job->setMessage('FAILED');
					$job->setDescription($job->getDescription() . ': TERMINATED');
					$job->save();
					BatchJob::addIndicator( $job->getId() );
					proc_close($this->handle);
					$this->handle = null;
					return true;
				}
			}

			if ($this->state == BJImportURL::PARSE_URL)
			{
				//"http://www.youtube.com/watch?v=".$objectId
				if (strpos($this->sourceUrl, 'youtube.com') !== FALSE)
				{
					self::safe_unlink($this->destFile);
					
					//http://www.youtube.com/watch?v=0cSuidxE8os
					$this->objectId = "";
					if (preg_match("/watch\?v=([^&]*)/", $this->sourceUrl, $videoIdMatch))
					{
						$this->objectId = $videoIdMatch[1];
					}
					else if (preg_match("/video_id=(.*?)&t=/", $this->sourceUrl, $videoIdMatch))
					{
						$this->objectId = $videoIdMatch[1];
					}
					
					if ($this->objectId)
					{
						$this->sourceUrl = "http://www.youtube.com/watch?v=".$this->objectId;
				
						// make HEAD request to flv file size
						$cmdLine = kConf::get( "bin_path_curl" ) . ' -L -o"'.$this->destFile.'" "'.$this->sourceUrl.'"';
						echo "$cmdLine\n";
						
						$this->handle = $this->spawnProcess($cmdLine);
						if ($this->handle)
						{
							$this->job->setMessage('Downloading video html page');
							$this->job->setDescription('starting at BJImportURL::PARSE_URL');
							$job->save();
		
							$this->state = BJImportUrl::PARSE_URL_PROGRESS;
							continue;
							
						}
					}
	
					$this->setFailure($job, 'failed to parse url');
					return true;
				}
				
				// no parsing is required, just download the url
				$this->state = BJImportURL::DOWNLOAD_HEADER;
				$this->job->save();
				continue;
			}
			else if ($this->state == BJImportURL::PARSE_URL_PROGRESS)
			{
				if ($status['running']) // if process is still running return
					return false;

				proc_close($this->handle);
				$this->handle = null;

				$htmlPage = file_get_contents($this->destFile);
				
				if (preg_match('/swfArgs.*?\{.*?, "t":\s*"(.*?)"/s', $htmlPage, $timestampMatch))
				//if (preg_match('/swfArgs.*?\{.*?,t:\'(.*?)\'/', $htmlPage, $timestampMatch))
				{
					$fmt_url = "";
					//"fmt_map": "35/640000/9/0/115,18/512000/9/0/115,34/0/9/0/115,5/0/7/0/0"
					if (preg_match('/swfArgs.*?\{.*?, "fmt_map":\s*"(.*?)"/s', $htmlPage, $fmt_map))
					{
						$fmt_map_array = explode(",", $fmt_map[1]);
						$fmt_details = explode("/", $fmt_map_array[0]);
						//print_r($fmt_map_array);
						//echo "fmt: ".$fmt_details[0]."\n";
						
						if ($fmt_details[0])
							$fmt_url = "&fmt=".$fmt_details[0];
					}
				
					//var swfArgs = {hl:'en',video_id:'F924-D-g5t8',l:'24',t:'OEgsToPDskL9BIntclUB-PPzMEpVQKo8',sk:'xXvbHpmFGQKgv-b9__DkgwC'};
					$tId = $timestampMatch[1];
					$this->sourceUrl = "http://youtube.com/get_video?video_id=".$this->objectId."&t=$tId$fmt_url";
					$this->state = BJImportURL::DOWNLOAD_HEADER;
					$this->job->save();
					continue;
				}

				$this->setFailure($job, 'failed to fetch parse youtube html page');
				return true;
			}
			else if ($this->state == BJImportURL::DOWNLOAD_HEADER)
			{
				self::safe_unlink($this->destFile);
		
				// make HEAD request to flv file size
				$url = str_replace ( " " , "%20" , $this->sourceUrl );
				
				// Gonen (2009-11-22): bugfix[#2903]
				//   make sure [] are encoded since they are part of curl syntax for range-download.
				//   for example:
				// 	ftp://ftp.numericals.com/file[1-100].txt
				//   will download file1.txt, file2.txt, ..., file99.txt, file100.txt
				$url = str_replace ( "[" , urlencode("[") , $url );
				$url = str_replace ( "]" , urlencode("]") , $url ); 
				
				$cmdLine = kConf::get( "bin_path_curl" ) .' -A ' . self::HTTP_USER_AGENT . ' -L -I -o"'.$this->destFile.'" "'.$url.'"';
				echo "$cmdLine\n";
				
				$this->handle = $this->spawnProcess($cmdLine);
				if ($this->handle)
				{
					$this->job->setMessage('Downloading file header');
					$this->job->setDescription('starting at BJImportURL::DOWNLOAD_HEADER');
					$job->save();

					$this->state = BJImportUrl::DOWNLOAD_HEADER_PROGRESS;
					continue;
					
				}

				$this->setFailure($job, 'failed to fetch http header. state: BJImportURL::DOWNLOAD_HEADER, command: '.$cmdLine);
				TRACE ( 'failed to fetch header, state is BJImportURL::DOWNLOAD_HEADER. cmdLine is: '.$cmdLine );
				return true;
			}
			else if ($this->state == BJImportURL::DOWNLOAD_HEADER_PROGRESS)
			{
				if ($status['running']) // if process is still running return
					return false;

				proc_close($this->handle);
				$this->handle = null;

TRACE ( __METHOD__ . " " . $this->destFile );
				
				$header = file_get_contents($this->destFile);

				if (preg_match_all('/Content-Length:\s*(\d+)\s*\r\n/', $header, $contentLengthMatch))
				{
					$this->fileSize = end($contentLengthMatch[1]);
					$this->state = BJImportUrl::DOWNLOAD_FILE;
					continue;
				}

				$this->setFailure($job, 'failed to fetch http header. state: DOWNLOAD_HEADER_PROGRESS, failed to match content-length '.$header);
				TRACE ( 'failed to fetch header, state is BJImportURL::DOWNLOAD_HEADER_PROGRESS. failed to match content-length '.$header );
				return true;
			}
			else if ($this->state == BJImportURL::DOWNLOAD_FILE)
			{
				self::safe_unlink($this->destFile);

				$url = str_replace ( " " , "%20" , $this->sourceUrl );
				
				// Gonen (2009-11-22): bugfix[#2903]
				//   make sure [] are encoded since they are part of curl syntax for range-download.
				//   for example:
				// 	ftp://ftp.numericals.com/file[1-100].txt
				//   will download file1.txt, file2.txt, ..., file99.txt, file100.txt
				$url = str_replace ( "[" , urlencode("[") , $url );
				$url = str_replace ( "]" , urlencode("]") , $url );
				
				$cmdLine = kConf::get( "bin_path_curl" ) . ' -A ' . self::HTTP_USER_AGENT . ' -L -o"'.$this->destFile.'" "'.$url.'"';
				echo "$cmdLine\n";

				$this->handle = $this->spawnProcess($cmdLine);
				if ($this->handle)
				{
					$job->setMessage('Downloading file (size='.$this->fileSize.')');
					$job->setDescription('switching to BJImportUrl::DOWNLOAD_FILE_PROGRESS');
					$job->save();

					$this->state = BJImportUrl::DOWNLOAD_FILE_PROGRESS;
					continue;
				}
				
				$this->setFailure($job, 'failed to download file');
			}
			else if ($this->state == BJImportURL::DOWNLOAD_FILE_PROGRESS)
			{
				clearstatcache();
				
				if (file_exists($this->destFile))
					$currentSize = filesize($this->destFile);
				else
					$currentSize = 0;

				$progress = $this->fileSize ? ($currentSize * 100 / $this->fileSize) : 100;
				
				$job->setProgress($progress);
				$job->setUpdatesCount($job->getUpdatesCount() + 1);
				$job->save();

				if (!$status['running']) // completed download
				{
					proc_close($this->handle);
					$this->handle = null;
					
					if ($this->fileSize != $currentSize)
					{
						$this->setFailure($job, 'partial download. filesize: '.$this->fileSize.' , currentSize: '.$currentSize);
						return true;
					}
						
					$this->state = BJImportUrl::MOVE_FILE;
					$job->setStatus(BatchJob::BATCHJOB_STATUS_MOVEFILE);
					$job->setMessage('succesfully fetched file');
					$job->setDescription('succesfully fetched file');
					$job->save();
						
					continue;
				}
				
				echo "(" . $job->getId() . ") ({$job->getEntryId()}) $currentSize out of ".$this->fileSize . "({$progress} %)\n";
			}
			else if ($this->state == BJImportURL::MOVE_FILE)
			{
				if ($this->entryId)
				{
					$entry = entryPeer::retrieveByPK($this->entryId);
					if ($entry)
					{
						$entry->setStatus(entry::ENTRY_STATUS_PRECONVERT);
						$entry->save();
					}
				}
				$targetFileName = basename($this->destFile);
if ( false /* old conversion */)
{				
				$preConvPath = myContentStorage::getFSContentRootPath().'/content/preconvert/';
				myContentStorage::moveFile($this->destFile, $preConvPath."data/".$targetFileName, true);

				$signalFilePath = $preConvPath."files/".$targetFileName;
				myContentStorage::fullMkdir($signalFilePath);
				touch($signalFilePath);
}
else
{
				$preConvPath = myContentStorage::getFSContentRootPath (). "/content/new_preconvert";
				$to_data = $preConvPath . "/$targetFileName" ;	
				myContentStorage::moveFile($this->destFile, $to_data , true);			
				touch ( $to_data . ".indicator" );
}				
					
				$job->setStatus(BatchJob::BATCHJOB_STATUS_FINISHED);
				$job->setMessage('succesfully moved file');
				$job->setDescription('succesfully moved file');
				$job->save();
				BatchJob::addIndicator( $job->getId() );

				return true;
			}
				
			return false;
		}
	}
}

class myBatchUrlImportServer extends myBatchBase
{
	// handle 10 files at a time
	const MAX_FILES_TO_HANDLE = 30;
	const SLEEP_INTERVAL_BETWEEN_IMPORTS = 1;

	protected $workers = array();
	protected $connection;
	
	public function runNewJob()
	{
		
		$job = null;
		//if ( BatchJob::isIndicatorSet() )
		{
			TRACE ( "Indicator exists - removing it and checking DB" );
			// in this case - remove the indicator and give the DB a try...
			BatchJob::removeIndicator();
			
			// count running import jobs for each partner 
			$running_partners = array();
			foreach($this->workers as $worker)
			{
				$partner_id = $worker->job->getPartnerId();
				@$running_partners[$partner_id]++;
			}
			
			// filter out partners with more than 3 jobs
			$quota_partners = array();
			$orderby_partners = array();
			foreach($running_partners as $partner_id => $count)
			{
				//$partner = PartnerPeer::retrieveByPK($partner_id);
				//$getMaxConcurrentImports = $partner->getMaxConcurrentImports();
				
				$getMaxConcurrentImports = 5;
				
				if ($count >= $getMaxConcurrentImports) // real limit of set by partner
					$quota_partners[] = $partner_id;
				if ($count >= 3) // limit set by kaltura to prevent starvation
					$orderby_partners[] = $partner_id;
			}
			
			$currentDcId = kDataCenterMgr::getCurrentDcId();
			$query = "SELECT ".BatchJobPeer::ID." FROM ".BatchJobPeer::TABLE_NAME. " WHERE ".
				BatchJobPeer::DC."='".$currentDcId."' AND ".
				BatchJobPeer::STATUS."=".BatchJob::BATCHJOB_STATUS_PENDING." AND ".
				BatchJobPeer::JOB_TYPE."=".BatchJob::BATCHJOB_TYPE_IMPORT." ".
				(count($quota_partners) ? (" AND ".BatchJobPeer::PARTNER_ID." NOT IN (".implode(",", $quota_partners).") ") : "").
				" ORDER BY ".
				(count($orderby_partners) ? (BatchJobPeer::PARTNER_ID." IN (".implode(",", $orderby_partners)."), ") : "").
				BatchJobPeer::CREATED_AT. " LIMIT 1";
				
			$statement = $this->connection->prepareStatement($query);
		    $resultset = $statement->executeQuery();
		    while ($resultset->next())
		    {
				$job = BatchJobPeer::retrieveByPK($resultset->getInt('ID'));
		    }
		    
			/*
			$c = new Criteria();
			$c->addAscendingOrderByColumn(BatchJobPeer::CREATED_AT);
			$c->add(BatchJobPeer::STATUS, BatchJob::BATCHJOB_STATUS_PENDING, Criteria::EQUAL);
			$c->add(BatchJobPeer::JOB_TYPE , BatchJob::BATCHJOB_TYPE_IMPORT, Criteria::EQUAL); // handle only jobs of type import
			
			// the partners used above wont run a new job
			$c->add(BatchJobPeer::PARTNER_ID , $quota_partners, Criteria::NOT_IN); // ignore partners with more than 3 running imports
			
			$job = BatchJobPeer::doSelectOne($c);
			
			$c->clear();
			*/
		}

		if ($job)
		{
			echo "jobid: ".$job->getId(). " msg: ".$job->getMessage()."\n";

			$job->setStatus(BatchJob::BATCHJOB_STATUS_PROCESSING);
			$job->save();
				
			$worker = new BJImportUrl($job);
			if (!$worker->runNewJob())
			{
				$job->setStatus(BatchJob::BATCHJOB_STATUS_FAILED);
				$job->save();
			}
			else
			{
				$this->workers[] = $worker;
				return true;
			}
		}

		return false;
	}

	public static function getBatchStatus( $args )	
	{	
		$batch_status = new batchStatus();
		$batch_status->batch_name = $args[0];
		$stats = $batch_status->getDbStats( $batch_status->batch_name , BatchJob::BATCHJOB_TYPE_IMPORT );
		$batch_status->addToPending( "DB:batch_job, type=" . BatchJob::BATCHJOB_TYPE_IMPORT . " status=" . BatchJob::BATCHJOB_STATUS_PENDING , @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_PENDING]["count"]);
		$batch_status->addToInProc( "DB:batch_job, type=" . BatchJob::BATCHJOB_TYPE_IMPORT . " status=" . BatchJob::BATCHJOB_STATUS_PROCESSING , @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_PROCESSING]["count"] ); 
		
		$batch_status->succeedded_in_period = @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_FINISHED]["count"];
		$batch_status->failed_in_period = @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_FAILED]["count"];
		
		$batch_status->last_log_time  = @$stats["log_timestamp"];
		
		return $batch_status; 
	}
	
	
	public function myBatchUrlImportServer( $script_name )
	{
		$this->script_name = $script_name;
		$this->register( $script_name );
		
		$MAX_ITERATIONS_DUE_TO_PROPEL_MEMORY_LEAK = 10000000;
		
		self::initDb();

		TRACE ( "Checking for BATCHJOB_STATUS_PROCESSING");
		
		$this->connection = Propel::getConnection();
		
		list ( $sleep_between_cycles ,
		$number_of_times_to_skip_writing_sleeping ) = self::getSleepParams( 'app_importer_' );

		// set all the BatchJob::BATCHJOB_STATUS_PROCESSING jobs to be PENDING
		$proc_c =  new Criteria();
		$currentDc = kDataCenterMgr::getCurrentDc();
		$proc_c->add(BatchJobPeer::DC, kDataCenterMgr::getCurrentDcId() );
		$proc_c->add(BatchJobPeer::JOB_TYPE, BatchJob::BATCHJOB_TYPE_IMPORT);
		$proc_c->add(BatchJobPeer::STATUS, 
			array ( BatchJob::BATCHJOB_STATUS_PROCESSING , BatchJob::BATCHJOB_STATUS_MOVEFILE ) , Criteria::IN );
		$proc_c->setLimit( 50 );
		$proc_jobs = BatchJobPeer::doSelect( $proc_c );
		$proc_c->clear();
		
		 
		if ( $proc_jobs  )
		{
			TRACE ( "Moving all BATCHJOB_STATUS_PROCESSING jobs to be BATCHJOB_STATUS_PENDING");
			foreach ( $proc_jobs  as $job )
			{
				$job->setStatus ( BatchJob::BATCHJOB_STATUS_PENDING );
				$job->setProgress ( 0 ) ;
				$job->save();
			}
		} 
		else
		{
			TRACE ( "No BATCHJOB_STATUS_PROCESSING jobs" );
		}	
		
			 

		$temp_count = 0;

		// will be used to detect the change between 1 worker to 0 worker - 
		// something worth logging !
		$last_worker_count = 0;
		$iteration = 0;
		while(1)
		{
			self::$s_pending_tasks = count($this->workers); 
			self::exitIfDone();
			try 
			{
				sleep(myBatchUrlImportServer::SLEEP_INTERVAL_BETWEEN_IMPORTS);
					
				$worker_count =  count($this->workers);
				if ( $worker_count > 0 || $temp_count == 0 || $last_worker_count != $worker_count )
				{
					TRACE (  "count workers: ". $worker_count );
				}
				$last_worker_count = $worker_count;
				
				if ($iteration < $MAX_ITERATIONS_DUE_TO_PROPEL_MEMORY_LEAK )
				{
					$iteration++;
					while(count($this->workers) < myBatchUrlImportServer::MAX_FILES_TO_HANDLE)
					{
						if ( $this->shouldProceed() )
						{
							// dont start any new threads if should not proceed
							if (!$this->runNewJob())
								break;
						}
						else
						{
							break; // exit this loop without actually running anything new
						}
					}
				}
				
				foreach($this->workers as $index => $worker)
				{
					if ($worker->monitorProgress())
					{
						unset($this->workers[$index]);
					}
				}
					
				// if no workers are running and we reached xxx iteration exit gracefully because of memory leak in propel...
				if (count($this->workers) == 0 && $iteration > $MAX_ITERATIONS_DUE_TO_PROPEL_MEMORY_LEAK )
				{
					TRACE ( "Exiting after $MAX_ITERATIONS_DUE_TO_PROPEL_MEMORY_LEAK - MAX_ITERATIONS_DUE_TO_PROPEL_MEMORY_LEAK");
					throw new Exception ("Exiting after $MAX_ITERATIONS_DUE_TO_PROPEL_MEMORY_LEAK - MAX_ITERATIONS_DUE_TO_PROPEL_MEMORY_LEAK");
				}
				
				if ( $temp_count == 0 )
				{
					TRACE ( "Ran all import jobs. sleeping for a while (" . $sleep_between_cycles .
					" seconds). Will write to the log in (" . ( $sleep_between_cycles * $number_of_times_to_skip_writing_sleeping ) . ") seconds" );
				}
					
				$temp_count++;
				if ($temp_count >= $number_of_times_to_skip_writing_sleeping ) $temp_count = 0;
					
				self::succeeded();
				sleep ( $sleep_between_cycles );
			}	
			catch ( Exception $ex )
			{
				TRACE ( "ERROR: " . $ex->getMessage() );
				self::initDb( true );
				self::failed();
			}
		}
	}
}

?>