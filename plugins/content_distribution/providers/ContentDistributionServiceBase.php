<?php

abstract class ContentDistributionServiceBase extends KalturaBaseService {
	
	const CACHE_CREATION_TIME_SUFFIX = ".time";
	const CACHE_SIZE = 100;
	
	/** Holds the distribution profile instance */
	protected $profile;
	
	/**
	 * This function is the actual function that generates the feed
	 * @param unknown_type $context
	 * @param unknown_type $distributionProfileId
	 * @param unknown_type $hash
	 */
	public function generateFeed($context, $distributionProfileId, $hash) {
		$this->validateRequest($distributionProfileId, $hash);
	
		$entries = $this->getEntries($context, null, null);
		$feed = $this->createFeedGenerator($context);
		$this->handleEntries($context, $feed, $entries);
		$this->doneFeedGeneration($context, $feed);
	}

	/**
	 * @return an instance of the supported distribution profile
	 */
	protected abstract function getProfileClass();
	
	/**
	 * Creates and initializes the feed generator
	 * @return the matching feed generator implementation
	 */
	protected abstract function createFeedGenerator($context);
	
	/**
	 * This function handles a single entry within a specific feed
	 * @param $feed The feed we want to add the entry to
	 * @param entry $entry The entry we want to handle
	 */
	protected abstract function handleEntry($context, $feed, entry $entry, Entrydistribution $entryDistribution);
	
	/**
	 * Validates whether a we can fullfill the get feed request.
	 * @throws KalturaAPIException In case we can't fullfill the request
	 */
	protected function validateRequest($distributionProfileId, $hash) 
	{
		if (!$this->getPartnerId() || !$this->getPartner())
			throw new KalturaAPIException(KalturaErrors::INVALID_PARTNER_ID, $this->getPartnerId());
			
		$this->profile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		$profileClass = $this->getProfileClass();
		if (!$this->profile || !$this->profile instanceof $profileClass)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND, $distributionProfileId);
	
		if ($this->profile->getStatus() != KalturaDistributionProfileStatus::ENABLED)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_DISABLED, $distributionProfileId);
	
		if ($this->profile->getUniqueHashForFeedUrl() != $hash)
			throw new KalturaAPIException(ContentDistributionErrors::INVALID_FEED_URL);
	}
	
	/**
	 * Returns the filter by which we will query the entries to generate the feed
	 * @param $context
	 * @param boolean $keepScheduling whether we should add the AFTER_SUNRISE condition
	 */
	protected function getEntryFilter($context, $keepScheduling = true)
	{
		
		// "Creates advanced filter on distribution profile
		$distributionAdvancedSearch = new ContentDistributionSearchFilter();
		$distributionAdvancedSearch->setDistributionProfileId($this->profile->getId());
		if ($keepScheduling)
			$distributionAdvancedSearch->setDistributionSunStatus(EntryDistributionSunStatus::AFTER_SUNRISE);
		$distributionAdvancedSearch->setEntryDistributionStatus(EntryDistributionStatus::READY);
		$distributionAdvancedSearch->setEntryDistributionFlag(EntryDistributionDirtyStatus::NONE);
		$distributionAdvancedSearch->setHasEntryDistributionValidationErrors(false);
			
		//Creates entry filter with advanced filter
		$entryFilter = new entryFilter();
		$entryFilter->setStatusEquel(entryStatus::READY);
		$entryFilter->setModerationStatusNot(entry::ENTRY_MODERATION_STATUS_REJECTED);
		$entryFilter->setPartnerSearchScope($this->getPartnerId());
		$entryFilter->setAdvancedSearch($distributionAdvancedSearch);
		
		return $entryFilter;
	}
	
	/**
	 * Queries for the entries from which we will generate the filter
	 * @param string $orderBy The field according to we'd like to sort the results
	 * @param int $limit the maximal number of results
	 */
	protected function getEntries($context, $orderBy = null, $limit = null) 
	{
		$baseCriteria = KalturaCriteria::create(entryPeer::OM_CLASS);
		$baseCriteria->add(entryPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_SYSTEM, Criteria::NOT_EQUAL);
		if(!is_null($limit))
			$baseCriteria->setLimit($limit);
		if(!is_null($orderBy)) {
			$baseCriteria->addDescendingOrderByColumn($orderBy);
		}
		$entryFilter = $this->getEntryFilter($context, $context->keepScheduling);
		$entryFilter->attachToCriteria($baseCriteria);
		
		return entryPeer::doSelect($baseCriteria);
	}
	
	protected function handleEntries($context, $feed, array $entries) {
		
		$cacheStore = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_FEED_ENTRY) ;
		$cachePrefix = "dist_" . ($this->profile->getId()) . "/entry_";
		$profileUpdatedAt = $this->profile->getUpdatedAt(null);
		
		$extendItems = $this->profile->getItemXpathsToExtend();
		$enableCache = empty($extendItems);
		if ($enableCache)
			$cacheStore = null;
		
		$counter = 0;
		
		foreach($entries as $entry)
		{
			$xml = null;
			$cacheFileName = $cachePrefix . str_replace("_", "-",  $entry->getId()); // replace _ with - so cache folders will be created with random entry id and not 0_/1_
			
			if($enableCache) {
				$cacheTime = $cacheStore->get($cacheFileName . self::CACHE_CREATION_TIME_SUFFIX);
				$updatedAt = max($profileUpdatedAt,  $entry->getUpdatedAt(null));
				if ($updatedAt < $cacheTime) {
					$xml = $cacheStore->get($enableCache);
				}
			}
			
			if(is_null($xml))
			{
				$entryDistribution = EntryDistributionPeer::retrieveByEntryAndProfileId($entry->getId(), $this->profile->getId());
				if (!$entryDistribution)
				{
					KalturaLog::err('Entry distribution was not found for entry ['.$entry->getId().'] and profile [' . $this->profile->getId() . ']');
					continue;
				}
		
				$xml = $this->handleEntry($context, $feed, $entry, $entryDistribution);
				if(!is_null($xml) && $enableCache) {
					$cacheStore->set($cacheFileName . self::CACHE_CREATION_TIME_SUFFIX, time());
					$cacheStore->set($cacheFileName, $xml);
				}
			}
				
			$feed->addItemXml($xml);
				
			//to avoid the cache exceeding the memory size
			if ($counter % self::CACHE_SIZE == 0){
				kMemoryManager::clearMemory();
				$counter++;
			}
		}
	}
	
	/**
	 * This function terminates the feed generation and returns it
	 * @param $feed
	 */
	protected function doneFeedGeneration ($context, $feed) {
		header('Content-Type: text/xml');
		echo $feed->getXml();
		die;
	}
}

?>