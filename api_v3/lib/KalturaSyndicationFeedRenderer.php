<?php
/**
 * @package api
 * @subpackage v3
 */
class KalturaSyndicationFeedRenderer
{
	const MAX_RETUREND_ENTRIES = 10000;
	const ENTRY_PEER_LIMIT_QUERY = 500;
	const LEVEL_INDENTATION = '  ';
	const CACHE_CREATION_TIME_SUFFIX = ".time";
	const CACHE_CREATION_MARGIN = 30;
	
	const STATE_HEADER = 1;
	const STATE_BODY = 2;
	const STATE_FOOTER = 3;

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
	
	public function __construct($feedId)
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;

		$microTimeStart = microtime(true);
		KalturaLog::info("syndicationFeedRenderer- initialize ");
		
		// initialize the database for all services
		DbManager::setConfig(kConf::getDB());
		DbManager::initialize();
		
		$this->syndicationFeedDB = $syndicationFeedDB = syndicationFeedPeer::retrieveByPK($feedId);
		if( !$syndicationFeedDB )
			throw new Exception("Feed Id not found");
		
		kEntitlementUtils::initEntitlementEnforcement($syndicationFeedDB->getPartnerId(), $syndicationFeedDB->getEnforceEntitlement());

		if(!is_null($syndicationFeedDB->getPrivacyContext()) && $syndicationFeedDB->getPrivacyContext() != '')
			kEntitlementUtils::setPrivacyContextSearch($syndicationFeedDB->getPrivacyContext());
			
		$tmpSyndicationFeed = KalturaSyndicationFeedFactory::getInstanceByType($syndicationFeedDB->getType());
		$tmpSyndicationFeed->fromObject($syndicationFeedDB);
		$this->syndicationFeed = $tmpSyndicationFeed;
		
		
		// add partner to default criteria
		myPartnerUtils::addPartnerToCriteria('category', $this->syndicationFeed->partnerId, true);
		myPartnerUtils::addPartnerToCriteria('asset', $this->syndicationFeed->partnerId, true);
		
		myPartnerUtils::resetPartnerFilter('entry');

		$this->baseCriteria = entryPeer::getDefaultCriteriaFilter();
		
		$startDateCriterion = $this->baseCriteria->getNewCriterion(entryPeer::START_DATE, time(), Criteria::LESS_EQUAL);
		$startDateCriterion->addOr($this->baseCriteria->getNewCriterion(entryPeer::START_DATE, null));
		$this->baseCriteria->addAnd($startDateCriterion);
		
		$endDateCriterion = $this->baseCriteria->getNewCriterion(entryPeer::END_DATE, time(), Criteria::GREATER_EQUAL);
		$endDateCriterion->addOr($this->baseCriteria->getNewCriterion(entryPeer::END_DATE, null));
		$this->baseCriteria->addAnd($endDateCriterion);
		
		$entryFilter = new entryFilter();
		$entryFilter->setPartnerSearchScope($this->syndicationFeed->partnerId);
		$entryFilter->setStatusEquel(entryStatus::READY);
		$entryFilter->setTypeIn(array(entryType::MEDIA_CLIP, entryType::MIX));
		$entryFilter->setModerationStatusNotIn(array(
			entry::ENTRY_MODERATION_STATUS_REJECTED, 
			entry::ENTRY_MODERATION_STATUS_PENDING_MODERATION));
			
		$entryFilter->attachToCriteria($this->baseCriteria);
			
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
			
		$microTimeEnd = microtime(true);
		KalturaLog::info("syndicationFeedRenderer- initialization done [".($microTimeEnd - $microTimeStart)."]");		
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
		$c->setLimit(self::ENTRY_PEER_LIMIT_QUERY);

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

		if ($limit)
			$this->limit = $limit;
			
		$microTimeStart = microtime(true);

		$renderers = array(
			KalturaSyndicationFeedType::GOOGLE_VIDEO => "renderGoogleVideoFeed",
			KalturaSyndicationFeedType::ITUNES => "renderITunesFeed",
			KalturaSyndicationFeedType::TUBE_MOGUL => "renderTubeMogulFeed",
			KalturaSyndicationFeedType::YAHOO => "renderYahooFeed",
			KalturaSyndicationFeedType::KALTURA => "renderKalturaFeed",
			KalturaSyndicationFeedType::KALTURA_XSLT => "renderKalturaFeed"
		);
		
		$renderer = array($this, $renderers[$this->syndicationFeed->type]);
		
		call_user_func($renderer, self::STATE_HEADER);
		
		$cacheStore = null;	// kCacheManager::getCache(kCacheManager::FS_ENTRY);
		$cachePrefix = "feed_{$this->syndicationFeed->id}/entry-";
		$feedUpdatedAt = $this->syndicationFeedDB->getUpdatedAt(null);

		$e = null;
		$kalturaFeed = $this->syndicationFeed->type == KalturaSyndicationFeedType::KALTURA || $this->syndicationFeed->type == KalturaSyndicationFeedType::KALTURA_XSLT;

		$nextEntry = $this->getNextEntry();
		while($nextEntry)
		{
			$entry = $nextEntry;
			$nextEntry = $this->getNextEntry();

			// in case no video player is requested by user and the entry is mix, skip it	
			if ($entry->getType() === entryType::MIX && !$this->syndicationFeed->allowEmbed)
				continue;
				
			$xml = false;

			// check cache
			$updatedAt = max($feedUpdatedAt,  $entry->getUpdatedAt(null));

			if ($cacheStore)
			{	
				$cacheKey = $cachePrefix.str_replace("_", "-", $entry->getId()); // replace _ with - so cache folders will be created with random entry id and not 0_/1_
				$cacheTime = $cacheStore->get($cacheKey.self::CACHE_CREATION_TIME_SUFFIX);
				if ($cacheTime !== false && $cacheTime > $updatedAt + self::CACHE_CREATION_MARGIN)
				{
					$xml = $cacheStore->get($cacheKey);
					if ($xml !== false) // valid entry found in cache
					{
						echo $xml;
						continue;
					}
				}
			}

			$e = null;
			if (!$kalturaFeed) // non kaltura feed use the KalturaMediaEntry
			{
				$e = new KalturaMediaEntry();
				$e->fromObject($entry);
				// non kaltura feeds require a flavor asset url
				if (!$kalturaFeed && $entry->getType() !== entryType::MIX && $this->getFlavorAssetUrl($e) == null)
					$xml = ""; // cache empty result to avoid checking getFlavorAssetUrl next time
					
			}
	
			if ($xml === false)
			{	
				ob_start();
				call_user_func($renderer, self::STATE_BODY, $entry, $e, $nextEntry !== false);
				$xml = ob_get_flush();
			}

			if ($cacheStore)
			{
				$cacheStore->set($cacheKey.self::CACHE_CREATION_TIME_SUFFIX, time());
				$cacheStore->set($cacheKey, $xml);
			}
		}
		
		call_user_func($renderer, self::STATE_FOOTER);
		
		$microTimeEnd = microtime(true);
		KalturaLog::info("syndicationFeedRenderer- render time for ({$this->syndicationFeed->type}) is " . ($microTimeEnd - $microTimeStart));
	}
	
	private function stringToSafeXml($string, $now = false)
	{
		$string = @iconv('utf-8', 'utf-8', $string);
		$safe = kString::xmlEncode($string);
		return $safe;
	}
	
	private function secondsToWords($seconds)
	{
		/*** return value ***/
		$ret = "";
		
		/*** get the hours ***/
		$hours = intval(intval($seconds) / 3600);
		if($hours > 0)
		{
		    $ret .= "$hours:";
		}
		/*** get the minutes ***/
		$minutes = (intval($seconds) / 60)%60;
		$ret .= ($minutes >= 10 || $minutes == 0)? "$minutes:": "0$minutes:";
		
		/*** get the seconds ***/
		$seconds = intval($seconds)%60;
		$ret .= ($seconds >= 10)? "$seconds": "0$seconds";
		
		return $ret;
	}
	
	private function renderKalturaFeed($state, $entry = null, $e = null, $moreItems = false)
	{
		switch ($state)
		{
		case self::STATE_HEADER:
			header ("content-type: text/xml; charset=utf-8");
			echo kSyndicationFeedManager::getMrssHeader($this->syndicationFeed->name, $this->syndicationFeed->feedLandingPage, $this->syndicationFeed->feedDescription, $this->syndicationFeedDB);
			break;

		case self::STATE_BODY:
			//syndication parameters to pass to XSLT
			$xslParams = array();	
			$xslParams[XsltParameterName::KALTURA_HAS_NEXT_ITEM] = $moreItems;
			$xslParams[XsltParameterName::KALTURA_SYNDICATION_FEED_FLAVOR_PARAM_ID] = $this->syndicationFeedDB->getFlavorParamId();
				
			echo kSyndicationFeedManager::getMrssEntry($entry, $this->syndicationFeedDB, $this->syndicationFeed->landingPage, $xslParams);				
			break;
			
 		case self::STATE_FOOTER:
			echo kSyndicationFeedManager::getMrssFooter($this->syndicationFeed->name, $this->syndicationFeed->feedLandingPage, $this->syndicationFeed->feedDescription, $this->syndicationFeedDB);
			break;
		}
	}
	
	private function renderYahooFeed($state, $entry = null, $e = null, $moreItems = false)
	{
		switch ($state)
		{
		case self::STATE_HEADER:
			header ("content-type: text/xml; charset=utf-8");
			$this->writeOpenXmlNode('rss', 0, array('version' => "2.0",  'xmlns:media' => "http://search.yahoo.com/mrss/", 'xmlns:dcterms' => "http://purl.org/dc/terms/"));
			$this->writeOpenXmlNode('channel',1);
			$this->writeFullXmlNode('title', $this->stringToSafeXml($this->syndicationFeed->name), 2);
			$this->writeFullXmlNode('link', $this->syndicationFeed->feedLandingPage, 2);
			$this->writeFullXmlNode('description', $this->stringToSafeXml($this->syndicationFeed->feedDescription), 2);
			break;

		case self::STATE_BODY:
			$this->writeOpenXmlNode('item',2); // open ITEM
			$this->writeFullXmlNode('title', $this->stringToSafeXml($e->name), 3);
			$this->writeFullXmlNode('link', $this->syndicationFeed->landingPage.$e->id, 3);
			$this->writeOpenXmlNode('media:content', 3, array( 'url' => $this->getFlavorAssetUrl($e)));
			$this->writeFullXmlNode('media:title', $this->stringToSafeXml($e->name), 4);
			$this->writeFullXmlNode('media:description', $this->stringToSafeXml($e->description), 4);
			$this->writeFullXmlNode('media:keywords', $this->stringToSafeXml($e->tags), 4);
			$this->writeFullXmlNode('media:thumbnail', '', 4, array('url'=>$e->thumbnailUrl));
			$categories = explode(',', $this->syndicationFeed->categories);
			foreach($categories as $category)
			{
				if(!$category) continue;
				$this->writeFullXmlNode('media:category',$category,4, array('scheme'=>"http://search.yahoo.com/mrss/category_schema"));
			}
			if($this->syndicationFeed->allowEmbed)
			{
				$this->writeFullXmlNode('media:player', null, 4, array('url'=>$this->getPlayerUrl($e->id)));
			}
			// TODO - add restirction on entry ???? media:restriction
			// TODO - consider adding media:community
			$this->writeFullXmlNode('media:rating',$this->syndicationFeed->adultContent, 4, array( 'scheme' => "urn:simple"));
			$this->writeClosingXmlNode('media:content',3);
			$this->writeClosingXmlNode('item',2); // close ITEM
			break;
			
 		case self::STATE_FOOTER:
			$this->writeClosingXmlNode('channel',1); // close CHANNEL
			$this->writeClosingXmlNode('rss'); // close RSS
			break;
		}
	}
	
	private function getPlayerUrl($entryId)
	{
		$uiconfId = ($this->syndicationFeed->playerUiconfId)? '/ui_conf_id/'.$this->syndicationFeed->playerUiconfId: '';
		$url = 'http://'.kConf::get('www_host').
			'/kwidget/wid/_'.$this->syndicationFeed->partnerId.
			'/entry_id/'.$entryId.$uiconfId;
		return $url;
			
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
			
		$urlManager = kUrlManager::getUrlManagerByStorageProfile($fileSync->getDc(), $flavorAsset->getEntryId());
		$urlManager->setFileExtension($flavorAsset->getFileExt());
		$url = $storage->getDeliveryHttpBaseUrl() . '/' . $urlManager->getFileSyncUrl($fileSync);
		
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
		
		$this->protocol = PlaybackProtocol::HTTP;
		$this->cdnHost = myPartnerUtils::getCdnHost($this->syndicationFeed->partnerId, $this->protocol);
		
		$urlManager = kUrlManager::getUrlManagerByCdn($this->cdnHost, $flavorAsset->getEntryId());
		$urlManager->setDomain($this->cdnHost);
		$url = $urlManager->getAssetUrl($flavorAsset);
		$url = $this->cdnHost . $url;
		$url = preg_replace('/^https?:\/\//', '', $url);
			
		return $this->protocol . '://' . $url;
	}
	
	private function getSpacesForLevel($level)
	{
		$spaces = '';
		for($i=0;$i<$level;$i++) $spaces .= self::LEVEL_INDENTATION;
		return $spaces;
	}
	
	private function writeClosingXmlNode($nodeName, $level = 0)
	{
		echo $this->getSpacesForLevel($level)."</$nodeName>".PHP_EOL;
	}
	
	private function writeOpenXmlNode($nodeName, $level, $attributes = array(), $eol = true)
	{
		$tag = $this->getSpacesForLevel($level)."<$nodeName";
		if(count($attributes))
		{
			foreach($attributes as $key => $val)
			{
				$tag .= ' '.$key.'="'.$val.'"';
			}
		}
		$tag .= ">";
		
		if($eol)
			$tag .= PHP_EOL;
		
		echo $tag;
		
	}
	
	private function writeFullXmlNode($nodeName, $value, $level, $attributes = array())
	{
		$this->writeOpenXmlNode($nodeName, $level, $attributes, false);
		echo kString::xmlEncode(kString::xmlDecode("$value")); //to create a valid XML (without unescaped special chars) 
		//we decode before encoding to avoid breaking an xml which its special chars had already been escaped 
		$this->writeClosingXmlNode($nodeName, 0);
	}
	
	private function renderTubeMogulFeed($state, $entry = null, $e = null, $moreItems = false)
	{
		switch ($state)
		{
		case self::STATE_HEADER:
			header ("content-type: text/xml; charset=utf-8");
			$this->writeOpenXmlNode('rss', 0, array('version'=>"2.0", 'xmlns:media'=>"http://search.yahoo.com/mrss/", 'xmlns:tm'=>"http://www.tubemogul.com/mrss"));
			$this->writeOpenXmlNode('channel',1);
			break;

		case self::STATE_BODY:
			$entryDescription = $this->stringToSafeXml($e->description);
			if(!$entryDescription) 
				$entryDescription = $this->stringToSafeXml($e->name);
			$entryTags = $this->stringToSafeXml($e->tags);
			if(!$entryTags) 
				$entryTags = $this->stringToSafeXml(str_replace(' ', ', ', $e->name));
			
			$this->writeOpenXmlNode('item',2);
			$this->writeFullXmlNode('pubDate',date('Y-m-d',$e->createdAt).'T'.date('H:i:sO',$e->createdAt),3);
			$this->writeFullXmlNode('media:title', $this->stringToSafeXml($e->name), 3);
			$this->writeFullXmlNode('media:description', $entryDescription,3);
			$this->writeFullXmlNode('media:keywords', $entryTags, 3);
			
			$categories = explode(',', $this->syndicationFeed->categories);
			foreach($categories as $category)
			{
				$categoryId = KalturaTubeMogulSyndicationFeed::getCategoryId($category);
				$this->writeFullXmlNode('media:category', $categoryId, 3, array( 'scheme'=>"http://www.tubemogul.com"));
				break;
			}
			
			$this->writeFullXmlNode('media:content', '', 3, array('url'=> $this->getFlavorAssetUrl($e)));
			$this->writeClosingXmlNode('item',1);
			break;

		case self::STATE_FOOTER:
			$this->writeClosingXmlNode('channel',1);
			$this->writeClosingXmlNode('rss');
			break;
		}
	}

	private function renderITunesFeed($state, $entry = null, $e = null, $moreItems = false)
	{
		switch ($state)
		{
		case self::STATE_HEADER:
			if(is_null($this->mimeType))
			{
				$flavor = assetParamsPeer::retrieveByPK($this->syndicationFeed->flavorParamId);
				if(!$flavor)
					throw new Exception("flavor not found for id " . $this->syndicationFeed->flavorParamId);
			
				switch($flavor->getFormat())
				{
					case 'mp4':
						$this->mimeType = 'video/mp4';
						break;
					case 'm4v':
						$this->mimeType = 'video/x-m4v';
						break;
					case 'mov':
						$this->mimeType = 'video/quicktime';
						break;
					default:
						$this->mimeType = 'video/mp4';
				}
			}
			
			$partner = PartnerPeer::retrieveByPK($this->syndicationFeed->partnerId);
			header ("content-type: text/xml; charset=utf-8");
			'<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL;
			$this->writeOpenXmlNode('rss', 0, array('xmlns:itunes'=>"http://www.itunes.com/dtds/podcast-1.0.dtd",  'version'=>"2.0"));
			$this->writeOpenXmlNode('channel', 1);
			$this->writeFullXmlNode('title', $this->stringToSafeXml($this->syndicationFeed->name), 2);
			$this->writeFullXmlNode('link', $this->syndicationFeed->feedLandingPage, 2);
			$this->writeFullXmlNode('language', $this->syndicationFeed->language, 2);
			$this->writeFullXmlNode('copyright', $partner->getName(), 2);
			$this->writeFullXmlNode('itunes:subtitle', $this->syndicationFeed->name, 2);
			$this->writeFullXmlNode('itunes:author', $this->syndicationFeed->feedAuthor, 2);
			$this->writeFullXmlNode('itunes:summary', $this->syndicationFeed->feedDescription, 2);
			$this->writeFullXmlNode('description', $this->syndicationFeed->feedDescription, 2);
			$this->writeOpenXmlNode('itunes:owner', 2);
			$this->writeFullXmlNode('itunes:name', $this->syndicationFeed->ownerName, 3);
			$this->writeFullXmlNode('itunes:email', $this->syndicationFeed->ownerEmail, 3);
			$this->writeClosingXmlNode('itunes:owner', 2);

			if($this->syndicationFeed->feedImageUrl)
			{
				$this->writeOpenXmlNode('image', 2);
				$this->writeFullXmlNode('link', $this->syndicationFeed->feedLandingPage,3);
				$this->writeFullXmlNode('url', $this->syndicationFeed->feedLandingPage, 3);
				$this->writeFullXmlNode('title', $this->syndicationFeed->name, 3);
				$this->writeClosingXmlNode('image', 2);
				$this->writeFullXmlNode('itunes:image', '', 2, array( 'href'=> $this->syndicationFeed->feedImageUrl));
			}

			$categories = explode(',', $this->syndicationFeed->categories);
			$catTree = array();
			foreach($categories as $category)
			{
				if(!$category) continue;
				if(strpos($category, '/')) // category & subcategory
				{
					$category_parts = explode('/', $category);
					$catTree[$category_parts[0]][] = $category_parts[1];
				}
				else
				{
					$this->writeFullXmlNode('itunes:category', '', 2, array( 'text'=> $category ));
				}
			}
			
			foreach($catTree as $topCat => $subCats)
			{
				if(!$topCat) continue;
				$this->writeOpenXmlNode('itunes:category', 2, array( 'text' => $topCat ));
				foreach($subCats as $cat)
				{
					if(!$cat) continue;
					$this->writeFullXmlNode('itunes:category', '', 3, array( 'text'=> $cat ));
				}
				$this->writeClosingXmlNode('itunes:category', 2);
			}
			break;

		case self::STATE_BODY:
			$url = $this->getFlavorAssetUrl($e);
			$this->writeOpenXmlNode('item',2);
			$this->writeFullXmlNode('title', $this->stringToSafeXml($e->name), 3);
			$this->writeFullXmlNode('link', $this->syndicationFeed->landingPage.$e->id, 3);
			$this->writeFullXmlNode('guid', $url, 3);
			$this->writeFullXmlNode('pubDate', date('r',$e->createdAt), 3);
			$this->writeFullXmlNode('description', $this->stringToSafeXml($e->description), 3);

			$enclosure_attr = array(
				'url'=> $url,
				//'length'=>$entry->getLengthInMsecs(), removed by Noa, 25/08/10: we'll need to place here file size (of flavor asset).
				'type'=> $this->mimeType,
			);
			$this->writeFullXmlNode('enclosure', '', 3, $enclosure_attr);
			
			$kuser = $entry->getkuser();
			if($kuser && $kuser->getScreenName())
				$this->writeFullXmlNode('itunes:author', $this->stringToSafeXml($kuser->getScreenName()), 3);
				
			if($e->description)
			{
				$this->writeFullXmlNode('itunes:subtitle', $this->stringToSafeXml($e->description), 3);
				$this->writeFullXmlNode('itunes:summary', $this->stringToSafeXml($e->description), 3);
			}
			$this->writeFullXmlNode('itunes:duration', $this->secondsToWords($e->duration), 3);
			$this->writeFullXmlNode('itunes:explicit', $this->syndicationFeed->adultContent, 3);
			$this->writeFullXmlNode('itunes:image', '', 3, array( 'href' => $e->thumbnailUrl.'/width/600/height/600/ext.jpg'));
			if($e->tags)
				$this->writeFullXmlNode('itunes:keywords', $this->stringToSafeXml($e->tags), 3);
			$this->writeClosingXmlNode('item',2);
			break;
			
		case self::STATE_FOOTER:
			$this->writeClosingXmlNode('channel', 1);
			$this->writeClosingXmlNode('rss');
			break;
		}
	}
	
	private function renderGoogleVideoFeed($state, $entry = null, $e = null, $moreItems = false)
	{
		switch ($state)
		{
		case self::STATE_HEADER:
			header ("content-type: text/xml; charset=utf-8");
			
			$this->writeOpenXmlNode('urlset', 0, array( 'xmlns' => "http://www.sitemaps.org/schemas/sitemap/0.9", 
				'xmlns:video' => "http://www.google.com/schemas/sitemap-video/1.1" ));
			break;
			
		case self::STATE_BODY:
			$this->writeOpenXmlNode('url',1);
			$this->writeFullXmlNode('loc', $this->syndicationFeed->landingPage.$e->id, 2);
			$this->writeOpenXmlNode('video:video', 2);
			$this->writeFullXmlNode('video:content_loc', $this->getFlavorAssetUrl($e), 3);
			if($this->syndicationFeed->allowEmbed)
			{
				$this->writeFullXmlNode('video:player_loc', $this->getPlayerUrl($e->id), 3, array('allow_embed' => 'yes'));
			}
			$this->writeFullXmlNode('video:thumbnail_loc', $e->thumbnailUrl . '/width/480', 3);
			$this->writeFullXmlNode('video:title', $this->stringToSafeXml($e->name), 3);
			$this->writeFullXmlNode('video:description', $this->stringToSafeXml($e->description), 3);
			$this->writeFullXmlNode('video:view_count', $e->views, 3);
			$this->writeFullXmlNode('video:publication_date', date('Y-m-d',$e->createdAt).'T'.date('H:i:sP',$e->createdAt), 3);
			$tags = explode(',', $e->tags);
			foreach($tags as $tag)
			{
				if(!$tag) continue;
				$this->writeFullXmlNode('video:tag', rtrim(ltrim($this->stringToSafeXml($tag))), 3);
			}
			$this->writeFullXmlNode('video:category', $this->stringToSafeXml($e->categories), 3);
			if($this->syndicationFeed->adultContent == KalturaGoogleSyndicationFeedAdultValues::NO)
			{
				$familyFriendly = KalturaGoogleSyndicationFeedAdultValues::YES;
			}
			else
			{
				$familyFriendly = KalturaGoogleSyndicationFeedAdultValues::NO;
			}
			$this->writeFullXmlNode('video:family_friendly', $familyFriendly, 3);
			$this->writeFullXmlNode('video:duration', $e->duration, 3);
			$this->writeClosingXmlNode('video:video', 2);
			$this->writeClosingXmlNode('url', 1);
			break;
			
		case self::STATE_FOOTER:
			$this->writeClosingXmlNode('urlset');
			break;
		}
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
}
