<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaMixEntryFilter extends KalturaMixEntryBaseFilter
{
	public function __construct()
	{
		$this->typeIn = KalturaEntryType::MIX;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaBaseEntryFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null)
	{
		list($list, $totalCount) = $this->doGetListResponse($pager);
		
	    $newList = KalturaMixEntryArray::fromDbArray($list, $responseProfile);
		$response = new KalturaBaseEntryListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		
		return $response;
	}
}
