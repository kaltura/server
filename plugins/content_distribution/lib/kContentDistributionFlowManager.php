<?php
/**
 * @package plugins.contentDistribution
 * @subpackage lib
 */
class kContentDistributionFlowManager extends kContentDistributionManager implements kObjectChangedEventConsumer, kObjectCreatedEventConsumer, kBatchJobStatusEventConsumer, kObjectDeletedEventConsumer, kObjectUpdatedEventConsumer, kObjectAddedEventConsumer, kObjectDataChangedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if($object instanceof entry && $object->wasObjectSaved())
			return true;
		
		if($object instanceof asset && $object->getStatus() == asset::FLAVOR_ASSET_STATUS_READY && in_array(assetPeer::STATUS, $modifiedColumns) || in_array(assetPeer::VERSION, $modifiedColumns))
			return true;
		
		if($object instanceof EntryDistribution)
			return true;
			
		return false;		
	}
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		if($object instanceof entry && $object->getStatus() != entryStatus::DELETED)
		{
			if(in_array(entryPeer::STATUS, $modifiedColumns) && $object->getStatus() == entryStatus::READY)
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
			
			KalturaLog::log("Status and version didn't change");
		}
		
		if($object instanceof EntryDistribution)
			return self::onEntryDistributionChanged($object, $modifiedColumns);
		
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::shouldConsumeCreatedEvent()
	 */
	public function shouldConsumeCreatedEvent(BaseObject $object)
	{
		if($object instanceof asset && $object->getStatus() == asset::FLAVOR_ASSET_STATUS_READY)
			return true;
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::objectCreated()
	 */
	public function objectCreated(BaseObject $object)
	{
		return self::onAssetReadyOrDeleted($object);
	}
	
	/* (non-PHPdoc)
	 * @see kObjectUpdatedEventConsumer::shouldConsumeUpdatedEvent()
	 */
	public function shouldConsumeUpdatedEvent(BaseObject $object)
	{
		if($object instanceof EntryDistribution)
			return true;
			
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectUpdatedEventConsumer::objectUpdated()
	 */
	public function objectUpdated(BaseObject $object, BatchJob $raisedJob = null)
	{
		$entry = entryPeer::retrieveByPKNoFilter($object->getEntryId());
		if($entry)
		{
			$entry->setUpdatedAt(time());
			$entry->save();
			$entry->indexToSearchIndex();
		}
		
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::shouldConsumeAddedEvent()
	 */
	public function shouldConsumeAddedEvent(BaseObject $object)
	{
		if($object instanceof EntryDistribution)
			return true;
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::objectAdded()
	 */
	public function objectAdded(BaseObject $object, BatchJob $raisedJob = null)
	{
		$entry = entryPeer::retrieveByPK($object->getEntryId());
		if($entry) // updated in the indexing server (sphinx)
			kEventsManager::raiseEvent(new kObjectUpdatedEvent($entry, $raisedJob));
		
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		if($dbBatchJob->getJobType() == ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT))
			return true;
		
		if($dbBatchJob->getJobType() == ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE))
			return true;
		
		if($dbBatchJob->getJobType() == ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE))
			return true;
		
		if($dbBatchJob->getJobType() == ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT))
			return true;
		
		if($dbBatchJob->getJobType() == ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_ENABLE))
			return true;
		
		if($dbBatchJob->getJobType() == ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DISABLE))
			return true;
		
		if($dbBatchJob->getJobType() == BatchJobType::IMPORT)
		    return true;
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob)
	{
		if($dbBatchJob->getJobType() == ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT))
			self::onDistributionSubmitJobUpdated($dbBatchJob, $dbBatchJob->getData());
		
		if($dbBatchJob->getJobType() == ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE))
			self::onDistributionUpdateJobUpdated($dbBatchJob, $dbBatchJob->getData());
		
		if($dbBatchJob->getJobType() == ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE))
			self::onDistributionDeleteJobUpdated($dbBatchJob, $dbBatchJob->getData());
		
		if($dbBatchJob->getJobType() == ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT))
			self::onDistributionFetchReportJobUpdated($dbBatchJob, $dbBatchJob->getData());
		
		if($dbBatchJob->getJobType() == ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_ENABLE))
			self::onDistributionEnableJobUpdated($dbBatchJob, $dbBatchJob->getData());
		 
		if($dbBatchJob->getJobType() == ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DISABLE))
			self::onDistributionDisableJobUpdated($dbBatchJob, $dbBatchJob->getData());
		
		if($dbBatchJob->getJobType() == BatchJobType::IMPORT)
			self::onImportJobUpdated($dbBatchJob, $dbBatchJob->getData());
		
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::shouldConsumeDeletedEvent()
	 */
	public function shouldConsumeDeletedEvent(BaseObject $object)
	{
		if($object instanceof entry)
			return true;
		
		if($object instanceof GenericDistributionProvider)
			return true;
	
		if($object instanceof SyndicationDistributionProfile)
			return true;
	
		if($object instanceof Metadata)
			return true;
	
		if($object instanceof asset)
			return true;
			
		if($object instanceof EntryDistribution)
			return true;
			
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::objectDeleted()
	 */
	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null)
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
				kEventsManager::raiseEvent(new kObjectUpdatedEvent($entry, $raisedJob));
		}
		
		return true;
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
		return self::onMetadataChanged($object, $previousVersion);
	}
	
	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionSubmitJobData $data
	 * @return BatchJob
	 */
	public static function onDistributionSubmitJobUpdated(BatchJob $dbBatchJob, kDistributionSubmitJobData $data)
	{
		if($data->getRemoteId() || $data->getResults() || $data->getSentData() || $data->getMediaFiles())
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
			$dbBatchJob->setData($data);
			$dbBatchJob->save();
		}
		
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return self::onDistributionSubmitJobPending($dbBatchJob, $data);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return self::onDistributionSubmitJobFinished($dbBatchJob, $data);
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return self::onDistributionSubmitJobFailed($dbBatchJob, $data);
			default:
				return $dbBatchJob;
		}
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionUpdateJobData $data
	 * @return BatchJob
	 */
	public static function onDistributionUpdateJobUpdated(BatchJob $dbBatchJob, kDistributionUpdateJobData $data)
	{
		if($data->getResults() || $data->getSentData() || $data->getMediaFiles())
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
				
			if($data->getMediaFiles())
				$entryDistribution->setMediaFiles($data->getMediaFiles());
				
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
			$dbBatchJob->setData($data);
			$dbBatchJob->save();
		}
		
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return self::onDistributionUpdateJobPending($dbBatchJob, $data);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return self::onDistributionUpdateJobFinished($dbBatchJob, $data);
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return self::onDistributionUpdateJobFailed($dbBatchJob, $data);
			default:
				return $dbBatchJob;
		}
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionUpdateJobData $data
	 * @return BatchJob
	 */
	public static function onDistributionEnableJobUpdated(BatchJob $dbBatchJob, kDistributionUpdateJobData $data)
	{
		if($data->getResults() || $data->getSentData())
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
			$dbBatchJob->setData($data);
			$dbBatchJob->save();
		}
		
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return self::onDistributionEnableJobPending($dbBatchJob, $data);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return self::onDistributionEnableJobFinished($dbBatchJob, $data);
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return self::onDistributionEnableJobFailed($dbBatchJob, $data);
			default:
				return $dbBatchJob;
		}
	}
	
	/**
	 * @param BatchJob $dbBatchJob
	 * @param kImportJobData $data
	 * @return BatchJob
	 */
	public static function onImportJobUpdated(BatchJob $dbBatchJob, kImportJobData $data)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return self::onImportJobFinished($dbBatchJob, $data);
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return self::onImportJobFailed($dbBatchJob, $data);
			default:
				return $dbBatchJob;
		}
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionUpdateJobData $data
	 * @return BatchJob
	 */
	public static function onDistributionDisableJobUpdated(BatchJob $dbBatchJob,kDistributionUpdateJobData $data)
	{
		if($data->getResults() || $data->getSentData())
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
			$dbBatchJob->setData($data);
			$dbBatchJob->save();
		}
		
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return self::onDistributionDisableJobPending($dbBatchJob, $data);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return self::onDistributionDisableJobFinished($dbBatchJob, $data);
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return self::onDistributionDisableJobFailed($dbBatchJob, $data);
			default:
				return $dbBatchJob;
		}
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionDeleteJobData $data
	 * @return BatchJob
	 */
	public static function onDistributionDeleteJobUpdated(BatchJob $dbBatchJob, kDistributionDeleteJobData $data)
	{
		if($data->getResults() || $data->getSentData())
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
			$dbBatchJob->setData($data);
			$dbBatchJob->save();
		}
		
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return self::onDistributionDeleteJobPending($dbBatchJob, $data);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return self::onDistributionDeleteJobFinished($dbBatchJob, $data);
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return self::onDistributionDeleteJobFailed($dbBatchJob, $data);
			default:
				return $dbBatchJob;
		}
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionFetchReportJobData $data
	 * @return BatchJob
	 */
	public static function onDistributionFetchReportJobUpdated(BatchJob $dbBatchJob, kDistributionFetchReportJobData $data)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return self::onDistributionFetchReportJobFinished($dbBatchJob, $data);
			default:
				return $dbBatchJob;
		}
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionSubmitJobData $data
	 * @return BatchJob
	 */
	public static function onDistributionSubmitJobPending(BatchJob $dbBatchJob, kDistributionSubmitJobData $data)
	{
		if($data->getProviderType() == DistributionProviderType::SYNDICATION)
		{
			$dbBatchJob = kJobsManager::updateBatchJob($dbBatchJob, BatchJob::BATCHJOB_STATUS_FINISHED);
		}
		
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionUpdateJobData $data
	 * @return BatchJob
	 */
	public static function onDistributionUpdateJobPending(BatchJob $dbBatchJob, kDistributionUpdateJobData $data)
	{
		if($data->getProviderType() == DistributionProviderType::SYNDICATION)
		{
			$dbBatchJob = kJobsManager::updateBatchJob($dbBatchJob, BatchJob::BATCHJOB_STATUS_FINISHED);
		}
		
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionUpdateJobData $data
	 * @return BatchJob
	 */
	public static function onDistributionEnableJobPending(BatchJob $dbBatchJob, kDistributionUpdateJobData $data)
	{
		if($data->getProviderType() == DistributionProviderType::SYNDICATION)
		{
			$dbBatchJob = kJobsManager::updateBatchJob($dbBatchJob, BatchJob::BATCHJOB_STATUS_FINISHED);
		}
		
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionUpdateJobData $data
	 * @return BatchJob
	 */
	public static function onDistributionDisableJobPending(BatchJob $dbBatchJob, kDistributionUpdateJobData $data)
	{
		if($data->getProviderType() == DistributionProviderType::SYNDICATION)
		{
			$dbBatchJob = kJobsManager::updateBatchJob($dbBatchJob, BatchJob::BATCHJOB_STATUS_FINISHED);
		}
		
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionDeleteJobData $data
	 * @return BatchJob
	 */
	public static function onDistributionDeleteJobPending(BatchJob $dbBatchJob, kDistributionDeleteJobData $data)
	{
		if($data->getProviderType() == DistributionProviderType::SYNDICATION)
		{
			$dbBatchJob = kJobsManager::updateBatchJob($dbBatchJob, BatchJob::BATCHJOB_STATUS_FINISHED);
		}
		
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionSubmitJobData $data
	 * @return BatchJob
	 */
	public static function onDistributionSubmitJobFinished(BatchJob $dbBatchJob, kDistributionSubmitJobData $data)
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
		if(!$distributionProvider->isScheduleUpdateEnabled())
		{
			if($entryDistribution->getSunStatus() == EntryDistributionSunStatus::BEFORE_SUNRISE)
			{
				if($distributionProvider->isAvailabilityUpdateEnabled())
					$entryDistribution->setDirtyStatus(EntryDistributionDirtyStatus::ENABLE_REQUIRED);
			}
			elseif($entryDistribution->getSunset(null) > 0)
			{
				if($distributionProvider->isAvailabilityUpdateEnabled())
					$entryDistribution->setDirtyStatus(EntryDistributionDirtyStatus::DISABLE_REQUIRED);
				else
					$entryDistribution->setDirtyStatus(EntryDistributionDirtyStatus::DELETE_REQUIRED);
			}
		}
			
		$entryDistribution->save();
		
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionUpdateJobData $data
	 * @return BatchJob
	 */
	public static function onDistributionUpdateJobFinished(BatchJob $dbBatchJob, kDistributionUpdateJobData $data)
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
		if(!$distributionProvider->isScheduleUpdateEnabled())
		{
			if($entryDistribution->getSunStatus() == EntryDistributionSunStatus::BEFORE_SUNRISE)
			{
				if($distributionProvider->isAvailabilityUpdateEnabled())
					$entryDistribution->setDirtyStatus(EntryDistributionDirtyStatus::ENABLE_REQUIRED);
			}
			elseif($entryDistribution->getSunset(null) > 0)
			{
				if($distributionProvider->isAvailabilityUpdateEnabled())
					$entryDistribution->setDirtyStatus(EntryDistributionDirtyStatus::DISABLE_REQUIRED);
				else
					$entryDistribution->setDirtyStatus(EntryDistributionDirtyStatus::DELETE_REQUIRED);
			}
		}
			
		$entryDistribution->save();
		
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionUpdateJobData $data
	 * @return BatchJob
	 */
	public static function onDistributionEnableJobFinished(BatchJob $dbBatchJob, kDistributionUpdateJobData $data)
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
		if(!$distributionProvider->isScheduleUpdateEnabled())
		{
			if($entryDistribution->getSunStatus() == EntryDistributionSunStatus::BEFORE_SUNRISE)
			{
				if($distributionProvider->isAvailabilityUpdateEnabled())
					$entryDistribution->setDirtyStatus(EntryDistributionDirtyStatus::ENABLE_REQUIRED);
			}
			elseif($entryDistribution->getSunset(null) > 0)
			{
				if($distributionProvider->isAvailabilityUpdateEnabled())
					$entryDistribution->setDirtyStatus(EntryDistributionDirtyStatus::DISABLE_REQUIRED);
				else
					$entryDistribution->setDirtyStatus(EntryDistributionDirtyStatus::DELETE_REQUIRED);
			}
		}
			
		$entryDistribution->save();
		
		return $dbBatchJob;
	}

	
	/**
	 * @param BatchJob $dbBatchJob
	 * @param kImportJobData $data
	 * @return BatchJob
	 */
	public static function onImportJobFinished(BatchJob $dbBatchJob, kImportJobData $data)
	{
		$statuses = array(
			EntryDistributionStatus::IMPORT_SUBMITTING,
			EntryDistributionStatus::IMPORT_UPDATING,
		);
		
		$entryDistributions = EntryDistributionPeer::retrieveByEntryAndStatuses($dbBatchJob->getEntryId(), $statuses);
		foreach($entryDistributions as $entryDistribution)
		{
			/* @var $entryDistribution EntryDistribution */
			
			$distributionProfile = DistributionProfilePeer::retrieveByPK($entryDistribution->getDistributionProfileId());
			
			if($entryDistribution->getStatus() == EntryDistributionStatus::IMPORT_SUBMITTING)
			{
				KalturaLog::notice("Submitting add entry distribution [" . $entryDistribution->getId() . "]");
				kContentDistributionManager::submitAddEntryDistribution($entryDistribution, $distributionProfile, true);
			}
			elseif($entryDistribution->getStatus() == EntryDistributionStatus::IMPORT_UPDATING)
			{
				KalturaLog::notice("Submitting update entry distribution [" . $entryDistribution->getId() . "]");
				kContentDistributionManager::submitUpdateEntryDistribution($entryDistribution, $distributionProfile);	
			}
		}
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kImportJobData $data
	 * @return BatchJob
	 */
	public static function onImportJobFailed(BatchJob $dbBatchJob, kImportJobData $data)
	{
		$statuses = array(
			EntryDistributionStatus::IMPORT_SUBMITTING,
			EntryDistributionStatus::IMPORT_UPDATING,
		);
		
		$entryDistributions = EntryDistributionPeer::retrieveByEntryAndStatuses($dbBatchJob->getEntryId(), $statuses);
		foreach($entryDistributions as $entryDistribution)
		{
			/* @var $entryDistribution EntryDistribution */
			
			if($entryDistribution->getStatus() == EntryDistributionStatus::IMPORT_SUBMITTING)
				$entryDistribution->setStatus(EntryDistributionStatus::ERROR_SUBMITTING);
			elseif($entryDistribution->getStatus() == EntryDistributionStatus::IMPORT_UPDATING)
				$entryDistribution->setStatus(EntryDistributionStatus::ERROR_UPDATING);
				
			$entryDistribution->setErrorType($dbBatchJob->getErrType());
			$entryDistribution->setErrorNumber($dbBatchJob->getErrNumber());
			$entryDistribution->setErrorDescription($dbBatchJob->getMessage());
			
			$entryDistribution->setDirtyStatus(null);
			$entryDistribution->save();
		}
	}
	
	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionUpdateJobData $data
	 * @return BatchJob
	 */
	public static function onDistributionDisableJobFinished(BatchJob $dbBatchJob, kDistributionUpdateJobData $data)
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
			
		$entryDistribution->save();
		
		return $dbBatchJob;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionDeleteJobData $data
	 * @return BatchJob
	 */
	public static function onDistributionDeleteJobFinished(BatchJob $dbBatchJob, kDistributionDeleteJobData $data)
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
	 * @return BatchJob
	 */
	public static function onDistributionFetchReportJobFinished(BatchJob $dbBatchJob, kDistributionFetchReportJobData $data)
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
	 * @return BatchJob
	 */
	public static function onDistributionSubmitJobFailed(BatchJob $dbBatchJob, kDistributionSubmitJobData $data)
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
	 * @return BatchJob
	 */
	public static function onDistributionUpdateJobFailed(BatchJob $dbBatchJob, kDistributionUpdateJobData $data)
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
	 * @param kDistributionUpdateJobData $data
	 * @return BatchJob
	 */
	public static function onDistributionEnableJobFailed(BatchJob $dbBatchJob, kDistributionUpdateJobData $data)
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
	 * @param kDistributionUpdateJobData $data
	 * @return BatchJob
	 */
	public static function onDistributionDisableJobFailed(BatchJob $dbBatchJob, kDistributionUpdateJobData $data)
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
	 * @return BatchJob
	 */
	public static function onDistributionDeleteJobFailed(BatchJob $dbBatchJob, kDistributionDeleteJobData $data)
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
			
		if($metadata->getObjectType() != MetadataObjectType::ENTRY)
			return true;
		
		KalturaLog::log("Metadata [" . $metadata->getId() . "] for entry [" . $metadata->getObjectId() . "] deleted");
		
		$entry = entryPeer::retrieveByPK($metadata->getObjectId());
		if (!$entry){
			KalturaLog::debug("Entry [".$metadata->getObjectId()."] not found");
			return true; 
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
				self::assignAssetsAndValidateForSubmission($entryDistribution, $entry, $distributionProfile, DistributionAction::SUBMIT);
				
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
//					continue;
				}
				
				$distributionProvider = $distributionProfile->getProvider();
				if(!$distributionProvider->isUpdateEnabled())
				{
					KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] provider [" . $distributionProvider->getName() . "] does not support update");
					continue;
				}
				
				if($distributionProfile->getUpdateEnabled() == DistributionProfileActionStatus::DISABLED)
				{
					KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] profile id  [" . $distributionProfile->getId() . "] update not enabled");
					continue;
				}
				
				self::assignAssetsAndValidateForSubmission($entryDistribution, $entry, $distributionProfile, DistributionAction::UPDATE);
				$validationErrors = $entryDistribution->getValidationErrors();
				
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
			
		if($metadata->getObjectType() != MetadataObjectType::ENTRY)
			return true;
		
		KalturaLog::log("Metadata [" . $metadata->getId() . "] for entry [" . $metadata->getObjectId() . "] changed");
		
		$syncKey = $metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
		$xmlPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
		if(!$xmlPath)
		{
			KalturaLog::log("Entry metadata xml not found");
			return true;
		}
		$xml = new KDOMDocument();
		$xml->load($xmlPath);
		
		$previousXml = null;
		if($previousVersion)
		{
			$syncKey = $metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA, $previousVersion);
			$xmlPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
			if($xmlPath)
			{
				$previousXml = new KDOMDocument();
				$previousXml->load($xmlPath);
			}
			else 
			{
				KalturaLog::log("Entry metadata previous version xml not found");
			}
		}
		
		$entry = entryPeer::retrieveByPK($metadata->getObjectId());
		if (!$entry){
			KalturaLog::debug("Entry [".$metadata->getObjectId()."] not found");
			return true; 
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
				self::assignAssetsAndValidateForSubmission($entryDistribution, $entry, $distributionProfile, DistributionAction::SUBMIT);
				
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
//					continue;
				}
				
				if(!$distributionProvider->isUpdateEnabled())
				{
					KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] provider [" . $distributionProvider->getName() . "] does not support update");
					continue;
				}
				
				if($distributionProfile->getUpdateEnabled() == DistributionProfileActionStatus::DISABLED)
				{
					KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] profile id  [" . $distributionProfile->getId() . "] update not enabled");
					continue;
				}

				$updateRequiredMetadataXPaths = $distributionProvider->getUpdateRequiredMetadataXPaths($distributionProfileId);
				$updateRequired = false;
				
				foreach($updateRequiredMetadataXPaths as $updateRequiredMetadataXPath)
				{
					$xPath = new DOMXPath($xml);
					$newElements = $xPath->query($updateRequiredMetadataXPath);
					
					$oldElements = null;
					if($previousXml)
					{
						$xPath = new DOMXPath($previousXml);
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
				
				self::assignAssetsAndValidateForSubmission($entryDistribution, $entry, $distributionProfile, DistributionAction::UPDATE);
				$validationErrors = $entryDistribution->getValidationErrors();
				
				if(!$updateRequired)
				{
					KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] update not required");
					continue;	
				}				
				
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
		$distributionProfileId = $entryDistribution->getDistributionProfileId();
		$distributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if(!$distributionProfile)
			return true;
		
		$distributionProvider = $distributionProfile->getProvider();
		if(!$distributionProvider)
		{
			KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] provider not found");
			return true;
		}
		
		if(!$distributionProvider->isUpdateEnabled())
		{
			KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] provider [" . $distributionProvider->getName() . "] does not support update");
			return true;
		}
		
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

		if($distributionProfile->getUpdateEnabled() == DistributionProfileActionStatus::DISABLED)
		{
			KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] profile id  [" . $distributionProfile->getId() . "] update not enabled");
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
			EntryDistributionPeer::ASSET_IDS,
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
			
		$entryDistributions = EntryDistributionPeer::retrieveByEntryId($entry->getId());
		foreach($entryDistributions as $entryDistribution)
		{
			$distributionProfileId = $entryDistribution->getDistributionProfileId();
			$distributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
			if(!$distributionProfile)
			{
				KalturaLog::err("Entry distribution [" . $entryDistribution->getId() . "] profile [$distributionProfileId] not found");
				continue;
			}
			
			if (in_array(entryPeer::START_DATE, $modifiedColumns) || in_array(entryPeer::END_DATE, $modifiedColumns))
			{
				$entryDistribution->setUpdatedAt(time());
				$entryDistribution->save();
			}
			
			switch($entryDistribution->getStatus())
			{
				case EntryDistributionStatus::DELETED:
				case EntryDistributionStatus::DELETING:
				case EntryDistributionStatus::REMOVED:
				
					KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] status [" . $entryDistribution->getStatus() . "] no update required");
					continue;
				
				case EntryDistributionStatus::PENDING:
				case EntryDistributionStatus::ERROR_SUBMITTING:	
				
					self::assignAssetsAndValidateForSubmission($entryDistribution, $entry, $distributionProfile, DistributionAction::SUBMIT);
					$validationErrors = $entryDistribution->getValidationErrors();
					
					KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] validation errors [" . print_r($validationErrors, true) . "]");
					if (count($distributionProfile->getAutoCreateFlavorsArray()) || count($distributionProfile->getAutoCreateThumbArray()) )
					{
					    self::submitAddEntryDistribution($entryDistribution, $distributionProfile);
					}
					
					break;
					
				case EntryDistributionStatus::QUEUED:
				
					self::assignAssetsAndValidateForSubmission($entryDistribution, $entry, $distributionProfile, DistributionAction::SUBMIT);
					$validationErrors = $entryDistribution->getValidationErrors();
					
					KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] validation errors [" . print_r($validationErrors, true) . "]");
					
					if(!count($validationErrors) && $entry->getStatus() == entryStatus::READY)
						self::submitAddEntryDistribution($entryDistribution, $distributionProfile);
					break;
				
				default:
				
					if($entryDistribution->getDirtyStatus() == EntryDistributionDirtyStatus::UPDATE_REQUIRED || $entryDistribution->getDirtyStatus() == EntryDistributionDirtyStatus::SUBMIT_REQUIRED)
					{
						KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] already flaged for updating");
//						continue;
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
					
					if($distributionProfile->getUpdateEnabled() == DistributionProfileActionStatus::DISABLED)
					{
						KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] profile id  [" . $distributionProfile->getId() . "] update not enabled");
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
				
					self::assignAssetsAndValidateForSubmission($entryDistribution, $entry, $distributionProfile, DistributionAction::SUBMIT);
					
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
					
					KalturaLog::log("Updating entry distribution [" . $entryDistribution->getId() . "]");
					self::submitUpdateEntryDistribution($entryDistribution, $distributionProfile);
			}
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
			
		if($feed->getDisplayInSearch() == mySearchUtils::DISPLAY_IN_SEARCH_SYSTEM)
		{
			$feed->setStatus(syndicationFeed::SYNDICATION_DELETED);
			$feed->save();
		}
		
		// deletes the playlist
		$playlist = entryPeer::retrieveByPK($feed->getPlaylistId());
		if($playlist && $playlist->getDisplayInSearch() == mySearchUtils::DISPLAY_IN_SEARCH_SYSTEM)
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
		{
			KalturaLog::log("Partner [ . $asset->getPartnerId() . ] is not allowed");
			return true;
		}
			
		$entry = $asset->getentry();
		if(!$entry)
		{
			KalturaLog::log("Entry [ . $asset->getEntryId() . ] not found");
			return true;
		}
			
		$entryDistributions = EntryDistributionPeer::retrieveByEntryId($asset->getEntryId());
		KalturaLog::log("Entry distributions [" . count($entryDistributions) . "] found");
		
		foreach($entryDistributions as $entryDistribution)
		{
			$distributionProfileId = $entryDistribution->getDistributionProfileId();
			$distributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
			if(!$distributionProfile)
			{
				KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] profile [" . $entryDistribution->getDistributionProfileId() . "] not found");
				continue;
			}
			
			$distributionProvider = $distributionProfile->getProvider();
			if(!$distributionProvider)
			{
				KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] provider not found");
				continue;
			}
			
			if(!$distributionProvider->isUpdateEnabled() || !$distributionProvider->isMediaUpdateEnabled())
			{
				KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] provider [" . $distributionProvider->getName() . "] does not support update");
				continue;
			}
			
			self::onEntryDistributionUpdateRequired($entryDistribution);
		}
		
		return true;
	}
	
	/**
	 * @param asset $asset
	 */
	public static function onAssetReadyOrDeleted(asset $asset)
	{
		if(!ContentDistributionPlugin::isAllowedPartner($asset->getPartnerId()))
		{
			KalturaLog::log("Partner [" . $asset->getPartnerId() . "] is not allowed");
			return true;
		}
			
		$entry = $asset->getentry();
		if(!$entry)
		{
			KalturaLog::log("Entry [" . $asset->getEntryId() . "] not found");
			return true;
		}
			
		$entryDistributions = EntryDistributionPeer::retrieveByEntryId($asset->getEntryId());
		foreach($entryDistributions as $entryDistribution)
		{
			$distributionProfileId = $entryDistribution->getDistributionProfileId();
			$distributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
			if(!$distributionProfile)
			{
				KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] profile [$distributionProfileId] not found");
				continue;
			}
				
			if($entryDistribution->getStatus() == EntryDistributionStatus::QUEUED || $entryDistribution->getStatus() == EntryDistributionStatus::PENDING)
			{
				$listChanged = self::assignAssetsAndValidateForSubmission($entryDistribution, $entry, $distributionProfile, DistributionAction::SUBMIT);
				if (!$listChanged){
					continue;
				}
				
				if($entryDistribution->getStatus() == EntryDistributionStatus::QUEUED)
				{
					if($entryDistribution->getDirtyStatus() != EntryDistributionDirtyStatus::SUBMIT_REQUIRED)
						self::submitAddEntryDistribution($entryDistribution, $distributionProfile);
				}
			} // submit
			
			if($entryDistribution->getStatus() == EntryDistributionStatus::READY || $entryDistribution->getStatus() == EntryDistributionStatus::ERROR_UPDATING)
			{
				$distributionProvider = $distributionProfile->getProvider();
				if(!$distributionProvider)
				{
					KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] provider not found");
					continue;
				}
				
				if(!$distributionProvider->isUpdateEnabled() || !$distributionProvider->isMediaUpdateEnabled())
				{
					KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] provider [" . $distributionProvider->getName() . "] does not support update");
					continue;
				}
				
				if($distributionProfile->getUpdateEnabled() == DistributionProfileActionStatus::DISABLED)
				{
					KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] profile id  [" . $distributionProfile->getId() . "] update not enabled");
					continue;
				}
				
				$listChanged = self::assignAssetsAndValidateForSubmission($entryDistribution, $entry, $distributionProfile, DistributionAction::UPDATE);
				if (!$listChanged){
					continue;
				}
				$validationErrors = $entryDistribution->getValidationErrors();
				
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
	
	protected static function assignAssetsAndValidateForSubmission(EntryDistribution $entryDistribution, entry $entry, DistributionProfile $distributionProfile, $action){
		$listChanged = kContentDistributionManager::assignFlavorAssets($entryDistribution, $entry, $distributionProfile);
		$listChanged = ($listChanged | kContentDistributionManager::assignThumbAssets($entryDistribution, $entry, $distributionProfile));
		$listChanged = ($listChanged | kContentDistributionManager::assignAssets($entryDistribution, $entry, $distributionProfile));
		
		if(!$listChanged)
		{
			KalturaLog::log("Entry distribution [" . $entryDistribution->getId() . "] asset lists didn't change");
		}
		
		$validationErrors = $distributionProfile->validateForSubmission($entryDistribution, $action);
		$entryDistribution->setValidationErrorsArray($validationErrors);
		$entryDistribution->save();
		
		return $listChanged;
	}
}