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
		
		if(!ReachPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, ReachPlugin::PLUGIN_NAME);
		
		if(!in_array($this->actionName, array('listTemplates', 'clone')))
			$this->applyPartnerFilterForClass('vendorCatalogItem');
	}
	
	/* (non-PHPdoc)
 	 * @see KalturaBaseService::partnerGroup()
 	 */
	protected function partnerGroup($peer = null)
	{
		switch ($this->actionName)
		{
			case 'clone':
				return '0';
			case 'listTemplates':
				return '0';
			case 'list':
				return $this->partnerGroup . ',0';
		}
		
		return $this->partnerGroup;
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
		$dbVendorCatalogItem->setStatus(KalturaVendorCatalogItemStatus::DISABLED);
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
	 * Update vedor catalog item status by id
	 *
	 * @action updateStatus
	 * @param int $id
	 * @param KalturaVendorCatalogItemStatus $status
	 * @return KalturaVendorCatalogItem
	 *
	 * @throws KalturaReachErrors::CATALOG_ITEM_NOT_FOUND
	 * @throws KalturaReachErrors::VENDOR_CATALOG_ITEM_DUPLICATE_SYSTEM_NAME
	 */
	function updateStatusAction($id, $status)
	{
		// get the object
		$dbVendorCatalogItem = VendorCatalogItemPeer::retrieveByPK($id);
		if (!$dbVendorCatalogItem)
			throw new KalturaAPIException(KalturaReachErrors::CATALOG_ITEM_NOT_FOUND, $id);
		
		if($status == KalturaVendorCatalogItemStatus::ACTIVE)
		{
			//Check uniqueness of new object's system name
			$systemNameTemplates = VendorCatalogItemPeer::retrieveBySystemName($dbVendorCatalogItem->getSystemName());
			if (count($systemNameTemplates))
				throw new KalturaAPIException(KalturaReachErrors::VENDOR_CATALOG_ITEM_DUPLICATE_SYSTEM_NAME, $dbVendorCatalogItem->getSystemName());
		}
		
		// save the object
		$dbVendorCatalogItem->setStatus($status);
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
		
		// set the object status to deleted
		$dbVendorCatalogItem->setStatus(KalturaVendorCatalogItemStatus::DELETED);
		$dbVendorCatalogItem->save();
	}
	
	/**
	 * This action assigning various bvendor cataog items to a specifc account.
	 *
	 * @action clone
	 * @param int $id source catalog item to clone
	 * @throws KalturaReachErrors::CATALOG_ITEM_NOT_FOUND
	 * @throws KalturaReachErrors::VENDOR_CATALOG_ITEM_DUPLICATE_SYSTEM_NAME
	 * 
	 * @return KalturaVendorCatalogItem
	 */
	public function cloneAction($id)
	{
		// get the object
		$dbVendorCatalogItem = VendorCatalogItemPeer::retrieveByPK($id);
		if (!$dbVendorCatalogItem)
			throw new KalturaAPIException(KalturaReachErrors::CATALOG_ITEM_NOT_FOUND, $id);
		
		//Check uniqueness of new object's system name
		$systemNameTemplates = VendorCatalogItemPeer::retrieveBySystemName($dbVendorCatalogItem->getSystemName());
		if (count($systemNameTemplates))
			throw new KalturaAPIException(KalturaReachErrors::VENDOR_CATALOG_ITEM_DUPLICATE_SYSTEM_NAME, $dbVendorCatalogItem->getSystemName());
		
		
		// copy into new db object
		$newDbVendorCatalogItem = $dbVendorCatalogItem->copy();
		$newDbVendorCatalogItem->setPartnerId($this->impersonatedPartnerId);
		$newDbVendorCatalogItem->save();
		
		
		// return the saved object
		$vendorCatalogItem = KalturaVendorCatalogItem::getInstance($newDbVendorCatalogItem, $this->getResponseProfile());
		$vendorCatalogItem->fromObject($newDbVendorCatalogItem, $this->getResponseProfile());
		return $vendorCatalogItem;
	}
	
	/**
	 * Action lists the basic vendor catalog items.
	 * @action listTemplates
	 *
	 * @param KalturaVendorCatalogItemFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaVendorCatalogItemListResponse
	 */
	public function listTemplatesAction (KalturaVendorCatalogItemFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaVendorCatalogItemFilter();
		
		if (!$pager)
			$pager = new KalturaFilterPager();
		
		return $filter->getTypeListTemplatesResponse($pager, $this->getResponseProfile());
	}
}