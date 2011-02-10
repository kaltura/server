<?php
require_once('bootstrap.php');

/**
 *
 *
 * @package Scheduler
 * @subpackage FileSyncImport
 */
class KAsyncFileSyncImport extends KBatchBase
{
	/**
	 * @return number
	 */
	public static function getType()
	{
		return KalturaBatchJobType::FILESYNC_IMPORT;
	}

	
	protected function init()
	{
		$this->saveQueueFilter(self::getType());
	}

	
	public function run($jobs = null)
	{
		KalturaLog::info("FileSyncImport batch is running");

		if($this->taskConfig->isInitOnly()) {
			return $this->init();
		}

		// no jobs given - get from server
		if(is_null($jobs)) {
			$jobs = $this->kClient->fileSyncImportBatch->getExclusiveFileSyncImportJobs($this->getExclusiveLockKey(), $this->taskConfig->maximumExecutionTime, 1, $this->getFilter());
		}
			
		KalturaLog::info(count($jobs) . " filesync import jobs to perform");

		if(!count($jobs) > 0)
		{
			// no jobs to perform
			KalturaLog::info("Queue size: 0 sent to scheduler");
			$this->saveSchedulerQueue(self::getType());
			return null;
		}

		// should we use the KAsyncFileSyncImportCloser batch worker or not ?
		$useCloser = $this->taskConfig->params->useCloser;

		foreach($jobs as &$job)
		{
			if ($useCloser)
			{
				// if closer is used, the file will be download to a temporary directory, and then moved to its final destination by the KAsyncFileSyncImportCloser batch
				if (!$job->data->tmpFilePath)
				{
					// adding temp path to the job data, so that the closer will be able to use it later
					$tmpPath = $this->getTmpPath($job->data->sourceUrl);
					if (!$tmpPath) {
						$msg = 'Error: Cannot create temporary directory for url ['.$job->data->sourceUrl.']';
						$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CANNOT_CREATE_DIRECTORY, $msg, KalturaBatchJobStatus::RETRY);				
						continue; // proceed to next job
					}
					$job->data->tmpFilePath = $tmpPath;
					$this->updateJob($job, "Temp destination set", KalturaBatchJobStatus::PROCESSING, 2, $job->data);
				}
				// destination = temporary path
				$fileDestination = $job->data->tmpFilePath;
			}
			else
			{
				// destination = final path
				$fileDestination = $job->data->destFilePath;
			}
			
			// start downoading the file to its destination (temp or final)
			$job = $this->fetchUrl($job, $job->data->sourceUrl, $fileDestination);			
		}
			
		return $jobs;
	}

	

	
	
	private function fetchUrl(KalturaBatchJob &$job, $sourceUrl, $destination)
	{
		KalturaLog::debug('fetchUrl - job id ['.$job->id.'], source url ['.$sourceUrl.'], destination ['.$destination.']');
		
		try
		{
			// get the url header and check close job on any errors
			$curlHeaderResponse = $this->fetchHeader($job, $sourceUrl);
			if (!$curlHeaderResponse) {
				return false; // job already closed by fetchHeader function
			}
			
			// check if url leads to a file or a directory
			$isDir = $this->isDirectoryHeader($curlHeaderResponse);
	
			if ($isDir) {
				// fetch all directory contents, one by one
				$result = $this->fetchDir($job, $sourceUrl, $destination);
			}
			else {
				// fetch the file
				$result = $this->fetchFile($job, $sourceUrl, $destination, $curlHeaderResponse);
			}
		}
		catch(Exception $ex)
		{
			// run time error occured
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED);
		}
		
		if (!$result)
		{
			// job failed
			KalturaLog::debug('fetchUrl - job id ['.$job->id.'] failed!');
		}
		else
		{
			// job completed successfuly
			KalturaLog::debug('fetchUrl - job id ['.$job->id.'] completed successfuly!');
			
			if ($this->taskConfig->params->useCloser) {
				// close and mark job as almost done
				$this->closeJob($job, null, null, "File downloaded successfully to tmp space", KalturaBatchJobStatus::ALMOST_DONE, null, $job->data);
			}
			else {
				// close and mark job as finished
				$this->closeJob($job, null, null, "File is in final destination", KalturaBatchJobStatus::FINISHED, null, $job->data);
			}
		}
		
		return $job;
	}
	

	/**
	 * Fetch all content of a $sourceUrl that leads to a directory and save it in the given $dirDestination.
	 * @param KalturaBatchJob $job
	 * @param string $sourceUrl
	 * @param string $dirDestination
	 */
	private function fetchDir(KalturaBatchJob &$job, $sourceUrl, $dirDestination)
	{
		KalturaLog::debug('fetchDir - job id ['.$job->id.'], source url ['.$sourceUrl.'], destination ['.$dirDestination.']');
		
		// create directory if does not exist
		$res = $this->createAndSetDir($dirDestination);
		if (!$res) {
			$msg = "Error: Cannot create destination directory [$dirDestination]";
			KalturaLog::err($msg);
			$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CANNOT_CREATE_DIRECTORY, $msg, KalturaBatchJobStatus::RETRY);				
			return false;
		}
		
		// get directory contents
		KalturaLog::debug('Executing CURL to get directory contents for ['.$sourceUrl.']');
		$curlWrapper = new KCurlWrapper($sourceUrl);	
		$contents = $curlWrapper->exec();
		if ($contents === false) {
			$msg = 'Error: ' . $curlWrapper->getError();
			KalturaLog::err($msg);
			$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $curlWrapper->getErrorNumber(), $msg, KalturaBatchJobStatus::RETRY);
			return false;
		}
		$contents = unserialize($contents); // if an exception is thrown, it will be catched in fetchUrl
		
		// sort contents alphabetically - this is important so that we will first encounter directories and only later the files in them
		sort($contents, SORT_STRING);		
		
		// fetch each direcotry content
		foreach ($contents as $current)
		{
			$current = trim($current,' /');
			$newUrl = $sourceUrl .'/fileName/'.base64_encode($current);
			$curlHeaderResponse = $this->fetchHeader($job, $newUrl);
			if (!$curlHeaderResponse) {
				return false; // job already closed with an error
			}
			
			// check if current is a file or directory
			$isDir = $this->isDirectoryHeader($curlHeaderResponse);
			if ($isDir)
			{
				// is a directory - no need to fetch from server, just create it and proceed
				$res = $this->createAndSetDir($dirDestination.'/'.$current);
				if (!$res)
				{
					$msg = 'Error: Cannot create destination directory ['.$dirDestination.'/'.$current.']';
					KalturaLog::err($msg);
					$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CANNOT_CREATE_DIRECTORY, $msg, KalturaBatchJobStatus::RETRY);				
					return false;
				}
			}
			else
			{
				// is a file - fetch it from server
				$res = $this->fetchFile($job, $newUrl, $dirDestination.'/'.$current, $curlHeaderResponse);
				if (!$res) {
					return false; // job already closed with an error
				}
			}
		}

		KalturaLog::debug('fetchDir - done succesfuly');
		return true;
	}
	
	
	/**
	 * Download a file from $sourceUrl to $fileDestination
	 * @param KalturaBatchJob $job
	 * @param string $sourceUrl
	 * @param string $fileDestination
	 * @param KCurlHeaderResponse $curlHeaderResponse header fetched for the $sourceUrl
	 */
	private function fetchFile(KalturaBatchJob &$job, $sourceUrl, $fileDestination, $curlHeaderResponse = null)
	{
		KalturaLog::debug('fetchFile - job id ['.$job->id.'], source url ['.$sourceUrl.'], destination ['.$fileDestination.']');
		
		// fetch header if not given
		if (!$curlHeaderResponse) {
			$curlHeaderResponse = $this->fetchHeader($job, $sourceUrl);
			if (!$curlHeaderResponse) {
				return false; // job already closed with an error
			}
		}
		
		// try to get file size from headers
		$fileSize = null;
		if (isset($curlHeaderResponse->headers['content-length'])) {
			$fileSize = $curlHeaderResponse->headers['content-length'];
		}

		// if file already exists - check if we can start from specific offset on exising partial content
		$resumeOffset = 0;
		if($fileSize && $fileDestination && file_exists($fileDestination))
		{
			clearstatcache();
			$actualFileSize = filesize($fileDestination);
			if($actualFileSize >= $fileSize)
			{
				// file download finished ?
				KalturaLog::debug('File exists with size ['.$actualFileSize.'] - checking if finished...');
				return $this->checkFile($job, $fileDestination, $fileSize);
			}
			else
			{
				// will resume from the current offset
				KalturaLog::debug('File partialy exists - resume offset set to ['.$actualFileSize.']');
				$resumeOffset = $actualFileSize;
			}
		}
		
		// get http body
		$curlWrapper = new KCurlWrapper($sourceUrl);
		$curlWrapper->setTimeout($this->taskConfig->params->curlTimeout);

		if($resumeOffset)
		{
			// will resume from the current offset
			$curlWrapper->setResumeOffset($resumeOffset);
		}
		else
		{
			// create destination directory if doesn't already exist
			$res = self::createDir(dirname($fileDestination));
			if ( !$res )
			{
				$msg = 'Error: Cannot create destination directory ['.dirname($fileDestination).']';
				KalturaLog::err($msg);
				$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CANNOT_CREATE_DIRECTORY, $msg, KalturaBatchJobStatus::RETRY);				
				return false;
			}
			
			// about to start downloading
			$this->updateJob($job, "Downloading file, size: $fileSize", KalturaBatchJobStatus::PROCESSING);
		}
			
		KalturaLog::debug("Executing curl for downloading file at [$sourceUrl]");
		$res = $curlWrapper->exec($fileDestination); // download file
		KalturaLog::debug("Curl results: $res");

		// handle errors
		if (!$res || $curlWrapper->getError())
		{
			$errNumber = $curlWrapper->getErrorNumber();
			if($errNumber != CURLE_OPERATION_TIMEOUTED)
			{
				// an error other than timeout occured  - cannot continue (timeout is handled with resuming)
				$msg = 'Error: ' . $curlWrapper->getError();
				KalturaLog::err($msg);
				$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $errNumber, $msg, KalturaBatchJobStatus::RETRY);
				$curlWrapper->close();
				return false;
			}
			else
			{
				// timeout error occured
				KalturaLog::debug('Timeout occured');
				clearstatcache();
				$actualFileSize = filesize($fileDestination);
				if($actualFileSize == $resumeOffset)
				{
					// no downloading was done at all - error
					$msg = 'Error: ' . $curlWrapper->getError();
					KalturaLog::err($msg);
					$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $errNumber, $msg, KalturaBatchJobStatus::RETRY);
					$curlWrapper->close();
					return false;
				}
			}
		}
		
		$curlWrapper->close();
			
		if(!file_exists($fileDestination))
		{
			// destination file does not exist for an unknown reason
			$msg = "Error: output file doesn't exist";
			KalturaLog::err($msg);
			$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::OUTPUT_FILE_DOESNT_EXIST, $msg, KalturaBatchJobStatus::RETRY);
			return false;
		}


		if($fileSize)
		{
			clearstatcache();
			$actualFileSize = filesize($fileDestination);
			if ($actualFileSize < $fileSize)
			{
				// part of file was downloaded - will resume in next run
				KalturaLog::debug('File partialy downloaded - will resumt in next run');
				$this->updateJob($job, "Downloaded size: $actualFileSize", KalturaBatchJobStatus::PROCESSING);
				$this->kClient->batch->resetJobExecutionAttempts($job->id, $this->getExclusiveLockKey(), $job->jobType);
				return false;
			}
		}

		KalturaLog::debug('File downloaded completely - will now check if done...');
		$this->updateJob($job, 'File downloaded', KalturaBatchJobStatus::PROCESSING);
		
		// file downloaded completely - check it
		return $this->checkFile($job, $fileDestination, $fileSize);
	}

	
	/**
	 * Checks downloaded file.
	 * Changes the file mode and owner if required.
	 * 
	 * @param KalturaBatchJob $job
	 * @param string $destFile
	 * @param int $fileSize
	 * @return KalturaBatchJob
	 */
	private function checkFile(KalturaBatchJob &$job, $destFile, $fileSize = null)
	{
		KalturaLog::debug("checkFile($job->id, $destFile, $fileSize)");

		if(!file_exists($destFile))
		{
			// destination file does not exist
			KalturaLog::err("Error: file [$destFile] doesn't exist");
			$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::OUTPUT_FILE_DOESNT_EXIST, "Error: file [$destFile] doesn't exist", KalturaBatchJobStatus::FAILED);
			return false;
		}

		clearstatcache();
		if($fileSize)
		{
			if(filesize($destFile) != $fileSize)
			{
				// destination file size is wrong
				KalturaLog::err("Error: file [$destFile] has a wrong size");
				$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::OUTPUT_FILE_WRONG_SIZE, "Error: file [$destFile] has a wrong size", KalturaBatchJobStatus::FAILED);
				return false;
			}
		}
		else
		{
			$fileSize = filesize($destFile);
		}
			
		// set file owner
		$chown_name = $this->taskConfig->params->fileOwner;
		if ($chown_name) {
			KalturaLog::debug("Changing owner of file [$destFile] to [$chown_name]");
			@chown($destFile, $chown_name);
		}
		
		// set file mode
		$chmod_perm = octdec($this->taskConfig->params->fileChmod);
		if (!$chmod_perm) {
			$chmod_perm = 0644;
		}
		KalturaLog::debug("Changing mode of file [$destFile] to [$chmod_perm]");
		@chmod($destFile, $chmod_perm);
			
			
		// IMPORTANT - check's if file is seen by apache
		if(!$this->checkFileExists($destFile, $fileSize))
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, 'File not moved correctly', KalturaBatchJobStatus::RETRY);
			return false;
		}
		
		return true;
	}

	
	// ----------------------
	// -- Helper functions --
	// ----------------------
	
	
	/**
	 * Fetches the header for the given $url and closes the job on any errors
	 * @param KalturaBatchJob $job
	 * @param string $url
	 * @return false|KCurlHeaderResponse
	 */
	private function fetchHeader(KalturaBatchJob &$job, $url)
	{
		KalturaLog::debug('Fetching header for ['.$url.']');
		$this->updateJob($job, 'Downloading header for ['.$url.']', KalturaBatchJobStatus::PROCESSING);
		
		// fetch the http headers
		$curlWrapper = new KCurlWrapper($url);
		$curlHeaderResponse = $curlWrapper->getHeader();
		$curlError = $curlWrapper->getError();
		$curlWrapper->close();
		
		if(!$curlHeaderResponse || $curlError)
		{
			// error fetching headers
			$msg = 'Error: ' . $curlWrapper->getError();
			KalturaLog::err($msg);
			$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $curlWrapper->getErrorNumber(), $msg, KalturaBatchJobStatus::FAILED);
			return false;
		}
			
		if(!$curlHeaderResponse->isGoodCode())
		{
			// some error exists in the response
			$msg = 'HTTP Error: ' . $curlHeaderResponse->code . ' ' . $curlHeaderResponse->codeName;
			KalturaLog::err($msg);
			$this->closeJob($job, KalturaBatchJobErrorTypes::HTTP, $curlHeaderResponse->code, $msg, KalturaBatchJobStatus::FAILED);
			return false;
		}
		
		// header fetched successfully - return it
		return $curlHeaderResponse;
	}
	
	
	
	/**
	 * Check if the given curl header response contains a File-Sync-Type header == 'dir'
	 * @param KCurlHeaderResponse $curlHeaderResponse
	 * @return bool true/false
	 */
	private function isDirectoryHeader($curlHeaderResponse)
	{
		if (isset($curlHeaderResponse->headers['file-sync-type'])) {
			if (trim($curlHeaderResponse->headers['file-sync-type']) === 'dir') {
				return true;
			}
		}
		return false;
	}
	
	
	
	/**
	 * Create a new directory with the given $dirPath and changing its owner and mode according to the batch worker parameters
	 * @param string $dirPath path for the new directory
	 * @return bool true on success, false otherwise
	 */
	private function createAndSetDir($dirPath)
	{
		// create directory if does not exist
		KalturaLog::debug('Creating new directory ['.$dirPath.']');

		$res = self::createDirRecursive( $dirPath );
		if (!$res) 
		{
			return false;
		}
				
		// set directory owner
		$chown_name = $this->taskConfig->params->fileOwner;
		if ($chown_name) {
			KalturaLog::debug("Changing owner of directory [$dirPath] to [$chown_name]");
			@chown($dirPath, $chown_name);
		}
		
		// set directory mode
		$chmod_perm = octdec($this->taskConfig->params->fileChmod);
		if (!$chmod_perm) {
			$chmod_perm = 0644;
		}
		KalturaLog::debug("Changing mode of directory [$dirPath] to [$chmod_perm]");
		@chmod($dirPath, $chmod_perm);
		
		return true;
	}
	
	
	/**
	 * Recursivly create directories for the given $dirPath
	 * @param string $dirPath
	 */
	private function createDirRecursive($dirPath)
	{
		if (is_null($dirPath) || $dirPath == '')
		{
			return false;
		}
		
		if (is_dir($dirPath))
		{
			return true;
		}		
		
		if (is_dir(dirname($dirPath)))
		{
			// parent directory exists
			return $this->createDir($dirPath);
		}
		else
		{
			// parent directory does not exist
			$res = $this->createDirRecursive(dirname($dirPath));
			return $res && $this->createDir($dirPath);
		}
	}
	
	
	
	/**
	 * Create a temporary path for the given $sourceUrl
	 * @param string $sourceUrl
	 */
	private function getTmpPath($sourceUrl)
	{
		// create a temporary file path 
		$rootPath = $this->taskConfig->params->localTempPath;
		
		$res = self::createDir( $rootPath );
		if ( !$res ) 
		{
			KalturaLog::err( "Cannot continue filesync import without a temp directory");
			return false;
		}
		
		// add a unique id to the temporary file path
		$uniqid = uniqid('filesync_import_');
		$destFile = realpath($rootPath) . "/$uniqid";
		KalturaLog::debug("destFile [$destFile]");
		
		// add file extension if any
		$ext = pathinfo($sourceUrl, PATHINFO_EXTENSION);
		$extArr = explode('?', $ext); // remove query string
		$ext = reset($extArr);
		if(strlen($ext))
			$destFile .= ".$ext";
			
		return $destFile;
	}
	
	
	
	
	// --------------------------
	// -- Job update functions --
	// --------------------------
	
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job, $entryStatus = null)
	{
		return $this->kClient->fileSyncImportBatch->updateExclusiveFileSyncImportJob($jobId, $this->getExclusiveLockKey(), $job);
	}

	
	protected function freeExclusiveJob(KalturaBatchJob $job)
	{
		$resetExecutionAttempts = false;
		if ($job->status == KalturaBatchJobStatus::ALMOST_DONE) {
			$resetExecutionAttempts = true;
		}

		$response = $this->kClient->fileSyncImportBatch->freeExclusiveFileSyncImportJob($job->id, $this->getExclusiveLockKey(), $resetExecutionAttempts);

		KalturaLog::info("Queue size: $response->queueSize sent to scheduler");
		$this->saveSchedulerQueue(self::getType(), $response->queueSize);

		return $response->job;
	}

}

