<?php
/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorProfileFilter extends KalturaVendorProfileBaseFilter
{
	protected function getCoreFilter()
	{
		return new VendorProfileFilter();
	}
	
	/* (non-PHPdoc)
 	 * @see KalturaRelatedFilter::getListResponse()
 	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$c = new Criteria();
		$filter = $this->toObject();
		$filter->attachToCriteria($c);
		$pager->attachToCriteria($c);
		
		$list = VendorProfilePeer::doSelect($c);
		
		$resultCount = count($list);
		if ($resultCount && $resultCount < $pager->pageSize)
			$totalCount = ($pager->pageIndex - 1) * $pager->pageSize + $resultCount;
		else
		{
			KalturaFilterPager::detachFromCriteria($c);
			$totalCount = VendorProfilePeer::doCount($c);
		}
		
		$response = new KalturaVendorProfileListResponse();
		$response->objects = KalturaVendorProfileArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;
	}
}
