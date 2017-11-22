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
		$dbVendorCatalogItem->setStatus(KalturaVendorCatalogItemStatus::ACTIVE);
		$dbVendorCatalogItem->save();
		
		// return the saved object
		$vendorCatalogItem = KalturaVendorCatalogItem::getInstance($dbVendorCatalogItem, $this->getResponseProfile());
		$vendorCatalogItem->fromObject($dbVendorCatalogItem, $this->getResponseProfile());
		return $vendorCatalogItem;
	}
	
	/**
	 * Retrieve specifc catalog item by id
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
	 * List KalturaVendroCatalogItem objects
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
}
