<?php
/**
 * @package plugins.venodr
 * @subpackage model.oAuth
 */
class kZoomOauth
{
	const OAUTH_TOKEN_PATH = '/oauth/token';
	const ACCESS_TOKEN = 'access_token';
	const REFRESH_TOKEN = 'refresh_token';
	const VERIFICATION_TOKEN = 'verificationToken';
	const AUTHORIZATION_HEADER = 'Authorization';
	const TOKEN_TYPE = 'token_type';
	const EXPIRES_IN = 'expires_in';
	const SCOPE = 'scope';
	const MAP_NAME = 'vendor';
	const CONFIGURATION_PARAM_NAME = 'ZoomAccount';

	/**
	 * @param ZoomVendorIntegration $vendorIntegration
	 * @return array
	 * @throws Exception
	 */
	public static function refreshTokens($vendorIntegration)
	{
		KalturaLog::info('Refreshing tokens');
		list($zoomBaseURL, , $header, $userPwd) = self::getZoomHeaderData();
		$oldRefreshToken = $vendorIntegration->getRefreshToken();
		$postFields = "grant_type=refresh_token&refresh_token=$oldRefreshToken";
		$response = self::curlRetrieveTokensData($zoomBaseURL, $userPwd, $header, $postFields);
		$tokensData = self::parseTokensResponse($response);
		self::validateToken($tokensData);
		$tokensData = self::extractTokensFromData($tokensData);
		$expiresIn = $tokensData[self::EXPIRES_IN];
		$tokensData[self::EXPIRES_IN] = self::setTokenExpiryRelativeTime($expiresIn);
		$vendorIntegration->saveTokensData($tokensData);
		return $tokensData;
	}

	public static function requestAccessToken($authCode)
	{
		list($zoomBaseURL, $redirectUrl, $header, $userPwd) = self::getZoomHeaderData();
		$postFields = "grant_type=authorization_code&code={$authCode}&redirect_uri=$redirectUrl";
		$response = self::curlRetrieveTokensData($zoomBaseURL, $userPwd, $header, $postFields);
		$tokensData = self::parseTokensResponse($response);
		self::validateToken($tokensData);
		$tokensData = self::extractTokensFromData($tokensData);
		$expiresIn = $tokensData[self::EXPIRES_IN];
		$tokensData[self::EXPIRES_IN] = self::setTokenExpiryRelativeTime($expiresIn);
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
	private static function curlRetrieveTokensData($url, $userPwd, $header, $postFields)
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
	 * @param array $expiresIn
	 * @return array $expiresIn
	 */
	public static function setTokenExpiryAbsoluteTime($expiresIn)
	{
		$expiresIn = $expiresIn - 120;
		KalturaLog::info("Set Token 'expires_in' to " . $expiresIn);
		return $expiresIn;
	}

	/**
	 * set two minutes off the token expiration, avoid 401 response from zoom
	 * @param array $expiresIn
	 * @return array $expiresIn
	 */
	public static function setTokenExpiryRelativeTime($expiresIn)
	{
		$expiresIn = time() + $expiresIn - 120;
		KalturaLog::info("Set Token 'expires_in' to " . $expiresIn);
		return $expiresIn;
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
	public static function parseTokensResponse($response)
	{
		$dataAsArray = json_decode($response, true);
		KalturaLog::debug(print_r($dataAsArray, true));
		return $dataAsArray;
	}

	public static function validateToken($tokensData)
	{
		if (!$tokensData || !isset($tokensData[self::REFRESH_TOKEN]) || !isset($tokensData[self::ACCESS_TOKEN]) ||
			!isset($tokensData[self::EXPIRES_IN]))
		{
			ZoomHelper::exitWithError(kZoomErrorMessages::TOKEN_PARSING_FAILED . print_r($tokensData));
		}
	}


	/**
	 * @return array
	 * @throws Exception
	 */
	private static function getZoomHeaderData()
	{
		$zoomConfiguration = kConf::get(self::CONFIGURATION_PARAM_NAME, self::MAP_NAME);
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
	 * @throws kZoomErrorMessages
	 */
	public static function getValidAccessToken($zoomIntegration)
	{
		if (time() >= $zoomIntegration->getExpiresIn()) // token have expired -> refresh
		{
			self::refreshTokens($zoomIntegration);
		}

		return $zoomIntegration->getAccessToken();
	}

	/**
	 * verify header token, if not equal die
	 * @param array $zoomConfiguration
	 */
	public static function verifyHeaderToken($zoomConfiguration)
	{
		$headers = getallheaders();
		if (isset($headers[self::AUTHORIZATION_HEADER]))
		{
			$verificationToken = $zoomConfiguration[self::VERIFICATION_TOKEN];
			if ($verificationToken === $headers[self::AUTHORIZATION_HEADER])
			{
				return;
			}
		}

		ZoomHelper::exitWithError(kZoomErrorMessages::FAILED_VERIFICATION);
	}
}