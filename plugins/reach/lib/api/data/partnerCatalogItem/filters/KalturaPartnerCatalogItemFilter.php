<?php
/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaPartnerCatalogItemFilter extends KalturaPartnerCatalogItemBaseFilter
{
	protected function getCoreFilter()
	{
		return new PartnerCatalogItemFilter();
	}

	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		return $this->doGetListResponse($pager, $responseProfile);
	}

	public function doGetListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$c = new Criteria();

		$filter = $this->toObject();
		$filter->attachToCriteria($c);
		$pager->attachToCriteria($c);

		$list = PartnerCatalogItemPeer::doSelect($c);

		$resultCount = count($list);
		if ($resultCount && $resultCount < $pager->pageSize)
		{
			$totalCount = ($pager->pageIndex - 1) * $pager->pageSize + $resultCount;
		}
		else
		{
			KalturaFilterPager::detachFromCriteria($c);
			$totalCount = PartnerCatalogItemPeer::doCount($c);
		}

		$response = new KalturaPartnerCatalogItemListResponse();
		$response->objects = KalturaPartnerCatalogItemArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;
	}

	/* (non-PHPdoc)
 	 * @see KalturaRelatedFilter::getListResponse()
 	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		return $this->getTypeListResponse($pager, $responseProfile);
	}
}
