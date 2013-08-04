<?php

abstract class ContentDistributionServiceBase extends KalturaBaseService {
	
	protected $CACHE_SIZE = 100;
	
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
		
		$distributionProfileId = $this->profile->getId();
		$extendItems = $this->profile->getItemXpathsToExtend();
		$enableCache = empty($extendItems);
		$profileUpdatedAt = $this->profile->getUpdatedAt(null);
		$cacheDir = kConf::get("global_cache_dir")."/feeds/dist_$distributionProfileId/";
		$counter = 0;
		
		foreach($entries as $entry)
		{
			// check cache
			$cacheFileName = $cacheDir.myContentStorage::dirForId($entry->getIntId(), $entry->getId().".xml");
			$updatedAt = max($profileUpdatedAt,  $entry->getUpdatedAt(null));
			if ($enableCache && file_exists($cacheFileName) && $updatedAt < filemtime($cacheFileName))
			{
				$xml = file_get_contents($cacheFileName);
			}
			else
			{
				$entryDistribution = EntryDistributionPeer::retrieveByEntryAndProfileId($entry->getId(), $this->profile->getId());
				if (!$entryDistribution)
				{
					KalturaLog::err('Entry distribution was not found for entry ['.$entry->getId().'] and profile [' . $this->profile->getId() . ']');
					continue;
				}
		
				$xml = $this->handleEntry($context, $feed, $entry, $entryDistribution);
				if(is_null($xml)) {
					continue;
				} else if ($enableCache) {
					mkdir(dirname($cacheFileName), 0750, true);
					file_put_contents($cacheFileName, $xml);
				}
			}
				
			$feed->addItemXml($xml);
				
			//to avoid the cache exceeding the memory size
			if ($enableCache && $counter % CACHE_SIZE == 0){
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