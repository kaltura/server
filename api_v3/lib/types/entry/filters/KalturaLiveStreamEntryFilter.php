<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaLiveStreamEntryFilter extends KalturaLiveStreamEntryBaseFilter
{
	public function __construct()
	{
		$this->typeIn = KalturaEntryType::LIVE_STREAM;
	}

	/* (non-PHPdoc)
	 * @see KalturaBaseEntryFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null)
	{
		list($list, $totalCount) = $this->doGetListResponse($pager);
		
	    $newList = KalturaLiveStreamEntryArray::fromDbArray($list, $responseProfile);
		$response = new KalturaBaseEntryListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		
		return $response;
	}
}
