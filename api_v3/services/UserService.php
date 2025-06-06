<?php
/**
 * Manage partner users on Kaltura's side
 * The userId in kaltura is the unique ID in the partner's system, and the [partnerId,Id] couple are unique key in kaltura's DB
 *
 * @service user
 * @package api
 * @subpackage services
 */
class UserService extends KalturaBaseUserService 
{

	/**
	 * Adds a new user to an existing account in the Kaltura database.
	 * Input param $id is the unique identifier in the partner's system.
	 *
	 * @action add
	 * @param KalturaUser $user The new user
	 * @return KalturaUser The new user
	 *
	 * @throws KalturaErrors::DUPLICATE_USER_BY_ID
	 * @throws KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL
	 * @throws KalturaErrors::INVALID_FIELD_VALUE
	 * @throws KalturaErrors::UNKNOWN_PARTNER_ID
	 * @throws KalturaErrors::ADMIN_LOGIN_USERS_QUOTA_EXCEEDED
	 * @throws KalturaErrors::PASSWORD_STRUCTURE_INVALID
	 * @throws KalturaErrors::DUPLICATE_USER_BY_LOGIN_ID
	 * @throws KalturaErrors::USER_ROLE_NOT_FOUND
	 */
	function addAction(KalturaUser $user)
	{
		if(isset($user->id) && isset($user->externalId) && trim($user->externalId) && trim($user->id))
		{
			throw new KalturaAPIException(KalturaErrors::ID_AND_EXTERNAL_ID_ARE_MUTUALLY_EXCLUSIVE);
		}
		
		if($user->externalId)
		{
			$user->id = $user->externalId;
		}
		
		if (!preg_match(kuser::PUSER_ID_REGEXP, $user->id))
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_FIELD_VALUE, 'id');
		}

		if ($user instanceof KalturaAdminUser)
		{
			$user->isAdmin = true;
		}

		$lockKey = "user_add_" . $this->getPartnerId() . $user->id;
		return kLock::runLocked($lockKey, array($this, 'adduserImpl'), array($user));
	}

	/**
	 * Updates an existing user object.
	 * You can also use this action to update the userId.
	 * 
	 * @action update
	 * @param string $userId The user's unique identifier in the partner's system
	 * @param KalturaUser $user The user parameters to update
	 * @return KalturaUser The updated user object
	 *
	 * @throws KalturaErrors::INVALID_USER_ID
	 * @throws KalturaErrors::CANNOT_DELETE_OR_BLOCK_ROOT_ADMIN_USER
	 * @throws KalturaErrors::USER_ROLE_NOT_FOUND
	 * @throws KalturaErrors::ACCOUNT_OWNER_NEEDS_PARTNER_ADMIN_ROLE
	 */
	public function updateAction($userId, KalturaUser $user)
	{		
		$dbUser = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $userId);
		
		if (!$dbUser)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $userId);
		}

		$partner = $dbUser->getPartner();
		if ($dbUser->getIsAdmin() && !is_null($user->isAdmin) && !$user->isAdmin)
		{
			throw new KalturaAPIException(KalturaErrors::CANNOT_SET_ROOT_ADMIN_AS_NO_ADMIN);
		}

		if ($dbUser->getIsAdmin() && !is_null($user->email))
		{
			$allowedEmailDomainsForAdmins = $partner->getAllowedEmailDomainsForAdmins();
			if ($allowedEmailDomainsForAdmins)
			{
				$allowedEmailDomainsForAdminsArray = explode(',', $partner->getAllowedEmailDomainsForAdmins());
				if (!kString::isEmailString($user->email))
				{
					throw new KalturaAPIException(KalturaErrors::INVALID_FIELD_VALUE, 'email');
				}
				if (!myKuserUtils::isAllowedAdminEmailDomain($user->email, $allowedEmailDomainsForAdminsArray))
				{
					throw new KalturaAPIException(KalturaErrors::EMAIL_DOMAIN_IS_NOT_ALLOWED_FOR_ADMINS);
				}
			}
		}
		
		if($dbUser->getIsHashed() && $user->id)
		{
			throw new KalturaAPIException(KalturaErrors::UPDATING_USER_ID_FOR_HASHED_USER_NOT_ALLOWED);
		}
		
		if ($partner->getUseSso() && !PermissionPeer::isValidForPartner(PermissionName::ALLOW_SSO_PER_USER, $this->getPartnerId())
			&& $user->isSsoExcluded)
		{
			throw new KalturaAPIException(KalturaErrors::SETTING_SSO_PER_USER_NOT_ALLOWED);
		}

		// update user
		try
		{
			if (!is_null($user->roleIds))
			{
				if ($this->getPartnerId() == Partner::ADMIN_CONSOLE_PARTNER_ID && !kPermissionManager::isPermitted(PermissionName::SYSTEM_ADMIN_PERMISSIONS_UPDATE))
				{
					throw new KalturaAPIException(KalturaErrors::NOT_ALLOWED_TO_CHANGE_ROLE);
				}
				
				UserRolePeer::testValidRolesForUser($user->roleIds, $this->getPartnerId());
				if ($user->roleIds != $dbUser->getRoleIds() && $dbUser->getId() == $this->getKuser()->getId())
				{
					throw new KalturaAPIException(KalturaErrors::CANNOT_CHANGE_OWN_ROLE);
				}
			}
			if (!is_null($user->id) && $user->id != $userId)
			{
				if(!preg_match(kuser::PUSER_ID_REGEXP, $user->id))
				{
					throw new KalturaAPIException(KalturaErrors::INVALID_FIELD_VALUE, 'id');
				} 
				
				$existingUser = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $user->id);
				if ($existingUser)
				{
					throw new KalturaAPIException(KalturaErrors::DUPLICATE_USER_BY_ID, $user->id);
				}
			}
			$dbUser = $user->toUpdatableObject($dbUser);
			$dbUser->save();
		}
		catch (kPermissionException $e)
		{
			$code = $e->getCode();
			if ($code == kPermissionException::ROLE_ID_MISSING)
			{
				throw new KalturaAPIException(KalturaErrors::ROLE_ID_MISSING);
			}
			if ($code == kPermissionException::ONLY_ONE_ROLE_PER_USER_ALLOWED)
			{
				throw new KalturaAPIException(KalturaErrors::ONLY_ONE_ROLE_PER_USER_ALLOWED);
			}
			if ($code == kPermissionException::USER_ROLE_NOT_FOUND)
			{
				throw new KalturaAPIException(KalturaErrors::USER_ROLE_NOT_FOUND);
			}
			if ($code == kPermissionException::ACCOUNT_OWNER_NEEDS_PARTNER_ADMIN_ROLE)
			{
				throw new KalturaAPIException(KalturaErrors::ACCOUNT_OWNER_NEEDS_PARTNER_ADMIN_ROLE);
			}
			throw $e;
		}
		catch (kUserException $e)
		{
			$code = $e->getCode();
			if ($code == kUserException::CANNOT_DELETE_OR_BLOCK_ROOT_ADMIN_USER)
			{
				throw new KalturaAPIException(KalturaErrors::CANNOT_DELETE_OR_BLOCK_ROOT_ADMIN_USER);
			}
			throw $e;			
		}
				
		$user = new KalturaUser();
		$user->fromObject($dbUser, $this->getResponseProfile());
		
		return $user;
	}

	
	/**
	 * Retrieves a user object for a specified user ID.
	 * 
	 * @action get
	 * @param string $userId The user's unique identifier in the partner's system
	 * @return KalturaUser The specified user object
	 *
	 * @throws KalturaErrors::INVALID_USER_ID
	 */		
	public function getAction($userId = null)
	{
	    if (is_null($userId) || $userId == '')
	    {
            $userId = kCurrentContext::$ks_uid;	        
	    }

		if (!kCurrentContext::$is_admin_session && kCurrentContext::$ks_uid != $userId)
			throw new KalturaAPIException(KalturaErrors::CANNOT_RETRIEVE_ANOTHER_USER_USING_NON_ADMIN_SESSION, $userId);

		$dbUser = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $userId);
	
		if (!$dbUser)
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $userId);

		$user = new KalturaUser();
		$user->fromObject($dbUser, $this->getResponseProfile());
		
		return $user;
	}
	
	/**
	 * Retrieves a user object for a user's login ID and partner ID.
	 * A login ID is the email address used by a user to log into the system.
	 * 
	 * @action getByLoginId
	 * @param string $loginId The user's email address that identifies the user for login
	 * @return KalturaUser The user object represented by the login and partner IDs
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

		// users that are not publisher administrator are only allowed to get their own object   
		if ($kuser->getId() != kCurrentContext::getCurrentKsKuserId() && !in_array(PermissionName::MANAGE_ADMIN_USERS, kPermissionManager::getCurrentPermissions()))
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $loginId);
		
		$user = new KalturaUser();
		$user->fromObject($kuser, $this->getResponseProfile());
		
		return $user;
	}

	/**
	 * Deletes a user from a partner account.
	 * 
	 * @action delete
	 * @param string $userId The user's unique identifier in the partner's system
	 * @return KalturaUser The deleted user object
	 *
	 * @throws KalturaErrors::INVALID_USER_ID
	 */		
	public function deleteAction($userId)
	{
		$dbUser = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $userId);
	
		if (!$dbUser) {
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $userId);
		}
					
		try {
			$dbUser->setStatus(KalturaUserStatus::DELETED);
		}
		catch (kUserException $e) {
			$code = $e->getCode();
			if ($code == kUserException::CANNOT_DELETE_OR_BLOCK_ROOT_ADMIN_USER) {
				throw new KalturaAPIException(KalturaErrors::CANNOT_DELETE_OR_BLOCK_ROOT_ADMIN_USER);
			}
			throw $e;			
		}
		$dbUser->save();
		
		$user = new KalturaUser();
		$user->fromObject($dbUser, $this->getResponseProfile());
		
		return $user;
	}
	
	/**
	 * Lists user objects that are associated with an account.
	 * Blocked users are listed unless you use a filter to exclude them.
	 * Deleted users are not listed unless you use a filter to include them.
	 * 
	 * @action list
	 * @param KalturaUserFilter $filter A filter used to exclude specific types of users
	 * @param KalturaFilterPager $pager A limit for the number of records to display on a page
	 * @return KalturaUserListResponse The list of user objects
	 */
	public function listAction(KalturaUserFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaUserFilter();
			
		if(!$pager)
			$pager = new KalturaFilterPager();
			
		return $filter->getListResponse($pager, $this->getResponseProfile());
	}
	
	/**
	 * Notifies that a user is banned from an account.
	 * 
	 * @action notifyBan
	 * @param string $userId The user's unique identifier in the partner's system
	 *
	 * @throws KalturaErrors::INVALID_USER_ID
	 */		
	public function notifyBan($userId)
	{
		$dbUser = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $userId);
		if (!$dbUser)
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $userId);
		
		myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_USER_BANNED, $dbUser);
	}

	/**
	 * Logs a user into a partner account with a partner ID, a partner user ID (puser), and a user password.
	 * 
	 * @action login
	 * @param int $partnerId The identifier of the partner account
	 * @param string $userId The user's unique identifier in the partner's system
	 * @param string $password The user's password
	 * @param int $expiry The requested time (in seconds) before the generated KS expires (By default, a KS expires after 24 hours).
	 * @param string $privileges Special privileges
	 * @return string A session KS for the user
	 * @ksIgnored
	 * @maskedParams userId,password
	 *
	 * @throws KalturaErrors::USER_NOT_FOUND
	 * @throws KalturaErrors::USER_WRONG_PASSWORD
	 * @throws KalturaErrors::INVALID_PARTNER_ID
	 * @throws KalturaErrors::LOGIN_RETRIES_EXCEEDED
	 * @throws KalturaErrors::LOGIN_BLOCKED
	 * @throws KalturaErrors::PASSWORD_EXPIRED
	 * @throws KalturaErrors::USER_IS_BLOCKED
	 */		
	public function loginAction($partnerId, $userId, $password, $expiry = 86400, $privileges = '*')
	{
		// exceptions might be thrown
		return parent::loginImpl($userId, null, $password, $partnerId, $expiry, $privileges);
	}
	
	/**
	 * Logs a user into a partner account with a user login ID and a user password.
	 * 
	 * @action loginByLoginId
	 * 
	 * @param string $loginId The user's email address that identifies the user for login
	 * @param string $password The user's password
	 * @param int $partnerId The identifier of the partner account
	 * @param int $expiry The requested time (in seconds) before the generated KS expires (By default, a KS expires after 24 hours).
	 * @param string $privileges Special privileges
	 * @param string $otp the user's one-time password
	 * @return string A session KS for the user
	 * @ksIgnored
	 * @maskedParams loginId,password
	 *
	 * @throws KalturaErrors::USER_NOT_FOUND
	 * @throws KalturaErrors::USER_WRONG_PASSWORD
	 * @throws KalturaErrors::INVALID_PARTNER_ID
	 * @throws KalturaErrors::LOGIN_RETRIES_EXCEEDED
	 * @throws KalturaErrors::LOGIN_BLOCKED
	 * @throws KalturaErrors::PASSWORD_EXPIRED
	 * @throws KalturaErrors::USER_IS_BLOCKED
	 * @throws KalturaErrors::DIRECT_LOGIN_BLOCKED
	 */		
	public function loginByLoginIdAction($loginId, $password, $partnerId = null, $expiry = 86400, $privileges = '*', $otp = null)
	{
		// exceptions might be thrown
		return parent::loginImpl(null, $loginId, $password, $partnerId, $expiry, $privileges, $otp);
	}

	protected static function validateLoginDataParams($paramsArray)
	{
		kCurrentContext::$HTMLPurifierBehaviour = HTMLPurifierBehaviourType::BLOCK;
		foreach ($paramsArray as $paramName => $paramValue)
		{
			try
			{
				kHtmlPurifier::purify('kuser', $paramName, $paramValue);
			}
			catch (Exception $e)
			{
				throw new KalturaAPIException(KalturaErrors::UNSAFE_HTML_TAGS, 'UserLoginData', $paramName);
			}
		}
	}


	/**
	 * Updates a user's login data: email, password, name.
	 * 
	 * @action updateLoginData
	 * 
	 * @param string $oldLoginId The user's current email address that identified the user for login
	 * @param string $password The user's current email address that identified the user for login
	 * @param string $newLoginId Optional, The user's email address that will identify the user for login
	 * @param string $newPassword Optional, The user's new password
	 * @param string $newFirstName Optional, The user's new first name
	 * @param string $newLastName Optional, The user's new last name
	 * @param string $otp the user's one-time password
	 * @ksIgnored
	 * @maskedParams oldLoginId,password,newLoginId,newPassword,newFirstName,newLastName
	 *
	 * @throws KalturaErrors::INVALID_FIELD_VALUE
	 * @throws KalturaErrors::PASSWORD_STRUCTURE_INVALID
	 * @throws KalturaErrors::PASSWORD_ALREADY_USED
	 * @throws KalturaErrors::LOGIN_ID_ALREADY_USED
	 * @throws KalturaErrors::ADMIN_KUSER_NOT_FOUND
	 * @throws APIErrors::LOGIN_RETRIES_EXCEEDED
	 * @throws APIErrors::LOGIN_BLOCKED
	 */
	public function updateLoginDataAction( $oldLoginId , $password , $newLoginId = "" , $newPassword = "", $newFirstName = null, $newLastName = null, $otp = null)
	{
		self::validateLoginDataParams(array('id' => $newLoginId,
										'firstName' => $newFirstName,
										'lastName' => $newLastName));

		try
		{
			$updateLoginData = parent::updateLoginDataImpl($oldLoginId , $password , $newLoginId, $newPassword, $newFirstName, $newLastName, $otp);
		}
		catch(KalturaAPIException $e)
		{
			$error = $e->getCode().';;'.$e->getMessage();
			if ($error == KalturaErrors::LOGIN_DATA_NOT_FOUND ||
				$error == KalturaErrors::USER_WRONG_PASSWORD ||
				$error == KalturaErrors::WRONG_OLD_PASSWORD ||
				$error == KalturaErrors::INVALID_OTP ||
				$error == KalturaErrors::MISSING_OTP)
			{
				throw new KalturaAPIException(KalturaErrors::USER_DATA_ERROR);
			}
			throw $e;
		}
		return $updateLoginData;
	}
	
	/**
	 * Resets user login password
	 * @action loginDataResetPassword
	 * @param string $loginDataId The user's current email address that identified the user for login
	 * @param string $newPassword The user's new password
	 * @return KalturaUser The user object represented by the user and login IDs
	 * @throws KalturaErrors::INVALID_FIELD_VALUE
	 * @throws KalturaErrors::PASSWORD_STRUCTURE_INVALID
	 * @throws KalturaErrors::PASSWORD_ALREADY_USED
	 * @throws KalturaErrors::LOGIN_ID_ALREADY_USED
	 * @throws KalturaErrors::ADMIN_KUSER_NOT_FOUND
	 * @throws APIErrors::LOGIN_RETRIES_EXCEEDED
	 * @throws APIErrors::LOGIN_BLOCKED
	 */
	public function loginDataResetPasswordAction($loginDataId, $newPassword)
	{
		self::validateLoginDataParams(array('id' => $loginDataId));

		$loginData = UserLoginDataPeer::getByEmail($loginDataId);
		if(!$loginData)
		{
			throw new KalturaAPIException(APIErrors::LOGIN_DATA_NOT_FOUND);
		}

		$user = kuserPeer::getByLoginDataAndPartner($loginData->getId(), kCurrentContext::$ks_partner_id);
		if(!$user)
		{
			throw new KalturaAPIException(KalturaErrors::USER_NOT_FOUND);
		}

		try
		{
			parent::updateLoginDataImpl($loginDataId , null , null, $newPassword, null, null, null, true);
		}
		catch(KalturaAPIException $e)
		{
			$error = $e->getCode().';;'.$e->getMessage();
			if ($error == KalturaErrors::LOGIN_DATA_NOT_FOUND ||
				$error == KalturaErrors::USER_WRONG_PASSWORD ||
				$error == KalturaErrors::WRONG_OLD_PASSWORD ||
				$error == KalturaErrors::INVALID_OTP ||
				$error == KalturaErrors::MISSING_OTP)
			{
				throw new KalturaAPIException(KalturaErrors::USER_DATA_ERROR);
			}
			throw $e;
		}
		return $this->getResponseUserWithEncryptedSeed($user);
	}
	
	/**
	 * Reset user's password and send the user an email to generate a new one.
	 * 
	 * @action resetPassword
	 * 
	 * @param string $email The user's email address (login email)
	 * @param KalturaResetPassLinkType $linkType kmc or kms
	 * @ksIgnored
	 *
	 * @throws KalturaErrors::LOGIN_DATA_NOT_FOUND
	 * @throws KalturaErrors::PASSWORD_STRUCTURE_INVALID
	 * @throws KalturaErrors::PASSWORD_ALREADY_USED
	 * @throws KalturaErrors::INVALID_FIELD_VALUE
	 * @throws KalturaErrors::LOGIN_ID_ALREADY_USED
	 */	
	public function resetPasswordAction($email, $linkType = KalturaResetPassLinkType::KMC)
	{
		return parent::resetPasswordImpl($email, $linkType);
	}
	
	/**
	 * Set initial user password
	 * 
	 * @action setInitialPassword
	 * 
	 * @param string $hashKey The hash key used to identify the user (retrieved by email)
	 * @param string $newPassword The new password to set for the user
	 * @return KalturaAuthentication The authentication response
	 * @ksIgnored
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
	 * Validate hash key
	 *
	 * @action validateHashKey
	 *
	 * @param string $hashKey The hash key used to identify the user (retrieved by email)
	 * @return KalturaAuthentication The authentication response
	 *
	 * @throws KalturaErrors::LOGIN_DATA_NOT_FOUND
	 * @throws KalturaErrors::NEW_PASSWORD_HASH_KEY_INVALID
	 * @throws KalturaErrors::NEW_PASSWORD_HASH_KEY_EXPIRED
	 * @throws KalturaErrors::INVALID_ACCESS_TO_PARTNER_SPECIFIC_SEARCH
	 * @throws KalturaErrors::INTERNAL_SERVERL_ERROR
	 */
	public function validateHashKeyAction($hashKey)
	{
		KalturaResponseCacher::disableCache();

		try
		{
			$loginData = UserLoginDataPeer::isHashKeyValid($hashKey);
		}
		catch (kUserException $e)
		{
			switch($e->getCode())
			{
				case kUserException::LOGIN_DATA_NOT_FOUND:
					throw new KalturaAPIException(KalturaErrors::LOGIN_DATA_NOT_FOUND);

				case kUserException::NEW_PASSWORD_HASH_KEY_INVALID:
					throw new KalturaAPIException(KalturaErrors::NEW_PASSWORD_HASH_KEY_INVALID);

				case kUserException::NEW_PASSWORD_HASH_KEY_EXPIRED:
					throw new KalturaAPIException(KalturaErrors::NEW_PASSWORD_HASH_KEY_EXPIRED);

				default:
					throw $e;
			}
		}
		
		if ($this->getKs() && $this->getKs()->partner_id != $loginData->getConfigPartnerId())
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_ACCESS_TO_PARTNER_SPECIFIC_SEARCH, $this->getKs()->partner_id);
		}

		return new KalturaAuthentication();
	}

	/**
	 * Enables a user to log into a partner account using an email address and a password
	 * 
	 * @action enableLogin
	 * 
	 * @param string $userId The user's unique identifier in the partner's system
	 * @param string $loginId The user's email address that identifies the user for login
	 * @param string $password The user's password
	 * @return KalturaUser The user object represented by the user and login IDs
	 * @maskedParams userId,loginId,password
	 *
	 * @throws KalturaErrors::USER_LOGIN_ALREADY_ENABLED
	 * @throws KalturaErrors::USER_NOT_FOUND
	 * @throws KalturaErrors::ADMIN_LOGIN_USERS_QUOTA_EXCEEDED
	 * @throws KalturaErrors::PASSWORD_STRUCTURE_INVALID
	 * @throws KalturaErrors::LOGIN_ID_ALREADY_USED
	 *
	 */	
	public function enableLoginAction($userId, $loginId, $password = null)
	{		
		try
		{
			$user = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $userId);
			
			if (!$user)
			{
				throw new KalturaAPIException(KalturaErrors::USER_NOT_FOUND);
			}
			
			if (!$user->getIsAdmin() && !$password) {
				throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, 'password');
			}
			
			// Gonen 2011-05-29 : NOTE - 3rd party uses this action and expect that email notification will not be sent by default
			// if this call ever changes make sure you do not change default so mails are sent.
			$user->enableLogin($loginId, $password, true);	
			$user->save();
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
			else if ($code == kUserException::ADMIN_LOGIN_USERS_QUOTA_EXCEEDED) {
				throw new KalturaAPIException(KalturaErrors::ADMIN_LOGIN_USERS_QUOTA_EXCEEDED);
			}
			else if ($code == kUserException::PASSWORD_STRUCTURE_INVALID) {
				throw new KalturaAPIException(KalturaErrors::PASSWORD_STRUCTURE_INVALID);
			}
			else if ($code == kUserException::LOGIN_ID_ALREADY_USED) {
				throw new KalturaAPIException(KalturaErrors::USER_DATA_ERROR);
			}
			else if ($code == kUserException::ADMIN_LOGIN_USERS_QUOTA_EXCEEDED) {
				throw new KalturaAPIException(KalturaErrors::ADMIN_LOGIN_USERS_QUOTA_EXCEEDED);
			}
			throw $e;
		}

		return $this->getResponseUserWithEncryptedSeed($user);
	}

	/**
	 * Replace a user's existing login data to a new or an existing login data
	 * to only be used when admin impersonates a partner
	 *
	 * @action replaceUserLoginData
	 *
	 * @param string $userId The user's unique identifier in the partner's system
	 * @param string $newLoginId The new user's email address that identifies the user for login
	 * @param string $existingLoginId The user's email address that identifies the user for login
	 * @return KalturaUser The user object represented by the user and login IDs
	 *
	 * @throws KalturaErrors::USER_NOT_FOUND
	 * @throws KalturaErrors::ADMIN_LOGIN_USERS_QUOTA_EXCEEDED
	 * @throws KalturaErrors::LOGIN_ID_ALREADY_USED
	 * @throws KalturaErrors::USER_LOGIN_ALREADY_ENABLED
	 * @throws KalturaErrors::USER_DATA_ERROR
	 * @throws KalturaErrors::LOGIN_DATA_NOT_PROVIDED
	 * @throws KalturaErrors::LOGIN_DATA_MISMATCH
	 * @throws KalturaErrors::CANNOT_DISABLE_LOGIN_FOR_ADMIN_USER
	 * @throws KalturaErrors::USER_LOGIN_ALREADY_DISABLED
	 * @throws KalturaErrors::LOGIN_ID_ALREADY_USED
	 */
	public function replaceUserLoginDataAction($userId, $newLoginId, $existingLoginId = null)
	{
		try
		{
			if (kCurrentContext::$master_partner_id != Partner::EP_PARTNER_ID)
			{
				throw new KalturaAPIException(KalturaErrors::ACTION_FORBIDDEN, 'replaceUserLoginData');
			}

			$user = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $userId);
			if (!$user)
			{
				throw new KalturaAPIException(KalturaErrors::USER_NOT_FOUND);
			}

			$user->replaceUserLoginData($newLoginId, $existingLoginId);
			$user->save();
		}
		catch (Exception $e)
		{
			switch ($e->getCode())
			{
				case kUserException::LOGIN_DATA_NOT_PROVIDED:
				{
					throw new KalturaAPIException(KalturaErrors::LOGIN_DATA_NOT_PROVIDED);
				}
				case kUserException::USER_LOGIN_ALREADY_ENABLED:
				{
					throw new KalturaAPIException(KalturaErrors::USER_LOGIN_ALREADY_ENABLED);
				}
				case kUserException::INVALID_EMAIL:
				{
					throw new KalturaAPIException(KalturaErrors::USER_NOT_FOUND);
				}
				case kUserException::INVALID_PARTNER:
				{
					throw new KalturaAPIException(KalturaErrors::USER_NOT_FOUND);
				}
				case kUserException::ADMIN_LOGIN_USERS_QUOTA_EXCEEDED:
				{
					throw new KalturaAPIException(KalturaErrors::ADMIN_LOGIN_USERS_QUOTA_EXCEEDED);
				}
				case kUserException::PASSWORD_STRUCTURE_INVALID:
				{
					throw new KalturaAPIException(KalturaErrors::PASSWORD_STRUCTURE_INVALID);
				}
				case kUserException::LOGIN_ID_ALREADY_USED:
				{
					throw new KalturaAPIException(KalturaErrors::USER_DATA_ERROR);
				}
				case kUserException::LOGIN_DATA_MISMATCH:
				{
					throw new KalturaAPIException(KalturaErrors::LOGIN_DATA_MISMATCH);
				}
				case kUserException::USER_LOGIN_ALREADY_DISABLED:
				{
					throw new KalturaAPIException(KalturaErrors::USER_LOGIN_ALREADY_DISABLED);
				}
				case kUserException::CANNOT_DISABLE_LOGIN_FOR_ADMIN_USER:
				{
					throw new KalturaAPIException(KalturaErrors::CANNOT_DISABLE_LOGIN_FOR_ADMIN_USER);
				}
				default:
				{
					throw $e;
				}
			}
		}

		return $this->getResponseUserWithEncryptedSeed($user);
	}

	protected function getResponseUserWithEncryptedSeed($user)
	{
		$apiUser = new KalturaUser();
		$apiUser->fromObject($user, $this->getResponseProfile());
		if(!$user->getIsAdmin())
		{
			$apiUser->encryptedSeed = $this->getEncryptSeedBase64($user->getPartner()->getAdminSecret(), $user->getLoginData()->getSeedFor2FactorAuth());
		}
		return $apiUser;
	}
	
	protected function getEncryptSeedBase64($key, $message)
	{
		return base64_encode(OpenSSLWrapper::encrypt_aes($message, $key, ''));
	}
	
	
	
	/**
	 * Disables a user's ability to log into a partner account using an email address and a password.
	 * You may use either a userId or a loginId parameter for this action.
	 * 
	 * @action disableLogin
	 * 
	 * @param string $userId The user's unique identifier in the partner's system
	 * @param string $loginId The user's email address that identifies the user for login
	 * 
	 * @return KalturaUser The user object represented by the user and login IDs
	 * 
	 * @throws KalturaErrors::USER_LOGIN_ALREADY_DISABLED
	 * @throws KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL
	 * @throws KalturaErrors::USER_NOT_FOUND
	 * @throws KalturaErrors::CANNOT_DISABLE_LOGIN_FOR_ADMIN_USER
	 *
	 */	
	public function disableLoginAction($userId = null, $loginId = null)
	{
		if (!$loginId && !$userId)
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
			if ($code == kUserException::CANNOT_DISABLE_LOGIN_FOR_ADMIN_USER) {
				throw new KalturaAPIException(KalturaErrors::CANNOT_DISABLE_LOGIN_FOR_ADMIN_USER);
			}
			throw $e;
		}
		
		$apiUser = new KalturaUser();
		$apiUser->fromObject($user, $this->getResponseProfile());
		return $apiUser;
	}
	
	/**
	 * Index an entry by id.
	 * 
	 * @action index
	 * @param string $id
	 * @param bool $shouldUpdate
	 * @return string 
	 * @throws KalturaErrors::USER_NOT_FOUND
	 */
	function indexAction($id, $shouldUpdate = true)
	{
		$kuser = kuserPeer::getActiveKuserByPartnerAndUid(kCurrentContext::getCurrentPartnerId(), $id);
		
		if (!$kuser)
			throw new KalturaAPIException(KalturaErrors::USER_NOT_FOUND);
		
		$kuser->indexToSearchIndex();
			
		return $kuser->getPuserId();
	}
	
	/**
	 * Logs a user to the destination account provided the KS' user ID is associated with the destination account and the loginData ID matches
	 *
	 * @action loginByKs
	 * @param int $requestedPartnerId
	 * @throws APIErrors::PARTNER_CHANGE_ACCOUNT_DISABLED
	 *
	 * @return KalturaSessionResponse The generated session information
	 * 
	 * @throws KalturaErrors::INVALID_USER_ID
	 * @throws KalturaErrors::PARTNER_CHANGE_ACCOUNT_DISABLED
	 * @throws KalturaErrors::ADMIN_KUSER_NOT_FOUND
	 * @throws KalturaErrors::LOGIN_DATA_NOT_FOUND
	 * @throws KalturaErrors::LOGIN_BLOCKED
	 * @throws KalturaErrors::USER_IS_BLOCKED
	 * @throws KalturaErrors::INTERNAL_SERVERL_ERROR
	 * @throws KalturaErrors::UNKNOWN_PARTNER_ID
	 * @throws KalturaErrors::SERVICE_ACCESS_CONTROL_RESTRICTED
	 * @throws KalturaErrors::DIRECT_LOGIN_BLOCKED
	 * 
	 */
	public function loginByKsAction($requestedPartnerId)
	{
		$this->partnerGroup .= ",$requestedPartnerId";
		$this->applyPartnerFilterForClass('kuser');
		
		$ks = parent::loginByKsImpl($this->getKs()->getOriginalString(), $requestedPartnerId);
		
		$res = new KalturaSessionResponse();
		$res->ks = $ks;
		$res->userId = $this->getKuser()->getPuserId();
		$res->partnerId = $requestedPartnerId;
		
		return $res;
	}
	/**
	 *
	 * Will serve a requested CSV
	 * @action serveCsv
	 * @deprecated use exportCsv.serveCsv
	 *
	 * @param string $id - the requested file id
	 * @return string
	 */
	public function serveCsvAction($id)
	{
		$file_path = ExportCsvService::generateCsvPath($id, $this->getKs());
		return $this->dumpFile($file_path, 'text/csv');
	}

	/**
	 * get QR image content
	 *
	 * @action generateQrCode
	 * @param string $hashKey
	 * @return string
	 * @throws KalturaErrors::INVALID_HASH
	 * @throws KalturaErrors::INVALID_USER_ID
	 * @throws KalturaErrors::ERROR_IN_QR_GENERATION
	 *
	 */
	public function generateQrCodeAction($hashKey)
	{
		try
		{
			$loginData = UserLoginDataPeer::isHashKeyValid($hashKey);
			if ($loginData)
			{
				$this->validateApiAccessControl($loginData->getLastLoginPartnerId());
			}
			$dbUser = kuserPeer::getAdminUser($loginData->getConfigPartnerId(), $loginData);
		}
		catch (kUserException $e)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_HASH);
		}

		if (!$dbUser)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $loginData->getLoginEmail());
		}
		$imgContent = authenticationUtils::getQRImage($dbUser, $loginData);
		if(!$imgContent)
		{
			throw new KalturaAPIException(KalturaErrors::ERROR_IN_QR_GENERATION);
		}

		$loginData->setPasswordHashKey(null);
		$loginData->save();

		return $imgContent;
	}

	/**
	 * @action demoteAdmin
	 * @param string $userId
	 * @return KalturaUser
	 * @throws KalturaAPIException
	 * @throws PropelException
	 */
	public function demoteAdminAction($userId)
	{
		$dbUser = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $userId);
		if (!$dbUser)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $userId);
		}

		$status = $dbUser->getStatus();
		if (($status == KuserStatus::DELETED || $status == KuserStatus::BLOCKED))
		{
			throw new KalturaAPIException(KalturaErrors::USER_IS_BLOCKED);
		}

		if ($dbUser->getIsAccountOwner())
		{
			throw new KalturaAPIException(KalturaErrors::CANNOT_SET_ROOT_ADMIN_AS_NO_ADMIN);
		}

		if (!$dbUser->getIsAdmin())
		{
			throw new KalturaAPIException(KalturaErrors::DEMOTE_ONLY_ADMIN);
		}

		$dbUser->setIsAdmin(false);
		$dbUser->setRoleIds('');
		$dbUser->save();

		$user = new KalturaUser();
		$user->fromObject($dbUser, $this->getResponseProfile());

		return $user;
	}
}
