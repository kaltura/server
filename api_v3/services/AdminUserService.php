<?php
/**
 * Manage details for the administrative user
 *
 * @service adminUser
 * @package api
 * @subpackage services
 */
class AdminUserService extends KalturaBaseService 
{
	/**
	 * Update admin user password and email
	 * 
	 * @action updatePassword
	 * @param string $email
	 * @param string $password
	 * @param string $newEmail Optional, provide only when you want to update the email
	 * @param string $newPassword
	 * @return KalturaAdminUser
	 *
	 * @throws KalturaErrors::INVALID_FIELD_VALUE
	 * @throws KalturaErrors::ADMIN_KUSER_WRONG_OLD_PASSWORD
	 */
	function updatePasswordAction( $email , $password , $newEmail = "" , $newPassword = "" )
	{
		KalturaResponseCacher::disableCache();
		
		if ($newEmail != "")
		{
			if(!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i', $newEmail))
				throw new KalturaAPIException ( KalturaErrors::INVALID_FIELD_VALUE, "newEmail" );
		}

		try {
			$adminKuserPeer = new adminKuserPeer(); // TODO - why not static ?
			list( $new_password , $new_email) = $adminKuserPeer->resetUserPassword ( $email , $newPassword , $password , $newEmail );
		}
		catch (kAdminKuserException $e) {
			$code = $e->getCode();
			if ($code == kAdminKuserException::ADMIN_KUSER_NOT_FOUND) {
				throw new KalturaAPIException(KalturaErrors::ADMIN_KUSER_NOT_FOUND);
			}
			else if ($code == kAdminKuserException::ADMIN_KUSER_WRONG_OLD_PASSWORD) {
				throw new KalturaAPIException(KalturaErrors::ADMIN_KUSER_WRONG_OLD_PASSWORD, "wrong password" );
			}
			else if ($code == kAdminKuserException::PASSWORD_STRUCTURE_INVALID) {
				throw new KalturaAPIException(KalturaErrors::PASSWORD_STRUCTURE_INVALID);
			}
			else if ($code == kAdminKuserException::PASSWORD_ALREADY_USED) {
				throw new KalturaAPIException(KalturaErrors::PASSWORD_ALREADY_USED);
			}
			throw $e;			
		}			
		
		$adminUser = new KalturaAdminUser;
		$adminUser->email = ( $new_email )? $new_email: $email;
		$adminUser->password = $new_password;
		
		return $adminUser;
	}

	/**
	 * Reset admin user password and send it to the users email address
	 * 
	 * @action resetPassword
	 * @param string $email
	 *
	 * @throws KalturaErrors::ADMIN_KUSER_NOT_FOUND
	 */	
	function resetPasswordAction($email)
	{
		KalturaResponseCacher::disableCache();
		
		try {
			$adminKuserPeer = new adminKuserPeer(); // TODO - why not static ?
			list( $new_password , $new_email) = $adminKuserPeer->resetUserPassword($email);
		}
		catch (kAdminKuserException $e) {
			$code = $e->getCode();
			if ($code == kAdminKuserException::ADMIN_KUSER_NOT_FOUND) {
				throw new KalturaAPIException(KalturaErrors::ADMIN_KUSER_NOT_FOUND, "user not found");
			}
			else if ($code == kAdminKuserException::ADMIN_KUSER_WRONG_OLD_PASSWORD) {
				throw new KalturaAPIException(KalturaErrors::ADMIN_KUSER_WRONG_OLD_PASSWORD, "wrong password" );
			}
			else if ($code == kAdminKuserException::PASSWORD_STRUCTURE_INVALID) {
				throw new KalturaAPIException(KalturaErrors::PASSWORD_STRUCTURE_INVALID);
			}
			else if ($code == kAdminKuserException::PASSWORD_ALREADY_USED) {
				throw new KalturaAPIException(KalturaErrors::PASSWORD_ALREADY_USED);
			}
			throw $e;			
		}	
		
		if (!$new_password)
			throw new KalturaAPIException(KalturaErrors::ADMIN_KUSER_NOT_FOUND, "user not found" );
	}

	/**
	 * Get an admin session using admin email and password (Used for login to the KMC application)
	 * 
	 * @action login
	 * @param string $email
	 * @param string $password
	 * @return string
	 *
	 * @throws KalturaErrors::ADMIN_KUSER_NOT_FOUND
	 * @thrown KalturaErrors::INVALID_PARTNER_ID
	 */		
	function loginAction($email, $password)
	{
		KalturaResponseCacher::disableCache();
		
		try {
			$adminKuser = adminKuserPeer::adminLogin($email, $password);
		}
		catch (kAdminKuserException $e) {
			$code = $e->getCode();
			if ($code == kAdminKuserException::ADMIN_KUSER_NOT_FOUND) {
				throw new KalturaAPIException(KalturaErrors::ADMIN_KUSER_NOT_FOUND);
			}
			else if ($code == kAdminKuserException::LOGIN_RETRIES_EXCEEDED) {
				throw new KalturaAPIException(KalturaErrors::LOGIN_RETRIES_EXCEEDED);
			}
			else if ($code == kAdminKuserException::LOGIN_BLOCKED) {
				throw new KalturaAPIException(KalturaErrors::LOGIN_BLOCKED);
			}
			else if ($code == kAdminKuserException::PASSWORD_EXPIRED) {
				throw new KalturaAPIException(KalturaErrors::PASSWORD_EXPIRED);
			}
			throw new KalturaAPIException(KalturaErrors::INTERNAL_SERVERL_ERROR);
		}
		if (!$adminKuser) {
			throw new KalturaAPIException(KalturaErrors::ADMIN_KUSER_NOT_FOUND);
		}
		
		
		$partner = PartnerPeer::retrieveByPK($adminKuser->getPartnerId());
		
		if (!$partner)
			throw new KalturaAPIException(KalturaErrors::INVALID_PARTNER_ID, $adminKuser->getPartnerId());
		
		$admin_puser_id = "__ADMIN__" . $adminKuser->getId(); // the prefix __ADMIN__ and the id in the admin_kuser table
		$kuser = kuserPeer::createKuserForPartner($this->getPartnerId(), $admin_puser_id);
		
		$ks = null;
		// create a ks for this admin_kuser as if entered the admin_secret using the API
		kSessionUtils::createKSessionNoValidations ( $partner->getId() ,  $kuser->getPuserId() , $ks , 86400 , 2 , "" , "*" );
		
		return $ks;
	}
	
	
	/**
	 * Set initial users password
	 * 
	 * @action setInitialPassword
	 * @param string $hashKey
	 * @param string $newPassword new password to set
	 *
	 * @throws 
	 */	
	function setInitialPasswordAction($hashKey, $newPassword)
	{
		KalturaResponseCacher::disableCache();
		
		try {
			$result = adminKuserPeer::setInitialPassword($hashKey, $newPassword);
		}
		catch (kAdminKuserException $e) {
			$code = $e->getCode();
			if ($code == kAdminKuserException::ADMIN_KUSER_NOT_FOUND) {
				throw new KalturaAPIException(KalturaErrors::ADMIN_KUSER_NOT_FOUND);
			}
			if ($code == kAdminKuserException::PASSWORD_STRUCTURE_INVALID) {
				throw new KalturaAPIException(KalturaErrors::PASSWORD_STRUCTURE_INVALID);
			}
			if ($code == kAdminKuserException::NEW_PASSWORD_HASH_KEY_EXPIRED) {
				throw new KalturaAPIException(KalturaErrors::NEW_PASSWORD_HASH_KEY_EXPIRED);
			}
			if ($code == kAdminKuserException::NEW_PASSWORD_HASH_KEY_INVALID) {
				throw new KalturaAPIException(KalturaErrors::NEW_PASSWORD_HASH_KEY_INVALID);
			}
			if ($code == kAdminKuserException::PASSWORD_ALREADY_USED) {
				throw new KalturaAPIException(KalturaErrors::PASSWORD_ALREADY_USED);
			}
			
			throw $e;
		}
		if (!$result) {
			throw new KalturaAPIException(KalturaErrors::INTERNAL_SERVERL_ERROR);
		}
	}
}