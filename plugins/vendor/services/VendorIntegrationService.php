<?php

/**
 * @service vendor
 * @package plugins.vendor
 * @subpackage api.services
 */
class VendorIntegrationService extends KalturaBaseService
{

	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		$this->applyPartnerFilterForClass('VendorIntegrationPeer');
	}

	/**
	 * Retrieve integration setting object by ID
	 *
	 * @action get
	 * @param int $integrationId
	 * @return KalturaIntegrationSetting
	 * @throws APIErrors::INVALID_OBJECT_ID
	 */
	public function getAction($integrationId)
	{
		$vendorIntegration = VendorIntegrationPeer::retrieveByPK($integrationId);
		if($vendorIntegration)
		{
			$integrationSetting = KalturaIntegrationSetting::getInstance($vendorIntegration);
			$integrationSetting->fromObject($vendorIntegration);
			return $integrationSetting;
		}

		throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $integrationId);
	}

	/**
	 * Add new integration setting object
	 *
	 * @action add
	 * @param KalturaIntegrationSetting $integration
	 * @return KalturaIntegrationSetting
	 * @throws APIErrors::INVALID_OBJECT_ID
	 */
	public function addAction(KalturaIntegrationSetting $integration)
	{
		$dbObject = $integration->toInsertableObject();

		/* @var $dbObject VendorIntegration */
		$dbObject->setStatus(VendorIntegrationStatus::ACTIVE);
		$dbObject->save();

		// return the saved object
		$vendorIntegration = KalturaIntegrationSetting::getInstance($dbObject, $this->getResponseProfile());
		$vendorIntegration->fromObject($dbObject, $this->getResponseProfile());

		return $vendorIntegration;
	}

	/**
	 * Delete integration object by ID
	 *
	 * @action delete
	 * @param int $integrationId
	 * @return KalturaIntegrationSetting
	 * @throws APIErrors::INVALID_OBJECT_ID
	 */
	public function deleteAction($integrationId)
	{
		// get the object
		$dbVendorIntegration = VendorIntegrationPeer::retrieveByPK($integrationId);
		if (!$dbVendorIntegration)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $integrationId);

		// set the object status to deleted
		$dbVendorIntegration->setStatus(KalturaVendorCatalogItemStatus::DELETED);
		$dbVendorIntegration->save();
	}

	/**
	 * Update an existing vedor catalog item object
	 *
	 * @action update
	 * @param int $id
	 * @param KalturaIntegrationSetting $vendorCatalogItem
	 * @return KalturaIntegrationSetting
	 *
	 * @throws APIErrors::INVALID_OBJECT_ID
	 */
	public function updateAction($id, KalturaIntegrationSetting $integrationSetting)
	{
		// get the object
		$dbVendorIntegration = VendorIntegrationPeer::retrieveByPK($id);
		if(!$dbVendorIntegration)
			throw new KalturaAPIException(APIErrors::INVALID_OBJECT_ID, $id);

		// save the object
		$dbVendorIntegration = $integrationSetting->toUpdatableObject($dbVendorIntegration);
		$dbVendorIntegration->save();

		// return the saved object
		$vendorIntegration = KalturaIntegrationSetting::getInstance($dbVendorIntegration, $this->getResponseProfile());
		$vendorIntegration->fromObject($dbVendorIntegration, $this->getResponseProfile());

		return $vendorIntegration;
	}

	/**
	 * Update vendor catalog item status by id
	 *
	 * @action updateStatus
	 * @param int $id
	 * @param KalturaIntegrationSetting $status
	 * @return KalturaIntegrationSetting
	 *
	 * @throws APIErrors::INVALID_OBJECT_ID
	 */
	public function updateStatusAction($id, $status)
	{
		// get the object
		$dbVendorIntegration = VendorIntegrationPeer::retrieveByPK($id);
		if(!$dbVendorIntegration)
			throw new KalturaAPIException(APIErrors::INVALID_OBJECT_ID, $id);

		// save the object
		$dbVendorIntegration->setStatus($status);
		$dbVendorIntegration->save();

		// return the saved object
		$vendorIntegration = KalturaIntegrationSetting::getInstance($dbVendorIntegration, $this->getResponseProfile());
		$vendorIntegration->fromObject($dbVendorIntegration, $this->getResponseProfile());

		return $vendorIntegration;
	}
}