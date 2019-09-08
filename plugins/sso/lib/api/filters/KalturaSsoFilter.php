<?php
/**
 * @package plugins.sso
 * @subpackage api.filters
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
		$pager->attachToCriteria($c);

		$list = SsoPeer::doSelect($c);

		$resultCount = count($list);
		if ($resultCount && $resultCount < $pager->pageSize)
		{
			$totalCount = ($pager->pageIndex - 1) * $pager->pageSize + $resultCount;
		}
		else
		{
			KalturaFilterPager::detachFromCriteria($c);
			$totalCount = SsoPeer::doCount($c);
		}

		$response = new KalturaSsoListResponse();
		$response->objects = KalturaSsoArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;
	}
}
