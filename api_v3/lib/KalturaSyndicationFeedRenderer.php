<?php
class KalturaSyndicationFeedRenderer
{
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
		
		$this->baseCriteria = KalturaCriteria::create(entryPeer::OM_CLASS);

		$startDateCriterion = $this->baseCriteria->getNewCriterion(entryPeer::START_DATE, time(), Criteria::LESS_EQUAL);
		$startDateCriterion->addOr($this->baseCriteria->getNewCriterion(entryPeer::START_DATE, null));
		$this->baseCriteria->addAnd($startDateCriterion);
		
		$endDateCriterion = $this->baseCriteria->getNewCriterion(entryPeer::END_DATE, time(), Criteria::GREATER_EQUAL);
		$endDateCriterion->addOr($this->baseCriteria->getNewCriterion(entryPeer::END_DATE, null));
		$this->baseCriteria->addAnd($endDateCriterion);
		
		$entryFilter = new entryFilter();
		$entryFilter->setPartnerSearchScope($this->syndicationFeed->partnerId);
		$entryFilter->setStatusEquel(entry::ENTRY_STATUS_READY);
		$entryFilter->setTypeIn(array(entry::ENTRY_TYPE_MEDIACLIP, entry::ENTRY_TYPE_SHOW));
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
		}
		
		$entry = current($this->entriesCurrentPage);
		if($entry)
		{
			next($this->entriesCurrentPage);
			$this->lastEntryIntId = $entry->getIntId();
			return $entry;
		}
			
		$this->fetchNextPage();
		if(!$this->entriesCurrentPage)
		{
			$this->lastEntryIntId = null;
			return false;
		}
	
		$entry = current($this->entriesCurrentPage);
		if($entry)
		{
			next($this->entriesCurrentPage);
			$this->lastEntryIntId = $entry->getIntId();
		}
		else
		{
			$this->lastEntryIntId = null;
		}
			
		return $entry;
	}
	
	private function fetchNextPage()
	{
		$this->entriesCurrentPage = null;
		entryPeer::clearInstancePool();
		flavorAssetPeer::clearInstancePool();
		
		if($this->currentCriteria)
		{
			if($this->lastEntryIntId)
				$this->currentCriteria->add(entryPeer::INT_ID, $this->lastEntryIntId, Criteria::LESS_THAN);
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
			
			return $c; // return the base criteria
		}
			
		$filter = current($this->entryFilters);
		if(!$filter) // no more filters found
			return null;
			
		next($this->entryFilters);
			
		$filter->attachToCriteria($c);
		
		$c->clearOrderByColumns();
		$c->addDescendingOrderByColumn(entryPeer::CREATED_AT);
		
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
		$yahooFeed[] = $this->generateOpenXmlNode('rss', 0, array('version' => "2.0",  'xmlns:media' => "http://search.yahoo.com/mrss/", 'xmlns:dcterms' => "http://purl.org/dc/terms/"));
		$yahooFeed[] = $this->generateOpenXmlNode('channel',1);
		$yahooFeed[] = $this->generateFullXmlNode('title', $this->stringToSafeXml($this->syndicationFeed->name), 2);
		$yahooFeed[] = $this->generateFullXmlNode('link', $this->syndicationFeed->feedLandingPage, 2);
		$yahooFeed[] = $this->generateFullXmlNode('description', $this->stringToSafeXml($this->syndicationFeed->feedDescription), 2);
		
		while($entry = $this->getNextEntry())
		{
			$e= new KalturaMediaEntry();
			$e->fromObject($entry);
			$yahooFeed[] = $this->generateOpenXmlNode('item',2); // open ITEM
			$yahooFeed[] = $this->generateFullXmlNode('title', $this->stringToSafeXml($e->name), 3);
			$yahooFeed[] = $this->generateFullXmlNode('link', $this->syndicationFeed->landingPage.$e->id, 3);
			$yahooFeed[] = $this->generateOpenXmlNode('media:content', 3, array( 'url' => $this->getFlavorAssetUrl($e)));
			$yahooFeed[] = $this->generateFullXmlNode('media:title', $this->stringToSafeXml($e->name), 4);
			$yahooFeed[] = $this->generateFullXmlNode('media:description', $this->stringToSafeXml($e->description), 4);
			$yahooFeed[] = $this->generateFullXmlNode('media:keywords', $this->stringToSafeXml($e->tags), 4);
			$yahooFeed[] = $this->generateFullXmlNode('media:thumbnail', '', 4, array('url'=>$e->thumbnailUrl));
			$categories = explode(',', $this->syndicationFeed->categories);
			foreach($categories as $category)
			{
				if(!$category) continue;
				$yahooFeed[] = $this->generateFullXmlNode('media:category',$category,4, array('scheme'=>"http://search.yahoo.com/mrss/category_schema"));
			}
			if($this->syndicationFeed->allowEmbed)
			{
				$yahooFeed[] = $this->generateFullXmlNode('media:player', null, 4, array('url'=>$this->getPlayerUrl($e->id)));
			}
			// TODO - add restirction on entry ???? media:restriction
			// TODO - consider adding media:community
			$yahooFeed[] = $this->generateFullXmlNode('media:rating',$this->syndicationFeed->adultContent, 4, array( 'scheme' => "urn:simple"));
			$yahooFeed[] = $this->generateClosingXmlNode('media:content',3);
			$yahooFeed[] = $this->generateClosingXmlNode('item',2); // close ITEM
		}
		$yahooFeed[] = $this->generateClosingXmlNode('channel',1); // close CHANNEL
		$yahooFeed[] = $this->generateClosingXmlNode('rss'); // close RSS
		echo implode('', $yahooFeed);
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
	private function generateClosingXmlNode($nodeName, $level = 0)
	{
		return $this->getSpacesForLevel($level)."</$nodeName>".PHP_EOL;
	}
	private function generateOpenXmlNode($nodeName, $level, $attributes = array())
	{
		$tag = $this->getSpacesForLevel($level)."<$nodeName";
		if(count($attributes))
		{
			foreach($attributes as $key => $val)
			{
				$tag .= ' '.$key.'="'.$val.'"';
			}
		}
		$tag .= ">".PHP_EOL;
		return $tag;
		
	}
	private function generateFullXmlNode($nodeName, $value, $level, $attributes = array())
	{
		$tag = $this->generateOpenXmlNode($nodeName, $level, $attributes);
		$tag = str_replace(PHP_EOL, '', $tag);
		$tag .= "$value".$this->generateClosingXmlNode($nodeName, 0);
		return $tag;
	}
	private function renderTubeMogulFeed()
	{
		header ("content-type: text/xml; charset=utf-8");
		$tubeMoguleFeed[] = $this->generateOpenXmlNode('rss', 0, array('version'=>"2.0", 'xmlns:media'=>"http://search.yahoo.com/mrss/", 'xmlns:tm'=>"http://www.tubemogul.com/mrss"));
		$tubeMoguleFeed[] = $this->generateOpenXmlNode('channel',1);
		while($entry = $this->getNextEntry())
		{
			$e= new KalturaMediaEntry();
			$e->fromObject($entry);
			$entryDescription = $this->stringToSafeXml($e->description);
			if(!$entryDescription) $entryDescription = $this->stringToSafeXml($e->name);
			$entryTags = $this->stringToSafeXml($e->tags);
			if(!$entryTags) $entryTags = $this->stringToSafeXml(str_replace(' ', ', ', $e->name));
			
			$tubeMoguleFeed[] = $this->generateOpenXmlNode('item',2);
			$tubeMoguleFeed[] = $this->generateFullXmlNode('pubDate',date('Y-m-d',$e->createdAt).'T'.date('H:i:sO',$e->createdAt),3);
			$tubeMoguleFeed[] = $this->generateFullXmlNode('media:title', $this->stringToSafeXml($e->name), 3);
			$tubeMoguleFeed[] = $this->generateFullXmlNode('media:description', $entryDescription,3);
			$tubeMoguleFeed[] = $this->generateFullXmlNode('media:keywords', $entryTags, 3);
			$categories = explode(',', $this->syndicationFeed->categories);
			foreach($categories as $category)
			{
				$categoryId = KalturaTubeMogulSyndicationFeed::getCategoryId($category);
				$tubeMoguleFeed[] = $this->generateFullXmlNode('media:category', $categoryId, 3, array( 'scheme'=>"http://www.tubemogul.com"));
				break;
			}
			$tubeMoguleFeed[] = $this->generateFullXmlNode('media:content', '', 3, array('url'=> $this->getFlavorAssetUrl($e)));
			$tubeMoguleFeed[] = $this->generateClosingXmlNode('item',1);
		}
		$tubeMoguleFeed[] = $this->generateClosingXmlNode('channel',1);
		$tubeMoguleFeed[] = $this->generateClosingXmlNode('rss');
		echo implode('', $tubeMoguleFeed);
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
		$itunesFeed[] = '<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL;
		$itunesFeed[] = $this->generateOpenXmlNode('rss', 0, array('xmlns:itunes'=>"http://www.itunes.com/dtds/podcast-1.0.dtd",  'version'=>"2.0"));
		$itunesFeed[] = $this->generateOpenXmlNode('channel', 1);
		$itunesFeed[] = $this->generateFullXmlNode('title', $this->stringToSafeXml($this->syndicationFeed->name), 2);
		$itunesFeed[] = $this->generateFullXmlNode('link', $this->syndicationFeed->feedLandingPage, 2);
		$itunesFeed[] = $this->generateFullXmlNode('language', $this->syndicationFeed->language, 2);
		$itunesFeed[] = $this->generateFullXmlNode('copyright', $partner->getName(), 2);
		$itunesFeed[] = $this->generateFullXmlNode('itunes:subtitle', $this->syndicationFeed->name, 2);
		$itunesFeed[] = $this->generateFullXmlNode('itunes:author', $this->syndicationFeed->feedAuthor, 2);
		$itunesFeed[] = $this->generateFullXmlNode('itunes:summary', $this->syndicationFeed->feedDescription, 2);
		$itunesFeed[] = $this->generateFullXmlNode('description', $this->syndicationFeed->feedDescription, 2);
		$itunesFeed[] = $this->generateOpenXmlNode('itunes:owner', 2);
		$itunesFeed[] = $this->generateFullXmlNode('itunes:name', $this->syndicationFeed->ownerName, 3);
		$itunesFeed[] = $this->generateFullXmlNode('itunes:email', $this->syndicationFeed->ownerEmail, 3);
		$itunesFeed[] = $this->generateClosingXmlNode('itunes:owner', 2);

		if($this->syndicationFeed->feedImageUrl)
		{
			$itunesFeed[] = $this->generateOpenXmlNode('image', 2);
			$itunesFeed[] = $this->generateFullXmlNode('link', $this->syndicationFeed->feedLandingPage,3);
			$itunesFeed[] = $this->generateFullXmlNode('url', $this->syndicationFeed->feedLandingPage, 3);
			$itunesFeed[] = $this->generateFullXmlNode('title', $this->syndicationFeed->name, 3);
			$itunesFeed[] = $this->generateClosingXmlNode('image', 2);
			$itunesFeed[] = $this->generateFullXmlNode('itunes:image', '', 2, array( 'href'=> $this->syndicationFeed->feedImageUrl));
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
				$itunesFeed[] = $this->generateFullXmlNode('itunes:category', '', 2, array( 'text'=> $category ));
			}
		}
		foreach($catTree as $topCat => $subCats)
		{
			if(!$topCat) continue;
			$itunesFeed[] = $this->generateOpenXmlNode('itunes:category', 2, array( 'text' => $topCat ));
			foreach($subCats as $cat)
			{
				if(!$cat) continue;
				$itunesFeed[] = $this->generateFullXmlNode('itunes:category', '', 3, array( 'text'=> $cat ));
			}
			$itunesFeed[] = $this->generateClosingXmlNode('itunes:category', 2);
		}
		
		while($entry = $this->getNextEntry())
		{
			$e= new KalturaMediaEntry();
			$e->fromObject($entry);
						
			$url = $this->getFlavorAssetUrl($e);
			$itunesFeed[] = $this->generateOpenXmlNode('item',2);
			$itunesFeed[] = $this->generateFullXmlNode('title', $this->stringToSafeXml($e->name), 3);
			$itunesFeed[] = $this->generateFullXmlNode('link', $this->syndicationFeed->landingPage.$e->id, 3);
			$itunesFeed[] = $this->generateFullXmlNode('guid', $url, 3);
			$itunesFeed[] = $this->generateFullXmlNode('pubDate', date('r',$e->createdAt), 3);
			$itunesFeed[] = $this->generateFullXmlNode('description', $this->stringToSafeXml($e->description), 3);

			$enclosure_attr = array(
				'url'=> $url,
				//'length'=>$entry->getLengthInMsecs(), removed by Noa, 25/08/10: we'll need to place here file size (of flavor asset).
				'type'=> $this->mimeType,
			);
			$itunesFeed[] = $this->generateFullXmlNode('enclosure', '', 3, $enclosure_attr);
			
			$kuser = $entry->getkuser();
			if($kuser && $kuser->getScreenName())
				$itunesFeed[] = $this->generateFullXmlNode('itunes:author', $this->stringToSafeXml($kuser->getScreenName()), 3);
				
			if($e->description)
			{
				$itunesFeed[] = $this->generateFullXmlNode('itunes:subtitle', $this->stringToSafeXml($e->description), 3);
				$itunesFeed[] = $this->generateFullXmlNode('itunes:summary', $this->stringToSafeXml($e->description), 3);
			}
			$itunesFeed[] = $this->generateFullXmlNode('itunes:duration', $this->secondsToWords($e->duration), 3);
			$itunesFeed[] = $this->generateFullXmlNode('itunes:explicit', $this->syndicationFeed->adultContent, 3);
			$itunesFeed[] = $this->generateFullXmlNode('itunes:image', '', 3, array( 'href' => $e->thumbnailUrl.'/width/600/height/600/ext.jpg'));
			if($e->tags)
				$itunesFeed[] = $this->generateFullXmlNode('itunes:keywords', $this->stringToSafeXml($e->tags), 3);
			$itunesFeed[] = $this->generateClosingXmlNode('item',2);
		}
		
		$itunesFeed[] = $this->generateClosingXmlNode('channel', 1);
		$itunesFeed[] = $this->generateClosingXmlNode('rss');
		echo implode('', $itunesFeed);
	}
	
	private function renderGoogleVideoFeed()
	{
		header ("content-type: text/xml; charset=utf-8");
		$uiconfId = ($this->syndicationFeed->playerUiconfId)? '/ui_conf_id/'.$this->syndicationFeed->playerUiconfId: '';
		
		$googleFeed[] = $this->generateOpenXmlNode('urlset', 0, array( 'xmlns' => "http://www.sitemaps.org/schemas/sitemap/0.9", 
		'xmlns:video' => "http://www.google.com/schemas/sitemap-video/1.1" ));
		while($entry = $this->getNextEntry())
		{
			$e= new KalturaMediaEntry();
			$e->fromObject($entry);			
			$googleFeed[] = $this->generateOpenXmlNode('url',1);
			$googleFeed[] = $this->generateFullXmlNode('loc', $this->syndicationFeed->landingPage.$e->id, 2);
			$googleFeed[] = $this->generateOpenXmlNode('video:video', 2);
			$googleFeed[] = $this->generateFullXmlNode('video:content_loc', $this->getFlavorAssetUrl($e), 3);
			if($this->syndicationFeed->allowEmbed)
			{
				$googleFeed[] = $this->generateFullXmlNode('video:player_loc', $this->getPlayerUrl($e->id), 3, array('allow_embed' => 'yes'));
			}
			$googleFeed[] = $this->generateFullXmlNode('video:thumbnail_loc', $e->thumbnailUrl . '/width/480', 3);
			$googleFeed[] = $this->generateFullXmlNode('video:title', $this->stringToSafeXml($e->name), 3);
			$googleFeed[] = $this->generateFullXmlNode('video:description', $this->stringToSafeXml($e->description), 3);
			$googleFeed[] = $this->generateFullXmlNode('video:view_count', $e->views, 3);
			$googleFeed[] = $this->generateFullXmlNode('video:publication_date', date('Y-m-d',$e->createdAt).'T'.date('H:i:sP',$e->createdAt), 3);
			$tags = explode(',', $e->tags);
			foreach($tags as $tag)
			{
				if(!$tag) continue;
				$googleFeed[] = $this->generateFullXmlNode('video:tag', rtrim(ltrim($this->stringToSafeXml($tag))), 3);
			}
			$googleFeed[] = $this->generateFullXmlNode('video:category', $this->stringToSafeXml($e->categories), 3);
			if($this->syndicationFeed->adultContent == KalturaGoogleSyndicationFeedAdultValues::NO)
			{
				$familyFriendly = KalturaGoogleSyndicationFeedAdultValues::YES;
			}
			else
			{
				$familyFriendly = KalturaGoogleSyndicationFeedAdultValues::NO;
			}
			$googleFeed[] = $this->generateFullXmlNode('video:family_friendly', $familyFriendly, 3);
			$googleFeed[] = $this->generateFullXmlNode('video:duration', $e->duration, 3);
			$googleFeed[] = $this->generateClosingXmlNode('video:video', 2);
			$googleFeed[] = $this->generateClosingXmlNode('url', 1);
		}
		$googleFeed[] = $this->generateClosingXmlNode('urlset');
		echo implode('', $googleFeed);
	}
}
