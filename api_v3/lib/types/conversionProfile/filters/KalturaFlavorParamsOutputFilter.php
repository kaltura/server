<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaFlavorParamsOutputFilter extends KalturaFlavorParamsOutputBaseFilter
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
		
		$response = new KalturaFlavorParamsOutputListResponse();
		$response->objects = KalturaFlavorParamsOutputArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;  
	}
}
