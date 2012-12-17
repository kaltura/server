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
	/**
	 * @action getFeed
	 * @disableTags TAG_WIDGET_SESSION,TAG_ENTITLEMENT_ENTRY,TAG_ENTITLEMENT_CATEGORY
	 * @param int $distributionProfileId
	 * @param string $hash
	 * @param int $page
	 * @param int $period
	 * @param string $state
	 * @param bool $ignoreScheduling
	 * @return file
	 */
	public function getFeedAction($distributionProfileId, $hash, $page = 1, $period = -1, $state = '', $ignoreScheduling = false)
	{
		if (!$this->getPartnerId() || !$this->getPartner())
			throw new KalturaAPIException(KalturaErrors::INVALID_PARTNER_ID, $this->getPartnerId());
			
		$profile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		/* @var $profile DoubleClickDistributionProfile */
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
		if ($ignoreScheduling !== true && $profile->getIgnoreSchedulingInFeed() !== true)
			$distributionAdvancedSearch->setDistributionSunStatus(EntryDistributionSunStatus::AFTER_SUNRISE);
		$distributionAdvancedSearch->setEntryDistributionStatus(EntryDistributionStatus::READY);
		$distributionAdvancedSearch->setEntryDistributionFlag(EntryDistributionDirtyStatus::NONE);
		$distributionAdvancedSearch->setHasEntryDistributionValidationErrors(false);
			
		// Creates entry filter with advanced filter
		$entryFilter = new entryFilter();
		$entryFilter->setStatusEquel(entryStatus::READY);
		$entryFilter->setModerationStatusNot(entry::ENTRY_MODERATION_STATUS_REJECTED);
		$entryFilter->setPartnerSearchScope($this->getPartnerId());
		$entryFilter->setAdvancedSearch($distributionAdvancedSearch);
		$entryFilter->set('_order_by', '-created_at');
		if ($period && $period > 0)
			$entryFilter->set('_gte_updated_at', time() - 24*60*60); // last 24 hours
			
		// Dummy query to get the total count
		$baseCriteria = KalturaCriteria::create(entryPeer::OM_CLASS);
		$baseCriteria->add(entryPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_SYSTEM, Criteria::NOT_EQUAL);
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
		$baseCriteria->add(entryPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_SYSTEM, Criteria::NOT_EQUAL);
		$baseCriteria->setLimit($profile->getItemsPerPage() + 1); // get +1 to check if we have next page
		$entryFilter->attachToCriteria($baseCriteria);
		$entries = entryPeer::doSelect($baseCriteria);

		$hasNextPage = false;
		if (count($entries) === ($profile->getItemsPerPage() + 1)) { // we tried to get (itemsPerPage + 1) entries, meaning we have another page
			$hasNextPage = true;
			unset($entries[$profile->getItemsPerPage()]);
		}

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
		if ($hasNextPage)
			$feed->setNextLink($this->getUrl($distributionProfileId, $hash, $page + 1, $period, $nextPageStateLastEntryCreatedAt, $nextPageStateLastEntryIds));

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
			
			$cuePoints = $this->getCuePoints($entry->getPartnerId(), $entry->getId());
			$xml = $feed->getItemXml($fields, $flavorAssets, $thumbAssets, $cuePoints);
			mkdir(dirname($cacheFileName), 0750, true);
			file_put_contents($cacheFileName, $xml);
			}
            $feed->addItemXml($xml);
		}
		
		header('Content-Type: text/xml');
		echo $feed->getXml();
		die;
	}
	
	/**
	 * @action getFeedByEntryId
	 * @disableTags TAG_WIDGET_SESSION,TAG_ENTITLEMENT_ENTRY,TAG_ENTITLEMENT_CATEGORY
	 * @param int $distributionProfileId
	 * @param string $hash
	 * @param string $entryId
	 * @return file
	 */
	public function getFeedByEntryIdAction($distributionProfileId, $hash, $entryId)
	{
		if (!$this->getPartnerId() || !$this->getPartner())
			throw new KalturaAPIException (KalturaErrors::INVALID_PARTNER_ID, $this->getPartnerId());

		$profile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if (!$profile || !$profile instanceof DoubleClickDistributionProfile)
			throw new KalturaAPIException (ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND, $distributionProfileId);

		if ($profile->getStatus() != KalturaDistributionProfileStatus::ENABLED)
			throw new KalturaAPIException (ContentDistributionErrors::DISTRIBUTION_PROFILE_DISABLED, $distributionProfileId);

		if ($profile->getUniqueHashForFeedUrl() != $hash)
			throw new KalturaAPIException (DoubleClickDistributionErrors::INVALID_FEED_URL);

		// Creates entry filter with advanced filter
		$entry = entryPeer::retrieveByPK($entryId);
		if (!$entry || ($entry->getPartnerId() != $this->getPartnerId()))
			throw new KalturaAPIException (KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		// Construct the feed
		$feed = new DoubleClickFeed ('doubleclick_template.xml', $profile);
		$feed->setTotalResult(1);
		$feed->setStartIndex(1);

		$profileUpdatedAt = $profile->getUpdatedAt(null);
		$cacheDir = kConf::get("global_cache_dir") . "feeds/dist_$distributionProfileId/";

		// check cache
		$cacheFileName = $cacheDir . myContentStorage::dirForId($entry->getIntId(), $entry->getId() . ".xml");
		$updatedAt = max($profileUpdatedAt, $entry->getUpdatedAt(null));
		if (file_exists($cacheFileName) && $updatedAt < filemtime($cacheFileName))
		{
			$xml = file_get_contents($cacheFileName);
		}
		else
		{
			$entryDistribution = EntryDistributionPeer::retrieveByEntryAndProfileId($entry->getId(), $profile->getId());
			if (!$entryDistribution)
				throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND, '');

			$fields = $profile->getAllFieldValues($entryDistribution);
			$flavorAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getFlavorAssetIds()));
			$thumbAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getThumbAssetIds()));

			$cuePoints = $this->getCuePoints($entry->getPartnerId(), $entry->getId());
			$xml = $feed->getItemXml($fields, $flavorAssets, $thumbAssets, $cuePoints);
			mkdir(dirname($cacheFileName), 0777, true);
			file_put_contents($cacheFileName, $xml);
		}
		$feed->addItemXml($xml);

		header('Content-Type: text/xml');
		echo $feed->getXml();
		die();
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
