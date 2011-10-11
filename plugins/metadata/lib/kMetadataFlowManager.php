<?php
/**
 * @package plugins.metadata
 * @subpackage lib
 */
class kMetadataFlowManager implements kBatchJobStatusEventConsumer, kObjectDataChangedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		if($dbBatchJob->getJobType() == BatchJobType::METADATA_TRANSFORM)
			return true;
				
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob, BatchJob $twinJob = null)
	{
		$dbBatchJob = $this->updatedTransformMetadata($dbBatchJob, $dbBatchJob->getData(), $twinJob);
				
		return true;
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
		if($data->getSrcXslPath())
		{
			$metadataProfile = MetadataProfilePeer::retrieveById($data->getMetadataProfileId());
			$metadataProfile->setStatus(MetadataProfile::STATUS_TRANSFORMING);
			$metadataProfile->save();
		}
		
		return $dbBatchJob;
	}
	
	protected function updatedTransformMetadataFinished(BatchJob $dbBatchJob, kTransformMetadataJobData $data, BatchJob $twinJob = null)
	{
		if($data->getSrcXslPath())
		{
			$metadataProfile = MetadataProfilePeer::retrieveById($data->getMetadataProfileId());
			$metadataProfile->setStatus(MetadataProfile::STATUS_ACTIVE);
			$metadataProfile->save();
		}
		
		return $dbBatchJob;
	}
	
	protected function updatedTransformMetadataFailed(BatchJob $dbBatchJob, kTransformMetadataJobData $data, BatchJob $twinJob = null)
	{
		if(!$data->getMetadataProfileId())
			return $dbBatchJob;
			
		$metadataProfile = MetadataProfilePeer::retrieveById($data->getMetadataProfileId());
		if(!$metadataProfile)
			return $dbBatchJob;
	
		if($data->getSrcXslPath())
		{
			$metadataProfile->setStatus(MetadataProfile::STATUS_DEPRECATED);
			$metadataProfile->save();
		}
		
		return $dbBatchJob;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectDataChangedEventConsumer::shouldConsumeDataChangedEvent()
	 */
	public function shouldConsumeDataChangedEvent(BaseObject $object, $previousVersion = null)
	{
		if(class_exists('Metadata') && $object instanceof Metadata)
			return true;
			
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectDataChangedEventConsumer::objectDataChanged()
	 */
	public function objectDataChanged(BaseObject $object, $previousVersion = null, BatchJob $raisedJob = null)
	{
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