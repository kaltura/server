<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaThumbParamsOutputFilter extends KalturaThumbParamsOutputBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new assetParamsOutputFilter();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaAssetParamsFilter::getTypeListResponse()
	 */
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, array $types = null)
	{
		list($list, $totalCount) = $this->doGetListResponse($pager, $types);
		
		$response = new KalturaThumbParamsOutputListResponse();
		$response->objects = KalturaThumbParamsOutputArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;  
	}
}
