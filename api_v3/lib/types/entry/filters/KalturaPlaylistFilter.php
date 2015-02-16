<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaPlaylistFilter extends KalturaPlaylistBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaBaseEntryFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null)
	{
		list($list, $totalCount) = $this->doGetListResponse($pager);
		
	    $newList = KalturaPlaylistArray::fromDbArray($list, $responseProfile);
		$response = new KalturaPlaylistListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		
		return $response;
	}
}
