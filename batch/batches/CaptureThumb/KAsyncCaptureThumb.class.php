<?php
/**
 * @package Scheduler
 * @subpackage Capture-Thumbnail
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
 * @subpackage Capture-Thumbnail
 */
class KAsyncCaptureThumb extends KJobHandlerWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::CAPTURE_THUMB;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->captureThumb($job, $job->data);
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::getMaxJobsEachRun()
	 */
	protected function getMaxJobsEachRun()
	{
		return 1;
	}
	
	private function captureThumb(KalturaBatchJob $job, KalturaCaptureThumbJobData $data)
	{
		$thumbParamsOutput = self::$kClient->thumbParamsOutput->get($data->thumbParamsOutputId);
		
		try
		{
			$mediaFile = trim($data->fileContainer->filePath);
			
			if(!file_exists($mediaFile))
				return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "Source file $mediaFile does not exist", KalturaBatchJobStatus::RETRY);
			
			if(!is_file($mediaFile))
				return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "Source file $mediaFile is not a file", KalturaBatchJobStatus::FAILED);
				
			$this->updateJob($job,"Capturing thumbnail on $mediaFile", KalturaBatchJobStatus::QUEUED);
		}
		catch(Exception $ex)
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED);
		}
		
		try
		{
			$data->thumbPath = null;
			// creates a temp file path
			$rootPath = self::createDir(self::$taskConfig->params->localTempPath);
			if (!$rootPath)
				die();

			$capturePath = null;
			if($data->srcAssetType == KalturaAssetType::FLAVOR)
			{
				$capturePath = $this->createUniqFileName($rootPath);
				list($mediaInfoWidth, $mediaInfoHeight, $mediaInfoDar, $mediaInfoVidDur, $mediaInfoScanType) = $this->getMediaInfoData($job->partnerId, $data->srcAssetId);
				
				// generates the thumbnail
				$thumbMaker = new KFFMpegThumbnailMaker($mediaFile, $capturePath, self::$taskConfig->params->FFMpegCmd);
				$videoOffset = max(0 ,min($thumbParamsOutput->videoOffset, $mediaInfoVidDur-1));
				$params['dar'] = $mediaInfoDar;
				$params['vidDur'] = $mediaInfoVidDur;
				$params['scanType'] = $mediaInfoScanType;
				$created = $thumbMaker->createThumnail($videoOffset, $mediaInfoWidth, $mediaInfoHeight, $params);
				if(!$created || !file_exists($capturePath))
					return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::THUMBNAIL_NOT_CREATED, "Thumbnail not created", KalturaBatchJobStatus::FAILED);
				
				$this->updateJob($job, "Thumbnail captured [$capturePath]", KalturaBatchJobStatus::PROCESSING);
			}
			
			$thumbPath = $this->createUniqFileName($rootPath);
			
			if ($capturePath || !$data->fileContainer->encryptionKey)
			{
				//if generate the thumb here or the file is not encrypt just crop
				$srcPath = $capturePath ? $capturePath : $mediaFile;
				$cropped = $this->crop($srcPath ,$thumbPath, $thumbParamsOutput);
			}
			else 
			{
				$tempPath = self::createTempClearFile($mediaFile, $data->fileContainer->encryptionKey);
				$cropped = $this->crop($tempPath ,$thumbPath, $thumbParamsOutput);
				unlink($tempPath);
			}
			
			
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
			$this->unimpersonate();
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
		// creates a temp file path
		$rootPath = self::createDir(self::$taskConfig->params->sharedTempPath);
		if (!$rootPath)
			throw new Exception("Cannot create temp thumbnail directory [$rootPath] due to an error. Please fix and restart", -1);
		
		$sharedFile = $this->createUniqFileName($rootPath);
		
		clearstatcache();
		$fileSize = filesize($data->thumbPath);
		rename($data->thumbPath, $sharedFile);
		if(!file_exists($sharedFile) || filesize($sharedFile) != $fileSize)
		{
			$err = 'moving file failed';
			throw new Exception($err, -1);
		}
		
		$this->setFilePermissions($sharedFile);
		$data->thumbPath = $sharedFile;
		$job->data = $data;
		return $job;
	}

	private function crop($srcPath, $targetPath,$thumbParamsOutput )
	{
		$quality = $thumbParamsOutput->quality;
		$cropType = $thumbParamsOutput->cropType;
		$cropX = $thumbParamsOutput->cropX;
		$cropY = $thumbParamsOutput->cropY;
		$cropWidth = $thumbParamsOutput->cropWidth;
		$cropHeight = $thumbParamsOutput->cropHeight;
		$bgcolor = $thumbParamsOutput->backgroundColor;
		$width = $thumbParamsOutput->width;
		$height = $thumbParamsOutput->height;
		$scaleWidth = $thumbParamsOutput->scaleWidth;
		$scaleHeight = $thumbParamsOutput->scaleHeight;
		$density = $thumbParamsOutput->density;
		$rotate = $thumbParamsOutput->rotate;

		$cropper = new KImageMagickCropper($srcPath, $targetPath, self::$taskConfig->params->ImageMagickCmd, true);
		return $cropper->crop($quality, $cropType, $width, $height, $cropX, $cropY, $cropWidth, $cropHeight, $scaleWidth, $scaleHeight, $bgcolor, $density, $rotate);
	}
	
	private function getMediaInfoData($partnerId, $srcAssetId)
	{
		$mediaInfoWidth = null;
		$mediaInfoHeight = null;
		$mediaInfoDar = null;
		$mediaInfoVidDur = null;
		$mediaInfoScanType = null;
		$mediaInfoFilter = new KalturaMediaInfoFilter();
		$mediaInfoFilter->flavorAssetIdEqual = $srcAssetId;
		$this->impersonate($partnerId);
		$mediaInfoList = self::$kClient->mediaInfo->listAction($mediaInfoFilter);
		$this->unimpersonate();
		if(count($mediaInfoList->objects))
		{
			$mediaInfo = reset($mediaInfoList->objects);
			/* @var $mediaInfo KalturaMediaInfo */
			$mediaInfoWidth = $mediaInfo->videoWidth;
			$mediaInfoHeight = $mediaInfo->videoHeight;
			$mediaInfoDar = $mediaInfo->videoDar;
			$mediaInfoScanType = $mediaInfo->scanType;

			if($mediaInfo->videoDuration)
				$mediaInfoVidDur = $mediaInfo->videoDuration/1000;
			else if ($mediaInfo->containerDuration)
				$mediaInfoVidDur = $mediaInfo->containerDuration/1000;
			else if($mediaInfo->audioDuration)
				$mediaInfoVidDur = $mediaInfo->audioDuration/1000;
		}
		return array($mediaInfoWidth, $mediaInfoHeight, $mediaInfoDar, $mediaInfoVidDur, $mediaInfoScanType);
	}
	
	private function createUniqFileName($rootPath)
	{
		return realpath($rootPath) . DIRECTORY_SEPARATOR . uniqid('thumb_');
	}

}
