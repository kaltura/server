<?php
class kWidevineEventsConsumer implements kObjectChangedEventConsumer, kObjectDeletedEventConsumer, kObjectCreatedEventConsumer
{	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns) 
	{
		try 
		{
			$c = new Criteria();
			$c->add(assetPeer::ENTRY_ID, $object->getId());		
			$flavorType = WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR);
			$c->add(assetPeer::TYPE, $flavorType);		
			$wvFlavorAssets = assetPeer::doSelect($c);
			KalturaLog::debug('Found '.count($wvFlavorAssets).' widevine flavors');	

			if(count($wvFlavorAssets))
			{
				$this->addWidevineRepositoryModifySyncJob($object->getId(), $object->getPartnerId(), $wvFlavorAssets, $this->getLicenseStartDateFromEntry($object), $this->getLicenseEndDateFromEntry($object));
			}
		}
		catch(Exception $e)
		{
			KalturaLog::err('Failed to process objectChangedEvent for entry ['.$object->getId().'] - '.$e->getMessage());
		}		
		return true;
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns) 
	{
		//TODO: check permission
		if(	$object instanceof entry && 
			(in_array(entryPeer::START_DATE, $modifiedColumns) || in_array(entryPeer::END_DATE, $modifiedColumns)) &&
			$this->shouldSyncWidevineRepositoryForPartner($object->getPartnerId())) 
			return true;
		else		
			return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::objectDeleted()
	 */
	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null) 
	{
		try 
		{
			$this->addWidevineRepositoryModifySyncJob($object->getEntryId(), $object->getPartnerId(), array($object), time(), time(), false);
		}
		catch(Exception $e)
		{
			KalturaLog::err('Failed to process objectDeleted for widevine flavor asset ['.$object->getId().'] - '.$e->getMessage());
		}
		return true;
	}

	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::shouldConsumeDeletedEvent()
	 */
	public function shouldConsumeDeletedEvent(BaseObject $object) 
	{
		if($object instanceof WidevineFlavorAsset) 
			return true;
		else		
			return false;		
	}
	
	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::objectCreated()
	 */
	public function objectCreated(BaseObject $object) 
	{
		$entry = entryPeer::retrieveByPK($object->getEntryId());
		$wvFlavorParamsOutput = assetParamsOutputPeer::retrieveByPK($object->getId());
		if($entry && $wvFlavorParamsOutput)
		{
			KalturaLog::debug('setting widevine distribution dates from entry');
			$wvFlavorParamsOutput->setWidevineDistributionStartDate($this->getLicenseStartDateFromEntry($entry));
			$wvFlavorParamsOutput->setWidevineDistributionEndDate($this->getLicenseEndDateFromEntry($entry));
			$wvFlavorParamsOutput->save();	
		}		
		return true;			
	}

	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::shouldConsumeCreatedEvent()
	 */
	public function shouldConsumeCreatedEvent(BaseObject $object) 
	{
		if(	$object instanceof flavorParamsOutputWrap && 
			$object->getType() == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR) &&
			$this->shouldSyncWidevineRepositoryForPartner($object->getPartnerId())) 
			return true;
		else		
			return false;				
	}
	
	private function addWidevineRepositoryModifySyncJob($entryId, $partnerId, array $flavorAssets, $entryStartDate, $entryEndDate, $monitorSyncCompletion = true)
	{	
		KalturaLog::debug('adding  WidevineRepositorySync job, mode = MODIFY');		
 		$batchJobType = WidevinePlugin::getCoreValue('BatchJobType', WidevineBatchJobType::WIDEVINE_REPOSITORY_SYNC);
 		
		$batchJob = new BatchJob();
		$batchJob->setPartnerId($partnerId);			
		$batchJob->setObjectId($entryId);
		$batchJob->setObjectType(BatchJobObjectType::ENTRY);
		$batchJob->setEntryId($entryId);
					
		$jobData = new kWidevineRepositorySyncJobData();
		$jobData->setSyncMode(WidevineRepositorySyncMode::MODIFY);
		$jobData->setMonitorSyncCompletion($monitorSyncCompletion);
		$wvAssetIds = array();
		foreach ($flavorAssets as $flavorAsset) 
		{
			/* @var $flavorAsset WidevineFlavorAsset */
			if($flavorAsset->getWidevineAssetId())
				$wvAssetIds[] = $flavorAsset->getWidevineAssetId();			
		}
		
		if(!count($wvAssetIds))
		{
			KalturaLog::debug("No valid WV assets found, Widevine Sync job is not created");
			return;
		}
			
		$jobData->setWvAssetIds(implode(',', $wvAssetIds));				
		$jobData->addModifiedAttribute('licenseStartDate', $entryStartDate);
		$jobData->addModifiedAttribute('licenseEndDate', $entryEndDate);
			
		return kJobsManager::addJob($batchJob, $jobData, $batchJobType);		
	}

	private function shouldSyncWidevineRepositoryForPartner($partnerId)
	{
		return PermissionPeer::isValidForPartner(WidevinePlugin::WIDEVINE_ENABLE_DISTRIBUTION_DATES_SYNC_PERMISSION, $partnerId);
	}
	
	private function getLicenseStartDateFromEntry($entry)
	{
		$startDate = $entry->getStartDate(null);
		if(!$startDate)
		{
			$dt = new DateTime(WidevinePlugin::DEFAULT_LICENSE_START);
			$startDate = (int) $dt->format('U');
		}
		return $startDate;
	}
	
	private function getLicenseEndDateFromEntry($entry)
	{
		$endDate = $entry->getEndDate(null);
		if(!$endDate)
		{
			$dt = new DateTime(WidevinePlugin::DEFAULT_LICENSE_END);
			$endDate = (int) $dt->format('U');
		}
		return $endDate;
	}
	
}