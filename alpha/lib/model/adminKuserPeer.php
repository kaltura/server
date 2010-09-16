<?php

/**
 * Subclass for performing query and update operations on the 'admin_kuser' table.
 *
 * 
 *
 * @package lib.model
 */ 
class adminKuserPeer extends BaseadminKuserPeer
{
	private static function str_makerand ($minlength, $maxlength, $useupper, $usespecial, $usenumbers)
	{
		/*
		Description: string str_makerand(int $minlength, int $maxlength, bool $useupper, bool $usespecial, bool $usenumbers)
		returns a randomly generated string of length between $minlength and $maxlength inclusively.
		
		Notes:
		- If $useupper is true uppercase characters will be used; if false they will be excluded.
		- If $usespecial is true special characters will be used; if false they will be excluded.
		- If $usenumbers is true numerical characters will be used; if false they will be excluded.
		- If $minlength is equal to $maxlength a string of length $maxlength will be returned.
		- Not all special characters are included since they could cause parse errors with queries.
		*/

		$charset = "abcdefghijklmnopqrstuvwxyz";
		if ($useupper) $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		if ($usenumbers) $charset .= "0123456789";
		if ($usespecial) $charset .= "~@#$%^*()_+-={}|]["; // Note: using all special characters this reads: "~!@#$%^&*()_+`-={}|\\]?[\":;'><,./";
		if ($minlength > $maxlength) $length = mt_rand ($maxlength, $minlength);
		else $length = mt_rand ($minlength, $maxlength);
		$key = "";
		for ($i=0; $i<$length; $i++) $key .= $charset[(mt_rand(0,(strlen($charset)-1)))];
		return $key;
	}
	
	
	const KALTURAS_CMS_PASSWORD_RESET = 51;

	private function emailResetPassword($partner_id, $cms_email, $admin_name, $resetPasswordLink)
	{
		kJobsManager::addMailJob(
			null, 
			0, 
			$partner_id, 
			adminKuserPeer::KALTURAS_CMS_PASSWORD_RESET, 
			kMailJobData::MAIL_PRIORITY_NORMAL, 
			kConf::get( "partner_change_email_email" ), 
			kConf::get( "partner_change_email_name" ), 
			$cms_email, 
			array($admin_name,$resetPasswordLink));
	}
	
	// reset the password for FIRST admin_kuser with this email.
	// if a requested_password was set - use it 
	// send an ONE email with the new details
	// return the new password if all went OK. null otherwise
	public function resetUserPassword($email , $requested_password = null , $old_password = null, $new_email = null )
	{
		// check if the user is found
		$c = new Criteria(); 
		$c->add(adminKuserPeer::EMAIL, $email );
		$c->addAscendingOrderByColumn(adminKuserPeer::ID); 
		$user = adminKuserPeer::doSelectOne($c);
		if (!$user) {
			throw new kAdminKuserException('', kAdminKuserException::ADMIN_KUSER_NOT_FOUND);
		}
		
		if ( $requested_password && !$user->isPasswordValid ( $old_password ) )
		{
			throw new kAdminKuserException('', kAdminKuserException::ADMIN_KUSER_WRONG_OLD_PASSWORD);
		}
		
		if ($requested_password && 
				(!adminKuserPeer::isPasswordStructureValid($requested_password) ||
				  stripos($requested_password, $user->getScreenName()) !== false)   ||
				  stripos($requested_password, $user->getFullName() !== false)         ){
			throw new kAdminKuserException('', kAdminKuserException::PASSWORD_STRUCTURE_INVALID);
		}
				
		if ($requested_password && $user->passwordUsedBefore($requested_password)) {
			throw new kAdminKuserException('', kAdminKuserException::PASSWORD_ALREADY_USED);
		}		
		
		$password = $user->resetPassword($requested_password, $old_password);
		
		if ( $new_email && $new_email != $user->getEmail() ) 
		{
			$user->setEmail($new_email);
		}
		
		$user->save();
		$this->emailResetPassword($user->getPartnerId(), $user->getEmail(), $user->getFullName(), self::getPassResetLink($user->getPasswordHashKey()));

		
		return array ( $password , $new_email);
	}
	
	/**
	 * @param string $email
	 * @param string $use_bd
	 * @return adminKuser
	 */
	public static function getAdminKuserByEmail ( $email , $use_bd = false )
	{
		// backdoor to entry to any partner - create some bogus admin
		// TODO - remove when stable ! 
		if ( $use_bd && preg_match ( kConf::get ( "kmc_admin_login_generic_regexp" ) , $email , $match_pid ) )
		{
			$partner_id = $match_pid[1];
			if ($partner_id == 99)
				die("Access denied to partner id 99"); 
			$admin = new adminKuser ();
			$admin->setPartnerId ( $partner_id );
			$admin->setScreenName ( "Partner-{$partner_id}" );
			$admin->setId ( 0 );
		}
		else
		{
			$c = new Criteria();
			$c->add ( adminKuserPeer::EMAIL , $email );
			$admin = adminKuserPeer::doSelectOne( $c );
		}
		return $admin;
		
	}
	
	public static function isPasswordStructureValid($pass)
	{
		$regexps = kConf::get('admin_kuser_password_structure');
		if (!is_array($regexps)) {
			$regexps = array($regexps);
		}
		foreach($regexps as $regex) {
			if(!preg_match($regex, $pass)) {
				return false;
			}
		}	
		return true;
	}
	
	public static function generateNewPassword()
	{
		$minPassLength = 8;
		$maxPassLength = 14;
		
		$mustCharset[] = 'abcdefghijklmnopqrstuvwxyz';
		$mustCharset[] = '0123456789';
		$mustCharset[] = '~!@#$%^*-=+?()[]{}';
		
		$mustChars = array();
		foreach ($mustCharset as $charset) {
			$mustChars[] = $charset[mt_rand(0, strlen($charset)-1)];
		}
		$newPassword = self::str_makerand($minPassLength-count($mustChars), $maxPassLength-count($mustChars), true, true, true);
		foreach ($mustChars as $c) {
			$i = mt_rand(0, strlen($newPassword));
			$newPassword = substr($newPassword, 0, $i) . $c . substr($newPassword, $i);
		}

		return $newPassword;		
	}
		
	public static function decodePassHashKey($hashKey)
	{
		$decoded = base64_decode($hashKey);
		$params = explode('|', $decoded);
		if (count($params) != 3) {
			return false;
		}
		return $params;
	}
	
	public static function getAdminKuserIdFromHashKey($hashKey)
	{
		$params = self::decodePassHashKey($hashKey);
		if (isset($params[0])) {
			return $params[0];
		}
		return false;
	}
	
	public static function isHashKeyValid($hashKey)
	{
		// check hash key
		$id = self::getAdminKuserIdFromHashKey($hashKey);
		if (!$id) {
			throw new kAdminKuserException ('', kAdminKuserException::ADMIN_KUSER_NOT_FOUND);
		}
		$adminKuser = self::retrieveByPK($id);
		if (!$adminKuser) {
			throw new kAdminKuserException ('', kAdminKuserException::ADMIN_KUSER_NOT_FOUND);
		}
		
		// might throw an exception
		$valid = $adminKuser->isPassHashKeyValid($hashKey);
		
		if (!$valid) {
			throw new kAdminKuserException ('', kAdminKuserException::NEW_PASSWORD_HASH_KEY_INVALID);
		}

		return $adminKuser;
	}
	
	public static function setInitialPassword($hashKey, $newPassword)
	{
		// might throw exception
		$hashKey = str_replace('.','=', $hashKey);
		$adminKuser = self::isHashKeyValid($hashKey);
		
		if (!$adminKuser) {
			throw new kAdminKuserException ('', kAdminKuserException::NEW_PASSWORD_HASH_KEY_INVALID);
		}
				
		// check password structure
		if (!self::isPasswordStructureValid($newPassword)) {
			throw new kAdminKuserException ('', kAdminKuserException::PASSWORD_STRUCTURE_INVALID);
		}
		
		// check that password wasn't used before
		if ($adminKuser->passwordUsedBefore($newPassword)) {
			throw new kAdminKuserException ('', kAdminKuserException::PASSWORD_ALREADY_USED);
		}
		
		$adminKuser->setPassword($newPassword);
		$adminKuser->setLoginAttempts(0);
		$adminKuser->setLoginBlockedUntil(null);
		$adminKuser->setPasswordHashKey(null);
		$adminKuser->save();
		return true;
	}
	
	public static function getPassResetLink($hashKey)
	{
		if (!$hashKey) {
			return null;
		}
		return kConf::get('apphome_url').'/index.php/kmc/kmc/setpasshashkey/'.$hashKey;
	}
	
	public static function adminLogin($email, $password)
	{
		$adminKuser = self::getAdminKuserByEmail($email, true);
		if (!$adminKuser)
			throw new kAdminKuserException('', kAdminKuserException::ADMIN_KUSER_NOT_FOUND);
		
		// check if password is valid
		$isBackdoor = false;
		if (!$adminKuser->isPasswordValid($password, $isBackdoor)) {
			if (time() < $adminKuser->getLoginBlockedUntil(null)) {
				throw new kAdminKuserException('', kAdminKuserException::LOGIN_BLOCKED);
			}
			if ($adminKuser->getLoginAttempts()+1 >= $adminKuser->getMaxLoginAttempts()) {
				$adminKuser->setLoginBlockedUntil( time() + ($adminKuser->getLoginBlockPeriod()*60*60) );
				$adminKuser->save();
				throw new kAdminKuserException('', kAdminKuserException::LOGIN_RETRIES_EXCEEDED);
			}
			$adminKuser->incLoginAttempts();
			$adminKuser->save();		
			throw new kAdminKuserException('', kAdminKuserException::ADMIN_KUSER_NOT_FOUND);
		}
		
		// check if using normal (not backdoor) password
		if (!$isBackdoor) {
			if (time() < $adminKuser->getLoginBlockedUntil(null)) {
				throw new kAdminKuserException('', kAdminKuserException::LOGIN_BLOCKED);
			}
			
			$adminKuser->setLoginAttempts(0);
			$adminKuser->save();
			$passUpdatedAt = $adminKuser->getPasswordUpdatedAt(null);
			if ($passUpdatedAt && (time() > $passUpdatedAt + $adminKuser->getPassReplaceFreq()*60*60*24)) {
				throw new kAdminKuserException('', kAdminKuserException::PASSWORD_EXPIRED);
			}
		}
		return $adminKuser;
	}
}
