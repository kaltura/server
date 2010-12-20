<?php


/**
 * Skeleton subclass for performing query and update operations on the 'user_login_data' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class UserLoginDataPeer extends BaseUserLoginDataPeer {

	const KALTURAS_CMS_PASSWORD_RESET = 51;
	
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
	
	
	
	private static function emailResetPassword($partner_id, $cms_email, $user_name, $resetPasswordLink)
	{
		kJobsManager::addMailJob(
			null, 
			0, 
			$partner_id, 
			UserLoginDataPeer::KALTURAS_CMS_PASSWORD_RESET, 
			kMailJobData::MAIL_PRIORITY_NORMAL, 
			kConf::get( "partner_change_email_email" ), 
			kConf::get( "partner_change_email_name" ), 
			$cms_email, 
			array($user_name, $resetPasswordLink)
		);
	}
	

	public static function resetUserPassword($email , $requested_password = null , $old_password = null, $new_email = null)
	{		
		// if email is null, no need to do any DB queries
		if (!$email) {
			throw new kUserException('', kUserException::LOGIN_DATA_NOT_FOUND);
		}
		
		$c = new Criteria(); 
		$c->add(UserLoginDataPeer::LOGIN_EMAIL, $email ); 
		$loginData = UserLoginDataPeer::doSelectOne($c);
		
		// check if login data exists
		if (!$loginData) {
			throw new kUserException('', kUserException::LOGIN_DATA_NOT_FOUND);
		}
		
		// check if the email string is a valid email
		if ($new_email && !kString::isEmailString($new_email)) {
			throw new kUserException('', kUserException::INVALID_EMAIL);
		}
		
		// check if a user with the new email already exists
		if ($new_email && UserLoginDataPeer::getByEmail($new_email)) {
			throw new kUserException('', kUserException::LOGIN_ID_ALREADY_USED);
		}
		
		// if this is an update request (and not just password reset), check that old password is valid
		if ( $requested_password && (!$old_password || !$loginData->isPasswordValid ( $old_password )) )
		{
			throw new kUserException('', kUserException::WRONG_PASSWORD);
		}
		
		// check that new password structure is valid
		if ($requested_password && 
				  !UserLoginDataPeer::isPasswordStructureValid($requested_password) ||
				  (stripos($requested_password, $loginData->getFirstName()) !== false)   ||
				  (stripos($requested_password, $loginData->getLastName()) !== false)    ||
				  (stripos($requested_password, $loginData->getFullName()) !== false)         ){
			throw new kUserException('', kUserException::PASSWORD_STRUCTURE_INVALID);
		}
		
		// check that password hasn't been used before by this user
		if ($requested_password && $loginData->passwordUsedBefore($requested_password)) {
			throw new kUserException('', kUserException::PASSWORD_ALREADY_USED);
		}		
		
		// reset password
		$password = $loginData->resetPassword($requested_password, $old_password);
		
		// update email if requested
		if ( $new_email && $new_email != $loginData->getLoginEmail()) 
		{
			$loginData->setLoginEmail($new_email);
		}
				
		$loginData->save();
		
		// if this is a reset request (not update), send reset password link by email to the user
		if (!$requested_password) {
			self::emailResetPassword(0, $loginData->getLoginEmail(), $loginData->getFullName(), self::getPassResetLink($loginData->getPasswordHashKey()));
		}
		
		return array ( $password , $new_email);
	}
	
	/**
	 * @param string $email
	 * @param string $use_bd
	 * @return UserLoginData
	 */
	public static function getByEmail($email)
	{
		$c = new Criteria();
		$c->add ( UserLoginDataPeer::LOGIN_EMAIL , $email );
		$data = UserLoginDataPeer::doSelectOne( $c );
		return $data;
		
	}
	
	public static function isPasswordStructureValid($pass)
	{
		$regexps = kConf::get('user_login_password_structure');
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
			
	public static function decodePassHashKey($hashKey)
	{
		$decoded = base64_decode($hashKey);
		$params = explode('|', $decoded);
		if (count($params) != 3) {
			return false;
		}
		return $params;
	}
	
	public static function getIdFromHashKey($hashKey)
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
		$id = self::getIdFromHashKey($hashKey);
		if (!$id) {
			throw new kUserException ('', kUserException::LOGIN_DATA_NOT_FOUND);
		}
		$loginData = self::retrieveByPK($id);
		if (!$loginData) {
			throw new kUserException ('', kUserException::LOGIN_DATA_NOT_FOUND);
		}
		
		// might throw an exception
		$valid = $loginData->isPassHashKeyValid($hashKey);
		
		if (!$valid) {
			throw new kUserException ('', kUserException::NEW_PASSWORD_HASH_KEY_INVALID);
		}

		return $loginData;
	}
	
	public static function setInitialPassword($hashKey, $newPassword)
	{
		// might throw exception
		$hashKey = str_replace('.','=', $hashKey);
		$loginData = self::isHashKeyValid($hashKey);
		
		if (!$loginData) {
			throw new kUserException ('', kUserException::NEW_PASSWORD_HASH_KEY_INVALID);
		}
		
		// check password structure
		if (!self::isPasswordStructureValid($newPassword)                 ||
			stripos($newPassword, $loginData->getFirstName() !== false)   ||
			stripos($newPassword, $loginData->getLastName() !== false)         ) {
			throw new kUserException ('', kUserException::PASSWORD_STRUCTURE_INVALID);
		}
		
		// check that password wasn't used before
		if ($loginData->passwordUsedBefore($newPassword)) {
			throw new kUserException ('', kUserException::PASSWORD_ALREADY_USED);
		}
		
		$loginData->setPassword($newPassword);
		$loginData->setLoginAttempts(0);
		$loginData->setLoginBlockedUntil(null);
		$loginData->setPasswordHashKey(null);
		$loginData->save();
		return true;
	}
	
	public static function getPassResetLink($hashKey)
	{
		if (!$hashKey) {
			return null;
		}
		$loginData = self::isHashKeyValid($hashKey);
		if (!$loginData) {
			throw new Exception('Hash key not valid');
		}
		
		$partner = PartnerPeer::retrieveByPK($loginData->getConfigPartnerId());
		if ($partner && $partnerPrefix = $partner->getPassResetUrlPrefix()) {
			// partner has defined a custom reset password url (admin console for example)
			return $partnerPrefix.$hashKey;
		}		
		
		// default password reset url
		return kConf::get('apphome_url').'/index.php/kmc/kmc/setpasshashkey/'.$hashKey;
	}
	
	// user login by user_login_data record id
	public static function userLoginByDataId($loginDataId, $password, $partnerId = null)
	{
		$loginData = self::retrieveByPK($loginDataId);
		if (!$loginData) {
			throw new kUserException('', kUserException::LOGIN_DATA_NOT_FOUND);
		}
		
		return self::userLogin($loginData, $password, $partnerId);
	}
	
	// user login by login_email
	public static function userLoginByEmail($email, $password, $partnerId = null)
	{
		$loginData = self::getByEmail($email);
		if (!$loginData) {
			throw new kUserException('', kUserException::LOGIN_DATA_NOT_FOUND);
		}
		return self::userLogin($loginData, $password, $partnerId);
	}
	
	// user login by user_login_data object
	private static function userLogin(UserLoginData $loginData, $password, $partnerId = null)
	{
		$requestedPartner = $partnerId;
		
		if (!$loginData) {
			throw new kUserException('', kUserException::LOGIN_DATA_NOT_FOUND);
		}		
		
		// check if password is valid
		if (!$loginData->isPasswordValid($password)) 
		{
			if (time() < $loginData->getLoginBlockedUntil(null)) 
			{
				throw new kUserException('', kUserException::LOGIN_BLOCKED);
			}
			if ($loginData->getLoginAttempts()+1 >= $loginData->getMaxLoginAttempts()) 
			{
				$loginData->setLoginBlockedUntil( time() + ($loginData->getLoginBlockPeriod()) );
				$loginData->setLoginAttempts(0);
				$loginData->save();
				throw new kUserException('', kUserException::LOGIN_RETRIES_EXCEEDED);
			}
			$loginData->incLoginAttempts();
			$loginData->save();	
				
			throw new kUserException('', kUserException::WRONG_PASSWORD);
		}
		
		if (time() < $loginData->getLoginBlockedUntil(null)) {
			throw new kUserException('', kUserException::LOGIN_BLOCKED);
		}
		
		$loginData->setLoginAttempts(0);
		$loginData->save();
		$passUpdatedAt = $loginData->getPasswordUpdatedAt(null);
		if ($passUpdatedAt && (time() > $passUpdatedAt + $loginData->getPassReplaceFreq())) {
			throw new kUserException('', kUserException::PASSWORD_EXPIRED);
		}
		if (!$partnerId) {
			$partnerId = $loginData->getLastLoginPartnerId();
		}
		if (!$partnerId) {
			throw new kUserException('', kUserException::INVALID_PARTNER);
		}
		
		$kuser = kuserPeer::getByLoginDataAndPartner($loginData->getId(), $partnerId);
		if (!$kuser || $kuser->getStatus() != kuser::KUSER_STATUS_ACTIVE)
		{
			
			
			// if a specific partner was requested - throw error
			if ($requestedPartner) {
				if ($kuser && $kuser->getStatus() == kuser::KUSER_STATUS_SUSPENDED) {
					throw new kUserException('', kUserException::USER_IS_BLOCKED);
				}
				else {
					throw new kUserException('', kUserException::USER_NOT_FOUND);
				}
			}
			
			$kuser = null;
			
			// if no specific partner was request, but last logged in partner is not available, login to first found partner
			$c = new Criteria();
			$c->addAnd(kuserPeer::LOGIN_DATA_ID, $loginData->getId());
			$c->addAnd(kuserPeer::STATUS, kuser::KUSER_STATUS_DELETED, Criteria::NOT_EQUAL);
			$kuser = kuserPeer::doSelectOne($c);
			
			if ($kuser && $kuser->getStatus() == kuser::KUSER_STATUS_SUSPENDED) {
				throw new kUserException('', kUserException::USER_IS_BLOCKED);
			}
			else {
				throw new kUserException('', kUserException::USER_NOT_FOUND);
			}
		}
		
		$loginData->setLastLoginPartnerId($partnerId);
		$loginData->save();
		
		$kuser->setLastLoginTime(time());
		$kuser->save();
		
		return $kuser;
	}
	
	/**
	 * Adds a new user login data record
	 * @param unknown_type $loginEmail
	 * @param unknown_type $password
	 * @param unknown_type $partnerId
	 * @param unknown_type $firstName
	 * @param unknown_type $lastName
	 * @param bool $checkPasswordStructure backward compatibility - some extensions are registering a partner and setting its first password without checking its structure
	 *
	 * @throws kUserException::INVALID_EMAIL
	 * @throws kUserException::INVALID_PARTNER
	 * @throws kUserException::LOGIN_USERS_QUOTA_EXCEEDED
	 * @throws kUserException::PASSWORD_STRUCTURE_INVALID
	 * @throws kUserException::LOGIN_ID_ALREADY_USED
	 * @throws kUserException::USER_EXISTS_WITH_DIFFERENT_PASSWORD
	 * @throws kUserException::LOGIN_USERS_QUOTA_EXCEEDED
	 */
	public static function addLoginData($loginEmail, $password, $partnerId, $firstName, $lastName, $checkPasswordStructure = true)
	{
		if (!kString::isEmailString($loginEmail)) {
			throw new kUserException('', kUserException::INVALID_EMAIL);
		}
			
		$partner = partnerPeer::retrieveByPK($partnerId);
		if (!$partner) {
			throw new kUserException('', kUserException::INVALID_PARTNER);
		}
				
		$userQuota = $partner->getLoginUsersQuota();
		// check if login users quota exceeded - value -1 means unlimited
		if (is_null($userQuota) || $userQuota != -1 && $userQuota <= $partner->getLoginUsersNumber()) {
			throw new kUserException('', kUserException::LOGIN_USERS_QUOTA_EXCEEDED);
		}
		
		$existingData = self::getByEmail($loginEmail);
		if (!$existingData)
		{
			if ($checkPasswordStructure && !UserLoginDataPeer::isPasswordStructureValid($password)) {
				throw new kUserException('', kUserException::PASSWORD_STRUCTURE_INVALID);
			}
			
			// create a new login data record
			$loginData = new UserLoginData();
			$loginData->setConfigPartnerId($partnerId);
			$loginData->setLoginEmail($loginEmail);
			$loginData->setFirstName($firstName);
			$loginData->setLastName($lastName);
			$loginData->setPassword($password);
			$loginData->setLoginAttempts(0);
			$loginData->setLoginBlockedUntil(null);
			$loginData->resetPreviousPasswords();
			$loginData->setLastLoginPartnerId($partnerId);
			$loginData->save();
			// now $loginData has an id and hash key can be generated
			$hashKey = $loginData->newPassHashKey();
			$loginData->setPasswordHashKey($hashKey);
			$loginData->save();
			return $loginData;			
		}
		else
		{
			// add existing login data if password is valid
			$existingKuser = kuserPeer::getByLoginDataAndPartner($existingData->getId(), $partnerId);
			if ($existingKuser) {
				// partner already has a user with the same login data
				throw new kUserException('', kUserException::LOGIN_ID_ALREADY_USED);
			}
			
			if (!$existingData->isPasswordValid($password)) {
				throw new kUserException('', kUserException::USER_EXISTS_WITH_DIFFERENT_PASSWORD);
			}
			
			KalturaLog::DEBUG('Existing login data with the same email & password exists - returning id ['.$existingData->getId().']');	
			return $existingData;
		}	
	}
	
	/**
	 * 
	 * updates first and last name on the login data record, according to the given kuser object
	 * @param int $loginDataId
	 * @param kuser $kuser
	 * @throws kUserException::LOGIN_DATA_NOT_FOUND
	 */
	public static function updateFromUserDetails($loginDataId, kuser $kuser)
	{
		$loginData = self::retrieveByPK($loginDataId);
		if (!$loginData) {
			throw new kUserException('', kUserException::LOGIN_DATA_NOT_FOUND);
		}
		
		$loginData->setFirstName($kuser->getFirstName());
		$loginData->setLastName($kuser->getLastName());
		$loginData->save();	
	}
	
	
	public static function notifyOneLessUser($loginDataId)
	{
		if (!$loginDataId) {
			return;
		}
		
		kuserPeer::setUseCriteriaFilter(false);
		$c = new Criteria();
		$c->addAnd(kuserPeer::PARTNER_ID, null, Criteria::NOT_EQUAL);
		$c->addAnd(kuserPeer::LOGIN_DATA_ID, $loginDataId);
		$c->addAnd(kuserPeer::STATUS, kuser::KUSER_STATUS_DELETED, Criteria::NOT_EQUAL);
		$countUsers = kuserPeer::doCount($c);
		kuserPeer::setUseCriteriaFilter(true);
		
		if ($countUsers <= 0) {
			$loginData = self::retrieveByPK($loginDataId);
			$loginData->delete();
		}
		
		
	}
	
	
} // UserLoginDataPeer
