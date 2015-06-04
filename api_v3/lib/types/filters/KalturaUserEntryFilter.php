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
		$userEntryFilter = new UserEntryFilter();
		$this->toObject($userEntryFilter);
		$userEntryFilter->attachToCriteria($c);

		if (!is_null($pager))
		{
			$pager->attachToCriteria($c);
		}

		$list = UserEntryPeer::doSelect($c);

		$response->totalCount = UserEntryPeer::doCount($c);
		$response->objects = KalturaUserEntryArray::fromDbArray($list, $responseProfile);
		return $response;
	}

}
