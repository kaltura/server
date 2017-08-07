<?php
/**
 * @package Scheduler
 * @subpackage Import
 */

/**
 * Will import a single URL and store it in the file system.
 * The state machine of the job is as follows:
 * 	 	parse URL	(youTube is a special case)
 * 		fetch heraders (to calculate the size of the file)
 * 		fetch file
 * 		move the file to the archive
 * 		set the entry's new status and file details  (check if FLV)
 *
 * @package Scheduler
 * @subpackage Import
 */
class KAsyncImport extends KJobHandlerWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::IMPORT;
	}

	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->fetchFile($job, $job->data);
	}

	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::getMaxJobsEachRun()
	 */
	protected function getMaxJobsEachRun()
	{
		return 1;
	}

	/*
	 * Will take a single KalturaBatchJob and fetch the URL to the job's destFile
	 */
	private function fetchFile(KalturaBatchJob $job, KalturaImportJobData $data)
	{
		$jobSubType = $job->jobSubType;

		$sshProtocols = array(
			kFileTransferMgrType::SCP,
			kFileTransferMgrType::SFTP,
		);

		if (in_array($jobSubType, $sshProtocols))
		{
		    // use SSH file transfer manager for SFTP/SCP
            return $this->fetchFileSsh($job, $data);
		}

		try
		{
			$sourceUrl = $data->srcFileUrl;

			$this->updateJob($job, 'Downloading file header', KalturaBatchJobStatus::QUEUED);
			$fileSize = null;
			$resumeOffset = 0;
			$contentType = null;
			if ($data->destFileLocalPath && file_exists($data->destFileLocalPath) )
			{
    			$curlWrapper = new KCurlWrapper(self::$taskConfig->params);
    			$useNoBody = ($job->executionAttempts > 1); // if the process crashed first time, tries with no body instead of range 0-0
    			$curlHeaderResponse = $curlWrapper->getHeader($sourceUrl, $useNoBody);
    			if(!$curlHeaderResponse || !count($curlHeaderResponse->headers))
    			{
    				$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $curlWrapper->getErrorNumber(), "Couldn't read file. Error: " . $curlWrapper->getError(), KalturaBatchJobStatus::FAILED);
    				return $job;
    			}

    			if($curlWrapper->getError())
    			{
    				KalturaLog::err("Headers error: " . $curlWrapper->getError());
    				KalturaLog::err("Headers error number: " . $curlWrapper->getErrorNumber());
    				$curlWrapper->close();

    				$curlWrapper = new KCurlWrapper(self::$taskConfig->params);
    			}

    			if(!$curlHeaderResponse->isGoodCode())
    			{
    				$this->closeJob($job, KalturaBatchJobErrorTypes::HTTP, $curlHeaderResponse->code, "Failed while reading file. HTTP Error: " . $curlHeaderResponse->code . " " . $curlHeaderResponse->codeName, KalturaBatchJobStatus::FAILED);
    				$curlWrapper->close();
    				return $job;
    			}
			if(isset($curlHeaderResponse->headers['content-type']))
                        	$contentType = $curlHeaderResponse->headers['content-type'];
			if(isset($curlHeaderResponse->headers['content-length']))
    				$fileSize = $curlHeaderResponse->headers['content-length'];
    			$curlWrapper->close();

    			if( $fileSize )
    			{
    				clearstatcache();
    				$actualFileSize = kFile::fileSize($data->destFileLocalPath);
    				if($actualFileSize >= $fileSize)
    				{
    					return $this->moveFile($job, $data->destFileLocalPath, $fileSize);
    				}
    				else
    				{
    					$resumeOffset = $actualFileSize;
    				}
    			}
			}

			$curlWrapper = new KCurlWrapper(self::$taskConfig->params);

			if(is_null($fileSize)) {
				// Read file size
				$curlHeaderResponse = $curlWrapper->getHeader($sourceUrl, true);
				if(isset($curlHeaderResponse->headers['content-type']))
	                               	$contentType = $curlHeaderResponse->headers['content-type'];

				if($curlHeaderResponse && count($curlHeaderResponse->headers) && !$curlWrapper->getError() && isset($curlHeaderResponse->headers['content-length']))
					$fileSize = $curlHeaderResponse->headers['content-length'];
				
				//Close the curl used to fetch the header and create a new one. 
				//When fetching headers we set curl options that than are not reset once header is fetched. 
				//Not all servers support all the options so we need to remove them from our headers.
				$curlWrapper->close();
				$curlWrapper = new KCurlWrapper(self::$taskConfig->params);
			}

			if($resumeOffset)
			{
				$curlWrapper->setResumeOffset($resumeOffset);
			}
			else
			{
				// creates a temp file path
				$destFile = $this->getTempFilePath($sourceUrl);
				KalturaLog::debug("destFile [$destFile]");
				$data->destFileLocalPath = $destFile;
				$data->fileSize = is_null($fileSize) ? -1 : $fileSize;
				$this->updateJob($job, "Downloading file, size: $fileSize", KalturaBatchJobStatus::PROCESSING, $data);
			}

			$res = $curlWrapper->exec($sourceUrl, $data->destFileLocalPath);
			$responseStatusCode = $curlWrapper->getInfo(CURLINFO_HTTP_CODE);
			KalturaLog::debug("Curl results: [$res] responseStatusCode [$responseStatusCode]");
			
			if($responseStatusCode && KCurlHeaderResponse::isError($responseStatusCode))
			{
				$this->closeJob($job, KalturaBatchJobErrorTypes::HTTP, KalturaBatchJobAppErrors::REMOTE_DOWNLOAD_FAILED, "Failed while reading file. HTTP Error: [$responseStatusCode]", KalturaBatchJobStatus::RETRY);
				$curlWrapper->close();
				return $job;
			}
			
			if(!$res || $curlWrapper->getError())
			{
				$errNumber = $curlWrapper->getErrorNumber();
				if($errNumber != CURLE_OPERATION_TIMEOUTED)
				{
					$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $errNumber, "Error: " . $curlWrapper->getError(), KalturaBatchJobStatus::RETRY);
					$curlWrapper->close();
					return $job;
				}
				else
				{
					clearstatcache();
					$actualFileSize = kFile::fileSize($data->destFileLocalPath);
					if($actualFileSize == $resumeOffset)
					{
						$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $errNumber, "No new information. Error: " . $curlWrapper->getError(), KalturaBatchJobStatus::RETRY);
						$curlWrapper->close();
						return $job;
					}
					if(!$fileSize)
					{
						$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $errNumber, "Received timeout, but no filesize available. Completed size [$actualFileSize]" . $curlWrapper->getError(), KalturaBatchJobStatus::RETRY);
						$curlWrapper->close();
						return $job;
					}
				}
			}
			$curlWrapper->close();

			if(!file_exists($data->destFileLocalPath))
			{
				$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::OUTPUT_FILE_DOESNT_EXIST, "Error: output file doesn't exist", KalturaBatchJobStatus::RETRY);
				return $job;
			}

			// check the file size only if its first or second retry
			// in case it failed few times, taks the file as is
			if($fileSize)
			{
				clearstatcache();
				$actualFileSize = kFile::fileSize($data->destFileLocalPath);

				//Ignore file size check based on content.
				$shouldCheckFileSize = ($contentType!='text/html');
				KalturaLog::debug("shouldCheckFileSize:{$shouldCheckFileSize} actualFileSize:{$actualFileSize} fileSize:{$fileSize}");
				if($actualFileSize < $fileSize && $shouldCheckFileSize)
				{
					$percent = floor($actualFileSize * 100 / $fileSize);
					$this->updateJob($job, "Downloaded size: $actualFileSize($percent%)", KalturaBatchJobStatus::PROCESSING, $data);
					self::$kClient->batch->resetJobExecutionAttempts($job->id, $this->getExclusiveLockKey(), $job->jobType);
//					$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::OUTPUT_FILE_WRONG_SIZE, "Expected file size[$fileSize] actual file size[$actualFileSize]", KalturaBatchJobStatus::RETRY);
					return $job;
				}
				
				KalturaLog::info("headers " . print_r($curlHeaderResponse, true));
				$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaImportHandler');
				foreach ($pluginInstances as $pluginInstance)
				{
					/* @var $pluginInstance IKalturaImportHandler */
					$data = $pluginInstance->handleImportContent($curlHeaderResponse, $data, KBatchBase::$taskConfig->params);
				}
			}

			$this->updateJob($job, 'File imported, copy to shared folder', KalturaBatchJobStatus::PROCESSED);
			$job = $this->moveFile($job, $data->destFileLocalPath);
		}
		catch(kTemporaryException $tex)
		{
			$data->destFileLocalPath = KalturaClient::getKalturaNullValue();
			$tex->setData($data);
			throw $tex;
		}
		catch(Exception $ex)
		{
			$data->destFileLocalPath = KalturaClient::getKalturaNullValue();
			if($ex->getMessage() == KCurlWrapper::COULD_NOT_CONNECT_TO_HOST_ERROR)
			{
				throw new kTemporaryException($ex->getMessage(), $ex->getCode(), $data);
			}
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED, $data);
		}
		return $job;
	}


	/*
	 * Will take a single KalturaBatchJob and fetch the URL to the job's destFile
	 */
	private function fetchFileSsh(KalturaBatchJob $job, KalturaSshImportJobData $data)
	{
		try
		{
			$sourceUrl = $data->srcFileUrl;

            // extract information from URL and job data
			$parsedUrl = parse_url($sourceUrl);

			$host = isset($parsedUrl['host']) ? $parsedUrl['host'] : null;
			$remotePath = isset($parsedUrl['path']) ? $parsedUrl['path'] : null;
			$username = isset($parsedUrl['user']) ? $parsedUrl['user'] : null;
			$password = isset($parsedUrl['pass']) ? $parsedUrl['pass'] : null;
			$port = isset($parsedUrl['port']) ? $parsedUrl['port'] : null;

			$privateKey = isset($data->privateKey) ? $data->privateKey : null;
			$publicKey  = isset($data->publicKey) ? $data->publicKey : null;
			$passPhrase = isset($data->passPhrase) ? $data->passPhrase : null;

			KalturaLog::debug("host [$host] remotePath [$remotePath] username [$username] password [$password] port [$port]");
			if ($privateKey || $publicKey) {
			    KalturaLog::debug("Private Key: $privateKey");
			    KalturaLog::debug("Public Key: $publicKey");
			}

			if (!$host) {
			    $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::MISSING_PARAMETERS, 'Error: missing host', KalturaBatchJobStatus::FAILED);
			    return $job;
			}
			if (!$remotePath) {
			    $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::MISSING_PARAMETERS, 'Error: missing path', KalturaBatchJobStatus::FAILED);
			    return $job;
			}

			// create suitable file transfer manager object
			$subType = $job->jobSubType;
			$engineOptions = isset(self::$taskConfig->engineOptions) ? self::$taskConfig->engineOptions->toArray() : array();
			$fileTransferMgr = kFileTransferMgr::getInstance($subType, $engineOptions);

			if (!$fileTransferMgr) {
			    $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::ENGINE_NOT_FOUND, "Error: file transfer manager not found for type [$subType]", KalturaBatchJobStatus::FAILED);
			    return $job;
			}
			
			try{
				// login to server
				if (!$privateKey || !$publicKey) {
				    $fileTransferMgr->login($host, $username, $password, $port);
				}
				else {
				    $privateKeyFile = $this->getFileLocationForSshKey($privateKey, 'privateKey');
				    $publicKeyFile = $this->getFileLocationForSshKey($publicKey, 'publicKey');
				    $fileTransferMgr->loginPubKey($host, $username, $publicKeyFile, $privateKeyFile, $passPhrase);
				}
			
				// check if file exists
				$fileExists = $fileTransferMgr->fileExists($remotePath);
				if (!$fileExists) {
				    $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::MISSING_PARAMETERS, "Error: remote file [$remotePath] does not exist", KalturaBatchJobStatus::FAILED);
				    return $job;
				}
	
				// get file size
				$fileSize = $fileTransferMgr->fileSize($remotePath);
				
	            // create a temp file path
				$destFile = $this->getTempFilePath($remotePath);
				$data->destFileLocalPath = $destFile;
				$data->fileSize = is_null($fileSize) ? -1 : $fileSize;
				KalturaLog::debug("destFile [$destFile]");
	
				// download file - overwrite local if exists
				$this->updateJob($job, "Downloading file, size: $fileSize", KalturaBatchJobStatus::PROCESSING, $data);
				KalturaLog::info("Downloading remote file [$remotePath] to local path [$destFile]");
				$res = $fileTransferMgr->getFile($remotePath, $destFile);
				
			}
			catch (kFileTransferMgrException $ex){
				$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::RETRY);
				return $job;
			}

			if(!file_exists($data->destFileLocalPath))
			{
				$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::OUTPUT_FILE_DOESNT_EXIST, "Error: output file doesn't exist", KalturaBatchJobStatus::RETRY);
				return $job;
			}

			// check the file size only if its first or second retry
			// in case it failed few times, taks the file as is
			if($fileSize)
			{
				clearstatcache();
				$actualFileSize = kFile::fileSize($data->destFileLocalPath);
				if($actualFileSize < $fileSize)
				{
					$percent = floor($actualFileSize * 100 / $fileSize);
					$job = $this->updateJob($job, "Downloaded size: $actualFileSize($percent%)", KalturaBatchJobStatus::PROCESSING, $data);
					self::$kClient->batch->resetJobExecutionAttempts($job->id, $this->getExclusiveLockKey(), $job->jobType);
					return $job;
				}
			}

			$this->updateJob($job, 'File imported, copy to shared folder', KalturaBatchJobStatus::PROCESSED);

			$job = $this->moveFile($job, $data->destFileLocalPath);
		}
		catch(Exception $ex)
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED);
		}
		return $job;
	}

	/**
	 * @param KalturaBatchJob $job
	 * @param string $destFile
	 * @param int $fileSize
	 * @return KalturaBatchJob
	 */
	private function moveFile(KalturaBatchJob $job, $destFile)
	{
		try
		{
			// creates a shared file path
			$rootPath = self::$taskConfig->params->sharedTempPath;

			$res = self::createDir( $rootPath );
			if ( !$res )
			{
				KalturaLog::err( "Cannot continue import without shared directory");
				die();
			}
			$uniqid = uniqid('import_');
			$sharedFile = $rootPath . DIRECTORY_SEPARATOR . $uniqid;

			$ext = pathinfo($destFile, PATHINFO_EXTENSION);
			if(strlen($ext))
				$sharedFile .= ".$ext";

			KalturaLog::debug("rename('$destFile', '$sharedFile')");
			rename($destFile, $sharedFile);
			if(!file_exists($sharedFile))
			{
				KalturaLog::err("Error: renamed file doesn't exist");
				die();
			}

			clearstatcache();

			$fileSize = kFile::fileSize($sharedFile);

			$this->setFilePermissions($sharedFile);

			$data = $job->data;
			$data->destFileLocalPath = $sharedFile;
			$data->fileSize = is_null($fileSize) ? -1 : $fileSize;

			if($this->checkFileExists($sharedFile, $fileSize))
			{
				$this->closeJob($job, null, null, 'Succesfully moved file', KalturaBatchJobStatus::FINISHED, $data);
			}
			else
			{
				$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, 'File not moved correctly', KalturaBatchJobStatus::RETRY);
			}
		}
		catch(Exception $ex)
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED);
		}
		return $job;
	}

	/*
	 * Lazy saving of the key to a temporary path, the key will exist in this location until the temp files are purged
	 */
	protected function getFileLocationForSshKey($keyContent, $prefix = 'key')
	{
		$tempDirectory = sys_get_temp_dir();
		$fileLocation = tempnam($tempDirectory, $prefix);
		file_put_contents($fileLocation, $keyContent);
		return $fileLocation;
	}


	protected function getTempFilePath($remotePath)
	{
	    // create a temp file path
		$rootPath = self::$taskConfig->params->localTempPath;

		$res = self::createDir( $rootPath );
		if ( !$res )
		{
			KalturaLog::err( "Cannot continue import without temp directory");
			die();
		}

		$uniqid = uniqid('import_');
		$destFile = realpath($rootPath) . DIRECTORY_SEPARATOR . $uniqid;

		// in case the url has added arguments, remove them (and reveal the real URL path)
		// in order to find the file extension
		$urlPathEndIndex = strpos($remotePath, "?");
		if ($urlPathEndIndex !== false)
			$remotePath = substr($remotePath, 0, $urlPathEndIndex);

		$ext = pathinfo($remotePath, PATHINFO_EXTENSION);
		if(strlen($ext))
			$destFile .= ".$ext";

		return $destFile;
	}
}
