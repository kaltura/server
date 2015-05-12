<?php

/**
 * @package api
 * @subpackage filters
 */
class KalturaUserEntryFilter extends KalturaRelatedFilter
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
		return new KalturaUserEntryListResponse();
	}


}