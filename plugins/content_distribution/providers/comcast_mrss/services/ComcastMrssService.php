<?php
/**
 * Comcast Mrss Service
 *
 * @service comcastMrss
 * @package plugins.comcastMrssDistribution
 * @subpackage api.services
 */
class ComcastMrssService extends ContentDistributionServiceBase
{
	/**
	 * @action getFeed
	 * @disableTags TAG_WIDGET_SESSION,TAG_ENTITLEMENT_ENTRY,TAG_ENTITLEMENT_CATEGORY
	 * @param int $distributionProfileId
	 * @param string $hash
	 * @return file
	 * @ksOptional
	 */
	public function getFeedAction($distributionProfileId, $hash)
	{
		return $this->generateFeed(new ContentDistributionServiceContext(), $distributionProfileId, $hash);
	}
	
	public function getProfileClass() {
		return new ComcastMrssDistributionProfile();
	}
	
	protected function createFeedGenerator($context) {
		$feed = new ComcastMrssFeed('comcast_mrss_template.xml');
		$feed->setDistributionProfile($this->profile);
		return $feed;
	}
	
	protected function handleEntry($context, $feed, entry $entry, Entrydistribution $entryDistribution)
	{
		$fields = $this->profile->getAllFieldValues($entryDistribution);
		$flavorAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getFlavorAssetIds()));
		$thumbAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getThumbAssetIds()));
		
		$captionAssets = null;
		if ($this->	profile instanceof ComcastMrssDistributionProfile && $this->profile->getShouldIncludeCaptions())
		{
			KalturaLog::info("Adding entry captions.");
			$captionAssets = $this->getCaptions($entry->getPartnerId(), $entry->getId());
		}
		
		$cuePoints = null;
		if ($this->	profile instanceof ComcastMrssDistributionProfile && $this->profile->getShouldIncludeCuePoints())
		{
			KalturaLog::info("Adding entry cue points.");
			$cuePoints = $this->getCuePoints($entry->getPartnerId(), $entry->getId()); 
		}
		
		return $feed->getItemXml($fields, $flavorAssets, $thumbAssets, $captionAssets, $cuePoints);
	}
	
	/**
	 * @param $partnerId
	 * @param $entryId
	 */
	protected function getCuePoints($partnerId, $entryId)
	{
		$c = KalturaCriteria::create(CuePointPeer::OM_CLASS);
		$c->add(CuePointPeer::PARTNER_ID, $partnerId);
		$c->add(CuePointPeer::ENTRY_ID, $entryId);
		$c->add(CuePointPeer::TYPE, AdCuePointPlugin::getCuePointTypeCoreValue(AdCuePointType::AD));
		$c->addAscendingOrderByColumn(CuePointPeer::START_TIME);
		return CuePointPeer::doSelect($c);
	}
	
	protected function getCaptions ($partnerId, $entryId)
	{
		$c = new Criteria();
		$c->add(assetPeer::PARTNER_ID, $partnerId);
		$c->add(assetPeer::ENTRY_ID, $entryId);
		$c->add(assetPeer::TYPE, CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION));
		$c->add(assetPeer::STATUS, asset::ASSET_STATUS_READY);
		
		return assetPeer::doSelect($c);
	}
}
