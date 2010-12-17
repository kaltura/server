<?php

/**
 * UserRole service lets you create and manage user permissions
 * @service userRole
 * @package api
 * @subpackage services
 */
class UserRoleService extends KalturaBaseService
{
	public function initService($partnerId, $puserId, $ksStr, $serviceName, $action)
	{
		parent::initService($partnerId, $puserId, $ksStr, $serviceName, $action);
		
		myPartnerUtils::addPartnerToCriteria(new UserRolePeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		myPartnerUtils::addPartnerToCriteria(new PermissionPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
	}	

	
	/**
	 * Allows you to add a new KalturaUserRole object
	 * 
	 * @action add
	 * @param KalturaUserRole $permissionItem
	 * @return KalturaUserRole
	 * 
	 * @throws KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL
	 * @throws KalturaErrors::PROPERTY_VALIDATION_NOT_UPDATABLE
	 */
	public function addAction(KalturaUserRole $userRole)
	{
		$userRole->validateForInsert();
		$userRole->validatePropertyNotNull('name');
		
		if (!$userRole->status) {
			$userRole->status = KalturaUserRoleStatus::ACTIVE;
		}
							
		$dbUserRole = $userRole->toInsertableObject();
		$dbUserRole->setPartnerId($this->getPartnerId());
		$dbUserRole->save();
		
		$userRole = new KalturaUserRole();
		$userRole->fromObject($dbUserRole);
		
		return $userRole;
	}
	
	/**
	 * Retrieve a KalturaUserRole object by ID
	 * 
	 * @action get
	 * @param int $userRoleId 
	 * @return KalturaUserRole
	 * 
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	public function getAction($userRoleId)
	{
		$dbUserRole = UserRolePeer::retrieveByPK($userRoleId);
		
		if (!$dbUserRole) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $userRoleId);
		}
			
		$userRole = new KalturaUserRole();
		$userRole->fromObject($dbUserRole);
		
		return $userRole;
	}


	/**
	 * Update an existing KalturaUserRole object
	 * 
	 * @action update
	 * @param int $userRoleId
	 * @param KalturaUserRole $userRole
	 * @return KalturaUserRole
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */	
	public function updateAction($userRoleId, KalturaUserRole $userRole)
	{
		$dbUserRole = UserRolePeer::retrieveByPK($userRoleId);
	
		if (!$dbUserRole) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $userRoleId);
		}
		
		$dbUserRole = $userRole->toUpdatableObject($dbUserRole);
		$dbUserRole->save();
	
		$userRole = new KalturaUserRole();
		$userRole->fromObject($dbUserRole);
		
		return $userRole;
	}

	/**
	 * Mark the KalturaUserRole object as deleted
	 * 
	 * @action delete
	 * @param int $userRoleId 
	 * @return KalturaUserRole
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	public function deleteAction($userRoleId)
	{
		$dbUserRole = UserRolePeer::retrieveByPK($userRoleId);
	
		if (!$dbUserRole) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $userRoleId);
		}
		
		$dbUserRole->setStatus(KalturaUserRoleStatus::DELETED);
		$dbUserRole->save();
			
		$userRole = new KalturaUserRole();
		$userRole->fromObject($dbUserRole);
		
		return $userRole;
	}
	
	/**
	 * List permission items
	 * 
	 * @action list
	 * @param KalturaUserRoleFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaUserRoleListResponse
	 */
	public function listAction(KalturaUserRoleFilter  $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaUserRoleFilter();
			
		$userRoleFilter = $filter->toObject();
		
		UserRolePeer::setUseCriteriaFilter(false);

		$c = new Criteria();
		$c->addAnd(UserRolePeer::PARTNER_ID, array (0, $this->getPartnerId()), Criteria::IN);
		$userRoleFilter->attachToCriteria($c);
		$count = UserRolePeer::doCount($c);
		
		if ($pager)
			$pager->attachToCriteria($c);
		$list = UserRolePeer::doSelect($c);
		
		UserRolePeer::setUseCriteriaFilter(true);
		
		$response = new KalturaUserRoleListResponse();
		$response->objects = KalturaUserRoleArray::fromDbArray($list);
		$response->totalCount = $count;
		
		return $response;
	}
}
