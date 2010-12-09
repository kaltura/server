<?php
/**
 * Manage partner users on Kaltura's side
 * The userId in kaltura is the unique Id in the partner's system, and the [partnerId,Id] couple are unique key in kaltura's DB
 *
 * @service user
 * @package api
 * @subpackage services
 */
class UserService extends KalturaBaseUserService 
{
	
	/**
	 * Adds a user to the Kaltura DB.
	 * Input param $id is the unique identifier in the partner's system
	 *
	 * @action add
	 * @param KalturaUser $user 
	 * @return KalturaUser
	 *
	 * @throws KalturaErrors::DUPLICATE_USER_BY_ID
	 * @throws KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL
	 * @throws KalturaErrors::ADMIN_USER_PASSWORD_MISSING
	 * @throws KalturaErrors::INVALID_FIELD_VALUE
	 * @throws KalturaErrors::UNKNOWN_PARTNER_ID
	 * @throws KalturaErrors::LOGIN_USERS_QUOTA_EXCEEDED
	 * @throws KalturaErrors::USER_EXISTS_WITH_DIFFERENT_PASSWORD
	 */
	function addAction(KalturaUser $user)
	{		
		$user->validatePropertyNotNull("id");
		
		if ($user instanceof KalturaAdminUser) {
			$user->isAdmin = true;
		}
		
		$user->partnerId = $this->getPartnerId();
		
		$dbUser = null;
		$dbUser = $user->toObject($dbUser);
		
		try {
			$dbUser = kuserPeer::addUser($dbUser, $user->password);
		}
		catch (kUserException $e) {
			$code = $e->getCode();
			if ($code == kUserException::USER_ALREADY_EXISTS) {
				throw new KalturaAPIException(KalturaErrors::DUPLICATE_USER_BY_ID, $user->id); //backward compatibility
			}
			if ($code == kUserException::LOGIN_ID_ALREADY_USED) {
				throw new KalturaAPIException(KalturaErrors::DUPLICATE_USER_BY_LOGIN_ID, $user->email); //backward compatibility
			}
			else if ($code == kUserException::USER_ID_MISSING) {
				throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, $this->getFormattedPropertyNameWithClassName('id'));
			}
			else if ($code == kUserException::INVALID_EMAIL) {
				throw new KalturaAPIException(KalturaErrors::INVALID_FIELD_VALUE, 'email');
			}
			else if ($code == kUserException::INVALID_PARTNER) {
				throw new KalturaAPIException(KalturaErrors::UNKNOWN_PARTNER_ID);
			}
			else if ($code == kUserException::LOGIN_USERS_QUOTA_EXCEEDED) {
				throw new KalturaAPIException(KalturaErrors::LOGIN_USERS_QUOTA_EXCEEDED);
			}
			else if ($code == kUserException::USER_EXISTS_WITH_DIFFERENT_PASSWORD) {
				throw new KalturaAPIException(KalturaErrors::USER_EXISTS_WITH_DIFFERENT_PASSWORD);
			}
			throw $e;			
		}
			
		$newUser = new KalturaUser();
		$newUser->fromObject($dbUser);
		
		return $newUser;
	}
	
	/**
	 * Update exisitng user, it is possible to update the user id too
	 * 
	 * @action update
	 * @param string $userId
	 * @param KalturaUser $user
	 * @param bool $allUserPartners wheter to update the user details for all partners he/she can login to
	 * @return KalturaUser
	 *
	 * @throws APIErrors::INVALID_USER_ID
	 * @throws KalturaErrors::LOGIN_DATA_NOT_FOUND
	 */
	public function updateAction($userId, KalturaUser $user, $allUserPartners = false)
	{		
		$dbUser = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $userId);
		
		if (!$dbUser)
			throw new KalturaAPIException(APIErrors::INVALID_USER_ID, $userId);
			
		if (!$dbUser->getIsAdmin() && $user->isAdmin && !$dbUser->getLoginDataId()) {
			throw new KalturaAPIException(APIErrors::INVALID_USER_ID, $userId);
		}
								
		// update user
		$dbUser = $user->toUpdatableObject($dbUser);
		$dbUser->save();
		
		if ($allUserPartners && $dbUser->getLoginDataId()) {
			// if current user has a login record for multiple partners, update for all partners
			$c = new Criteria();
			$c->addAnd(kuserPeer::LOGIN_DATA_ID, $dbUser->getLoginDataId());
			$c->addAnd(kuserPeer::ID, $dbUser->getId(), Criteria::NOT_EQUAL);
			$otherKusers = kuserPeer::doSelect($c);
			
			foreach ($otherKusers as $kuser) {
				// update user (another kuser record)
				$kuser = $user->toUpdatableObject($kuser);
				$kuser->save();
			}
			
			// update user's login data record
			try {
				UserLoginDataPeer::updateFromUserDetails($dbUser->getLoginDataId(), $dbUser);
			}
			catch (kUserException $e) {
				$code = $e->getCode();
				if ($code == kUserException::LOGIN_DATA_NOT_FOUND) {
					throw new KalturaAPIException(KalturaErrors::LOGIN_DATA_NOT_FOUND);
				}
			}
		}
		
		$user = new KalturaUser();
		$user->fromObject($dbUser);
		
		return $user;
	}

	
	/**
	 * Get user by user ID
	 * 
	 * @action get
	 * @param string $userId
	 * @return KalturaUser
	 *
	 * @throws APIErrors::INVALID_USER_ID
	 */		
	public function getAction($userId)
	{
		$dbUser = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $userId);
	
		if (!$dbUser)
			throw new KalturaAPIException(APIErrors::INVALID_USER_ID, $userId);

		$user = new KalturaUser();
		$user->fromObject($dbUser);
		
		return $user;
	}
	
	/**
	 * Get user by user's login ID and partner ID
	 * 
	 * @action getByLoginId
	 * @param string $loginId
	 * @return KalturaUser
	 * 
	 * @throws KalturaErrors::LOGIN_DATA_NOT_FOUND
	 * @throws KalturaErrors::USER_NOT_FOUND
	 */
	public function getByLoginIdAction($loginId)
	{
		$loginData = UserLoginDataPeer::getByEmail($loginId);
		if (!$loginData) {
			throw new KalturaAPIException(KalturaErrors::LOGIN_DATA_NOT_FOUND);
		}
		
		$kuser = kuserPeer::getByLoginDataAndPartner($loginData->getId(), $this->getPartnerId());
		if (!$kuser) {
			throw new KalturaAPIException(KalturaErrors::USER_NOT_FOUND);
		}
		$user = new KalturaUser();
		$user->fromObject($kuser);
		
		return $user;
	}

	/**
	 * Mark the user as deleted
	 * 
	 * @action delete
	 * @param string $userId 
	 * @return KalturaUser
	 *
	 * @throws APIErrors::INVALID_USER_ID
	 */		
	public function deleteAction($userId)
	{
		$dbUser = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $userId);
	
		if (!$dbUser)
			throw new KalturaAPIException(APIErrors::INVALID_USER_ID, $userId);
		
		$dbUser->setStatus(KalturaUserStatus::DELETED);
		$dbUser->save();
		
		UserLoginDataPeer::notifyOneLessUser($dbUser->getLoginDataId());
			
		$user = new KalturaUser();
		$user->fromObject($dbUser);
		
		return $user;
	}
	
	/**
	 * List users (When not set in the filter, blocked and deleted users will be returned too)
	 * 
	 * @action list
	 * @param KalturaUserFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaUserListResponse
	 */
	public function listAction(KalturaUserFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaUserFilter();
			
		if (!$pager)
			$pager = new KalturaFilterPager();	

		$userFilter = new kuserFilter();
		$filter->toObject($userFilter);
	
		$c = new Criteria();
		$c->addAnd(kuserPeer::PUSER_ID, NULL, Criteria::ISNOTNULL);
		$userFilter->attachToCriteria($c);
		$totalCount = kuserPeer::doCount($c);
		
		$pager->attachToCriteria($c);
		$list = kuserPeer::doSelect($c);

		$newList = KalturaUserArray::fromUserArray($list);
		$response = new KalturaUserListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		
		return $response;
	}
	
	/**
	 * Notify about user ban
	 * 
	 * @action notifyBan
	 * @param string $userId
	 *
	 * @throws APIErrors::INVALID_USER_ID
	 */		
	public function notifyBan($userId)
	{
		$dbUser = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $userId);
		if (!$dbUser)
			throw new KalturaAPIException(APIErrors::INVALID_USER_ID, $userId);
		
		myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_USER_BANNED, $dbUser);
	}

	/**
	 * Get a session using user id and password
	 * 
	 * @action login
	 * @param int $partnerId
	 * @param string $userId
	 * @param string $password
	 * @param int $expiry
	 * @param string $privileges
	 * @return string
	 *
	 * @throws KalturaErrors::USER_NOT_FOUND
	 * @throws KalturaErrors::USER_WRONG_PASSWORD
	 * @throws KalturaErrors::INVALID_PARTNER_ID
	 * @throws KalturaErrors::LOGIN_RETRIES_EXCEEDED
	 * @throws KalturaErrors::LOGIN_BLOCKED
	 * @throws KalturaErrors::PASSWORD_EXPIRED
	 * @throws KalturaErrors::INVALID_PARTNER_ID
	 */		
	public function loginAction($partnerId, $userId, $password, $expiry = 86400, $privileges = '*')
	{
		// exceptions might be thrown
		return parent::loginImpl($userId, null, $password, $partnerId, $expiry, $privileges);
	}
	
	/**
	 * Get a session using user's kaltura id and password
	 * 
	 * @action loginByLoginId
	 * @param int $partnerId
	 * @param string $loginId login email
	 * @param string $password
	 * @param int $expiry
	 * @param string $privileges
	 * @return string
	 *
	 * @throws KalturaErrors::USER_NOT_FOUND
	 * @throws KalturaErrors::USER_WRONG_PASSWORD
	 * @throws KalturaErrors::INVALID_PARTNER_ID
	 * @throws KalturaErrors::LOGIN_RETRIES_EXCEEDED
	 * @throws KalturaErrors::LOGIN_BLOCKED
	 * @throws KalturaErrors::PASSWORD_EXPIRED
	 * @throws KalturaErrors::INVALID_PARTNER_ID
	 */		
	public function loginByLoginIdAction($loginId, $password, $partnerId = null, $expiry = 86400, $privileges = '*')
	{
		// exceptions might be thrown
		return parent::loginImpl(null, $loginId, $password, $partnerId, $expiry, $privileges);
	}
	
	
	/**
	 * Update user password and email
	 * 
	 * @action updateLoginData
	 * 
	 * @param string $oldLoginId
	 * @param string $password
	 * @param string $newLoginId Optional, provide only when you want to update the login id
	 * @param string $newPassword
	 * @return KalturaUser
	 *
	 * @throws KalturaErrors::INVALID_FIELD_VALUE
	 * @throws KalturaErrors::LOGIN_DATA_NOT_FOUND
	 * @throws KalturaErrors::WRONG_OLD_PASSWORD
	 * @throws KalturaErrors::PASSWORD_STRUCTURE_INVALID
	 * @throws KalturaErrors::PASSWORD_ALREADY_USED
	 * @throws KalturaErrors::INVALID_FIELD_VALUE
	 * @throws KalturaErrors::LOGIN_ID_ALREADY_USED
	 */
	public function updateLoginDataAction( $oldLoginId , $password , $newLoginId = "" , $newPassword = "")
	{	
		return parent::updateLoginDataImpl($oldLoginId , $password , $newLoginId, $newPassword);
	}
	
	/**
	 * Reset admin user password and send it to the users email address
	 * 
	 * @action resetPassword
	 * 
	 * @param string $email
	 *
	 * @throws KalturaErrors::LOGIN_DATA_NOT_FOUND
	 * @throws KalturaErrors::PASSWORD_STRUCTURE_INVALID
	 * @throws KalturaErrors::PASSWORD_ALREADY_USED
	 * @throws KalturaErrors::INVALID_FIELD_VALUE
	 * @throws KalturaErrors::LOGIN_ID_ALREADY_USED
	 */	
	public function resetPasswordAction($email)
	{
		return parent::resetPasswordImpl($email);
	}
	
	/**
	 * Set initial users password
	 * 
	 * @action setInitialPassword
	 * 
	 * @param string $hashKey
	 * @param string $newPassword new password to set
	 *
	 * @throws KalturaErrors::LOGIN_DATA_NOT_FOUND
	 * @throws KalturaErrors::PASSWORD_STRUCTURE_INVALID
	 * @throws KalturaErrors::NEW_PASSWORD_HASH_KEY_EXPIRED
	 * @throws KalturaErrors::NEW_PASSWORD_HASH_KEY_INVALID
	 * @throws KalturaErrors::PASSWORD_ALREADY_USED
	 * @throws KalturaErrors::INTERNAL_SERVERL_ERROR
	 */	
	public function setInitialPasswordAction($hashKey, $newPassword)
	{
		return parent::setInitialPasswordImpl($hashKey, $newPassword);
	}
	
	/**
	 * Enable the user to login with a loginId (email) and password.
	 * 
	 * @action enableLogin
	 * 
	 * @param string $userId
	 * @param string $loginId
	 * @param string $password
	 * 
	 * @throws KalturaErrors::USER_LOGIN_ALREADY_ENABLED
	 * @throws KalturaErrors::USER_NOT_FOUND
	 * @throws KalturaErrors::LOGIN_USERS_QUOTA_EXCEEDED
	 * @throws KalturaErrors::PASSWORD_STRUCTURE_INVALID
	 * @throws KalturaErrors::LOGIN_ID_ALREADY_USED
	 * @throws KalturaErrors::USER_EXISTS_WITH_DIFFERENT_PASSWORD
	 * @throws KalturaErrors::LOGIN_USERS_QUOTA_EXCEEDED
	 *
	 */	
	public function enableLoginAction($userId, $loginId, $password)
	{		
		try
		{
			$user = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $userId);
			$user->enableLogin($loginId, $password);
		}
		catch (Exception $e)
		{
			$code = $e->getCode();
			if ($code == kUserException::USER_LOGIN_ALREADY_ENABLED) {
				throw new KalturaAPIException(KalturaErrors::USER_LOGIN_ALREADY_ENABLED);
			}
			if ($code == kUserException::INVALID_EMAIL) {
				throw new KalturaAPIException(KalturaErrors::USER_NOT_FOUND);
			}
			else if ($code == kUserException::INVALID_PARTNER) {
				throw new KalturaAPIException(KalturaErrors::USER_NOT_FOUND);
			}
			else if ($code == kUserException::LOGIN_USERS_QUOTA_EXCEEDED) {
				throw new KalturaAPIException(KalturaErrors::LOGIN_USERS_QUOTA_EXCEEDED);
			}
			else if ($code == kUserException::PASSWORD_STRUCTURE_INVALID) {
				throw new KalturaAPIException(KalturaErrors::PASSWORD_STRUCTURE_INVALID);
			}
			else if ($code == kUserException::LOGIN_ID_ALREADY_USED) {
				throw new KalturaAPIException(KalturaErrors::LOGIN_ID_ALREADY_USED);
			}
			else if ($code == kUserException::USER_EXISTS_WITH_DIFFERENT_PASSWORD) {
				throw new KalturaAPIException(KalturaErrors::USER_EXISTS_WITH_DIFFERENT_PASSWORD);
			}
			else if ($code == kUserException::LOGIN_USERS_QUOTA_EXCEEDED) {
				throw new KalturaAPIException(KalturaErrors::LOGIN_USERS_QUOTA_EXCEEDED);
			}
			throw $e;
		}
		
		$apiUser = new KalturaUser();
		$apiUser->fromObject($user);
		return $apiUser;
	}
	
	
	
	/**
	 * Disallow user to login with an id/password.
	 * Passing either a loginId or a userId is allowed.
	 * 
	 * @action disableLogin
	 * 
	 * @param string $loginId
	 * @param string $userId
	 * 
	 * @throws KalturaErrors::USER_LOGIN_ALREADY_DISABLED
	 * @throws KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL
	 * @throws KalturaErrors::USER_NOT_FOUND
	 *
	 */	
	public function disableLoginAction($loginId, $userId = null)
	{
		if (!$loginId && !userId)
		{
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, 'userId');
		}
		
		$user = null;
		try
		{
			if ($loginId)
			{
				$loginData = UserLoginDataPeer::getByEmail($loginId);
				if (!$loginData) {
					throw new KalturaAPIException(KalturaErrors::USER_NOT_FOUND);
				}
				$user = kuserPeer::getByLoginDataAndPartner($loginData->getId(), $this->getPartnerId());
			}
			else
			{
				$user = kuserPeer::getKuserByPartnerAndUid($this->getPArtnerId(), $userId);
			}
			
			if (!$user)
			{
				throw new KalturaAPIException(KalturaErrors::USER_NOT_FOUND);
			}
			
			$user->disableLogin();
		}
		catch (Exception $e)
		{
			$code = $e->getCode();
			if ($code == kUserException::USER_LOGIN_ALREADY_DISABLED) {
				throw new KalturaAPIException(KalturaErrors::USER_LOGIN_ALREADY_DISABLED);
			}
			throw $e;
		}
		
		$apiUser = new KalturaUser();
		$apiUser->fromObject($user);
		return $apiUser;
	}

}