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
	 * @param string $state
	 * @return file
	 */
	public function getFeedAction($distributionProfileId, $hash, $page = 1, $period = -1, $state = '')
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

		$stateLastEntryCreatedAt = null;
		$stateLastEntryIds = array();
		if ($state) 
		{
			$stateDecoded = base64_decode($state);
			if (strpos($stateDecoded, '|') !== false) 
			{
				$stateExploded = explode('|', $stateDecoded);
				$stateLastEntryCreatedAt = $stateExploded[0];
				$stateLastEntryIdsStr =  $stateExploded[1];
				$stateLastEntryIds = explode(',', $stateLastEntryIdsStr);
			}
		}

		// "Creates advanced filter on distribution profile
		$distributionAdvancedSearch = new ContentDistributionSearchFilter();
		$distributionAdvancedSearch->setDistributionProfileId($profile->getId());
		$distributionAdvancedSearch->setDistributionSunStatus(EntryDistributionSunStatus::AFTER_SUNRISE);
		$distributionAdvancedSearch->setEntryDistributionStatus(EntryDistributionStatus::READY);
		$distributionAdvancedSearch->setEntryDistributionFlag(EntryDistributionDirtyStatus::NONE);
		$distributionAdvancedSearch->setHasEntryDistributionValidationErrors(false);
			
		// Creates entry filter with advanced filter
		$entryFilter = new entryFilter();
		$entryFilter->setStatusEquel(entryStatus::READY);
		$entryFilter->setModerationStatusNot(entry::ENTRY_MODERATION_STATUS_REJECTED);
		$entryFilter->setPartnerIdEquel($this->getPartnerId());
		$entryFilter->setAdvancedSearch($distributionAdvancedSearch);
		$entryFilter->set('_order_by', '-created_at');
		if ($period && $period > 0)
			$entryFilter->set('_gte_updated_at', time() - 24*60*60); // last 24 hours
			
		// Dummy query to get the total count
		$baseCriteria = KalturaCriteria::create(entryPeer::OM_CLASS);
		$baseCriteria->setLimit(1);
		$entryFilter->attachToCriteria($baseCriteria);
		$entries = entryPeer::doSelect($baseCriteria);
		$totalCount = $baseCriteria->getRecordsCount();
		
		// Add the state data to proceed to next page
		if ($stateLastEntryCreatedAt)
			$entryFilter->set('_lte_created_at', $stateLastEntryCreatedAt);
		if ($stateLastEntryIds)
			$entryFilter->set('_notin_id', $stateLastEntryIds);

		$baseCriteria = KalturaCriteria::create(entryPeer::OM_CLASS);
		$baseCriteria->setLimit($profile->getItemsPerPage());
		$entryFilter->attachToCriteria($baseCriteria);
		$entries = entryPeer::doSelect($baseCriteria);
		
		// Find the state
		$entryIds = array();
		$nextPageStateLastEntryCreatedAt = $stateLastEntryCreatedAt;
		$nextPageStateLastEntryIds = $stateLastEntryIds;
		foreach($entries as $entry)
		{
			$entryIds[] = $entry->getId();
			
			if ($nextPageStateLastEntryCreatedAt > $entry->getCreatedAt(null))
				$nextPageStateLastEntryIds = array();
			
			$nextPageStateLastEntryIds[] = $entry->getId();
			$nextPageStateLastEntryCreatedAt = $entry->getCreatedAt(null);
		}
		
		// Construct the feed
		$feed = new DoubleClickFeed('doubleclick_template.xml', $profile);
		$feed->setTotalResult($totalCount);
		$feed->setStartIndex(($page - 1) * $profile->getItemsPerPage() + 1);
		$feed->setSelfLink($this->getUrl($distributionProfileId, $hash, $page, $period, $stateLastEntryCreatedAt, $stateLastEntryIds));
		if ($totalCount > $page * $profile->getItemsPerPage())
			$feed->setNextLink($this->getUrl($distributionProfileId, $hash, $page + 1, $period, $nextPageStateLastEntryCreatedAt, $nextPageStateLastEntryIds));
		
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
	protected function getUrl($distributionProfileId, $hash, $page, $period, $stateLastEntryCreatedAt, $stateLastEntryIds)
	{
		if (!is_null($stateLastEntryCreatedAt) && !is_null($stateLastEntryIds) && count($stateLastEntryIds) > 0)
			$state = $stateLastEntryCreatedAt.'|'.implode(',', $stateLastEntryIds);
		else
			$state = '';
		$urlParams = array(
			'service' => 'doubleclickdistribution_doubleclick',
			'action' => 'getFeed',
			'partnerId' => $this->getPartnerId(),
			'distributionProfileId' => $distributionProfileId,
			'hash' => $hash,
			'page' => $page,
			'state' => base64_encode($state),
			'period' => $period,
		);
		return requestUtils::getRequestHost() . '/api_v3/index.php?' . http_build_query($urlParams, null, '&');	
	}
}
