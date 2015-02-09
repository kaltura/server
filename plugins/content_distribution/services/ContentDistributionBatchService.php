<?php
/**
 * @service contentDistributionBatch
 * @package plugins.contentDistribution
 * @subpackage api.services
 */
class ContentDistributionBatchService extends KalturaBaseService
{

// --------------------------------- Distribution Synchronizer functions 	--------------------------------- //

	/**
	 * updates entry distribution sun status in the search engine
	 *
	 * @action updateSunStatus
	 */
	function updateSunStatusAction()
	{
		// serach all records that their sun status changed to after sunset
		$criteria = KalturaCriteria::create(EntryDistributionPeer::OM_CLASS);
		$criteria->add(EntryDistributionPeer::SUN_STATUS, EntryDistributionSunStatus::AFTER_SUNSET , Criteria::NOT_EQUAL);
		$crit1 = $criteria->getNewCriterion(EntryDistributionPeer::SUNSET, time(), Criteria::LESS_THAN);
		$criteria->add($crit1);
		$entryDistributions = EntryDistributionPeer::doSelect($criteria);
		foreach($entryDistributions as $entryDistribution) // raise the updated events to trigger index in search engine (sphinx)
			kEventsManager::raiseEvent(new kObjectUpdatedEvent($entryDistribution));


		// serach all records that their sun status changed to after sunrise
		$criteria = KalturaCriteria::create(EntryDistributionPeer::OM_CLASS);
		$criteria->add(EntryDistributionPeer::SUN_STATUS, EntryDistributionSunStatus::BEFORE_SUNRISE);
		$criteria->add(EntryDistributionPeer::SUNRISE, time(), Criteria::LESS_THAN);
		$entryDistributions = EntryDistributionPeer::doSelect($criteria);
		foreach($entryDistributions as $entryDistribution) // raise the updated events to trigger index in search engine (sphinx)
			kEventsManager::raiseEvent(new kObjectUpdatedEvent($entryDistribution));
	}


	/**
	 * creates all required jobs according to entry distribution dirty flags
	 *
	 * @action createRequiredJobs
	 */
	function createRequiredJobsAction()
	{
		// serach all records that their next report time arrived
		$criteria = KalturaCriteria::create(EntryDistributionPeer::OM_CLASS);
		$criteria->add(EntryDistributionPeer::NEXT_REPORT, time(), Criteria::LESS_EQUAL);
		$entryDistributions = EntryDistributionPeer::doSelect($criteria);
		foreach($entryDistributions as $entryDistribution)
		{
			$distributionProfile = DistributionProfilePeer::retrieveByPK($entryDistribution->getDistributionProfileId());
			if($distributionProfile)
				kContentDistributionManager::submitFetchEntryDistributionReport($entryDistribution, $distributionProfile);
			else
				KalturaLog::err("Distribution profile [" . $entryDistribution->getDistributionProfileId() . "] not found for entry distribution [" . $entryDistribution->getId() . "]");
		}


		// serach all records that arrived their sunrise time and requires submittion
		$criteria = KalturaCriteria::create(EntryDistributionPeer::OM_CLASS);
		$criteria->add(EntryDistributionPeer::DIRTY_STATUS, EntryDistributionDirtyStatus::SUBMIT_REQUIRED);
		$criteria->add(EntryDistributionPeer::SUNRISE, time(), Criteria::LESS_EQUAL);
		$entryDistributions = EntryDistributionPeer::doSelect($criteria);
		foreach($entryDistributions as $entryDistribution)
		{
			$distributionProfile = DistributionProfilePeer::retrieveByPK($entryDistribution->getDistributionProfileId());
			if($distributionProfile)
				kContentDistributionManager::submitAddEntryDistribution($entryDistribution, $distributionProfile);
			else
				KalturaLog::err("Distribution profile [" . $entryDistribution->getDistributionProfileId() . "] not found for entry distribution [" . $entryDistribution->getId() . "]");
		}


		// serach all records that arrived their sunrise time and requires enable
		$criteria = KalturaCriteria::create(EntryDistributionPeer::OM_CLASS);
		$criteria->add(EntryDistributionPeer::DIRTY_STATUS, EntryDistributionDirtyStatus::ENABLE_REQUIRED);
		$criteria->add(EntryDistributionPeer::SUNRISE, time(), Criteria::LESS_EQUAL);
		$entryDistributions = EntryDistributionPeer::doSelect($criteria);
		foreach($entryDistributions as $entryDistribution)
		{
			$distributionProfile = DistributionProfilePeer::retrieveByPK($entryDistribution->getDistributionProfileId());
			if($distributionProfile)
				kContentDistributionManager::submitEnableEntryDistribution($entryDistribution, $distributionProfile);
			else
				KalturaLog::err("Distribution profile [" . $entryDistribution->getDistributionProfileId() . "] not found for entry distribution [" . $entryDistribution->getId() . "]");
		}


		// serach all records that arrived their sunset time and requires deletion
		$criteria = KalturaCriteria::create(EntryDistributionPeer::OM_CLASS);
		$criteria->add(EntryDistributionPeer::DIRTY_STATUS, EntryDistributionDirtyStatus::DELETE_REQUIRED);
		$criteria->add(EntryDistributionPeer::SUNSET, time(), Criteria::LESS_EQUAL);
		$entryDistributions = EntryDistributionPeer::doSelect($criteria);
		foreach($entryDistributions as $entryDistribution)
		{
			$distributionProfile = DistributionProfilePeer::retrieveByPK($entryDistribution->getDistributionProfileId());
			if($distributionProfile)
				kContentDistributionManager::submitDeleteEntryDistribution($entryDistribution, $distributionProfile);
			else
				KalturaLog::err("Distribution profile [" . $entryDistribution->getDistributionProfileId() . "] not found for entry distribution [" . $entryDistribution->getId() . "]");
		}


		// serach all records that arrived their sunset time and requires disable
		$criteria = KalturaCriteria::create(EntryDistributionPeer::OM_CLASS);
		$criteria->add(EntryDistributionPeer::DIRTY_STATUS, EntryDistributionDirtyStatus::DISABLE_REQUIRED);
		$criteria->add(EntryDistributionPeer::SUNSET, time(), Criteria::LESS_EQUAL);
		$entryDistributions = EntryDistributionPeer::doSelect($criteria);
		foreach($entryDistributions as $entryDistribution)
		{
			$distributionProfile = DistributionProfilePeer::retrieveByPK($entryDistribution->getDistributionProfileId());
			if($distributionProfile)
				kContentDistributionManager::submitDisableEntryDistribution($entryDistribution, $distributionProfile);
			else
				KalturaLog::err("Distribution profile [" . $entryDistribution->getDistributionProfileId() . "] not found for entry distribution [" . $entryDistribution->getId() . "]");
		}
	}


// --------------------------------- Distribution Synchronizer functions 	--------------------------------- //


	/**
	 * returns absolute valid url for asset file
	 *
	 * @action getAssetUrl
	 * @param string $assetId
	 * @return string
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 * @throws KalturaErrors::FLAVOR_ASSET_IS_NOT_READY
	 * @throws KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND
	 */
	function getAssetUrlAction($assetId)
	{
		$asset = assetPeer::retrieveById($assetId);
		if(!$asset)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $assetId);

		$ext = $asset->getFileExt();
		if(is_null($ext))
			$ext = 'jpg';

		$fileName = $asset->getEntryId() . "_" . $asset->getId() . ".$ext";

		$syncKey = $asset->getSyncKey(asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		if(!kFileSyncUtils::fileSync_exists($syncKey))
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_IS_NOT_READY, $asset->getId());

		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, true, false, false);
		if(!$fileSync)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $asset->getId());

		return $fileSync->getExternalUrl($asset->getEntryId());
	}
}
