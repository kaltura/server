<?php
/**
 * DoubleClick Service
 *
 * @service doubleClick
 * @package plugins.doubleClickDistribution
 * @subpackage api.services
 */
class DoubleClickService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
	}
	
	/**
	 * @action getFeed
	 * @param int $distributionProfileId
	 * @param string $hash
	 * @param int $page
	 * @param int $period
	 * @return file
	 */
	public function getFeedAction($distributionProfileId, $hash, $page = 1, $period = -1)
	{
		if (!$this->getPartnerId() || !$this->getPartner())
			throw new KalturaAPIException(KalturaErrors::INVALID_PARTNER_ID, $this->getPartnerId());
			
		$profile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if (!$profile || !$profile instanceof DoubleClickDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND, $distributionProfileId);

		if ($profile->getStatus() != KalturaDistributionProfileStatus::ENABLED)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_DISABLED, $distributionProfileId);

		if ($profile->getUniqueHashForFeedUrl() != $hash)
			throw new KalturaAPIException(DoubleClickDistributionErrors::INVALID_FEED_URL);

		if (!$page || $page < 1)
			$page = 1;

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
		$entryFilter->set('_order_by', '-created_at');
		
		if ($period && $period > 0)
			$entryFilter->set('_gte_updated_at', time() - 24*60*60); // last 24 hours

		$baseCriteria = KalturaCriteria::create(entryPeer::OM_CLASS);
		$baseCriteria->setLimit($profile->getItemsPerPage());
		$entryFilter->attachToCriteria($baseCriteria);
		$baseCriteria->setOffset(($page - 1) * $profile->getItemsPerPage());
		$entries = entryPeer::doSelect($baseCriteria);
		$totalCount = $baseCriteria->getRecordsCount();
		
		$feed = new DoubleClickFeed('doubleclick_template.xml', $profile);
		$feed->setTotalResult($totalCount);
		$feed->setStartIndex(($page - 1) * $profile->getItemsPerPage() + 1);
		$feed->setSelfLink($this->getUrl($distributionProfileId, $hash, $page, $period));
		if ($totalCount > $page * $profile->getItemsPerPage())
			$feed->setNextLink($this->getUrl($distributionProfileId, $hash, $page + 1, $period));
		
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
			
			$cuePoints = $this->getCuePoints($entry->getPartnerId(), $entry->getId());
			$feed->addItem($fields, $flavorAssets, $thumbAssets, $cuePoints);
		}
		
		header('Content-Type: text/xml');
		echo $feed->getXml();
		die;
	}
	
	/**
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
	
	/**
	 * @param int $distributionProfileId
	 * @param string $hash
	 * @param int $page
	 */
	protected function getUrl($distributionProfileId, $hash, $page, $period)
	{
		$urlParams = array(
			'service' => 'doubleclickdistribution_doubleclick',
			'action' => 'getFeed',
			'partnerId' => $this->getPartnerId(),
			'distributionProfileId' => $distributionProfileId,
			'hash' => $hash,
			'page' => $page,
			'period' => $period,
		);
		return requestUtils::getRequestHost() . '/api_v3/index.php?' . http_build_query($urlParams, null, '&');	
	}
}
