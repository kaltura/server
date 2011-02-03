<?php

/**
 * Add & Manage Access Controls
 *
 * @service accessControl
 */
class AccessControlService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		parent::applyPartnerFilterForClass(new accessControlPeer()); 	
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
		
		try
		{
			$dbAccessControl = new accessControl();
			$accessControl->toObject($dbAccessControl);
			$dbAccessControl->save();
		}
		catch(Exception $ex)
		{
			if ($ex instanceof kCoreException)
				$this->handleCoreException($ex, $dbAccessControl, $accessControl);
			else
				throw $ex;
		}
		
		$accessControl = new KalturaAccessControl();
		$accessControl->fromObject($dbAccessControl);
		return $accessControl;
	}
	
	/**
	 * Get Access Control Profile by id
	 * 
	 * @action get
	 * @param int $id
	 * @return KalturaAccessControl
	 */
	function getAction($id)
	{
		$dbAccessControl = accessControlPeer::retrieveByPK($id);
		if (!$dbAccessControl)
			throw new KalturaAPIException(KalturaErrors::ACCESS_CONTROL_ID_NOT_FOUND, $id);
			
		$accessControl = new KalturaAccessControl();
		$accessControl->fromObject($dbAccessControl);
		return $accessControl;
	}
	
	/**
	 * Update Access Control Profile by id
	 * 
	 * @action update
	 * @param int $id
	 * @param KalturaAccessControl $accessControl
	 * @return KalturaAccessControl
	 */
	function updateAction($id, KalturaAccessControl $accessControl)
	{
		$dbAccessControl = accessControlPeer::retrieveByPK($id);
		if (!$dbAccessControl)
			throw new KalturaAPIException(KalturaErrors::ACCESS_CONTROL_ID_NOT_FOUND, $id);
		
		if ($accessControl->name !== null)
			$accessControl->validatePropertyMinLength("name", 1);
			
		$accessControl->toUpdatableObject($dbAccessControl);
		$dbAccessControl->save();
		
		$accessControl = new KalturaAccessControl();
		$accessControl->fromObject($dbAccessControl);
		return $accessControl;
	}
	
	/**
	 * Delete Access Control Profile by id
	 * 
	 * @action delete
	 * @param int $id
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
		{
			entryPeer::updateAccessControl($this->getPartnerId(), $id, $this->getPartner()->getDefaultAccessControlId());
		}
			
		$dbAccessControl->setDeletedAt(time());
		$dbAccessControl->save();
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

		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$accessControlFilter = new accessControlFilter();
		
		$filter->toObject($accessControlFilter);

		$c = new Criteria();
		$accessControlFilter->attachToCriteria($c);
		
		$totalCount = accessControlPeer::doCount($c);
		
		$pager->attachToCriteria($c);
		$dbList = accessControlPeer::doSelect($c);
		
		$list = KalturaAccessControlArray::fromDbArray($dbList);
		$response = new KalturaAccessControlListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;    
	}
	
	private function handleCoreException(kCoreException $ex, accessControl $accessControlDb, KalturaAccessControl $accessControl)
	{
		switch($ex->getCode())
		{
			case kCoreException::MAX_NUMBER_OF_ACCESS_CONTROLS_REACHED:
				throw new KalturaAPIException(KalturaErrors::MAX_NUMBER_OF_ACCESS_CONTROLS_REACHED, Partner::MAX_ACCESS_CONTROLS);
				
			default:
				throw $ex;
		}
	}
}