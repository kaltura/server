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
	
	/**
	 * Assign an application role for a user
	 *
	 * @action add
	 *
	 * @param KalturaUserAppRole $userAppRole
	 * @return KalturaUserAppRole
	 *
	 * @throws KalturaAPIException
	 * @throws PropelException
	 * @throws Exception
	 */
	public function addAction(KalturaUserAppRole $userAppRole)
	{
		// todo: consider adding $master_partner_id = -11
		// todo: if it's EP impersonated session, fetch user_role from 'global_partner' (0)
		
//		try
//		{
			$dbUserAppRole = $userAppRole->toInsertableObject();
			$dbUserAppRole->save();
//		}
//		catch (kCoreException $ex)
//		{
//			$this->handleCoreException($ex);
//		}
		
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
		
//		try
//		{
			$dbUserAppRole = $userAppRole->toUpdatableObject($dbUserAppRole);
			$dbUserAppRole->save();
//		}
//		catch (kCoreException $ex)
//		{
//			$this->handleCoreException($ex);
//		}
		
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
	 * @throws kCoreException
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
	 * @throws kCoreException
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
	 * @throws kCoreException
	 */
	protected function getByUserAndAppGuid($userId, $appGuid)
	{
		$puserId = trim($userId);
		$appGuid = trim($appGuid);
		
		if (!kCurrentContext::$is_admin_session && kCurrentContext::$ks_uid != $puserId)
		{
			throw new KalturaAPIException(KalturaErrors::CANNOT_RETRIEVE_ANOTHER_USER_USING_NON_ADMIN_SESSION, $puserId);
		}
		
		$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::getCurrentPartnerId(), $puserId);
		
		if (!$kuser)
		{
			throw new KalturaAPIException(KalturaErrors::USER_ID_NOT_FOUND, $puserId);
		}
		
		if (!kString::isValidMongoId($appGuid))
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_APP_GUID, $appGuid);
		}
		
		// validate appGuid belong to ks partner
		$appGuidExist = MicroServiceAppRegistry::getExistingAppGuid(kCurrentContext::getCurrentPartnerId(), $appGuid);
		if (!$appGuidExist)
		{
			throw new KalturaAPIException(KalturaErrors::APP_GUID_NOT_FOUND, $appGuid);
		}
		
		$dbUserAppRole = KuserToUserRolePeer::getByKuserIdAndAppGuid($kuser->getId(), $appGuid);
		
		if (!$dbUserAppRole)
		{
			throw new KalturaAPIException(KalturaErrors::USER_APP_ROLE_NOT_FOUND, $puserId, $appGuid);
		}
		
		return $dbUserAppRole;
	}
	
	/**
	 * @throws KalturaAPIException
	 * @throws kCoreException
	 */
//	protected function handleCoreException(kCoreException $ex)
//	{
//		switch ($ex->getCode())
//		{
//			case kCoreException::USER_APP_ROLE_NOT_ALLOWED_FOR_GROUP:
//				throw new KalturaAPIException(KalturaErrors::USER_APP_ROLE_NOT_ALLOWED_FOR_GROUP);
//
//			case kCoreException::USER_ROLE_NOT_FOUND:
//				throw new KalturaAPIException(KalturaErrors::USER_ROLE_NOT_FOUND);
//
//			case kCoreException::USER_APP_ROLE_ALREADY_EXISTS:
//				$args = explode(',', $ex->getData());
//				throw new KalturaAPIException(KalturaErrors::USER_APP_ROLE_ALREADY_EXISTS, $args[0], $args[1]);
//
//			case kCoreException::CANNOT_RETRIEVE_ANOTHER_USER_USING_NON_ADMIN_SESSION:
//				throw new KalturaAPIException(KalturaErrors::CANNOT_RETRIEVE_ANOTHER_USER_USING_NON_ADMIN_SESSION, $ex->getData());
//
//			case kCoreException::INVALID_USER_ID:
//				throw new KalturaAPIException(KalturaErrors::USER_ID_NOT_FOUND, $ex->getData());
//
//			case kCoreException::INVALID_APP_GUID:
//				throw new KalturaAPIException(KalturaErrors::INVALID_APP_GUID, $ex->getData());
//
//			case kCoreException::APP_GUID_NOT_FOUND:
//				throw new KalturaAPIException(KalturaErrors::APP_GUID_NOT_FOUND, $ex->getData());
//
//			case kCoreException::USER_APP_ROLE_NOT_FOUND:
//				$args = explode(',', $ex->getData());
//				throw new KalturaAPIException(KalturaErrors::USER_APP_ROLE_NOT_FOUND, $args[0], $args[1]);
//
//			case kCoreException::FAILED_TO_INSTANTIATE_MICROSERVICE_CACHE:
//				// todo - do I want to throw API exception for cache? probably not
//				// currently throws 'INTERNAL_SERVER_ERROR'
//
//			default:
//				throw $ex;
//		}
//	}
}
