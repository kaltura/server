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
	 * extractMediaInfo extract the file info using mediainfo and parse the returned data
	 *  
	 * @param string $mediaFile file full path
	 * @return KalturaMediaInfo or null for failure
	 */
	private function extractMediaInfo($mediaFile)
	{
		$engine = KBaseMediaParser::getParser($job->jobSubType, realpath($mediaFile), self::$taskConfig);
		if($engine)
			return $engine->getMediaInfo();		
		
		return null;
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

		if(is_null($mediaInfo))
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::EXTRACT_MEDIA_FAILED, "Failed to extract media info: $mediaFile", KalturaBatchJobStatus::RETRY);
		}
		/*
		 * Calculate media file 'complexity'
		 */
		if(isset(self::$taskConfig->params->localTempPath) && file_exists(self::$taskConfig->params->localTempPath)){
			$ffmpegBin = isset(self::$taskConfig->params->ffmpegCmd)? self::$taskConfig->params->ffmpegCmd: null;
			$ffprobeBin = isset(self::$taskConfig->params->ffprobeCmd)? self::$taskConfig->params->ffprobeCmd: null;
			$mediaInfoBin = isset(self::$taskConfig->params->mediaInfoCmd)? self::$taskConfig->params->mediaInfoCmd: null;
			$calcComplexity = new KMediaFileComplexity($ffmpegBin, $ffprobeBin, $mediaInfoBin);
			
			$baseOutputName = tempnam(self::$taskConfig->params->localTempPath, "/complexitySampled_".pathinfo($mediaFile, PATHINFO_FILENAME)).".mp4";
			$stat = $calcComplexity->EvaluateSampled($mediaFile, $mediaInfo, $baseOutputName);
			if(isset($stat->complexityValue)){
				KalturaLog::log("Complexity: value($stat->complexityValue)");
				if(isset($stat->y))
					KalturaLog::log("Complexity: y($stat->y)");
				$mediaInfo->complexityValue = $stat->complexityValue;
			}
		}

		KalturaLog::debug("flavorAssetId [$data->flavorAssetId]");
		$mediaInfo->flavorAssetId = $data->flavorAssetId;
		$mediaInfo = $this->getClient()->batch->addMediaInfo($mediaInfo);
		$data->mediaInfoId = $mediaInfo->id;

		$this->updateJob($job, "Saving media info id $mediaInfo->id", KalturaBatchJobStatus::PROCESSED, $data);
		$this->closeJob($job, null, null, null, KalturaBatchJobStatus::FINISHED);

		return $job;
	}
}

