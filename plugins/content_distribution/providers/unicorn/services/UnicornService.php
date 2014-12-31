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
	 */
	public function notifyAction($id) 
	{
		$validJobTypes = array(
			ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT),
			ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE),
			ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE),
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
		
		if($batchJob->getJobType() == ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT))
		{
			$this->attachRemoteAssetResource($batchJob->getEntry(), $batchJob->getData());
		}
	}
	
	protected function attachRemoteAssetResource(entry $entry, kDistributionSubmitJobData $data)
	{
		$distributionProfile = DistributionProfilePeer::retrieveByPK($data->getDistributionProfileId());
		/* @var $distributionProfile UnicornDistributionProfile */
		
		$domainGuid = $distributionProfile->getDomainGuid();
		$applicationGuid = $distributionProfile->getAdFreeApplicationGuid();
		$mediaItemGuid = $data->getRemoteId();
		
		$url = "$domainGuid/$applicationGuid/$mediaItemGuid/content.m3u8";
		
		$entry->setSource(KalturaSourceType::URL);
		$entry->save();
		
		$asset = assetPeer::retrieveByEntryIdAndParams($entry->getId(), $distributionProfile->getRemoteAssetParamsId());
		if(!$asset)
		{
			KalturaLog::err("Flavor asset not created for entry [" . $entry->getId() . "]");
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $distributionProfile->getRemoteAssetParamsId());
		}
				
		$syncKey = $asset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$storageProfile = StorageProfilePeer::retrieveByPK($distributionProfile->getStorageProfileId());
		$fileSync = kFileSyncUtils::createReadyExternalSyncFileForKey($syncKey, $url, $storageProfile);

		$asset->setFileExt('m3u8');
		$asset->setStatus(asset::FLAVOR_ASSET_STATUS_READY);
		$asset->save();
		
		kEventsManager::raiseEvent(new kObjectDataChangedEvent($asset));
		kBusinessPostConvertDL::handleConvertFinished(null, $asset);
	}
}
