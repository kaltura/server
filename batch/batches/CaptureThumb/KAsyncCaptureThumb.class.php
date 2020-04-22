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
	const LOG_SUFFIX = '.log';
	const BIF_TAG = 'bif';
	const DEFAULT_BIF_INTERVAL = 10;
	const TEMP_FILE_POSTFIX = "temp_1.jpg";

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
			
			if(!kFile::checkFileExists($mediaFile))
				return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "Source file $mediaFile does not exist", KalturaBatchJobStatus::RETRY);
			
			if(!kFile::isFile($mediaFile))
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

			if(KCsvWrapper::contains(self::BIF_TAG, $thumbParamsOutput->tags))
			{
				return $this->createBifFile($job, $data, $rootPath, $mediaFile, $thumbParamsOutput);
			}

			return $this->createBasicThumb($job, $data, $rootPath, $mediaFile, $thumbParamsOutput);
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
		$mediaInfoVideoRotation = null;
		$mediaInfoFilter = new KalturaMediaInfoFilter();
		$mediaInfoFilter->flavorAssetIdEqual = $srcAssetId;
		$this->impersonate($partnerId);
		$mediaInfoList = self::$kClient->mediaInfo->listAction($mediaInfoFilter);
		$this->unimpersonate();
		if($mediaInfoList->objects && count($mediaInfoList->objects))
		{
			$mediaInfo = reset($mediaInfoList->objects);
			/* @var $mediaInfo KalturaMediaInfo */
			$mediaInfoWidth = $mediaInfo->videoWidth;
			$mediaInfoHeight = $mediaInfo->videoHeight;
			$mediaInfoDar = $mediaInfo->videoDar;
			$mediaInfoScanType = $mediaInfo->scanType;
			$mediaInfoVideoRotation = $mediaInfo->videoRotation;

			if($mediaInfo->videoDuration)
				$mediaInfoVidDur = $mediaInfo->videoDuration/1000;
			else if ($mediaInfo->containerDuration)
				$mediaInfoVidDur = $mediaInfo->containerDuration/1000;
			else if($mediaInfo->audioDuration)
				$mediaInfoVidDur = $mediaInfo->audioDuration/1000;
		}
		return array($mediaInfoWidth, $mediaInfoHeight, $mediaInfoDar, $mediaInfoVidDur, $mediaInfoScanType, $mediaInfoVideoRotation);
	}
	
	private function createUniqFileName($rootPath)
	{
		return realpath($rootPath) . DIRECTORY_SEPARATOR . uniqid('thumb_');
	}

	protected function createBasicThumb($job, $data, $rootPath, $mediaFile, $thumbParamsOutput)
	{
		$capturePath = null;
		if($data->srcAssetType == KalturaAssetType::FLAVOR)
		{
			$capturePath = $this->createUniqFileName($rootPath);
			list($mediaInfoWidth, $mediaInfoHeight, $mediaInfoDar, $mediaInfoVidDur, $mediaInfoScanType, $mediaInfoVideoRotation) = $this->getMediaInfoData($job->partnerId, $data->srcAssetId);

			// generates the thumbnail
			$thumbMaker = new KFFMpegThumbnailMaker($mediaFile, $capturePath, self::$taskConfig->params->FFMpegCmd);
			$videoOffset = max(0 ,min($thumbParamsOutput->videoOffset, $mediaInfoVidDur-1));
			$params['dar'] = $mediaInfoDar;
			$params['vidDur'] = $mediaInfoVidDur;
			$params['scanType'] = $mediaInfoScanType;
			if ( $data->srcAssetEncryptionKey )
			{
				$params['encryption_key'] = $data->srcAssetEncryptionKey;
			}

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

	protected function createBifFile($job, $data, $rootPath, $mediaFile, $thumbParamsOutput)
	{
		if($data->srcAssetType != KalturaAssetType::FLAVOR)
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::MISSING_ASSETS, 'Asset type not supported', KalturaBatchJobStatus::FAILED, $data);
		}

		$bifInterval = $this->getBifInterval($thumbParamsOutput);
		$rootPath = $this->createRootFolder($rootPath, $job);
		$generalCapturePath = $this->createUniqFileName($rootPath);

		$images = $this->createBifFrames($job, $data, $bifInterval, $thumbParamsOutput, $generalCapturePath, $mediaFile);

		$finalBifPath = $generalCapturePath . '.bif';
		KalturaLog::debug("Create bif file in path - [$finalBifPath]");
		$bifCreator = new kBifCreator($images, $finalBifPath, $bifInterval);
		$bifCreator->createBif();

		$data->thumbPath = $finalBifPath;
		$job = $this->moveFile($job, $data);

		$files = scandir($rootPath);
		$files = array_diff($files, array('.', '..'));
		if($this->checkFileExists($job->data->thumbPath))
		{
			$updateData = new KalturaCaptureThumbJobData();
			$updateData->thumbPath = $data->thumbPath;
			$this->removeFiles($rootPath, $files);
			return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::FINISHED, $updateData);
		}

		$this->removeFiles($rootPath, $files);
		return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, 'File not moved correctly', KalturaBatchJobStatus::FAILED, $data);
	}

	protected function generateSingleThumbUsingFfmpeg($mediaFile, $capturePath, $mediaInfoVidDur, $mediaInfoDar,
											  $mediaInfoScanType, $data, $mediaInfoWidth, $mediaInfoHeight, $captureVidSec)
	{
		KalturaLog::debug("capture new frame - [$capturePath]");
		$thumbMaker = new KFFMpegThumbnailMaker($mediaFile, $capturePath, self::$taskConfig->params->FFMpegCmd);
		$params['dar'] = $mediaInfoDar;
		$params['vidDur'] = $mediaInfoVidDur;
		$params['scanType'] = $mediaInfoScanType;
		if ( $data->srcAssetEncryptionKey )
		{
			$params['encryption_key'] = $data->srcAssetEncryptionKey;
		}

		return $thumbMaker->createThumnail($captureVidSec, $mediaInfoWidth, $mediaInfoHeight, $params);
	}

	protected function getBifInterval($thumbParamsOutput)
	{
		if(!$thumbParamsOutput->interval)
		{
			return self::DEFAULT_BIF_INTERVAL;
		}
		return $thumbParamsOutput->interval;
	}

	protected function cropFrame ($capturePath, $thumbParamsOutput, $job)
	{
		$cropped = $this->crop($capturePath ,$capturePath, $thumbParamsOutput);

		if(!$cropped || !file_exists($capturePath))
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::THUMBNAIL_NOT_CREATED, "One of BIF frames was not cropped", KalturaBatchJobStatus::FAILED);
	}


	protected function isBif($thumbParamsOutput)
	{
		$tagsArray = explode(',', $thumbParamsOutput->tags);
		$lowerTagsArray = array_map('strtolower', $tagsArray);
		if(in_array(self::BIF_TAG, $lowerTagsArray))
		{
			return true;
		}
		return false;
	}

	protected function captureThumbUsingFfmpeg($mediaFile, $capturePath, $thumbParamsOutput, $mediaInfoVidDur, $mediaInfoDar,
											   $mediaInfoScanType, $data, $mediaInfoWidth, $mediaInfoHeight, $captureVidSec, $job)
	{
		$created = $this->generateSingleThumbUsingFfmpeg($mediaFile, $capturePath, $mediaInfoVidDur, $mediaInfoDar,
			$mediaInfoScanType, $data, $mediaInfoWidth, $mediaInfoHeight, $captureVidSec);

		if(!$created || !file_exists($capturePath))
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::THUMBNAIL_NOT_CREATED, "One of BIF frames was not created", KalturaBatchJobStatus::FAILED);

		$this->cropFrame($capturePath, $thumbParamsOutput, $job);
	}

	protected function createRootFolder($rootPath, $job)
	{
		$folderPath = realpath($rootPath) . DIRECTORY_SEPARATOR . uniqid();
		$rootPath = self::createDir($folderPath);
		if (!$rootPath)
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CANNOT_CREATE_DIRECTORY, 'Can not create directory', KalturaBatchJobStatus::FAILED, $data);
		}
		return $rootPath;
	}

	protected function createBifFrames($job, $data, $bifInterval, $thumbParamsOutput, $generalCapturePath, $mediaFile)
	{
		$images = array();
		$packagerRetries = 3;
		list($mediaInfoWidth, $mediaInfoHeight, $mediaInfoDar, $mediaInfoVidDur, $mediaInfoScanType, $mediaInfoVideoRotation) = $this->getMediaInfoData($job->partnerId, $data->srcAssetId);

		$captureVidSec = $bifInterval;
		$count = floor($mediaInfoVidDur / $bifInterval);
		KalturaLog::debug("Number of images to capture - [$count]");

		$params = array($thumbParamsOutput->density, $thumbParamsOutput->quality, $mediaInfoVideoRotation, $thumbParamsOutput->cropX, $thumbParamsOutput->cropY,
			$thumbParamsOutput->cropWidth, $thumbParamsOutput->cropHeight, $thumbParamsOutput->stripProfiles);
		// 5 - always force setting the given dimensions
		$shouldResizeByPackager = KThumbnailCapture::shouldResizeByPackager($params, 5, array($thumbParamsOutput->width, $thumbParamsOutput->height));
		list($picWidth, $picHeight) = $shouldResizeByPackager ? array($thumbParamsOutput->width, $thumbParamsOutput->height) : array(null, null);
		$this->updateJob($job, "Starting to capture [" . $count . "] thumbnails", KalturaBatchJobStatus::PROCESSING);
		while($count--)
		{
			if($captureVidSec >= $mediaInfoVidDur)
			{
				continue;
			}
			$capturePath = $generalCapturePath . '_sec_' . $captureVidSec . '_';
			if($packagerRetries)
			{
				$thumbCaptureByPackager = false;
				$success = self::captureLocalThumbForBifUsingPackager($mediaFile, $capturePath, $captureVidSec, $picWidth, $picHeight);
				$packagerResizeFullPath = $capturePath . self::TEMP_FILE_POSTFIX;
				KalturaLog::debug("Packager capture is [$success] with dimension [$picWidth,$picHeight] and packagerResize [$shouldResizeByPackager] in path [$packagerResizeFullPath]");
				if(!$success)
				{
					$packagerRetries--;
					$thumbCaptureByPackager = $success;
					$this->captureThumbUsingFfmpeg($mediaFile, $packagerResizeFullPath, $thumbParamsOutput, $mediaInfoVidDur, $mediaInfoDar, $mediaInfoScanType, $data, $mediaInfoWidth, $mediaInfoHeight, $captureVidSec, $job);
					if($packagerRetries == 0)
					{
						KalturaLog::warning("Packager retries reached max value. Capturing thumb only using ffmpeg");
					}
				}
				$this->shouldCropImage($thumbCaptureByPackager, $shouldResizeByPackager, $packagerResizeFullPath, $thumbParamsOutput, $job);
				$capturePath = $packagerResizeFullPath;
			}
			else
			{
				$this->captureThumbUsingFfmpeg($mediaFile, $capturePath, $thumbParamsOutput, $mediaInfoVidDur, $mediaInfoDar, $mediaInfoScanType, $data, $mediaInfoWidth, $mediaInfoHeight, $captureVidSec, $job);
			}
			$captureVidSec += $bifInterval;
			$images[] = $capturePath;
		}
		$this->updateJob($job, "Captured all frames for BIF", KalturaBatchJobStatus::ALMOST_DONE);
		return $images;
	}

	protected function shouldCropImage($thumbCaptureByPackager, $shouldResizeByPackager, $packagerResizeFullPath, $thumbParamsOutput, $job)
	{
		if ($thumbCaptureByPackager && $shouldResizeByPackager)
		{
			KalturaLog::debug("Image was resize in the packager -  setting path [$packagerResizeFullPath]");
		}
		else //need to crop the image
		{
			$this->cropFrame($packagerResizeFullPath, $thumbParamsOutput, $job);
		}
	}

	public static function captureLocalThumbForBifUsingPackager($srcPath, $capturedThumbPath, $calc_vid_sec, $width = null, $height = null)
	{
		$packagerCaptureUrl = self::getConfigParam('packager_local_thumb_capture_url', 'local', null);
		if (!$packagerCaptureUrl || !$srcPath)
		{
			return false;
		}
		$srcPath = strstr($srcPath, 'content');

		list($packagerThumbCapture, $tempThumbPath) = KThumbnailCapture::generateThumbUrlWithOffset($srcPath, $calc_vid_sec, $packagerCaptureUrl, $capturedThumbPath, $width, $height);
		return  KCurlWrapper::getDataFromFile($packagerThumbCapture, $tempThumbPath, null, true);
	}

	protected function removeFiles($dirPath , $filesList = null)
	{
		if(!$filesList)
		{
			array_map('unlink', array_filter((array) glob($dirPath)));
		}
		else
		{
			foreach ($filesList as $file)
			{
				$fullFilePath = $dirPath . '/' . $file;
				if(file_exists($fullFilePath))
				{
					unlink($fullFilePath);
				}
			}
		}
		rmdir($dirPath);
	}

}
