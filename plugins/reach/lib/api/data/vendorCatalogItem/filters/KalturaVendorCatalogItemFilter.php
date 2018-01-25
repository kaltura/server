<?php
/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorCatalogItemFilter extends KalturaVendorCatalogItemBaseFilter
{
	/**
	 * @var int
	 */
	public $partnerIdEqual;
	
	protected function getCoreFilter()
	{
		return new VendorCatalogItemFilter();
	}
	
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		return $this->doGetListResponse($pager, $responseProfile, $type);
	}
	
	public function doGetListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		$c = new Criteria();
		if($type)
			$c->add(VendorCatalogItemPeer::SERVICE_FEATURE, $type);
		
		$filter = $this->toObject();
		$filter->attachToCriteria($c);
		$pager->attachToCriteria($c);
		
		if($this->partnerIdEqual)
		{
			$c->add(PartnerCatalogItemPeer::PARTNER_ID, $this->partnerIdEqual);
			$c->addJoin(PartnerCatalogItemPeer::CATALOG_ITEM_ID, VendorCatalogItemPeer::ID, Criteria::INNER_JOIN);
		}
		
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
	
	/* (non-PHPdoc)
 	 * @see KalturaRelatedFilter::getListResponse()
 	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		return $this->getTypeListResponse($pager, $responseProfile);
	}
}
