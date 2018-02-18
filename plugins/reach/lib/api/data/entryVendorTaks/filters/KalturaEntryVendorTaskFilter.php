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
		
		$this->fixFilterUserId($c);
		
		$list = EntryVendorTaskPeer::doSelect($c);
		
		$resultCount = count($list);
		if ($resultCount && $resultCount < $pager->pageSize)
			$totalCount = ($pager->pageIndex - 1) * $pager->pageSize + $resultCount;
		else
		{
			KalturaFilterPager::detachFromCriteria($c);
			$totalCount = EntryVendorTaskPeer::doCount($c);
		}
		
		$response = new KalturaEntryVendorTaskListResponse();
		$response->objects = KalturaEntryVendorTaskArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;
	}
	
	/**
	 * The user_id is infact a puser_id and the kuser_id should be retrieved
	 */
	private function fixFilterUserId(Criteria $c)
	{
		if ($this->userIdEqual !== null) 
		{
			$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::getCurrentPartnerId(), $this->userIdEqual);
			if ($kuser)
				$c->add(EntryVendorTaskPeer::KUSER_ID, $kuser->getId());
			else
				$c->add(EntryVendorTaskPeer::KUSER_ID, -1); // no result will be returned when the user is missing
		}
	}
}
