<?php 

class KalturaPlaylist extends KalturaBaseEntry
{
	/**
	 * Content of the playlist - 
	 * XML if the playlistType is dynamic 
	 * text if the playlistType is static 
	 * url if the playlistType is mRss 
	 * @var string
	 */
	public $playlistContent;
	
	/**
	 * 
	 * @var KalturaMediaEntryFilterForPlaylistArray
	 */
	public $filters;
	
	/**
	 * 
	 * @var int
	 */
	public $totalResults;
	
	/**
	 * Type of playlist  
	 * @var KalturaPlaylistType
	 */	
	public $playlistType;

	/**
	 * Number of plays
	 * @var int
	 * @readonly
	 */
	public $plays;
	
	/**
	 * Number of views
	 * @var int
	 * @readonly
	 */
	public $views;
	
	/**
	 * The duration in seconds
	 * @var int
	 * @readonly
	 */
	public $duration;
	
	/**
	 * The url for this playlist
	 * @var string
	 * @readonly
	 */
	private $executeUrl;
	
	private static $map_between_objects = array
	(
		"playlistType" => "mediaType" ,
		"playlistContent" => "dataContent" ,	// MUST APPEAR after THE playlistType	 
	 	"plays" , 
	 	"views" , 
	 	"duration" 
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}	
	
	public function toUpdatablePlaylist()
	{
		// support filters array only if atleast one filters was specified
		if ($this->playlistType == KalturaPlaylistType::DYNAMIC && $this->filters && $this->filters->count > 0)
			$this->filtersToPlaylistContentXml();
			
		$playlist = new entry();
		$playlist->setType ( entry::ENTRY_TYPE_PLAYLIST );
		parent::toUpdatableObject( $playlist )	;
		$playlist->setType ( entry::ENTRY_TYPE_PLAYLIST );
		$playlist->setDataContent( $this->playlistContent );
		return $playlist;
	}
	
	public function toPlaylist()
	{
		// support filters array only if atleast one filters was specified
		if ($this->playlistType == KalturaPlaylistType::DYNAMIC && $this->filters && $this->filters->count > 0)
			$this->filtersToPlaylistContentXml();
		
		$playlist = new entry();
		$playlist->setType ( entry::ENTRY_TYPE_PLAYLIST );
		parent::toObject( $playlist )	;
		$playlist->setType ( entry::ENTRY_TYPE_PLAYLIST );
		$playlist->setDataContent( $this->playlistContent );
		
		return $playlist;
	}
	
	public function fromPlaylist ( entry  $playlist )
	{
		parent::fromObject( $playlist );
		$host = requestUtils::getHost();
		$this->executeUrl = myPlaylistUtils::toPlaylistUrl( $playlist , $host );
		
		if ($this->playlistType == KalturaPlaylistType::DYNAMIC)
			$this->playlistContentXmlToFilters();
	}
	
	public function filtersToPlaylistContentXml()
	{
		$playlistXml = new SimpleXMLElement("<playlist/>");
		$playlistXml->addChild("total_results", $this->totalResults);
		$filtersXml = $playlistXml->addChild("filters");
		$xml = new SimpleXMLElement(); 
		if ($this->filters instanceof KalturaMediaEntryFilterForPlaylistArray)
		{
			foreach($this->filters as $filter)
			{
				$filterXml = $filtersXml->addChild("filter");
				$entryFilter = new entryFilter();
				$filter->toObject($entryFilter);
				$fields = $entryFilter->fields;
				foreach($fields as $field => $value)
				{
					$field = substr($field, 1);
					if ($value != null)
						$filterXml->addChild($field, htmlspecialchars($value));
				}
				
				$entryFilter->addAdvancedSearchToXml($filterXml);
			}
		}
		$this->playlistContent = $playlistXml->asXML();
	}
	
	public function playlistContentXmlToFilters()
	{
		list($totalResults, $listOfFilters) = myPlaylistUtils::getPlaylistFilterListStruct($this->playlistContent);
		// $totalResults is SimpleXMLElement
		$this->filters = new KalturaMediaEntryFilterForPlaylistArray();
		foreach($listOfFilters as $entryFilterXml)
		{
			$entryFilter = new entryFilter();
			$entryFilter->fillObjectFromXml($entryFilterXml, "_"); 
			$filter = new KalturaMediaEntryFilterForPlaylist();
			$filter->fromObject($entryFilter);
			$this->filters[] = $filter;
		}
		
		$this->totalResults = (int)$totalResults; // will cast SimpleXMLElement correctly
	}
}