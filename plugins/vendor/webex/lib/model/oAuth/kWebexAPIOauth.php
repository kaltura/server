<?php
/**
 * @package plugins.WebexAPIDropFolder
 * @subpackage model.oAuth
 */
class kWebexAPIOauth extends kOAuth
{
	const OAUTH_TOKEN_PATH = 'access_token/';
	
	/**
	 * @return array
	 * @throws Exception
	 */
	protected static function getHeaderData()
	{
		$webexConfiguration = kConf::get(WebexAPIDropFolderPlugin::CONFIGURATION_PARAM_NAME, kConfMapNames::VENDOR);
		$webexBaseURL = $webexConfiguration['BaseUrl'];
		$redirectUrl = $webexConfiguration['redirectUrl'];
		$clientId = $webexConfiguration['clientId'];
		$clientSecret = $webexConfiguration['clientSecret'];
		$header = array('Content-Type:application/x-www-form-urlencoded');
		return array($webexBaseURL, $redirectUrl, $clientId, $clientSecret, $header);
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
		$curlWrapper->setOpt(CURLOPT_POSTFIELDS, $postFields);
		return $curlWrapper->exec($url . self::OAUTH_TOKEN_PATH);
	}
	
	public static function validateToken($tokensData)
	{
		if (!$tokensData || !isset($tokensData[self::REFRESH_TOKEN]) || !isset($tokensData[self::ACCESS_TOKEN]) ||
			!isset($tokensData[self::EXPIRES_IN]))
		{
			throw new KalturaException(KalturaWebexAPIErrors::TOKEN_PARSING_FAILED);
		}
	}
	
	/**
	 * @param $response
	 * @return array $tokensData
	 * @throws Exception
	 */
	protected static function retrieveTokenData($response)
	{
		$tokensData = self::parseTokensResponse($response);
		self::validateToken($tokensData);
		$tokensData = self::extractTokensFromData($tokensData);
		$expiresIn = $tokensData[self::EXPIRES_IN];
		$tokensData[self::EXPIRES_IN] = self::getTokenExpiryRelativeTime($expiresIn);
		return $tokensData;
	}
	
	public static function requestAccessToken($authCode)
	{
		list($webexBaseURL, $redirectUrl, $clientId, $clientSecret, $header) = self::getHeaderData();
		$redirectUri = urlencode($redirectUrl);
		$postFields = "grant_type=authorization_code&client_id=$clientId&client_secret=$clientSecret&code=$authCode&redirect_uri=$redirectUri";
		$response = self::curlRetrieveTokensData($webexBaseURL, $header, $postFields);
		$tokensData = self::retrieveTokenData($response);
		return $tokensData;
	}
}