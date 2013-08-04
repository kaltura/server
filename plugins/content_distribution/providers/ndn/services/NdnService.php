<?php
/**
 * Ndn Service
 *
 * @service ndn
 * @package plugins.ndnDistribution
 * @subpackage api.services
 */
class NdnService extends ContentDistributionServiceBase
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
		return new NdnDistributionProfile();
	}
	
	protected function createFeedGenerator($context) {
		$context->lastBuildDate = $this->profile->getUpdatedAt(null);
		
		$feed = new NdnFeed('ndn_template.xml');
		$feed->setDistributionProfile($this->profile);		
		$feed->setChannelFields();
		return $feed;
	}
	
	protected function handleEntry($context, $feed, entry $entry, Entrydistribution $entryDistribution)
	{
		$fields = $this->profile->getAllFieldValues($entryDistribution);
		$flavorAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getFlavorAssetIds()));
		$thumbAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getThumbAssetIds()));				
		$xml = $feed->getItemXml($fields, $flavorAssets, $thumbAssets, $entry);	
		
		if ($entry->getUpdatedAt(null) > $this->lastBuildDate) {
			$context->lastBuildDate = $entry->getUpdatedAt(null);
		}

		return $xml;
	}	
	
	protected function doneFeedGeneration ($context, $feed) {
		$feed->setChannelLastBuildDate($context->lastBuildDate);
		parent::doneFeedGeneration($context, $feed);
	}
}
