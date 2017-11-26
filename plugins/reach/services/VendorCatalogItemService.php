<?php
/**
 * Vendor Catalog Item Service
 *
 * @service vendorCatalogItem
 * @package plugins.reach
 * @subpackage api.services
 * @throws KalturaErrors::SERVICE_FORBIDDEN
 */

class VendorCatalogItemService extends KalturaBaseService
{
	
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
//		if(!ReachPlugin::isAllowedPartner($this->getPartnerId()))
//			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, ReachPlugin::PLUGIN_NAME);
		
		$this->applyPartnerFilterForClass('vendorCatalogItem');
		$this->applyPartnerFilterForClass('partnerCatalogItem');
	}
	
	protected function globalPartnerAllowed($actionName)
	{
		if ($actionName === 'list') {
			return true;
		}
		
		return parent::globalPartnerAllowed($actionName);
	}
	
	/**
	 * Allows you to add an service catalog item
	 *
	 * @action add
	 * @param KalturaVendorCatalogItem $vendorCatalogItem
	 * @return KalturaVendorCatalogItem
	 */
	public function addAction(KalturaVendorCatalogItem $vendorCatalogItem)
	{
		$dbVendorCatalogItem = $vendorCatalogItem->toInsertableObject();
		
		/* @var $dbVendorCatalogItem VendorCatalogItem */
		$dbVendorCatalogItem->setPartnerId($this->impersonatedPartnerId);
		$dbVendorCatalogItem->save();
		
		// return the saved object
		$vendorCatalogItem = KalturaVendorCatalogItem::getInstance($dbVendorCatalogItem, $this->getResponseProfile());
		$vendorCatalogItem->fromObject($dbVendorCatalogItem, $this->getResponseProfile());
		return $vendorCatalogItem;
	}
	
	/**
	 * Retrieve specific catalog item by id
	 *
	 * @action get
	 * @param int $id
	 * @return KalturaVendorCatalogItem
	 * @throws KalturaReachErrors::CATALOG_ITEM_NOT_FOUND
	 */
	function getAction($id)
	{
		$dbVendorCatalogItem = VendorCatalogItemPeer::retrieveByPK($id);
		if(!$dbVendorCatalogItem)
			throw new KalturaAPIException(KalturaReachErrors::CATALOG_ITEM_NOT_FOUND, $id);
		
		$vendorCatalogItem = KalturaVendorCatalogItem::getInstance($dbVendorCatalogItem, $this->getResponseProfile());
		$vendorCatalogItem->fromObject($dbVendorCatalogItem, $this->getResponseProfile());
		return $vendorCatalogItem;
	}
	
	/**
	 * List KalturaVendorCatalogItem objects
	 *
	 * @action list
	 * @param KalturaVendorCatalogItemFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaVendorCatalogItemListResponse
	 */
	public function listAction(KalturaVendorCatalogItemFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaVendorCatalogItemFilter();
		
		if(!$pager)
			$pager = new KalturaFilterPager();
		
		return $filter->getTypeListResponse($pager, $this->getResponseProfile());
	}
	
	/**
	 * Update an existing vedor catalog item object
	 *
	 * @action update
	 * @param int $id
	 * @param KalturaVendorCatalogItem $vendorCatalogItem
	 * @return KalturaVendorCatalogItem
	 *
	 * @throws KalturaReachErrors::CATALOG_ITEM_NOT_FOUND
	 */
	public function updateAction($id, KalturaVendorCatalogItem $vendorCatalogItem)
	{
		// get the object
		$dbVendorCatalogItem = VendorCatalogItemPeer::retrieveByPK($id);
		if(!$dbVendorCatalogItem)
			throw new KalturaAPIException(KalturaReachErrors::CATALOG_ITEM_NOT_FOUND, $id);
		
		// save the object
		$dbVendorCatalogItem = $vendorCatalogItem->toUpdatableObject($dbVendorCatalogItem);
		$dbVendorCatalogItem->save();
		
		// return the saved object
		$vendorCatalogItem = KalturaVendorCatalogItem::getInstance($dbVendorCatalogItem, $this->getResponseProfile());
		$vendorCatalogItem->fromObject($dbVendorCatalogItem, $this->getResponseProfile());
		return $vendorCatalogItem;
	}
	
	/**
	 * Delete vedor catalog item object
	 *
	 * @action delete
	 * @param int $id
	 *
	 * @throws KalturaReachErrors::CATALOG_ITEM_NOT_FOUND
	 */
	public function deleteAction($id)
	{
		// get the object
		$dbVendorCatalogItem = VendorCatalogItemPeer::retrieveByPK($id);
		if (!$dbVendorCatalogItem)
			throw new KalturaAPIException(KalturaReachErrors::CATALOG_ITEM_NOT_FOUND, $id);

		$dbVendorCatalogItem->delete();
	}
	
	/**
	 * Action assigns vendor catalog item to specific partner
	 * @action assignToPartner
	 *
	 * @param int $vendorCatalogItemId
	 * @throws KalturaReachErrors::CATALOG_ITEM_NOT_FOUND
	 */
	
	public function assignToPartnerAction ($vendorCatalogItemId) 
	{
		$dbVendorCatalogItem = VendorCatalogItemPeer::retrieveByPK($vendorCatalogItemId);
		if(!$dbVendorCatalogItem)
			throw new KalturaAPIException(KalturaReachErrors::CATALOG_ITEM_NOT_FOUND, $vendorCatalogItemId);
		
		/* @var $dbPartnerCatalogItem PartnerCatalogItem */
		$dbPartnerCatalogItem = new PartnerCatalogItem();
		$dbPartnerCatalogItem->setVendorCatalogItemId($vendorCatalogItemId);
		$dbPartnerCatalogItem->setPartnerId(kCurrentContext::$partner_id);
		$dbPartnerCatalogItem->setStatus(PartnerCatalogItemStatus::ACTIVE);
		$dbPartnerCatalogItem->save();
		
		// return the saved object
		$partnerCatalogItem = new KalturaPartnerCatalogItem();
		$partnerCatalogItem->fromObject($dbPartnerCatalogItem, $this->getResponseProfile());
		return $partnerCatalogItem;
	}
	
	/**
	 * Action removes vendor catalog item from partner
	 * @action remobveFromPartner
	 *
	 * @param int $id
	 * @throws KalturaReachErrors::PARTNER_CATALOG_ITEM_NOT_FOUND
	 */
	public function removeFromPartnerAction ($id)
	{
		$dbPartnerCatalogItem = PartnerCatalogItemPeer::retrieveByPK($id);
		if(!$dbPartnerCatalogItem)
			throw new KalturaAPIException(KalturaReachErrors::PARTNER_CATALOG_ITEM_NOT_FOUND, $id);
		
		$dbPartnerCatalogItem->setStatus(PartnerCatalogItemStatus::DISABLED);
		$dbPartnerCatalogItem->save();
		
		// return the saved object
		$partnerCatalogItem = new KalturaPartnerCatalogItem();
		$partnerCatalogItem->fromObject($dbPartnerCatalogItem, $this->getResponseProfile());
		return $partnerCatalogItem;
	}
	
	/**
	 * @action listByPartner
	 * @param KalturaPartnerFilter $filter
	 * @param KalturaFilterPager $pager
	 * 
	 * @return KalturaPartnerCatalogItemListResponse
	 */
	
	public function listByPartnerAction(KalturaPartnerFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$c = new Criteria();
		
		if (!is_null($filter))
		{
			$partnerFilter = new partnerFilter();
			$filter->toObject($partnerFilter);
			$partnerFilter->set('_gt_id', -1);
			
			$partnerCriteria = new Criteria();
			$partnerFilter->attachToCriteria($partnerCriteria);
			$partnerCriteria->setLimit(1000);
			$partnerCriteria->clearSelectColumns();
			$partnerCriteria->addSelectColumn(PartnerPeer::ID);
			$stmt = PartnerPeer::doSelectStmt($partnerCriteria);
			
			if($stmt->rowCount() < 1000) // otherwise, it's probably all partners
			{
				$partnerIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
				$c->add(PartnerCatalogItemPeer::PARTNER_ID, $partnerIds, Criteria::IN);
			}
		}
		
		if (is_null($pager))
			$pager = new KalturaFilterPager();
		
		$c->addDescendingOrderByColumn(PartnerCatalogItemPeer::CREATED_AT);
		
		$totalCount = PartnerCatalogItemPeer::doCount($c);
		$pager->attachToCriteria($c);
		$list = PartnerCatalogItemPeer::doSelect($c);
		$newList = KalturaPartnerCatalogItemArray::fromDbArray($list, $this->getResponseProfile());
		
		$response = new KalturaPartnerCatalogItemListResponse();
		$response->totalCount = $totalCount;
		$response->objects = $newList;
		return $response;
	}
}
