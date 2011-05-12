<?php
/**
 * Manage details for the administrative user
 *
 * @service adminUser
 * @package api
 * @subpackage services
 */
class AdminUserService extends KalturaBaseUserService 
{
	
	protected function partnerRequired($actionName)
	{
		if ($actionName === 'updatePassword') {
			return false;
		}
		if ($actionName === 'resetPassword') {
			return false;
		}
		if ($actionName === 'login') {
			return false;
		}
		if ($actionName === 'setInitialPassword') {
			return false;
		}
		return parent::partnerRequired($actionName);
	}	
	

	/**
	 * keep backward compatibility with changed error codes
	 * @param KalturaAPIException $e
	 * @throws KalturaAPIException
	 */
	private function throwTranslatedException(KalturaAPIException $e)
	{
		$code = $e->getCode();
		if ($code == KalturaErrors::USER_NOT_FOUND) {
			throw new KalturaAPIException(KalturaErrors::ADMIN_KUSER_NOT_FOUND);
		}
		else if ($code == KalturaErrors::WRONG_OLD_PASSWORD) {
			throw new KalturaAPIException(KalturaErrors::ADMIN_KUSER_WRONG_OLD_PASSWORD, "wrong password" );
		}
		else if ($code == KalturaErrors::USER_WRONG_PASSWORD) {
			throw new KalturaAPIException(KalturaErrors::ADMIN_KUSER_NOT_FOUND);
		}
		else if ($code == KalturaErrors::LOGIN_DATA_NOT_FOUND) {
			throw new KalturaAPIException(KalturaErrors::ADMIN_KUSER_NOT_FOUND);
		}
		throw $e;
	}
	
	
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
	 * @throws KalturaErrors::ADMIN_KUSER_NOT_FOUND
	 * @throws KalturaErrors::PASSWORD_STRUCTURE_INVALID
	 * @throws KalturaErrors::PASSWORD_ALREADY_USED
	 * @throws KalturaErrors::INVALID_FIELD_VALUE
	 * @throws KalturaErrors::LOGIN_ID_ALREADY_USED
	 * 
	 * @deprecated
	 */
	public function updatePasswordAction( $email , $password , $newEmail = "" , $newPassword = "" )
	{
		try
		{
			parent::updateLoginDataImpl($email, $password, $newEmail, $newPassword);
			
			// copy required parameters to a KalturaAdminUser object for backward compatibility
			$adminUser = new KalturaAdminUser();
			$adminUser->email = $newEmail ? $newEmail : $email;
			$adminUser->password = $newPassword ? $newPassword : $password;
			
			return $adminUser;
		}
		catch (KalturaAPIException $e) // keep backward compatibility with changed error codes
		{
			$this->throwTranslatedException($e);
		}
	}
	
	
	/**
	 * Reset admin user password and send it to the users email address
	 * 
	 * @action resetPassword
	 * @param string $email
	 *
	 * @throws KalturaErrors::ADMIN_KUSER_NOT_FOUND
	 * @throws KalturaErrors::PASSWORD_STRUCTURE_INVALID
	 * @throws KalturaErrors::PASSWORD_ALREADY_USED
	 * @throws KalturaErrors::INVALID_FIELD_VALUE
	 * @throws KalturaErrors::LOGIN_ID_ALREADY_USED
	 */	
	public function resetPasswordAction($email)
	{
		try
		{
			return parent::resetPasswordImpl($email);
		}
		catch (KalturaAPIException $e) // keep backward compatibility with changed error codes
		{
			$this->throwTranslatedException($e);
		}
	}
	
	/**
	 * Get an admin session using admin email and password (Used for login to the KMC application)
	 * 
	 * @action login
	 * @param string $email
	 * @param string $password
	 * @param int $partnerId
	 * @return string
	 *
	 * @throws KalturaErrors::ADMIN_KUSER_NOT_FOUND
	 * @thrown KalturaErrors::INVALID_PARTNER_ID
	 * @thrown KalturaErrors::LOGIN_RETRIES_EXCEEDED
	 * @thrown KalturaErrors::LOGIN_BLOCKED
	 * @thrown KalturaErrors::PASSWORD_EXPIRED
	 * @thrown KalturaErrors::INVALID_PARTNER_ID
	 * @thrown KalturaErrors::INTERNAL_SERVERL_ERROR
	 */		
	public function loginAction($email, $password, $partnerId = null)
	{
		try
		{
			$ks = parent::loginImpl(null, $email, $password, $partnerId);
			$tempKs = kSessionUtils::crackKs($ks);
			if (!$tempKs->isAdmin()) {
				throw new KalturaAPIException(KalturaErrors::ADMIN_KUSER_NOT_FOUND); 
			}
			return $ks;
		}
		catch (KalturaAPIException $e) // keep backward compatibility with changed error codes
		{
			$this->throwTranslatedException($e);
		}
	}
	
	
	
	/**
	 * Set initial users password
	 * 
	 * @action setInitialPassword
	 * @param string $hashKey
	 * @param string $newPassword new password to set
	 *
	 * @throws KalturaErrors::ADMIN_KUSER_NOT_FOUND
	 * @throws KalturaErrors::PASSWORD_STRUCTURE_INVALID
	 * @throws KalturaErrors::NEW_PASSWORD_HASH_KEY_EXPIRED
	 * @throws KalturaErrors::NEW_PASSWORD_HASH_KEY_INVALID
	 * @throws KalturaErrors::PASSWORD_ALREADY_USED
	 * @throws KalturaErrors::INTERNAL_SERVERL_ERROR
	 */	
	public function setInitialPasswordAction($hashKey, $newPassword)
	{
		try
		{
			return parent::setInitialPasswordImpl($hashKey, $newPassword);
		}
		catch (KalturaAPIException $e) // keep backward compatibility with changed error codes
		{
			$this->throwTranslatedException($e);
		}
	}
	
	
}