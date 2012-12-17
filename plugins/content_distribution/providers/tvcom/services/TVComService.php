<?php
/**
 * TVCom service
 *
 * @service tvCom
 * @package plugins.contentDistribution
 * @subpackage api.services
 */
class TVComService extends KalturaBaseService
{
	/**
	 * @action getFeed
	 * @disableTags TAG_WIDGET_SESSION,TAG_ENTITLEMENT_ENTRY,TAG_ENTITLEMENT_CATEGORY
	 * @param int $distributionProfileId
	 * @param string $hash
	 */
	public function getFeedAction($distributionProfileId, $hash)
	{
		if (!$this->getPartnerId() || !$this->getPartner())
			throw new KalturaAPIException(KalturaErrors::INVALID_PARTNER_ID, $this->getPartnerId());
			
		$profile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if (!$profile || !$profile instanceof TVComDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND, $distributionProfileId);

		if ($profile->getStatus() != KalturaDistributionProfileStatus::ENABLED)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_DISABLED, $distributionProfileId);

		if ($profile->getUniqueHashForFeedUrl() != $hash)
			throw new KalturaAPIException(TVComDistributionErrors::INVALID_FEED_URL);

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
		$baseCriteria->add(entryPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_SYSTEM, Criteria::NOT_EQUAL);
		$entryFilter->attachToCriteria($baseCriteria);
		$entries = entryPeer::doSelect($baseCriteria);
		
		$feed = new TVComFeed('tvcom_template.xml');
		$feed->setDistributionProfile($profile);
		$profileUpdatedAt = $profile->getUpdatedAt(null);
		$cacheDir = kConf::get("global_cache_dir")."/feeds/dist_$distributionProfileId/";
		foreach($entries as $entry)
		{
			// check cache
			$cacheFileName = $cacheDir.myContentStorage::dirForId($entry->getIntId(), $entry->getId().".xml");
			$updatedAt = max($profileUpdatedAt,  $entry->getUpdatedAt(null));
			if (file_exists($cacheFileName) && $updatedAt < filemtime($cacheFileName))
			{
				$xml = file_get_contents($cacheFileName);
			}
			else
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
				$xml = $feed->getItemXml($fields, count($flavorAssets) ? $flavorAssets[0] : null, count($thumbAssets) ? $thumbAssets[0] : null,$additionalAssets);
				mkdir(dirname($cacheFileName), 0750, true);
				file_put_contents($cacheFileName, $xml);
			}
			$feed->addItemXml($xml);
		}
		
		echo $feed->getXml();
		die;
	}
}
