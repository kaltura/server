<?php


/**
 * Skeleton subclass for representing a row from the 'user_login_data' table.
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
class UserLoginData extends BaseUserLoginData
{
	const SHA1 = 'sha1';
	const PASSWORD_ARGON2ID = 'argon2id';
	const PASSWORD_ARGON2I = 'argon2i';

	public function getFullName()
	{
		return trim($this->getFirstName() . ' ' . $this->getLastName());
	}
	
	public function setLastLoginPartnerId($partnerId)
	{
		$this->putInCustomData('last_login_partner_id', $partnerId);
	}
	
	public function getLastLoginPartnerId()
	{
		$lastPartner =  $this->getFromCustomData('last_login_partner_id');
		if (!$lastPartner) {
			$lastPartner = $this->config_partner_id;
		}
		return $lastPartner;
	}
	
	public function getConfigPartnerId()
	{
		$configPartner = PartnerPeer::retrieveByPK($this->config_partner_id);
		if ($configPartner && $configPartner->getStatus() != Partner::PARTNER_STATUS_ACTIVE)
		{
			return $this->getLastloginPartnerId();
		}
		return $this->config_partner_id;
	}
	
	public function isLastLoginPartnerIdSet()
	{
		return !is_null($this->getFromCustomData('last_login_partner_id'));
	}
	
	public function setPassword($password, $setUpdatedAt = true)
	{
		$passwordHashingAlgo = $this->getDefaultPasswordHashAlgo();
		switch ($passwordHashingAlgo)
		{
			case self::PASSWORD_ARGON2I:
				$hashedPassword = password_hash($password, PASSWORD_ARGON2I);
				$this->setUserPassword($hashedPassword);
				break;
			case self::PASSWORD_ARGON2ID:
				$hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
				$this->setUserPassword($hashedPassword);
				break;

			case self::SHA1:
			default:
				$salt = md5(rand(100000, 999999).$this->getLoginEmail());
				$this->setSalt($salt);
				$this->setSha1Password(sha1($salt.$password));
				break;

		}

		$this->setUserPasswordHashAlgo($passwordHashingAlgo);
		if($setUpdatedAt)
		{
			$this->setPasswordUpdatedAt(time());
		}
	} 

	public function isPasswordValid($password_to_match)
	{
		$result = false;
		$passwordHashingAlgo = $this->getUserPasswordHashAlgo();
		$defaultPasswordHashingAlgo = $this->getDefaultPasswordHashAlgo();

		switch ($passwordHashingAlgo)
		{
			case self::PASSWORD_ARGON2ID:
			case self::PASSWORD_ARGON2I:
				$result = password_verify($password_to_match, $this->getUserPassword());
			case self::SHA1:
			default:
				$result = sha1( $this->getSalt().$password_to_match ) === $this->getSha1Password();
		}

		if($result && $defaultPasswordHashingAlgo != self::SHA1 && $passwordHashingAlgo == self::SHA1)
		{
			$this->setPassword($password_to_match, false);
		}

		return $result;
	}

	private function getDefaultPasswordHashAlgo()
	{
		return kConf::get('password_hash_algo', 'security', self::PASSWORD_ARGON2ID);
	}
	
	public function resetPassword ($newPassword)
	{
		$this->setPassword( $newPassword );

		$oldPassword = $this->getCurrentHashedPassword();
		$salt = $this->getUserPasswordHashAlgo() == self::SHA1
			? $this->getSalt() : null;

		$this->addToPreviousPasswords($oldPassword, $salt, $this->getUserPasswordHashAlgo());
		$this->setPasswordHashKey(null);
		$this->setLoginAttempts(0);
		$this->setLoginBlockedUntil(null);
		$this->save();		
	}
	
	
	/**
	 * Code to be run after deleting the object in database
	 * @param PropelPDO $con
	 */
	public function postDelete(PropelPDO $con = null)
	{
		parent::postDelete($con);
		kQueryCache::invalidateQueryCache($this);
		kEventsManager::raiseEvent(new kObjectErasedEvent($this));
	}
	
	/**
	 * @return int
	 */
	public function getLoginAttempts()
	{
		return $this->getFromCustomData('login_attempts', null, null);
	}
	
	/**
	 * @param int $attempts
	 */
	public function setLoginAttempts($attempts)
	{
		$this->putInCustomData('login_attempts', $attempts, null);
	}
	
	public function incLoginAttempts()
	{
		$this->incInCustomData('login_attempts', 1, null);
	}
	
	public function setPasswordUpdatedAt($v)
	{		
		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		if ($v === null || $v === '') {
			$dt = null;
		} elseif ($v instanceof DateTime) {
			$dt = $v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric($v)) { // if it's a unix timestamp
					$dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					$dt = new DateTime($v);
				}
			} catch (Exception $x) {
				throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
			}
		}

		$curValue = $this->getPasswordUpdatedAt();
		if ( $curValue !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($curValue !== null && $tmpDt = new DateTime($curValue)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$newValue = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->putInCustomData('password_updated_at', $newValue, null);
			}
		} // if either are not null

		return $this;
	}
	
	public function getPasswordUpdatedAt($format = 'Y-m-d H:i:s')
	{
		$value = $this->getFromCustomData('password_updated_at', null, null);
		
		if ($value === null) {
			return null;
		}

		if ($value === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($value);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($value, true), $x);
			}
		}

		if ($format === null) {
			// We cast here to maintain BC in API; obviously we will lose data if we're dealing with pre-/post-epoch dates.
			return (int) $dt->format('U');
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
		
	}
	
	/**
	 * @param string $hashKey
	 */
	public function setPasswordHashKey($hashKey)
	{
		$this->putInCustomData('password_hash_key', $hashKey, null);
	}
	
	/**
	 * @return string
	 */
	public function getPasswordHashKey()
	{
		return $this->getFromCustomData('password_hash_key', null, null);
	}
	
	
	public function resetPreviousPasswords()
	{
		$this->putInCustomData('previous_passwords', null, null);
	}
	
	
	public function addToPreviousPasswords($sha1, $salt, $hashMethod)
	{
		$passwords = $this->getPreviousPasswords();
		if (!$passwords) {
			$passwords = array();
		}
		array_unshift($passwords, array ('sha1' => $sha1, 'salt' => $salt, 'hashMethod' => $hashMethod));
		$passToKeep = $this->getNumPrevPassToKeep();
		while (count($passwords) > $passToKeep) {
			array_pop($passwords);
		}
		$this->setPreviousPasswords($passwords);
	}
	
	//TODO: should be set private after migration from admin_kuser to user_login_data
	public function getPreviousPasswords()
	{
		return $this->getFromCustomData('previous_passwords', null, null);
	}
	
	//TODO: should be set private after migration from admin_kuser to user_login_data
	public function setPreviousPasswords($prevPass)
	{
		$this->putInCustomData('previous_passwords', $prevPass);
	}
	
	public function getSeedFor2FactorAuth ()
	{
		return $this->getFromCustomData ('seedFor2FactorAuth');
	}
	
	public function setSeedFor2FactorAuth ($v)
	{
		$this->putInCustomData ('seedFor2FactorAuth', $v);
	}
	
	public function passwordUsedBefore($pass)
	{
		$passToKeep = $this->getNumPrevPassToKeep();
		$previousPass = $this->getPreviousPasswords();
		if ($passToKeep > 0 && count($previousPass) == 0)
		{
			return $this->isSamePassword($pass, $this->salt, $this->getCurrentHashedPassword());
		}
		
		$i = 0;
		while ($i < count($previousPass) && $i < $passToKeep) {
			if($this->isSamePassword($pass, $previousPass[$i]['salt'], $previousPass[$i]['sha1']))
			{
				return true;
			}
			$i++;
		}

		return false;
	}

	private function isSamePassword($newPass, $salt, $oldPass)
	{
		$passwordHashingAlgo = $this->getUserPasswordHashAlgo();

		switch ($passwordHashingAlgo)
		{
			case self::PASSWORD_ARGON2I:
			case self::PASSWORD_ARGON2ID:
				return password_verify($newPass, $oldPass);

			case self::SHA1:
			default:
				return sha1($salt . $newPass) === $oldPass;
		}

		return false;
	}

	private function getCurrentHashedPassword()
	{
		$passwordHashingAlgo = $this->getUserPasswordHashAlgo();

		$currentHashedPass = $this->getSha1Password();
		if(in_array($passwordHashingAlgo, array(self::PASSWORD_ARGON2ID, self::PASSWORD_ARGON2I)))
		{
			$currentHashedPass = $this->getUserPassword();
		}

		return $currentHashedPass;
	}
	
	public function getMaxLoginAttempts()
	{
		$partner = PartnerPeer::retrieveByPK($this->getConfigPartnerId());
		if (!$partner) {
			return kConf::get('user_login_max_wrong_attempts');
		}
		return $partner->getMaxLoginAttempts();
	}
	
	public function getLoginBlockPeriod()
	{
		$partner = PartnerPeer::retrieveByPK($this->getConfigPartnerId());
		if (!$partner) {
			return kConf::get('user_login_block_period');
		}
		return $partner->getLoginBlockPeriod();
	}
		
	public function getNumPrevPassToKeep()
	{
		$partner = PartnerPeer::retrieveByPK($this->getConfigPartnerId());
		if (!$partner) {
			return kConf::get('user_login_num_prev_passwords_to_keep');
		}
		return $partner->getNumPrevPassToKeep();
	}
		
	public function getPassReplaceFreq()
	{
		$partner = PartnerPeer::retrieveByPK($this->getConfigPartnerId());
		if (!$partner) {
			return kConf::get('user_login_password_replace_freq');
		}
		return $partner->getPassReplaceFreq();
	}
	

	
	public function newPassHashKey($validity = null)
	{
		$loginDataId = $this->getId();
		$validity = (is_null($validity) ? kConf::get('user_login_set_password_hash_key_validity') : $validity);
		$expiryTime = time() + $validity; // now + 24 hours on default
		$random = sha1(KCryptoWrapper::random_pseudo_bytes(32));
		$hashKey = base64_encode(implode('|', array($loginDataId, $expiryTime, $random)));
		return $hashKey;
	}
	
	public function getNewHashKeyIfCurrentInvalid()
	{
		$valid = false;
		try {
			$valid = $this->isPassHashKeyValid($this->getPasswordHashKey());
		}
		catch (kUserException $e) {
			$valid = false;
		}
		if (!$valid) {
			$this->setPasswordHashKey($this->newPassHashKey());
		}
		return $this->getPasswordHashKey();
	}
	
	
	public function isPassHashKeyValid($hashKey)
	{
		// check if same as user's saved hash key
		if (base64_decode($hashKey) != base64_decode($this->getPasswordHashKey())) {
			throw new kUserException('', kUserException::NEW_PASSWORD_HASH_KEY_INVALID);
		}
		
		// decode
		$params = UserLoginDataPeer::decodePassHashKey($hashKey);
		if (!$params) {
			throw new kUserException('', kUserException::NEW_PASSWORD_HASH_KEY_INVALID);
		}

		// check if user_login_data id is right
		if ($params[0] != $this->getId()) {
			throw new kUserException('', kUserException::NEW_PASSWORD_HASH_KEY_INVALID);
		}
		// check if not expired
		if ($params[1] < time()) {
			throw new kUserException('', kUserException::NEW_PASSWORD_HASH_KEY_EXPIRED);
		}
		
		return true;
	}
	
	//will return the relevant invalid password message in case the provided password is not valid.
	public function getInvalidPasswordStructureMessage(){
		$invalidPasswordStructureMessage='';
		$partnerId = $this->getConfigPartnerId();
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if($partner && $partner->getInvalidPasswordStructureMessage())
			$invalidPasswordStructureMessage = $partner->getInvalidPasswordStructureMessage();
		return $invalidPasswordStructureMessage; 
	}
	
	public function isCommonPassword($password)
	{
		$commonPasswordsMap = kConf::getMap(kConfMapNames::COMMON_PASSWORDS);
		return $commonPasswordsMap and isset($commonPasswordsMap[$password]);
	}
		
	public function getCacheInvalidationKeys()
	{
		return array("userLoginData:id=".strtolower($this->getId()), "userLoginData:loginEmail=".strtolower($this->getLoginEmail()));
	}

	public function isTwoFactorAuthenticationRequired(kuser $dbUser)
	{
		$partnerIds = $dbUser->getAllowedPartnerIds();
		$partners = PartnerPeer::retrieveByPKs($partnerIds);
		foreach ($partners as $partner)
		{
			if($partner->getUseTwoFactorAuthentication())
			{
				return true;
			}
		}
		return false;
	}

	/*
	* @return string
	*/
	public function getUserPassword()
	{
		return $this->getFromCustomData('password', null, null);
	}

	/**
	 * @param string $password
	 */
	public function setUserPassword($password)
	{
		$this->putInCustomData('password', $password, null);
	}

	/*
	* @return string
	*/
	public function getUserPasswordHashAlgo()
	{
		return $this->getFromCustomData('hash_algo', null, self::SHA1);
	}

	/**
	 * @param string $hasAlgo
	 */
	public function setUserPasswordHashAlgo($hasAlgo)
	{
		$this->putInCustomData('hash_algo', $hasAlgo, null);
	}

} // UserLoginData
