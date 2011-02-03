<?php

class KalturaBaseUserService extends KalturaBaseService 
{
	
	protected function partnerRequired($actionName)
	{
		if ($actionName === 'loginByLoginId') {
			return false;
		}
		if ($actionName === 'updatLoginData') {
			return false;
		}
		if ($actionName === 'resetPassword') {
			return false;
		}
		if ($actionName === 'setInitialPassword') {
			return false;
		}
		return parent::partnerRequired($actionName);
	}
	
	
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService ($serviceId, $serviceName, $actionName);
		parent::applyPartnerFilterForClass ( new kuserPeer() );
	}	
	
	/**
	 * Update admin user password and email
	 * 
	 * @param string $email
	 * @param string $password
	 * @param string $newEmail Optional, provide only when you want to update the email
	 * @param string $newPassword
	 *
	 * @throws KalturaErrors::INVALID_FIELD_VALUE
	 * @throws KalturaErrors::LOGIN_DATA_NOT_FOUND
	 * @throws KalturaErrors::WRONG_OLD_PASSWORD
	 * @throws KalturaErrors::PASSWORD_STRUCTURE_INVALID
	 * @throws KalturaErrors::PASSWORD_ALREADY_USED
	 * @throws KalturaErrors::INVALID_FIELD_VALUE
	 * @throws KalturaErrors::LOGIN_ID_ALREADY_USED
	 */
	protected function updateLoginDataImpl( $email , $password , $newEmail = "" , $newPassword = "")
	{
		KalturaResponseCacher::disableCache();

		if ($newEmail != "")
		{
			if(!kString::isEmailString($newEmail))
				throw new KalturaAPIException ( KalturaErrors::INVALID_FIELD_VALUE, "newEmail" );
		}

		try {
			UserLoginDataPeer::updateLoginData ( $email , $password, $newEmail, $newPassword );
		}
		catch (kUserException $e) {
			$code = $e->getCode();
			if ($code == kUserException::LOGIN_DATA_NOT_FOUND) {
				throw new KalturaAPIException(KalturaErrors::LOGIN_DATA_NOT_FOUND);
			}
			else if ($code == kUserException::WRONG_PASSWORD) {
				throw new KalturaAPIException(KalturaErrors::WRONG_OLD_PASSWORD);
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
	 */		
	protected function loginImpl($puserId, $loginEmail, $password, $partnerId = null, $expiry = 86400, $privileges = '*')
	{
		KalturaResponseCacher::disableCache();
		kuserPeer::setUseCriteriaFilter(false);
		
		// if a KS of a specific partner is used, don't allow logging in to a different partner
		if ($this->getPartnerId() && $partnerId && $this->getPartnerId() != $partnerId) {
			throw new KalturaAPIException(KalturaErrors::INVALID_PARTNER_ID, $partnerId);
		}		
		
		try {
			if ($loginEmail) {
				$user = UserLoginDataPeer::userLoginByEmail($loginEmail, $password, $partnerId);
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
			$result = UserLoginDataPeer::setInitialPassword($hashKey, $newPassword);
		}
		catch (kUserException $e) {
			$code = $e->getCode();
			if ($code == kUserException::LOGIN_DATA_NOT_FOUND) {
				throw new KalturaAPIException(KalturaErrors::LOGIN_DATA_NOT_FOUND);
			}
			if ($code == kUserException::PASSWORD_STRUCTURE_INVALID) {
				throw new KalturaAPIException(KalturaErrors::PASSWORD_STRUCTURE_INVALID);
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
		if (!$result) {
			throw new KalturaAPIException(KalturaErrors::INTERNAL_SERVERL_ERROR);
		}
	}
}