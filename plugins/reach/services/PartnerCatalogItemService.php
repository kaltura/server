<?php
/**
 * Partner Catalog Item Service
 *
 * @service PartnerCatalogItem
 * @package plugins.reach
 * @subpackage api.services
 * @throws KalturaErrors::SERVICE_FORBIDDEN
 */

class PartnerCatalogItemService extends KalturaBaseService
{
	
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		if(!ReachPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, ReachPlugin::PLUGIN_NAME);
		
//		$this->applyPartnerFilterForClass('PartnerCatalogItem');
	}
	
	/**
	 * Assign existing catalogItem to specific account
	 *
	 * @action add
	 * @param int $id source catalog item to assign to partner
	 * @throws KalturaReachErrors::CATALOG_ITEM_NOT_FOUND
	 * @throws KalturaReachErrors::VENDOR_CATALOG_ITEM_ALREADY_ENABLED_ON_PARTNER
	 *
	 * @return KalturaVendorCatalogItem
	 */
	public function addAction($id)
	{
		// get the object
		$dbVendorCatalogItem = VendorCatalogItemPeer::retrieveByPK($id);
		if (!$dbVendorCatalogItem)
			throw new KalturaAPIException(KalturaReachErrors::CATALOG_ITEM_NOT_FOUND, $id);
		
		//Check if catalog item already enabled
		$dbPartnerCatalogItem = PartnerCatalogItemPeer::retrieveByCatalogItemId($id, kCurrentContext::$ks_partner_id) ;
		if ($dbPartnerCatalogItem)
			throw new KalturaAPIException(KalturaReachErrors::VENDOR_CATALOG_ITEM_ALREADY_ENABLED_ON_PARTNER, $id, $this->getPartnerId());
		
		$partnerCatalogItem = new PartnerCatalogItem();
		$partnerCatalogItem->setPartnerId($this->getPartnerId());
		$partnerCatalogItem->setStatus(KalturaVendorCatalogItemStatus::ACTIVE);
		$partnerCatalogItem->setCatalogItemId($id);
		$partnerCatalogItem->save();
		
		// return the cloned catalog item
		$vendorCatalogItem = KalturaVendorCatalogItem::getInstance($dbVendorCatalogItem, $this->getResponseProfile());
		$vendorCatalogItem->fromObject($dbVendorCatalogItem, $this->getResponseProfile());
		return $vendorCatalogItem;
	}
	
	/**
	 * Remove existing catalogItem from specific account
	 *
	 * @action delete
	 * @param int $id source catalog item to remove
	 * @throws KalturaReachErrors::CATALOG_ITEM_NOT_FOUND
	 * @throws KalturaReachErrors::VENDOR_CATALOG_ITEM_ALREADY_ENABLED_ON_PARTNER
	 *
	 * @return KalturaVendorCatalogItem
	 */
	public function deleteAction($id)
	{
		$dbVendorCatalogItem = VendorCatalogItemPeer::retrieveByPK($id);
		if(!$dbVendorCatalogItem)
			throw new KalturaAPIException(KalturaReachErrors::CATALOG_ITEM_NOT_FOUND, $id);
		
		//Check if catalog item already enabled
		$dbPartnerCatalogItem = PartnerCatalogItemPeer::retrieveByCatalogItemId($id, kCurrentContext::$ks_partner_id) ;
		if(!$dbPartnerCatalogItem)
			throw new KalturaAPIException(KalturaReachErrors::PARTNER_CATALOG_ITEM_NOT_FOUND, $id);
		
		$dbPartnerCatalogItem->setStatus(VendorCatalogItemStatus::DELETED);
		$dbPartnerCatalogItem->save();
	}
}