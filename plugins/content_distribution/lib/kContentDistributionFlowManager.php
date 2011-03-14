<?php
/**
 * @package plugins.contentDistribution
 * @subpackage lib
 */
class kContentDistributionFlowManager extends kContentDistributionManager implements kObjectChangedEventConsumer, kObjectCreatedEventConsumer, kBatchJobStatusEventConsumer, kObjectDeletedEventConsumer, kObjectUpdatedEventConsumer, kObjectAddedEventConsumer, kObjectDataChangedEventConsumer
{
	/**
	 * @param BaseObject $object
	 * @param array $modifiedColumns
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		if($object instanceof entry && $object->getStatus() == entryStatus::READY)
		{
			if(in_array(entryPeer::STATUS, $modifiedColumns))
				return self::onEntryReady($object);
			else
				return self::onEntryChanged($object, $modifiedColumns);
		}
		
		if($object instanceof asset && $object->getStatus() == asset::FLAVOR_ASSET_STATUS_READY)
		{
			if(in_array(assetPeer::STATUS, $modifiedColumns))
				return self::onAssetReadyOrDeleted($object);
				
			if(in_array(assetPeer::VERSION, $modifiedColumns))
				return self::onAssetVersionChanged($object);
		}
		
		if($object instanceof EntryDistribution)
			return self::onEntryDistributionChanged($object, $modifiedColumns);
		
		return true;
	}
	
	/**
	 * @param BaseObject $object
	 * @return bool true if should continue to the next consumer
	 */
	public function objectCreated(BaseObject $object)
	{
		if($object instanceof asset && $object->getStatus() == asset::FLAVOR_ASSET_STATUS_READY)
			return self::onAssetReadyOrDeleted($object);
			
		return true;
	}
	
	/**
	 * @param BaseObject $object
	 * @return bool true if should continue to the next consumer
	 */
	public function objectUpdated(BaseObject $object)
	{
		if($object instanceof EntryDistribution)
		{
			$entry = entryPeer::retrieveByPK($object->getEntryId());
			if($entry)
			{
				$entry->setUpdatedAt(time());
				$entry->save();
			}
		}
		
		return true;
	}
	
	/**
	 * @param BaseObject $object
	 * @return bool true if should continue to the next consumer
	 */
	public function objectAdded(BaseObject $object)
	{
		if($object instanceof EntryDistribution)
		{
			$entry = entryPeer::retrieveByPK($object->getEntryId());
			if($entry) // updated in the indexing server (sphinx)
				kEventsManager::raiseEvent(new kObjectUpdatedEvent($entry));
		}
		
		return true;
	}
	
	/**
	 * @param BatchJob $dbBatchJob
	 * @param BatchJob $twinJob
	 */
	public function updatedJob(BatchJob $dbBatchJob, BatchJob $twinJob = null)
	{
		if($dbBatchJob->getJobType() == ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT))
			self::onDistributionSubmitJobUpdated($dbBatchJob, $dbBatchJob->getData(), $twinJob);
		
		if($dbBatchJob->getJobType() == ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE))
			self::onDistributionUpdateJobUpdated($dbBatchJob, $dbBatchJob->getData(), $twinJob);
		
		if($dbBatchJob->getJobType() == ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE))
			self::onDistributionDeleteJobUpdated($dbBatchJob, $dbBatchJob->getData(), $twinJob);
		
		if($dbBatchJob->getJobType() == ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT))
			self::onDistributionFetchReportJobUpdated($dbBatchJob, $dbBatchJob->getData(), $twinJob);
		
		return true;
	}
	
	/**
	 * @param BaseObject $object
	 * @return bool true if should continue to the next consumer
	 */
	public function objectDeleted(BaseObject $object)
	{
		if($object instanceof entry)
			return self::onEntryDeleted($object);
		
		if($object instanceof GenericDistributionProvider)
			return self::onGenericDistributionProviderDeleted($object);
	
		if($object instanceof SyndicationDistributionProfile)
			return self::onSyndicationDistributionProfileDeleted($object);
	
		if($object instanceof Metadata)
			return self::onMetadataDeleted($object);
	
		if($object instanceof asset)
			return self::onAssetReadyOrDeleted($object);
			
		if($object instanceof EntryDistribution)
		{
			$entry = entryPeer::retrieveByPK($object->getEntryId());
			if($entry) // updated in the indexing server (sphinx)
				kEventsManager::raiseEvent(new kObjectUpdatedEvent($entry));
		}
		
		return true;
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

		return self::onMetadataChanged($object, $previousVersion);
	}
	
	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionSubmitJobData $data
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function onDistributionSubmitJobUpdated(BatchJob $dbBatchJob, kDistributionSubmitJobData $data, BatchJob $twinJob = null)
	{
		if($data->getRemoteId() || ($data->getResults() && $data->getSentData()))
		{
			$entryDistribution = EntryDistributionPeer::retrieveByPK($data->getEntryDistributionId());
			if(!$entryDistribution)
			{
				KalturaLog::err("Entry distribution [" . $data->getEntryDistributionId() . "] not found");
				return $dbBatchJob;
			}
			
			if($data->getResults())
				$entryDistribution->incrementSubmitResultsVersion();
				
			if($data->getSentData())
				$entryDistribution->incrementSubmitDataVersion();
				
			if($data->getRemoteId())
				$entryDistribution->setRemoteId($data->getRemoteId());
				
			if($data->getMediaFiles())
				$entryDistribution->setMediaFiles($data->getMediaFiles());
				
			$entryDistribution->save();
			
			if($data->getResults())
			{
				$key = $entryDistribution->getSyncKey(EntryDistribution::FILE_SYNC_ENTRY_DISTRIBUTION_SUBMIT_RESULTS);
				kFileSyncUtils::file_put_contents($key, $data->getResults());
				$data->setResults(null);
			}
			
			if($data->getSentData())
			{
				$key = $entryDistribution->getSyncKey(EntryDistribution::FILE_SYNC_ENTRY_DISTRIBUTION_SUBMIT_DATA);
				kFileSyncUtils::file_put_contents($key, $data->getSentData());
				$data->setSentData(null);
			}
			$dbBatchJob->save();
		}
		
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return self::onDistributionSubmitJobPending($dbBatchJob, $data, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return self::onDistributionSubmitJobFinished($dbBatchJob, $data, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return self::onDistributionSubmitJobFailed($dbBatchJob, $data, $twinJob);
			default:
				return $dbBatchJob;
		}
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionUpdateJobData $data
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function onDistributionUpdateJobUpdated(BatchJob $dbBatchJob, kDistributionUpdateJobData $data, BatchJob $twinJob = null)
	{
		if($data->getResults() && $data->getSentData())
		{
			$entryDistribution = EntryDistributionPeer::retrieveByPK($data->getEntryDistributionId());
			if(!$entryDistribution)
			{
				KalturaLog::err("Entry distribution [" . $data->getEntryDistributionId() . "] not found");
				return $dbBatchJob;
			}
			
			if($data->getResults())
				$entryDistribution->incrementUpdateResultsVersion();
				
			if($data->getSentData())
				$entryDistribution->incrementUpdateDataVersion();
				
			$entryDistribution->save();
			
			if($data->getResults())
			{
				$key = $entryDistribution->getSyncKey(EntryDistribution::FILE_SYNC_ENTRY_DISTRIBUTION_UPDATE_RESULTS);
				kFileSyncUtils::file_put_contents($key, $data->getResults());
				$data->setResults(null);
			}
			
			if($data->getSentData())
			{
				$key = $entryDistribution->getSyncKey(EntryDistribution::FILE_SYNC_ENTRY_DISTRIBUTION_UPDATE_DATA);
				kFileSyncUtils::file_put_contents($key, $data->getSentData());
				$data->setSentData(null);
			}
			$dbBatchJob->save();
		}
		
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return self::onDistributionUpdateJobPending($dbBatchJob, $data, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return self::onDistributionUpdateJobFinished($dbBatchJob, $data, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return self::onDistributionUpdateJobFailed($dbBatchJob, $data, $twinJob);
			default:
				return $dbBatchJob;
		}
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionDeleteJobData $data
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function onDistributionDeleteJobUpdated(BatchJob $dbBatchJob, kDistributionDeleteJobData $data, BatchJob $twinJob = null)
	{
		if($data->getResults() && $data->getSentData())
		{
			$entryDistribution = EntryDistributionPeer::retrieveByPK($data->getEntryDistributionId());
			if(!$entryDistribution)
			{
				KalturaLog::err("Entry distribution [" . $data->getEntryDistributionId() . "] not found");
				return $dbBatchJob;
			}
			
			if($data->getResults())
				$entryDistribution->incrementDeleteResultsVersion();
				
			if($data->getSentData())
				$entryDistribution->incrementDeleteDataVersion();
				
			$entryDistribution->save();
			
			if($data->getResults())
			{
				$key = $entryDistribution->getSyncKey(EntryDistribution::FILE_SYNC_ENTRY_DISTRIBUTION_DELETE_RESULTS);
				kFileSyncUtils::file_put_contents($key, $data->getResults());
				$data->setResults(null);
			}
			
			if($data->getSentData())
			{
				$key = $entryDistribution->getSyncKey(EntryDistribution::FILE_SYNC_ENTRY_DISTRIBUTION_DELETE_DATA);
				kFileSyncUtils::file_put_contents($key, $data->getSentData());
				$data->setSentData(null);
			}
			$dbBatchJob->save();
		}
		
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return self::onDistributionDeleteJobPending($dbBatchJob, $data, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return self::onDistributionDeleteJobFinished($dbBatchJob, $data, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return self::onDistributionDeleteJobFailed($dbBatchJob, $data, $twinJob);
			default:
				return $dbBatchJob;
		}
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionFetchReportJobData $data
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function onDistributionFetchReportJobUpdated(BatchJob $dbBatchJob, kDistributionFetchReportJobData $data, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return self::onDistributionFetchReportJobFinished($dbBatchJob, $data, $twinJob);
			default:
				return $dbBatchJob;
		}
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionSubmitJobData $data
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function onDistributionSubmitJobPending(BatchJob $dbBatchJob, kDistributionSubmitJobData $data, BatchJob $twinJob = null)
	{
		$entryDistribution = EntryDistributionPeer::retrieveByPK($data->getEntryDistributionId());
		if(!$entryDistribution)
		{
			KalturaLog::err("Entry distribution [" . $data->getEntryDistributionId() . "] not found");
			return $dbBatchJob;
		}
		
		$entryDistribution->setStatus(EntryDistributionStatus::SUBMITTING);
		$entryDistribution->setDirtyStatus(null);
		$entryDistribution->save();
	
		if($data->getProviderType() == DistributionProviderType::SYNDICATION)
		{
			$dbBatchJob = kJobsManager::updateBatchJob($dbBatchJob, BatchJob::BATCHJOB_STATUS_FINISHED);
		}
		
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionUpdateJobData $data
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function onDistributionUpdateJobPending(BatchJob $dbBatchJob, kDistributionUpdateJobData $data, BatchJob $twinJob = null)
	{
		$entryDistribution = EntryDistributionPeer::retrieveByPK($data->getEntryDistributionId());
		if(!$entryDistribution)
		{
			KalturaLog::err("Entry distribution [" . $data->getEntryDistributionId() . "] not found");
			return $dbBatchJob;
		}
		
		$entryDistribution->setStatus(EntryDistributionStatus::UPDATING);
		$entryDistribution->setDirtyStatus(null);
		$entryDistribution->save();
	
		if($data->getProviderType() == DistributionProviderType::SYNDICATION)
		{
			$dbBatchJob = kJobsManager::updateBatchJob($dbBatchJob, BatchJob::BATCHJOB_STATUS_FINISHED);
		}
		
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionDeleteJobData $data
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function onDistributionDeleteJobPending(BatchJob $dbBatchJob, kDistributionDeleteJobData $data, BatchJob $twinJob = null)
	{
		$entryDistribution = EntryDistributionPeer::retrieveByPK($data->getEntryDistributionId());
		if(!$entryDistribution)
		{
			KalturaLog::err("Entry distribution [" . $data->getEntryDistributionId() . "] not found");
			return $dbBatchJob;
		}
		
		$entryDistribution->setStatus(EntryDistributionStatus::DELETING);
		$entryDistribution->setDirtyStatus(null);
		$entryDistribution->save();
	
		if($data->getProviderType() == DistributionProviderType::SYNDICATION)
		{
			$dbBatchJob = kJobsManager::updateBatchJob($dbBatchJob, BatchJob::BATCHJOB_STATUS_FINISHED);
		}
		
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionSubmitJobData $data
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function onDistributionSubmitJobFinished(BatchJob $dbBatchJob, kDistributionSubmitJobData $data, BatchJob $twinJob = null)
	{
		$entryDistribution = EntryDistributionPeer::retrieveByPK($data->getEntryDistributionId());
		if(!$entryDistribution)
		{
			KalturaLog::err("Entry distribution [" . $data->getEntryDistributionId() . "] not found");
			return $dbBatchJob;
		}
		
		$entryDistribution->setErrorType(null);
		$entryDistribution->setErrorNumber(null);
		$entryDistribution->setErrorDescription(null);
		$entryDistribution->setSubmittedAt(time());
		$entryDistribution->setStatus(EntryDistributionStatus::READY);
		$entryDistribution->setDirtyStatus(null);
	
		$distributionProfileId = $entryDistribution->getDistributionProfileId();
		$distributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if(!$distributionProfile)
		{
			KalturaLog::err("Entry distribution [" . $entryDistribution->getId() . "] profile [$distributionProfileId] not found");
			return $dbBatchJob;
		}
		
		$distributionProvider = $distributionProfile->getProvider();
		if(!$distributionProvider->isScheduleUpdateEnabled() && $entryDistribution->getSunset(null) > 0)
			$entryDistribution->setDirtyStatus(EntryDistributionDirtyStatus::DELETE_REQUIRED);
			
		$entryDistribution->save();
		
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionUpdateJobData $data
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function onDistributionUpdateJobFinished(BatchJob $dbBatchJob, kDistributionUpdateJobData $data, BatchJob $twinJob = null)
	{
		$entryDistribution = EntryDistributionPeer::retrieveByPK($data->getEntryDistributionId());
		if(!$entryDistribution)
		{
			KalturaLog::err("Entry distribution [" . $data->getEntryDistributionId() . "] not found");
			return $dbBatchJob;
		}
		
		$entryDistribution->setErrorType(null);
		$entryDistribution->setErrorNumber(null);
		$entryDistribution->setErrorDescription(null);
		
		$entryDistribution->setStatus(EntryDistributionStatus::READY);
		$entryDistribution->setDirtyStatus(null);
	
		$distributionProfileId = $entryDistribution->getDistributionProfileId();
		$distributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if(!$distributionProfile)
		{
			KalturaLog::err("Entry distribution [" . $entryDistribution->getId() . "] profile [$distributionProfileId] not found");
			return $dbBatchJob;
		}
		
		$distributionProvider = $distributionProfile->getProvider();
		if(!$distributionProvider->isScheduleUpdateEnabled() && $entryDistribution->getSunset(null) > 0)
			$entryDistribution->setDirtyStatus(EntryDistributionDirtyStatus::DELETE_REQUIRED);
			
		$entryDistribution->save();
		
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionDeleteJobData $data
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function onDistributionDeleteJobFinished(BatchJob $dbBatchJob, kDistributionDeleteJobData $data, BatchJob $twinJob = null)
	{
		$entryDistribution = EntryDistributionPeer::retrieveByPK($data->getEntryDistributionId());
		if(!$entryDistribution)
		{
			KalturaLog::err("Entry distribution [" . $data->getEntryDistributionId() . "] not found");
			return $dbBatchJob;
		}
		
		$entryDistribution->setErrorType(null);
		$entryDistribution->setErrorNumber(null);
		$entryDistribution->setErrorDescription(null);
		$entryDistribution->setStatus(EntryDistributionStatus::REMOVED);
		$entryDistribution->setDirtyStatus(null);
		$entryDistribution->save();
		
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionFetchReportJobData $data
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function onDistributionFetchReportJobFinished(BatchJob $dbBatchJob, kDistributionFetchReportJobData $data, BatchJob $twinJob = null)
	{
		$entryDistribution = EntryDistributionPeer::retrieveByPK($data->getEntryDistributionId());
		if(!$entryDistribution)
		{
			KalturaLog::err("Entry distribution [" . $data->getEntryDistributionId() . "] not found");
			return $dbBatchJob;
		}
		$entryDistribution->setPlays($data->getPlays());
		$entryDistribution->setViews($data->getViews());
		$entryDistribution->save();
		
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionSubmitJobData $data
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function onDistributionSubmitJobFailed(BatchJob $dbBatchJob, kDistributionSubmitJobData $data, BatchJob $twinJob = null)
	{
		$entryDistribution = EntryDistributionPeer::retrieveByPK($data->getEntryDistributionId());
		if(!$entryDistribution)
		{
			KalturaLog::err("Entry distribution [" . $data->getEntryDistributionId() . "] not found");
			return $dbBatchJob;
		}
		
		$entryDistribution->setErrorType($dbBatchJob->getErrType());
		$entryDistribution->setErrorNumber($dbBatchJob->getErrNumber());
		$entryDistribution->setErrorDescription($dbBatchJob->getMessage());
		
		$entryDistribution->setStatus(EntryDistributionStatus::ERROR_SUBMITTING);
		$entryDistribution->setDirtyStatus(null);
		$entryDistribution->save();
		
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionUpdateJobData $data
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function onDistributionUpdateJobFailed(BatchJob $dbBatchJob, kDistributionUpdateJobData $data, BatchJob $twinJob = null)
	{
		$entryDistribution = EntryDistributionPeer::retrieveByPK($data->getEntryDistributionId());
		if(!$entryDistribution)
		{
			KalturaLog::err("Entry distribution [" . $data->getEntryDistributionId() . "] not found");
			return $dbBatchJob;
		}
		
		$entryDistribution->setErrorType($dbBatchJob->getErrType());
		$entryDistribution->setErrorNumber($dbBatchJob->getErrNumber());
		$entryDistribution->setErrorDescription($dbBatchJob->getMessage());
		
		$entryDistribution->setStatus(EntryDistributionStatus::ERROR_UPDATING);
		$entryDistribution->setDirtyStatus(null);
		$entryDistribution->save();
		
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionDeleteJobData $data
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function onDistributionDeleteJobFailed(BatchJob $dbBatchJob, kDistributionDeleteJobData $data, BatchJob $twinJob = null)
	{
		$entryDistribution = EntryDistributionPeer::retrieveByPK($data->getEntryDistributionId());
		if(!$entryDistribution)
		{
			KalturaLog::err("Entry distribution [" . $data->getEntryDistributionId() . "] not found");
			return $dbBatchJob;
		}
		
		$entryDistribution->setErrorType($dbBatchJob->getErrType());
		$entryDistribution->setErrorNumber($dbBatchJob->getErrNumber());
		$entryDistribution->setErrorDescription($dbBatchJob->getMessage());
		
		$entryDistribution->setStatus(EntryDistributionStatus::ERROR_DELETING);
		$entryDistribution->setDirtyStatus(null);
		$entryDistribution->save();
		
		return $dbBatchJob;
	}

	/**
	 * @param Metadata $metadata
	 */
	public static function onMetadataDeleted(Metadata $metadata)
	{
		if(!ContentDistributionPlugin::isAllowedPartner($metadata->getPartnerId()))
			return true;
			
		if($metadata->getObjectType() != KalturaMetadataObjectType::ENTRY)
			return true;
		
		KalturaLog::log("Metadata [" . $metadata->getId() . "] for entry [" . $metadata->getObjectId() . "] deleted");
		
		$entryDistributions = EntryDistributionPeer::retrieveByEntryId($metadata->getObjectId());
		foreach($entryDistributions as $entryDistribution)
		{
			if($entryDistribution->getStatus() != EntryDistributionStatus::QUEUED && $entryDistribution->getStatus() != EntryDistributionStatus::PENDING && $entryDistribution->getStatus() != EntryDistributionStatus::READY)
				continue;
		
			$distributionProfileId = $entryDistribution->getDistributionProfileId();
			$distributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
			if(!$distributionProfile)
			{
				KalturaLog::err("Entry distribution [" . $entryDistribution->getId() . "] profile [$distributionProfileId] not found");
				continue;
			}
			
			$distributionProvider = $distributionProfile->getProvider();
			if(!$distributionProvider)
			{
				KalturaLog::err("Entry distribution [" . $entryDistribution->getId() . "] provider [" . $distributionProfile->getProviderType() . "] not found");
				continue;
			}
			
			if($entryDistribution->getStatus() == EntryDistributionStatus::PENDING || $entryDistribution->getStatus() == EntryDistributionStatus::QUEUED)
			{
				$validationErrors = $distributionProfile->validateForSubmission($entryDistribution, DistributionAction::SUBMIT);
				$entryDistribution->setValidationErrorsArray($validationErrors);
				$entryDistribution->save();
			
				if($entryDistribution->getStatus() == EntryDistributionStatus::QUEUED)
				{
					if($entryDistribution->getDirtyStatus() != EntryDistributionDirtyStatus::SUBMIT_REQUIRED)
						self::submitAddEntryDistribution($entryDistribution, $distributionProfile);
				}
				continue;
			}
		
			if($entryDistribution->getStatus() == EntryDistributionStatus::READY)
			{
				if($entryDistribution->getDirtyStatus() == EntryDistributionDirtyStatus::UPDATE_REQUIRED)
				{
					KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] already flaged for updating");
					continue;
				}
				
				$distributionProvider = $distributionProfile->getProvider();
				if(!$distributionProvider->isUpdateEnabled())
				{
					KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] provider [" . $distributionProvider->getName() . "] does not support update");
					continue;
				}
				
				$validationErrors = $distributionProfile->validateForSubmission($entryDistribution, DistributionAction::UPDATE);
				$entryDistribution->setValidationErrorsArray($validationErrors);
				$entryDistribution->save();
				
				if(!count($validationErrors) && $distributionProfile->getUpdateEnabled() == DistributionProfileActionStatus::AUTOMATIC)
				{
					self::submitUpdateEntryDistribution($entryDistribution, $distributionProfile);
				}
				else
				{
					KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] should not be updated automatically");
					$entryDistribution->setDirtyStatus(EntryDistributionDirtyStatus::UPDATE_REQUIRED);
					$entryDistribution->save();
					continue;
				}
			}
		}
		
		return true;
	}
	
	/**
	 * @param Metadata $metadata
	 */
	public static function onMetadataChanged(Metadata $metadata, $previousVersion)
	{
		if(!ContentDistributionPlugin::isAllowedPartner($metadata->getPartnerId()))
			return true;
			
		if($metadata->getObjectType() != KalturaMetadataObjectType::ENTRY)
			return true;
		
		KalturaLog::log("Metadata [" . $metadata->getId() . "] for entry [" . $metadata->getObjectId() . "] changed");
		
		$syncKey = $metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
		$xmlPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
		if(!$xmlPath)
		{
			KalturaLog::log("Entry metadata xml not found");
			return true;
		}
		$xml = new DOMDocument();
		$xml->load($xmlPath);
		
		$previousXml = null;
		if($previousVersion)
		{
			$syncKey = $metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA, $previousVersion);
			$xmlPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
			if($xmlPath)
			{
				$previousXml = new DOMDocument();
				$previousXml->load($xmlPath);
			}
			else 
			{
				KalturaLog::log("Entry metadata previous version xml not found");
			}
		}
		
		$entryDistributions = EntryDistributionPeer::retrieveByEntryId($metadata->getObjectId());
		foreach($entryDistributions as $entryDistribution)
		{
			if($entryDistribution->getStatus() != EntryDistributionStatus::QUEUED && $entryDistribution->getStatus() != EntryDistributionStatus::PENDING && $entryDistribution->getStatus() != EntryDistributionStatus::READY)
				continue;
		
			$distributionProfileId = $entryDistribution->getDistributionProfileId();
			$distributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
			if(!$distributionProfile)
			{
				KalturaLog::err("Entry distribution [" . $entryDistribution->getId() . "] profile [$distributionProfileId] not found");
				continue;
			}
			
			$distributionProvider = $distributionProfile->getProvider();
			if(!$distributionProvider)
			{
				KalturaLog::err("Entry distribution [" . $entryDistribution->getId() . "] provider [" . $distributionProfile->getProviderType() . "] not found");
				continue;
			}
			
			if($entryDistribution->getStatus() == EntryDistributionStatus::PENDING || $entryDistribution->getStatus() == EntryDistributionStatus::QUEUED)
			{
				$validationErrors = $distributionProfile->validateForSubmission($entryDistribution, DistributionAction::SUBMIT);
				$entryDistribution->setValidationErrorsArray($validationErrors);
				$entryDistribution->save();
			
				if($entryDistribution->getStatus() == EntryDistributionStatus::QUEUED)
				{
					if($entryDistribution->getDirtyStatus() != EntryDistributionDirtyStatus::SUBMIT_REQUIRED)
						self::submitAddEntryDistribution($entryDistribution, $distributionProfile);
				}
				continue;
			}
		
			if($entryDistribution->getStatus() == EntryDistributionStatus::READY)
			{
				if($entryDistribution->getDirtyStatus() == EntryDistributionDirtyStatus::UPDATE_REQUIRED)
				{
					KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] already flaged for updating");
					continue;
				}
				
				if(!$distributionProvider->isUpdateEnabled())
				{
					KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] provider [" . $distributionProvider->getName() . "] does not support update");
					continue;
				}

				$updateRequiredMetadataXPaths = $distributionProvider->getUpdateRequiredMetadataXPaths($distributionProfileId);
				$updateRequired = false;
				
				foreach($updateRequiredMetadataXPaths as $updateRequiredMetadataXPath)
				{
					$xPath = new DOMXpath($xml);
					$newElements = $xPath->query($updateRequiredMetadataXPath);
					
					$oldElements = null;
					if($previousXml)
					{
						$xPath = new DOMXpath($previousXml);
						$oldElements = $xPath->query($updateRequiredMetadataXPath);
					}
					
					if(is_null($newElements) && is_null($oldElements))
						continue;
						
					if(is_null($newElements) XOR is_null($oldElements))
					{
						$updateRequired = true;
					}
					elseif($newElements->length == $oldElements->length)
					{
						for($index = 0; $index < $newElements->length; $index++)
						{
							$newValue = $newElements->item($index)->textContent;
							$oldValue = $oldElements->item($index)->textContent;
							
							if($newValue != $oldValue)
							{
								$updateRequired = true;
								break;
							}
						}
					}
				
					if($updateRequired)
						break;
				}
				
				if(!$updateRequired)
				{
					KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] update not required");
					continue;	
				}
				
				$validationErrors = $distributionProfile->validateForSubmission($entryDistribution, DistributionAction::UPDATE);
				$entryDistribution->setValidationErrorsArray($validationErrors);
				$entryDistribution->save();
				
				if(!count($validationErrors) && $distributionProfile->getUpdateEnabled() == DistributionProfileActionStatus::AUTOMATIC)
				{
					self::submitUpdateEntryDistribution($entryDistribution, $distributionProfile);
				}
				else
				{
					KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] should not be updated automatically");
					$entryDistribution->setDirtyStatus(EntryDistributionDirtyStatus::UPDATE_REQUIRED);
					$entryDistribution->save();
					continue;
				}
			}
		}
		
		return true;
	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 */
	public static function onEntryDistributionUpdateRequired(EntryDistribution $entryDistribution)
	{
		$ignoreStatuses = array(
			EntryDistributionStatus::PENDING,
			EntryDistributionStatus::DELETED,
			EntryDistributionStatus::DELETING,
			EntryDistributionStatus::QUEUED,
			EntryDistributionStatus::REMOVED,
		);
		
		if(in_array($entryDistribution->getStatus(), $ignoreStatuses))
		{			
			KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] status [" . $entryDistribution->getStatus() . "] does not require update");
			return true;
		}
		
		if($entryDistribution->getDirtyStatus() == EntryDistributionDirtyStatus::UPDATE_REQUIRED)
		{			
			KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] already requires update");
			return true;
		}
			
		$distributionProfileId = $entryDistribution->getDistributionProfileId();
		$distributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if(!$distributionProfile)
		{
			KalturaLog::err("Entry distribution [" . $entryDistribution->getId() . "] profile [$distributionProfileId] not found");
			return true;
		}
				
		$distributionProvider = $distributionProfile->getProvider();
		if(!$distributionProvider->isUpdateEnabled())
		{
			KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] provider [" . $distributionProvider->getName() . "] does not support update");
			return true;
		}

		if($distributionProfile->getUpdateEnabled() != DistributionProfileActionStatus::AUTOMATIC)
		{
			KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] should not be updated automatically");
			$entryDistribution->setDirtyStatus(EntryDistributionDirtyStatus::UPDATE_REQUIRED);
			$entryDistribution->save();
			return true;
		}
		
		self::submitUpdateEntryDistribution($entryDistribution, $distributionProfile);
		return true;
	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param array $modifiedColumns
	 */
	public static function onEntryDistributionChanged(EntryDistribution $entryDistribution, array $modifiedColumns)
	{
		$updateRequiredFields = array(
			EntryDistributionPeer::SUNRISE,
			EntryDistributionPeer::SUNSET,
			EntryDistributionPeer::FLAVOR_ASSET_IDS,
			EntryDistributionPeer::THUMB_ASSET_IDS,
		);
		
		foreach($updateRequiredFields as $updateRequiredField)
			if(in_array($updateRequiredField, $modifiedColumns))
				return self::onEntryDistributionUpdateRequired($entryDistribution);
				
		return true;
	}
	
	/**
	 * @param entry $entry
	 * @param array $modifiedColumns
	 */
	public static function onEntryChanged(entry $entry, array $modifiedColumns)
	{
		if(!ContentDistributionPlugin::isAllowedPartner($entry->getPartnerId()))
			return true;
			
		$ignoreStatuses = array(
			EntryDistributionStatus::PENDING,
			EntryDistributionStatus::DELETED,
			EntryDistributionStatus::DELETING,
			EntryDistributionStatus::QUEUED,
			EntryDistributionStatus::REMOVED,
			EntryDistributionStatus::ERROR_SUBMITTING,
		);
		
		$entryDistributions = EntryDistributionPeer::retrieveByEntryId($entry->getId());
		foreach($entryDistributions as $entryDistribution)
		{
			if(in_array($entryDistribution->getStatus(), $ignoreStatuses))
			{
				KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] status [" . $entryDistribution->getStatus() . "] no update required");
				continue;
			}
				
			if($entryDistribution->getDirtyStatus() == EntryDistributionDirtyStatus::UPDATE_REQUIRED || $entryDistribution->getDirtyStatus() == EntryDistributionDirtyStatus::SUBMIT_REQUIRED)
			{
				KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] already flaged for updating");
				continue;
			}
				
			$distributionProfileId = $entryDistribution->getDistributionProfileId();
			$distributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
			if(!$distributionProfile)
			{
				KalturaLog::err("Entry distribution [" . $entryDistribution->getId() . "] profile [$distributionProfileId] not found");
				continue;
			}

			$distributionProvider = $distributionProfile->getProvider();
			if(!$distributionProvider)
			{
				KalturaLog::err("Entry distribution [" . $entryDistribution->getId() . "] provider [" . $distributionProfile->getProviderType() . "] not found");
				continue;
			}
				
			if(!$distributionProvider->isUpdateEnabled())
			{
				KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] provider [" . $distributionProvider->getName() . "] does not support update");
				continue;
			}
			
			$updateRequiredEntryFields = $distributionProvider->getUpdateRequiredEntryFields($distributionProfileId);
			$updateRequired = false;
			
			foreach($updateRequiredEntryFields as $updateRequiredEntryField)
			{
				if(in_array($updateRequiredEntryField, $modifiedColumns))
				{
					$updateRequired = true;
					break;
				}
			}
			
			if(!$updateRequired)
			{
				KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] update not required");
				continue;	
			}
			
			if($distributionProfile->getUpdateEnabled() != DistributionProfileActionStatus::AUTOMATIC)
			{
				KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] should not be updated automatically");
				$entryDistribution->setDirtyStatus(EntryDistributionDirtyStatus::UPDATE_REQUIRED);
				$entryDistribution->save();
				continue;
			}
			
			self::submitUpdateEntryDistribution($entryDistribution, $distributionProfile);
		}
		
		return true;
	}
	
	/**
	 * @param SyndicationDistributionProfile $syndicationDistributionProfile
	 */
	public static function onSyndicationDistributionProfileDeleted(SyndicationDistributionProfile $syndicationDistributionProfile)
	{
		// deletes the feed
		$feed = syndicationFeedPeer::retrieveByPK($syndicationDistributionProfile->getFeedId());
		if(!$feed)
			return;
			
		if($feed->getDisplayInSearch() == mySearchUtils::DISPLAY_IN_SEARCH_NONE)
		{
			$feed->setStatus(syndicationFeed::SYNDICATION_DELETED);
			$feed->save();
		}
		
		// deletes the playlist
		$playlist = entryPeer::retrieveByPK($feed->getPlaylistId());
		if($playlist && $playlist->getDisplayInSearch() == mySearchUtils::DISPLAY_IN_SEARCH_NONE)
		{
			$playlist->setStatus(entryStatus::DELETED);
			$playlist->save();
		}
	}
	
	/**
	 * @param GenericDistributionProvider $genericDistributionProvider
	 */
	public static function onGenericDistributionProviderDeleted(GenericDistributionProvider $genericDistributionProvider)
	{
		$genericDistributionProfiles = GenericDistributionProfilePeer::retrieveByProviderId($genericDistributionProvider->getId());
		foreach($genericDistributionProfiles as $genericDistributionProfile)
		{
			$genericDistributionProfiles->setStatus(DistributionProfileStatus::DELETED);
			$genericDistributionProfiles->save();
		}
	}
	
	/**
	 * @param entry $entry
	 */
	public static function onEntryDeleted(entry $entry)
	{
		if(!ContentDistributionPlugin::isAllowedPartner($entry->getPartnerId()))
			return true;
			
		$entryDistributions = EntryDistributionPeer::retrieveByEntryId($entry->getId());
		foreach($entryDistributions as $entryDistribution)
		{
			if($entryDistribution->getStatus() == EntryDistributionStatus::DELETING)
			{
				KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] already deleting");
				continue;
			}
				
			if($entryDistribution->getDirtyStatus() == EntryDistributionDirtyStatus::DELETE_REQUIRED)
			{
				KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] already flaged for deletion");
				continue;
			}
				
			$distributionProfileId = $entryDistribution->getDistributionProfileId();
			$distributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
			if(!$distributionProfile || $distributionProfile->getDeleteEnabled() != DistributionProfileActionStatus::AUTOMATIC)
			{
				KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] should not be deleted automatically");
				continue;
			}
				
			self::submitDeleteEntryDistribution($entryDistribution, $distributionProfile);
		}
		
		return true;
	}

	/**
	 * @param entry $entry
	 */
	public static function onEntryReady(entry $entry)
	{
		if(!ContentDistributionPlugin::isAllowedPartner($entry->getPartnerId()))
			return true;
			
		$distributionProfiles = DistributionProfilePeer::retrieveByPartnerId($entry->getPartnerId());
		foreach($distributionProfiles as $distributionProfile)
			if($distributionProfile->getSubmitEnabled() == DistributionProfileActionStatus::AUTOMATIC)
				self::addEntryDistribution($entry, $distributionProfile, true);
		
		return true;
	}

	/**
	 * @param asset $asset
	 */
	public static function onAssetVersionChanged(asset $asset)
	{
		if(!ContentDistributionPlugin::isAllowedPartner($asset->getPartnerId()))
			return true;
			
		$entry = $asset->getentry();
		if(!$entry)
			return true;
			
		$entryDistributions = EntryDistributionPeer::retrieveByEntryId($asset->getEntryId());
		foreach($entryDistributions as $entryDistribution)
			self::onEntryDistributionUpdateRequired($entryDistribution);
		
		return true;
	}
	
	/**
	 * @param asset $asset
	 */
	public static function onAssetReadyOrDeleted(asset $asset)
	{
		if(!ContentDistributionPlugin::isAllowedPartner($asset->getPartnerId()))
			return true;
			
		$entry = $asset->getentry();
		if(!$entry)
			return true;
			
		$entryDistributions = EntryDistributionPeer::retrieveByEntryId($asset->getEntryId());
		foreach($entryDistributions as $entryDistribution)
		{
			$distributionProfileId = $entryDistribution->getDistributionProfileId();
			$distributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
			if(!$distributionProfile)
				continue;
				
			if($entryDistribution->getStatus() == EntryDistributionStatus::QUEUED || $entryDistribution->getStatus() == EntryDistributionStatus::PENDING)
			{
				$listChanged = kContentDistributionManager::assignFlavorAssets($entryDistribution, $entry, $distributionProfile);
				$listChanged = ($listChanged || kContentDistributionManager::assignThumbAssets($entryDistribution, $entry, $distributionProfile));
				
				if(!$listChanged)
					continue;
				
				$validationErrors = $distributionProfile->validateForSubmission($entryDistribution, DistributionAction::SUBMIT);
				$entryDistribution->setValidationErrorsArray($validationErrors);
				$entryDistribution->save();
				
				if($entryDistribution->getStatus() == EntryDistributionStatus::QUEUED)
				{
					if($entryDistribution->getDirtyStatus() != EntryDistributionDirtyStatus::SUBMIT_REQUIRED)
						self::submitAddEntryDistribution($entryDistribution, $distributionProfile);
				}
			} // submit
			
			if($entryDistribution->getStatus() == EntryDistributionStatus::READY || $entryDistribution->getStatus() == EntryDistributionStatus::ERROR_UPDATING)
			{
				$distributionProvider = $distributionProfile->getProvider();
				if(!$distributionProvider->isUpdateEnabled() || !$distributionProvider->isMediaUpdateEnabled())
				{
					KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] provider [" . $distributionProvider->getName() . "] does not support update");
					continue;
				}
				
				$listChanged = kContentDistributionManager::assignFlavorAssets($entryDistribution, $entry, $distributionProfile);
				$listChanged = ($listChanged || kContentDistributionManager::assignThumbAssets($entryDistribution, $entry, $distributionProfile));
				
				if(!$listChanged)
					continue;
					
				$validationErrors = $distributionProfile->validateForSubmission($entryDistribution, DistributionAction::UPDATE);
				$entryDistribution->setValidationErrorsArray($validationErrors);
				$entryDistribution->save();
				
				if(!count($validationErrors) && $distributionProfile->getUpdateEnabled() == DistributionProfileActionStatus::AUTOMATIC)
				{
					self::submitUpdateEntryDistribution($entryDistribution, $distributionProfile);
				}
				else
				{
					KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] should not be updated automatically");
					$entryDistribution->setDirtyStatus(EntryDistributionDirtyStatus::UPDATE_REQUIRED);
					$entryDistribution->save();
					continue;
				}
			} // update
		}
		
		return true;
	}
}