<?php 
/**
 * @package api
 * @subpackage objects
 */
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
	 * Maximum count of results to be returned in playlist execution
	 * 
	 * @var int
	 */
	public $totalResults;
	
	/**
	 * Type of playlist
	 * 
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
	public $executeUrl;
	
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
	
	public function toUpdatableObject ( $object_to_fill , $props_to_skip = array() )
	{		
		if (!$object_to_fill)
			$object_to_fill = new entry();
		$object_to_fill->setType ( entryType::PLAYLIST );
		parent::toUpdatableObject( $object_to_fill )	;
		$object_to_fill->setType ( entryType::PLAYLIST );
//		$object_to_fill->setDataContent( $this->playlistContent );
		return $object_to_fill;
	}
	
	public function validateForInsert($propertiesToSkip = array())
	{
		parent::validateForInsert($propertiesToSkip);
		
		$this->validatePropertyNotNull("playlistType");
		if ($this->playlistType == KalturaPlaylistType::DYNAMIC)
			$this->validatePropertyNotNull("totalResults");
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new entry();
		
		// support filters array only if atleast one filters was specified
		if ($this->playlistType == KalturaPlaylistType::DYNAMIC && $this->filters !== null)
			$this->filtersToPlaylistContentXml();
		
		$dbObject->setType ( entryType::PLAYLIST );
		parent::toObject( $dbObject )	;
		$dbObject->setType ( entryType::PLAYLIST );
//		$dbObject->setDataContent( $this->playlistContent );
		
		return $dbObject;
	}
	
	public function doFromObject($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		if(!$sourceObject)
			return;

		parent::doFromObject($sourceObject, $responseProfile);
		
		if($this->shouldGet('executeUrl', $responseProfile))
			$this->executeUrl = myPlaylistUtils::toPlaylistUrl( $sourceObject , requestUtils::getHost() );
		
		if ($this->shouldGet('filters', $responseProfile) && $this->playlistType == KalturaPlaylistType::DYNAMIC)
			$this->playlistContentXmlToFilters();
	}
	
	public function filtersToPlaylistContentXml()
	{
		$playlistXml = new SimpleXMLElement("<playlist/>");
		$playlistXml->addChild("total_results", $this->totalResults);
		$filtersXml = $playlistXml->addChild("filters");
		if ($this->filters instanceof KalturaMediaEntryFilterForPlaylistArray)
		{
			foreach($this->filters as $filter)
			{
				$filterXml = $filtersXml->addChild("filter");
				$entryFilter = new mediaEntryFilterForPlaylist();
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
			$entryFilter = new mediaEntryFilterForPlaylist();
			$entryFilter->fillObjectFromXml($entryFilterXml, "_"); 
			$filter = new KalturaMediaEntryFilterForPlaylist();
			$filter->fromObject($entryFilter);
			$this->filters[] = $filter;
		}
		
		$this->totalResults = (int)$totalResults; // will cast SimpleXMLElement correctly
	}
}