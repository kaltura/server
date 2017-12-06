<?php
/**
 * Will transform metadata XML based on XSL and will update the metadata object with the new version 
 *
 * @package plugins.metadata
 * @subpackage Scheduler.Transform
 */
class KAsyncTransformMetadata extends KJobHandlerWorker
{
	/**
	 * @var int
	 */
	protected $multiRequestSize = 20;
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::METADATA_TRANSFORM;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->upgrade($job, $job->data);
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::getJobs()
	 * 
	 * TODO remove the destXsdPath from the job data and get it later using the api, then delete this method
	 */
	protected function getJobs()
	{
		$maxJobToPull = KBatchBase::$taskConfig->maxJobToPullToCache;
		return self::$kClient->metadataBatch->getExclusiveTransformMetadataJobs($this->getExclusiveLockKey(), self::$taskConfig->maximumExecutionTime, 1, 
				$this->getFilter(), $maxJobToPull);
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::getMaxJobsEachRun()
	 */
	protected function getMaxJobsEachRun()
	{
		return 1;
	}
	
	private function invalidateFailedMetadatas($results, $transformObjectIds = array())
	{
		self::$kClient->startMultiRequest();
		foreach($results as $index => $result){
        	if(is_array($result) && isset($result['code']) && isset($result['message'])){
              	KalturaLog::err('error in object id['.$transformObjectIds[$index] .'] with code: '. $result['code']."\n".$result['message']." going to invalidate it");
              	self::$kClient->metadata->invalidate($transformObjectIds[$index]);
        	}
        }
        $resultsOfInvalidating = self::$kClient->doMultiRequest();	
		foreach($resultsOfInvalidating as $index => $resultOfInvalidating){
        	if(is_array($resultOfInvalidating) && isset($resultOfInvalidating['code']) && isset($resultOfInvalidating['message'])){
              	KalturaLog::err('error while invalidating object id['.$transformObjectIds[$index] .'] with code: '. $resultOfInvalidating['code']."\n".$resultOfInvalidating['message']);        	
        	}
        }	
	}
	
	private function upgrade(KalturaBatchJob $job, KalturaTransformMetadataJobData $data)
	{
		if(self::$taskConfig->params->multiRequestSize)
			$this->multiRequestSize = self::$taskConfig->params->multiRequestSize;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 40;
		if(self::$taskConfig->params && self::$taskConfig->params->maxObjectsEachRun)
			$pager->pageSize = self::$taskConfig->params->maxObjectsEachRun;
		
		$transformList = self::$kClient->metadataBatch->getTransformMetadataObjects(
			$data->metadataProfileId,
			$data->srcVersion,
			$data->destVersion,
			$pager
		);
			
		if(!$transformList->totalCount) // if no metadata objects returned
		{
			if(!$transformList->lowerVersionCount) // if no metadata objects of lower version exist
			{
				$this->closeJob($job, null, null, 'All metadata transformed', KalturaBatchJobStatus::FINISHED);
				return $job;
			}
			
			$this->closeJob($job, null, null, "Waiting for metadata objects [$transformList->lowerVersionCount] of lower versions", KalturaBatchJobStatus::RETRY);
			return $job;
		}
		
		if($transformList->lowerVersionCount || $transformList->totalCount) // another retry will be needed later
		{
			self::$kClient->batch->resetJobExecutionAttempts($job->id, $this->getExclusiveLockKey(), $job->jobType);
		}
			
		self::$kClient->startMultiRequest();
		$transformObjectIds = array();
		foreach($transformList->objects as $object)
		{
			/* @var $object KalturaMetadata */
			$xslStr = kEncryptFileUtils::getEncryptedFileContent($data->srcXsl->filePath, $data->srcXsl->encryptionKey, self::getIV());
			$xml = kXsd::transformXmlData($object->xml, $data->destXsdPath, $xslStr);
			if($xml)
			{
				self::$kClient->metadata->update($object->id, $xml, $object->version);
			}
			else 
			{			
				self::$kClient->metadata->invalidate($object->id, $object->version);
			}
			
			$transformObjectIds[] = $object->id;
				    
			if(self::$kClient->getMultiRequestQueueSize() >= $this->multiRequestSize)
			{
				$results = self::$kClient->doMultiRequest();
				$this->invalidateFailedMetadatas($results, $transformObjectIds);
				$transformObjectIds = array();
				self::$kClient->startMultiRequest();
			}
			
		}
		$results = self::$kClient->doMultiRequest();
		$this->invalidateFailedMetadatas($results, $transformObjectIds);
		
		$this->closeJob($job, null, null, "Metadata objects [" . count($transformList->objects) . "] transformed", KalturaBatchJobStatus::RETRY);
		
		return $job;
	}
}
