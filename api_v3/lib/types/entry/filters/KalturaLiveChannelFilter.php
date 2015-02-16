<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaLiveChannelFilter extends KalturaLiveChannelBaseFilter
{
	public function __construct()
	{
		$this->typeIn = KalturaEntryType::LIVE_CHANNEL;
	}

	/* (non-PHPdoc)
	 * @see KalturaBaseEntryFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null)
	{
		list($list, $totalCount) = $this->doGetListResponse($pager);
		
	    $newList = KalturaLiveChannelArray::fromDbArray($list, $responseProfile);
		$response = new KalturaLiveChannelListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		
		return $response;
	}
}
