<?php
/**
 * @package Scheduler
 * @subpackage Conversion
 */

/**
 * Will close almost done conversions that sent to remote systems and store the files in the file system.
 * The state machine of the job is as follows:
 * 	 	get almost done conversions
 * 		check the convert status
 * 		download the converted file
 * 		save recovery file in case of crash
 * 		move the file to the archive
 *
 * @package Scheduler
 * @subpackage Conversion
 */
class KAsyncConvertCollectionCloser extends KJobCloserWorker
{
	private $localTempPath;
	private $sharedTempPath;
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::CONVERT_COLLECTION;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->closeConvert($job, $job->data);
	}
	
	public function run($jobs = null)
	{
		// creates a temp file path
		$this->localTempPath = self::$taskConfig->params->localTempPath;
		$this->sharedTempPath = self::$taskConfig->params->sharedTempPath;
	
		$res = self::createDir( $this->localTempPath );
		if ( !$res )
		{
			KalturaLog::err( "Cannot continue conversion without temp local directory");
			return null;
		}
		
		$res = self::createDir( $this->sharedTempPath );
		if ( !$res )
		{
			KalturaLog::err( "Cannot continue conversion without temp shared directory");
			return null;
		}
			
		return parent::run($jobs);
	}
	
	private function closeConvert(KalturaBatchJob $job, KalturaConvertCollectionJobData $data)
	{
		if(($job->queueTime + self::$taskConfig->params->maxTimeBeforeFail) < time())
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CLOSER_TIMEOUT, 'Timed out', KalturaBatchJobStatus::FAILED);

		$filesToDownload = array(
			"{$data->destDirRemoteUrl}/{$data->destFileName}.log",
			"{$data->destDirRemoteUrl}/{$data->destFileName}.ism",
			"{$data->destDirRemoteUrl}/{$data->destFileName}.ismc",
			"{$data->destDirRemoteUrl}/{$data->destFileName}_Thumb.jpg"
		);
		
		foreach($filesToDownload as $index => $file)
		{
			$fileName = basename($file);
			$destFilePath = "{$this->localTempPath}/$fileName";
			if(!$this->fetchFile($file, $destFilePath))
				unset($filesToDownload[$index]);
		}
		
		foreach($data->flavors as $flavor)
		{
			$fileName = basename($flavor->destFileSyncRemoteUrl);
			$flavor->destFileSyncLocalPath = "{$this->localTempPath}/$fileName";
			
			$filesToDownload[] = $flavor->destFileSyncRemoteUrl;
			$err = null;
			if(!$this->fetchFile($flavor->destFileSyncRemoteUrl, $flavor->destFileSyncLocalPath, $err))
				return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::REMOTE_DOWNLOAD_FAILED, $err, KalturaBatchJobStatus::ALMOST_DONE);
		}
		
		return $this->moveFiles($job, $data);
	}
	
	private function moveFiles(KalturaBatchJob $job, KalturaConvertCollectionJobData $data)
	{
		clearstatcache();
		$files2move = array();
		
		$fileNames = array(
			$data->destFileName . '.log',
			$data->destFileName . '.ism',
			$data->destFileName . '.ismc',
			$data->destFileName . '_Thumb.jpg'
		);
		
		foreach($fileNames as $fileName)
		{
			$srcPath = "{$this->localTempPath}/$fileName";
			if(!file_exists($srcPath))
				continue;
				
			$destPath = "{$this->sharedTempPath}/$fileName";
			$sharedPath = $this->translateLocalPath2Shared($destPath);
			$fileSize = kFile::fileSize($srcPath);
			
			$files2move[] = array(
				'from' => $srcPath,
				'to' => $destPath,
				'path' => $sharedPath,
				'size' => $fileSize,
			);
		}
		
		foreach($data->flavors as $flavor)
		{
			$srcPath = $flavor->destFileSyncLocalPath;
			$destPath = $this->sharedTempPath . DIRECTORY_SEPARATOR . basename($srcPath);
			$sharedPath = $this->translateLocalPath2Shared($destPath);
			$fileSize = kFile::fileSize($srcPath);
			
			$flavor->destFileSyncLocalPath = $sharedPath;
			if(self::$taskConfig->params->isRemote)
				$flavor->destFileSyncRemoteUrl = $this->distributedFileManager->getRemoteUrl($sharedPath);
			
			$files2move[] = array(
				'from' => $srcPath,
				'to' => $destPath,
				'path' => $sharedPath,
				'size' => $fileSize,
			);
		}
		
		foreach($files2move as $file2move)
		{
			$srcPath = $file2move['from'];
			$destPath = $file2move['to'];
			$fileSize = $file2move['size'];
			
			if(file_exists($destPath))
				unlink($destPath);
				
			KalturaLog::info("rename($srcPath, $destPath)");
			rename($srcPath, $destPath);
		
			if(!file_exists($destPath) || kFile::fileSize($destPath) != $fileSize)
			{
				KalturaLog::err("Error: moving file [$srcPath] failed");
				die();
			}
			$this->setFilePermissions($destPath);
		}
		
		$data->destDirLocalPath = $this->translateLocalPath2Shared($this->sharedTempPath);
		if($this->checkFilesArrayExist($files2move))
		{
			$job->status = KalturaBatchJobStatus::FINISHED;
			$job->message = "Files moved to shared";
		}
		else
		{
			$job->status = KalturaBatchJobStatus::ALMOST_DONE;
			$job->message = "Files not moved correctly";
		}
		return $this->closeJob($job, null, null, $job->message, $job->status, $data);
	}
	
	/**
	 * @param string $srcFileSyncRemoteUrl
	 * @param string $srcFileSyncLocalPath
	 * @param string $errDescription
	 * @return string
	 */
	private function fetchFile($srcFileSyncRemoteUrl, $srcFileSyncLocalPath, &$errDescription = null)
	{
		try
		{
			$curlWrapper = new KCurlWrapper();
			$curlHeaderResponse = $curlWrapper->getHeader($srcFileSyncRemoteUrl, true, true);
			if(!$curlHeaderResponse || $curlWrapper->getError())
			{
				$errDescription = "Error: " . $curlWrapper->getError();
				return false;
			}
			
			if($curlHeaderResponse->code != KCurlHeaderResponse::HTTP_STATUS_OK)
			{
				$errDescription = "HTTP Error: " . $curlHeaderResponse->code . " " . $curlHeaderResponse->codeName;
				return false;
			}
			$fileSize = null;
			KalturaLog::debug("Headers:\n" . print_r($curlHeaderResponse->headers, true));
			if(isset($curlHeaderResponse->headers['content-length']))
				$fileSize = $curlHeaderResponse->headers['content-length'];
			$curlWrapper->close();
			
			if($fileSize && file_exists($srcFileSyncLocalPath))
			{
				clearstatcache();
				$actualFileSize = kFile::fileSize($srcFileSyncLocalPath);
				
				if($actualFileSize == $fileSize)
				{
					KalturaLog::log("File [$srcFileSyncLocalPath] already exists with right size[$fileSize]");
					return true;
				}
				
				KalturaLog::log("File [$srcFileSyncLocalPath] already exists with wrong size[$actualFileSize] expected size[$fileSize]");
				KalturaLog::info("Unlink file[$srcFileSyncLocalPath]");
				unlink($srcFileSyncLocalPath);
			}
			
			$curlWrapper = new KCurlWrapper();
			$res = $curlWrapper->exec($srcFileSyncRemoteUrl, $srcFileSyncLocalPath, null, true);
			KalturaLog::debug("Curl results: $res");
		
			if(!$res || $curlWrapper->getError())
			{
				$errDescription = "Error: " . $curlWrapper->getError();
				$curlWrapper->close();
				return false;
			}
			$curlWrapper->close();
			
			if(!file_exists($srcFileSyncLocalPath))
			{
				$errDescription = "Error: output file[$srcFileSyncLocalPath] doesn't exist";
				return false;
			}
			
			clearstatcache();
			$actualFileSize = kFile::fileSize($srcFileSyncLocalPath);
			KalturaLog::info("Fetched file to [$srcFileSyncLocalPath] size[$actualFileSize]");
				
			if($fileSize)
			{
				if($actualFileSize != $fileSize)
				{
					$errDescription = "Error: output file[$srcFileSyncLocalPath] have a wrong size[$actualFileSize] expected size[$fileSize]";
					return false;
				}
			}
		}
		catch(Exception $ex)
		{
			$errDescription = "Error: " . $ex->getMessage();
			return false;
		}
		
		return true;
	}
	
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job)
	{
		$flavors = null;
		if($job->data && $job->data->flavors)
		{
			$flavors = $job->data->flavors;
			$job->data->flavors = null;
		}
		return self::$kClient->batch->updateExclusiveConvertCollectionJob($jobId, $this->getExclusiveLockKey(), $job, $flavors);
	}
}
