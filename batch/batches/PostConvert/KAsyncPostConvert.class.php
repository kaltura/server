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
 * @subpackage Post-Convert
 */
class KAsyncPostConvert extends KBatchBase
{
	/**
	 * @return int
	 */
	public static function getType()
	{
		return KalturaBatchJobType::POSTCONVERT;
	}
	
	protected function init()
	{
		$this->saveQueueFilter(self::getType());
	}
	
	public function run($jobs = null)
	{
		KalturaLog::info("Post convert batch is running");
		
		if($this->taskConfig->isInitOnly())
			return $this->init();
		
		if(is_null($jobs))
			$jobs = $this->kClient->batch->getExclusivePostConvertJobs($this->getExclusiveLockKey(), $this->taskConfig->maximumExecutionTime, 1, $this->getFilter());
		
		KalturaLog::info(count($jobs) . " post convert jobs to perform");
		
		if(!count($jobs))
		{
			KalturaLog::info("Queue size: 0 sent to scheduler");
			$this->saveSchedulerQueue(self::getType());
			return null;
		}
		
		foreach($jobs as &$job) {
			$job = $this->postConvert($job, $job->data);
		}
		return $jobs;
	}
	
	/**
	 * extractMediaInfo extract the file info using mediainfo and parse the returned data
	 *  
	 * @param string $mediaFile file full path
	 * @return KalturaMediaInfo or null for failure
	 */
	private function extractMediaInfo($mediaFile)
	{
		KalturaLog::debug("extractMediaInfo($mediaFile)");
		
		$mediaParser = new KMediaInfoMediaParser($mediaFile, $this->taskConfig->params->mediaInfoCmd);
		return $mediaParser->getMediaInfo();
	}
	
	/**
	 * extractFfmpegInfo extract the file info using FFmpeg and parse the returned data
	 *  
	 * @param string $mediaFile file full path
	 * @return KalturaMediaInfo or null for failure
	 */
	private function extractFfmpegInfo($mediaFile)
	{
		KalturaLog::debug("extractFfmpegInfo($mediaFile)");
		
		$mediaParser = new KFFMpegMediaParser($mediaFile, $this->taskConfig->params->FFMpegCmd);
		return $mediaParser->getMediaInfo();
	}
	
	private function postConvert(KalturaBatchJob $job, KalturaPostConvertJobData $data)
	{
		KalturaLog::debug("postConvert($job->id)");
		
		try
		{
			$mediaFile = trim($data->srcFileSyncLocalPath);
			
			if(!file_exists($mediaFile))
				return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "Source file $mediaFile does not exist", KalturaBatchJobStatus::RETRY);
			
			if(!is_file($mediaFile))
				return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "Source file $mediaFile is not a file", KalturaBatchJobStatus::FAILED);
				
			KalturaLog::debug("mediaFile [$mediaFile]");
			$this->updateJob($job,"Extracting file media info on $mediaFile", KalturaBatchJobStatus::QUEUED, 1);
		}
		catch(Exception $ex)
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED);
		}
		
		$mediaInfo = null;
		
		try
		{
//			if($this->taskConfig->params->useMediaInfo)
				$mediaInfo = $this->extractMediaInfo(realpath($mediaFile));
		}
		catch(Exception $ex)
		{
			KalturaLog::err("Error: " . $ex->getMessage());
			$mediaInfo = null;
		}
		
		try
		{
//			if(is_null($mediaInfo) && $this->taskConfig->params->useFFMpeg)
//				$mediaInfo = $this->extractFfmpegInfo(realpath($mediaFile));
			
			if(is_null($mediaInfo))
			{
				return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::EXTRACT_MEDIA_FAILED, "Failed to extract media info: $mediaFile", KalturaBatchJobStatus::FAILED);
			}
			
			KalturaLog::debug("flavorAssetId [$data->flavorAssetId]");
			$mediaInfo->flavorAssetId = $data->flavorAssetId;
			$mediaInfo = $this->getClient()->batch->addMediaInfo($mediaInfo);
			
			// must save the mediaInfoId before reporting that the task is finished
			$this->updateJob($job, "Saving media info id $mediaInfo->id", KalturaBatchJobStatus::PROCESSED, 50, $data);
			
			$data->thumbPath = null;
			if($data->createThumb)
			{
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
						KalutraLog::err("Cannot create temp thumbnail directory [$rootPath] due to an error. Please fix and restart");
						die();
					}
				}
				
				// creates the path
				$uniqid = uniqid('thumb_');
				$thumbPath = realpath($rootPath) . "/$uniqid";
				
				$videoDurationSec = floor($mediaInfo->videoDuration / 1000);
				$data->thumbOffset = max(0 ,min($data->thumbOffset, $videoDurationSec));
				$width = $mediaInfo->videoWidth;
				$height = $mediaInfo->videoHeight;
					
				// generates the thumbnail
				$thumbMaker = new KFFMpegThumbnailMaker($mediaFile, $thumbPath, $this->taskConfig->params->FFMpegCmd);
				$created = $thumbMaker->createThumnail($data->thumbOffset, $width, $height);
				
				if(!$created || !file_exists($thumbPath))
				{
					$data->createThumb = false;
					return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::THUMBNAIL_NOT_CREATED, 'Thumbnail not created', KalturaBatchJobStatus::FINISHED, null, $data);
				}
				
				$data->thumbPath = $thumbPath;
				
				$job = $this->moveFile($job, $data);
				
				if($this->checkFileExists($job->data->thumbPath))
					return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::FINISHED, null, $data);
				
				$data->createThumb = false;
				return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, 'File not moved correctly', KalturaBatchJobStatus::FINISHED, null, $data);
			}
			
			return $this->closeJob($job, null, null, "Media info id $mediaInfo->id saved", KalturaBatchJobStatus::FINISHED, null, $data);
		}
		catch(Exception $ex)
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED);
		}
	}
	
	/**
	 * @param KalturaBatchJob $job
	 * @param KalturaPostConvertJobData $data
	 * @return KalturaBatchJob
	 */
	private function moveFile(KalturaBatchJob $job, KalturaPostConvertJobData $data)
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
	
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job, $entryStatus = null)
	{
		return $this->kClient->batch->updateExclusivePostConvertJob($jobId, $this->getExclusiveLockKey(), $job, $entryStatus);
	}
	
	protected function freeExclusiveJob(KalturaBatchJob $job)
	{
		$resetExecutionAttempts = false;
		if($job->status == KalturaBatchJobStatus::ALMOST_DONE)
			$resetExecutionAttempts = true;
	
		$response = $this->kClient->batch->freeExclusivePostConvertJob($job->id, $this->getExclusiveLockKey(), $resetExecutionAttempts);
		
		KalturaLog::info("Queue size: $response->queueSize sent to scheduler");
		$this->saveSchedulerQueue(self::getType(), $response->queueSize);
		
		return $response->job;
	}
}
?>