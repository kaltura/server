<?php
/**
 * Time Warner Service
 *
 * @service timeWarner
 * @package plugins.timeWarnerDistribution
 * @subpackage api.services
 */
class TimeWarnerService extends KalturaBaseService
{
	/**
	 * @action getFeed
	 * @disableTags TAG_WIDGET_SESSION,TAG_ENTITLEMENT_ENTRY,TAG_ENTITLEMENT_CATEGORY
	 * @param int $distributionProfileId
	 * @param string $hash
	 * @return file
	 */
	public function getFeedAction($distributionProfileId, $hash)
	{
		if (!$this->getPartnerId() || !$this->getPartner())
			throw new KalturaAPIException(KalturaErrors::INVALID_PARTNER_ID, $this->getPartnerId());
			
		$profile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if (!$profile || !$profile instanceof TimeWarnerDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND, $distributionProfileId);

		if ($profile->getStatus() != KalturaDistributionProfileStatus::ENABLED)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_DISABLED, $distributionProfileId);

		if ($profile->getUniqueHashForFeedUrl() != $hash)
			throw new KalturaAPIException(TimeWarnerDistributionErrors::INVALID_FEED_URL);

		// "Creates advanced filter on distribution profile
		$distributionAdvancedSearch = new ContentDistributionSearchFilter();
		$distributionAdvancedSearch->setDistributionProfileId($profile->getId());
		$distributionAdvancedSearch->setDistributionSunStatus(EntryDistributionSunStatus::AFTER_SUNRISE);
		$distributionAdvancedSearch->setEntryDistributionStatus(EntryDistributionStatus::READY);
		$distributionAdvancedSearch->setEntryDistributionFlag(EntryDistributionDirtyStatus::NONE);
		$distributionAdvancedSearch->setHasEntryDistributionValidationErrors(false);
			
		//Creates entry filter with advanced filter
		$entryFilter = new entryFilter();
		$entryFilter->setStatusEquel(entryStatus::READY);
		$entryFilter->setModerationStatusNot(entry::ENTRY_MODERATION_STATUS_REJECTED);
		$entryFilter->setPartnerSearchScope($this->getPartnerId());
		$entryFilter->setAdvancedSearch($distributionAdvancedSearch);
		
		$baseCriteria = KalturaCriteria::create(entryPeer::OM_CLASS);
		$entryFilter->attachToCriteria($baseCriteria);
		$entries = entryPeer::doSelect($baseCriteria);
		
		$feed = new TimeWarnerFeed('time_warner_template.xml');
		$feed->setDistributionProfile($profile);
		$counter = 0;
		foreach($entries as $entry)
		{
			/* @var $entry entry */
			$entryDistribution = EntryDistributionPeer::retrieveByEntryAndProfileId($entry->getId(), $profile->getId());
			if (!$entryDistribution)
			{
				KalturaLog::err('Entry distribution was not found for entry ['.$entry->getId().'] and profile [' . $profile->getId() . ']');
				continue;
			}
			$fields = $profile->getAllFieldValues($entryDistribution);
			$flavorAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getFlavorAssetIds()));
			$thumbAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getThumbAssetIds()));
			$additionalAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getAssetIds()));
			$feed->addItem($fields, $flavorAssets, $thumbAssets,$additionalAssets);
			$counter++;
			
			//to avoid the cache exceeding the memory size 
			if ($counter >= 100){
				kMemoryManager::clearMemory();
				$counter = 0;
			}
		}
		
		header('Content-Type: text/xml');		
		echo $feed->getXml();
		die;
	}
}
