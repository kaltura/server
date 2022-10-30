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
	const VERIFICATION_TOKEN = 'verificationToken';
	
	protected static function getHeaderData()
	{
	
	}
	
	protected static function curlRetrieveTokensData($url, $userPwd, $header, $postFields)
	{
	
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
			throw new KalturaAPIException(KalturaWebexAPIErrors::TOKEN_PARSING_FAILED);
		}
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
	 * set two minutes off the token expiration, avoid 401 response from vendor
	 * @param int $expiresIn
	 * @return int $expiresIn
	 */
	public static function getTokenExpiryRelativeTime($expiresIn)
	{
		$expiresIn = time() + $expiresIn - 120;
		KalturaLog::info("Set Token 'expires_in' to " . $expiresIn);
		return $expiresIn;
	}
	
	/**
	 * @param string $tokensData
	 * @param string $iv
	 * @param array $configuration
	 * @return array
	 * @throws Exception
	 */
	public static function handleEncryptTokens($tokensData, $iv, $configuration)
	{
		$verificationToken = $configuration[self::VERIFICATION_TOKEN];
		$tokensResponse = AESEncrypt::decrypt($verificationToken, $tokensData, $iv);
		$tokens = self::retrieveTokenData($tokensResponse);
		if (!$tokens)
		{
			KExternalErrors::dieGracefully();
		}
		
		return $tokens;
	}
}
