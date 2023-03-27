<?php
/**
 * @package plugins.vendor
 * @subpackage model.oAuth
 */
class kZoomOauth extends kOAuth
{
	const OAUTH_TOKEN_PATH = '/oauth/token';
	const AUTHORIZATION_HEADER = 'Authorization';
	const TOKEN_TYPE = 'token_type';
	const SCOPE = 'scope';
	const MAP_NAME = 'vendor';
	const CONFIGURATION_PARAM_NAME = 'ZoomAccount';
	const X_ZM_SIGNATURE = 'x-zm-signature';
	const X_ZM_REQUEST_TIMESTAMP = 'x-zm-request-timestamp';

	/**
	 * @param ZoomVendorIntegration $vendorIntegration
	 * @return array
	 * @throws Exception
	 */
	public static function refreshTokens($vendorIntegration)
	{
		KalturaLog::info('Refreshing tokens');
		list($zoomBaseURL, , $header, $userPwd) = self::getHeaderData();
		$oldRefreshToken = $vendorIntegration->getRefreshToken();
		$postFields = "grant_type=refresh_token&refresh_token=$oldRefreshToken";
		$response = self::curlRetrieveTokensData($zoomBaseURL, $userPwd, $header, $postFields);
		$tokensData = self::retrieveTokensDataFromResponse($response);
		$vendorIntegration->saveTokensData($tokensData);
		return $tokensData;
	}
	

	public static function requestAuthorizationTokens($authCode)
	{
		list($zoomBaseURL, $redirectUrl, $header, $userPwd) = self::getHeaderData();
		$postFields = "grant_type=authorization_code&code={$authCode}&redirect_uri=$redirectUrl";
		$response = self::curlRetrieveTokensData($zoomBaseURL, $userPwd, $header, $postFields);
		$tokensData = self::retrieveTokensDataFromResponse($response);
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
	protected static function curlRetrieveTokensData($url, $userPwd, $header, $postFields)
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
	 * @return int $expiresIn
	 */
	public static function getTokenExpiryAbsoluteTime($expiresIn)
	{
		$expiresIn = $expiresIn - 120;
		KalturaLog::info("Set Token 'expires_in' to " . $expiresIn);
		return $expiresIn;
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	protected static function getHeaderData()
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
	 * @throws Exception
	 */
	public static function verifyHeaderToken($zoomConfiguration)
	{
		$headers = getallheaders();

		if (isset($headers[self::AUTHORIZATION_HEADER]))
		{
			$verificationToken = $zoomConfiguration[kOAuth::VERIFICATION_TOKEN];
			if ($verificationToken === $headers[self::AUTHORIZATION_HEADER])
			{
				return;
			}
		}

		if(isset($headers[self::X_ZM_SIGNATURE]) && isset($headers[self::X_ZM_REQUEST_TIMESTAMP]))
		{
			$signatureTimestamp = $headers[self::X_ZM_REQUEST_TIMESTAMP];
			$signatureHeader = $headers[self::X_ZM_SIGNATURE];
			$secretToken = $zoomConfiguration[kOAuth::SECRET_TOKEN];
			$body = ZoomHelper::getPayloadData(true);
			$signature = "v0=" . hash_hmac("sha256", "v0:$signatureTimestamp:$body", $secretToken);

			if($signature == $signatureHeader)
			{
				return;
			}

			KalturaLog::debug("Zoom signature header received [$signatureHeader]");
			KalturaLog::debug("Zoom request timestamp header received [$signatureTimestamp]");
			KalturaLog::debug("Signature created for comparison [$signature]");
		}

		ZoomHelper::exitWithError(kZoomErrorMessages::FAILED_VERIFICATION);
	}
}