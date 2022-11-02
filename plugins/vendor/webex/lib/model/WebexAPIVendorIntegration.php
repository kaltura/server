<?php
/**
 * @package plugins.WebexAPIDropFolder
 * @subpackage webex.model
 */

class WebexAPIVendorIntegration extends VendorIntegration
{
	const ACCESS_TOKEN = 'accessToken';
	const REFRESH_TOKEN = 'refreshToken';
	const WEBEX_CATEGORY = 'webexCatogery';
	const ENABLE_MEETING_UPLOAD = 'enableMeetingUpload';
	const USER_MATCHING_MODE = 'userMatchingMode';
	const USER_POSTFIX = 'UserPostfix';

	public function setAccessToken ($v)	{ $this->putInCustomData ( self::ACCESS_TOKEN, $v);	}
	public function getAccessToken ( )	{ return $this->getFromCustomData(self::ACCESS_TOKEN);	}
	
	public function setRefreshToken ($v)	{ $this->putInCustomData ( self::REFRESH_TOKEN, $v);	}
	public function getRefreshToken ( )	{ return $this->getFromCustomData(self::REFRESH_TOKEN);	}
	
	public function setWebexCategory($v)	{ $this->putInCustomData ( self::WEBEX_CATEGORY, $v);	}
	public function getWebexCategory( )	{ return $this->getFromCustomData(self::WEBEX_CATEGORY);	}
	public function unsetCategory( )  {return $this->removeFromCustomData(self::WEBEX_CATEGORY);	}
	
	public function setEnableMeetingUpload ($v)	{ $this->putInCustomData ( self::ENABLE_MEETING_UPLOAD, $v);	}
	public function getEnableMeetingUpload ( )	{ return $this->getFromCustomData(self::ENABLE_MEETING_UPLOAD); }
		
	public function setUserMatchingMode($v) { $this->putInCustomData ( self::USER_MATCHING_MODE, $v); }
	public function getUserMatchingMode() { return $this->getFromCustomData ( self::USER_MATCHING_MODE,null, kZoomUsersMatching::DO_NOT_MODIFY); }
	
	public function setUserPostfix($v) { $this->putInCustomData ( self::USER_POSTFIX, $v); }
	public function getUserPostfix ( )	{ return $this->getFromCustomData(self::USER_POSTFIX); }

	/**
	 * returns all tokens as array
	 * @return array
	 */
	public function getTokens()
	{
		return array(kOAuth::ACCESS_TOKEN => $this->getAccessToken(), kOAuth::REFRESH_TOKEN => $this->getRefreshToken());
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
		$this->setAccessToken($tokensDataAsArray[kOAuth::ACCESS_TOKEN]);
		$this->setRefreshToken($tokensDataAsArray[kOAuth::REFRESH_TOKEN]);
		$this->setVendorType(VendorTypeEnum::WEBEX_ACCOUNT);
	}
}