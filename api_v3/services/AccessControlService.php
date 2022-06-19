<?php

/**
 * Add & Manage Access Controls
 *
 * @service accessControl
 * @deprecated use accessControlProfile service instead
 */
class AccessControlService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		$this->applyPartnerFilterForClass('accessControl'); 	
	}
	
	/**
	 * Add new Access Control Profile
	 * 
	 * @action add
	 * @param KalturaAccessControl $accessControl
	 * @return KalturaAccessControl
	 */
	function addAction(KalturaAccessControl $accessControl)
	{
		$accessControl->validatePropertyMinLength("name", 1);
		$accessControl->partnerId = $this->getPartnerId();
		
		$dbAccessControl = new accessControl();
		$accessControl->toObject($dbAccessControl);
		$dbAccessControl->save();
		
		$accessControl = new KalturaAccessControl();
		$accessControl->fromObject($dbAccessControl, $this->getResponseProfile());
		return $accessControl;
	}
	
	/**
	 * Get Access Control Profile by id
	 * 
	 * @action get
	 * @param bigint $id
	 * @return KalturaAccessControl
	 */
	function getAction($id)
	{
		$dbAccessControl = accessControlPeer::retrieveByPK($id);
		if (!$dbAccessControl)
			throw new KalturaAPIException(KalturaErrors::ACCESS_CONTROL_ID_NOT_FOUND, $id);
			
		$accessControl = new KalturaAccessControl();
		$accessControl->fromObject($dbAccessControl, $this->getResponseProfile());
		return $accessControl;
	}
	
	/**
	 * Update Access Control Profile by id
	 * 
	 * @action update
	 * @param bigint $id
	 * @param KalturaAccessControl $accessControl
	 * @return KalturaAccessControl
	 * 
	 * @throws KalturaErrors::ACCESS_CONTROL_ID_NOT_FOUND
	 * @throws KalturaErrors::ACCESS_CONTROL_NEW_VERSION_UPDATE
	 */
	function updateAction($id, KalturaAccessControl $accessControl)
	{
		$dbAccessControl = accessControlPeer::retrieveByPK($id);
		if (!$dbAccessControl)
			throw new KalturaAPIException(KalturaErrors::ACCESS_CONTROL_ID_NOT_FOUND, $id);
	
		$rules = $dbAccessControl->getRulesArray();
		foreach($rules as $rule)
		{
			if(!($rule instanceof kAccessControlRestriction))
				throw new KalturaAPIException(KalturaErrors::ACCESS_CONTROL_NEW_VERSION_UPDATE, $id);
		}
		
		$accessControl->validatePropertyMinLength("name", 1, true);
			
		$accessControl->toUpdatableObject($dbAccessControl);
		$dbAccessControl->save();
		
		$accessControl = new KalturaAccessControl();
		$accessControl->fromObject($dbAccessControl, $this->getResponseProfile());
		return $accessControl;
	}
	
	/**
	 * Delete Access Control Profile by id
	 * 
	 * @action delete
	 * @param bigint $id
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
	 * List Access Control Profiles by filter and pager
	 * 
	 * @action list
	 * @param KalturaFilterPager $filter
	 * @param KalturaAccessControlFilter $pager
	 * @return KalturaAccessControlListResponse
	 */
	function listAction(KalturaAccessControlFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaAccessControlFilter();
			
		if(!$pager)
			$pager = new KalturaFilterPager();
			
		return $filter->getListResponse($pager, $this->getResponseProfile());  
	}
}
