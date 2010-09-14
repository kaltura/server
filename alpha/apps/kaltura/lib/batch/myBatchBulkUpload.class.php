<?php
require_once('myBatchBase.class.php');
require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'lib/model/BatchJob.php');
require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'lib/model/entry.php');
define('MODULES' , SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR);

class myBatchBulkUpload extends myBatchBase
{
	const SLEEP_TIME = 5;
	
	public static function getBatchStatus( $args )	
	{	
		$batch_status = new batchStatus();
		$batch_status->batch_name = $args[0];
		$stats = $batch_status->getDbStats( $batch_status->batch_name , BatchJob::BATCHJOB_TYPE_BULKUPLOAD );
		$batch_status->addToPending( "DB:batch_job, type=" . BatchJob::BATCHJOB_TYPE_BULKUPLOAD . " status=" . BatchJob::BATCHJOB_STATUS_PENDING , @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_PENDING]["count"]);
		$batch_status->addToInProc( "DB:batch_job, type=" . BatchJob::BATCHJOB_TYPE_BULKUPLOAD . " status=" . BatchJob::BATCHJOB_STATUS_PROCESSING , @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_PROCESSING]["count"] );
		$batch_status->succeedded_in_period = @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_FINISHED]["count"];
		$batch_status->failed_in_period = @$stats["full_stats"][BatchJob::BATCHJOB_STATUS_FAILED]["count"];
		
		 
		$batch_status->last_log_time  = @$stats["log_timestamp"];
		return $batch_status; 
	}

	
	public function myBatchBulkUpload( $script_name )
	{
		$this->script_name = $script_name;
		$this->register( $script_name );
		
		self::initDb();

		TRACE("Starting bulk upload batch job");
					
		$state = BatchBulkUploadStates::VERIFY_CSV;
		while(1)
		{
			self::exitIfDone();
			TRACE("");
			switch($state)
			{
				case BatchBulkUploadStates::VERIFY_CSV:
					$this->handleVerifyCSV();
					$nextState = BatchBulkUploadStates::PARSE_CSV;
					break;
				case BatchBulkUploadStates::PARSE_CSV:
					$this->handleParseCSV();
					$nextState = BatchBulkUploadStates::ADD_ENTRIES;
					break;
				case BatchBulkUploadStates::ADD_ENTRIES:
					$this->handleAddEntries();
					$nextState = BatchBulkUploadStates::VERIFY_CSV;
					break;
			}
			
			$state = $nextState;
			
			// sleep
			sleep(self::SLEEP_TIME);
		}
	}
	
	public function handleVerifyCSV() 
	{
		$error = "";
		$c = new Criteria();
		$currentDc = kDataCenterMgr::getCurrentDc();
		$c->add(BatchJobPeer::DC, kDataCenterMgr::getCurrentDcId() );
		$c->addAnd(BatchJobPeer::JOB_TYPE, BatchJob::BATCHJOB_TYPE_BULKUPLOAD);
		$c->addAnd(BatchJobPeer::STATUS, BatchJob::BATCHJOB_STATUS_PENDING);
		$jobs = BatchJobPeer::doSelect($c);
		TRACE("handleVerifyCSV - Number of jobs: " . count($jobs));
		foreach($jobs as $job)
		{
			$jobData = unserialize($job->getData());
			$syncKey = $job->getSyncKey(BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADCSV);
			$fileLocation = kFileSyncUtils::getReadyLocalFilePathForKey($syncKey);
			if (!file_exists($fileLocation))
			{
				$error = "CVS file was not found";
			}
			else 
			{
				$fileHandle = fopen($fileLocation, "r");
				$lineNumber = 0;
				while($values = fgetcsv($fileHandle))
				{
					$lineNumber++;
					
					if (count($values) != 5) 
					{
						$error = "Number of values on line " . $lineNumber . " is not right";
						break;										
					}
					
					$url = trim($values[3]);
					
					/* removed the validatiod
					if (!preg_match("/^[a-zA-Z]+[:\/\/]+(\w+:{0,1}\w*@)?[A-Za-z0-9\-_]+\\.+[A-Za-z0-9\.\/%&=\?\-_:]+$/i", $url)) 
					{
						$error = "Url on line " . $lineNumber . " is not valid"; 
						break;
					}
					*/
				}
				
				fclose($fileHandle);
			}
			
			if (strlen($error) > 0)
			{
				$job->setStatus(BatchJob::BATCHJOB_STATUS_ABORTED);
				$jobData["error"] = $error;
				$job->setData(serialize($jobData));
				$job->save();
			}
			else
			{
				$job->setStatus(BatchJob::BATCHJOB_STATUS_QUEUED);
				$job->save();
			}
		}
	}
	
	public function handleParseCSV()
	{
		$c = new Criteria();
		$currentDc = kDataCenterMgr::getCurrentDc();
		$c->add(BatchJobPeer::DC, kDataCenterMgr::getCurrentDcId() );
		$c->addAnd(BatchJobPeer::JOB_TYPE, BatchJob::BATCHJOB_TYPE_BULKUPLOAD);
		$c->addAnd(BatchJobPeer::STATUS, BatchJob::BATCHJOB_STATUS_QUEUED);
		$jobs = BatchJobPeer::doSelect($c);
		TRACE("handleParseCSV - Number of jobs: " . count($jobs));
		foreach($jobs as $job)
		{
			$jobData = unserialize($job->getData());
			$partnerId = $job->getPartnerId();
			$jobId = $job->getId();
			
			// open the csv file
			$syncKey = $job->getSyncKey(BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADCSV);
			$fileLocation = kFileSyncUtils::getReadyLocalFilePathForKey($syncKey);
			$fileHandle = fopen($fileLocation, "r");
			
			$syncKey = $job->getSyncKey(BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADLOG);
			kFileSyncUtils::file_put_contents($syncKey, "");
			$logFile = kFileSyncUtils::getLocalFilePathForKey($syncKey);
			
			// open the log file
			$logFileHandle = fopen($logFile, "w");

			$linesCount = 0;
			while($values = fgetcsv($fileHandle))
			{
				$linesCount++;
				
				$title 			= trim($values[0]);
				$description	= trim($values[1]);
				$tags 			= trim($values[2]);
				$url 			= trim($values[3]);
				$contentType 	= trim($values[4]);
				
				// our csv format is "title,tags,url,kaltura entry id,status"  
				$logLine = array($title, $description, $tags, $url, $contentType, 0, "Queued");
				fputcsv($logFileHandle, $logLine);
			}
			
			fclose($fileHandle);
			fclose($logFileHandle);
			
			$jobData["numOfEntries"] = $linesCount;
			
			$job->setData(serialize($jobData));
			$job->setStatus(BatchJob::BATCHJOB_STATUS_PROCESSING);
			$job->save();
		}
	}
	
	public function handleAddEntries() 
	{
		$c = new Criteria();
		$currentDc = kDataCenterMgr::getCurrentDc();
		$c->add(BatchJobPeer::DC, kDataCenterMgr::getCurrentDcId() );
		$c->addAnd(BatchJobPeer::JOB_TYPE, BatchJob::BATCHJOB_TYPE_BULKUPLOAD);
		$c->addAnd(BatchJobPeer::STATUS, BatchJob::BATCHJOB_STATUS_PROCESSING);
		$jobs = BatchJobPeer::doSelect($c);
		TRACE("handleAddEntries - Number of jobs: " . count($jobs));
		foreach($jobs as $job)
		{
			$wereErrors = false;
			$jobData = unserialize($job->getData());
			

			// check that the job was created in the last 24 hours
			// if it was created more than 24 hours ago, we mark it as aborted
			// it is needed because if the url is not valid we won't get a status change on the entry
			$hours = (time() - $job->getCreatedAt(null)) / 60 / 60;
			
			$logSyncKey = $job->getSyncKey(BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADLOG);
			if ($hours > 24)
			{
				$jobData["error"] = "Timed out, view log for more details";
				$job->setData(serialize($jobData));
				$job->setStatus(BatchJob::BATCHJOB_STATUS_ABORTED);
				$job->save();
				TRACE('Job is timed out!');
				kFileSyncUtils::markLocalFileSyncAsReady($logSyncKey);
				continue;
			}
			
			
			
			$logFileLocation = kFileSyncUtils::getLocalFilePathForKey($logSyncKey);
			TRACE('Reading file: "' . $logFileLocation . '"');
			$logFileHandle = fopen($logFileLocation, "r+");

			$currentCsvValues = array();
			$linesCount = 0;
			$jobNotFinished = false;
			
			while($values = fgetcsv($logFileHandle))
			{
				$currentCsvValues[] = $values;
			}
			
			fclose($logFileHandle);
			
			TRACE('File was read to a memory array, handle was closed');
			
			foreach($currentCsvValues as &$values)
			{
				$linesCount++;
				
				if (count($values) == 1)
					continue;
				
				TRACE('Proccesing line number ' . $linesCount);		
					
				$title 			= $values[0];
				$description	= $values[1];
				$tags 			= $values[2];
				$url 			= $values[3];
				$contentType	= $values[4];
				$id 			= $values[5];
				$status 		= $values[6];
				
				TRACE('Status is "' . $status . '", entry #' . $id);
				
				if (strpos($status, "Failed") !== false)
				{
					$wereErrors = true;
				}
				elseif ($id) 
				{
					// check its status
					$entry = entryPeer::retrieveByPKNoFilter($id);
					if ( !$entry )
					{
						TRACE ( "Error while updating entry with id [$id] - entry does not exist" );
						continue;
					}
					switch($entry->getStatus())
					{
						case entry::ENTRY_STATUS_IMPORT:
							$this->setCSVLineStatus($values, "In proccess");
							$jobNotFinished = true;
							break;
						case entry::ENTRY_STATUS_PRECONVERT:
							$this->setCSVLineStatus($values, "Converting");
							$jobNotFinished = true;
							break;
						case entry::ENTRY_STATUS_READY:
							$this->setCSVLineStatus($values, "Ready");
							break;
						case entry::ENTRY_STATUS_MODERATE:
							$this->setCSVLineStatus($values, "Waiting for moderation");
							break;
						case entry::ENTRY_STATUS_BLOCKED:
							$this->setCSVLineStatus($values, "Blocked");
							break;
						case entry::ENTRY_STATUS_ERROR_CONVERTING:
							$this->setCSVLineStatus($values, "Error converting");
							$wereErrors = true;
							break;
						default:
							$this->setCSVLineStatus($values, "Failed");
							$wereErrors = true;
							break;
					}
				}
				else
				{
					$jobNotFinished = true;
					$partnerId = $job->getPartnerId();
					$subpId = $job->getSubpId();
					
require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'modules/partnerservices2/actions/addentryAction.class.php');

					$addEntryAction = new addentryAction();
					$params = array();
					$params["format"] = 3;
					if (!@$jobData["uid"])
						$params["uid"] = "0";
					else
						$params["uid"] = $jobData["uid"];

					$params["partner_id"] = $partnerId;
					$params["subp_id"] = $subpId;
					if ( true )
					{
						$params["kshow_id"] = -1;		// new version - smallest footprint posible
						$params["quick_edit"] = false;
					}
					else
					{
						$params["kshow_id"] = -2; 		// old version - new roughcut for every entry
					}
					$params["entry1_name"] = $title;
					$params["entry1_description"] = $description;
					$params["entry1_tags"] = $tags;
					$params["entry1_source"] = entry::ENTRY_MEDIA_SOURCE_URL;
					if (strtolower($contentType) == "image")
						$params["entry1_mediaType"] = entry::ENTRY_MEDIA_TYPE_IMAGE;
					elseif (strtolower($contentType) == "audio")
						$params["entry1_mediaType"] = entry::ENTRY_MEDIA_TYPE_AUDIO;
					else 
						$params["entry1_mediaType"] = entry::ENTRY_MEDIA_TYPE_VIDEO;
					$params["entry1_url"] = $url;
					
					if (@$jobData["profileId"])
						$params["entry1_conversionQuality"] = $jobData["profileId"];

					$addEntryAction->setInputParams($params);
					$res = unserialize($addEntryAction->internalExecute());

					if (!@$res["result"]["entries"]["entry1_"])
					{
						$wereErrors = true;
						$err = "Failed to create entry (".print_r ( $res["error"][0] , true ).")";
						$this->setCSVLineStatus($values, $err);
						TRACE($err);
					}
					else
					{
						TRACE('entry was created with id ' . $res["result"]["entries"]["entry1_"]["id"]);
						$this->setCSVLineStatus($values, "In proccess");
						$this->setCSVLineEntryId($values, $res["result"]["entries"]["entry1_"]["id"]);
					} 
				}
				
				// write the csv log file 
				// FIXME: fopen will truncate the file, if proccess dies while writing new data, old data will be lost
				$logFileHandle = fopen($logFileLocation, "w");
				foreach($currentCsvValues as $valuesTemp)
				{
					fputcsv($logFileHandle, $valuesTemp);
				}
				fclose($logFileHandle);
			}
			
			if ($jobNotFinished == false)
			{
				if ($wereErrors)
					$jobData["error"] = "View log for more details";
					
				$job->setData(serialize($jobData));
				$job->setStatus(BatchJob::BATCHJOB_STATUS_FINISHED);
				$job->save();
				
				kFileSyncUtils::markLocalFileSyncAsReady($logSyncKey);
				
				TRACE('Job is finished!');
			}
		}
	}
	
	private function setCSVLineStatus(&$values, $status)
	{
		TRACE('Status changed to "'.$status.'"');
		$values[6] = $status;
	}
	
	private function setCSVLineEntryId(&$values, $entryId)
	{
		$values[5] = $entryId;
	}
}

class BatchBulkUploadStates
{
	const VERIFY_CSV = 1;
	const PARSE_CSV = 2;	
	const ADD_ENTRIES = 3;
	const VERIFY_ENTRIES = 4;
}

?>