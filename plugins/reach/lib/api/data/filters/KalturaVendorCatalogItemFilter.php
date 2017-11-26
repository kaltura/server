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
		return $this->doGetTypeListResponse($pager, $responseProfile, $type);
	}
	
	public function doGetListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		$c = new Criteria();
		if($type)
		{
			$c->add(VendorCatalogItemPeer::SERVICE_TYPE, $type);
		}
		
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
		$response->objects = KalturaVendroCatalogItemArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;
	}
	
	public function doGetTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		$coreFilter = new VendorCatalogItemFilter();
		$this->toObject($coreFilter);
		
		$criteria = new Criteria();
		$coreFilter->attachToCriteria($criteria);
		$criteria->add(VendorCatalogItemPeer::PARTNER_ID, PartnerPeer::GLOBAL_PARTNER);
		$count = VendorCatalogItemPeer::doCount($criteria);
		
		$pager->attachToCriteria($criteria);
		$results = VendorCatalogItemPeer::doSelect($criteria);
		
		$response = new KalturaVendorCatalogItemListResponse();
		$response->objects = KalturaVendroCatalogItemArray::fromDbArray($results, $this->getResponseProfile());
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
