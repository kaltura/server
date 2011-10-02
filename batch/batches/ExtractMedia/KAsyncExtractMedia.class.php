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
	 * @see KBatchBase::getJobType()
	 */
	public function getJobType()
	{
		return self::getType();
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
		KalturaLog::debug("file path [$mediaFile]");
		
		$engine = KBaseMediaParser::getParser($job->jobSubType, realpath($mediaFile), $this->taskConfig);
		if($engine)
			return $engine->getMediaInfo();		
		
		return null;
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
	
	/**
	 * Will take a single KalturaBatchJob and extract the media info for the given file 
	 */
	private function extract(KalturaBatchJob $job, KalturaExtractMediaJobData $data)
	{
		KalturaLog::debug("extract($job->id)");
		
		$mediaFile = trim($data->srcFileSyncLocalPath);
		
// 		if(!file_exists($mediaFile))
// 			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "Source file $mediaFile does not exist", KalturaBatchJobStatus::RETRY);
		
// 		if(!is_file($mediaFile))
// 			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "Source file $mediaFile is not a file", KalturaBatchJobStatus::FAILED);
			
		KalturaLog::debug("mediaFile [$mediaFile]");
		$this->updateJob($job, "Extracting file media info on $mediaFile", KalturaBatchJobStatus::QUEUED, 1);
			
		$mediaInfo = null;
		try
		{
			$mediaFile = realpath($mediaFile);
			KalturaLog::debug("file path [$mediaFile]");
			
			$engine = KBaseMediaParser::getParser($job->jobSubType, realpath($mediaFile), $this->taskConfig);
			if($engine)
			{
				KalturaLog::debug("Found engine [" . get_class($engine) . "]");
				$mediaInfo = $engine->getMediaInfo();
			}
			else
			{
				$err = "No media info parser engine found for job sub type [$job->jobSubType]";
				KalturaLog::err($err);
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
		
		KalturaLog::debug("flavorAssetId [$data->flavorAssetId]");
		$mediaInfo->flavorAssetId = $data->flavorAssetId;
		$mediaInfo = $this->getClient()->batch->addMediaInfo($mediaInfo);
		$data->mediaInfoId = $mediaInfo->id;
		
		$this->updateJob($job, "Saving media info id $mediaInfo->id", KalturaBatchJobStatus::PROCESSED, 99, $data);
		$this->closeJob($job, null, null, null, KalturaBatchJobStatus::FINISHED);
		return $job;
	}
}
