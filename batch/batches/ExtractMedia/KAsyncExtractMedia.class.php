<?php
/**
 * @package Scheduler
 * @subpackage Extract-Media
 */

/**
 * Will extract the media info of a single file 
 *
 * @package Scheduler
 * @subpackage Extract-Media
 */
class KAsyncExtractMedia extends KJobHandlerWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::EXTRACT_MEDIA;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->extract($job, $job->data);
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::getMaxJobsEachRun()
	 */
	protected function getMaxJobsEachRun()
	{
		return 1;
	}
	
	/**
	 * Will take a single KalturaBatchJob and extract the media info for the given file
	 */
	private function extract(KalturaBatchJob $job, KalturaExtractMediaJobData $data)
	{
		$srcFileSyncDescriptor = reset($data->srcFileSyncs);
		$mediaFile = null;
		if($srcFileSyncDescriptor)
			$mediaFile = trim($srcFileSyncDescriptor->fileSyncLocalPath);
		
		if(!$this->pollingFileExists($mediaFile))
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "Source file $mediaFile does not exist", KalturaBatchJobStatus::RETRY);
		
		if(!is_file($mediaFile))
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "Source file $mediaFile is not a file", KalturaBatchJobStatus::FAILED);
		
		$this->updateJob($job, "Extracting file media info on $mediaFile", KalturaBatchJobStatus::QUEUED);
		
		$mediaInfo = $this->extractMediaInfo($job, $mediaFile);
		
		if(is_null($mediaInfo))
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::EXTRACT_MEDIA_FAILED, "Failed to extract media info: $mediaFile", KalturaBatchJobStatus::RETRY);
		}
		
		if($data->calculateComplexity)
			$this->calculateMediaFileComplexity($mediaInfo, $mediaFile);
		
		if($data->detectGOP>0) {
			$this->detectMediaFileGOP($mediaInfo, $mediaFile, $data->detectGOP);
		}

		$duration = $mediaInfo->containerDuration;
		if(!$duration)
			$duration = $mediaInfo->videoDuration;
		if(!$duration)
			$duration = $mediaInfo->audioDuration;
		
		if($data->extractId3Tags)
			$this->extractId3Tags($mediaFile, $data, $duration);
		
		KalturaLog::debug("flavorAssetId [$data->flavorAssetId]");
		$mediaInfo->flavorAssetId = $data->flavorAssetId;
		$mediaInfo = $this->getClient()->batch->addMediaInfo($mediaInfo);
		$data->mediaInfoId = $mediaInfo->id;
		
		$this->updateJob($job, "Saving media info id $mediaInfo->id", KalturaBatchJobStatus::PROCESSED, $data);
		$this->closeJob($job, null, null, null, KalturaBatchJobStatus::FINISHED);
		
		return $job;
	}
	
	/**
	 * extractMediaInfo extract the file info using mediainfo and parse the returned data
	 *  
	 * @param string $mediaFile file full path
	 * @return KalturaMediaInfo or null for failure
	 */
	private function extractMediaInfo($job, $mediaFile)
	{
		$mediaInfo = null;
		try
		{
			$mediaFile = realpath($mediaFile);
			
			$engine = KBaseMediaParser::getParser($job->jobSubType, $mediaFile, self::$taskConfig, $job);
			if($engine)
			{
				$mediaInfo = $engine->getMediaInfo();
			}
			else
			{
				$err = "No media info parser engine found for job sub type [$job->jobSubType]";
				return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::ENGINE_NOT_FOUND, $err, KalturaBatchJobStatus::FAILED);
			}
		}
		catch(Exception $ex)
		{
			KalturaLog::err($ex->getMessage());
			$mediaInfo = null;
		}
		
		return $mediaInfo;
	}
	
	/*
	 * Calculate media file 'complexity'
	 */
	private function calculateMediaFileComplexity(&$mediaInfo, $mediaFile)
	{
		$complexityValue = null;
		
		if(isset(self::$taskConfig->params->localTempPath) && file_exists(self::$taskConfig->params->localTempPath))
		{
			$ffmpegBin = isset(self::$taskConfig->params->ffmpegCmd)? self::$taskConfig->params->ffmpegCmd: null;
			$ffprobeBin = isset(self::$taskConfig->params->ffprobeCmd)? self::$taskConfig->params->ffprobeCmd: null;
			$mediaInfoBin = isset(self::$taskConfig->params->mediaInfoCmd)? self::$taskConfig->params->mediaInfoCmd: null;
			$calcComplexity = new KMediaFileComplexity($ffmpegBin, $ffprobeBin, $mediaInfoBin);
			
			$baseOutputName = tempnam(self::$taskConfig->params->localTempPath, "/complexitySampled_".pathinfo($mediaFile, PATHINFO_FILENAME)).".mp4";
			$stat = $calcComplexity->EvaluateSampled($mediaFile, $mediaInfo, $baseOutputName);
			if(isset($stat->complexityValue))
			{
				KalturaLog::log("Complexity: value($stat->complexityValue)");
				if(isset($stat->y))
					KalturaLog::log("Complexity: y($stat->y)");
				
				$complexityValue = $stat->complexityValue;
			}
		}
		
		if($complexityValue)
			$mediaInfo->complexityValue = $complexityValue;
	}
	
	private function extractId3Tags($filePath, KalturaExtractMediaJobData $data, $duration)
	{
		try
		{
			$kalturaId3TagParser = new KSyncPointsMediaInfoParser($filePath);
			$syncPointArray = $kalturaId3TagParser->getStreamSyncPointData();
			
			$outputFileName = pathinfo($filePath, PATHINFO_FILENAME) . ".data";
			$localTempSyncPointsFilePath = self::$taskConfig->params->localTempPath . DIRECTORY_SEPARATOR . $outputFileName;
			$sharedTempSyncPointFilePath = self::$taskConfig->params->sharedTempPath . DIRECTORY_SEPARATOR . $outputFileName;

			$retries = 3;
			while ($retries-- > 0)
			{
				$res = kFile::setFileContent($localTempSyncPointsFilePath, serialize($syncPointArray));
				$res = $res && $this->moveDataFile($data, $localTempSyncPointsFilePath, $sharedTempSyncPointFilePath);
				if ($res)
					return;
			}
			throw new kTemporaryException("Failed on writing syncPoint array to disk in path {$localTempSyncPointsFilePath}");
		}
		catch(kTemporaryException $ktex)
		{
			$this->unimpersonate();
			throw $ktex;
		}
		catch(Exception $ex) 
		{
			$this->unimpersonate();
			KalturaLog::warning("Failed to extract id3tags data or duration data with error: " . print_r($ex));
		}
		
	}
	
	private function moveDataFile(KalturaExtractMediaJobData $data, $localTempSyncPointsFilePath, $sharedTempSyncPointFilePath)
	{
		KalturaLog::debug("moving file from [$localTempSyncPointsFilePath] to [$sharedTempSyncPointFilePath]");
		$fileSize = kFile::fileSize($localTempSyncPointsFilePath);
		
		$res = kFile::moveFile($localTempSyncPointsFilePath, $sharedTempSyncPointFilePath, true);
		if (!$res)
			return false;
		clearstatcache();
		
		$this->setFilePermissions($sharedTempSyncPointFilePath);
		if(!$this->checkFileExists($sharedTempSyncPointFilePath, $fileSize))
		{
			KalturaLog::warning("Failed to move file to [$sharedTempSyncPointFilePath]");
			return false;
		}
		else
			$data->destDataFilePath = $sharedTempSyncPointFilePath;
		return true;
	}

	/*
	 *
	 */
	 private function detectMediaFileGOP($mediaInfo, $mediaFile, $interval)
	 {
		KalturaLog::log("Detection interval($interval)");
		list($minGOP,$maxGOP,$detectedGOP) = KFFMpegMediaParser::detectGOP((isset(self::$taskConfig->params->ffprobeCmd)? self::$taskConfig->params->ffprobeCmd: null), $mediaFile, 0, $interval);
		KalturaLog::log("Detected - minGOP($minGOP),maxGOP($maxGOP),detectedGOP($detectedGOP)");
		if(isset($maxGOP)){
			$mediaInfo->maxGOP = $maxGOP;
		}
	 }
}

