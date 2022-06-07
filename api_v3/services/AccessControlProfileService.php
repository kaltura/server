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
	 * @param bigint $id
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
	 * @param bigint $id
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
	 * @param bigint $id
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
			
		$dbAccessControl->setDeletedAt(time());
		try
		{
			$dbAccessControl->save();
		}
		catch(kCoreException $e)
		{
			$code = $e->getCode();
			switch($code)
			{
				case kCoreException::EXCEEDED_MAX_ENTRIES_PER_ACCESS_CONTROL_UPDATE_LIMIT :
					throw new KalturaAPIException(KalturaErrors::EXCEEDED_ENTRIES_PER_ACCESS_CONTROL_FOR_UPDATE, $id);
				case kCoreException::NO_DEFAULT_ACCESS_CONTROL :
					throw new KalturaAPIException(KalturaErrors::CANNOT_TRANSFER_ENTRIES_TO_ANOTHER_ACCESS_CONTROL_OBJECT);
				default:
					throw $e;
			}
		}
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
