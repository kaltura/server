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

	/**
	 * @var int
	 */
	public $catalogItemIdEqual;
	
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
		if ($type)
		{
			$c->add(VendorCatalogItemPeer::SERVICE_FEATURE, $type);
		}

		$filter = $this->toObject();
		$filter->attachToCriteria($c);
		$pager->attachToCriteria($c);
		
		$partnerIdEqual = null;
		if ($this->partnerIdEqual && !in_array(kCurrentContext::$ks_partner_id, array(Partner::ADMIN_CONSOLE_PARTNER_ID, $this->partnerIdEqual)))
		{
			//Add Id that does not exist to break list
			$c->add(VendorCatalogItemPeer::ID, -1);
		}
		elseif ($this->partnerIdEqual && in_array(kCurrentContext::$ks_partner_id, array(Partner::ADMIN_CONSOLE_PARTNER_ID, $this->partnerIdEqual)))
		{
			$partnerIdEqual = $this->partnerIdEqual;
		}
		// Dont filter on partner if requesting partner id is admin console or has the vendor permission
		elseif (!$this->partnerIdEqual && kCurrentContext::$ks_partner_id != Partner::ADMIN_CONSOLE_PARTNER_ID && strtolower(kCurrentContext::$action) !== 'getjobs')
		{
			if (!$this->idEqual)
			{
				$partnerIdEqual = kCurrentContext::$ks_partner_id;
			}
		}
			
		if ($partnerIdEqual)
		{
			$c->add(PartnerCatalogItemPeer::PARTNER_ID, $partnerIdEqual);
			$c->add(PartnerCatalogItemPeer::STATUS, VendorCatalogItemStatus::ACTIVE);
			$c->addJoin(PartnerCatalogItemPeer::CATALOG_ITEM_ID, VendorCatalogItemPeer::ID, Criteria::INNER_JOIN);
			VendorCatalogItemPeer::addSelectColumns($c);
			$c->addSelectColumn(PartnerCatalogItemPeer::CUSTOM_DATA);
		}
		elseif ($this->catalogItemIdEqual)
		{
			return $this->listPartnersWithVendorCatalogItem($pager, $c);
		}

		$list = VendorCatalogItemPeer::doSelect($c);

		$resultCount = count($list);
		if ($resultCount && $resultCount < $pager->pageSize)
		{
			$totalCount = ($pager->pageIndex - 1) * $pager->pageSize + $resultCount;
		}
		else
		{
			KalturaFilterPager::detachFromCriteria($c);
			$totalCount = VendorCatalogItemPeer::doCount($c);
		}

		$responseObjects = KalturaVendorCatalogItemArray::fromDbArray($list, $responseProfile);
		if ($this->partnerIdEqual && kCurrentContext::$ks_partner_id == Partner::ADMIN_CONSOLE_PARTNER_ID)
		{
			$catalogItemFields = VendorCatalogItemPeer::doSelectStmt($c);
			foreach ($responseObjects as $responseObject)
			{
				$responseObject->partnerId = $partnerIdEqual;
				$catalogItemCustomData = $catalogItemFields->fetchColumn(14);
				$partnerCatalogItem = new PartnerCatalogItem();
				$partnerCatalogItem->setCustomData($catalogItemCustomData);
				$responseObject->defaultReachProfileId = $partnerCatalogItem->getDefaultReachProfileId() ? $partnerCatalogItem->getDefaultReachProfileId() : null;
			}
		}

		$response = new KalturaVendorCatalogItemListResponse();
		$response->objects = $responseObjects;
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

	protected function listPartnersWithVendorCatalogItem($pager, $vendorCatalogItemCriteria)
	{
		$partnerCatalogItemCriteria = new Criteria();
		$partnerCatalogItemCriteria->add(PartnerCatalogItemPeer::CATALOG_ITEM_ID, $this->catalogItemIdEqual);
		$partnerCatalogItemCriteria->add(PartnerCatalogItemPeer::STATUS, VendorCatalogItemStatus::ACTIVE);
		$pager->attachToCriteria($partnerCatalogItemCriteria);
		$partnerCatalogItemList = PartnerCatalogItemPeer::doSelect($partnerCatalogItemCriteria);

		$resultCount = count($partnerCatalogItemList);
		if ($resultCount && $resultCount < $pager->pageSize)
		{
			$totalCount = ($pager->pageIndex - 1) * $pager->pageSize + $resultCount;
		}
		else
		{
			KalturaFilterPager::detachFromCriteria($partnerCatalogItemCriteria);
			$totalCount = PartnerCatalogItemPeer::doCount($partnerCatalogItemCriteria);
		}

		$vendorCatalogItemCriteria->add(VendorCatalogItemPeer::ID, $this->catalogItemIdEqual);
		$catalogItem = VendorCatalogItemPeer::doSelectOne($vendorCatalogItemCriteria);

		$catalogItemsList = new KalturaVendorCatalogItemArray();
		foreach ($partnerCatalogItemList as $partnerCatalogItem)
		{
			$catalogItemWithPartnerId = KalturaVendorCatalogItem::getInstance($catalogItem);
			$catalogItemWithPartnerId->partnerId = $partnerCatalogItem->getPartnerId();
			$catalogItemWithPartnerId->defaultReachProfileId = $partnerCatalogItem->getDefaultReachProfileId();
			$catalogItemsList[] = $catalogItemWithPartnerId;
		}

		$response = new KalturaVendorCatalogItemListResponse();
		$response->objects = $catalogItemsList;
		$response->totalCount = $totalCount;
		return $response;
	}
}
