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
		if ($newEmail != "")
		{
			if(!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i', $newEmail))
				throw new KalturaAPIException ( KalturaErrors::INVALID_FIELD_VALUE, "newEmail" );
		}
		
		$adminKuser = new adminKuserPeer(); // TODO - why not static ?
		list( $new_password , $new_email ) = $adminKuser->resetUserPassword ( $email , $newPassword , $password , $newEmail );
		
		if ( ! $new_password )
			throw new KalturaAPIException ( KalturaErrors::ADMIN_KUSER_WRONG_OLD_PASSWORD, "wrong password" );
		
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
		$adminKuser = new adminKuserPeer(); // FIXME - should be static
		$newPassword = $adminKuser->resetUserPassword($email);
		
		if (!$newPassword)
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
		$adminKuser = adminKuserPeer::getAdminKuserByEmail($email, true);
		if (!$adminKuser)
			throw new KalturaAPIException(KalturaErrors::ADMIN_KUSER_NOT_FOUND);
		
		if (!$adminKuser->isPasswordValid($password))
			throw new KalturaAPIException(KalturaErrors::ADMIN_KUSER_NOT_FOUND);
		
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
}