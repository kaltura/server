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
	
	function addUserImpl(KalturaUser $user)
	{
		/* @var $dbUser kuser */
		$dbUser = $user->toInsertableObject();
		$dbUser->setPartnerId($this->getPartnerId());
		try {
			$checkPasswordStructure = isset($user->password) ? true : false;
			$dbUser = kuserPeer::addUser($dbUser, $user->password, $checkPasswordStructure);
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
				throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, $user->getFormattedPropertyNameWithClassName('id'));
			}
			else if ($code == kUserException::INVALID_EMAIL) {
				throw new KalturaAPIException(KalturaErrors::INVALID_FIELD_VALUE, 'email');
			}
			else if ($code == kUserException::INVALID_PARTNER) {
				throw new KalturaAPIException(KalturaErrors::UNKNOWN_PARTNER_ID);
			}
			else if ($code == kUserException::ADMIN_LOGIN_USERS_QUOTA_EXCEEDED) {
				throw new KalturaAPIException(KalturaErrors::ADMIN_LOGIN_USERS_QUOTA_EXCEEDED);
			}
			else if ($code == kUserException::PASSWORD_STRUCTURE_INVALID) {
				$partner = $dbUser->getPartner();
				$invalidPasswordStructureMessage='';
				if($partner && $partner->getInvalidPasswordStructureMessage())
					$invalidPasswordStructureMessage = $partner->getInvalidPasswordStructureMessage();
				throw new KalturaAPIException(KalturaErrors::PASSWORD_STRUCTURE_INVALID,$invalidPasswordStructureMessage);
			}
			throw $e;			
		}
		catch (kPermissionException $e)
		{
			$code = $e->getCode();
			if ($code == kPermissionException::ROLE_ID_MISSING) {
				throw new KalturaAPIException(KalturaErrors::ROLE_ID_MISSING);
			}
			if ($code == kPermissionException::ONLY_ONE_ROLE_PER_USER_ALLOWED) {
				throw new KalturaAPIException(KalturaErrors::ONLY_ONE_ROLE_PER_USER_ALLOWED);
			}
			else if ($code == kPermissionException::USER_ROLE_NOT_FOUND) {
				throw new KalturaAPIException(KalturaErrors::USER_ROLE_NOT_FOUND);
			}
			throw $e;
		}

		$newUser = new KalturaUser();
		$newUser->fromObject($dbUser, $this->getResponseProfile());
		
		return $newUser;
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
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $userId);

		if ($dbUser->getIsAdmin() && !is_null($user->isAdmin) && !$user->isAdmin) {
			throw new KalturaAPIException(KalturaErrors::CANNOT_SET_ROOT_ADMIN_AS_NO_ADMIN);
		}
			
		// update user
		try
		{
			if (!is_null($user->roleIds)) {
				UserRolePeer::testValidRolesForUser($user->roleIds, $this->getPartnerId());
				if ($user->roleIds != $dbUser->getRoleIds() &&
					$dbUser->getId() == $this->getKuser()->getId()) {
					throw new KalturaAPIException(KalturaErrors::CANNOT_CHANGE_OWN_ROLE);
				}
			}
			if (!is_null($user->id) && $user->id != $userId) {
				if(!preg_match(kuser::PUSER_ID_REGEXP, $user->id)) {
					throw new KalturaAPIException(KalturaErrors::INVALID_FIELD_VALUE, 'id');
				} 
				
				$existingUser = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $user->id);
				if ($existingUser) {
					throw new KalturaAPIException(KalturaErrors::DUPLICATE_USER_BY_ID, $user->id);
				}
			}			
			$dbUser = $user->toUpdatableObject($dbUser);
			$dbUser->save();
		}
		catch (kPermissionException $e)
		{
			$code = $e->getCode();
			if ($code == kPermissionException::ROLE_ID_MISSING) {
				throw new KalturaAPIException(KalturaErrors::ROLE_ID_MISSING);
			}
			if ($code == kPermissionException::ONLY_ONE_ROLE_PER_USER_ALLOWED) {
				throw new KalturaAPIException(KalturaErrors::ONLY_ONE_ROLE_PER_USER_ALLOWED);
			}
			if ($code == kPermissionException::USER_ROLE_NOT_FOUND) {
				throw new KalturaAPIException(KalturaErrors::USER_ROLE_NOT_FOUND);
			}
			if ($code == kPermissionException::ACCOUNT_OWNER_NEEDS_PARTNER_ADMIN_ROLE) {
				throw new KalturaAPIException(KalturaErrors::ACCOUNT_OWNER_NEEDS_PARTNER_ADMIN_ROLE);
			}
			throw $e;
		}
		catch (kUserException $e) {
			$code = $e->getCode();
			if ($code == kUserException::CANNOT_DELETE_OR_BLOCK_ROOT_ADMIN_USER) {
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
	 *
	 * @throws KalturaErrors::USER_NOT_FOUND
	 * @throws KalturaErrors::USER_WRONG_PASSWORD
	 * @throws KalturaErrors::INVALID_PARTNER_ID
	 * @throws KalturaErrors::LOGIN_RETRIES_EXCEEDED
	 * @throws KalturaErrors::LOGIN_BLOCKED
	 * @throws KalturaErrors::PASSWORD_EXPIRED
	 * @throws KalturaErrors::USER_IS_BLOCKED
	 */		
	public function loginByLoginIdAction($loginId, $password, $partnerId = null, $expiry = 86400, $privileges = '*', $otp = null)
	{
		// exceptions might be thrown
		return parent::loginImpl(null, $loginId, $password, $partnerId, $expiry, $privileges, $otp);
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
	 * @ksIgnored
	 *
	 * @throws KalturaErrors::INVALID_FIELD_VALUE
	 * @throws KalturaErrors::LOGIN_DATA_NOT_FOUND
	 * @throws KalturaErrors::WRONG_OLD_PASSWORD
	 * @throws KalturaErrors::PASSWORD_STRUCTURE_INVALID
	 * @throws KalturaErrors::PASSWORD_ALREADY_USED
	 * @throws KalturaErrors::LOGIN_ID_ALREADY_USED
	 */
	public function updateLoginDataAction( $oldLoginId , $password , $newLoginId = "" , $newPassword = "", $newFirstName = null, $newLastName = null)
	{	
		return parent::updateLoginDataImpl($oldLoginId , $password , $newLoginId, $newPassword, $newFirstName, $newLastName);
	}
	
	/**
	 * Reset user's password and send the user an email to generate a new one.
	 * 
	 * @action resetPassword
	 * 
	 * @param string $email The user's email address (login email)
	 * @ksIgnored
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
	 * @param string $hashKey The hash key used to identify the user (retrieved by email)
	 * @param string $newPassword The new password to set for the user
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
	 * Enables a user to log into a partner account using an email address and a password
	 * 
	 * @action enableLogin
	 * 
	 * @param string $userId The user's unique identifier in the partner's system
	 * @param string $loginId The user's email address that identifies the user for login
	 * @param string $password The user's password
	 * @return KalturaUser The user object represented by the user and login IDs
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
				throw new KalturaAPIException(KalturaErrors::LOGIN_ID_ALREADY_USED);
			}
			else if ($code == kUserException::ADMIN_LOGIN_USERS_QUOTA_EXCEEDED) {
				throw new KalturaAPIException(KalturaErrors::ADMIN_LOGIN_USERS_QUOTA_EXCEEDED);
			}
			throw $e;
		}
		
		$apiUser = new KalturaUser();
		$apiUser->fromObject($user, $this->getResponseProfile());
		return $apiUser;
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
	 * Loges a user to the destination account as long the ks user id exists in the desc acount and the loginData id match for both accounts
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
	 * add batch job that sends an email with a link to download an updated CSV that contains list of users
	 *
	 * @action exportToCsv
	 * @param KalturaUserFilter $filter A filter used to exclude specific types of users
	 * @param int $metadataProfileId
	 * @param KalturaCsvAdditionalFieldInfoArray $additionalFields
	 * @return string
	 *
	 * @throws APIErrors::USER_EMAIL_NOT_FOUND
	 * @throws MetadataErrors::INVALID_METADATA_PROFILE
	 * @throws MetadataErrors::METADATA_PROFILE_NOT_SPECIFIED
	 */
	function exportToCsvAction(KalturaUserFilter $filter = null, $metadataProfileId = null, $additionalFields = null)
	{
		if($metadataProfileId)
		{
			$metadataProfile = MetadataProfilePeer::retrieveByPK($metadataProfileId);
			if (!$metadataProfile || ($metadataProfile->getPartnerId() != $this->getPartnerId()))
				throw new KalturaAPIException(MetadataErrors::INVALID_METADATA_PROFILE, $metadataProfileId);
		}
		else
		{
			if($additionalFields->count)
				throw new KalturaAPIException(MetadataErrors::METADATA_PROFILE_NOT_SPECIFIED, $metadataProfileId);
		}

		if (!$filter)
			$filter = new KalturaUserFilter();
		$dbFilter = new kuserFilter();
		$filter->toObject($dbFilter);

		$kuser = $this->getKuser();
		if(!$kuser || !$kuser->getEmail())
			throw new KalturaAPIException(APIErrors::USER_EMAIL_NOT_FOUND, $kuser);

		kJobsManager::addUsersCsvJob($this->getPartnerId(), $dbFilter, $metadataProfileId, $additionalFields, $kuser);

		return $kuser->getEmail();
	}


	/**
	 *
	 * Will serve a requested csv
	 * @action serveCsv
	 *
	 *
	 * @param string $id - the requested file id
	 * @return string
	 */
	public function serveCsvAction($id)
	{
		if(!preg_match('/^\w+\.csv$/', $id))
			throw new KalturaAPIException(KalturaErrors::INVALID_ID, $id);

		// KS verification - we accept either admin session or download privilege of the file
		$ks = $this->getKs();
		if(!$ks->verifyPrivileges(ks::PRIVILEGE_DOWNLOAD, $id))
			KExternalErrors::dieError(KExternalErrors::ACCESS_CONTROL_RESTRICTED);

		$partner_id = $this->getPartnerId();
		$folderPath = "/content/userscsv/$partner_id";
		$fullPath = myContentStorage::getFSContentRootPath() . $folderPath;
		$file_path = "$fullPath/$id";

		return $this->dumpFile($file_path, 'text/csv');
	}

}
