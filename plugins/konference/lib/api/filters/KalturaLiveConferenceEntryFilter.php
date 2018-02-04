<?php
/**
 * @package plugins.konference
 * @subpackage api.filters
 */
class KalturaLiveConferenceEntryFilter extends KalturaLiveConferenceEntryBaseFilter
{
	public function __construct()
	{
		$this->typeIn = KonferencePlugin::getCoreValue('entryType', ConferenceEntryType::CONFERENCE);
	}

//	/* (non-PHPdoc)
//	 * @see KalturaBaseEntryFilter::getListResponse()
//	 */
//	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
//	{
//		list($list, $totalCount) = $this->doGetListResponse($pager);
//
//	    $newList = KalturaLiveStreamEntryArray::fromDbArray($list, $responseProfile);
//		$response = new KalturaBaseEntryListResponse();
//		$response->objects = $newList;
//		$response->totalCount = $totalCount;
//
//		return $response;
//	}
}
