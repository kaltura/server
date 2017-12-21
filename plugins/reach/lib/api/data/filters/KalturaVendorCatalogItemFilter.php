<?php
/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorCatalogItemFilter extends KalturaVendorCatalogItemBaseFilter
{
	protected function getCoreFilter()
	{
		return new VendorCatalogItemFilter();
	}
	
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		return $this->doGetListResponse($pager, $responseProfile, $type);
	}
	
	public function getTypeListTemplatesResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		return $this->doGetTypeListTemplatesResponse($pager, $responseProfile, $type);
	}
	
	public function doGetListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		$c = new Criteria();
		if($type)
			$c->add(VendorCatalogItemPeer::SERVICE_TYPE, $type);
		
		$filter = $this->toObject();
		$filter->attachToCriteria($c);
		$pager->attachToCriteria($c);
		
		$list = VendorCatalogItemPeer::doSelect($c);
		
		$resultCount = count($list);
		if ($resultCount && $resultCount < $pager->pageSize)
			$totalCount = ($pager->pageIndex - 1) * $pager->pageSize + $resultCount;
		else
		{
			KalturaFilterPager::detachFromCriteria($c);
			$totalCount = VendorCatalogItemPeer::doCount($c);
		}
		
		$response = new KalturaVendorCatalogItemListResponse();
		$response->objects = KalturaVendorCatalogItemArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;
	}
	
	public function doGetTypeListTemplatesResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		$criteria = new Criteria();
		if($type)
			$criteria->add(VendorCatalogItemPeer::SERVICE_FEATURE, $type);
		
		$coreFilter = $this->toObject();
		$coreFilter->attachToCriteria($criteria);
		$criteria->add(VendorCatalogItemPeer::PARTNER_ID, PartnerPeer::GLOBAL_PARTNER);
		$count = VendorCatalogItemPeer::doCount($criteria);
		
		$pager->attachToCriteria($criteria);
		$results = VendorCatalogItemPeer::doSelect($criteria);
		
		$response = new KalturaVendorCatalogItemListResponse();
		$response->objects = KalturaVendorCatalogItemArray::fromDbArray($results, $responseProfile);
		$response->totalCount = $count;
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
