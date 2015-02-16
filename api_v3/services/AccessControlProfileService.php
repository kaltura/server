<?php

/**
 * Manage access control profiles
 *
 * @service accessControlProfile
 */
class AccessControlProfileService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		$this->applyPartnerFilterForClass('accessControl'); 	
	}
	
	/**
	 * Add new access control profile
	 * 
	 * @action add
	 * @param KalturaAccessControlProfile $accessControlProfile
	 * @return KalturaAccessControlProfile
	 */
	function addAction(KalturaAccessControlProfile $accessControlProfile)
	{
		$dbAccessControl = $accessControlProfile->toInsertableObject();
		$dbAccessControl->setPartnerId($this->getPartnerId());
		$dbAccessControl->save();
		
		$accessControlProfile = new KalturaAccessControlProfile();
		$accessControlProfile->fromObject($dbAccessControl, $this->getResponseProfile());
		return $accessControlProfile;
	}
	
	/**
	 * Get access control profile by id
	 * 
	 * @action get
	 * @param int $id
	 * @return KalturaAccessControlProfile
	 * 
	 * @throws KalturaErrors::ACCESS_CONTROL_ID_NOT_FOUND
	 */
	function getAction($id)
	{
		$dbAccessControl = accessControlPeer::retrieveByPK($id);
		if (!$dbAccessControl)
			throw new KalturaAPIException(KalturaErrors::ACCESS_CONTROL_ID_NOT_FOUND, $id);
			
		$accessControlProfile = new KalturaAccessControlProfile();
		$accessControlProfile->fromObject($dbAccessControl, $this->getResponseProfile());
		return $accessControlProfile;
	}
	
	/**
	 * Update access control profile by id
	 * 
	 * @action update
	 * @param int $id
	 * @param KalturaAccessControlProfile $accessControlProfile
	 * @return KalturaAccessControlProfile
	 * 
	 * @throws KalturaErrors::ACCESS_CONTROL_ID_NOT_FOUND
	 */
	function updateAction($id, KalturaAccessControlProfile $accessControlProfile)
	{
		$dbAccessControl = accessControlPeer::retrieveByPK($id);
		if (!$dbAccessControl)
			throw new KalturaAPIException(KalturaErrors::ACCESS_CONTROL_ID_NOT_FOUND, $id);
		
		$accessControlProfile->toUpdatableObject($dbAccessControl);
		$dbAccessControl->save();
		
		$accessControlProfile = new KalturaAccessControlProfile();
		$accessControlProfile->fromObject($dbAccessControl, $this->getResponseProfile());
		return $accessControlProfile;
	}
	
	/**
	 * Delete access control profile by id
	 * 
	 * @action delete
	 * @param int $id
	 * 
	 * @throws KalturaErrors::ACCESS_CONTROL_ID_NOT_FOUND
	 * @throws KalturaErrors::CANNOT_DELETE_DEFAULT_ACCESS_CONTROL
	 */
	function deleteAction($id)
	{
		$dbAccessControl = accessControlPeer::retrieveByPK($id);
		if (!$dbAccessControl)
			throw new KalturaAPIException(KalturaErrors::ACCESS_CONTROL_ID_NOT_FOUND, $id);

		if ($dbAccessControl->getIsDefault())
			throw new KalturaAPIException(KalturaErrors::CANNOT_DELETE_DEFAULT_ACCESS_CONTROL);
			
		$c = new Criteria();
		$c->add(entryPeer::ACCESS_CONTROL_ID, $dbAccessControl->getId());
		
		// move entries to the default access control
		$entryCount = entryPeer::doCount($c);
		if ($entryCount > 0)
			entryPeer::updateAccessControl($this->getPartnerId(), $id, $this->getPartner()->getDefaultAccessControlId());
			
		$dbAccessControl->setDeletedAt(time());
		$dbAccessControl->save();
	}
	
	/**
	 * List access control profiles by filter and pager
	 * 
	 * @action list
	 * @param KalturaFilterPager $filter
	 * @param KalturaAccessControlProfileFilter $pager
	 * @return KalturaAccessControlProfileListResponse
	 */
	function listAction(KalturaAccessControlProfileFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaAccessControlProfileFilter();
			
		if(!$pager)
			$pager = new KalturaFilterPager();
			
		return $filter->getListResponse($pager, $this->getResponseProfile());
	}
}