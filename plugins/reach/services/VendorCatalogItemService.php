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
		
		$this->applyPartnerFilterForClass('PartnerCatalogItem');
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
		$dbVendorCatalogItem->setStatus(KalturaVendorCatalogItemStatus::ACTIVE);
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
	public function getAction($id)
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
	 * Update vendor catalog item status by id
	 *
	 * @action updateStatus
	 * @param int $id
	 * @param KalturaVendorCatalogItemStatus $status
	 * @return KalturaVendorCatalogItem
	 *
	 * @throws KalturaReachErrors::CATALOG_ITEM_NOT_FOUND
	 * @throws KalturaReachErrors::VENDOR_CATALOG_ITEM_DUPLICATE_SYSTEM_NAME
	 */
	public function updateStatusAction($id, $status)
	{
		// get the object
		$dbVendorCatalogItem = VendorCatalogItemPeer::retrieveByPK($id);
		if (!$dbVendorCatalogItem)
			throw new KalturaAPIException(KalturaReachErrors::CATALOG_ITEM_NOT_FOUND, $id);
		
		if($status == KalturaVendorCatalogItemStatus::ACTIVE)
		{
			//Check uniqueness of new object's system name
			$systemNameTemplates = VendorCatalogItemPeer::retrieveBySystemName($dbVendorCatalogItem->getSystemName(), $id);
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
		
		// Check if partnerCatalogItem exists, in this case you should not be able to delete the vendorCatalogItem prior to deleting the partner assignment first 
		$partnerCatalogItem = PartnerCatalogItemPeer::retrieveByCatalogItemId($id);
		if($partnerCatalogItem)
			throw new KalturaAPIException(KalturaReachErrors::CATALOG_ITEM_CANNOT_BE_DELETED, $id);
		
		// set the object status to deleted
		$dbVendorCatalogItem->setStatus(KalturaVendorCatalogItemStatus::DELETED);
		$dbVendorCatalogItem->save();
	}

	/**
	 * @action serve
	 * @param int $vendorPartnerId
	 * @return file
	 */
	public function serveAction($vendorPartnerId = null)
	{
		$filter = new KalturaVendorCatalogItemFilter();
		if($vendorPartnerId)
		{
			$filter->vendorPartnerIdEqual = $vendorPartnerId;
		}

		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		$pager->pageIndex = 1;

		$content = implode(',', kReachUtils::getVendorCatalogItemsCsvHeaders()) . PHP_EOL;
		$res =  $filter->getTypeListResponse($pager, $this->getResponseProfile());
		$totalCount = $res->totalCount;
		while ($totalCount > 0)
		{
			foreach ($res->objects as $vendorCatalogItem)
			{
				$catalogItemValues = kReachUtils::getObejctValues($vendorCatalogItem);
				$csvRowData = kReachUtils::createCsvRowData($catalogItemValues, 'vendorCatalogItem');
				$content .= $csvRowData . PHP_EOL;
			}

			$pager->pageIndex++;
			$totalCount = $totalCount - $pager->pageSize;
			$res = $filter->getTypeListResponse($pager, $this->getResponseProfile());
		}
		$fileName = "export.csv";
		header('Content-Disposition: attachment; filename="'.$fileName.'"');
		return new kRendererString($content, 'text/csv');
	}

	/**
	 * @action getServeUrl
	 * @param int $vendorPartnerId
	 * @return string $url
	 */
	public function getServeUrlAction($vendorPartnerId = null)
	{
		$finalPath = '/api_v3/service/reach_vendorcatalogitem/action/serve/';
		if ($vendorPartnerId)
		{
			$finalPath .= "vendorPartnerId/$vendorPartnerId";
		}
		$finalPath .= '/ks/' . kCurrentContext::$ks;
		$url = myPartnerUtils::getCdnHost($this->getPartnerId()) . $finalPath;
		return $url;
	}


	/**
	 * @action addFromBulkUpload
	 * Action adds vendor catalog items from a bulkupload CSV file
	 * @param file $fileData
	 * @param KalturaBulkUploadJobData $bulkUploadData
	 * @param KalturaBulkUploadVendorCatalogItemData $bulkUploadVendorCatalogItemData
	 * @return KalturaBulkUpload
	 */
	public function addFromBulkUploadAction ($fileData, KalturaBulkUploadJobData $bulkUploadData = null, KalturaBulkUploadVendorCatalogItemData $bulkUploadVendorCatalogItemData = null)
	{
		if (!$bulkUploadData)
		{
			$bulkUploadData = KalturaPluginManager::loadObject('KalturaBulkUploadJobData', null);
		}

		if (!$bulkUploadVendorCatalogItemData)
		{
			$bulkUploadVendorCatalogItemData = new KalturaBulkUploadVendorCatalogItemData();
		}

		if(!$bulkUploadData->fileName)
			$bulkUploadData->fileName = $fileData['name'];

		$dbBulkUploadJobData = $bulkUploadData->toInsertableObject();
		$bulkUploadCoreType = kPluginableEnumsManager::apiToCore('BulkUploadType', $bulkUploadData->type);

		$dbBulkUploadJobData->setBulkUploadObjectType(BulkUploadObjectType::VENDOR_CATALOG_ITEM);
		$dbBulkUploadJobData->setUserId($this->getKuser()->getPuserId());
		$dbObjectData = $bulkUploadVendorCatalogItemData->toInsertableObject();
		$dbBulkUploadJobData->setObjectData($dbObjectData);
		$dbBulkUploadJobData->setFilePath($fileData['tmp_name']);

		$dbJob = kJobsManager::addBulkUploadJob($this->getPartner(), $dbBulkUploadJobData, $bulkUploadCoreType);
		$dbJobLog = BatchJobLogPeer::retrieveByBatchJobId($dbJob->getId());
		if(!$dbJobLog)
		{
			return null;
		}

		$bulkUpload = new KalturaBulkUpload();
		$bulkUpload->fromObject($dbJobLog, $this->getResponseProfile());

		return $bulkUpload;
	}


}