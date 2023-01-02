<?php
/**
 * @package plugins.WebexAPIDropFolder
 * @subpackage model.oAuth
 */
class kWebexAPIOauth extends kOAuth
{
	const OAUTH_TOKEN_PATH = 'access_token?';
	const ACCESS_TOKEN_NOT_AUTHORIZED_CODE = 400;
	
	/**
	 * @return array
	 * @throws Exception
	 */
	protected static function getHeaderData()
	{
		$webexConfiguration = WebexAPIDropFolderPlugin::getWebexConfiguration();
		$webexBaseURL = $webexConfiguration[WebexAPIDropFolderPlugin::CONFIGURATION_WEBEX_BASE_URL];
		$redirectUrl = $webexConfiguration[WebexAPIDropFolderPlugin::CONFIGURATION_REDIRECT_URL];
		$redirectUri = urlencode($redirectUrl);
		$clientId = $webexConfiguration[WebexAPIDropFolderPlugin::CONFIGURATION_CLIENT_ID];
		$clientSecret = $webexConfiguration[WebexAPIDropFolderPlugin::CONFIGURATION_CLIENT_SECRET];
		$header = array('Content-Type:application/x-www-form-urlencoded');
		return array($webexBaseURL, $redirectUri, $clientId, $clientSecret, $header);
	}
	
	protected static function retrieveTokensData($webexBaseURL, $header, $postFields)
	{
		$response = self::curlRetrieveTokensData($webexBaseURL, null, $header, $postFields);
		if (self::$errorCode == self::ACCESS_TOKEN_NOT_AUTHORIZED_CODE)
		{
			KalturaLog::warning('Retrieving access token from Webex was not authorized: ' . print_r($response));
			return null;
		}
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
		self::$errorCode = 0;
		
		$curlWrapper = new KCurlWrapper();
		$curlWrapper->setOpt(CURLOPT_POST, 1);
		$curlWrapper->setOpt(CURLOPT_HTTPHEADER, $header);
		$curlWrapper->setOpt(CURLOPT_POSTFIELDS, $postFields);
		$response = $curlWrapper->exec($url . self::OAUTH_TOKEN_PATH);
		self::$errorCode = $curlWrapper->getHttpCode();
		
		return $response;
	}
	
	/**
	 * @param $authCode
	 * @return array|void
	 * @throws Exception
	 */
	public static function requestAuthorizationTokens($authCode)
	{
		KalturaLog::info('Requesting authorization tokens from Webex');
		list($webexBaseURL, $redirectUri, $clientId, $clientSecret, $header) = self::getHeaderData();
		$postFields = "grant_type=authorization_code&client_id=$clientId&client_secret=$clientSecret&code=$authCode&redirect_uri=$redirectUri";
		return self::retrieveTokensData($webexBaseURL, $header, $postFields);
	}
	
	public static function requestAccessToken($refreshToken)
	{
		KalturaLog::info('Requesting new access token from Webex');
		list($webexBaseURL, $redirectUri, $clientId, $clientSecret, $header) = self::getHeaderData();
		$postFields = "grant_type=refresh_token&client_id=$clientId&client_secret=$clientSecret&refresh_token=$refreshToken";
		return self::retrieveTokensData($webexBaseURL, $header, $postFields);
	}
}
