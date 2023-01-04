<?php
/**
 * Skeleton subclass for representing a row from the 'vendor_integration' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.vendor
 * @subpackage model
 */
class VendorIntegration extends BaseVendorIntegration
{
	const ACCESS_TOKEN = 'accessToken';
	const REFRESH_TOKEN = 'refreshToken';
	const EXPIRES_IN = 'expiresIn';
	const DELETE_POLICY = 'deletionPolicy';
	const ENABLE_MEETING_UPLOAD = 'enableMeetingUpload';
	const CREATE_USER_IF_NOT_EXIST = 'createUserIfNotExist';
	const HANDLE_PARTICIPANTS_MODE = 'HandleParticipantsMode';
	const CONVERSION_PROFILE_ID = 'conversionProfileId';
	const DEFAULT_USER_E_MAIL = 'defaultUserEMail';
	const LAST_ERROR = 'lastError';
	const LAST_ERROR_TIMESTAMP = 'lastErrorTimestamp';
	const ENABLE_MEETING_CHAT = 'enableMeetingChat';
	
	public function setAccessToken($v)
	{
		$this->putInCustomData(self::ACCESS_TOKEN, $v);
	}
	
	public function getAccessToken()
	{
		return $this->getFromCustomData(self::ACCESS_TOKEN);
	}
	
	public function setRefreshToken($v)
	{
		$this->putInCustomData(self::REFRESH_TOKEN, $v);
	}
	
	public function getRefreshToken()
	{
		return $this->getFromCustomData(self::REFRESH_TOKEN);
	}
	
	public function setExpiresIn($v)
	{
		$this->putInCustomData(self::EXPIRES_IN, $v);
	}
	
	public function getExpiresIn()
	{
		return $this->getFromCustomData(self::EXPIRES_IN);
	}
	
	public function setDeletionPolicy($v)
	{
		$this->putInCustomData(self::DELETE_POLICY, $v);
	}
	
	public function getDeletionPolicy()
	{
		return $this->getFromCustomData(self::DELETE_POLICY);
	}
	
	public function setEnableMeetingUpload($v)
	{
		$this->putInCustomData(self::ENABLE_MEETING_UPLOAD, $v);
	}
	
	public function getEnableMeetingUpload()
	{
		return $this->getFromCustomData(self::ENABLE_MEETING_UPLOAD);
	}
	
	public function setCreateUserIfNotExist($v)
	{
		$this->putInCustomData(self::CREATE_USER_IF_NOT_EXIST, $v);
	}
	
	public function getCreateUserIfNotExist()
	{
		return $this->getFromCustomData(self::CREATE_USER_IF_NOT_EXIST, null, false);
	}
	
	public function setHandleParticipantsMode($v)
	{
		$this->putInCustomData(self::HANDLE_PARTICIPANTS_MODE, $v);
	}
	
	public function getHandleParticipantsMode()
	{
		return $this->getFromCustomData(self::HANDLE_PARTICIPANTS_MODE, null, kHandleParticipantsMode::ADD_AS_CO_PUBLISHERS);
	}
	
	public function setConversionProfileId($v)
	{
		$this->putInCustomData(self::CONVERSION_PROFILE_ID, $v);
	}
	
	public function getConversionProfileId()
	{
		return $this->getFromCustomData(self::CONVERSION_PROFILE_ID, null, null);
	}
	
	public function setDefaultUserEMail($v)
	{
		$this->putInCustomData(self::DEFAULT_USER_E_MAIL, $v);
	}
	
	public function getDefaultUserEMail()
	{
		return $this->getFromCustomData(self::DEFAULT_USER_E_MAIL);
	}
	
	public function setLastError($v)
	{
		$this->putInCustomData(self::LAST_ERROR_TIMESTAMP, time());
		$this->putInCustomData(self::LAST_ERROR, $v);
	}
	
	public function saveLastError($v)
	{
		$this->setLastError($v);
		$this->save();
	}
	
	public function setEnableMeetingChat($v)
	{
		$this->putInCustomData(self::ENABLE_MEETING_CHAT, $v);
	}
	
	public function getEnableMeetingChat()
	{
		return $this->getFromCustomData(self::ENABLE_MEETING_CHAT, null, null);
	}
	
	/**
	 * returns all tokens as array
	 * @return array
	 */
	public function getTokens()
	{
		return array(kOAuth::ACCESS_TOKEN => $this->getAccessToken(), kOAuth::REFRESH_TOKEN => $this->getRefreshToken(),
			kOAuth::EXPIRES_IN => $this->getExpiresIn());
	}
	
	/**
	 * @param array $tokensDataAsArray
	 * @throws PropelException
	 */
	public function saveTokensData($tokensDataAsArray)
	{
		$this->setTokensData($tokensDataAsArray);
		$this->save();
	}
	
	public function setTokensData($tokensDataAsArray)
	{
		$this->setExpiresIn($tokensDataAsArray[kOAuth::EXPIRES_IN]);
		$this->setAccessToken($tokensDataAsArray[kOAuth::ACCESS_TOKEN]);
		$this->setRefreshToken($tokensDataAsArray[kOAuth::REFRESH_TOKEN]);
	}
	
} // VendorIntegration
