<?php
/**
 * @package api
 * @subpackage objects
 * @abstract
 */
abstract class KalturaBaseSyndicationFeed extends KalturaObject implements IFilterable
{
	/**
	 * 
	 * @var string
	 * @readonly
	 */
	public $id;
	
	/**
	 *
	 * @var string
	 * @readonly
	 */
	public $feedUrl;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * link a playlist that will set what content the feed will include
	 * if empty, all content will be included in feed
	 * 
	 * @var string
	 * @filter order
	 */
	public $playlistId;
	
	/**
	 * feed name
	 * 
	 * @var string
	 * @filter order
	 */
	public $name;
	
	/**
	 * feed status
	 * 
	 * @var KalturaSyndicationFeedStatus
	 * @readonly
	 */
	public $status;
	
	/**
	 * feed type
	 * 
	 * @var KalturaSyndicationFeedType
	 * @insertonly
	 * @filter order
	 */
	public $type;
	
	/**
	 * Base URL for each video, on the partners site
	 * This is required by all syndication types.
	 *
	 * @var string
	 */
	public $landingPage;
	
	/**
	 * Creation date as Unix timestamp (In seconds)
	 * 
	 * @var time
	 * @readonly
	 * @filter order
	 */
	public $createdAt;
	
	/**
	 * allow_embed tells google OR yahoo weather to allow embedding the video on google OR yahoo video results
	 * or just to provide a link to the landing page.
	 * it is applied on the video-player_loc property in the XML (google)
	 * and addes media-player tag (yahoo)
	 *
	 * @var bool
	 */
	public $allowEmbed;
	
	/**
	 * Select a uiconf ID as player skin to include in the kwidget url
	 *
	 * @var int
	 */
	public $playerUiconfId;
	
	/**
	 *
	 * @var int
	 */
	public $flavorParamId;
	
	/**
	 *
	 * @var bool
	 */
	public $transcodeExistingContent;
	
	/**
	 *
	 * @var bool
	 */
	public $addToDefaultConversionProfile;
	
	/**
	 *
	 * @var string
	 */
	public $categories;
	
	/**
	 *
	 * @var int
	 */
	public $storageId;

	/**
	 * @var KalturaSyndicationFeedEntriesOrderBy
	 */
	public $entriesOrderBy;
	
	/**
	 * 
	 * Should enforce entitlement on feed entries
	 * @var bool
	 */
	public $enforceEntitlement;
	
	/**
	 * Set privacy context for search entries that assiged to private and public categories within a category privacy context.
	 *  
	 * @var string
	 * $filter eq
	 */
	public $privacyContext;
	
	/**
	 * Update date as Unix timestamp (In seconds)
	 * 
	 * @var time
	 * @readonly
	 * @filter order
	 */
	public $updatedAt;
	
	/**
	 * @var bool
	 */
	public $useCategoryEntries;
	
	/**
	 * Feed content-type header value
	 * @var string
	 */
	public $feedContentTypeHeader;
	
	
	private static $mapBetweenObjects = array("id", "partnerId", "playlistId", "name", "status", "type", "landingPage", "createdAt", "playerUiconfId", "allowEmbed", "flavorParamId", "transcodeExistingContent", "addToDefaultConversionProfile", "categories", "storageId", "entriesOrderBy", "enforceEntitlement", "privacyContext", "updatedAt", "useCategoryEntries", "feedContentTypeHeader",);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	function getExtraFilters()
	{
		return array();
	}
	
	function getFilterDocs()
	{
		return array();
	}
	
	public function validatePlaylistId()
	{
		if(! $this->playlistId) // we allow empty playlistID. this means all content
			return;
		
		$playlistEntry = entryPeer::retrieveByPK($this->playlistId);
		if(! $playlistEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->playlistId);
	}
	
	public function validateStorageId($partnerId)
	{
		if (is_null($this->storageId) || $this->storageId instanceof KalturaNullField)
			return;
			
		$storage = StorageProfilePeer::retrieveByPK($this->storageId);
		if(!$storage)
			throw new KalturaAPIException(KalturaErrors::SYNDICATION_FEED_INVALID_STORAGE_ID);

		$partner = PartnerPeer::retrieveByPK($partnerId);
		
		// storage doesn't belong to the partner
		if($storage->getPartnerId() != $partner->getId())
			throw new KalturaAPIException(KalturaErrors::SYNDICATION_FEED_INVALID_STORAGE_ID);
			
		// partner configured to use kaltura data centers only
		if($partner->getStorageServePriority() ==  StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY)
			throw new KalturaAPIException(KalturaErrors::SYNDICATION_FEED_KALTURA_DC_ONLY);
	}
	
	public function doFromObject($source_object, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($source_object, $responseProfile);
		if($this->shouldGet('feedUrl', $responseProfile) && isset($this->id) && $this->id)
		{
			$this->feedUrl = kConf::get('apphome_url') . '/api_v3/getFeed.php';
			
			if($this->partnerId)
				$this->feedUrl .= '?partnerId=' . $this->partnerId . '&';
			else
				$this->feedUrl .= '?';
				
			$this->feedUrl .= 'feedId=' . $this->id;
		}
	}

	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill))
			$object_to_fill = new syndicationFeed();
		$object_to_fill->init();
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}


	public function getPropertiesToValidate()
	{
		return array();
	}
}