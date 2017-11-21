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
		
		//if(!ReachPlugin::isAllowedPartner($this->getPartnerId()))
		//	throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, ReachPlugin::PLUGIN_NAME);
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
		$dbVendorCatalogItem->setPartnerId($this->getPartnerId());
		$dbVendorCatalogItem->setStatus(KalturaVendorCatalogItemStatus::ACTIVE);
		$dbVendorCatalogItem->save();
		
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
		
		$vendorCatalogItem = new KalturaVendorCatalogItem();
		$vendorCatalogItem->fromObject($dbVendorCatalogItem, $this->getResponseProfile());
		return $vendorCatalogItem;
	}
}
