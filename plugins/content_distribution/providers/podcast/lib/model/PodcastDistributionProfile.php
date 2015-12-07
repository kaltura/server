<?php
/**
 * @package plugins.podcastDistribution
 * @subpackage model
 */
class PodcastDistributionProfile extends DistributionProfile
{
	const CUSTOM_DATA_METADATA_PROFILE_ID = 'metadataProfileId';
	const CUSTOM_DATA_FEED_ID = 'feedId';
	const METADATA_FIELD_KEYWORDS = 'PodcastKeywords';
	const METADATA_FIELD_DESCRIPTION = 'PodcastDescription';

	const ENTRY_NAME_MINIMUM_LENGTH = 1;
	const ENTRY_NAME_MAXIMUM_LENGTH = 255;
	const ENTRY_DESC_MINIMUM_LENGTH = 1;
	
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
//		return PodcastDistributionProvider::get();
		return PodcastDistributionPlugin::getProvider();

	}

	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{
		$missingDesc = false; 
		$missingTags = false;
		
		$validationErrors = parent::validateForSubmission($entryDistribution, $action);
		
		$entry = entryPeer::retrieveByPK($entryDistribution->getEntryId());
		if(!$entry)
		{
			KalturaLog::err("Entry [" . $entryDistribution->getEntryId() . "] not found");
			$validationErrors[] = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, 'entry', 'entry not found');
			return $validationErrors;
		}
		
		// validate entry name minumum length of 1 character
		if(strlen($entry->getName()) < self::ENTRY_NAME_MINIMUM_LENGTH)
		{
			$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, entryPeer::NAME, '');
			$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_SHORT);
			$validationError->setValidationErrorParam(self::ENTRY_NAME_MINIMUM_LENGTH);
			$validationErrors[] = $validationError;
		}
		if(strlen($entry->getName()) > self::ENTRY_NAME_MAXIMUM_LENGTH)
		{
			$description = 'entry name length must be between ' . self::ENTRY_NAME_MINIMUM_LENGTH . ' and ' . self::ENTRY_NAME_MAXIMUM_LENGTH;
			$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, entryPeer::NAME, $description);
			$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_LONG);
			$validationError->setValidationErrorParam(self::ENTRY_NAME_MAXIMUM_LENGTH);
			$validationErrors[] = $validationError;
		}

		if(strlen($entry->getDescription()) < self::ENTRY_DESC_MINIMUM_LENGTH)
			$missingDesc = true;
		
		if(strlen($entry->getTags()) < self::ENTRY_DESC_MINIMUM_LENGTH)
			$missingTags = true;
		
		if(!class_exists('MetadataProfile'))
			return $validationErrors;
			
		$metadataFields = array(
			self::METADATA_FIELD_KEYWORDS,
			self::METADATA_FIELD_DESCRIPTION,
		);
		
		$metadataProfileId = $this->getMetadataProfileId();

		$metadatas = MetadataPeer::retrieveAllByObject(MetadataObjectType::ENTRY, $entryDistribution->getEntryId());
		
		foreach($metadataFields as $index => $metadataField)
		{
			$values = $this->findMetadataValue($metadatas, $metadataField);
			
			if(!count($values))
			{ 
				switch($metadataField)
				{
					case self::METADATA_FIELD_DESCRIPTION: 
						if ($missingDesc)
							$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, entryPeer::DESCRIPTION, "");
						break;
					case self::METADATA_FIELD_KEYWORDS:
						if ($missingTags)
							$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, entryPeer::TAGS, "");
						break;
					default:
						$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, $metadataField);
				}
			}
			
			foreach($values as $value)
			{
				if(!strlen($value))
				{
					$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $metadataField, "");
					$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_EMPTY);
					$validationError->setMetadataProfileId($metadataProfileId);
					$validationErrors[] = $validationError;
					break;
				}
			}
		}
		
		return $validationErrors;
	}	
	/* (non-PHPdoc)
	 * @see BaseDistributionProfile::preSave()
	 */
	public function preSave(PropelPDO $con = null)
	{
		if($this->getFeedId())
			return parent::preSave($con);	
			
		// Creating podcast feed
		$podcastFeed = new genericSyndicationFeed();
		$podcastFeed->setPartnerId($this->getPartnerId());
		$podcastFeed->setStatus(syndicationFeed::SYNDICATION_ACTIVE);
		$podcastFeed->setDisplayInSearch(mySearchUtils::DISPLAY_IN_SEARCH_SYSTEM);
		$podcastFeed->setAllowEmbed(false);
		$podcastFeed->setType(syndicationFeedType::KALTURA);
		$podcastFeed->setAddXmlHeader(true);
		$podcastFeed->save();
		
		$this->setFeedId($podcastFeed->getId());
		KalturaLog::log("Podcast feed created id [" . $this->getFeedId() . "]");
		
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
			
		$podcastFeed = syndicationFeedPeer::retrieveByPK($this->getFeedId());
		if(!$podcastFeed || !($podcastFeed instanceof genericSyndicationFeed))
			return;

		// "Creates advanced filter on distribution profile
		$distributionAdvancedSearch = new ContentDistributionSearchFilter();
		$distributionAdvancedSearch->setDistributionProfileId($this->getId());
		$distributionAdvancedSearch->setDistributionSunStatus(EntryDistributionSunStatus::AFTER_SUNRISE);
		$distributionAdvancedSearch->setEntryDistributionStatus(EntryDistributionStatus::READY);
		$distributionAdvancedSearch->setEntryDistributionFlag(EntryDistributionDirtyStatus::NONE);
			
		//Creates entry filter with advanced filter
		$entryFilter = new entryFilter();
		$entryFilter->setPartnerSearchScope($this->getPartnerId());
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
		$playlist->setCreatorKuserId(kCurrentContext::$uid);
		$playlist->setDisplayInSearch(mySearchUtils::DISPLAY_IN_SEARCH_SYSTEM);
		$playlist->setPartnerId($this->getPartnerId());
		$playlist->setStatus(entryStatus::READY);
		$playlist->setKshowId(null);
		$playlist->setType(entryType::PLAYLIST);
		$playlist->setMediaType(entry::ENTRY_MEDIA_TYPE_XML);
		$playlist->setDataContent($playlistContent);
		$playlist->save();
		
		KalturaLog::log("Playlist [" . $playlist->getId() . "] created");
		
		// creates feed based on the playlist
		$podcastFeed->setPlaylistId($playlist->getId());
		$podcastFeed->save();
	}

	/* (non-PHPdoc)
	 * @see BaseDistributionProfile::postSave()
	 */
	public function postSave(PropelPDO $con = null) 
	{
		parent::postSave($con);
	
		if($this->xslModified && $this->xsl && $this->getFeedId())
		{
			$podcastFeed = syndicationFeedPeer::retrieveByPK($this->getFeedId());
			if($podcastFeed && $podcastFeed instanceof genericSyndicationFeed)
			{
				$podcastFeed->setType(syndicationFeedType::KALTURA_XSLT);
				$podcastFeed->incrementVersion();
				$podcastFeed->save();
				$syncKey = $podcastFeed->getSyncKey(genericSyndicationFeed::FILE_SYNC_SYNDICATION_FEED_XSLT);
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
	public function getMetadataProfileId()			{return $this->getFromCustomData(self::CUSTOM_DATA_METADATA_PROFILE_ID);}
		
	public function setFeedId($v)		{$this->putInCustomData(self::CUSTOM_DATA_FEED_ID, $v);}
	public function setMetadataProfileId($v)		{$this->putInCustomData(self::CUSTOM_DATA_METADATA_PROFILE_ID, $v);}	
}