<?php
/**
 * Time Warner Service
 *
 * @service timeWarner
 * @package plugins.timeWarnerDistribution
 * @subpackage api.services
 */
class TimeWarnerService extends ContentDistributionServiceBase
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
		return new TimeWarnerDistributionProfile();
	}
	
	protected function createFeedGenerator($context) {
		$feed = new TimeWarnerFeed('time_warner_template.xml');
		$feed->setDistributionProfile($this->profile);
		return $feed;
	}
	
	protected function handleEntry($context, $feed, entry $entry, Entrydistribution $entryDistribution)
	{
		$fields = $this->profile->getAllFieldValues($entryDistribution);
		$flavorAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getFlavorAssetIds()));
		$thumbAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getThumbAssetIds()));
		$additionalAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getAssetIds()));
		
		return $feed->getItemXml($fields, $flavorAssets, $thumbAssets,$additionalAssets);
	}
}
