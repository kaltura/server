<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.model
 */

class ZoomVendorIntegration extends VendorIntegration
{
	const ACCESS_TOKEN = 'accessToken';
	const REFRESH_TOKEN = 'refreshToken';
	const EXPIRES_IN = 'expiresIn';
	const DEFAULT_USER_E_MAIL = 'defaultUserEMail';
	const ZOOM_CATEGORY = 'zoomCategory';
	const ZOOM_WEBINAR_CATEGORY = 'zoomWebinarCategory';
	const CREATE_USER_IF_NOT_EXIST = 'createUserIfNotExist';
	const LAST_ERROR = 'lastError';
	const LAST_ERROR_TIMESTAMP = 'lastErrorTimestamp';
	const USER_MATCHING = 'userMatching';
	const HANDLE_PARTICIPANTS_MODE = 'HandleParticipantsMode';
	const USER_POSTFIX = 'UserPostfix';
	const ENABLE_WEBINAR_UPLOADS = "enableWebinarUploads";

	public function setAccessToken ($v)	{ $this->putInCustomData ( self::ACCESS_TOKEN, $v);	}
	public function getAccessToken ( )	{ return $this->getFromCustomData(self::ACCESS_TOKEN);	}

	public function setRefreshToken ($v)	{ $this->putInCustomData ( self::REFRESH_TOKEN, $v);	}
	public function getRefreshToken ( )	{ return $this->getFromCustomData(self::REFRESH_TOKEN);	}

	public function setExpiresIn ($v)	{ $this->putInCustomData ( self::EXPIRES_IN, $v);	}
	public function getExpiresIn( )	{ return $this->getFromCustomData(self::EXPIRES_IN);	}

	public function setDefaultUserEMail ($v)	{ $this->putInCustomData ( self::DEFAULT_USER_E_MAIL, $v);	}
	public function getDefaultUserEMail ( )	{ return $this->getFromCustomData(self::DEFAULT_USER_E_MAIL);	}

	public function setZoomCategory($v)	{ $this->putInCustomData ( self::ZOOM_CATEGORY, $v);	}
	public function getZoomCategory( )	{ return $this->getFromCustomData(self::ZOOM_CATEGORY);	}
	public function unsetCategory( )  {return $this->removeFromCustomData(self::ZOOM_CATEGORY);	}

	public function setZoomWebinarCategory($v)	{ $this->putInCustomData ( self::ZOOM_WEBINAR_CATEGORY, $v);	}
	public function getZoomWebinarCategory( )	{ return $this->getFromCustomData(self::ZOOM_WEBINAR_CATEGORY);	}
	public function unsetWebinarCategory( )  {return $this->removeFromCustomData(self::ZOOM_WEBINAR_CATEGORY);	}

	public function setCreateUserIfNotExist($v) { $this->putInCustomData ( self::CREATE_USER_IF_NOT_EXIST, $v); }
	public function getCreateUserIfNotExist() { return $this->getFromCustomData ( self::CREATE_USER_IF_NOT_EXIST,null, false); }

	public function setUserMatching($v) { $this->putInCustomData ( self::USER_MATCHING, $v); }
	public function getUserMatching() { return $this->getFromCustomData ( self::USER_MATCHING,null, kZoomUsersMatching::DO_NOT_MODIFY); }

	public function setHandleParticipantsMode($v) { $this->putInCustomData ( self::HANDLE_PARTICIPANTS_MODE, $v); }
	public function getHandleParticipantsMode() { return $this->getFromCustomData ( self::HANDLE_PARTICIPANTS_MODE,null, kHandleParticipantsMode::ADD_AS_CO_PUBLISHERS); }

	public function setUserPostfix($v) { $this->putInCustomData ( self::USER_POSTFIX, $v); }
	public function getUserPostfix() { return $this->getFromCustomData ( self::USER_POSTFIX,null, ""); }

	public function setEnableWebinarUploads($v) { $this->putInCustomData ( self::ENABLE_WEBINAR_UPLOADS, $v); }
	public function getEnableWebinarUploads() { return $this->getFromCustomData ( self::ENABLE_WEBINAR_UPLOADS,null, true); }

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

	/**
	 * returns all tokens as array
	 * @return array
	 */
	public function getTokens()
	{
		return array(kZoomOauth::ACCESS_TOKEN => $this->getAccessToken(), kZoomOauth::REFRESH_TOKEN => $this->getRefreshToken(),
			kZoomOauth::EXPIRES_IN => $this->getExpiresIn());
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
		$this->setExpiresIn($tokensDataAsArray[kZoomOauth::EXPIRES_IN]);
		$this->setAccessToken($tokensDataAsArray[kZoomOauth::ACCESS_TOKEN]);
		$this->setRefreshToken($tokensDataAsArray[kZoomOauth::REFRESH_TOKEN]);
		$this->setVendorType(VendorTypeEnum::ZOOM_ACCOUNT);
	}
}