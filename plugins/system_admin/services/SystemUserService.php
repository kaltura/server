<?php
/**
 * System user service
 *
 * @service systemUser
 */
class SystemUserService extends KalturaBaseService
{
	public function initService($partnerId, $puserId, $ksStr, $serviceName, $action)
	{
		parent::initService($partnerId, $puserId, $ksStr, $serviceName, $action);

		// since plugin might be using KS impersonation, we need to validate the requesting
		// partnerId from the KS and not with the $_POST one
		if(!SystemPartnerPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(SystemUserErrors::SERVICE_FORBIDDEN);
	}
	
	/**
	 * Verify password for email address
	 * 
	 * @action verifyPassword
	 * @param string $email
	 * @param string $password
	 * @return KalturaSystemUser
	 */
	function verifyPasswordAction($email, $password)
	{
		KalturaResponseCacher::disableCache();
		
		$user = SystemUserPeer::retrieveByEmail($email);
		if (!$user)
			throw new KalturaAPIException(SystemUserErrors::SYSTEM_USER_INVALID_CREDENTIALS); // same error as password not valid
			
		if ($user->getStatus() !== SystemUser::SYSTEM_USER_ACTIVE)
			 throw new KalturaAPIException(SystemUserErrors::SYSTEM_USER_DISABLED);
			
		if (!$user->isPasswordValid($password))
			throw new KalturaAPIException(SystemUserErrors::SYSTEM_USER_INVALID_CREDENTIALS);

		$systemUser = new KalturaSystemUser();
		$systemUser->fromObject($user);
		
		return $systemUser;
	}
	
	/**
	 * Generate new random password
	 * 
	 * @action generateNewPassword
	 * @return string
	 */
	function generateNewPasswordAction()
	{
		 return SystemUser::generateRandomPassword();
	}
	
	/**
	 * Set new password for user by email address
	 * 
	 * @action setNewPassword
	 * @param int $userId
	 * @param string $password
	 */
	function setNewPasswordAction($userId, $password)
	{
		KalturaResponseCacher::disableCache();
		
		$dbSystemUser = SystemUserPeer::retrieveByPK($userId);
		if (!$dbSystemUser)
			throw new KalturaAPIException(SystemUserErrors::SYSTEM_USER_NOT_FOUND);
			
		$dbSystemUser->setPassword($password);
		$dbSystemUser->save();
	}
	
	/**
	 * Add new system administrative user
	 * 
	 * @action add
	 * @param KalturaSystemUser $systemUser
	 * @return KalturaSystemUser
	 */
	function addAction(KalturaSystemUser $systemUser)
	{
		// TODO: validate object
		$user = SystemUserPeer::retrieveByEmail($systemUser->email);
		if ($user)
			throw new KalturaAPIException(SystemUserErrors::SYSTEM_USER_ALREADY_EXISTS);
						
		$dbSystemUser = $systemUser->toObject();
		$dbSystemUser->save();
		
		$systemUser->fromObject($dbSystemUser);
		return $systemUser;
	}
	
	/**
	 * Get system administrative user by id
	 * 
	 * @action get
	 * @param int $userId
	 * @return KalturaSystemUser
	 */
	function getAction($userId)
	{
		$dbSystemUser = SystemUserPeer::retrieveByPK($userId);
		if (!$dbSystemUser)
			return null;
			
		$systemUser = new KalturaSystemUser();
		$systemUser->fromObject($dbSystemUser);
		return $systemUser;
	}
	
	/**
	 * Get system administrative user by email
	 * 
	 * @action getByEmail
	 * @param string $email
	 * @return KalturaSystemUser
	 */
	function getByEmailAction($email)
	{
		$dbSystemUser = SystemUserPeer::retrieveByEmail($email);
		if (!$dbSystemUser)
			return null;

		$systemUser = new KalturaSystemUser();
		$systemUser->fromObject($dbSystemUser);
		return $systemUser;
	}
	
	/**
	 * Update system administrative user by id 
	 * 
	 * @action update
	 * @param int $userId
	 * @param KalturaSystemUser $systemUser
	 * @return KalturaSystemUser
	 */
	function updateAction($userId, KalturaSystemUser $systemUser)
	{
		// TODO: validate object
		$dbSystemUser = SystemUserPeer::retrieveByPK($userId);
		if (!$dbSystemUser)
			throw new KalturaAPIException(SystemUserErrors::SYSTEM_USER_NOT_FOUND);
			
		if (!is_null($systemUser->email))
		{
			$tempUser = SystemUserPeer::retrieveByEmail($systemUser->email);
			if ($tempUser && $tempUser->getId() !== $dbSystemUser->getId())
				throw new KalturaAPIException(SystemUserErrors::SYSTEM_USER_ALREADY_EXISTS);
		}
			
		$dbSystemUser = $systemUser->toUpdatableObject($dbSystemUser);
		$dbSystemUser->save();
		
		$systemUser->fromObject($dbSystemUser);
		return $systemUser;
	}
	
	/**
	 * Delete system administrative user by id
	 * 
	 * @action delete
	 * @param int $userId
	 */
	function deleteAction($userId)
	{
		$dbSystemUser = SystemUserPeer::retrieveByPK($userId);
		if (!$dbSystemUser)
			throw new KalturaAPIException(SystemUserErrors::SYSTEM_USER_NOT_FOUND);
			
		$dbSystemUser->setDeletedAt(time());
		$dbSystemUser->setEmail('_DELETED_'.time().'_'.$dbSystemUser->getEmail());
		$dbSystemUser->save();
	}
	
	/**
	 * List system administrative users by filter and pager
	 * 
	 * @action list
	 * @param KalturaSystemUserFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaSystemUserListResponse
	 */
	function listAction(KalturaSystemUserFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaSystemUserFilter();

		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$systemUserFilter = new SystemUserFilter();
		
		$filter->toObject($systemUserFilter);

		$c = new Criteria();
		$systemUserFilter->attachToCriteria($c);
		
		$totalCount = SystemUserPeer::doCount($c);
		
		$pager->attachToCriteria($c);
		$dbList = SystemUserPeer::doSelect($c);
		
		$list = KalturaSystemUserArray::fromDbArray($dbList);
		$response = new KalturaSystemUserListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;
	}
}
