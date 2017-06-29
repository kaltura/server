<?php
/**
 * @package api
 * @subpackage v3
 */
class KalturaSyndicationFeedRenderer
{
	const MAX_RETUREND_ENTRIES = 10000;
	const ENTRY_PEER_LIMIT_QUERY = 100;
	const STATIC_PLAYLIST_ENTRY_PEER_LIMIT_QUERY = 500;
	const CACHE_CREATION_TIME_SUFFIX = ".time";
	const CACHE_CREATION_MARGIN = 30;
	
	const CACHE_VERSION = 1;
	const CACHE_EXPIRY = 2592000;		// 30 days

	const PAGE_SIZE_MAX_VALUE = 500;
	
	/**
	 * Maximum number of items to list
	 * @var int
	 */
	private $limit = self::MAX_RETUREND_ENTRIES;

	/**
	 * @var KalturaBaseSyndicationFeed
	 */
	public $syndicationFeed = null;
	
	/**
	 * @var syndicationFeed
	 */
	private $syndicationFeedDb = null;
	
	/**
	 * Array of entry filters, based on playlist or entire entries pool
	 * @var array
	 */
	private $entryFilters = array();
	
	/**
	 * The number of returned entries
	 * @var int
	 */
	private $returnedEntriesCount = 0;
	
	/**
	 * Stores the current page of entries
	 * @var array<entry>
	 */
	private $entriesCurrentPage = null;
	
	/**
	 * The int id of last entry
	 * @var array
	 */
	private $lastEntryIds = array();
	
	/**
	 * The created at of last entry
	 * @var int
	 */
	private $lastEntryCreatedAt = null;
	
	/**
	 * The critria used currently
	 * @var KalturaCriteria
	 */
	private $currentCriteria = null;
	
	/**
	 * Set to true when executed, filters shouldn't be touched
	 * @var bool
	 */
	private $executed = false;
	
	/**
	 * @var KalturaCriteria
	 */
	private $baseCriteria = null;
	
	/**
	 * @var string
	 */
	private $mimeType = null;
	
	/**
	 * @var bool
	 */
	private $staticPlaylist = false;
					
	/**
	 * @var string
	 */
	private $staticPlaylistEntriesIdsOrder = '';
	
	/**
	 * @var string
	 */
	private $feedProcessingKey = null;
	
	/**
	 * @var int
	 */
	private $nextProcessingSetTime = null;

	/**
	 * flag for adding xml-level link for next iteration
 	 *
	 * @var bool
	 */
	private $addLinkForNextIteration = false;
	
	/**
	 * @var kBaseCacheWrapper
	 */
	private $cache = null;
	
	public function __construct($feedId, $feedProcessingKey = null, $ks = null, $state = null)
	{
		$this->feedProcessingKey = $feedProcessingKey;
		
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;

		$microTimeStart = microtime(true);
				
		$this->syndicationFeedDb = $syndicationFeedDB = syndicationFeedPeer::retrieveByPK($feedId);
		if( !$syndicationFeedDB )
			throw new Exception("Feed Id not found");
		kCurrentContext::initKsPartnerUser($ks, $syndicationFeedDB->getPartnerId(), '');
		kPermissionManager::init();
		kEntitlementUtils::initEntitlementEnforcement($syndicationFeedDB->getPartnerId(), $syndicationFeedDB->getEnforceEntitlement());

		// in case ks exists, it's privacy context will be added in entryPeer::setDefaultCriteriaFilter
		$ksObj = kCurrentContext::$ks_object;
		
		if((!$ksObj || !$ksObj->getPrivacyContext()) && !is_null($syndicationFeedDB->getPrivacyContext()) && $syndicationFeedDB->getPrivacyContext() != '')
			kEntitlementUtils::setPrivacyContextSearch($syndicationFeedDB->getPrivacyContext());
			
		$tmpSyndicationFeed = KalturaSyndicationFeedFactory::getInstanceByType($syndicationFeedDB->getType());
		$tmpSyndicationFeed->fromObject($syndicationFeedDB);
		$this->syndicationFeed = $tmpSyndicationFeed;
		
		
		// add partner to default criteria
		myPartnerUtils::addPartnerToCriteria('category', $this->syndicationFeed->partnerId, true);
		myPartnerUtils::addPartnerToCriteria('asset', $this->syndicationFeed->partnerId, true);
		
		myPartnerUtils::resetPartnerFilter('entry');

		if($this->shouldAddNextLink())
		{
			$this->addLinkForNextIteration = true;

			if($this->syndicationFeed->entryFilter && ($this->syndicationFeed->entryFilter->statusEqual || $this->syndicationFeed->entryFilter->statusIn))
			{
				entryPeer::allowDeletedInCriteriaFilter();
			}
		}

		$this->baseCriteria = clone entryPeer::getDefaultCriteriaFilter();
		
		$startDateCriterion = $this->baseCriteria->getNewCriterion(entryPeer::START_DATE, time(), Criteria::LESS_EQUAL);
		$startDateCriterion->addOr($this->baseCriteria->getNewCriterion(entryPeer::START_DATE, null));
		$this->baseCriteria->addAnd($startDateCriterion);
		
		$endDateCriterion = $this->baseCriteria->getNewCriterion(entryPeer::END_DATE, time(), Criteria::GREATER_EQUAL);
		$endDateCriterion->addOr($this->baseCriteria->getNewCriterion(entryPeer::END_DATE, null));
		$this->baseCriteria->addAnd($endDateCriterion);
		
		$this->baseCriteria->addAnd(entryPeer::PARTNER_ID, $this->syndicationFeed->partnerId);
	
		if($this->addLinkForNextIteration)
		{
			$this->addExternalAttachedFilter();

			if($state)
			{
				list($lastCreatedAt, $excludedEntryIds) = $this->extractStateParams($state);
				if($lastCreatedAt)
					$this->baseCriteria->add(entryPeer::CREATED_AT, $lastCreatedAt, Criteria::LESS_EQUAL);
				if($excludedEntryIds)
					$this->baseCriteria->addAnd(entryPeer::ID, $excludedEntryIds, Criteria::NOT_IN);
			}
			entryPeer::allowDeletedInCriteriaFilter();
		}
		else
		{
			$this->baseCriteria->addAnd(entryPeer::TYPE, array(entryType::MEDIA_CLIP, entryType::MIX), Criteria::IN);
		}
		$this->baseCriteria->addAnd(entryPeer::MODERATION_STATUS, array(
			entry::ENTRY_MODERATION_STATUS_REJECTED, 
			entry::ENTRY_MODERATION_STATUS_PENDING_MODERATION), Criteria::NOT_IN);
			
		if($this->syndicationFeed->playlistId)
		{
			$this->entryFilters = myPlaylistUtils::getPlaylistFiltersById($this->syndicationFeed->playlistId);
			foreach($this->entryFilters as $entryFilter)
			{
				$entryFilter->setPartnerSearchScope(baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE);		// partner scope already attached
			}
			
			$playlist = entryPeer::retrieveByPK( $this->syndicationFeed->playlistId );
			if ($playlist)
			{
				if($playlist->getMediaType() != entry::ENTRY_MEDIA_TYPE_XML)
				{
					$this->staticPlaylist = true;
					$this->staticPlaylistEntriesIdsOrder = explode(',', $playlist->getDataContent());
				}
			}
		}
		else
		{
			$this->entryFilters = array();
		}
		
		$this->cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_LOCK);
		
		$microTimeEnd = microtime(true);
		KalturaLog::info("syndicationFeedRenderer- initialization done [".($microTimeEnd - $microTimeStart)."]");		
	}
	
	private function extractStateParams($state)
	{
		$decodedState = base64_decode($state);

		if($decodedState !== false && strpos($decodedState, ","))
		{
			list($lastCreatedAt, $excludedEntryIdsStr) = explode(",", $decodedState);
			$excludedEntryIdsArr = explode(":", $excludedEntryIdsStr);
			return array($lastCreatedAt,$excludedEntryIdsArr);
		}
		return array(null, null);
	}

	private function encodeStateParams($lastCreateVal, array $entryIds)
	{
		$str = $lastCreateVal . "," . implode(":", $entryIds);
		return base64_encode($str);
	}

	private function shouldAddNextLink()
	{
		if(($this->syndicationFeed->type == KalturaSyndicationFeedType::KALTURA || $this->syndicationFeed->type == KalturaSyndicationFeedType::KALTURA_XSLT) && (!$this->syndicationFeed->playlistId && $this->syndicationFeed->pageSize && $this->syndicationFeed->pageSize <= self::PAGE_SIZE_MAX_VALUE))
		{
			return true;
		}
		return false;
	}

	public function addFlavorParamsAttachedFilter()
	{
		if($this->executed)
			return;
			
		if(!$this->syndicationFeed->flavorParamId)
			return;
			
		$entryFilter = new entryFilter();
		$entryFilter->setPartnerSearchScope(baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE);		// partner scope already attached
		$entryFilter->setFlavorParamsMatchOr($this->syndicationFeed->flavorParamId);
		$entryFilter->attachToCriteria($this->baseCriteria);
	}
	
	public function addExternalAttachedFilter()
	{
		$entryFilter = $this->syndicationFeed->entryFilter;

		if(!$entryFilter)
			return;
		
		$coreFilter = new entryFilter();
		$entryFilter->toObject($coreFilter);
		$coreFilter->transformFieldsToRelative();
		$this->addFilter($coreFilter);
	}

	public function addEntryAttachedFilter($entryId)
	{
		if($this->executed)
			return;
		
		$entryFilter = new entryFilter();
		$entryFilter->setIdEquel($entryId);		
		$this->addFilter($entryFilter);
	}
	
	public function addFilter(entryFilter $entryFilter)
	{
		if($this->executed)
			return;
			
		$entryFilter->setPartnerSearchScope(baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE);		// partner scope already attached
		$entryFilter->attachToCriteria($this->baseCriteria);
	}
	
	public function getEntriesCount()
	{
		if($this->executed)
			return null;
		
		if(!count($this->entryFilters))
		{
			$c = clone $this->baseCriteria;
			$c->applyFilters();
			return $c->getRecordsCount();
		}
		
		$count = 0;
		foreach($this->entryFilters as $entryFilter)
		{
			$c = clone $this->baseCriteria;
			$entryFilter->attachToCriteria($c);
			$c->applyFilters();
			$count += $c->getRecordsCount();
		}
		return $count;
	}
	
	public function getEntriesIds()
	{
		if($this->executed)
			return array();
		
		if(!count($this->entryFilters))
		{
			$c = clone $this->baseCriteria;
			$c->applyFilters();
			$c->clearSelectColumns();
			$c->addSelectColumn(entryPeer::ID);
			
			$rs = entryPeer::doSelectStmt($c);
			return $rs->fetchAll(PDO::FETCH_COLUMN);
		}
		
		$entries = array();
		foreach($this->entryFilters as $entryFilter)
		{
			$c = clone $this->baseCriteria;
			$entryFilter->attachToCriteria($c);
			$c->applyFilters();
			$c->clearSelectColumns();
			$c->addSelectColumn(entryPeer::ID);
			
			$rs = entryPeer::doSelectStmt($c);
			$moreEntries = $rs->fetchAll(PDO::FETCH_COLUMN);
			$entries += $moreEntries;
		}
		return $entries;
	}
	
	public function getNextEntry()
	{
		if(!$this->executed)
		{
			$this->entriesCurrentPage = array();
			$this->lastEntryIds = array();
			$this->lastEntryCreatedAt = 0;
		}
		
		++$this->returnedEntriesCount;
		if ($this->returnedEntriesCount > $this->limit)
			return false;
				
		$entry = current($this->entriesCurrentPage);
		if($entry)
		{
			$orderByFieldValue = $this->getOrderByFieldValue($entry);
			next($this->entriesCurrentPage);
			if ($this->lastEntryCreatedAt > $orderByFieldValue)
				$this->lastEntryIds = array();
			
			$this->lastEntryIds[] = $entry->getId();
			$this->lastEntryCreatedAt = $orderByFieldValue;
			return $entry;
		}
			
		$this->fetchNextPage();
		if(!$this->entriesCurrentPage)
		{
			$this->lastEntryIds = array();
			$this->lastEntryCreatedAt = null;
			return false;
		}
	
		$entry = current($this->entriesCurrentPage);
		if($entry)
		{
			$orderByFieldValue = $this->getOrderByFieldValue($entry);
			next($this->entriesCurrentPage);
			if ($this->lastEntryCreatedAt > $orderByFieldValue)
				$this->lastEntryIds = array();
			
			$this->lastEntryIds[] = $entry->getId();
			$this->lastEntryCreatedAt = $orderByFieldValue;
		}
		else
		{
			$this->lastEntryIds = array();
			$this->lastEntryCreatedAt = null;
		}
		return $entry;
	}
	
	private function fetchNextPage()
	{
		if($this->executed && $this->staticPlaylist)
			return;

		$this->entriesCurrentPage = null;
		kMemoryManager::clearMemory();
		
		if($this->currentCriteria)
		{
			if($this->lastEntryCreatedAt)
			{
				$this->currentCriteria->add($this->getOrderByColumn(), $this->lastEntryCreatedAt, Criteria::LESS_EQUAL);
			}
			
			if (count($this->lastEntryIds))
			{
				$this->currentCriteria->add(entryPeer::ID, $this->lastEntryIds, Criteria::NOT_IN);
			}
		}
		else
		{
			$this->currentCriteria = $this->getNextCriteria();
			
			if(!$this->currentCriteria)
				return;
		}
			
		$nextPage = entryPeer::doSelect($this->currentCriteria);
		if(!count($nextPage)) // move to the next criteria
		{
			$this->currentCriteria = $this->getNextCriteria();
			if(!$this->currentCriteria)
				return;
			
			$nextPage = entryPeer::doSelect($this->currentCriteria);
		}
		
		if(!count($nextPage)) // finished all criterias and pages
			return;
		
		if ($this->staticPlaylist)
		{
			//order the entries by static (AKA manual) entries order
			$nextPageEntries = array();
			
			foreach ($nextPage as $entry)
				$nextPageEntries[$entry->getId()] = $entry;
			
			$nextPage = array();
			
			foreach ($this->staticPlaylistEntriesIdsOrder as $entryId)
			{
				if (isset($nextPageEntries[$entryId])) 	
					$nextPage[] = $nextPageEntries[$entryId];
			}
		} 
			
		$this->entriesCurrentPage = $nextPage;
		reset($this->entriesCurrentPage);
	}
	
	/**
	 * @return KalturaCriteria
	 */
	private function getNextCriteria()
	{
		if(!$this->executed && count($this->entryFilters))
			reset($this->entryFilters);
			
		$this->executed = true;

		$c = clone $this->baseCriteria;
		
		if($this->staticPlaylist)
		{
			$c->setLimit(self::STATIC_PLAYLIST_ENTRY_PEER_LIMIT_QUERY);
		}
		elseif($this->addLinkForNextIteration)
		{
			$c->setLimit($this->syndicationFeed->pageSize);
		}
		else 
		{
			$c->setLimit(min(self::ENTRY_PEER_LIMIT_QUERY, $this->limit));
		}

		if(!count($this->entryFilters))
		{
			if($this->currentCriteria) // already executed the base criteria
				return null;
				
			$c->clearOrderByColumns();
			$c->addDescendingOrderByColumn($this->getOrderByColumn());
			$c->dontCount();
			
			return $c; // return the base criteria
		}
			
		$filter = current($this->entryFilters);
		if(!$filter) // no more filters found
			return null;
			
		next($this->entryFilters);
			
		$filter->clearLimit();
		$filter->clearOrder();
		$filter->attachToCriteria($c);
		
		$c->clearOrderByColumns();
		$c->addDescendingOrderByColumn($this->getOrderByColumn());
		$c->dontCount();
		
		return $c;
	}

	public function execute($limit = 0)
	{
		if($this->executed)
			return;

		if($this->addLinkForNextIteration)
			$this->limit = $this->syndicationFeed->pageSize; 
		elseif ($limit)
			$this->limit = $limit;
			
		$microTimeStart = microtime(true);
		
		$renderer = KalturaSyndicationFeedFactory::getRendererByType($this->syndicationFeed->type);
		$renderer->init($this->syndicationFeed, $this->syndicationFeedDb, $this->mimeType);
		
		header($renderer->handleHttpHeader());
		echo $renderer->handleHeader();
		
		$cacheStore = null;
		if($renderer->shouldEnableCache())
			$cacheStore = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_FEED_ENTRY);
		
		$protocol = infraRequestUtils::getProtocol();
		$cachePrefix = "feed_{$this->syndicationFeed->id}/{$protocol}/entry_";
		$feedUpdatedAt = $this->syndicationFeedDb->getUpdatedAt(null);

		$e = null;
		$kalturaFeed = $this->syndicationFeed->type == KalturaSyndicationFeedType::KALTURA || in_array($this->syndicationFeed->type, array(KalturaSyndicationFeedType::KALTURA_XSLT, KalturaSyndicationFeedType::ROKU_DIRECT_PUBLISHER, KalturaSyndicationFeedType::OPERA_TV_SNAP));
		$nextEntry = $this->getNextEntry();
	
		$lastCreatedAtVal = null;
		$tempCreatedAtVal = null;
		$excludedEntryIds = array();	

		while($nextEntry)
		{
			if($this->addLinkForNextIteration)
			{
				$lastCreatedAtVal = $nextEntry->getCreatedAt(null);
				$excludedEntryId = $nextEntry->getId();
				if($lastCreatedAtVal == $tempCreatedAtVal)
					$excludedEntryIds[] = $excludedEntryId;
				else
					$excludedEntryIds = array($excludedEntryId);

				$tempCreatedAtVal = $lastCreatedAtVal; 
			}
			$this->enableApcProcessingFlag();
			$entry = $nextEntry;
			$nextEntry = $this->getNextEntry();

			if(is_null($this->lastEntryCreatedAt))
				$lastCreatedAtVal = null;

			// in case no video player is requested by user and the entry is mix, skip it	
			if ($entry->getType() === entryType::MIX && !$this->syndicationFeed->allowEmbed) 
				continue;
				
			$xml = false;

			// check cache
			$updatedAt = max($feedUpdatedAt,  $entry->getUpdatedAt(null));

			if ($cacheStore) {	
				$cacheKey = $cachePrefix.str_replace("_", "-", $entry->getId()).self::CACHE_VERSION; // replace _ with - so cache folders will be created with random entry id and not 0_/1_
				$cacheTime = $cacheStore->get($cacheKey.self::CACHE_CREATION_TIME_SUFFIX);
				if ($cacheTime !== false && $cacheTime > $updatedAt + self::CACHE_CREATION_MARGIN)
					$xml = $cacheStore->get($cacheKey);
			}

			if ($xml === false)
			{	
				$e = null;
				if(!$kalturaFeed) {
					$e = new KalturaMediaEntry();
					$e->fromObject($entry);
				}
				
				$flavorAssetUrl = is_null($e) ? null : $this->getFlavorAssetUrl($e);
				
				if(!$kalturaFeed && $entry->getType() !== entryType::MIX && is_null($flavorAssetUrl)) {
					$xml = ""; // cache empty result to avoid checking getFlavorAssetUrl next time
				} else {
					$xml = $renderer->handleBody($entry, $e, $flavorAssetUrl);
				}
				
				if ($cacheStore)
				{
					$cacheStore->set($cacheKey.self::CACHE_CREATION_TIME_SUFFIX, time(), self::CACHE_EXPIRY);
					$cacheStore->set($cacheKey, $xml, self::CACHE_EXPIRY);
				}
			} 
			echo $renderer->finalize($xml, $nextEntry !== false);
		}
		
		if($this->addLinkForNextIteration)
		{
			entryPeer::blockDeletedInCriteriaFilter();
			if($lastCreatedAtVal)
			{
				$currState = $this->encodeStateParams($lastCreatedAtVal, $excludedEntryIds);
				$renderer->setState($currState);
			}
		}

		echo $renderer->handleFooter();
		
		if ($this->feedProcessingKey && $this->cache)
			$this->cache->delete($this->feedProcessingKey);
				
		$microTimeEnd = microtime(true);
		KalturaLog::info("syndicationFeedRenderer- render time for ({$this->syndicationFeed->type}) is " . ($microTimeEnd - $microTimeStart));
	}
	
	/*
	 * Enable the processing flag in APC to prevent additional requests for this feed
	 */
	private function enableApcProcessingFlag() {
		$currentTime = time();
		if ($this->feedProcessingKey && $this->cache && $currentTime > $this->nextProcessingSetTime)
		{
			$this->cache->set($this->feedProcessingKey, true, 60);
			$this->nextProcessingSetTime = $currentTime + 30;
		}
	}
	
	private function getExternalStorageUrl(Partner $partner, flavorAsset $flavorAsset, FileSyncKey $key)
	{
		if(!$partner->getStorageServePriority() || $partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY)
			return null;
			
		if($partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_FIRST)
			if(kFileSyncUtils::getReadyInternalFileSyncForKey($key)) // check if having file sync on kaltura dcs
				return null;
				
		$fileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($key);
		if(!$fileSync)
			return null;
			
		$storage = StorageProfilePeer::retrieveByPK($fileSync->getDc());
		if(!$storage)
			return null;
			
		if($this->syndicationFeedDb->getServePlayManifest())
		{
			$deliveryProfile = DeliveryProfilePeer::getRemoteDeliveryByStorageId(
					DeliveryProfileDynamicAttributes::init($storage->getId(), $flavorAsset->getEntryId(), PlaybackProtocol::HTTP, "https"));
			$clientTag = 'feed:' . $this->syndicationFeedDb->getId();
		
			if (is_null($deliveryProfile))
				$url = infraRequestUtils::PROTOCOL_HTTP . "://" . kConf::get("cdn_api_host");
			else
				$url = requestUtils::getApiCdnHost();
		
			$url .= $flavorAsset->getPlayManifestUrl($clientTag, $storage->getId());
		}
		else
		{
			$dpda = new DeliveryProfileDynamicAttributes();
			$urlManager = DeliveryProfilePeer::getRemoteDeliveryByStorageId(
					DeliveryProfileDynamicAttributes::init($fileSync->getDc(), $flavorAsset->getEntryId()), null, $flavorAsset);
			$url = ltrim($urlManager->getFileSyncUrl($fileSync),'/');
			if (strpos($url, "://") === false){
				$url = rtrim($urlManager->getUrl(), "/") . "/".$url ;
			}
		}
		
		return $url;
	}
	
	private function getFlavorAssetUrl($kalturaEntry)
	{
		$partner = PartnerPeer::retrieveByPK($this->syndicationFeed->partnerId);
		if(!$partner)
			return null;
	
		$flavorAsset = assetPeer::retrieveByEntryIdAndParams($kalturaEntry->id,$this->syndicationFeed->flavorParamId);
		if (!$flavorAsset)
			return null;
					
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$externalStorageUrl = $this->getExternalStorageUrl($partner, $flavorAsset, $syncKey);
		if($externalStorageUrl)
			return $externalStorageUrl;
			
		if($partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY)
			return null;
		
		if($this->syndicationFeedDb->getServePlayManifest())
		{
			$shouldAddKtToken = false;
			if($this->syndicationFeed->type == KalturaSyndicationFeedType::ITUNES)
			{
				$entry = $flavorAsset->getentry();
				$accessControl = $entry->getaccessControl();
				if ($accessControl && $accessControl->hasRules())
					$shouldAddKtToken = true;
			}

			$cdnHost = requestUtils::getApiCdnHost();
			$clientTag = 'feed:' . $this->syndicationFeedDb->getId();
			$url = $cdnHost . $flavorAsset->getPlayManifestUrl($clientTag, null, PlaybackProtocol::HTTP , $shouldAddKtToken);
		}
		else
		{
			$urlManager = DeliveryProfilePeer::getDeliveryProfile($flavorAsset->getEntryId());
			$urlManager->initDeliveryDynamicAttributes(null, $flavorAsset);
			$protocol = requestUtils::getProtocol();
			if(!$urlManager->isProtocolSupported($protocol)){
				$protocol = ($protocol == 'http' ? 'https' : 'http');
				if(!$urlManager->isProtocolSupported($protocol)){
					$protocol = 'http';
				}
			}
			$url = $protocol . '://' . $urlManager->getFullAssetUrl($flavorAsset);
		}
		
		return $url;
	}
	
	private function getOrderByColumn()
	{
		if ($this->syndicationFeed->entriesOrderBy === 'recent')
			return entryPeer::AVAILABLE_FROM;

		return entryPeer::CREATED_AT; // the default
	}

	private function getOrderByFieldValue(entry $entry)
	{
		if ($this->syndicationFeed->entriesOrderBy === 'recent')
			return $entry->getAvailableFrom(null);

		return $entry->getCreatedAt(null); // the default
	}
	
	public function getSyndicationFeedDb() {
		return $this->syndicationFeedDb;
	}

	public function getReturnedEntriesCount()
	{
		return $this->returnedEntriesCount;
	}
}
