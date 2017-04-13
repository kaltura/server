<?php
/**
 * Unicorn Service
 *
 * @service unicorn
 * @package plugins.unicornDistribution
 * @subpackage api.services
 */
class UnicornService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		$this->applyPartnerFilterForClass('BatchJob');
		
		if(!ContentDistributionPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, ContentDistributionPlugin::PLUGIN_NAME);
	}
	
	/**
	 * @action notify
	 * @disableTags TAG_WIDGET_SESSION,TAG_ENTITLEMENT_ENTRY,TAG_ENTITLEMENT_CATEGORY
	 * @param int $id distribution job id
	 * @ksIgnored
	 */
	public function notifyAction($id) 
	{
		$submitCoreType = ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT);
		$updateCoreType = ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE);
		$deleteCoreType = ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE);
		
		$validJobTypes = array(
			$submitCoreType,
			$updateCoreType,
			$deleteCoreType,
		);
		
		$batchJob = BatchJobPeer::retrieveByPK($id);
		$invalid = false;
		if(!$batchJob)
		{
			$invalid = true;
			KalturaLog::err("Job [$id] not found");
		}
		elseif(!in_array($batchJob->getJobType(), $validJobTypes))
		{
			$invalid = true;
			KalturaLog::err("Job [$id] wrong type [" . $batchJob->getJobType() . "] expected [" . implode(', ', $validJobTypes) . "]");
		}
		elseif($batchJob->getJobSubType() != UnicornDistributionProvider::get()->getType())
		{
			$invalid = true;
			KalturaLog::err("Job [$id] wrong sub-type [" . $batchJob->getJobSubType() . "] expected [" . UnicornDistributionProvider::get()->getType() . "]");
		}
		elseif($batchJob->getStatus() != KalturaBatchJobStatus::ALMOST_DONE)
		{
			$invalid = true;
			KalturaLog::err("Job [$id] wrong status [" . $batchJob->getStatus() . "] expected [" . KalturaBatchJobStatus::ALMOST_DONE . "]");
		}
		if($invalid)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_BATCHJOB_ID, $id);
		}
			
		kJobsManager::updateBatchJob($batchJob, KalturaBatchJobStatus::FINISHED);
		
		$data = $batchJob->getData();
		/* @var $data kDistributionJobData */
		
		$providerData = $data->getProviderData();
		/* @var $providerData kUnicornDistributionJobProviderData */
		
		$entryDistribution = EntryDistributionPeer::retrieveByPK($data->getEntryDistributionId());
		if($entryDistribution)
		{
			$entryDistribution->putInCustomData(kUnicornDistributionJobProviderData::CUSTOM_DATA_FLAVOR_ASSET_OLD_VERSION, $providerData->getFlavorAssetVersion());
			$entryDistribution->save();
		}
		
		if($batchJob->getJobType() == $submitCoreType)
		{
			$this->attachRemoteAssetResource($batchJob->getEntry(), $batchJob->getData());
		}
		
		if($batchJob->getJobType() == $deleteCoreType)
		{
			$this->detachRemoteAssetResource($batchJob->getEntry(), $batchJob->getData());
		}
	}
	
	protected function detachRemoteAssetResource(entry $entry, kDistributionSubmitJobData $data)
	{
		$distributionProfile = DistributionProfilePeer::retrieveByPK($data->getDistributionProfileId());
		/* @var $distributionProfile UnicornDistributionProfile */
		
		$asset = assetPeer::retrieveByEntryIdAndParams($entry->getId(), $distributionProfile->getRemoteAssetParamsId());
		$asset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_DELETED);
		$asset->setDeletedAt(time());
		$asset->save();		
	}
	
	protected function attachRemoteAssetResource(entry $entry, kDistributionSubmitJobData $data)
	{
		$distributionProfile = DistributionProfilePeer::retrieveByPK($data->getDistributionProfileId());
		/* @var $distributionProfile UnicornDistributionProfile */
		
		$domainGuid = $distributionProfile->getDomainGuid();
		$applicationGuid = $distributionProfile->getAdFreeApplicationGuid();
		$assetParamsId = $distributionProfile->getRemoteAssetParamsId();
		$mediaItemGuid = $data->getRemoteId();
		
		$url = "$domainGuid/$applicationGuid/$mediaItemGuid/content.m3u8";
		
		$entry->setSource(KalturaSourceType::URL);
		$entry->save();
		
		$isNewAsset = false;
		$asset = assetPeer::retrieveByEntryIdAndParams($entry->getId(), $assetParamsId);
		if(!$asset)
		{
			$isNewAsset = true;
			$assetParams = assetParamsPeer::retrieveByPK($assetParamsId);
			
			$asset = assetPeer::getNewAsset($assetParams->getType());
			$asset->setPartnerId($entry->getPartnerId());
			$asset->setEntryId($entry->getId());
			$asset->setStatus(asset::FLAVOR_ASSET_STATUS_QUEUED);
			
			$asset->setFlavorParamsId($assetParamsId);
			$asset->setFromAssetParams($assetParams);
			if($assetParams->hasTag(assetParams::TAG_SOURCE))
				$asset->setIsOriginal(true);
		}
				
		$asset->incrementVersion();
		$asset->setFileExt('m3u8');
		$asset->setStatus(asset::FLAVOR_ASSET_STATUS_READY);
		$asset->save();
		
		$syncKey = $asset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$storageProfile = StorageProfilePeer::retrieveByPK($distributionProfile->getStorageProfileId());
		$fileSync = kFileSyncUtils::createReadyExternalSyncFileForKey($syncKey, $url, $storageProfile);
		
		if($isNewAsset)
			kEventsManager::raiseEvent(new kObjectAddedEvent($asset));
		
		kEventsManager::raiseEvent(new kObjectDataChangedEvent($asset));
		kBusinessPostConvertDL::handleConvertFinished(null, $asset);
	}
}
