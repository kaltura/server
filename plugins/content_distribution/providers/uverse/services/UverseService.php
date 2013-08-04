<?php
/**
 * Uverse Service
 *
 * @service uverse
 * @package plugins.uverseDistribution
 * @subpackage api.services
 */
class UverseService extends ContentDistributionServiceBase
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
		$this->generateFeed(new ContentDistributionServiceContext(), $distributionProfileId, $hash);
	}
	
	public function getProfileClass() {
		return new UverseDistributionProfile();
	}
	
	protected function createFeedGenerator($context) {
		$context->lastBuildDate = $this->profile->getUpdatedAt(null);
		
		$feed = new UverseFeed('uverse_template.xml');
		$feed->setDistributionProfile($this->profile);
		$feed->setChannelFields();
		return $feed;
	}
	
	protected function getEntryFilter($context, $keepScheduling = true) {
		// "Creates advanced filter on distribution profile
		$distributionAdvancedSearch = new ContentDistributionSearchFilter();
		$distributionAdvancedSearch->setDistributionProfileId($this->profile->getId());
		$distributionAdvancedSearch->setDistributionSunStatus(EntryDistributionSunStatus::AFTER_SUNRISE);
		$distributionAdvancedSearch->setEntryDistributionStatus(EntryDistributionStatus::READY);
		$distributionAdvancedSearch->setHasEntryDistributionValidationErrors(false);
			
		//Creates entry filter with advanced filter
		$entryFilter = new entryFilter();
		$entryFilter->setStatusEquel(entryStatus::READY);
		$entryFilter->setModerationStatusNot(entry::ENTRY_MODERATION_STATUS_REJECTED);
		$entryFilter->setPartnerSearchScope($this->getPartnerId());
		$entryFilter->setAdvancedSearch($distributionAdvancedSearch);
		
		return $entryFilter;
	}
	
	protected function handleEntry($context, $feed, entry $entry, Entrydistribution $entryDistribution) {
		$fields = $this->profile->getAllFieldValues($entryDistribution);
		
		$flavorAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getFlavorAssetIds()));
		$flavorAsset = reset($flavorAssets);
		$flavorAssetRemoteUrl = $entryDistribution->getFromCustomData(UverseEntryDistributionCustomDataField::REMOTE_ASSET_URL);
		
		$thumbAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getThumbAssetIds()));
		
		$xml = $feed->getItemXml($fields, $flavorAsset, $flavorAssetRemoteUrl, $thumbAssets);
		
		// we want to find the newest update time between all entries
		if ($entry->getUpdatedAt(null) > $context->lastBuildDate)
			$context->lastBuildDate = $entry->getUpdatedAt(null);
		
		return $xml;
	}	
	
	protected function doneFeedGeneration ($context, $feed) {
		$feed->setChannelLastBuildDate($context->lastBuildDate);
		parent::doneFeedGeneration($context, $feed);
	}
}
