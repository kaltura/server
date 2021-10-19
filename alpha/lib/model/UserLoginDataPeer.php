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
class UserLoginDataPeer extends BaseUserLoginDataPeer implements IRelatedObjectPeer
{
	const KALTURAS_CMS_PASSWORD_RESET = 51;
	const LAST_LOGIN_TIME_UPDATE_INTERVAL = 600; // 10 Minutes
	const OTP_MISSING = 'otp is missing';
	const OTP_INVALID = 'otp is invalid';
	
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
	
	public static function updateLoginData($oldLoginEmail, $oldPassword, $newLoginEmail = null, $newPassword = null, $newFirstName = null, $newLastName = null, $otp = null)
	{
		// if email is null, no need to do any DB queries
		if (!$oldLoginEmail) {
			throw new kUserException('', kUserException::LOGIN_DATA_NOT_FOUND);
		}

		$c = new Criteria(); 
		$c->add(UserLoginDataPeer::LOGIN_EMAIL, $oldLoginEmail ); 
		$loginData = UserLoginDataPeer::doSelectOne($c);
		
		// check if login data exists
		if (!$loginData) {
			throw new kUserException('', kUserException::LOGIN_DATA_NOT_FOUND);
		}
		
		// if this is an update request (and not just password reset), check that old password is valid
		if ( ($newPassword || $newLoginEmail || $newFirstName || $newLastName) && (!$oldPassword || !$loginData->isPasswordValid ( $oldPassword )) )
		{
			return self::loginAttemptsLogic($loginData);
		}
		
		// no need to query the DB if login email is the same
		if ($newLoginEmail === $oldLoginEmail) {
			$newLoginEmail = null;
		}
		
		// check if the email string is a valid email
		if ($newLoginEmail && !kString::isEmailString($newLoginEmail)) {
			throw new kUserException('', kUserException::INVALID_EMAIL);
		}
		
		// check if a user with the new email already exists
		if ($newLoginEmail && UserLoginDataPeer::getByEmail($newLoginEmail)) {
			throw new kUserException('', kUserException::LOGIN_ID_ALREADY_USED);
		}

		self::checkPasswordValidation($newPassword, $loginData);
		
		self::validate2FA($loginData, $otp);
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

	protected static function validate2FA($loginData, $otp)
	{

		$dbUser =  kuserPeer::getAdminUser($loginData->getConfigPartnerId(), $loginData);
		if ($dbUser && $loginData->isTwoFactorAuthenticationRequired($dbUser))
		{
			if(!$otp)
			{
				try
				{
					self::loginAttemptsLogic($loginData);
				}
				catch (kUserException $e)
				{
					$code = $e->getCode();
					if ($code == kUserException::WRONG_PASSWORD)
					{
						throw new kUserException (self::OTP_MISSING, kUserException::MISSING_OTP);
					}
					throw $e;
				}

			}
			$result = authenticationUtils::verify2FACode($loginData, $otp);
			if (!$result)
			{
				try
				{
					self::loginAttemptsLogic($loginData);
				}
				catch (kUserException $e)
				{
					$code = $e->getCode();
					if ($code == kUserException::WRONG_PASSWORD)
					{
						throw new kUserException (self::OTP_INVALID, kUserException::INVALID_OTP);
					}
					throw $e;
				}
			}
		}
	}

	public static function checkPasswordValidation($newPassword, $loginData) {
		// check that new password structure is valid
		if ($newPassword &&
			!UserLoginDataPeer::isPasswordStructureValid($newPassword,$loginData->getConfigPartnerId()) ||
			(strlen($loginData->getFirstName()) > 2 && (stripos($newPassword, $loginData->getFirstName())) !== false) ||
			(strlen($loginData->getLastName()) > 2 && (stripos($newPassword, $loginData->getLastName())) !== false) ||
			(stripos($newPassword, $loginData->getFullName()) !== false) ||
			($newPassword == $loginData->getLoginEmail()))
		{
			throw new kUserException('', kUserException::PASSWORD_STRUCTURE_INVALID);
		}
		
		if ($loginData->isCommonPassword($newPassword))
		{
			throw new kUserException('', kUserException::COMMON_PASSWORD_NOT_ALLOWED);
		}
		
		// check that password hasn't been used before by this user
		if ($newPassword && $loginData->passwordUsedBefore($newPassword)) {
			throw new kUserException('', kUserException::PASSWORD_ALREADY_USED);
		}
	}

		

	
	public static function resetUserPassword($email, $linkType = resetPassLinkType::KMC)
	{
		$c = new Criteria(); 
		$c->add(UserLoginDataPeer::LOGIN_EMAIL, $email ); 
		$loginData = UserLoginDataPeer::doSelectOne($c);
		
		// check if login data exists
		if (!$loginData) {
			throw new kUserException('', kUserException::LOGIN_DATA_NOT_FOUND);
		}
		
		$partnerId = $loginData->getConfigPartnerId();
		$partner = PartnerPeer::retrieveByPK($partnerId);
		// If on the partner it's set not to reset the password - skip the email sending
		if($partner->getEnabledService(PermissionName::FEATURE_DISABLE_RESET_PASSWORD_EMAIL)) {
			KalturaLog::log("Skipping reset-password email sending according to partner configuration.");
			return true;
		}
		
		$loginData->setPasswordHashKey($loginData->newPassHashKey());
		$loginData->save();
				
		self::emailResetPassword(0, $loginData->getLoginEmail(), $loginData->getFullName(), self::getPassResetLink($loginData->getPasswordHashKey(), $linkType));
		return true;
	}
	
	/**
	 * @param string $email
	 * @return UserLoginData
	 */
	public static function getByEmail($email)
	{
		$c = new Criteria();
		$c->add ( UserLoginDataPeer::LOGIN_EMAIL , $email );
		$data = UserLoginDataPeer::doSelectOne( $c );
		return $data;
		
	}
	
	public static function isPasswordStructureValid($pass,$partnerId = null)
	{
		if(kCurrentContext::getCurrentPartnerId() == Partner::ADMIN_CONSOLE_PARTNER_ID)
			return true;
			
		$regexps = kConf::get('user_login_password_structure');
		if($partnerId){
			$partner = PartnerPeer::retrieveByPK($partnerId);
			if($partner && $partner->getPasswordStructureRegex())
				$regexps = $partner->getPasswordStructureRegex();
		}
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
		
		self::checkPasswordValidation($newPassword, $loginData);
		
		$loginData->resetPassword($newPassword);
		myPartnerUtils::initialPasswordSetForFreeTrial($loginData);

		kuserPeer::setUseCriteriaFilter(false);
		$dbUser = kuserPeer::getByLoginDataAndPartner($loginData->getId(), $loginData->getConfigPartnerId());
		kuserPeer::setUseCriteriaFilter(true);
		if (!$dbUser)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $loginData->getLoginEmail());
		}

		if($loginData->isTwoFactorAuthenticationRequired($dbUser))
		{
			authenticationUtils::generateNewSeed($loginData);
			return authenticationUtils::getQRImage($dbUser, $loginData);
		}

		return true;
	}
	
	public static function getPassResetLink($hashKey, $linkType = resetPassLinkType::KMC)
	{
		if (!$hashKey) {
			return null;
		}
		$loginData = self::isHashKeyValid($hashKey);
		if (!$loginData) {
			throw new Exception('Hash key not valid');
		}

		$partnerId = $loginData->getConfigPartnerId();

		$resetLinksArray = kConf::get('password_reset_links');
		if($linkType == resetPassLinkType::KMS)
		{
			$resetLinkPrefix = $resetLinksArray['kms'];
			$resetLinkPrefix = vsprintf($resetLinkPrefix, array($partnerId) );
		}
		else
		{
			$resetLinkPrefix = $resetLinksArray['default'];
		}

		$partner = PartnerPeer::retrieveByPK($partnerId);
		if ($partner) {
			// partner may define a custom reset password url (admin console for example)
			$urlPrefixName = $partner->getPassResetUrlPrefixName();
			if ($urlPrefixName && isset($resetLinksArray[$urlPrefixName]))
			{
				$resetLinkPrefix = $resetLinksArray[$urlPrefixName];
			}
		}

		$httpsEnforcePermission = PermissionPeer::isValidForPartner(PermissionName::FEATURE_KMC_ENFORCE_HTTPS, $partnerId);
		if(strpos($resetLinkPrefix, infraRequestUtils::PROTOCOL_HTTPS) === false && $httpsEnforcePermission)
			$resetLinkPrefix = str_replace(infraRequestUtils::PROTOCOL_HTTP , infraRequestUtils::PROTOCOL_HTTPS , $resetLinkPrefix);

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
	public static function userLoginByEmail($email, $password, $partnerId = null, $otp = null)
	{
		$loginData = self::getByEmail($email);
		if (!$loginData) {
			throw new kUserException('', kUserException::LOGIN_DATA_NOT_FOUND);
		}
		return self::userLogin($loginData, $password, $partnerId, true, $otp);
	}
	
	// user login by ks
	public static function userLoginByKs($ks, $requestedPartnerId, $useOwnerIfNoUser = false)
	{
		$ksObj = kSessionUtils::crackKs($ks);
		
		$ksUserId = $ksObj->user;
		$ksPartnerId = $ksObj->partner_id;
		$kuser = null;

		$partner = PartnerPeer::retrieveByPK($ksPartnerId);
		if (!$partner)
		{
			throw new kUserException('Invalid partner id ['.$ksPartnerId.']', kUserException::INVALID_PARTNER);
		}

		if ((is_null($ksUserId) || $ksUserId === '') && $useOwnerIfNoUser)
		{
			$ksUserId = $partner->getAccountOwnerKuserId();
			$kuser = kuserPeer::retrieveByPK($ksUserId);
		}
		
		if (!$kuser) {
			$kuser = kuserPeer::getKuserByPartnerAndUid($ksPartnerId, $ksUserId, true);
		}
		if (!$kuser)
		{
			throw new kUserException('User with id ['.$ksUserId.'] was not found for partner with id ['.$ksPartnerId.']', kUserException::USER_NOT_FOUND);
		}

		$requestedPartner = PartnerPeer::retrieveByPK($requestedPartnerId);
		if (!$requestedPartner)
		{
			throw new kUserException('Invalid partner id ['.$requestedPartnerId.']', kUserException::INVALID_PARTNER);
		}
		self::verifyAuthenticatedPartnerSwitch($partner, $requestedPartner);
			
		return self::userLogin($kuser->getLoginData(), null, $requestedPartnerId, false, null, false);  // don't validate password
	}


	// user login by user_login_data object
	private static function userLogin(UserLoginData $loginData = null, $password, $partnerId = null, $validatePassword = true, $otp = null, $validateOtp = true)
	{
		$requestedPartner = $partnerId;
		$kuser = null;
		
		if (!$loginData) {
			throw new kUserException('', kUserException::LOGIN_DATA_NOT_FOUND);
		}		
		
		// check if password is valid
		if ($validatePassword && !$loginData->isPasswordValid($password)) 
		{
			return self::loginAttemptsLogic($loginData);
		}
		
		if (time() < $loginData->getLoginBlockedUntil(null)) {
			throw new kUserException('', kUserException::LOGIN_BLOCKED);
		}

		//Check if the user's ip address is in the right range to ignore the otp
		$otpRequired = false;
		if(kConf::hasParam ('otp_required_partners') && 
			in_array ($partnerId, kConf::get ('otp_required_partners')) &&
			kConf::hasParam ('partner_otp_internal_ips'))
		{
			$otpRequired = true;
			$ipRanges = explode(',', kConf::get('partner_otp_internal_ips'));
			foreach ($ipRanges as $curRange)
			{
				if (kIpAddressUtils::isIpInRange(infraRequestUtils::getRemoteAddress(), $curRange))
				{
					$otpRequired = false;
					break;
				}
			}
		}

		if (!$partnerId)
		{
			$partnerId = $loginData->getLastLoginPartnerId();
		}
		if (!$partnerId)
		{
			throw new kUserException('', kUserException::INVALID_PARTNER);
		}
		$partner = PartnerPeer::retrieveByPK($partnerId);

		if($partner && $partner->getBlockDirectLogin())
		{
			throw new kUserException('Direct login is blocked', kUserException::DIRECT_LOGIN_BLOCKED);
		}
		if($validateOtp && $partner && $partner->getUseTwoFactorAuthentication())
		{
			$otpRequired = true;
		}

		if($otpRequired && $partner->getTwoFactorAuthenticationMode() != TwoFactorAuthenticationMode::ALL)
		{
			$kuser = kuserPeer::getByLoginDataAndPartner($loginData->getId(), $partnerId);
			if ($kuser)
			{
				if($partner->getTwoFactorAuthenticationMode()==TwoFactorAuthenticationMode::ADMIN_USERS_ONLY)
				{
					$otpRequired = $kuser->getIsAdmin();
				}
				if($partner->getTwoFactorAuthenticationMode()==TwoFactorAuthenticationMode::NON_ADMIN_USERS_ONLY)
				{
					$otpRequired = !$kuser->getIsAdmin();
				}
			}
		}
		
		if ($otpRequired)
		{
			if(!$otp)
			{
				try
				{
					self::loginAttemptsLogic($loginData);
				}
				catch (kUserException $e)
				{
					$code = $e->getCode();
					if ($code == kUserException::WRONG_PASSWORD)
					{
						throw new kUserException ('otp is missing', kUserException::MISSING_OTP);
					}
					throw $e;
				}

			}
			$result = authenticationUtils::verify2FACode($loginData, $otp);
			if (!$result)
			{
				try
				{
					self::loginAttemptsLogic($loginData);
				}
				catch (kUserException $e)
				{
					$code = $e->getCode();
					if ($code == kUserException::WRONG_PASSWORD)
					{
						throw new kUserException ('otp is invalid', kUserException::INVALID_OTP);
					}
					throw $e;
				}
			}
		}

		$loginData->setLoginAttempts(0);
		$loginData->save();
		$passUpdatedAt = $loginData->getPasswordUpdatedAt(null);
		if ($passUpdatedAt && (time() > $passUpdatedAt + $loginData->getPassReplaceFreq())) {
			throw new kUserException('', kUserException::PASSWORD_EXPIRED);
		}

		if(is_null($kuser))
		{
			$kuser = kuserPeer::getByLoginDataAndPartner($loginData -> getId(), $partnerId);
		}
		
		if (!$kuser || $kuser->getStatus() != KuserStatus::ACTIVE || !$partner || $partner->getStatus() != Partner::PARTNER_STATUS_ACTIVE)
		{
			// if a specific partner was requested - throw error
			if ($requestedPartner) {
				if ($partner && $partner->getStatus() != Partner::PARTNER_STATUS_ACTIVE) {
					throw new kUserException('Partner is blocked', kUserException::USER_IS_BLOCKED);
				}
				else if ($kuser && $kuser->getStatus() == KuserStatus::BLOCKED) {
					throw new kUserException('User is blocked', kUserException::USER_IS_BLOCKED);
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

		return self::setLastLoginFields($loginData, $kuser);
	}

	public static function loginAttemptsLogic($loginData)
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

	public static function setLastLoginFields($loginData, $kuser)
	{
		$userLoginEmailToIgnore =  kConf::getMap('UserLoginNoUpdate');
		$ignoreUser = isset ($userLoginEmailToIgnore[$loginData->getLoginEmail()]);
		$isAdmin = $kuser->getIsAdmin();
		$updateTimeLimit = $loginData->getUpdatedAt(null) + 5 < time();
		$ignorePartner = in_array($kuser->getPartnerId(), kConf::get('no_save_of_last_login_partner_for_partner_ids'));
		if ($isAdmin && !$ignoreUser && $updateTimeLimit && !$ignorePartner)
		{
			$loginData->setLastLoginPartnerId($kuser->getPartnerId());
		}
		$loginData->save();
		
		$currentTime = time();
		$dbLastLoginTime = $kuser->getLastLoginTime();
		if(!$ignoreUser && (!$dbLastLoginTime || $dbLastLoginTime < $currentTime - self::LAST_LOGIN_TIME_UPDATE_INTERVAL))
			$kuser->setLastLoginTime($currentTime);
		
		$kuser->save();
		return $kuser;
	}
	
	
	
	private static function findFirstValidKuser($loginDataId, $notPartnerId = null)
	{
		$c = new Criteria();
		$c->addAnd(kuserPeer::LOGIN_DATA_ID, $loginDataId);
		$c->addAnd(kuserPeer::STATUS, KuserStatus::ACTIVE, Criteria::EQUAL);
		$c->addAnd(kuserPeer::PARTNER_ID, PartnerPeer::GLOBAL_PARTNER, Criteria::GREATER_THAN);
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
			
		$partner = PartnerPeer::retrieveByPK($partnerId);
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
			// create a new login data record
			$loginData = new UserLoginData();
			$loginData->setConfigPartnerId($partnerId);
			$loginData->setLoginEmail($loginEmail);
			$loginData->setFirstName($firstName);
			$loginData->setLastName($lastName);
			
			if ($checkPasswordStructure)
			{
				self::checkPasswordValidation($password, $loginData);
			}
			
			$loginData->setPassword($password);
			$loginData->setLoginAttempts(0);
			$loginData->setLoginBlockedUntil(null);
			$loginData->resetPreviousPasswords();
			
			$loginData->save();
			// now $loginData has an id and hash key can be generated
			$hashKey = $loginData->newPassHashKey();
			$loginData->setPasswordHashKey($hashKey);
			
			if ($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID)
			{
				// add google authenticator library to include path
				require_once KALTURA_ROOT_PATH . '/vendor/phpGangsta/GoogleAuthenticator.php';
				//generate a new secret for user's admin console logins
				$seed = GoogleAuthenticator::createSecret();
				$loginData->setSeedFor2FactorAuth($seed);
			}
			else
			{
				self::add2faSeed($partner,$isAdminUser,$loginData);
			}
			
			
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
			
			if ($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID)
			{
				// add google authenticator library to include path
				require_once KALTURA_ROOT_PATH . '/vendor/phpGangsta/GoogleAuthenticator.php';
				//generate a new secret for user's admin console logins
				$existingData->setSeedFor2FactorAuth(GoogleAuthenticator::createSecret());
				$existingData->save();
			}
			
			KalturaLog::info('Existing login data with the same email & password exists - returning id ['.$existingData->getId().']');	
			$alreadyExisted = true;
			
			if ($isAdminUser && !$existingData->isLastLoginPartnerIdSet()) {
				$existingData->setLastLoginPartnerId($partnerId);
				$existingData->save();
			}
			
			return $existingData;
		}	
	}
	
	protected static function add2faSeed($partner, $isAdminUser, $userLoginData)
	{
		$generateNewSeed = false;
		if ($partner->getUseTwoFactorAuthentication())
		{
			switch($partner->getTwoFactorAuthenticationMode())
			{
				case TwoFactorAuthenticationMode::ALL:
					$generateNewSeed=true;
					break;
				
				case TwoFactorAuthenticationMode::ADMIN_USERS_ONLY:
					$generateNewSeed = $isAdminUser;
					break;
				
				case TwoFactorAuthenticationMode::NON_ADMIN_USERS_ONLY;
					$generateNewSeed = !$isAdminUser;
					break;
				
				default:
					$generateNewSeed=false;
					break;
			}

			if($generateNewSeed)
			{
				require_once KALTURA_ROOT_PATH . '/vendor/phpGangsta/GoogleAuthenticator.php';
				$userLoginData->setSeedFor2FactorAuth(GoogleAuthenticator::createSecret());
			}
		}
		return $generateNewSeed;
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
			if($loginData)
				$loginData->delete();
		}
		
		
	}
	
	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::getRootObjects()
	 */
	public function getRootObjects(IRelatedObject $object)
	{
		return array();
	}

	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::isReferenced()
	 */
	public function isReferenced(IRelatedObject $object)
	{
		return true;
	}
	public static function getCacheInvalidationKeys()
	{
		return array(array("userLoginData:id=%s", self::ID), array("userLoginData:loginEmail=%s", self::LOGIN_EMAIL));		
	}

	public static function getAuthInfoLink($hashKey)
	{
		if (!$hashKey)
		{
			return null;
		}
		$loginData = self::isHashKeyValid($hashKey);
		if (!$loginData)
		{
			throw new kCoreException('Hash key not valid', kCoreException::INVALID_HASH);
		}

		$partnerId = $loginData->getConfigPartnerId();
		$resetLinksArray = kConf::get('password_reset_links');
		$qrLink = $resetLinksArray['qr_page'];

		$httpsEnforcePermission = PermissionPeer::isValidForPartner(PermissionName::FEATURE_KMC_ENFORCE_HTTPS, $partnerId);
		if(strpos($qrLink, infraRequestUtils::PROTOCOL_HTTPS) === false && $httpsEnforcePermission)
			$qrLink = str_replace(infraRequestUtils::PROTOCOL_HTTP , infraRequestUtils::PROTOCOL_HTTPS , $qrLink);

		return $qrLink.$hashKey;
	}


	protected static function verifyAuthenticatedPartnerSwitch($originPartner, $requestedPartner)
	{
		$originPartnerAuthType = $originPartner->getAuthenticationType();
		$requestedPartnerAuthType = $requestedPartner->getAuthenticationType();
		if ($requestedPartnerAuthType === PartnerAuthenticationType::SSO)
		{
			throw new kUserException ('Switching to requested partner requires re-login', kUserException::NEW_LOGIN_REQUIRED);
		}
		if($originPartnerAuthType !== $requestedPartnerAuthType)
		{
			if($requestedPartnerAuthType !== PartnerAuthenticationType::PASSWORD_ONLY)
			{
				throw new kUserException ('Switching to requested partner requires re-login', kUserException::NEW_LOGIN_REQUIRED);
			}
		}
	}

	public static function getPartnerIdFromLoginData($email)
	{
		$loginData = UserLoginDataPeer::getByEmail($email);
		if (!$loginData)
		{
			throw new kUserException('', kUserException::LOGIN_DATA_NOT_FOUND);
		}
		$partnerId = $loginData->getLastLoginPartnerId() ? $loginData->getLastLoginPartnerId() : $loginData->getConfigPartnerId();
		return $partnerId;
	}

} // UserLoginDataPeer
