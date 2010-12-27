<?php
class KalturaSyndicationFeedRenderer
{
	const MAX_RETUREND_ENTRIES = 10000;
	const ENTRY_PEER_LIMIT_QUERY = 500;
	const LEVEL_INDENTATION = '  ';
	
	/**
	 * @var KalturaBaseSyndicationFeed
	 */
	public $syndicationFeed = null;
	
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
	 * @var int
	 */
	private $lastEntryIntId = null;
	
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
	
	public function __construct($feedId)
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;

		$microTimeStart = microtime(true);
		KalturaLog::info("syndicationFeedRenderer- initialize ");
		
		// initialize the database for all services
		DbManager::setConfig(kConf::getDB());
		DbManager::initialize();
		
		$syndicationFeedDB = syndicationFeedPeer::retrieveByPK($feedId);
		if( !$syndicationFeedDB )
			throw new Exception("Feed Id not found");
			
		$tmpSyndicationFeed = KalturaSyndicationFeedFactory::getInstanceByType($syndicationFeedDB->getType());
		$tmpSyndicationFeed->fromObject($syndicationFeedDB);
		$this->syndicationFeed = $tmpSyndicationFeed;
		
		// add partner to default criteria
		myPartnerUtils::addPartnerToCriteria(new categoryPeer(), $this->syndicationFeed->partnerId, true);
		myPartnerUtils::addPartnerToCriteria(new flavorAssetPeer(), $this->syndicationFeed->partnerId, true);
		
		
		$this->baseCriteria = KalturaCriteria::create(entryPeer::OM_CLASS);

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
		$entryFilter->setModerationStatusNot(entry::ENTRY_MODERATION_STATUS_REJECTED);
		$entryFilter->setDurationGreaterThan(0);
			
		$entryFilter->attachToCriteria($this->baseCriteria);
			
		if($this->syndicationFeed->playlistId)
		{
			$this->entryFilters = myPlaylistUtils::getPlaylistFiltersById($this->syndicationFeed->playlistId);
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
		$entryFilter->setFlavorParamsMatchOr($this->syndicationFeed->flavorParamId);
		$entryFilter->attachToCriteria($this->baseCriteria);
	}
	
	public function addFilter(entryFilter $entryFilter)
	{
		if($this->executed)
			return;
			
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
			$this->lastEntryIntId = 0;
			$this->lastEntryCreatedAt = 0;
		}
		
		++$this->returnedEntriesCount;
		if ($this->returnedEntriesCount == self::MAX_RETUREND_ENTRIES)
			return false;
				
		$entry = current($this->entriesCurrentPage);
		if($entry)
		{
			next($this->entriesCurrentPage);
			$this->lastEntryIntId = $entry->getIntId();
			$this->lastEntryCreatedAt = $entry->getCreatedAt(null);
			return $entry;
		}
			
		$this->fetchNextPage();
		if(!$this->entriesCurrentPage)
		{
			$this->lastEntryIntId = null;
			$this->lastEntryCreatedAt = null;
			return false;
		}
	
		$entry = current($this->entriesCurrentPage);
		if($entry)
		{
			next($this->entriesCurrentPage);
			$this->lastEntryIntId = $entry->getIntId();
			$this->lastEntryCreatedAt = $entry->getCreatedAt(null);
		}
		else
		{
			$this->lastEntryIntId = null;
			$this->lastEntryCreatedAt = null;
		}
			
		return $entry;
	}
	
	private function clearMemory()
	{
		entryPeer::clearInstancePool();
		flavorAssetPeer::clearInstancePool();
		FileSyncPeer::clearInstancePool();
		categoryPeer::clearInstancePool();
		
		if(class_exists('MetadataPeer'))
		{
			MetadataPeer::clearInstancePool();
			MetadataProfilePeer::clearInstancePool();
		}
	}
	
	private function fetchNextPage()
	{
		$this->entriesCurrentPage = null;
		$this->clearMemory();
		
		if($this->currentCriteria)
		{
			if($this->lastEntryIntId && $this->lastEntryCreatedAt)
			{
				$this->currentCriteria->add(entryPeer::INT_ID, $this->lastEntryIntId, Criteria::LESS_THAN);
				$this->currentCriteria->add(entryPeer::CREATED_AT, $this->lastEntryCreatedAt, Criteria::LESS_EQUAL);
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
		
		$this->entriesCurrentPage = $nextPage;
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
			$c->addDescendingOrderByColumn(entryPeer::CREATED_AT);
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
		$c->addDescendingOrderByColumn(entryPeer::CREATED_AT);
		$c->dontCount();
		
		return $c;
	}
	
	public function execute()
	{
		if($this->executed)
			return;
			
		$microTimeStart = microtime(true);
		
		switch($this->syndicationFeed->type)
		{
			case KalturaSyndicationFeedType::GOOGLE_VIDEO:
				$this->renderGoogleVideoFeed();
				break;
			case KalturaSyndicationFeedType::ITUNES:
				$this->renderITunesFeed();
				break;
			case KalturaSyndicationFeedType::TUBE_MOGUL:
				$this->renderTubeMogulFeed();
				break;
			case KalturaSyndicationFeedType::YAHOO:
				$this->renderYahooFeed();
				break;
		}
		
		$microTimeEnd = microtime(true);
		KalturaLog::info("syndicationFeedRenderer- render time for ({$this->syndicationFeed->type}) is " . ($microTimeEnd - $microTimeStart));
	}
	
	private function stringToSafeXml($string, $now = false)
	{
		$string = @iconv('utf-8', 'utf-8', $string);
		$partially_safe = kString::xmlEncode($string);
		$safe = str_replace(array('*', '/', '[', ']'), '',$partially_safe);
		
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
	
	private function renderYahooFeed()
	{
		header ("content-type: text/xml; charset=utf-8");
		$this->writeOpenXmlNode('rss', 0, array('version' => "2.0",  'xmlns:media' => "http://search.yahoo.com/mrss/", 'xmlns:dcterms' => "http://purl.org/dc/terms/"));
		$this->writeOpenXmlNode('channel',1);
		$this->writeFullXmlNode('title', $this->stringToSafeXml($this->syndicationFeed->name), 2);
		$this->writeFullXmlNode('link', $this->syndicationFeed->feedLandingPage, 2);
		$this->writeFullXmlNode('description', $this->stringToSafeXml($this->syndicationFeed->feedDescription), 2);
		
		while($entry = $this->getNextEntry())
		{
			$e= new KalturaMediaEntry();
			$e->fromObject($entry);
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
		}
		$this->writeClosingXmlNode('channel',1); // close CHANNEL
		$this->writeClosingXmlNode('rss'); // close RSS
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
			
		$urlManager = kUrlManager::getUrlManagerByStorageProfile($fileSync->getDc());
		$urlManager->setFileExtension($flavorAsset->getFileExt());
		$url = $storage->getDeliveryHttpBaseUrl() . '/' . $urlManager->getFileSyncUrl($fileSync);
		
		return $url;
	}
	
	private function getFlavorAssetUrl($kalturaEntry)
	{
		$partner = PartnerPeer::retrieveByPK($this->syndicationFeed->partnerId);
		if(!$partner)
			return null;
	
		$flavorAsset = flavorAssetPeer::retrieveByEntryIdAndFlavorParams($kalturaEntry->id,$this->syndicationFeed->flavorParamId);
		if(!$flavorAsset)
			return null;
					
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$externalStorageUrl = $this->getExternalStorageUrl($partner, $flavorAsset, $syncKey);
		if($externalStorageUrl)
			return $externalStorageUrl;
			
		if($partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY)
			return null;
		
		$this->protocol = StorageProfile::PLAY_FORMAT_HTTP;
		$this->cdnHost = myPartnerUtils::getCdnHost($this->syndicationFeed->partnerId, $this->protocol);
		
		$urlManager = kUrlManager::getUrlManagerByCdn($this->cdnHost);
		$urlManager->setDomain($this->cdnHost);
		$url = $urlManager->getFlavorAssetUrl($flavorAsset);
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
		echo "$value";
		$this->writeClosingXmlNode($nodeName, 0);
	}
	
	private function renderTubeMogulFeed()
	{
		header ("content-type: text/xml; charset=utf-8");
		$this->writeOpenXmlNode('rss', 0, array('version'=>"2.0", 'xmlns:media'=>"http://search.yahoo.com/mrss/", 'xmlns:tm'=>"http://www.tubemogul.com/mrss"));
		$this->writeOpenXmlNode('channel',1);
		while($entry = $this->getNextEntry())
		{
			$e= new KalturaMediaEntry();
			$e->fromObject($entry);
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
		}
		$this->writeClosingXmlNode('channel',1);
		$this->writeClosingXmlNode('rss');
	}

	private function renderITunesFeed()
	{
		if(is_null($this->mimeType))
		{
			$flavor = flavorParamsPeer::retrieveByPK($this->syndicationFeed->flavorParamId);
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
		
		while($entry = $this->getNextEntry())
		{
			$e= new KalturaMediaEntry();
			$e->fromObject($entry);
						
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
		}
		
		$this->writeClosingXmlNode('channel', 1);
		$this->writeClosingXmlNode('rss');
	}
	
	private function renderGoogleVideoFeed()
	{
		header ("content-type: text/xml; charset=utf-8");
		$uiconfId = ($this->syndicationFeed->playerUiconfId)? '/ui_conf_id/'.$this->syndicationFeed->playerUiconfId: '';
		
		$this->writeOpenXmlNode('urlset', 0, array( 'xmlns' => "http://www.sitemaps.org/schemas/sitemap/0.9", 
		'xmlns:video' => "http://www.google.com/schemas/sitemap-video/1.1" ));
		while($entry = $this->getNextEntry())
		{
			$e= new KalturaMediaEntry();
			$e->fromObject($entry);			
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
		}
		$this->writeClosingXmlNode('urlset');
	}
}
