<?php
/**
 * Vendor Profile Service
 *
 * @service vendorProfile
 * @package plugins.reach
 * @subpackage api.services
 * @throws KalturaErrors::SERVICE_FORBIDDEN
 */

class VendorProfileService extends KalturaBaseService
{

	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		if (!ReachPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, ReachPlugin::PLUGIN_NAME);

		$this->applyPartnerFilterForClass('vendorProfile');
	}

	/**
	 * Allows you to add a partner specific vendor profile
	 *
	 * @action add
	 * @param KalturaVendorProfile $vendorProfile
	 * @return KalturaVendorProfile
	 */
	public function addAction(KalturaVendorProfile $vendorProfile)
	{
		$dbVendorProfile = $vendorProfile->toInsertableObject();

		/* @var $dbVendorProfile VendorProfile */
		$dbVendorProfile->setPartnerId(kCurrentContext::getCurrentPartnerId());
		$dbVendorProfile->setStatus(KalturaVendorProfileStatus::ACTIVE);
		$dbVendorProfile->save();

		// return the saved object
		$vendorProfile->fromObject($dbVendorProfile, $this->getResponseProfile());
		return $vendorProfile;
	}

	/**
	 * Retrieve specific vendor profile by id
	 *
	 * @action get
	 * @param int $id
	 * @return KalturaVendorProfile
	 * @throws KalturaReachErrors::VENDOR_PROFILE_NOT_FOUND
	 */
	function getAction($id)
	{
		$dbVendorProfile = VendorProfilePeer::retrieveByPK($id);
		if (!$dbVendorProfile)
			throw new KalturaAPIException(KalturaReachErrors::VENDOR_PROFILE_NOT_FOUND, $id);

		$vendorProfile = new KalturaVendorProfile();
		$vendorProfile->fromObject($dbVendorProfile, $this->getResponseProfile());
		return $vendorProfile;
	}

	/**
	 * List KalturaVendorProfile objects
	 *
	 * @action list
	 * @param KalturaVendorProfileFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaVendorProfileListResponse
	 */
	public function listAction(KalturaVendorProfileFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaVendorProfileFilter();

		if (!$pager)
			$pager = new KalturaFilterPager();

		return $filter->getListResponse($pager, $this->getResponseProfile());
	}

	/**
	 * Update an existing vendor profile object
	 *
	 * @action update
	 * @param int $id
	 * @param KalturaVendorProfile $vendorProfile
	 * @return KalturaVendorProfile
	 *
	 * @throws KalturaReachErrors::VENDOR_PROFILE_NOT_FOUND
	 */
	public function updateAction($id, KalturaVendorProfile $vendorProfile)
	{
		// get the object
		$dbVendorProfile = VendorProfilePeer::retrieveByPK($id);
		if (!$dbVendorProfile)
			throw new KalturaAPIException(KalturaReachErrors::CATALOG_ITEM_NOT_FOUND, $id);

		// save the object
		$dbVendorProfile = $vendorProfile->toUpdatableObject($dbVendorProfile);
		$dbVendorProfile->save();

		// return the saved object
		$vendorProfile = new KalturaVendorProfile();
		$vendorProfile->fromObject($dbVendorProfile, $this->getResponseProfile());
		return $vendorProfile;
	}

	/**
	 * Update vendor profile status by id
	 *
	 * @action updateStatus
	 * @param int $id
	 * @param KalturaVendorProfileStatus $status
	 * @return KalturaVendorProfile
	 *
	 * @throws KalturaReachErrors::VENDOR_PROFILE_NOT_FOUND
	 */
	function updateStatusAction($id, $status)
	{
		// get the object
		$dbVendorProfile = VendorProfilePeer::retrieveByPK($id);
		if (!$dbVendorProfile)
			throw new KalturaAPIException(KalturaReachErrors::CATALOG_ITEM_NOT_FOUND, $id);

		// save the object
		$dbVendorProfile->setStatus($status);
		$dbVendorProfile->save();

		// return the saved object
		// return the saved object
		$vendorProfile = new KalturaVendorProfile();
		$vendorProfile->fromObject($dbVendorProfile, $this->getResponseProfile());
		return $vendorProfile;
	}

	/**
	 * Delete vednor profile by id
	 *
	 * @action delete
	 * @param int $id
	 *
	 * @throws KalturaReachErrors::VENDOR_PROFILE_NOT_FOUND
	 */
	public function deleteAction($id)
	{
		// get the object
		$dbVendorProfile = VendorProfilePeer::retrieveByPK($id);
		if (!$dbVendorProfile)
			throw new KalturaAPIException(KalturaReachErrors::VENDOR_PROFILE_NOT_FOUND, $id);

		// set the object status to deleted
		$dbVendorProfile->setStatus(KalturaVendorProfileStatus::DELETED);
		$dbVendorProfile->save();
	}

	/**
	 * sync vednor profile credit
	 *
	 * @action syncCredit
	 * @param int $vendorProfileId
	 *
	 * @throws KalturaReachErrors::VENDOR_PROFILE_NOT_FOUND
	 */
	public function syncCredit($vendorProfileId)
	{
		$dbVendorProfile = VendorProfilePeer::retrieveByPK($vendorProfileId);
		if (!$dbVendorProfile)
			throw new KalturaAPIException(KalturaReachErrors::VENDOR_PROFILE_NOT_FOUND, $vendorProfileId);

		// set the object status to deleted
		$dbVendorProfile->syncCredit();
		$dbVendorProfile->save();

		// return the saved object
		$vendorProfile = new KalturaVendorProfile();
		$vendorProfile->fromObject($dbVendorProfile, $this->getResponseProfile());
		return $vendorProfile;
	}

}