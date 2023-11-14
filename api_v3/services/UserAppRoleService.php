<?php
/**
 * Manage application based roles for user
 *
 * @service userAppRole
 * @package api
 * @subpackage services
 */
class UserAppRoleService extends KalturaBaseService
{
	/**
	 * @param $serviceId
	 * @param $serviceName
	 * @param $actionName
	 * @return void
	 * @throws KalturaAPIException
	 */
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		// Add Partner Ids to criteria for the following peers during execution of that service
		$this->applyPartnerFilterForClass('UserRole');
	}
	
	protected function partnerGroup($peer = null)
	{
		// if the current KS is an impersonated KS system partner (pid < 0) add pid to mysql query
		if (kCurrentContext::$is_admin_session && isset(kCurrentContext::$master_partner_id) && kCurrentContext::$master_partner_id < 0)
		{
			return $this->partnerGroup . ',' . kCurrentContext::$master_partner_id;
		}
		
		return $this->partnerGroup;
	}
	
	/**
	 * Assign an application role for a user
	 *
	 * @action add
	 *
	 * @param KalturaUserAppRole $userAppRole
	 * @return KalturaUserAppRole
	 */
	public function addAction(KalturaUserAppRole $userAppRole)
	{
		// prevent race condition where 2 or more concurrent requests are fired
		$lockKey = 'userAppRole_add_' . kCurrentContext::getCurrentPartnerId() . '_' . $userAppRole->appGuid . '_' . $userAppRole->userId;
		return kLock::runLocked($lockKey, array($this, 'addUserAppRole'), array($userAppRole));
	}
	
	/**
	 * @throws KalturaAPIException
	 * @throws kCoreException
	 * @throws PropelException
	 */
	function addUserAppRole(KalturaUserAppRole $userAppRole)
	{
		$dbUserAppRole = $userAppRole->toInsertableObject();
		$dbUserAppRole->save();
		
		$userAppRole = new KalturaUserAppRole();
		$userAppRole->fromObject($dbUserAppRole, $this->getResponseProfile());
		
		return $userAppRole;
	}
	
	/**
	 * Update an application role for a user
	 *
	 * @action update
	 *
	 * @param string $userId
	 * @param string $appGuid
	 * @param KalturaUserAppRole $userAppRole
	 * @return KalturaUserAppRole
	 *
	 * @throws KalturaAPIException
	 * @throws PropelException
	 * @throws Exception
	 */
	public function updateAction($userId, $appGuid, KalturaUserAppRole $userAppRole)
	{
		$dbUserAppRole = $this->getByUserAndAppGuid($userId, $appGuid);
		$dbUserAppRole = $userAppRole->toUpdatableObject($dbUserAppRole);
		$dbUserAppRole->save();
		
		$userAppRole = new KalturaUserAppRole();
		$userAppRole->fromObject($dbUserAppRole, $this->getResponseProfile());
		
		return $userAppRole;
	}
	
	/**
	 * Get an application role for a user and app guid
	 *
	 * @action get
	 *
	 * @param string $userId the user id
	 * @param string $appGuid the app-registry id
	 * @return KalturaUserAppRole
	 *
	 * @throws KalturaAPIException
	 * @throws PropelException
	 */
	public function getAction($userId, $appGuid)
	{
		$dbUserAppRole = $this->getByUserAndAppGuid($userId, $appGuid);
		
		$userAppRole = new KalturaUserAppRole();
		$userAppRole->fromObject($dbUserAppRole, $this->getResponseProfile());
		
		return $userAppRole;
	}
	
	/**
	 * List an application roles by filter and pager
	 *
	 * @action list
	 *
	 * @param KalturaUserAppRoleFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaUserAppRoleListResponse
	 *
	 * @throws KalturaAPIException
	 * @throws PropelException
	 */
	public function listAction(KalturaUserAppRoleFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter || !($filter->userIdEqual || $filter->userIdIn || $filter->userRoleIdEqual || $filter->userRoleIdIn || $filter->appGuidEqual || $filter->appGuidIn))
		{
			throw new KalturaAPIException(KalturaErrors::MUST_FILTER_USERS_OR_APP_GUID_OR_USER_ROLE);
		}
		
		if (!$pager)
		{
			$pager = new KalturaFilterPager();
		}
		
		return $filter->getListResponse($pager, $this->getResponseProfile());
	}
	
	/**
	 * Delete an application role for a user and app guid
	 *
	 * @action delete
	 *
	 * @param string $userId the user id
	 * @param string $appGuid the app-registry id
	 * @return bool
	 *
	 * @throws KalturaAPIException
	 * @throws PropelException
	 */
	public function deleteAction($userId, $appGuid)
	{
		$dbUserAppRole = $this->getByUserAndAppGuid($userId, $appGuid);
		$dbUserAppRole->delete();
		
		return true;
	}
	
	/**
	 * @param string $userId
	 * @param string $appGuid
	 * @return KuserToUserRole
	 *
	 * @throws KalturaAPIException
	 * @throws PropelException
	 */
	protected function getByUserAndAppGuid($userId, $appGuid)
	{
		$puserId = trim($userId);
		$appGuid = trim($appGuid);
		
		if (!kCurrentContext::$is_admin_session && kCurrentContext::$ks_uid != $puserId)
		{
			throw new KalturaAPIException(KalturaErrors::CANNOT_RETRIEVE_ANOTHER_USER_USING_NON_ADMIN_SESSION, $puserId);
		}
		
		if (!kString::isValidMongoId($appGuid))
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_APP_GUID, $appGuid);
		}
		
		// if action is 'update' - verify kuser is active (not 'blocked')
		if ($this->actionName == 'update')
		{
			$kuser = kuserPeer::getActiveKuserByPartnerAndUid(kCurrentContext::getCurrentPartnerId(), $puserId);
		}
		else
		{
			$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::getCurrentPartnerId(), $puserId);
		}
		
		if (!$kuser)
		{
			throw new KalturaAPIException(KalturaErrors::USER_ID_NOT_FOUND, $puserId);
		}
		
		$dbUserAppRole = KuserToUserRolePeer::getByKuserIdAndAppGuid($kuser->getId(), $appGuid);
		
		if (!$dbUserAppRole)
		{
			throw new KalturaAPIException(KalturaErrors::USER_APP_ROLE_NOT_FOUND, $puserId, $appGuid);
		}
		
		return $dbUserAppRole;
	}
}

