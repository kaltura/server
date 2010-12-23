<?php
require_once ("bootstrap.php");
/**
 * Will convert a single flavor and store it in the file system.
 * The state machine of the job is as follows:
 * 	 	get the flavor 
 * 		convert using the right method
 * 		save recovery file in case of crash
 * 		move the file to the archive
 * 		set the entry's new status and file details 
 *
 * 
 * @package Scheduler
 * @subpackage Capture-Thumbnail
 */
class KAsyncCaptureThumb extends KBatchBase
{
	/**
	 * @return int
	 */
	public static function getType()
	{
		return KalturaBatchJobType::CAPTURE_THUMB;
	}
	
	protected function init()
	{
		$this->saveQueueFilter(self::getType());
	}
	
	public function run($jobs = null)
	{
		KalturaLog::info("Capture thumbnail batch is running");
		
		if($this->taskConfig->isInitOnly())
			return $this->init();
		
		if(is_null($jobs))
			$jobs = $this->kClient->batch->getExclusiveCaptureThumbJobs($this->getExclusiveLockKey(), $this->taskConfig->maximumExecutionTime, 1, $this->getFilter());
		
		KalturaLog::info(count($jobs) . " capture thumbnail jobs to perform");
		
		if(!count($jobs))
		{
			KalturaLog::info("Queue size: 0 sent to scheduler");
			$this->saveSchedulerQueue(self::getType());
			return null;
		}
		
		foreach($jobs as &$job) {
			$job = $this->captureThumb($job, $job->data);
		}
		return $jobs;
	}
	
	private function captureThumb(KalturaBatchJob $job, KalturaCaptureThumbJobData $data)
	{
		KalturaLog::debug("captureThumb($job->id)");
		
		try
		{
			$mediaFile = trim($data->srcFileSyncLocalPath);
			
			if(!file_exists($mediaFile))
				return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "Source file $mediaFile does not exist", KalturaBatchJobStatus::RETRY);
			
			if(!is_file($mediaFile))
				return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "Source file $mediaFile is not a file", KalturaBatchJobStatus::FAILED);
				
			KalturaLog::debug("mediaFile [$mediaFile]");
			$this->updateJob($job,"Capturing thumbnail on $mediaFile", KalturaBatchJobStatus::QUEUED, 1);
		}
		catch(Exception $ex)
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED);
		}
		
		try
		{
			$data->thumbPath = null;
			
			// creates a temp file path 
			$rootPath = $this->taskConfig->params->localTempPath;
			if(! is_dir($rootPath))
			{
				if(! file_exists($rootPath))
				{
					KalturaLog::info("Creating temp thumbnail directory [$rootPath]");
					mkdir($rootPath);
				}
				else
				{
					// already exists but not a directory 
					KalturaLog::err("Cannot create temp thumbnail directory [$rootPath] due to an error. Please fix and restart");
					die();
				}
			}
				
			$capturePath = $mediaFile;
			if($data->srcAssetType == KalturaAssetType::FLAVOR)
			{
				// creates the path
				$uniqid = uniqid('thumb_');
				$capturePath = realpath($rootPath) . "/$uniqid";
					
				// generates the thumbnail
				$thumbMaker = new KFFMpegThumbnailMaker($mediaFile, $capturePath, $this->taskConfig->params->FFMpegCmd);
				$created = $thumbMaker->createThumnail($data->thumbParamsOutput->videoOffset);
				if(!$created || !file_exists($capturePath))
					return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::THUMBNAIL_NOT_CREATED, "Thumbnail not created", KalturaBatchJobStatus::FAILED);
				
				$this->updateJob($job, "Thumbnail captured [$capturePath]", KalturaBatchJobStatus::PROCESSING, 40);
			}
			else 
			{
				KalturaLog::info("Source file is already an image");
			}
			
			$uniqid = uniqid('thumb_');
			$thumbPath = realpath($rootPath) . "/$uniqid";
			
			$quality = $data->thumbParamsOutput->quality;
			$cropType = $data->thumbParamsOutput->cropType;
			$cropX = $data->thumbParamsOutput->cropX;
			$cropY = $data->thumbParamsOutput->cropY;
			$cropWidth = $data->thumbParamsOutput->cropWidth;
			$cropHeight = $data->thumbParamsOutput->cropHeight;
			$bgcolor = $data->thumbParamsOutput->backgroundColor;
			$width = $data->thumbParamsOutput->width;
			$height = $data->thumbParamsOutput->height;
			$scaleWidth = $data->thumbParamsOutput->scaleWidth;
			$scaleHeight = $data->thumbParamsOutput->scaleHeight;
			
			$cropper = new KImageMagickCropper($capturePath, $thumbPath, $this->taskConfig->params->ImageMagickCmd, true);
			$cropped = $cropper->crop($quality, $cropType, $width, $height, $cropX, $cropY, $cropWidth, $cropHeight, $scaleWidth, $scaleHeight, $bgcolor);
			if(!$cropped || !file_exists($thumbPath))
				return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::THUMBNAIL_NOT_CREATED, "Thumbnail not cropped", KalturaBatchJobStatus::FAILED);
				
			$data->thumbPath = $thumbPath;
			$job = $this->moveFile($job, $data);
				
			if($this->checkFileExists($job->data->thumbPath))
			{
				$updateData = new KalturaCaptureThumbJobData();
				$updateData->thumbPath = $data->thumbPath;
				return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::FINISHED, $updateData);
			}
			
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, 'File not moved correctly', KalturaBatchJobStatus::FAILED, $data);
		}
		catch(Exception $ex)
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED);
		}
	}
	
	/**
	 * @param KalturaBatchJob $job
	 * @param KalturaCaptureThumbJobData $data
	 * @return KalturaBatchJob
	 */
	private function moveFile(KalturaBatchJob $job, KalturaCaptureThumbJobData $data)
	{
		KalturaLog::debug("moveFile($job->id, $data->thumbPath)");
		
		// creates a temp file path 
		$rootPath = $this->taskConfig->params->sharedTempPath;
		if(! is_dir($rootPath))
		{
			if(! file_exists($rootPath))
			{
				KalturaLog::info("Creating temp thumbnail directory [$rootPath]");
				mkdir($rootPath);
			}
			else
			{
				// already exists but not a directory 
				$err = "Cannot create temp thumbnail directory [$rootPath] due to an error. Please fix and restart";
				throw new Exception($err, -1);
			}
		}
		
		$uniqid = uniqid('thumb_');
		$sharedFile = realpath($rootPath) . "/$uniqid";
		
		clearstatcache();
		$fileSize = filesize($data->thumbPath);
		rename($data->thumbPath, $sharedFile);
		if(!file_exists($sharedFile) || filesize($sharedFile) != $fileSize)
		{
			$err = 'moving file failed';
			throw new Exception($err, -1);
		}
		
		@chmod($sharedFile, 0777);
		$data->thumbPath = $sharedFile;
		$job->data = $data;
		return $job;
	}
	
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job)
	{
		return $this->kClient->batch->updateExclusiveCaptureThumbJob($jobId, $this->getExclusiveLockKey(), $job);
	}
	
	protected function freeExclusiveJob(KalturaBatchJob $job)
	{
		$resetExecutionAttempts = false;
		if($job->status == KalturaBatchJobStatus::ALMOST_DONE)
			$resetExecutionAttempts = true;
	
		$response = $this->kClient->batch->freeExclusiveCaptureThumbJob($job->id, $this->getExclusiveLockKey(), $resetExecutionAttempts);
		
		KalturaLog::info("Queue size: $response->queueSize sent to scheduler");
		$this->saveSchedulerQueue(self::getType(), $response->queueSize);
		
		return $response->job;
	}
}
