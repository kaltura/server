<?php
/**
 * DoubleClick Service
 *
 * @service doubleClick
 * @package plugins.doubleClickDistribution
 * @subpackage api.services
 */
class DoubleClickService extends ContentDistributionServiceBase
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
		$context = new ContentDistributionServiceContext();
		$context->page = (!$page || $page < 1) ? 1 : $page;
		$context->period = $period;
		$context->state = $state;
		$context->ignoreScheduling = $ignoreScheduling;
		$context->hash = $hash;
		$this->generateFeed($context, $distributionProfileId, $hash);
	}
	
	public function getProfileClass() {
		return new DoubleClickDistributionProfile();
	}
	
	protected function fillStateDependentFields($context) {
		$context->stateLastEntryCreatedAt = null;
		$context->stateLastEntryIds = array();
		if ($context->state)
		{
			$stateDecoded = base64_decode($context->state);
			if (strpos($stateDecoded, '|') !== false)
			{
				$stateExploded = explode('|', $stateDecoded);
				$context->stateLastEntryCreatedAt = $stateExploded[0];
				$stateLastEntryIdsStr =  $stateExploded[1];
				$context->stateLastEntryIds = explode(',', $stateLastEntryIdsStr);
			}
		}
	}
	protected function fillnextStateDependentFields ($context, $entries) {
		// Find the new state
		$context->nextPageStateLastEntryCreatedAt = $context->stateLastEntryCreatedAt;
		$context->nextPageStateLastEntryIds = $context->stateLastEntryIds;
		foreach($entries as $entry)
		{
			if ($context->nextPageStateLastEntryCreatedAt > $entry->getCreatedAt(null))
				$context->nextPageStateLastEntryIds = array();
	
			$context->nextPageStateLastEntryIds[] = $entry->getId();
			$context->nextPageStateLastEntryCreatedAt = $entry->getCreatedAt(null);
		}
	}
	
	protected function getEntryFilter($context, $keepScheduling = true)
	{
		$keepScheduling = ($keepScheduling !== true && $this->profile->getIgnoreSchedulingInFeed() !== true);
		$entryFilter = parent::getEntryFilter($context, $keepScheduling);
		$entryFilter->set('_order_by', '-created_at');
		if ($this->period && $this->period > 0)
			$entryFilter->set('_gte_updated_at', time() - 24*60*60); // last 24 hours
		
		
		// Dummy query to get the total count
		$baseCriteria = KalturaCriteria::create(entryPeer::OM_CLASS);
		$baseCriteria->add(entryPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_SYSTEM, Criteria::NOT_EQUAL);
		$baseCriteria->setLimit(1);
		$entryFilter->attachToCriteria($baseCriteria);
		$context->totalCount = entryPeer::doCount($baseCriteria);
		
		// Add the state data to proceed to next page
		$this->fillStateDependentFields();
		
		if ($context->stateLastEntryCreatedAt)
			$entryFilter->set('_lte_created_at', $context->stateLastEntryCreatedAt);
		if ($context->LastEntryIds)
			$entryFilter->set('_notin_id', $context->stateLastEntryIds);
		
		return $entryFilter;
	}
	
	protected function getEntries($context, $orderBy = null, $limit = null) {
		$context->hasNextPage = false;
		$entries = parent::getEntries($context, null, $this->profile->getItemsPerPage() + 1); // get +1 to check if we have next page
		if (count($entries) === ($this->profile->getItemsPerPage() + 1)) { // we tried to get (itemsPerPage + 1) entries, meaning we have another page
			$context->hasNextPage = true;
			unset($entries[$this->profile->getItemsPerPage()]);
		}
		
		$this->fillnextStateDependentFields($context, $entries);
		return $entries;
	}
	
	protected function createFeedGenerator($context) 
	{
		// Construct the feed
		$distributionProfileId = $this->profile->getId();
		$feed = new DoubleClickFeed('doubleclick_template.xml', $this->profile);
		$feed->setTotalResult($context->totalCount);
		$feed->setStartIndex(($context->page - 1) * $this->profile->getItemsPerPage() + 1);
		$feed->setSelfLink($this->getUrl($distributionProfileId, $context->hash, $context->page, $context->period, $context->stateLastEntryCreatedAt, $context->stateLastEntryIds));
		if ($context->hasNextPage)
			$feed->setNextLink($this->getUrl($distributionProfileId, $context->hash, $context->page + 1, $context->period, $context->$nextPageStateLastEntryCreatedAt, $context->$nextPageStateLastEntryIds));
		
		return $feed;
	}
	
	protected function handleEntry($context, $feed,entry $entry, Entrydistribution $entryDistribution)
	{
		$fields = $this->profile->getAllFieldValues($entryDistribution);
		$flavorAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getFlavorAssetIds()));
		$thumbAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getThumbAssetIds()));
		
		$cuePoints = $this->getCuePoints($entry->getPartnerId(), $entry->getId());
		return $feed->getItemXml($fields, $flavorAssets, $thumbAssets, $cuePoints);
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
		$this->validateRequest($distributionProfileId, $hash);

		// Creates entry filter with advanced filter
		$entry = entryPeer::retrieveByPK($entryId);
		if (!$entry || ($entry->getPartnerId() != $this->getPartnerId()))
			throw new KalturaAPIException (KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		// Construct the feed
		$feed = new DoubleClickFeed ('doubleclick_template.xml', $this->profile);
		$feed->setTotalResult(1);
		$feed->setStartIndex(1);
		
		$entries = array();
		$entries[] = $entry;
		$context = new ContentDistributionServiceContext();
		$this->handleEntries($context, $feed, $entries);
		$this->doneFeedGeneration($context, $feed);
		
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
