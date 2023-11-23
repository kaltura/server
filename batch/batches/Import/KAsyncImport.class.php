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
	static $currentResource;
	static $currentEngine = self::CURL_DOWNLOAD_ENGINE;
	
	const IMPORT_TIMEOUT=120;
	const HEADERS_TIMEOUT=30;
	const CURL_DOWNLOAD_ENGINE = 'curl';
	const AXEL_DOWNLOAD_ENGINE = 'axel';
	const AXEL_MAX_URL_LENGTH = 1024;


	public static function  progressWatchDog($resource,$download_size, $downloaded, $upload_size)
	{
		if (version_compare(PHP_VERSION, '5.5.0') < 0) {
			$downloaded = $download_size;
			$resource =  self::$currentResource;
		}
		if(self::$downloadedSoFar < $downloaded)
		{
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

	private function shouldUseAxelDownloadEngine($partnerId, $jobSubType, $url)
	{
		if (!self::$taskConfig->params || !isset(self::$taskConfig->params->partnersUseAxel) || !isset(self::$taskConfig->params->axelPath))
		{
			return;
		}
		
		$axelPartnerIds = explode(',', self::$taskConfig->params->partnersUseAxel);
		if(!in_array($partnerId, $axelPartnerIds))
		{
			return;
		}
		
		if (!KAxelWrapper::checkAxelInstalled(self::$taskConfig->params->axelPath))
		{
			KalturaLog::debug("Axel not installed");
			return;
		}
		
		// in case its an sftp job - don't use axel
		$axelSupportedProtocols = array(
			kFileTransferMgrType::HTTP,
			kFileTransferMgrType::HTTPS,
			kFileTransferMgrType::FTP
		);
		
		if (!in_array($jobSubType, $axelSupportedProtocols))
		{
			return;
		}
		
		// axel cant handle urls > 1024
		if (strlen($url) > self::AXEL_MAX_URL_LENGTH)
		{
			KalturaLog::debug("URL length longer than [" . self::AXEL_MAX_URL_LENGTH . "] - cannot use axel due to axel limitation");
			return;
		}
		
		if (KAxelWrapper::checkUserAndPassOnUrl($url))
		{
			KalturaLog::debug("URL has a user/pass - not using axel for security reasons. URL [$url]");
			return;
		}
		
		if ($this->getRedirectUrlIfExist($url))
		{
			KalturaLog::debug("URL has a redirect - not using axel for security reasons. URL [$url]");
			return;
		}
		
		
		self::$currentEngine = self::AXEL_DOWNLOAD_ENGINE;
	}
	
	private function downloadExec($sourceUrl, $localPath, $resumeOffset = 0, $urlHeaders = null)
	{
		if(self::$currentEngine == self::AXEL_DOWNLOAD_ENGINE)
		{
			KalturaLog::debug("Import via Axel");
			return $this->axelExec($sourceUrl, $localPath, $resumeOffset);
		}
		
		KalturaLog::debug("Import via cURL");
		return $this->curlExec($sourceUrl, $localPath, $resumeOffset, $urlHeaders);
	}
	
	private function axelExec($sourceUrl, $localPath, $resumeOffset=0)
	{
		$axelWrapper = new KAxelWrapper(self::$taskConfig->params);
		
		if ($resumeOffset)
		{
			KalturaLog::debug("Resuming download from [$resumeOffset] bytes");
		}
		
		$res = $axelWrapper->exec($sourceUrl, $localPath);
		$responseStatusCode = $axelWrapper->getHttpCode();
		$errorMessage = $axelWrapper->getErrorMsg();
		$errorNumber = $axelWrapper->getErrorNumber();
		$axelWrapper->close();
		KalturaLog::debug("Axel results: [$res] responseStatusCode [$responseStatusCode] error [$errorMessage] error number [$errorNumber]");
		
		return array($res,$responseStatusCode,$errorMessage,$errorNumber);
	}
	
	/* Will download $sourceUrl to $localPath and will monitor progress with watchDog*/
	private function curlExec($sourceUrl, $localPath, $resumeOffset = 0, $urlHeaders = null)
	{
		self::$startTime			= time();
		self::$downloadedSoFar		= 0;
		$progressCallBack			= null;
		$curlWrapper				= new KCurlWrapper(self::$taskConfig->params);
		self::$currentResource  	= $curlWrapper->ch;
		if ($resumeOffset)
		{
			$curlWrapper->setResumeOffset($resumeOffset);
		}
		$protocol					= $curlWrapper->getSourceUrlProtocol($sourceUrl);
		if ($urlHeaders)
		{
			$curlWrapper->setOpt(CURLOPT_HTTPHEADER, $urlHeaders);
		}
		if ($protocol == KCurlWrapper::HTTP_PROTOCOL_HTTP)
		{
			$curlWrapper->setTimeout(self::IMPORT_TIMEOUT);
			$progressCallBack 		= array('KAsyncImport', 'progressWatchDog');
		}
		$res 						= $curlWrapper->exec($sourceUrl, $localPath,$progressCallBack);
		$responseStatusCode			= $curlWrapper->getHttpCode();
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
            		if ($data->shouldRedirect)
            		{
                		$sourceUrl =  KCurlWrapper::getRedirectUrl($data->srcFileUrl, $data->urlHeaders);
            		}
            		else
            		{
                		$sourceUrl = $data->srcFileUrl;
            		}

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
						return $this->moveFile($job, $data);
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
				$url = $this->getUrlForExtension($sourceUrl, $job->partnerId);
				$data->destFileLocalPath = $this->getTempFilePath($url, $fileSize);
				KalturaLog::debug("destFile [$data->destFileLocalPath]");
				$data->fileSize = is_null($fileSize) ? -1 : $fileSize;
				$this->updateJob($job, "Downloading file, size: $fileSize", KalturaBatchJobStatus::PROCESSING, $data);
			}

			$this->shouldUseAxelDownloadEngine($job->partnerId, $jobSubType, $sourceUrl);
			list($res,$responseStatusCode,$errorMessage,$errNumber) = $this->downloadExec($sourceUrl, $data->destFileLocalPath, $resumeOffset, $data->urlHeaders);

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
				$fileSize = self::$currentEngine == self::AXEL_DOWNLOAD_ENGINE ? KAxelWrapper::$fileSize : $fileSize;
				KalturaLog::debug("errNumber: $errNumber ,Actual file size: $actualFileSize ,Expected file size: $fileSize, Resume offset :$resumeOffset");

				if($this->isPartialFile($errNumber))
				{
					if( $actualFileSize >= $fileSize)
					{
						$this->updateJob($job, 'File imported, copy to shared folder', KalturaBatchJobStatus::PROCESSED);
						return $this->moveFile($job, $data);
					}
					else
					{
						list($actualFileSize, $data) = $this->movePartialFileToShared($data, $actualFileSize);
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
					//Handle flow where destFileSync local path is a different mount than the shared file system mount
					list($actualFileSize, $data) = $this->movePartialFileToShared($data, $actualFileSize);
					$percent = floor($actualFileSize * 100 / $fileSize);
					$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $errNumber, "Downloaded size: $actualFileSize($percent%) " . $curlWrapper->getError(), KalturaBatchJobStatus::RETRY, $data);
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
			$job = $this->moveFile($job, $data);
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
	private function fetchFileSsh(KalturaBatchJob $job, KalturaImportJobData $data)
	{
		try
		{
			$sourceUrl = $data->srcFileUrl;
			
			//Replace # sign to avoid cases where it's part of the user/password. The # sign is considered as fragment part of the URL.
			//https://bugs.php.net/bug.php?id=73754
			$sourceUrl = preg_replace("/#/", "_kHash_", $sourceUrl, -1, $replaceCount);
			
			// extract information from URL and job data
			$parsedUrl = parse_url($sourceUrl);
			if($replaceCount)
			{
				$parsedUrl = preg_replace("/_kHash_/", "#", $parsedUrl);
			}

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
				$destFile = $this->getTempFilePath($remotePath, $fileSize);
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
					list($actualFileSize, $data) = $this->movePartialFileToShared($data, $actualFileSize);
					$percent = floor($actualFileSize * 100 / $fileSize);
					$e = new kTemporaryException("Downloaded size: $actualFileSize($percent%)");
					$e->setResetJobExecutionAttempts(true);
					throw $e;
				}
			}

			$this->updateJob($job, 'File imported, copy to shared folder', KalturaBatchJobStatus::PROCESSED);

			$job = $this->moveFile($job, $data);
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
	private function moveFile(KalturaBatchJob $job, KalturaImportJobData $data)
	{
		try
		{
			// creates a shared file path or use the one set on the job data if provided
			$destFile = $data->destFileLocalPath;
			$sharedFile = $data->destFileSharedPath;
			if(!$sharedFile)
			{
				$rootPath = self::$taskConfig->params->sharedTempPath;
				$res = self::createDir( $rootPath );
				if ( !$res )
				{
					KalturaLog::err("Cannot continue import without shared directory");
					die();
				}
				$sharedFile = $rootPath . DIRECTORY_SEPARATOR . uniqid('import_');
			}

			$ext = pathinfo($destFile, PATHINFO_EXTENSION);
			if(strlen($ext))
			{
				$sharedFile .= ".$ext";
				//If we changed the shared file name we need to update it on the jobs data
				if($data->destFileSharedPath)
				{
					$data->destFileSharedPath = $sharedFile;
				}
			}
			
			KalturaLog::debug("rename('$destFile', '$sharedFile')");
			kFile::moveFile($destFile, $sharedFile);
			if(!kFile::checkFileExists($sharedFile))
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
				$this->closeJob($job, null, null, 'Successfully moved file', KalturaBatchJobStatus::FINISHED, $data);
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
	
	protected function getTempFilePath($remotePath, $fileSize = null)
	{
	    // create a temp file path
		$rootPath = self::$taskConfig->params->localTempPath;
		$fileSizeThreshold = isset(self::$taskConfig->params->fileSizeThreshold) ? self::$taskConfig->params->fileSizeThreshold : null;
		$shardTempPath = isset(self::$taskConfig->params->sharedTempPath) ? self::$taskConfig->params->sharedTempPath : null;
		if ($fileSize && $fileSizeThreshold && $shardTempPath && $fileSize > $fileSizeThreshold )
		{
			$rootPath = $shardTempPath;
		}

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
	
	/**
	 * @param $partialFilePath
	 */
	protected function movePartialFileToShared(KalturaImportJobData &$data, $partialFileSize)
	{
		$sharedTempPath = self::$taskConfig->params->sharedTempPath;
		if(!$sharedTempPath)
		{
			KalturaLog::err("Cannot continue import without shared directory");
			throw new Exception("Cannot continue import without shared directory");
		}
		
		
		$res = self::createDir( $sharedTempPath );
		if ( !$res )
		{
			KalturaLog::err("Failed to create shared dir, cannot continue import without shared directory");
			throw new Exception("Failed to create shared dir, cannot continue import without shared directory");
		}
		
		$partialFilePath = $data->destFileLocalPath;
		$sharedTempFilePath = $sharedTempPath . DIRECTORY_SEPARATOR . basename($partialFilePath);
		
		if (self::$currentEngine == self::AXEL_DOWNLOAD_ENGINE)
		{
			$axelStatePartialFilePath = $partialFilePath . '.st';
			$axelStateSharedTempFilePath = $sharedTempFilePath . '.st';
		}
		
		if(!kFile::moveFile($partialFilePath, $sharedTempFilePath))
		{
			//If we failed to copy local file to shared location reset download operation
			kFile::unlink($partialFilePath);
			
			if (self::$currentEngine == self::AXEL_DOWNLOAD_ENGINE)
			{
				kFile::unlink($axelStatePartialFilePath);
			}
			
			return array(0, null);
		}
		
		
		if (self::$currentEngine == self::AXEL_DOWNLOAD_ENGINE)
		{
			// some servers does not support multi-connection in which case there will be no state file
			if(kFile::checkFileExists($axelStatePartialFilePath) && !kFile::moveFile($axelStatePartialFilePath, $axelStateSharedTempFilePath))
			{
				//If we failed to copy local file to shared location reset download operation
				kFile::unlink($sharedTempFilePath);
				kFile::unlink($axelStatePartialFilePath);
				return array(0, null);
			}
			
		}
		
		$data->destFileLocalPath = $sharedTempFilePath;
		return array($partialFileSize, $data);
	}

	protected function getUrlForExtension($sourceUrl, $partnerId)
	{
		$useRedirectPartners = explode(',', self::$taskConfig->params->redirectedUrlPartnerIds);
		if(is_null($useRedirectPartners) || !in_array($partnerId, $useRedirectPartners))
		{
			return $sourceUrl;
		}

		$curlWrapperForRedirect = new KCurlWrapper(self::$taskConfig->params);
		$curlWrapperForRedirect->setTimeout(self::HEADERS_TIMEOUT);
		$curlWrapperForRedirect->getHeader($sourceUrl);
		$redirectUrl = $curlWrapperForRedirect->getInfo(CURLINFO_EFFECTIVE_URL);
		$curlWrapperForRedirect->close();

		if(!is_null($redirectUrl))
		{
			$ext = pathinfo($redirectUrl, PATHINFO_EXTENSION);
			if(!is_null($ext))
			{
				return $redirectUrl;
			}
		}
		return $sourceUrl;
	}
	
	protected function getRedirectUrlIfExist($url)
	{
		$curlWrapper = new KCurlWrapper(self::$taskConfig->params);
		$curlWrapper->setTimeout(self::HEADERS_TIMEOUT);
		$curlWrapper->getHeader($url);
		
		// if URL has a 'fragment' part, like: protocol://domain/file.ext?query#fragment
		// CURLINFO_EFFECTIVE_URL will return: protocol://domain/file.ext?query
		// we will treat it as 'redirectUrl'
		$redirectUrl = $curlWrapper->getInfo(CURLINFO_EFFECTIVE_URL);
		
		$curlWrapper->close();
		
		return ($redirectUrl && $url != $redirectUrl) ? $redirectUrl : null;
	}
	
	private function isPartialFile($errNumber)
	{
		switch (self::$currentEngine)
		{
			case self::CURL_DOWNLOAD_ENGINE:
				return $errNumber == CURLE_PARTIAL_FILE;
				
			case self::AXEL_DOWNLOAD_ENGINE:
				return KAxelWrapper::$partiallyDownloaded;
				
			default:
				return false;
		}
	}
}
