<?php
/**
 *
 *
 * @package plugins.multiCenters
 * @subpackage Scheduler.FileSyncImport
 */
class KAsyncFileSyncImport extends KJobHandlerWorker
{
	protected $curlWrapper;

	public function run($jobs = null)
	{
		$this->curlWrapper = new KCurlWrapper(self::$taskConfig->params);
		
		$retJobs = parent::run($jobs);
		
		$this->curlWrapper->close();
		
		return $retJobs;
	}
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::FILESYNC_IMPORT;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::getJobs()
	 * 
	 * TODO remove the destFilePath from the job data and get it later using the api, then delete this method
	 */
	protected function getJobs()
	{
		$maxOffset = min($this->getMaxOffset(), KBatchBase::$taskConfig->getQueueSize());
		$multiCentersPlugin = KalturaMultiCentersClientPlugin::get(self::$kClient);
		return $multiCentersPlugin->filesyncImportBatch->getExclusiveFileSyncImportJobs($this->getExclusiveLockKey(), self::$taskConfig->maximumExecutionTime, 
				$this->getMaxJobsEachRun(), $this->getFilter(), $maxOffset);
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		$fileDestination = $job->data->destFilePath;
		
		if($job->data->fileSize == 0) 
			return $this->fetchEmptyFile($job, $fileDestination);				
		
		// start downoading the file to its destination (temp or final)
		return $this->fetchUrl($job, $job->data->sourceUrl, $fileDestination);	
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
			// job failed and closed inside
			KalturaLog::debug('fetchUrl - job id ['.$job->id.'] failed!');
		else  
			$this->fetchUrlClose($job);
		
		return $job;
	}
	
	private function fetchUrlClose(KalturaBatchJob &$job) {
		
		// job completed successfuly
		KalturaLog::debug ( 'fetchUrl - job id [' . $job->id . '] completed successfuly!' );
		
		// close and mark job as finished
		$this->closeJob ( $job, null, null, "File is in final destination", KalturaBatchJobStatus::FINISHED, null, $job->data );
	}
	
	private function fetchEmptyFile(KalturaBatchJob &$job, $destination) {
		
		$res = self::createDir(dirname($destination));
		if ( !$res )
		{
			$msg = 'Error: Cannot create destination directory ['.dirname($destination).']';
			KalturaLog::err($msg);
			$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CANNOT_CREATE_DIRECTORY, $msg, KalturaBatchJobStatus::RETRY);
			return $job;
		}
		
		$res = touch($destination);
		if ( !$res )
		{
			$msg = 'Error: Cannot create file [$destination]';
			KalturaLog::err($msg);
			$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::OUTPUT_FILE_DOESNT_EXIST, $msg, KalturaBatchJobStatus::RETRY);
			return $job;
		}
		
		$this->fetchUrlClose($job);
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
		$contents = $this->curlWrapper->exec($sourceUrl);
		$curlError = $this->curlWrapper->getError();
		$curlErrorNumber = $this->curlWrapper->getErrorNumber();
		
		if ($contents === false || $curlError) {
			$msg = "Error: $curlError";
			KalturaLog::err($msg);
			$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $curlErrorNumber, $msg, KalturaBatchJobStatus::RETRY);
			return false;
		}
		$contents = unserialize($contents); // if an exception is thrown, it will be catched in fetchUrl
		
		// sort contents alphabetically - this is important so that we will first encounter directories and only later the files in them
		sort($contents, SORT_STRING);		
		
		// fetch each direcotry content
		foreach ($contents as $current)
		{
			$name     = trim($current[0],' /');
			$type     = trim($current[1]);
			$filesize = trim($current[2]);
			
			$newUrl = $sourceUrl .'/fileName/'.base64_encode($name);
			
			if (!$type || !$filesize)
			{
				$curlHeaderResponse = $this->fetchHeader($job, $newUrl);
				if (!$curlHeaderResponse) {
					return false; // job already closed with an error
				}
				// check if current is a file or directory
				$isDir = $this->isDirectoryHeader($curlHeaderResponse);
				$filesize = $this->getFilesizeFromHeader($curlHeaderResponse);
			}
			else {
				$isDir = $type === 'dir';
			}
			
			if ($isDir)
			{
				// is a directory - no need to fetch from server, just create it and proceed
				$res = $this->createAndSetDir($dirDestination.'/'.$name);
				if (!$res)
				{
					$msg = 'Error: Cannot create destination directory ['.$dirDestination.'/'.$name.']';
					KalturaLog::err($msg);
					$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CANNOT_CREATE_DIRECTORY, $msg, KalturaBatchJobStatus::RETRY);				
					return false;
				}
			}
			else
			{
				// is a file - fetch it from server
				$res = $this->fetchFile($job, $newUrl, $dirDestination.'/'.$name, null, $filesize);
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
	private function fetchFile(KalturaBatchJob &$job, $sourceUrl, $fileDestination, $curlHeaderResponse = null, $fileSize = null)
	{
		KalturaLog::debug('fetchFile - job id ['.$job->id.'], source url ['.$sourceUrl.'], destination ['.$fileDestination.']');
		
		if (!$fileSize)
		{
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
		if($resumeOffset)
		{
			// will resume from the current offset
			$this->curlWrapper->setResumeOffset($resumeOffset);
		}
		else
		{
			//If we run mutiple file sync import using the same curl we nned to reset the offset each time before fetching the file 
			$this->curlWrapper->setResumeOffset(0);
			
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
		$res = $this->curlWrapper->exec($sourceUrl, $fileDestination); // download file
		$curlError = $this->curlWrapper->getError();
		$curlErrorNumber = $this->curlWrapper->getErrorNumber();
		
		KalturaLog::debug("Curl results: $res");

		// handle errors
		if (!$res || $curlError)
		{
			if($curlErrorNumber != CURLE_OPERATION_TIMEOUTED)
			{
				// an error other than timeout occured  - cannot continue (timeout is handled with resuming)
				$msg = "Error: $curlError";
				KalturaLog::err($msg);
				$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $curlErrorNumber, $msg, KalturaBatchJobStatus::RETRY);
				return false;
			}
			else
			{
				// timeout error occured
				KalturaLog::debug('Timeout occured');
				clearstatcache();
				$actualFileSize = kFile::fileSize($fileDestination);
				if($actualFileSize == $resumeOffset)
				{
					// no downloading was done at all - error
					$msg = "Error: $curlError";
					KalturaLog::err($msg);
					$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $curlErrorNumber, $msg, KalturaBatchJobStatus::RETRY);
					return false;
				}
			}
		}
		
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
			$actualFileSize = kFile::fileSize($fileDestination);
			if ($actualFileSize < $fileSize)
			{
				// part of file was downloaded - will resume in next run
				KalturaLog::debug('File partialy downloaded - will resumt in next run');
				self::$kClient->batch->resetJobExecutionAttempts($job->id, $this->getExclusiveLockKey(), $job->jobType);
				$this->closeJob($job, null, null, "Downloaded size: $actualFileSize", KalturaBatchJobStatus::RETRY);
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

		if($fileSize)
		{
			if(kFile::fileSize($destFile) != $fileSize)
			{
				// destination file size is wrong
				KalturaLog::err("Error: file [$destFile] has a wrong size.  file size: [".kFile::fileSize($destFile)."] should be [$fileSize]");
				$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::OUTPUT_FILE_WRONG_SIZE, "Error: file [$destFile] has a wrong size", KalturaBatchJobStatus::FAILED);
				return false;
			}
		}
		else
		{
			$fileSize = kFile::fileSize($destFile);
		}
			
		// set file owner
		$chown_name = self::$taskConfig->params->fileOwner;
		if ($chown_name) {
			KalturaLog::debug("Changing owner of file [$destFile] to [$chown_name]");
			@chown($destFile, $chown_name);
		}
		
		// set file mode
		$chmod_perm = octdec(self::$taskConfig->params->fileChmod);
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
		$curlHeaderResponse = $this->curlWrapper->getHeader($url);
		$curlError = $this->curlWrapper->getError();
		$curlErrorNumber = $this->curlWrapper->getErrorNumber();
		
		if(!$curlHeaderResponse || !count($curlHeaderResponse->headers))
		{
			// error fetching headers
			$msg = "Error: $curlError";
			KalturaLog::err($msg);
			$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $curlErrorNumber, $msg, KalturaBatchJobStatus::RETRY);
			return false;
		}
	
    	if($curlError)
    	{
    		KalturaLog::err("Headers error: $curlError");
    		KalturaLog::err("Headers error number: $curlErrorNumber");
    	}
    			
		if(!$curlHeaderResponse->isGoodCode())
		{
			// some error exists in the response
			$msg = 'HTTP Error: ' . $curlHeaderResponse->code . ' ' . $curlHeaderResponse->codeName;
			KalturaLog::err($msg);
			$this->closeJob($job, KalturaBatchJobErrorTypes::HTTP, $curlHeaderResponse->code, $msg, KalturaBatchJobStatus::RETRY);
			return false;
		}
		
		// header fetched successfully - return it
		return $curlHeaderResponse;
	}
	
	
	
	/**
	 * Try to get the filesize from the given header
	 * @param KCurlHeaderResponse $curlHeaderResponse
	 * @return false|int file size or false on error
	 */
	private function getFilesizeFromHeader($curlHeaderResponse)
	{
		// try to get file size from headers
		if (isset($curlHeaderResponse->headers['content-length'])) {
			$fileSize = $curlHeaderResponse->headers['content-length'];
			return $fileSize;
		}
		return false;	
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
		$chown_name = self::$taskConfig->params->fileOwner;
		if ($chown_name) {
			KalturaLog::debug("Changing owner of directory [$dirPath] to [$chown_name]");
			@chown($dirPath, $chown_name);
		}
		
		// set directory mode
		$chmod_perm = octdec(self::$taskConfig->params->fileChmod);
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
		$rootPath = self::$taskConfig->params->localTempPath;
		
		$res = self::createDir( $rootPath );
		if ( !$res ) 
		{
			KalturaLog::err( "Cannot continue filesync import without a temp directory");
			return false;
		}
		
		// add a unique id to the temporary file path
		$uniqid = uniqid('filesync_import_');
		$destFile = realpath($rootPath) . DIRECTORY_SEPARATOR . $uniqid;
		KalturaLog::debug("destFile [$destFile]");
		
		// add file extension if any
		$ext = pathinfo($sourceUrl, PATHINFO_EXTENSION);
		$extArr = explode('?', $ext); // remove query string
		$ext = reset($extArr);
		if(strlen($ext))
			$destFile .= ".$ext";
			
		return $destFile;
	}
}
