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
	public function updatedJob(BatchJob $dbBatchJob)
	{
		$dbBatchJob = $this->updatedTransformMetadata($dbBatchJob, $dbBatchJob->getData());
				
		return true;
	}
	
	protected function updatedTransformMetadata(BatchJob $dbBatchJob, kTransformMetadataJobData $data)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return $this->updatedTransformMetadataPending($dbBatchJob, $data);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedTransformMetadataFinished($dbBatchJob, $data);
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedTransformMetadataFailed($dbBatchJob, $data);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedTransformMetadataPending(BatchJob $dbBatchJob, kTransformMetadataJobData $data)
	{
		if($data->getSrcXsl())
		{
			$metadataProfile = MetadataProfilePeer::retrieveByPK($data->getMetadataProfileId());
			if($metadataProfile)
			{
				$metadataProfile->setStatus(MetadataProfile::STATUS_TRANSFORMING);
				$metadataProfile->save();
			}
		}
		
		return $dbBatchJob;
	}
	
	protected function updatedTransformMetadataFinished(BatchJob $dbBatchJob, kTransformMetadataJobData $data)
	{
		if($data->getSrcXsl())
		{
			$metadataProfile = MetadataProfilePeer::retrieveByPK($data->getMetadataProfileId());
			if($metadataProfile)
			{
				$metadataProfile->setStatus(MetadataProfile::STATUS_ACTIVE);
				$metadataProfile->save();
			}
		}
		
		return $dbBatchJob;
	}
	
	protected function updatedTransformMetadataFailed(BatchJob $dbBatchJob, kTransformMetadataJobData $data)
	{
		if(!$data->getMetadataProfileId())
			return $dbBatchJob;
			
		$metadataProfile = MetadataProfilePeer::retrieveByPK($data->getMetadataProfileId());
		if(!$metadataProfile)
			return $dbBatchJob;
	
		if($data->getSrcXsl())
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
			$relatedObject->indexToSearchIndex();
		}

		/** @var Metadata $object */
		if ($object->getObjectType() == MetadataObjectType::DYNAMIC_OBJECT &&
			!$object->isLikeNew() &&
			!$object->getMetadataProfile()->getDisableReIndexing())
		{
			/**
			 * when dynamic object is modified, we need to reindex the metadata and the objects (users, entries)
			 * that are referencing it
			 */
			$profileFields = MetadataProfileFieldPeer::retrieveByPartnerAndRelatedMetadataProfileId($object->getPartnerId(), $object->getMetadataProfileId());
			$relatedMetadataProfiles = array();
			foreach ($profileFields as $profileField)
			{
				/** @var MetadataProfileField $profileField */
				if (in_array($profileField->getMetadataProfileId(), $relatedMetadataProfiles))
					continue;

				$filter = new MetadataFilter();
				$filter->set('_eq_metadata_profile_id', $profileField->getMetadataProfileId());
				$indexObjectType = kPluginableEnumsManager::apiToCore('IndexObjectType', MetadataPlugin::getApiValue(MetadataIndexObjectType::METADATA));
				kJobsManager::addIndexJob($object->getPartnerId(), $indexObjectType, $filter, true);
				$relatedMetadataProfiles[] = $profileField->getMetadataProfileId();
			}
		}

		if($relatedObject instanceof entry)
		{
			kStorageExporter::reExportEntry($relatedObject);
		}
		return true;
	}
}