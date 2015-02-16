<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaThumbParamsFilter extends KalturaThumbParamsBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaAssetParamsFilter::getTypeListResponse()
	 */
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null, array $types = null)
	{
		list($list, $totalCount) = $this->doGetListResponse($pager, $types);
		
		$response = new KalturaThumbParamsListResponse();
		$response->objects = KalturaThumbParamsArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;  
	}
}
