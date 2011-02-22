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
 * @package Core
 * @subpackage model
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
	
	public static function updateLoginData($oldLoginEmail, $oldPassword, $newLoginEmail = null, $newPassword = null, $newFirstName = null, $newLastName = null)
	{
		// if email is null, no need to do any DB queries
		if (!$oldLoginEmail) {
			throw new kUserException('', kUserException::LOGIN_DATA_NOT_FOUND);
		}
		
		if ($newLoginEmail === $oldLoginEmail) {
			$newLoginEmail = null;
		}
		
		$c = new Criteria(); 
		$c->add(UserLoginDataPeer::LOGIN_EMAIL, $oldLoginEmail ); 
		$loginData = UserLoginDataPeer::doSelectOne($c);
		
		// check if login data exists
		if (!$loginData) {
			throw new kUserException('', kUserException::LOGIN_DATA_NOT_FOUND);
		}
		
		// check if the email string is a valid email
		if ($newLoginEmail && !kString::isEmailString($newLoginEmail)) {
			throw new kUserException('', kUserException::INVALID_EMAIL);
		}
		
		// check if a user with the new email already exists
		if ($newLoginEmail && UserLoginDataPeer::getByEmail($newLoginEmail)) {
			throw new kUserException('', kUserException::LOGIN_ID_ALREADY_USED);
		}
		
		// if this is an update request (and not just password reset), check that old password is valid
		if ( ($newPassword || $newLoginEmail || $newFirstName || $newLastName) && (!$oldPassword || !$loginData->isPasswordValid ( $oldPassword )) )
		{
			throw new kUserException('', kUserException::WRONG_PASSWORD);
		}
		
		// check that new password structure is valid
		if ($newPassword && 
				  !UserLoginDataPeer::isPasswordStructureValid($newPassword) ||
				  (stripos($newPassword, $loginData->getFirstName()) !== false)   ||
				  (stripos($newPassword, $loginData->getLastName()) !== false)    ||
				  (stripos($newPassword, $loginData->getFullName()) !== false)         ){
			throw new kUserException('', kUserException::PASSWORD_STRUCTURE_INVALID);
		}
		
		// check that password hasn't been used before by this user
		if ($newPassword && $loginData->passwordUsedBefore($newPassword)) {
			throw new kUserException('', kUserException::PASSWORD_ALREADY_USED);
		}		
		 
		// update password if requested
		if ($newPassword && $newPassword != $oldPassword) {
			$password = $loginData->resetPassword($newPassword, $oldPassword);
		}
		
		// update email if requested
		if ($newLoginEmail || $newFirstName || $newLastName)
		{
			if ($newLoginEmail) { $loginData->setLoginEmail($newLoginEmail); } // update login email
			if ($newFirstName)  { $loginData->setFirstName($newFirstName);   } // update first name
			if ($newLastName)   { $loginData->setLastName($newLastName);     } // update last name
			
			// update all kusers using this login data, in all partners
			$c = new Criteria();
			$c->addAnd(kuserPeer::LOGIN_DATA_ID, $loginData->getId(), Criteria::EQUAL);
			$c->addAnd(kuserPeer::STATUS, KuserStatus::DELETED, Criteria::NOT_EQUAL);
			kuserPeer::setUseCriteriaFilter(false);
			$kusers = kuserPeer::doSelect($c);
			kuserPeer::setUseCriteriaFilter(true);
			foreach ($kusers as $kuser)
			{
				if ($newLoginEmail) { $kuser->setEmail($newLoginEmail);    } // update login email
				if ($newFirstName)  { $kuser->setFirstName($newFirstName); } // update first name
				if ($newLastName)   { $kuser->setLastName($newLastName);   } // update last name
				$kuser->save();
			}
		}
				
		$loginData->save();
		
		return $loginData;
	}
		

	
	public static function resetUserPassword($email)
	{
		$c = new Criteria(); 
		$c->add(UserLoginDataPeer::LOGIN_EMAIL, $email ); 
		$loginData = UserLoginDataPeer::doSelectOne($c);
		
		// check if login data exists
		if (!$loginData) {
			throw new kUserException('', kUserException::LOGIN_DATA_NOT_FOUND);
		}
		
		$loginData->setPasswordHashKey($loginData->newPassHashKey());
		$loginData->save();
				
		self::emailResetPassword(0, $loginData->getLoginEmail(), $loginData->getFullName(), self::getPassResetLink($loginData->getPasswordHashKey()));
		return true;
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
		
		$loginData->resetPassword($newPassword);
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
		
		$resetLinksArray = kConf::get('password_reset_links');
		$resetLinkPrefix = $resetLinksArray['default'];		
		
		$partner = PartnerPeer::retrieveByPK($loginData->getConfigPartnerId());
		if ($partner) {
			// partner may define a custom reset password url (admin console for example)
			$urlPrefixName = $partner->getPassResetUrlPrefixName();
			if ($urlPrefixName && isset($resetLinksArray[$urlPrefixName]))
			{
				$resetLinkPrefix = $resetLinksArray[$urlPrefixName];
			}
		}	
		
		return $resetLinkPrefix.$hashKey;
	}
	
	// user login by user_login_data record id
	public static function userLoginByDataId($loginDataId, $password, $partnerId = null)
	{
		$loginData = self::retrieveByPK($loginDataId);
		if (!$loginData) {
			throw new kUserException('', kUserException::LOGIN_DATA_NOT_FOUND);
		}
		
		return self::userLogin($loginData, $password, $partnerId, true);
	}
	
	// user login by login_email
	public static function userLoginByEmail($email, $password, $partnerId = null)
	{
		$loginData = self::getByEmail($email);
		if (!$loginData) {
			throw new kUserException('', kUserException::LOGIN_DATA_NOT_FOUND);
		}
		return self::userLogin($loginData, $password, $partnerId, true);
	}
	
	// user login by ks
	public static function userLoginByKs($ks, $requestedPartnerId, $useOwnerIfNoUser = false)
	{
		$ksObj = kSessionUtils::crackKs($ks);
		
		$ksUserId = $ksObj->user;
		$ksPartnerId = $ksObj->partner_id;
		
		if (!$ksUserId && $useOwnerIfNoUser)
		{
			KalturaLog::log('No user id on KS, trying to login as the account owner');
			$partner = PartnerPeer::retrieveByPK($ksPartnerId);
			if (!$partner) {
				throw new kUserException('Invalid partner id ['.$ksPartnerId.']', kUserException::INVALID_PARTNER);
			}
			$ksUserId = $partner->getAccountOwnerKuserId();
		}
		
		$kuser = kuserPeer::getKuserByPartnerAndUid($ksPartnerId, $ksUserId, true);
		if (!$kuser)
		{
			throw new kUserException('User with id ['.$ksUserId.'] was not found for partner with id ['.$ksPartnerId.']', kUserException::USER_NOT_FOUND);
		}
			
		return self::userLogin($kuser->getLoginData(), null, $requestedPartnerId, false);  // don't validate password		
	}
	
	// user login by user_login_data object
	private static function userLogin(UserLoginData $loginData = null, $password, $partnerId = null, $validatePassword = true)
	{
		$requestedPartner = $partnerId;
		
		if (!$loginData) {
			throw new kUserException('', kUserException::LOGIN_DATA_NOT_FOUND);
		}		
		
		// check if password is valid
		if ($validatePassword && !$loginData->isPasswordValid($password)) 
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
		
		$partner = PartnerPeer::retrieveByPK($partnerId);		
		$kuser = kuserPeer::getByLoginDataAndPartner($loginData->getId(), $partnerId);
		
		if (!$kuser || $kuser->getStatus() != KuserStatus::ACTIVE || !$partner || $partner->getStatus() != Partner::PARTNER_STATUS_ACTIVE)
		{
			// if a specific partner was requested - throw error
			if ($requestedPartner) {
				if ($partner && $partner->getStatus() != Partner::PARTNER_STATUS_ACTIVE) {
					throw new kUserException('', kUserException::USER_IS_BLOCKED);
				}
				else if ($kuser && $kuser->getStatus() == KuserStatus::BLOCKED) {
					throw new kUserException('', kUserException::USER_IS_BLOCKED);
				}
				else {
					throw new kUserException('', kUserException::USER_NOT_FOUND);
				}
			}
			
			// if kuser was found, keep status for following exception message
			$kuserStatus = $kuser ? $kuser->getStatus() : null;
			
			// if no specific partner was requested, but last logged in partner is not available, login to first found partner
			$kuser = null;
			$kuser = self::findFirstValidKuser($loginData->getId(), $partnerId);
			
			if (!$kuser) {
				if ($kuserStatus === KuserStatus::BLOCKED) {
					throw new kUserException('', kUserException::USER_IS_BLOCKED);
				}
				throw new kUserException('', kUserException::USER_NOT_FOUND);
			}
		}
		
		if ($kuser->getIsAdmin() && !in_array($kuser->getPartnerId(), kConf::get('no_save_of_last_login_partner_for_partner_ids'))) {
			$loginData->setLastLoginPartnerId($kuser->getPartnerId());
		}
		$loginData->save();
		
		$kuser->setLastLoginTime(time());
		$kuser->save();
		
		return $kuser;
	}
	
	
	
	private static function findFirstValidKuser($loginDataId, $notPartnerId = null)
	{
		$c = new Criteria();
		$c->addAnd(kuserPeer::LOGIN_DATA_ID, $loginDataId);
		$c->addAnd(kuserPeer::STATUS, KuserStatus::ACTIVE, Criteria::EQUAL);
		if ($notPartnerId) {
			$c->addAnd(kuserPeer::PARTNER_ID, $notPartnerId, Criteria::NOT_EQUAL);
		}
		$c->addAscendingOrderByColumn(kuserPeer::PARTNER_ID);
		
		$kusers = kuserPeer::doSelect($c);
						
		foreach ($kusers as $kuser)
		{
			if ($kuser->getStatus() != KuserStatus::ACTIVE)
			{
				continue;
			}
			$partner = PartnerPeer::retrieveByPK($kuser->getPartnerId());
			if (!$partner || $partner->getStatus() != Partner::PARTNER_STATUS_ACTIVE)
			{
				continue;
			}
			
			return $kuser;
		}
		
		return null;
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
	 * @throws kUserException::PASSWORD_STRUCTURE_INVALID
	 * @throws kUserException::LOGIN_ID_ALREADY_USED
	 * @throws kUserException::ADMIN_LOGIN_USERS_QUOTA_EXCEEDED
	 */
	public static function addLoginData($loginEmail, $password, $partnerId, $firstName, $lastName, $isAdminUser, $checkPasswordStructure = true, &$alreadyExisted = null)
	{
		if (!kString::isEmailString($loginEmail)) {
			throw new kUserException('', kUserException::INVALID_EMAIL);
		}
			
		$partner = partnerPeer::retrieveByPK($partnerId);
		if (!$partner) {
			throw new kUserException('', kUserException::INVALID_PARTNER);
		}
		
		if ($isAdminUser)
		{
			$userQuota = $partner->getAdminLoginUsersQuota();
			$adminLoginUsersNum = $partner->getAdminLoginUsersNumber();
			// check if login users quota exceeded - value -1 means unlimited
			if ($adminLoginUsersNum  && (is_null($userQuota) || ($userQuota != -1 && $userQuota <= $adminLoginUsersNum))) {
				throw new kUserException('', kUserException::ADMIN_LOGIN_USERS_QUOTA_EXCEEDED);
			}
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
			$loginData->save();
			// now $loginData has an id and hash key can be generated
			$hashKey = $loginData->newPassHashKey();
			$loginData->setPasswordHashKey($hashKey);
			$loginData->save();
			$alreadyExisted = false;
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
						
			KalturaLog::debug('Existing login data with the same email & password exists - returning id ['.$existingData->getId().']');	
			$alreadyExisted = true;
			
			if ($isAdminUser && !$existingData->isLastLoginPartnerIdSet()) {
				$existingData->setLastLoginPartnerId($partnerId);
				$existingData->save();
			}
			
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
		$c->addAnd(kuserPeer::STATUS, KuserStatus::DELETED, Criteria::NOT_EQUAL);
		$countUsers = kuserPeer::doCount($c);
		kuserPeer::setUseCriteriaFilter(true);
		
		if ($countUsers <= 0) {
			$loginData = self::retrieveByPK($loginDataId);
			$loginData->delete();
		}
		
		
	}
	
	
} // UserLoginDataPeer
