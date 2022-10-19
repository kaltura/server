<?php
/**
 * @package plugins.vendor
 * @subpackage model.oAuth
 */
abstract class kOAuth
{
	const ACCESS_TOKEN = 'access_token';
	const REFRESH_TOKEN = 'refresh_token';
	const EXPIRES_IN = 'expires_in';
	
	protected static function getHeaderData()
	{
	
	}
	
	protected static function curlRetrieveTokensData($url, $userPwd, $header, $postFields)
	{
	
	}
	
	protected static function retrieveTokenData($response)
	{

	}
	
	public static function requestAccessToken($authCode)
	{
	
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
	
	/**
	 * @param array $data
	 * @return array tokens
	 */
	public static function extractTokensFromData($data)
	{
		return array(self::ACCESS_TOKEN => $data[self::ACCESS_TOKEN], self::REFRESH_TOKEN => $data[self::REFRESH_TOKEN],
			self::EXPIRES_IN => $data[self::EXPIRES_IN]);
	}
	
	/**
	 * set two minutes off the token expiration, avoid 401 response from zoom
	 * @param int $expiresIn
	 * @return int $expiresIn
	 */
	public static function getTokenExpiryRelativeTime($expiresIn)
	{
		$expiresIn = time() + $expiresIn - 120;
		KalturaLog::info("Set Token 'expires_in' to " . $expiresIn);
		return $expiresIn;
	}
}