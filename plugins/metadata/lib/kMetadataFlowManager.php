<?php
class kMetadataFlowManager implements kBatchJobStatusEventConsumer, kObjectDataChangedEventConsumer
{
	/**
	 * @param BatchJob $dbBatchJob
	 * @param BatchJob $twinJob
	 * @return bool true if should continue to the next consumer
	 */
	public function updatedJob(BatchJob $dbBatchJob, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getJobType())
		{
			case BatchJobType::METADATA_IMPORT:
				$dbBatchJob = $this->updatedImportMetadata($dbBatchJob, $dbBatchJob->getData(), $twinJob);
				break;
		
			case BatchJobType::METADATA_TRANSFORM:
				$dbBatchJob = $this->updatedTransformMetadata($dbBatchJob, $dbBatchJob->getData(), $twinJob);
				break;
	
			default:
				break;
		}
		
		return true;
	}
	
		
	protected function updatedImportMetadata(BatchJob $dbBatchJob, kImportMetadataJobData $data, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedImportMetadataFinished($dbBatchJob, $data, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedImportMetadataFailed($dbBatchJob, $data, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedImportMetadataFinished(BatchJob $dbBatchJob, kImportMetadataJobData $data, BatchJob $twinJob = null)
	{
		// TODO - update the metadata file sync
		return $dbBatchJob;
	}
	
	protected function updatedImportMetadataFailed(BatchJob $dbBatchJob, kImportMetadataJobData $data, BatchJob $twinJob = null)
	{
		// TODO - set the metadata status to invalid
		return $dbBatchJob;
	}
	
	protected function updatedTransformMetadata(BatchJob $dbBatchJob, kTransformMetadataJobData $data, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return $this->updatedTransformMetadataPending($dbBatchJob, $data, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedTransformMetadataFinished($dbBatchJob, $data, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedTransformMetadataFailed($dbBatchJob, $data, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedTransformMetadataPending(BatchJob $dbBatchJob, kTransformMetadataJobData $data, BatchJob $twinJob = null)
	{
		$metadataProfile = MetadataProfilePeer::retrieveById($data->getMetadataProfileId());
		$metadataProfile->setStatus(MetadataProfile::STATUS_TRANSFORMING);
		$metadataProfile->save();
		
		return $dbBatchJob;
	}
	
	protected function updatedTransformMetadataFinished(BatchJob $dbBatchJob, kTransformMetadataJobData $data, BatchJob $twinJob = null)
	{
		$metadataProfile = MetadataProfilePeer::retrieveById($data->getMetadataProfileId());
		$metadataProfile->setStatus(MetadataProfile::STATUS_ACTIVE);
		$metadataProfile->save();
		
		return $dbBatchJob;
	}
	
	protected function updatedTransformMetadataFailed(BatchJob $dbBatchJob, kTransformMetadataJobData $data, BatchJob $twinJob = null)
	{
		if(!$data->getMetadataProfileId())
			return $dbBatchJob;
			
		$metadataProfile = MetadataProfilePeer::retrieveById($data->getMetadataProfileId());
		if(!$metadataProfile)
			return $dbBatchJob;
	
		$metadataProfile->setStatus(MetadataProfile::STATUS_DEPRECATED);
		$metadataProfile->save();
		
		return $dbBatchJob;
	}
	
	/**
	 * @param BaseObject $object
	 * @param string $previousVersion
	 * @return bool true if should continue to the next consumer
	 */
	public function objectDataChanged(BaseObject $object, $previousVersion = null)
	{
		if(!class_exists('Metadata') || !($object instanceof Metadata))
			return true;

		// updated in the indexing server (sphinx)
		$relatedObject = kMetadataManager::getObjectFromPeer($object);
		if($relatedObject && $relatedObject instanceof IIndexable)
		{
			$relatedObject->setUpdatedAt(time());
			$relatedObject->save();
		}
		
		return true;
	}
}