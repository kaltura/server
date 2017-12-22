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

	static $startTime;
	static $downloadedSoFar;
	const  IMPORT_TIMEOUT=120;
	const  HEADERS_TIMEOUT=30;
	static $currentResource;

	public static function  progressWatchDog($resource,$download_size, $downloaded, $upload_size)
	{
		if (version_compare(PHP_VERSION, '5.5.0') < 0) {
			$downloaded = $download_size;
			$resource =  self::$currentResource;
			KalturaLog::debug("Old version");
		}
		if(self::$downloadedSoFar < $downloaded)
		{
			KalturaLog::debug("Downloaded - $downloaded");
			$time = time() - self::$startTime + self::IMPORT_TIMEOUT;
			curl_setopt($resource, CURLOPT_TIMEOUT, $time);
			self::$downloadedSoFar = $downloaded;
		}
	}

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

	/* Will download $sourceUrl to $localPath and will monitor progress with watchDog*/
	private function curlExec($sourceUrl,$localPath,$resumeOffset=0)
	{
		self::$startTime			= time();
		self::$downloadedSoFar		= 0;
		$progressCallBack			= null;
		$curlWrapper				= new KCurlWrapper(self::$taskConfig->params);
		self::$currentResource  	= $curlWrapper->ch;
		if($resumeOffset)
			$curlWrapper->setResumeOffset($resumeOffset);
		$protocol					= $curlWrapper->getSourceUrlProtocol($sourceUrl);
		if($protocol				== KCurlWrapper::HTTP_PROTOCOL_HTTP)
		{
			$curlWrapper->setTimeout(self::IMPORT_TIMEOUT);
			$progressCallBack 		= array('KAsyncImport', 'progressWatchDog');
		}
		$res 						= $curlWrapper->exec($sourceUrl, $localPath,$progressCallBack);
		$responseStatusCode			= $curlWrapper->getInfo(CURLINFO_HTTP_CODE);
		$errorMessage				= $curlWrapper->getError();
		$errorNumber				= $curlWrapper->getErrorNumber();
		$curlWrapper->close();
		KalturaLog::debug("Curl results: [$res] responseStatusCode [$responseStatusCode] error [$errorMessage] error number [$errorNumber]");
		return array($res,$responseStatusCode,$errorMessage,$errorNumber);
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
				$curlWrapper->setTimeout(self::HEADERS_TIMEOUT);
				$curlHeaderResponse = $curlWrapper->getHeader($sourceUrl, $useNoBody);

				$curlErrorMessage = $curlWrapper->getError();
				$curlErrorNumber = $curlWrapper->getErrorNumber();
				$curlWrapper->close();

				if($curlErrorNumber)
				{
					KalturaLog::err("Headers error: " . $curlErrorMessage);
					KalturaLog::err("Headers error number: " . $curlErrorNumber);
				}

				if(!$curlHeaderResponse || !count($curlHeaderResponse->headers))
				{
					$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $curlErrorNumber, "Couldn't read file. Error: " .$curlErrorMessage, KalturaBatchJobStatus::FAILED);
					return $job;
				}

				if(!$curlHeaderResponse->isGoodCode())
				{
					$this->closeJob($job, KalturaBatchJobErrorTypes::HTTP, $curlHeaderResponse->code, "Failed while reading file. HTTP Error: " . $curlHeaderResponse->code . " " . $curlHeaderResponse->codeName, KalturaBatchJobStatus::FAILED);
					return $job;
				}
				if(isset($curlHeaderResponse->headers['content-type']))
					$contentType = $curlHeaderResponse->headers['content-type'];
				if(isset($curlHeaderResponse->headers['content-length']))
					$fileSize = $curlHeaderResponse->headers['content-length'];

				if( $fileSize )
				{
					clearstatcache();
					$actualFileSize = kFile::fileSize($data->destFileLocalPath);
					if($actualFileSize >= $fileSize)
					{
						$this->updateJob($job, 'File imported, copy to shared folder', KalturaBatchJobStatus::PROCESSED);
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
				$curlWrapper->setTimeout(self::HEADERS_TIMEOUT);
				$curlHeaderResponse = $curlWrapper->getHeader($sourceUrl, true);
				if(isset($curlHeaderResponse->headers['content-type']))
					$contentType = $curlHeaderResponse->headers['content-type'];

				if($curlHeaderResponse && count($curlHeaderResponse->headers) && !$curlWrapper->getError() && isset($curlHeaderResponse->headers['content-length']))
					$fileSize = $curlHeaderResponse->headers['content-length'];

				//Close the curl used to fetch the header and create a new one.
				//When fetching headers we set curl options that than are not reset once header is fetched. 
				//Not all servers support all the options so we need to remove them from our headers.
				$curlWrapper->close();
			}

			if($resumeOffset)
			{
				$this->updateJob($job, "Resuming download, from ".$resumeOffset ." size: $fileSize", KalturaBatchJobStatus::PROCESSING, $data);
			}
			else
			{
				// creates a temp file path
				$data->destFileLocalPath = $this->getTempFilePath($sourceUrl);;
				KalturaLog::debug("destFile [$data->destFileLocalPath]");
				$data->fileSize = is_null($fileSize) ? -1 : $fileSize;
				$this->updateJob($job, "Downloading file, size: $fileSize", KalturaBatchJobStatus::PROCESSING, $data);
			}

			list($res,$responseStatusCode,$errorMessage,$errNumber) = $this->curlExec($sourceUrl, $data->destFileLocalPath,$resumeOffset);

			if($responseStatusCode && KCurlHeaderResponse::isError($responseStatusCode))
			{
				if(!$resumeOffset && file_exists($data->destFileLocalPath))
					unlink($data->destFileLocalPath);
				$this->closeJob($job, KalturaBatchJobErrorTypes::HTTP, KalturaBatchJobAppErrors::REMOTE_DOWNLOAD_FAILED, "Failed while reading file. HTTP Error: [$responseStatusCode]", KalturaBatchJobStatus::RETRY);
				return $job;
			}
			
			if(!$res || $errNumber)
			{
				clearstatcache();
				$actualFileSize = kFile::fileSize($data->destFileLocalPath);
				KalturaLog::debug("errNumber: $errNumber ,Actual file size: $actualFileSize ,Expected file size: $fileSize, Resume offset :$resumeOffset");
				if($errNumber == CURLE_PARTIAL_FILE)
				{
					if( $actualFileSize >= $fileSize)
					{
						$this->updateJob($job, 'File imported, copy to shared folder', KalturaBatchJobStatus::PROCESSED);
						return $this->moveFile($job, $data->destFileLocalPath, $fileSize);
					}
					else
					{
						$percent = floor($actualFileSize/$fileSize*100);
						$e = new kTemporaryException("Downloaded size: $actualFileSize($percent%)");
						$e->setResetJobExecutionAttempts(true);
						throw $e;
					}
				}
				if($errNumber != CURLE_OPERATION_TIMEOUTED)
				{
					$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $errNumber, "Error: " . $errorMessage , KalturaBatchJobStatus::RETRY);
					return $job;
				}
				else
				{
					if($actualFileSize == $resumeOffset)
					{
						$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $errNumber, "No new information. Error: " . $errorMessage, KalturaBatchJobStatus::RETRY);
						return $job;
					}
					if(!$fileSize)
					{
						$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $errNumber, "Received timeout, but no filesize available. Completed size [$actualFileSize]" . $errorMessage, KalturaBatchJobStatus::RETRY);
						return $job;
					}
				}
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

				//Ignore file size check based on content.
				$shouldCheckFileSize = ($contentType!='text/html');
				KalturaLog::debug("shouldCheckFileSize:{$shouldCheckFileSize} actualFileSize:{$actualFileSize} fileSize:{$fileSize}");
				if($actualFileSize < $fileSize && $shouldCheckFileSize)
				{
					$percent = floor($actualFileSize * 100 / $fileSize);
					$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $errNumber, "DDownloaded size: $actualFileSize($percent%) " . $curlWrapper->getError(), KalturaBatchJobStatus::RETRY);
					return job;
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
					$privateKeyFile = kFile::createTempFile($privateKey, 'privateKey');
					$publicKeyFile = kFile::createTempFile($publicKey, 'publicKey');
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
			// in case it failed few times, take the file as is
			if($fileSize)
			{
				clearstatcache();
				$actualFileSize = kFile::fileSize($data->destFileLocalPath);
				if($actualFileSize < $fileSize)
				{
					$percent = floor($actualFileSize * 100 / $fileSize);
					$e = new kTemporaryException("Downloaded size: $actualFileSize($percent%)");
					$e->setResetJobExecutionAttempts(true);
					throw $e;
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
