<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model
 */
class SyndicationDistributionProfile extends DistributionProfile
{
	const CUSTOM_DATA_FEED_ID = 'feedId';
	
	/**
	 * @var string
	 */
	protected $xsl = null;

	/**
	 * @var bool
	 */
	protected $xslModified = false;
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return SyndicationDistributionProvider::get();
	}

	/* (non-PHPdoc)
	 * @see BaseDistributionProfile::preSave()
	 */
	public function preSave(PropelPDO $con = null)
	{
		if($this->getFeedId())
			return parent::preSave($con);	
			
		// Creating syndication feed
		$syndicationFeed = new genericSyndicationFeed();
		$syndicationFeed->setPartnerId($this->getPartnerId());
		$syndicationFeed->setStatus(SyndicationFeed::SYNDICATION_ACTIVE);
		$syndicationFeed->setDisplayInSearch(mySearchUtils::DISPLAY_IN_SEARCH_NONE);
		$syndicationFeed->setAllowEmbed(false);
		$syndicationFeed->setType(syndicationFeedType::KALTURA);
		$syndicationFeed->save();
		
		$this->setFeedId($syndicationFeed->getId());
		KalturaLog::log("Syndication feed created id [" . $this->getFeedId() . "]");
		
		return parent::preSave($con);	
	}
	
	/* (non-PHPdoc)
	 * @see BaseDistributionProfile::postInsert()
	 */
	public function postInsert(PropelPDO $con = null)
	{
		parent::postInsert($con);
		
		if(!$this->getFeedId() || !$this->getId())
			return;
			
		$syndicationFeed = syndicationFeedPeer::retrieveByPK($this->getFeedId());
		if(!$syndicationFeed || !($syndicationFeed instanceof genericSyndicationFeed))
			return;

		// "Creates advanced filter on distribution profile
		$distributionAdvancedSearch = new ContentDistributionSearchFilter();
		$distributionAdvancedSearch->setDistributionProfileId($this->getId());
		$distributionAdvancedSearch->setDistributionSunStatus(EntryDistributionSunStatus::AFTER_SUNRISE);
		$distributionAdvancedSearch->setEntryDistributionStatus(EntryDistributionStatus::READY);
		$distributionAdvancedSearch->setEntryDistributionFlag(EntryDistributionDirtyStatus::NONE);
			
		//Creates entry filter with advanced filter
		$entryFilter = new entryFilter();
		$entryFilter->setPartnerIdEquel($this->getPartnerId());
		$entryFilter->setAdvancedSearch($distributionAdvancedSearch);
		
		// Creates playlist with entry filter
		$playlistXml = new SimpleXMLElement("<playlist/>");
		$filtersXml = $playlistXml->addChild("filters");
		$filterXml = $filtersXml->addChild("filter");
		$entryFilter->toXml($filterXml);
		$playlistContent = $playlistXml->asXML();

		// creates playlist based on the filter XML
		$playlist = new entry();
		$playlist->setKuserId(kCurrentContext::$uid);
		$playlist->setDisplayInSearch(mySearchUtils::DISPLAY_IN_SEARCH_NONE);
		$playlist->setPartnerId($this->getPartnerId());
		$playlist->setStatus(entryStatus::READY);
		$playlist->setKshowId(null);
		$playlist->setType(entryType::PLAYLIST);
		$playlist->setMediaType(entry::ENTRY_MEDIA_TYPE_XML);
		$playlist->setDataContent($playlistContent);
		$playlist->save();
		
		KalturaLog::log("Playlist [" . $playlist->getId() . "] created");
		
		// creates feed based on the playlist
		$syndicationFeed->setPlaylistId($playlist->getId());
		$syndicationFeed->save();
	}

	/* (non-PHPdoc)
	 * @see BaseDistributionProfile::postSave()
	 */
	public function postSave(PropelPDO $con = null) 
	{
		parent::postSave($con);
	
		if($this->xslModified && $this->xsl && $this->getFeedId())
		{
			KalturaLog::debug("loads syndication feed id [" . $this->getFeedId() . "]");
			$syndicationFeed = syndicationFeedPeer::retrieveByPK($this->getFeedId());
			KalturaLog::debug("syndication feed id [" . $syndicationFeed->getId() . "] syndication feed type [" . get_class($syndicationFeed) . "]");
			if($syndicationFeed && $syndicationFeed instanceof genericSyndicationFeed)
			{
				KalturaLog::log("Updating syndication feed xsl");
				$syndicationFeed->setType(syndicationFeedType::KALTURA_XSLT);
				$syndicationFeed->incrementVersion();
				$syndicationFeed->save();
				$syncKey = $syndicationFeed->getSyncKey(genericSyndicationFeed::FILE_SYNC_SYNDICATION_FEED_XSLT);
				kFileSyncUtils::file_put_contents($syncKey, $this->xsl, false);
				$this->xslModified = false;
			}
		}
	}

	/**
	 * @return string $xsl
	 */
	public function getXsl()
	{
		if($this->xsl)
			return $this->xsl;
			
		if(!$this->getFeedId())
			return null;
			
		$feed = syndicationFeedPeer::retrieveByPK($this->getFeedId());
		if(!$feed || !($feed instanceof genericSyndicationFeed))
			return null;
			
		$syncKey = $feed->getSyncKey(genericSyndicationFeed::FILE_SYNC_SYNDICATION_FEED_XSLT);
		$this->xsl = kFileSyncUtils::file_get_contents($syncKey, true, false);
		return $this->xsl;
	}
	
	/**
	 * @param string $xsl
	 */
	public function setXsl($xsl)
	{
		$this->setUpdatedAt(time());
		$this->xsl = $xsl;
		$this->xslModified = true;
	}

	public function getFeedId()			{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_ID);}
		
	public function setFeedId($v)		{$this->putInCustomData(self::CUSTOM_DATA_FEED_ID, $v);}
}