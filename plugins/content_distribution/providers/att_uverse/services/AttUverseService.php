<?php
/**
 * Att Uverse Service
 *
 * @service attUverse
 * @package plugins.attUverseDistribution
 * @subpackage api.services
 */
class AttUverseService extends ContentDistributionServiceBase
{
	
	/**
	 * @action getFeed
	 * @disableTags TAG_WIDGET_SESSION,TAG_ENTITLEMENT_ENTRY,TAG_ENTITLEMENT_CATEGORY
	 * @param int $distributionProfileId
	 * @param string $hash
	 * @return file
	 */
	public function getFeedAction($distributionProfileId, $hash) {
		$this->generateFeed(new ContentDistributionServiceContext(), $distributionProfileId, $hash);
	}
	
	
	public function getProfileClass() {
		return new AttUverseDistributionProfile();
	}
	
	protected function createFeedGenerator($context) {
		return new AttUverseDistributionFeedHelper('feed_template.xml',$this->profile);
	}
	
	protected function handleEntry($context, $feed,entry $entry, Entrydistribution $entryDistribution) {
		$fields = $this->profile->getAllFieldValues($entryDistribution);
		
		//flavors assets and remote flavor asset file urls			
		$flavorAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getFromCustomData(AttUverseEntryDistributionCustomDataField::DISTRIBUTED_FLAVOR_IDS)));
		$remoteAssetFileUrls = unserialize($entryDistribution->getFromCustomData(AttUverseEntryDistributionCustomDataField::REMOTE_ASSET_FILE_URLS));
		
		//thumb assets and remote thumb asset file urls			
		$thumbAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getFromCustomData(AttUverseEntryDistributionCustomDataField::DISTRIBUTED_THUMBNAIL_IDS)));
		$remoteThumbailFileUrls = unserialize($entryDistribution->getFromCustomData(AttUverseEntryDistributionCustomDataField::REMOTE_THUMBNAIL_FILE_URLS));
		
		//thumb assets and remote thumb asset file urls			
		$captionAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getFromCustomData(AttUverseEntryDistributionCustomDataField::DISTRIBUTED_CAPTION_IDS)));
		$xml = $feed->getItemXml($fields, $flavorAssets, $remoteAssetFileUrls, $thumbAssets, $remoteThumbailFileUrls, $captionAssets);
		
		$context->channelTitle = $context->fields[AttUverseDistributionField::CHANNEL_TITLE];
		
		return $xml;
	}
	
	protected function doneFeedGeneration ($context, $feed) {
		$channelTitle = isset($context->channelTitle) ? $context->channelTitle : $this->profile->getChannelTitle();
		$feed->setChannelTitle($channelTitle);
		parent::doneFeedGeneration($context, $feed);
	}
}
