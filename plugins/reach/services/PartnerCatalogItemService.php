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

		if (!ReachPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, ReachPlugin::PLUGIN_NAME);

		$this->applyPartnerFilterForClass('PartnerCatalogItem');
	}

	/**
	 * Assign existing catalogItem to specific account
	 *
	 * @action add
	 * @param int $id source catalog item to assign to partner
	 * @param int $defaultReachProfileId
	 * @throws KalturaReachErrors::CATALOG_ITEM_NOT_FOUND
	 * @throws KalturaReachErrors::VENDOR_CATALOG_ITEM_ALREADY_ENABLED_ON_PARTNER
	 * @throws KalturaReachErrors::REACH_PROFILE_NOT_FOUND
	 *
	 * @return KalturaVendorCatalogItem
	 */
	public function addAction($id, $defaultReachProfileId = null)
	{
		// get the object
		$dbVendorCatalogItem = VendorCatalogItemPeer::retrieveByPK($id);
		if (!$dbVendorCatalogItem)
		{
			throw new KalturaAPIException(KalturaReachErrors::CATALOG_ITEM_NOT_FOUND, $id);
		}

		//Check if catalog item already enabled on partner
		$dbPartnerCatalogItem = PartnerCatalogItemPeer::retrieveByCatalogItemId($id, kCurrentContext::getCurrentPartnerId());
		if ($dbPartnerCatalogItem)
		{
			throw new KalturaAPIException(KalturaReachErrors::VENDOR_CATALOG_ITEM_ALREADY_ENABLED_ON_PARTNER, $id, kCurrentContext::getCurrentPartnerId());
		}

		//Check if catalog item exists but deleted to re-use it
		$partnerCatalogItem = PartnerCatalogItemPeer::retrieveByCatalogItemIdNoFilter($id, kCurrentContext::getCurrentPartnerId());
		if (!$partnerCatalogItem)
		{
			$partnerCatalogItem = new PartnerCatalogItem();
			$partnerCatalogItem->setPartnerId($this->getPartnerId());
			$partnerCatalogItem->setCatalogItemId($id);
		}

		if($defaultReachProfileId)
		{
			$dbReachProfile = ReachProfilePeer::retrieveActiveByPk($defaultReachProfileId, kCurrentContext::getCurrentPartnerId());
			if (!$dbReachProfile)
			{
				throw new KalturaAPIException(KalturaReachErrors::REACH_PROFILE_NOT_FOUND, $defaultReachProfileId);
			}
			$partnerCatalogItem->setDefaultReachProfileId($defaultReachProfileId);
		}

		$partnerCatalogItem->setStatus(KalturaVendorCatalogItemStatus::ACTIVE);
		$partnerCatalogItem->save();

		// return the catalog item
		$vendorCatalogItem = KalturaVendorCatalogItem::getInstance($dbVendorCatalogItem, $this->getResponseProfile());
		$vendorCatalogItem->fromObject($dbVendorCatalogItem, $this->getResponseProfile());
		$vendorCatalogItem->defaultReachProfileId = $partnerCatalogItem->getDefaultReachProfileId();
		return $vendorCatalogItem;
	}

	/**
	 * Remove existing catalogItem from specific account
	 *
	 * @action delete
	 * @param int $id source catalog item to remove
	 * @throws KalturaReachErrors::CATALOG_ITEM_NOT_FOUND
	 * @throws KalturaReachErrors::VENDOR_CATALOG_ITEM_ALREADY_ENABLED_ON_PARTNER
	 */
	public function deleteAction($id)
	{
		$dbVendorCatalogItem = VendorCatalogItemPeer::retrieveByPK($id);
		if (!$dbVendorCatalogItem)
			throw new KalturaAPIException(KalturaReachErrors::CATALOG_ITEM_NOT_FOUND, $id);

		//Check if catalog item already enabled
		$dbPartnerCatalogItem = PartnerCatalogItemPeer::retrieveByCatalogItemId($id, kCurrentContext::getCurrentPartnerId());
		if (!$dbPartnerCatalogItem)
			throw new KalturaAPIException(KalturaReachErrors::PARTNER_CATALOG_ITEM_NOT_FOUND, $id);

		$dbPartnerCatalogItem->setStatus(VendorCatalogItemStatus::DELETED);
		$dbPartnerCatalogItem->save();
	}
}
