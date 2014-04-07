<?php
/**
 * @package Scheduler
 * @subpackage Post-Convert
 */

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
class KAsyncPostConvert extends KJobHandlerWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::POSTCONVERT;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->postConvert($job, $job->data);
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::getMaxJobsEachRun()
	 */
	protected function getMaxJobsEachRun()
	{
		return 1;
	}

	/**
	 * @param KalturaBatchJob $job
	 * @param KalturaPostConvertJobData $data
	 * @return KalturaBatchJob
	 */
	private function postConvert(KalturaBatchJob $job, KalturaPostConvertJobData $data)
	{
		if($data->flavorParamsOutputId)
			$data->flavorParamsOutput = KBatchBase::$kClient->flavorParamsOutput->get($data->flavorParamsOutputId);
		
		try
		{
			$srcFileSyncDescriptor = reset($data->srcFileSyncs);
			$mediaFile = null;
			if($srcFileSyncDescriptor)
				$mediaFile = trim($srcFileSyncDescriptor->fileSyncLocalPath);
			
			if(!$data->flavorParamsOutput || !$data->flavorParamsOutput->sourceRemoteStorageProfileId)
			{
				if(!$this->pollingFileExists($mediaFile))
					return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "Source file $mediaFile does not exist", KalturaBatchJobStatus::RETRY);
				
				if(!is_file($mediaFile))
					return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "Source file $mediaFile is not a file", KalturaBatchJobStatus::FAILED);
			}
			
			KalturaLog::debug("mediaFile [$mediaFile]");
			$this->updateJob($job,"Extracting file media info on $mediaFile", KalturaBatchJobStatus::QUEUED);
		}
		catch(Exception $ex)
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED);
		}
		
		$mediaInfo = null;
		try
		{
			$engine = KBaseMediaParser::getParser($job->jobSubType, realpath($mediaFile), KBatchBase::$taskConfig, $job);
			if($engine)
			{
				KalturaLog::info("Media info engine [" . get_class($engine) . "]");
				$mediaInfo = $engine->getMediaInfo();
			}
			else
			{
				$err = "Media info engine not found for job subtype [".$job->jobSubType."]";
				KalturaLog::info($err);
				return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::ENGINE_NOT_FOUND, $err, KalturaBatchJobStatus::FAILED);
			}
		}
		catch(Exception $ex)
		{
			KalturaLog::err("Error: " . $ex->getMessage());
			$mediaInfo = null;
		}
		
		/* @var $mediaInfo KalturaMediaInfo */
		if(is_null($mediaInfo))
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::EXTRACT_MEDIA_FAILED, "Failed to extract media info: $mediaFile", KalturaBatchJobStatus::FAILED);

		/* Look for silent/black conversions. Cuurently checked only for Webex/ARF products */
		$detectMsg = $this->checkForSilentAudioAndBlackVideo($job, $data, realpath($mediaFile), $mediaInfo);
		if(isset($detectMsg)){
			$job->data->engineMessage = $detectMsg;
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::BLACK_OR_SILENT_CONTENT, $detectMsg, KalturaBatchJobStatus::FAILED);
		}
				
		try
		{
			$mediaInfo->flavorAssetId = $data->flavorAssetId;
			$createdMediaInfo = $this->getClient()->batch->addMediaInfo($mediaInfo);
			/* @var $createdMediaInfo KalturaMediaInfo */
			
			// must save the mediaInfoId before reporting that the task is finished
			$this->updateJob($job, "Saving media info id $createdMediaInfo->id", KalturaBatchJobStatus::PROCESSED, $data);
			
			$data->thumbPath = null;
			if(!$data->createThumb)
				return $this->closeJob($job, null, null, "Media info id $createdMediaInfo->id saved", KalturaBatchJobStatus::FINISHED, $data);
			
			// creates a temp file path
			$rootPath = KBatchBase::$taskConfig->params->localTempPath;
			$this->createDir($rootPath);
				
			// creates the path
			$uniqid = uniqid('thumb_');
			$thumbPath = $rootPath . DIRECTORY_SEPARATOR . $uniqid;
			
			$videoDurationSec = floor($mediaInfo->videoDuration / 1000);
			$data->thumbOffset = max(0 ,min($data->thumbOffset, $videoDurationSec));
			
			if($mediaInfo->videoHeight)
				$data->thumbHeight = $mediaInfo->videoHeight;
			
			if($mediaInfo->videoBitRate)
				$data->thumbBitrate = $mediaInfo->videoBitRate;
					
			// generates the thumbnail
			$thumbMaker = new KFFMpegThumbnailMaker($mediaFile, $thumbPath, KBatchBase::$taskConfig->params->FFMpegCmd);
			$created = $thumbMaker->createThumnail($data->thumbOffset, $mediaInfo->videoWidth, $mediaInfo->videoHeight, null, null, $mediaInfo->videoDar);
			
			if(!$created || !file_exists($thumbPath))
			{
				$data->createThumb = false;
				return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::THUMBNAIL_NOT_CREATED, 'Thumbnail not created', KalturaBatchJobStatus::FINISHED, $data);
			}
			$data->thumbPath = $thumbPath;
			
			$job = $this->moveFile($job, $data);
			
			if($this->checkFileExists($job->data->thumbPath))
				return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::FINISHED, $data);
			
			$data->createThumb = false;
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, 'File not moved correctly', KalturaBatchJobStatus::FINISHED, $data);
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
		$rootPath = KBatchBase::$taskConfig->params->sharedTempPath;
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
		$sharedFile = realpath($rootPath) . DIRECTORY_SEPARATOR . $uniqid;
		
		clearstatcache();
		$fileSize = kFile::fileSize($data->thumbPath);
		rename($data->thumbPath, $sharedFile);
		if(!file_exists($sharedFile) || kFile::fileSize($sharedFile) != $fileSize)
		{
			$err = 'moving file failed';
			throw new Exception($err, -1);
		}
		
		$this->setFilePermissions($sharedFile);
		$data->thumbPath = $sharedFile;
		$job->data = $data;
		return $job;
	}
	
	/**
	 * Check for invalidly generated content files -
	 * - Silent or black content for at least 50% of the total duration
	 * - The detection duration - at least 2 sec
	 * - Applicable only to Webex sources
	 * @param KalturaBatchJob $job
	 * @param KalturaPostConvertJobData $data
	 * $param $mediaFile
	 * #param KalturaMediaInfo $mediaInfo
	 * @return string
	 */
	private function checkForSilentAudioAndBlackVideo(KalturaBatchJob $job, KalturaPostConvertJobData $data, $srcFileName, KalturaMediaInfo $mediaInfo)
	{
		KalturaLog::debug("checkForSilentAudioAndBlackVideo(contDur:$mediaInfo->containerDuration,vidDur:$mediaInfo->videoDuration,audDur:$mediaInfo->audioDuration)");

		/*
		 * Check for Webex, other sources should not be checked
		 */
		if(!(isset($data->flavorParamsOutput) && isset($data->flavorParamsOutput->operators)
		&& strstr($data->flavorParamsOutput->operators, "webexNbrplayer.WebexNbrplayer")!=false)) {
			return null;
		}
		
		list($silenceDetect, $blackDetect) = KFFMpegMediaParser::checkForSilentAudioAndBlackVideo(KBatchBase::$taskConfig->params->FFMpegCmd, $srcFileName, $mediaInfo);
		
		$detectMsg = $silenceDetect;
		if(isset($blackDetect))
			$detectMsg = isset($detectMsg)?"$detectMsg,$blackDetect":$blackDetect;
		return $detectMsg;
	}
}
