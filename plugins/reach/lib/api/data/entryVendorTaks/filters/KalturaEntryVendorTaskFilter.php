<?php
/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaEntryVendorTaskFilter extends KalturaEntryVendorTaskBaseFilter
{
	protected function getCoreFilter()
	{
		return new EntryVendorTaskFilter();
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
		
		$list = EntryVendorTaskPeer::doSelect($c);
		
		$resultCount = count($list);
		if ($resultCount && $resultCount < $pager->pageSize)
			$totalCount = ($pager->pageIndex - 1) * $pager->pageSize + $resultCount;
		else
		{
			KalturaFilterPager::detachFromCriteria($c);
			$totalCount = VendorProfilePeer::doCount($c);
		}
		
		$response = new KalturaEntryVendorTaskListResponse();
		$response->objects = KalturaEntryVendorTaskArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;
	}

}
