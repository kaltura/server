<?php
/**
 * AVN service
 *
 * @service avn
 * @package plugins.avnDistribution
 * @subpackage api.services
 */
class AvnService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
	}
	
	/**
	 * @action getFeed
	 * @param int $distributionProfileId
	 * @param string $hash
	 */
	public function getFeedAction($distributionProfileId, $hash)
	{
		if (!$this->getPartnerId() || !$this->getPartner())
			throw new KalturaAPIException(KalturaErrors::INVALID_PARTNER_ID, $this->getPartnerId());
			
		$profile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if (!$profile || !$profile instanceof AvnDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND, $distributionProfileId);

		if ($profile->getStatus() != KalturaDistributionProfileStatus::ENABLED)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_DISABLED, $distributionProfileId);

		if ($profile->getUniqueHashForFeedUrl() != $hash)
			throw new KalturaAPIException(AvnDistributionErrors::INVALID_FEED_URL);

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
		$entryFilter->setPartnerIdEquel($this->getPartnerId());
		$entryFilter->setAdvancedSearch($distributionAdvancedSearch);
		
		$baseCriteria = KalturaCriteria::create(entryPeer::OM_CLASS);
		$entryFilter->attachToCriteria($baseCriteria);
		$entries = entryPeer::doSelect($baseCriteria);
		
		$feed = new AvnFeed('avn_template.xml');
		$feed->setDistributionProfile($profile);
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
			$feed->addItem($fields, count($flavorAssets) ? $flavorAssets[0] : null, count($thumbAssets) ? $thumbAssets[0] : null);
		}
		
		echo $feed->getXml();
		die;
	}
}
