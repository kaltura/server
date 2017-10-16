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

		self::applyPartnerFilterForClass('Permission');
		self::applyPartnerFilterForClass('PermissionItem');
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
	 * Adds a new permission object to the account.
	 * 
	 * @action add
	 * @param KalturaPermission $permission The new permission
	 * @return KalturaPermission The added permission object
	 * 
	 * @throws KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL
	 * @throws KalturaErrors::PROPERTY_VALIDATION_NOT_UPDATABLE
	 */
	public function addAction(KalturaPermission $permission)
	{
		$permission->validatePropertyNotNull('name');
		
		if (strpos($permission->name, ',') !== false) {
			throw new KalturaAPIException(KalturaErrors::INVALID_FIELD_VALUE, 'name');
		}

		if (!$permission->friendlyName) {
			$permission->friendlyName = $permission->name;
		}
		
		if (!$permission->status) {
			$permission->status = KalturaPermissionStatus::ACTIVE;
		}
											
		$dbPermission = $permission->toInsertableObject();
		
		$dbPermission->setType(PermissionType::NORMAL);  // only normal permission types are added through this services
		$dbPermission->setPartnerId($this->getPartnerId());
		
		try { PermissionPeer::addToPartner($dbPermission, $this->getPartnerId()); }
		catch (kPermissionException $e) {
			$code = $e->getCode();
			if ($code === kPermissionException::PERMISSION_ALREADY_EXISTS) {
				throw new KalturaAPIException(KalturaErrors::PERMISSION_ALREADY_EXISTS, $dbPermission->getName(), $this->getPartnerId());
			}
			if ($code === kPermissionException::PERMISSION_ITEM_NOT_FOUND) {
				throw new KalturaAPIException(KalturaErrors::PERMISSION_ITEM_NOT_FOUND);
			}			
			throw $e;
		}
		
		$permission = new KalturaPermission();
		$permission->fromObject($dbPermission, $this->getResponseProfile());
		
		return $permission;
	}
	
	/**
	 * Retrieves a permission object using its ID.
	 * 
	 * @action get
	 * @param string $permissionName The name assigned to the permission
	 * @return KalturaPermission The retrieved permission object
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
		$permission->fromObject($dbPermission, $this->getResponseProfile());
		
		return $permission;
	}


	/**
	 * Updates an existing permission object.
	 * 
	 * @action update
	 * @param string $permissionName The name assigned to the permission
	 * @param KalturaPermission $permission The updated permission parameters
	 * @return KalturaPermission The updated permission object
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */	
	public function updateAction($permissionName, KalturaPermission $permission)
	{
		$dbPermission = PermissionPeer::getByNameAndPartner($permissionName, explode(',', $this->partnerGroup()));
		
		if (!$dbPermission) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $permissionName);
		}
		
		// only normal permission types are allowed for updating through this service
		if ($dbPermission->getType() !== PermissionType::NORMAL)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $permissionName);
		}
		
		if ($permission->name && $permission->name != $permissionName)
		{
			if (strpos($permission->name, ',') !== false) {
				throw new KalturaAPIException(KalturaErrors::INVALID_FIELD_VALUE, 'name');
			}
			
			$existingPermission = PermissionPeer::getByNameAndPartner($permission->name, array($dbPermission->getPartnerId(), PartnerPeer::GLOBAL_PARTNER));
			if ($existingPermission)
			{
				throw new KalturaAPIException(KalturaErrors::PERMISSION_ALREADY_EXISTS, $permission->name, $this->getPartnerId());
			}
		}
		
		$dbPermission = $permission->toUpdatableObject($dbPermission);
		try
		{
			$dbPermission->save();
		}
		catch (kPermissionException $e)
		{
			$code = $e->getCode();
			if ($code === kPermissionException::PERMISSION_ITEM_NOT_FOUND) {
				throw new KalturaAPIException(KalturaErrors::PERMISSION_ITEM_NOT_FOUND);
			}
		}			
		
		$permission = new KalturaPermission();
		$permission->fromObject($dbPermission, $this->getResponseProfile());
		
		return $permission;
	}

	/**
	 * Deletes an existing permission object.
	 * 
	 * @action delete
	 * @param string $permissionName The name assigned to the permission
	 * @return KalturaPermission The deleted permission object
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
		$permission->fromObject($dbPermission, $this->getResponseProfile());
		
		return $permission;
	}
	
	/**
	 * Lists permission objects that are associated with an account.
	 * Blocked permissions are listed unless you use a filter to exclude them.
	 * Blocked permissions are listed unless you use a filter to exclude them.
	 * 
	 * @action list
	 * @param KalturaPermissionFilter $filter A filter used to exclude specific types of permissions
	 * @param KalturaFilterPager $pager A limit for the number of records to display on a page
	 * @return KalturaPermissionListResponse The list of permission objects
	 */
	public function listAction(KalturaPermissionFilter  $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaPermissionFilter();
			
		if(!$pager)
			$pager = new KalturaFilterPager();
			
		return $filter->getListResponse($pager, $this->getResponseProfile());
	}
	
	/**
	 * Retrieves a list of permissions that apply to the current KS.
	 * 
	 * @action getCurrentPermissions
	 * 
	 * @return string A comma-separated list of current permission names
	 * @ksOptional
	 * 
	 */	
	public function getCurrentPermissions()
	{	
		$permissions = kPermissionManager::getCurrentPermissions();
		$permissions = implode(',', $permissions);
		return $permissions;
	}
	
}
