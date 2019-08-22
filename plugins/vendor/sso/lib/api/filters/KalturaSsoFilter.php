<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaSsoFilter extends KalturaSsoBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new SsoFilter();
	}


	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$c = new Criteria();
		$ssoFilter = $this->toObject();
		$ssoFilter->attachToCriteria($c);
		$c->addAnd(VendorIntegrationPeer::VENDOR_TYPE,VendorTypeEnum::SSO);
		$pager->attachToCriteria($c);

		$list = VendorIntegrationPeer::doSelect($c);

		$resultCount = count($list);
		if ($resultCount && $resultCount < $pager->pageSize)
			$totalCount = ($pager->pageIndex - 1) * $pager->pageSize + $resultCount;
		else
		{
			KalturaFilterPager::detachFromCriteria($c);
			$totalCount = ReachProfilePeer::doCount($c);
		}

		$response = new KalturaSsoListResponse();
		$response->objects = KalturaSsoArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;
	}
}
