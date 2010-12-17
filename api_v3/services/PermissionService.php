<?php

/**
 * Permission service lets you create and manage user permissions
 * @service permission
 * @package api
 * @subpackage services
 */
class PermissionService extends KalturaBaseService
{
	public function initService($partnerId, $puserId, $ksStr, $serviceName, $action)
	{
		parent::initService($partnerId, $puserId, $ksStr, $serviceName, $action);

		myPartnerUtils::addPartnerToCriteria(new PermissionPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
	}	

	
	/**
	 * Allows you to add a new KalturaPermission object
	 * 
	 * @action add
	 * @param KalturaPermission $permission
	 * @return KalturaPermission
	 * 
	 * @throws KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL
	 * @throws KalturaErrors::PROPERTY_VALIDATION_NOT_UPDATABLE
	 */
	public function addAction(KalturaPermission $permission)
	{
		$permission->validateForInsert();
		$permission->validatePropertyNotNull('name');

		if (!$permission->friendlyName) {
			$permission->friendlyName = $permission->name;
		}
		
		if (!$permission->status) {
			$permission->status = KalturaPermissionStatus::ACTIVE;
		}
							
		$dbPermission = $permission->toInsertableObject();
		$dbPermission->setPartnerId($this->getPartnerId());
		$dbPermission->save();
		
		$permission = new KalturaPermission();
		$permission->fromObject($dbPermission);
		
		return $permission;
	}
	
	/**
	 * Retrieve a KalturaPermission object by ID
	 * 
	 * @action get
	 * @param int $permissionId 
	 * @return KalturaPermission
	 * 
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	public function getAction($permissionId)
	{
		$dbPermission = PermissionPeer::retrieveByPK($permissionId);
		
		if (!$dbPermission) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $permissionId);
		}
			
		$permission = new KalturaPermission();
		$permission->fromObject($dbPermission);
		
		return $permission;
	}


	/**
	 * Update an existing KalturaPermission object
	 * 
	 * @action update
	 * @param int $permissionId
	 * @param KalturaPermission $permission
	 * @return KalturaPermission
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */	
	public function updateAction($permissionId, KalturaPermission $permission)
	{
		$dbPermission = PermissionPeer::retrieveByPK($permissionId);
	
		if (!$dbPermission) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $permissionId);
		}
		
		$dbPermission = $permission->toUpdatableObject($dbPermission);
		$dbPermission->save();
	
		$permission = new KalturaPermission();
		$permission->fromObject($dbPermission);
		
		return $permission;
	}

	/**
	 * Mark the KalturaPermission object as deleted
	 * 
	 * @action delete
	 * @param int $permissionId 
	 * @return KalturaPermission
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	public function deleteAction($permissionId)
	{
		$dbPermission = PermissionPeer::retrieveByPK($permissionId);
	
		if (!$dbPermission) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $permissionId);
		}
		
		$dbPermission->setStatus(KalturaPermissionStatus::DELETED);
		$dbPermission->save();
			
		$permission = new KalturaPermission();
		$permission->fromObject($dbPermission);
		
		return $permission;
	}
	
	/**
	 * List KalturaPermission objects
	 * 
	 * @action list
	 * @param KalturaPermissionFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaPermissionListResponse
	 */
	public function listAction(KalturaPermissionFilter  $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaPermissionFilter();
			
		$permissionFilter = $filter->toObject();
		
		$c = new Criteria();
		$permissionFilter->attachToCriteria($c);
		$count = PermissionPeer::doCount($c);
		
		if ($pager)
			$pager->attachToCriteria($c);
		$list = PermissionPeer::doSelect($c);
		
		$response = new KalturaPermissionListResponse();
		$response->objects = KalturaPermissionArray::fromDbArray($list);
		$response->totalCount = $count;
		
		return $response;
	}
	
}
