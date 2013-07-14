<?php
/**
 * @package plugins.avnDistribution
 * @subpackage lib
 */
class kAvnFlowManager implements kBatchJobStatusEventConsumer, kObjectChangedEventConsumer, kObjectCreatedEventConsumer, kObjectDeletedEventConsumer
{
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
		
		return false;
	}
	
	public function updatedJob(BatchJob $dbBatchJob)
	{
		$data = $dbBatchJob->getData();
		if (!$data instanceof kDistributionJobData)
			return true;
			
		$avnCoreValueType = kPluginableEnumsManager::apiToCore('DistributionProviderType', AvnDistributionPlugin::getApiValue(AvnDistributionProviderType::AVN));
		if ($data->getProviderType() != $avnCoreValueType)
			return true;
			
		if ($dbBatchJob->getStatus() != BatchJob::BATCHJOB_STATUS_PENDING)
			return true;
			
		$jobTypesToFinish = array(
			ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT),
			ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE),
			ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE),
			ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT),
			ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_ENABLE),
			ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DISABLE)
		);
		
		if (in_array($dbBatchJob->getJobType(), $jobTypesToFinish))
			kJobsManager::updateBatchJob($dbBatchJob, BatchJob::BATCHJOB_STATUS_FINISHED);
		
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if($object instanceof thumbAsset && $object->getStatus() == asset::FLAVOR_ASSET_STATUS_READY && in_array(assetPeer::STATUS, $modifiedColumns))
			return true;
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		return self::onAssetReadyOrDeleted($object);
	}
	
	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::shouldConsumeCreatedEvent()
	 */
	public function shouldConsumeCreatedEvent(BaseObject $object)
	{
		if($object instanceof thumbAsset && $object->getStatus() == asset::FLAVOR_ASSET_STATUS_READY)
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
	 * @see kObjectDeletedEventConsumer::shouldConsumeDeletedEvent()
	 */
	public function shouldConsumeDeletedEvent(BaseObject $object)
	{
		if($object instanceof thumbAsset)
			return true;
			
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::objectDeleted()
	 */
	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null)
	{
		return self::onAssetReadyOrDeleted($object);
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
			
			$validateStatuses = array(
				EntryDistributionStatus::QUEUED, 
				EntryDistributionStatus::PENDING,
				EntryDistributionStatus::READY
			);
			
			if (!in_array($entryDistribution->getStatus(), $validateStatuses))
				continue;
			
			/* 
			 * we have special thumbnail definition for 'main menu' & 'thank you' entries 
			 * so we need to revalidate avn distribution profile because those thumbnails are not 
			 * defined in the distribution porofile so automatic revalidation doesn't work
			 */
			if ($distributionProfile instanceof AvnDistributionProfile) 
			{
				$validationErrors = $distributionProfile->validateForSubmission($entryDistribution, DistributionAction::SUBMIT);
				$entryDistribution->setValidationErrorsArray($validationErrors);
				$entryDistribution->save();
			}
		}
		
		return true;
	}
}