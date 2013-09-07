<?php
/**
 * AVN service
 *
 * @service avn
 * @package plugins.avnDistribution
 * @subpackage api.services
 */
class AvnService extends ContentDistributionServiceBase
{
	
	/**
	 * @action getFeed
	 * @disableTags TAG_WIDGET_SESSION,TAG_ENTITLEMENT_ENTRY,TAG_ENTITLEMENT_CATEGORY
	 * @param int $distributionProfileId
	 * @param string $hash
	 */
	public function getFeedAction($distributionProfileId, $hash)
	{
		$this->generateFeed(new ContentDistributionServiceContext(), $distributionProfileId, $hash);
	}
	
	public function getProfileClass() {
		return new AvnDistributionProfile();
	}
	
	protected function createFeedGenerator($context) {
		$feed = new AvnFeed('avn_template.xml');
		$feed->setDistributionProfile($this->profile);
		return $feed;
	}
	
	protected function handleEntry($context, $feed, entry $entry, Entrydistribution $entryDistribution)
	{
		$fields = $this->profile->getAllFieldValues($entryDistribution);
		$flavorAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getFlavorAssetIds()));
		$thumbAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getThumbAssetIds()));
		return $feed->getItemXml($fields, count($flavorAssets) ? $flavorAssets[0] : null, count($thumbAssets) ? $thumbAssets[0] : null);
	}
}
