<?php
require_once("bootstrap.php");
/**
 * Will extract the media info of a single file 
 *
 * @package Scheduler
 * @subpackage Extract-Media
 */
class KAsyncExtractMedia extends KBatchBase
{
	/**
	 * @return number
	 */
	public static function getType()
	{
		return KalturaBatchJobType::EXTRACT_MEDIA;
	}
	
	protected function init()
	{
		$this->saveQueueFilter(self::getType());
	}
	
	public function run($jobs = null)
	{
		KalturaLog::info("Extract media batch is running");
		
		if($this->taskConfig->isInitOnly())
			return $this->init();
		
		if(is_null($jobs))
			$jobs = $this->kClient->batch->getExclusiveExtractMediaJobs($this->getExclusiveLockKey(), $this->taskConfig->maximumExecutionTime, 1, $this->getFilter());
		
		KalturaLog::info(count($jobs) . " extract media jobs to perform");
		
		if(! count($jobs))
		{
			KalturaLog::info("Queue size: 0 sent to scheduler");
			$this->saveSchedulerQueue(self::getType());
			return null;
		}
		
		foreach($jobs as &$job)
		{
			try
			{
				$job = $this->extract($job, $job->data);
			}
			catch(KalturaException $kex)
			{
				return $this->closeJob($job, KalturaBatchJobErrorTypes::KALTURA_API, $kex->getCode(), "Error: " . $kex->getMessage(), KalturaBatchJobStatus::FAILED);
			}
			catch(KalturaClientException $kcex)
			{
				return $this->closeJob($job, KalturaBatchJobErrorTypes::KALTURA_CLIENT, $kcex->getCode(), "Error: " . $kcex->getMessage(), KalturaBatchJobStatus::RETRY);
			}
			catch(Exception $ex)
			{
				return $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED);
			}
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
		$mediaInfo = null;
		try {
			$mediaParser = new KMediaInfoMediaParser($mediaFile, $this->taskConfig->params->mediaInfoCmd);
			$mediaInfo = $mediaParser->getMediaInfo();
		}
		catch (Exception $ex){
			KalturaLog::err($ex->getMessage());
			$mediaInfo = null;
		}
		
		try {
			if(is_null($mediaInfo) && $this->taskConfig->params->mediaInfoCmd2){
				$mediaParser = new KMediaInfoMediaParser($mediaFile, $this->taskConfig->params->mediaInfoCmd2);
				$mediaInfo = $mediaParser->getMediaInfo();
			}
		}
		catch (Exception $ex){
			KalturaLog::err($ex->getMessage());
			$mediaInfo = null;
		}
		
		
		return $mediaInfo;
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
		
		if(!file_exists($mediaFile))
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "Source file $mediaFile does not exist", KalturaBatchJobStatus::RETRY);
		
		if(!is_file($mediaFile))
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "Source file $mediaFile is not a file", KalturaBatchJobStatus::FAILED);
			
		KalturaLog::debug("mediaFile [$mediaFile]");
		$this->updateJob($job, "Extracting file media info on $mediaFile", KalturaBatchJobStatus::QUEUED, 1);
			
		$mediaInfo = null;
		
				// First mediaInfo attempt - 
		try
		{
//			if($this->taskConfig->params->useMediaInfo)
				$mediaInfo = $this->extractMediaInfo(realpath($mediaFile));
		}
		catch(Exception $ex)
		{
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
	
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job)
	{
		return $this->kClient->batch->updateExclusiveExtractMediaJob($jobId, $this->getExclusiveLockKey(), $job);
	}
	
	protected function freeExclusiveJob(KalturaBatchJob $job)
	{
		$resetExecutionAttempts = false;
		if($job->status == KalturaBatchJobStatus::ALMOST_DONE)
			$resetExecutionAttempts = true;
	
		$response = $this->kClient->batch->freeExclusiveExtractMediaJob($job->id, $this->getExclusiveLockKey(), $resetExecutionAttempts);
		
		KalturaLog::info("Queue size: $response->queueSize sent to scheduler");
		$this->saveSchedulerQueue(self::getType(), $response->queueSize);
		
		return $response->job;
	}
}
?>