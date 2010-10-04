<?php
class KalturaSyndicationFeedRenderer implements IKalturaPlaylistUtils
{
	const MAX_ENTRIES_PER_FEED = 2000;
	const ENTRY_PEER_LIMIT_QUERY = 100;
	const LEVEL_INDENTATION = '  ';
	
	/**
	 * @var KalturaBaseSyndicationFeed
	 */
	public $syndicationFeed = null;
	
	/**
	 * @var entries
	 */
	private $entries = array();
	
	/**
	 * @var flavor_params
	 */
	private $flavor;
	
	/**
	 * @var KalturaCriteria
	 */
	private $allEntriesCriteria = null;
	
	private static $micro_times = array();
	
	public $feedTotalEntryCount;
	
	/**
	 * ignoreFlavorFilter - if true will list without filter, if false will list with filter
	 */
	private $_ignoreFlavorFilter = false;
	
	public function attachCriteriaHandler(Criteria &$c)
	{
		$entryFilter = new entryFilter();
		$entryFilter->setPartnerSearchScope($this->syndicationFeed->partnerId);
		$entryFilter->addSearchMatchToCriteria( $c , null  , entry::getSearchableColumnName() );

		if($this->_ignoreFlavorFilter === null)
		{
			$c->addAnd(entryPeer::SEARCH_TEXT_DISCRETE, '%_FLAVOR_'.$this->syndicationFeed->flavorParamId.'%', Criteria::NOT_LIKE);
		}
	}
	
	public function getEntries()
	{
		return $this->entries;
	}
	
	public function setIgnoreFlavor($value)
	{
		$this->_ignoreFlavorFilter = $value;
	}

	public function __construct($feedId, $ignoreFlavorFilter = false)
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;

		$this->setIgnoreFlavor($ignoreFlavorFilter);
		
		self::$micro_times['init'] = microtime(true);
		KalturaLog::info("syndicationFeedRenderer- initialize ");
		
		// initialize the database for all services
		DbManager::setConfig(kConf::getDB());
		DbManager::initialize();
		
		if ( ! $this->syndicationFeed )
		{
			$syndicationFeedDB = syndicationFeedPeer::retrieveByPK($feedId);
			if( !$syndicationFeedDB )
			{
				throw new Exception("Feed Id not found");
			}
			$tmpSyndicationFeed = KalturaSyndicationFeedFactory::getInstanceByType($syndicationFeedDB->getType());
			$tmpSyndicationFeed->fromObject($syndicationFeedDB);
			$this->syndicationFeed = $tmpSyndicationFeed;
		}
		
		if ( ! $this->flavor )
		{
			$flavorId = $this->syndicationFeed->flavorParamId;
			$flavor = flavorParamsPeer::retrieveByPK($flavorId);
			if(!$flavor)
			{
				throw new Exception("flavor id not found with ID $flavorId");
			}
			$this->flavor = $flavor;
		}
		
		if( ! $this->allEntriesCriteria && ! $this->syndicationFeed->playlistId )
		{
			$c = KalturaCriteria::create("entry");

			// allow entry type 1
			$criterion_clip = $c->getNewCriterion( entryPeer::TYPE, entry::ENTRY_TYPE_MEDIACLIP ) ;
			// or entry type 2
			$criterion_show = $c->getNewCriterion( entryPeer::TYPE, entry::ENTRY_TYPE_SHOW );
			$criterion_clip->addOr ( $criterion_show ) ;
			$c->addAnd ( $criterion_clip );
			$c->addAnd(entryPeer::STATUS, entry::ENTRY_STATUS_READY);
			$c->addAnd(entryPeer::MODERATION_STATUS, entry::ENTRY_MODERATION_STATUS_REJECTED, Criteria::NOT_EQUAL);
			$c->addAnd(entryPeer::PARTNER_ID, $this->syndicationFeed->partnerId);
			$c->addAnd(entryPeer::LENGTH_IN_MSECS, 0, Criteria::GREATER_THAN);
			
			$this->addSchedulingToCriteria($c);

			if($this->_ignoreFlavorFilter === false)
			{
				$entryFilter = new entryFilter();
				$entryFilter->setPartnerSearchScope($this->syndicationFeed->partnerId);
				$entryFilter->set ( "_matchand_flavor_params_ids" , $this->syndicationFeed->flavorParamId );
				$entryFilter->attachToCriteria( $c );			
			}
		
			$this->attachCriteriaHandler($c);
			$this->allEntriesCriteria = $c;
		}
		
		self::$micro_times['initdone'] = microtime(true);
		KalturaLog::info("syndicationFeedRenderer- initialization done [".(self::$micro_times['initdone']-self::$micro_times['init'])."]");		
	}
	
	public function fetchEntriesForFeed($count_only = false)
	{
		if($this->syndicationFeed->playlistId)
		{
			$this->fetchEntriesByPlaylist();
			$this->feedTotalEntryCount = count($this->entries);
		}
		else
		{
			$this->fetchAllEntries($count_only);
		}
	}
	
	public function execute()
	{
		$this->fetchEntriesForFeed();
		
		self::$micro_times['startRender'] = microtime(true);
		
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
		
		self::$micro_times['startRenderDone'] = microtime(true);
		KalturaLog::info("syndicationFeedRenderer- render time for ({$this->syndicationFeed->type}) is ".
				 (self::$micro_times['startRenderDone']-self::$micro_times['startRender']));
	}
	
	private function fetchAllEntries($count_only = false)
	{
		self::$micro_times['fecthEntries'] = microtime(true);
		
		ini_set('memory_limit', '128M');
		$peerSelectLimit = self::ENTRY_PEER_LIMIT_QUERY;
		
		$entryCount = entryPeer::doCount($this->allEntriesCriteria);
		$feedCountLimit = ($entryCount > self::MAX_ENTRIES_PER_FEED)? self::MAX_ENTRIES_PER_FEED: $entryCount;
		
		$this->feedTotalEntryCount = $feedCountLimit;
		if($count_only)
		{
			return;
		}

		$iterator = 0;
		$entries = array();
		while(count($this->entries) < $feedCountLimit)
		{
			$this->allEntriesCriteria->setOffset($iterator);
			$this->allEntriesCriteria->setLimit($peerSelectLimit);
			
			$entries = entryPeer::doSelect($this->allEntriesCriteria);
			if(!count($entries) || !$entries)
			{
				break;
			}
			$this->entries = array_merge($this->entries, $entries);
			$entries = array();
			$iterator += $peerSelectLimit;
		}
		
		self::$micro_times['fecthEntriesDone'] = microtime(true);
		KalturaLog::info("syndicationFeedRenderer- fetched entries [".(self::$micro_times['fecthEntriesDone']-self::$micro_times['fecthEntries'])."]");
	}
	
	private function fetchEntriesByPlaylist($detailed = false)
	{
		self::$micro_times['executePlaylist'] = microtime(true);
		
		$extraFilters = array();
		
		// partner scope is always relevant because the executePlaylistById uses the entryFilter
		// from the exterlaFilters it gets
		$entryFilter = new entryFilter();
		$entryFilter->setPartnerSearchScope($this->syndicationFeed->partnerId);
		if($this->_ignoreFlavorFilter === false)
		{
			$entryFilter->set ( "_matchor_flavor_params_ids" , $this->syndicationFeed->flavorParamId );
		}
		$extraFilters[1] = $entryFilter;

		myPlaylistUtils::setAttachCriteriaHandler($this);

		$this->entries = myPlaylistUtils::executePlaylistById( $this->syndicationFeed->partnerId , $this->syndicationFeed->playlistId , $extraFilters , $detailed );
		if(!$this->entries) $this->entries = array();
		self::$micro_times['executePlaylistDone'] = microtime(true);
		KalturaLog::info("syndicationFeedRenderer- playlist executed [".(self::$micro_times['executePlaylistDone']-self::$micro_times['executePlaylist'])."]");
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
		foreach($this->entries as $entry)
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
	
		$flavorAsset = flavorAssetPeer::retrieveByEntryIdAndFlavorParams($kalturaEntry->id,$this->flavor->getId());
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
		foreach($this->entries as $entry)
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
		
		foreach($this->entries as $entry)
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
			
			switch($this->flavor->getFormat())
			{
				case 'mp4':
					$mime = 'video/mp4';
					break;
				case 'm4v':
					$mime = 'video/x-m4v';
					break;
				case 'mov':
					$mime = 'video/quicktime';
					break;
				default:
					$mime = 'video/mp4';
			}
			$enclosure_attr = array(
				'url'=> $url,
				//'length'=>$entry->getLengthInMsecs(), removed by Noa, 25/08/10: we'll need to place here file size (of flavor asset).
				'type'=> $mime,
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
		foreach($this->entries as $entry)
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
	
	private function addSchedulingToCriteria(Criteria $c)
	{
		$startDateCriterion = $c->getNewCriterion(entryPeer::START_DATE, time(), Criteria::LESS_EQUAL);
		$startDateCriterion->addOr($c->getNewCriterion(entryPeer::START_DATE, null));
		
		$endDateCriterion = $c->getNewCriterion(entryPeer::END_DATE, time(), Criteria::GREATER_EQUAL);
		$endDateCriterion->addOr($c->getNewCriterion(entryPeer::END_DATE, null));
		
		$c->addAnd($startDateCriterion);
		$c->addAnd($endDateCriterion);
	}	
	
}
