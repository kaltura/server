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
class UserLoginData extends BaseUserLoginData {
	
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
			$lastPartner = $this->getConfigPartnerId();
		}
		return $lastPartner;
	}
	
	public function isLastLoginPartnerIdSet()
	{
		return !is_null($this->getFromCustomData('last_login_partner_id'));
	}
	
	public function setPassword($password) 
	{ 
		$salt = md5(rand(100000, 999999).$this->getLoginEmail()); 
		$this->setSalt($salt); 
		$this->setSha1Password(sha1($salt.$password)); 
		$this->setPasswordUpdatedAt(time());
	} 
	
	
	public function isPasswordValid($password_to_match)
	{
		return sha1( $this->getSalt().$password_to_match ) == $this->getSha1Password() ;
	}
	
	
	public function resetPassword ($newPassword)
	{
		$this->setPassword( $newPassword );
		$this->addToPreviousPasswords($this->getSha1Password(), $this->getSalt());
		$this->setPasswordHashKey(null);
		$this->setLoginAttempts(0);
		$this->setLoginBlockedUntil(null);
		$this->save();		
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
	
	
	public function addToPreviousPasswords($sha1, $salt)
	{
		$passwords = $this->getPreviousPasswords();
		if (!$passwords) {
			$passwords = array();
		}
		array_unshift($passwords, array ('sha1' => $sha1, 'salt' => $salt));
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
	
	public function passwordUsedBefore($pass)
	{
		$previousPass = $this->getPreviousPasswords();
		$i = 0;
		$passToKeep = $this->getNumPrevPassToKeep();
		while ($i < count($previousPass) && $i < $passToKeep) {
			if ($previousPass[$i]['sha1'] == sha1($previousPass[$i]['salt'] . $pass)) {
				return true;
			}
			$i++;
		}
		return false;		
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
	

	
	public function newPassHashKey()
	{
		$loginDataId = $this->getId();
		$expiryTime = time() + (kConf::get('user_login_set_password_hash_key_validity')); // now + 24 hours
		$random = sha1( uniqid() . (time() * rand(0,1)) );
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
		
} // UserLoginData
