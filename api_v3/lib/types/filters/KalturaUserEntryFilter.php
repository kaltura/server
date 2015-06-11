<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaUserEntryFilter extends KalturaUserEntryBaseFilter
{
	/**
	 * @return baseObjectFilter
	 */
	protected function getCoreFilter()
	{
		// TODO: Implement getCoreFilter() method.
		return new UserEntryFilter();
	}

	/**
	 * @param KalturaFilterPager $pager
	 * @param KalturaDetachedResponseProfile $responseProfile
	 * @return KalturaListResponse
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{

		$response = new KalturaUserEntryListResponse();
		if ( in_array(kCurrentContext::getCurrentSessionType(), array(kSessionBase::SESSION_TYPE_NONE,kSessionBase::SESSION_TYPE_WIDGET)) )
		{
			$response->totalCount = 0;
			return $response;
		}

		$c = new Criteria();
		$userEntryFilter = $this->toObject();
		$userEntryFilter->attachToCriteria($c);

		if (!is_null($pager))
		{
			$pager->attachToCriteria($c);
		}
		$list = UserEntryPeer::doSelect($c);

		$resultCount = count($list);
		if ($resultCount && ($resultCount < $pager->pageSize))
		{
			$totalCount = ($pager->pageIndex - 1) * $pager->pageSize + $resultCount;
		}
		else
		{
			KalturaFilterPager::detachFromCriteria($c);
			$totalCount = assetPeer::doCount($c);
		}

		$response->totalCount = $totalCount;
		$response->objects = KalturaUserEntryArray::fromDbArray($list, $responseProfile);
		return $response;
	}

}
