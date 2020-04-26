<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaPlaylistFilter extends KalturaPlaylistBaseFilter
{
	/**
	 * @var KalturaPlaylistType
	 */
	public $playListTypeEqual;

	/**
	 * @var string
	 */
	public $playListTypeIn;

	static private $map_between_objects = array
	(
		"playListTypeEqual" => "_eq_media_type",
		"playListTypeIn" => "_in_media_type",
	);

	/* (non-PHPdoc)
	 * @see KalturaBaseEntryFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		list($list, $totalCount) = $this->doGetListResponse($pager);
		
	    $newList = KalturaPlaylistArray::fromDbArray($list, $responseProfile);
		$response = new KalturaPlaylistListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		
		return $response;
	}

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}
