<?php
/**
 * @package plugins.WebexAPIDropFolder
 * @subpackage webex.model
 */

class WebexAPIVendorIntegration extends VendorIntegration
{
	const ACCESS_TOKEN = 'accessToken';
	const REFRESH_TOKEN = 'refreshToken';

	public function setAccessToken ($v)	{ $this->putInCustomData ( self::ACCESS_TOKEN, $v);	}
	public function getAccessToken ( )	{ return $this->getFromCustomData(self::ACCESS_TOKEN);	}
	
	public function setRefreshToken ($v)	{ $this->putInCustomData ( self::REFRESH_TOKEN, $v);	}
	public function getRefreshToken ( )	{ return $this->getFromCustomData(self::REFRESH_TOKEN);	}
	

	/**
	 * returns all tokens as array
	 * @return array
	 */
	public function getTokens()
	{
		return array(kZoomOauth::ACCESS_TOKEN => $this->getAccessToken(), kZoomOauth::REFRESH_TOKEN => $this->getRefreshToken());
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
		$this->setAccessToken($tokensDataAsArray[kZoomOauth::ACCESS_TOKEN]);
		$this->setRefreshToken($tokensDataAsArray[kZoomOauth::REFRESH_TOKEN]);
		$this->setVendorType(VendorTypeEnum::WEBEX_ACCOUNT);
	}
}