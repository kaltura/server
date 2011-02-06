<?php

/**
 * Permission service lets you create and manage user permissions
 * @service permission
 * @package api
 * @subpackage services
 */
class PermissionService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		myPartnerUtils::addPartnerToCriteria(new PermissionPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		myPartnerUtils::addPartnerToCriteria(new PermissionItemPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
	}
	
	protected function globalPartnerAllowed($actionName)
	{
		if ($actionName === 'get') {
			return true;
		}
		if ($actionName === 'list') {
			return true;
		}
		return parent::globalPartnerAllowed($actionName);
	}
	
	protected function partnerRequired($actionName)
	{
		if ($actionName === 'getCurrentPermissions') {
			return false;
		}
		return parent::partnerRequired($actionName);
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
		
		try { PermissionPeer::addToPartner($dbPermission, $this->getPartnerId()); }
		catch (kPermissionException $e) {
			$code = $e->getCode();
			if ($code === kPermissionException::PERMISSION_ALREADY_EXISTS) {
				throw new KalturaAPIException(KalturaErrors::PERMISSION_ALREADY_EXISTS, $permission->getName(), $this->getPartnerId());
			}
			throw $e;
		}
		
		$permission = new KalturaPermission();
		$permission->fromObject($dbPermission);
		
		return $permission;
	}
	
	/**
	 * Retrieve a KalturaPermission object by ID
	 * 
	 * @action get
	 * @param string $permissionName 
	 * @return KalturaPermission
	 * 
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	public function getAction($permissionName)
	{
		$dbPermission = PermissionPeer::getByNameAndPartner($permissionName, explode(',', $this->partnerGroup()));
		
		if (!$dbPermission) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $permissionName);
		}
			
		$permission = new KalturaPermission();
		$permission->fromObject($dbPermission);
		
		return $permission;
	}


	/**
	 * Update an existing KalturaPermission object
	 * 
	 * @action update
	 * @param string $permissionName
	 * @param KalturaPermission $permission
	 * @return KalturaPermission
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */	
	public function updateAction($permissionName, KalturaPermission $permission)
	{
		$dbPermission = PermissionPeer::getByNameAndPartner($permissionName, explode(',', $this->partnerGroup()));
		
		if (!$dbPermission) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $permissionName);
		}
		
		if ($permission->name && $permission->name != $permissionName)
		{
			$existingPermission = PermissionPeer::getByNameAndPartner($permission->name, array($dbPermission->getPartnerId(), PartnerPeer::GLOBAL_PARTNER));
			if ($existingPermission)
			{
				throw new KalturaAPIException(KalturaErrors::PERMISSION_ALREADY_EXISTS, $permission->name, $this->getPartnerId());
			}
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
	 * @param string $permissionName 
	 * @return KalturaPermission
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	public function deleteAction($permissionName)
	{
		$dbPermission = PermissionPeer::getByNameAndPartner($permissionName, array($this->partnerGroup()));
		
		if (!$dbPermission) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $permissionName);
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
	
	/**
	 * Return a list of current sessions's allowed permission names
	 * 
	 * @action getCurrentPermissions
	 * 
	 * @return string Comma seperated string of current permission names
	 * 
	 */	
	public function getCurrentPermissions()
	{	
		$permissions = kPermissionManager::getCurrentPermissions();
		$permissions = implode(',', $permissions);
		return $permissions;
	}
	
}
