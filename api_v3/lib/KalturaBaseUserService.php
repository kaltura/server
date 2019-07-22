<?php
/**
 * @package api
 * @subpackage services
 */
class KalturaBaseUserService extends KalturaBaseService 
{
	
	protected function partnerRequired($actionName)
	{
		$actionName = strtolower($actionName);
		if ($actionName === 'loginbyloginid') {
			return false;
		}
		if ($actionName === 'updatelogindata') {
			return false;
		}
		if ($actionName === 'resetpassword') {
			return false;
		}
		if ($actionName === 'setinitialpassword') {
			return false;
		}
		return parent::partnerRequired($actionName);
	}
	
	
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService ($serviceId, $serviceName, $actionName);
		$this->applyPartnerFilterForClass('kuser');
	}	
	
	/**
	 * Update admin user password and email
	 * 
	 * @param string $email
	 * @param string $password
	 * @param string $newEmail Optional, provide only when you want to update the email
	 * @param string $newPassword
	 * @param string $newFirstName Optional, provide only when you want to update the first name
	 * @param string $newLastName Optional, provide only when you want to update the last name
	 * @param string $otp the user's one-time password
	 *
	 * @throws KalturaErrors::INVALID_FIELD_VALUE
	 * @throws KalturaErrors::LOGIN_DATA_NOT_FOUND
	 * @throws KalturaErrors::WRONG_OLD_PASSWORD
	 * @throws KalturaErrors::PASSWORD_STRUCTURE_INVALID
	 * @throws KalturaErrors::PASSWORD_ALREADY_USED
	 * @throws KalturaErrors::LOGIN_ID_ALREADY_USED
	 * @throws KalturaErrors::INVALID_OTP
	 * @throws KalturaErrors::MISSING_OTP
	 */
	protected function updateLoginDataImpl( $email , $password , $newEmail = "" , $newPassword = "", $newFirstName = null, $newLastName = null, $otp = null)
	{
		KalturaResponseCacher::disableCache();

		$this->validateApiAccessControlByEmail($email);
		
		if ($newEmail != "")
		{
			if(!kString::isEmailString($newEmail))
				throw new KalturaAPIException ( KalturaErrors::INVALID_FIELD_VALUE, "newEmail" );
		}

		try {
			UserLoginDataPeer::updateLoginData ( $email , $password, $newEmail, $newPassword, $newFirstName, $newLastName, $otp);
		}
		catch (kUserException $e) {
			$code = $e->getCode();
			if ($code == kUserException::LOGIN_DATA_NOT_FOUND) {
				throw new KalturaAPIException(KalturaErrors::LOGIN_DATA_NOT_FOUND);
			}
			else if ($code == kUserException::WRONG_PASSWORD) {
				if($password == $newPassword)
					throw new KalturaAPIException(KalturaErrors::USER_WRONG_PASSWORD);
				else
					throw new KalturaAPIException(KalturaErrors::WRONG_OLD_PASSWORD);
			}
			else if ($code == kUserException::PASSWORD_STRUCTURE_INVALID) {
				$c = new Criteria(); 
				$c->add(UserLoginDataPeer::LOGIN_EMAIL, $email ); 
				$loginData = UserLoginDataPeer::doSelectOne($c);
				$invalidPasswordStructureMessage = $loginData->getInvalidPasswordStructureMessage();
				throw new KalturaAPIException(KalturaErrors::PASSWORD_STRUCTURE_INVALID,$invalidPasswordStructureMessage);
			}
			else if ($code == kUserException::PASSWORD_ALREADY_USED) {
				throw new KalturaAPIException(KalturaErrors::PASSWORD_ALREADY_USED);
			}
			else if ($code == kUserException::INVALID_EMAIL) {
				throw new KalturaAPIException(KalturaErrors::INVALID_FIELD_VALUE, 'email');
			}
			else if ($code == kUserException::LOGIN_ID_ALREADY_USED) {
				throw new KalturaAPIException(KalturaErrors::LOGIN_ID_ALREADY_USED);
			}
			else if ($code === kUserException::INVALID_OTP)
			{
				throw new KalturaAPIException(KalturaErrors::INVALID_OTP);
			}
			else if ($code === kUserException::MISSING_OTP)
			{
				throw new KalturaAPIException(KalturaErrors::MISSING_OTP);
			}
			throw $e;			
		}
	}

	
	/**
	 * Reset admin user password and send it to the users email address
	 * 
	 * @param string $email
	 *
	 * @throws KalturaErrors::LOGIN_DATA_NOT_FOUND
	 * @throws KalturaErrors::PASSWORD_STRUCTURE_INVALID
	 * @throws KalturaErrors::PASSWORD_ALREADY_USED
	 * @throws KalturaErrors::INVALID_FIELD_VALUE
	 * @throws KalturaErrors::LOGIN_ID_ALREADY_USED
	 */	
	protected function resetPasswordImpl($email)
	{
		KalturaResponseCacher::disableCache();
		
		$this->validateApiAccessControlByEmail($email);
		$this->validateRequestsAmount($email);
		
		try {
			$new_password = UserLoginDataPeer::resetUserPassword($email);
		}
		catch (kUserException $e) {
			$code = $e->getCode();
			if ($code == kUserException::LOGIN_DATA_NOT_FOUND) {
				throw new KalturaAPIException(KalturaErrors::LOGIN_DATA_NOT_FOUND, "user not found");
			}
			else if ($code == kUserException::PASSWORD_STRUCTURE_INVALID) {
				throw new KalturaAPIException(KalturaErrors::PASSWORD_STRUCTURE_INVALID);
			}
			else if ($code == kUserException::PASSWORD_ALREADY_USED) {
				throw new KalturaAPIException(KalturaErrors::PASSWORD_ALREADY_USED);
			}
			else if ($code == kUserException::INVALID_EMAIL) {
				throw new KalturaAPIException(KalturaErrors::INVALID_FIELD_VALUE, 'email');
			}
			else if ($code == kUserException::LOGIN_ID_ALREADY_USED) {
				throw new KalturaAPIException(KalturaErrors::LOGIN_ID_ALREADY_USED);
			}
			throw $e;			
		}	
		
		if (!$new_password)
			throw new KalturaAPIException(KalturaErrors::LOGIN_DATA_NOT_FOUND, "user not found" );
	}

	
	/**
	 * Get a session using user email and password
	 * 
	 * @param string $puserId
	 * @param string $loginEmail
	 * @param string $password
	 * @param int $partnerId
	 * @param int $expiry
	 * @param string $privileges
	 * @param string $otp
	 * 
	 * @return string KS
	 *
	 * @throws KalturaErrors::USER_NOT_FOUND
	 * @thrown KalturaErrors::LOGIN_RETRIES_EXCEEDED
	 * @thrown KalturaErrors::LOGIN_BLOCKED
	 * @thrown KalturaErrors::PASSWORD_EXPIRED
	 * @thrown KalturaErrors::INVALID_PARTNER_ID
	 * @thrown KalturaErrors::INTERNAL_SERVERL_ERROR
	 * @throws KalturaErrors::USER_IS_BLOCKED
	 * @throws KalturaErrors::DIRECT_LOGIN_BLOCKED
	 */		
	protected function loginImpl($puserId, $loginEmail, $password, $partnerId = null, $expiry = 86400, $privileges = '*', $otp = null)
	{
		KalturaResponseCacher::disableCache();
		myPartnerUtils::resetPartnerFilter('kuser');
		kuserPeer::setUseCriteriaFilter(true);
		
		// if a KS of a specific partner is used, don't allow logging in to a different partner
		if ($this->getPartnerId() && $partnerId && $this->getPartnerId() != $partnerId) {
			throw new KalturaAPIException(KalturaErrors::INVALID_PARTNER_ID, $partnerId);
		}

		if ($loginEmail && !$partnerId) {
			$this->validateApiAccessControlByEmail($loginEmail);
		}
		
		try {
			if ($loginEmail) {
				$user = UserLoginDataPeer::userLoginByEmail($loginEmail, $password, $partnerId, $otp);
			}
			else {
				$user = kuserPeer::userLogin($puserId, $password, $partnerId);
			}
		}
		catch (kUserException $e) {
			$code = $e->getCode();
			if ($code == kUserException::LOGIN_DATA_NOT_FOUND) {
				throw new KalturaAPIException(KalturaErrors::USER_NOT_FOUND);
			}
			if ($code == kUserException::USER_NOT_FOUND) {
				throw new KalturaAPIException(KalturaErrors::USER_NOT_FOUND);
			}
			else if ($code == kUserException::LOGIN_RETRIES_EXCEEDED) {
				throw new KalturaAPIException(KalturaErrors::LOGIN_RETRIES_EXCEEDED);
			}
			else if ($code == kUserException::LOGIN_BLOCKED) {
				throw new KalturaAPIException(KalturaErrors::LOGIN_BLOCKED);
			}
			else if ($code == kUserException::PASSWORD_EXPIRED) {
				throw new KalturaAPIException(KalturaErrors::PASSWORD_EXPIRED);
			}
			else if ($code == kUserException::WRONG_PASSWORD) {
				throw new KalturaAPIException(KalturaErrors::USER_WRONG_PASSWORD);
			}
			else if ($code == kUserException::USER_IS_BLOCKED) {
				throw new KalturaAPIException(KalturaErrors::USER_IS_BLOCKED);
			}
			else if ($code == kUserException::INVALID_OTP) {
				throw new KalturaAPIException(KalturaErrors::INVALID_OTP);
			}
			else if ($code == kUserException::MISSING_OTP) {
					throw new KalturaAPIException(KalturaErrors::MISSING_OTP);
			}
			else if ($code === kUserException::DIRECT_LOGIN_BLOCKED) {
				throw new KalturaAPIException(KalturaErrors::DIRECT_LOGIN_BLOCKED);
			}
									
			throw new $e;
		}
		if (!$user) {
			throw new KalturaAPIException(KalturaErrors::LOGIN_DATA_NOT_FOUND);
		}		
		
		if ( ($partnerId && $user->getPartnerId() != $partnerId) ||
		     ($this->getPartnerId() && !$partnerId && $user->getPartnerId() != $this->getPartnerId()) ) {
			throw new KalturaAPIException(KalturaErrors::INVALID_PARTNER_ID, $partnerId);
		}			
		
		$partner = PartnerPeer::retrieveByPK($user->getPartnerId());
		
		if (!$partner || $partner->getStatus() == Partner::PARTNER_STATUS_FULL_BLOCK)
			throw new KalturaAPIException(KalturaErrors::INVALID_PARTNER_ID, $user->getPartnerId());
		
		$ks = null;
		
		$admin = $user->getIsAdmin() ? KalturaSessionType::ADMIN : KalturaSessionType::USER;
		// create a ks for this admin_kuser as if entered the admin_secret using the API
		kSessionUtils::createKSessionNoValidations ( $partner->getId() ,  $user->getPuserId() , $ks , $expiry , $admin , "" , $privileges );
		
		return $ks;
	}
	
	
	/**
	 * Set initial users password
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
	protected function setInitialPasswordImpl($hashKey, $newPassword)
	{
		KalturaResponseCacher::disableCache();
		
		try {
			$loginData = UserLoginDataPeer::isHashKeyValid($hashKey);
			if ($loginData)
				$this->validateApiAccessControl($loginData->getLastLoginPartnerId());
			$result = UserLoginDataPeer::setInitialPassword($hashKey, $newPassword);
		}
		catch (kUserException $e) {
			$code = $e->getCode();
			if ($code == kUserException::LOGIN_DATA_NOT_FOUND) {
				throw new KalturaAPIException(KalturaErrors::LOGIN_DATA_NOT_FOUND);
			}
			if ($code == kUserException::PASSWORD_STRUCTURE_INVALID) {
				$loginData = UserLoginDataPeer::isHashKeyValid($hashKey);
				$invalidPasswordStructureMessage = $loginData->getInvalidPasswordStructureMessage();
				throw new KalturaAPIException(KalturaErrors::PASSWORD_STRUCTURE_INVALID,$invalidPasswordStructureMessage);
			}
			if ($code == kUserException::NEW_PASSWORD_HASH_KEY_EXPIRED) {
				throw new KalturaAPIException(KalturaErrors::NEW_PASSWORD_HASH_KEY_EXPIRED);
			}
			if ($code == kUserException::NEW_PASSWORD_HASH_KEY_INVALID) {
				throw new KalturaAPIException(KalturaErrors::NEW_PASSWORD_HASH_KEY_INVALID);
			}
			if ($code == kUserException::PASSWORD_ALREADY_USED) {
				throw new KalturaAPIException(KalturaErrors::PASSWORD_ALREADY_USED);
			}
			
			throw $e;
		}
		if (!$result)
		{
			throw new KalturaAPIException(KalturaErrors::INTERNAL_SERVERL_ERROR);
		}
		else
		{
			$response = new KalturaAuthentication();
			if($result !== true)
			{
				$response->qrCode = $result;
			}
			return $response;
		}

	}
	
	protected function validateApiAccessControlByEmail($email)
	{ 
		$loginData = UserLoginDataPeer::getByEmail($email);
		if ($loginData)
		{
			$this->validateApiAccessControl($loginData->getLastLoginPartnerId());
		}
	}

	/**
	 * check if there were more than max_allowed calls for different resources
	 * @param string $email
	 * @throws KalturaErrors::FAILED_TO_INIT_OBJECT
	 */
	protected function validateRequestsAmount($email)
	{
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_RESOURCE_RESERVATION);
		if (!$cache)
		{
			throw new KalturaAPIException(KalturaErrors::FAILED_TO_INIT_OBJECT);
		}
		$this->validateRequestsAmountPerEmail($email, $cache);
		$this->validateRequestsAmountPerIp($cache);
	}


	/**
	 * check if there were more than max_allowed calls to reset password action per sepecific email in specific time frame
	 * @param string $email
	 * @param kBaseCacheWrapper $cache
	 * @throws KalturaErrors::RESOURCE_IS_RESERVED
	 */
	protected function validateRequestsAmountPerEmail($email, $cache)
	{
		$resourceId = kCurrentContext::getCurrentPartnerId() . '_' . $email;
		$maxRequestsNum = kConf::get('max_reset_requests_per_email', 'local', 10);
		$reservationTime = kConf::get('reservation_time_per_email', 'local', 600);
		$resourceReservator = new CountingReservation($cache, $reservationTime, $maxRequestsNum);
		if(!$resourceReservator->tryAcquire($resourceId))
		{
			throw new KalturaAPIException(KalturaErrors::RESOURCE_IS_RESERVED, $resourceId);
		}
	}

	/**
	 * check if there were more than max_allowed calls to reset password action per sepecific ip in specific time frame
	 * @param kBaseCacheWrapper $cache
	 * @throws KalturaErrors::RESOURCE_IS_RESERVED
	 */
	protected function validateRequestsAmountPerIp($cache)
	{
		if (!kCurrentContext::$user_ip)
			return;
		$resourceId = 'ip_' . kCurrentContext::$user_ip;
		$maxRequestsNum = kConf::get('max_reset_requests_per_ip', 'local', 100);
		$reservationTime = kConf::get('reservation_time_per_ip', 'local', 600);
		$resourceReservator = new CountingReservation($cache, $reservationTime, $maxRequestsNum);
		if(!$resourceReservator->tryAcquire($resourceId))
		{
			throw new KalturaAPIException(KalturaErrors::RESOURCE_IS_RESERVED, $resourceId);
		}
	}
	
	public function loginByKsImpl($ks, $destPartnerId)
	{
		$ksObj = kSessionUtils::crackKs($ks);
		if($ksObj->partner_id == $destPartnerId)
			return $ks;
		
		if(!$ksObj->user || $ksObj->user == '')
			throw new KalturaAPIException(APIErrors::INVALID_USER_ID, $ksObj->user);

		if($ksObj->hasPrivilege(kSessionBase::PRIVILEGE_ENABLE_PARTNER_CHANGE_ACCOUNT) &&
			!$ksObj->verifyPrivileges(kSessionBase::PRIVILEGE_ENABLE_PARTNER_CHANGE_ACCOUNT, $destPartnerId))
			throw new KalturaAPIException(APIErrors::PARTNER_CHANGE_ACCOUNT_DISABLED);
		
		try 
		{
			$adminKuser = UserLoginDataPeer::userLoginByKs($ks, $destPartnerId, true);
		}
		catch (kUserException $e) 
		{
			$code = $e->getCode();
			if ($code == kUserException::USER_NOT_FOUND) 
			{
				throw new KalturaAPIException(APIErrors::ADMIN_KUSER_NOT_FOUND);
			}
			if ($code == kUserException::LOGIN_DATA_NOT_FOUND) 
			{
				throw new KalturaAPIException(APIErrors::LOGIN_DATA_NOT_FOUND);
			}
			else if ($code == kUserException::LOGIN_RETRIES_EXCEEDED) 
			{
				throw new KalturaAPIException(APIErrors::LOGIN_RETRIES_EXCEEDED);
			}
			else if ($code == kUserException::LOGIN_BLOCKED) 
			{
				throw new KalturaAPIException(APIErrors::LOGIN_BLOCKED);
			}
			else if ($code == kUserException::USER_IS_BLOCKED) 
			{
				throw new KalturaAPIException(APIErrors::USER_IS_BLOCKED);
			}
			else if ($code == kUserException::NEW_LOGIN_REQUIRED)
			{
				throw new KalturaAPIException(KalturaErrors::NEW_LOGIN_REQUIRED);
			}
			else if ($code === kUserException::DIRECT_LOGIN_BLOCKED)
			{
				throw new KalturaAPIException(KalturaErrors::DIRECT_LOGIN_BLOCKED);
			}
			throw new KalturaAPIException(APIErrors::INTERNAL_SERVERL_ERROR);
		}
		
		if (!$adminKuser || !$adminKuser->getIsAdmin()) 
		{
			throw new KalturaAPIException(APIErrors::ADMIN_KUSER_NOT_FOUND);
		}
		
		if ($destPartnerId != $adminKuser->getPartnerId()) 
		{
			throw new KalturaAPIException(APIErrors::UNKNOWN_PARTNER_ID, $destPartnerId);
		}
		
		$partner = PartnerPeer::retrieveByPK($adminKuser->getPartnerId());
		if (!$partner)
		{
			throw new KalturaAPIException(APIErrors::UNKNOWN_PARTNER_ID, $adminKuser->getPartnerId());
		}
		
		if(!$partner->validateApiAccessControl())
		{
			throw new KalturaAPIException(APIErrors::SERVICE_ACCESS_CONTROL_RESTRICTED, $this->serviceName);
		}
		
		
		kSessionUtils::createKSessionNoValidations ( $partner->getId() ,  $adminKuser->getPuserId() , $ks , dateUtils::DAY , SessionType::ADMIN , "" , $ksObj->getPrivileges() );
		return $ks;
	}
	
	function addUserImpl(KalturaBaseUser $user)
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

		$className = get_class ($user);
		$newUser = new $className;
		$newUser->fromObject($dbUser, $this->getResponseProfile());

		return $newUser;
	}
}
