<?php
/**
 * @package api
 * @subpackage objects
 * @abstract
 */
class KalturaBaseSyndicationFeed extends KalturaObject implements IFilterable
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
	 * @readonly
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
	 * @var int
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
	
	private static $mapBetweenObjects = array("id", "partnerId", "playlistId", "name", "status", "type", "landingPage", "createdAt", "playerUiconfId", "allowEmbed", "flavorParamId", "transcodeExistingContent", "addToDefaultConversionProfile", "categories");
	
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
	
	public function fromObject($source_object)
	{
		parent::fromObject($source_object);
		if(isset($this->id) && $this->id)
		{
			$this->feedUrl = 'http://' . kConf::get('www_host') . '/api_v3/getFeed.php';
			
			if($this->partnerId)
				$this->feedUrl .= '?partnerId=' . $this->partnerId . '&';
			else
				$this->feedUrl .= '?';
				
			$this->feedUrl .= 'feedId=' . $this->id;
		}
	}
}