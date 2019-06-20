<?php
/**
 * @package plugins.venodr
 * @subpackage model.zoomOauth
 */
class kZoomOauth implements kVendorOauth
{
	const OAUTH_TOKEN_PATH = '/oauth/token';
	const ACCESS_TOKEN = 'access_token';
	const REFRESH_TOKEN = 'refresh_token';
	const VERIFICATION_TOKEN = 'verificationToken';
	const TOKEN_TYPE = 'token_type';
	const EXPIRES_IN = 'expires_in';
	const SCOPE = 'scope';


	/**
	 * @param ZoomVendorIntegration $vendorIntegration
	 * @return array
	 * @throws Exception
	 */
	public function refreshTokens($vendorIntegration)
	{
		KalturaLog::info('Refreshing tokens');
		list($zoomBaseURL, , $header, $userPwd) = $this->getZoomHeaderData();
		$oldRefreshToken = $vendorIntegration->getRefreshToken();
		$postFields = "grant_type=refresh_token&refresh_token=$oldRefreshToken";
		$response = $this->curlRetrieveTokensData($zoomBaseURL, $userPwd, $header, $postFields);
		$tokensData = self::parseTokensResponse($response);
		$vendorIntegration->saveTokensData($tokensData);
		return $tokensData;
	}

	/**
	 * @param bool $forceNewToken
	 * @param string $accountId
	 * @return array
	 * @throws Exception
	 */
	public function retrieveTokensData($forceNewToken = false, $accountId = null)
	{
		KalturaLog::info('Retrieving Tokens');
		$zoomIntegration = null;
		if (!$forceNewToken && $accountId)
		{
			/** @var ZoomVendorIntegration $zoomIntegration */
			$zoomIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartner($accountId, VendorTypeEnum::ZOOM_ACCOUNT);
			if ($zoomIntegration) // tokens exist
			{
				if (time() > $zoomIntegration->getExpiresIn()) // token had expired -> refresh
				{
					return $this->refreshTokens($zoomIntegration->getRefreshToken(), $zoomIntegration);
				}

				return array(self::ACCESS_TOKEN => $zoomIntegration->getAccessToken(), self::REFRESH_TOKEN => $zoomIntegration->getRefreshToken(),
					self::EXPIRES_IN => $zoomIntegration->getExpiresIn());
			}
		}
		list($zoomBaseURL, $redirectUrl, $header, $userPwd) = $this->getZoomHeaderData();
		$postFields = "grant_type=authorization_code&code={$_GET['code']}&redirect_uri=$redirectUrl";
		$response = $this->curlRetrieveTokensData($zoomBaseURL, $userPwd, $header, $postFields);
		$tokensData = self::parseTokensResponse($response);
		return $tokensData;
	}

	/**
	 * @param $url
	 * @param $userPwd
	 * @param $header
	 * @param $postFields
	 * @return mixed|string
	 * @throws Exception
	 */
	private function curlRetrieveTokensData($url, $userPwd, $header, $postFields)
	{
		$curlWrapper = new KCurlWrapper();
		$curlWrapper->setOpt(CURLOPT_POST, 1);
		$curlWrapper->setOpt(CURLOPT_HEADER, true);
		$curlWrapper->setOpt(CURLOPT_HTTPHEADER, $header);
		$curlWrapper->setOpt(CURLOPT_USERPWD, $userPwd);
		$curlWrapper->setOpt(CURLOPT_POSTFIELDS, $postFields);
		return $curlWrapper->exec($url . self::OAUTH_TOKEN_PATH);
	}

	/**
	 * set two minutes off the token expiration, avoid 401 response from zoom
	 * @param int $expiresIn
	 * @return int
	 */
	public static function getValidUntil($expiresIn)
	{
		return time() + $expiresIn - 120;
	}

	/**
	 * @param array $data
	 * @return array<tokens>
	 */
	public static function extractTokensFromData($data)
	{
		return array(self::ACCESS_TOKEN => $data[self::ACCESS_TOKEN], self::REFRESH_TOKEN => $data[self::REFRESH_TOKEN],
			self::EXPIRES_IN => $data[self::EXPIRES_IN]);
	}

	/**
	 * @param string $response
	 * @return array
	 * @throws Exception
	 */
	protected static function parseTokensResponse($response)
	{
		$dataAsArray = json_decode($response, true);
		KalturaLog::debug(print_r($dataAsArray, true));
		return self::extractTokensFromData($dataAsArray);
	}

	public static function parseTokens($tokensData)
	{
		if (!$tokensData || isset($dataAsArray[self::REFRESH_TOKEN]) || isset($dataAsArray[self::ACCESS_TOKEN]) ||
			isset($dataAsArray[self::EXPIRES_IN]))
		{
			KalturaLog::err('Parse Tokens failed, response received from zoom is: ' . $tokensData);
			throw new KalturaAPIException("Unable to parse tokens please check zoom configuration");
		}

		$expiresIn = $tokensData[self::EXPIRES_IN];
		$tokensData[self::EXPIRES_IN] = self::getValidUntil($expiresIn);
		return self::extractTokensFromData($tokensData);
	}


	/**
	 * @return array
	 * @throws Exception
	 */
	private function getZoomHeaderData()
	{
		$zoomConfiguration = kConf::get(ZoomWrapper::CONFIGURATION_PARAM_NAME, ZoomWrapper::MAP_NAME);
		$clientId = $zoomConfiguration['clientId'];
		$zoomBaseURL = $zoomConfiguration['ZoomBaseUrl'];
		$redirectUrl = $zoomConfiguration['redirectUrl'];
		$clientSecret = $zoomConfiguration['clientSecret'];
		$header = array('Content-Type:application/x-www-form-urlencoded');
		$userPwd = "$clientId:$clientSecret";
		return array($zoomBaseURL, $redirectUrl, $header, $userPwd);
	}

	/**
	 * @param ZoomVendorIntegration $zoomIntegration
	 * @return string
	 * @throws kVendorException
	 */
	public function getValidAccessToken($zoomIntegration)
	{
		if (time() >= $zoomIntegration->getExpiresIn()) // token have expired -> refresh
		{
			$this->refreshTokens($zoomIntegration);
		}

		return $zoomIntegration->getAccessToken();
	}
}