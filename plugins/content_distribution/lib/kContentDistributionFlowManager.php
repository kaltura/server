<?php
class kContentDistributionFlowManager extends kContentDistributionManager implements kObjectChangedEventConsumer, kBatchJobStatusEventConsumer, kObjectDeletedEventConsumer, kObjectDataChangedEventConsumer
{
	/**
	 * @param BaseObject $object
	 * @param array $modifiedColumns
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		// Content distribution is not supporting partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return true;
		
		if($object instanceof entry && $object->getStatus() == entryStatus::READY)
		{
			if(isset($modifiedColumns[entryPeer::STATUS]))
				return self::onEntryReady($object);
			else
				return self::onEntryChanged($object, $modifiedColumns);
		}
		
		if($object instanceof asset && isset($modifiedColumns[assetPeer::STATUS]) && $object->getStatus() == asset::FLAVOR_ASSET_STATUS_READY)
			return self::onAssetReady($object);
		
		if($object instanceof EntryDistribution)
			return self::onEntryDistributionChanged($object, $modifiedColumns);
		
		return true;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param BatchJob $twinJob
	 */
	public function updatedJob(BatchJob $dbBatchJob, BatchJob $twinJob = null)
	{
		// Content distribution is not supporting partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return true;
		
		if($dbBatchJob->getJobType() == ContentDistributionBatchJobType::get()->coreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT))
			self::onDistributionSubmitJobUpdated($dbBatchJob, $dbBatchJob->getData(), $twinJob);
		
		if($dbBatchJob->getJobType() == ContentDistributionBatchJobType::get()->coreValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE))
			self::onDistributionUpdateJobUpdated($dbBatchJob, $dbBatchJob->getData(), $twinJob);
		
		if($dbBatchJob->getJobType() == ContentDistributionBatchJobType::get()->coreValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE))
			self::onDistributionDeleteJobUpdated($dbBatchJob, $dbBatchJob->getData(), $twinJob);
		
		if($dbBatchJob->getJobType() == ContentDistributionBatchJobType::get()->coreValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT))
			self::onDistributionFetchReportJobUpdated($dbBatchJob, $dbBatchJob->getData(), $twinJob);
		
		return true;
	}
	
	/**
	 * @param BaseObject $object
	 * @return bool true if should continue to the next consumer
	 */
	public function objectDeleted(BaseObject $object)
	{
		// Content distribution is not supporting partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return true;
		
		if($object instanceof entry)
			return self::onEntryDeleted($object);
		
		if($object instanceof GenericDistributionProvider)
			return self::onGenericDistributionProviderDeleted($object);
			
		return true;
	}

	/**
	 * @param BaseObject $object
	 * @param string $previousVersion
	 * @return bool true if should continue to the next consumer
	 */
	public function objectDataChanged(BaseObject $object, $previousVersion = null)
	{
		// Content distribution is not supporting partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return true;
		
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
		$entryDistribution->setRemoteId($data->getRemoteId());
		$entryDistribution->setStatus(EntryDistributionStatus::READY);
		$entryDistribution->setDirtyStatus(null);
	
		$distributionProfileId = $entryDistribution->getDistributionProfileId();
		$distributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if(!$distributionProfile)
		{
			KalturaLog::debug("Entry distribution [" . $entryDistribution->getId() . "] profile [$distributionProfileId] not found");
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
		$entryDistribution->setStatus(EntryDistributionStatus::READY);
		$entryDistribution->setDirtyStatus(null);
	
		$distributionProfileId = $entryDistribution->getDistributionProfileId();
		$distributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if(!$distributionProfile)
		{
			KalturaLog::debug("Entry distribution [" . $entryDistribution->getId() . "] profile [$distributionProfileId] not found");
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
		$entryDistribution->setStatus(EntryDistributionStatus::DELETED);
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
		$entryDistribution->setStatus(EntryDistributionStatus::ERROR_DELETING);
		$entryDistribution->setDirtyStatus(null);
		$entryDistribution->save();
		
		return $dbBatchJob;
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
		
		KalturaLog::debug("Metadata [" . $metadata->getId() . "] for entry [" . $metadata->getObjectId() . "] changed");
		
		$syncKey = $metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
		$xmlPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
		if(!$xmlPath)
		{
			KalturaLog::debug("Entry metadata xml not found");
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
				KalturaLog::debug("Entry metadata previous version xml not found");
			}
		}
		
		$entryDistributions = EntryDistributionPeer::retrieveByEntryId($metadata->getObjectId());
		foreach($entryDistributions as $entryDistribution)
		{
			if(!$previousXml && $entryDistribution->getStatus() != EntryDistributionStatus::QUEUED)
				continue;
				
			if($entryDistribution->getDirtyStatus() == EntryDistributionDirtyStatus::UPDATE_REQUIRED)
			{
				KalturaLog::debug("Entry distribution [" . $entryDistribution->getId() . "] already flaged for updating");
				continue;
			}
				
			$distributionProfileId = $entryDistribution->getDistributionProfileId();
			$distributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
			if(!$distributionProfile)
			{
				KalturaLog::debug("Entry distribution [" . $entryDistribution->getId() . "] profile [$distributionProfileId] not found");
				continue;
			}

			$distributionProvider = $distributionProfile->getProvider();
			if(!$distributionProvider)
			{
				KalturaLog::debug("Entry distribution [" . $entryDistribution->getId() . "] provider [" . $distributionProfile->getProviderType() . "] not found");
				continue;
			}
			
			$updateRequiredMetadataXPaths = $distributionProvider->getUpdateRequiredMetadataXPaths();
			$updateRequired = false;
			
			foreach($updateRequiredMetadataXPaths as $updateRequiredMetadataXPath)
			{
				KalturaLog::debug("Query xPath [$updateRequiredMetadataXPath] on metadata xml");
				
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
					
				if(is_null($newElements) || is_null($oldElements))
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
				KalturaLog::debug("Entry distribution [" . $entryDistribution->getId() . "] update not required");
				continue;	
			}
			
			if($distributionProfile->getUpdateEnabled() != DistributionProfileActionStatus::AUTOMATIC)
			{
				KalturaLog::debug("Entry distribution [" . $entryDistribution->getId() . "] should not be updated automatically");
				$entryDistribution->setDirtyStatus(EntryDistributionDirtyStatus::UPDATE_REQUIRED);
				$entryDistribution->save();
				continue;
			}
			
			self::submitUpdateEntryDistribution($entryDistribution, $distributionProfile);
		}
		
		return true;
	}
	
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param array $modifiedColumns
	 */
	public static function onEntryDistributionChanged(EntryDistribution $entryDistribution, array $modifiedColumns)
	{
		$entry = entryPeer::retrieveByPK($entryDistribution->getEntryId());
		if($entry) // updated in the indexing server (sphinx)
			kEventsManager::raiseEvent(new kObjectUpdatedEvent($entry));
			
		// updated in the indexing server (sphinx)
		kEventsManager::raiseEvent(new kObjectUpdatedEvent($entryDistribution));
			
		if(!isset($modifiedColumns[EntryDistributionPeer::SUNRISE]) && !isset($modifiedColumns[EntryDistributionPeer::SUNSET]))
			return true;
			
		KalturaLog::debug("Entry distribution [" . $entryDistribution->getId() . "] of entry [" . $entryDistribution->getEntryId() . "] schedule changed");
		
		if($entryDistribution->getDirtyStatus() == EntryDistributionDirtyStatus::UPDATE_REQUIRED)
		{			
			KalturaLog::debug("Entry distribution [" . $entryDistribution->getId() . "] already requires update");
			return true;
		}
			
		$distributionProfileId = $entryDistribution->getDistributionProfileId();
		$distributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if(!$distributionProfile)
		{
			KalturaLog::debug("Entry distribution [" . $entryDistribution->getId() . "] profile [$distributionProfileId] not found");
			continue;
		}

		if($distributionProfile->getUpdateEnabled() != DistributionProfileActionStatus::AUTOMATIC)
		{
			KalturaLog::debug("Entry distribution [" . $entryDistribution->getId() . "] should not be updated automatically");
			$entryDistribution->setDirtyStatus(EntryDistributionDirtyStatus::UPDATE_REQUIRED);
			$entryDistribution->save();
			continue;
		}
		
		self::submitUpdateEntryDistribution($entryDistribution, $distributionProfile);
	}
	
	/**
	 * @param entry $entry
	 * @param array $modifiedColumns
	 */
	public static function onEntryChanged(entry $entry, array $modifiedColumns)
	{
		if(!ContentDistributionPlugin::isAllowedPartner($entry->getPartnerId()))
			return true;
			
		KalturaLog::debug("Entry [" . $entry->getId() . "] changed");
		$entryDistributions = EntryDistributionPeer::retrieveByEntryId($entry->getId());
		foreach($entryDistributions as $entryDistribution)
		{
			if($entryDistribution->getDirtyStatus() == EntryDistributionDirtyStatus::UPDATE_REQUIRED)
			{
				KalturaLog::debug("Entry distribution [" . $entryDistribution->getId() . "] already flaged for updating");
				continue;
			}
				
			$distributionProfileId = $entryDistribution->getDistributionProfileId();
			$distributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
			if(!$distributionProfile)
			{
				KalturaLog::debug("Entry distribution [" . $entryDistribution->getId() . "] profile [$distributionProfileId] not found");
				continue;
			}

			$distributionProvider = $distributionProfile->getProvider();
			if(!$distributionProvider)
			{
				KalturaLog::debug("Entry distribution [" . $entryDistribution->getId() . "] provider [" . $distributionProfile->getProviderType() . "] not found");
				continue;
			}
			
			$updateRequiredEntryFields = $distributionProvider->getUpdateRequiredEntryFields();
			$updateRequired = false;
			
			foreach($updateRequiredEntryFields as $updateRequiredEntryField)
			{
				if(isset($modifiedColumns[$updateRequiredEntryField]))
				{
					$updateRequired = true;
					break;
				}
			}
			
			if(!$updateRequired)
			{
				KalturaLog::debug("Entry distribution [" . $entryDistribution->getId() . "] update not required");
				continue;	
			}
			
			if($distributionProfile->getUpdateEnabled() != DistributionProfileActionStatus::AUTOMATIC)
			{
				KalturaLog::debug("Entry distribution [" . $entryDistribution->getId() . "] should not be updated automatically");
				$entryDistribution->setDirtyStatus(EntryDistributionDirtyStatus::UPDATE_REQUIRED);
				$entryDistribution->save();
				continue;
			}
			
			self::submitUpdateEntryDistribution($entryDistribution, $distributionProfile);
		}
		
		return true;
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
			
		KalturaLog::debug("Entry [" . $entry->getId() . "] deleted");
		$entryDistributions = EntryDistributionPeer::retrieveByEntryId($entry->getId());
		foreach($entryDistributions as $entryDistribution)
		{
			if($entryDistribution->getStatus() == EntryDistributionStatus::DELETING)
			{
				KalturaLog::debug("Entry distribution [" . $entryDistribution->getId() . "] already deleting");
				continue;
			}
				
			if($entryDistribution->getDirtyStatus() == EntryDistributionDirtyStatus::DELETE_REQUIRED)
			{
				KalturaLog::debug("Entry distribution [" . $entryDistribution->getId() . "] already flaged for deletion");
				continue;
			}
				
			$distributionProfileId = $entryDistribution->getDistributionProfileId();
			$distributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
			if(!$distributionProfile || $distributionProfile->getDeleteEnabled() != DistributionProfileActionStatus::AUTOMATIC)
			{
				KalturaLog::debug("Entry distribution [" . $entryDistribution->getId() . "] should not be deleted automatically");
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
			
		KalturaLog::debug("Entry [" . $entry->getId() . "] ready");
		$distributionProfiles = DistributionProfilePeer::retrieveByPartnerId($entry->getPartnerId());
		foreach($distributionProfiles as $distributionProfile)
			if($distributionProfile->getSubmitEnabled() == DistributionProfileActionStatus::AUTOMATIC)
				self::addEntryDistribution($entry, $distributionProfile, true);
		
		return true;
	}

	/**
	 * @param asset $asset
	 */
	public static function onAssetReady(asset $asset)
	{
		if(!ContentDistributionPlugin::isAllowedPartner($asset->getPartnerId()))
			return true;
			
		KalturaLog::debug("Asset [" . $asset->getId() . "] of entry [" . $asset->getEntryId() . "] ready");
		$entryDistributions = EntryDistributionPeer::retrieveByEntryId($asset->getEntryId());
		foreach($entryDistributions as $entryDistribution)
		{
			if($entryDistribution->getStatus() != EntryDistributionStatus::QUEUED)
				continue;
				
			if($entryDistribution->getDirtyStatus() != EntryDistributionDirtyStatus::SUBMIT_REQUIRED)
				continue;
				
			$distributionProfileId = $entryDistribution->getDistributionProfileId();
			$distributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
			if(!$distributionProfile)
				continue;
				
			$validationErrors = $distributionProfile->validateForSubmission($entryDistribution, DistributionAction::SUBMIT);
			$entryDistribution->setValidationErrorsArray($validationErrors);
			$entryDistribution->save();
			
			if(count($validationErrors))
				KalturaLog::debug("Validation errors [" . print_r($validationErrors, true) . "]");
				
			self::submitAddEntryDistribution($entryDistribution, $distributionProfile);
		}
		
		return true;
	}
}