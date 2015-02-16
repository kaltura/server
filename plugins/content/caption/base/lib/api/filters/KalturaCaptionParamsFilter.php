<?php
/**
 * @package plugins.caption
 * @subpackage api.filters
 */
class KalturaCaptionParamsFilter extends KalturaCaptionParamsBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaAssetParamsFilter::getTypeListResponse()
	 */
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null, array $types = null)
	{
		list($list, $totalCount) = $this->doGetListResponse($pager, $types);
		
		$response = new KalturaCaptionParamsListResponse();
		$response->objects = KalturaCaptionParamsArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;  
	}
}
