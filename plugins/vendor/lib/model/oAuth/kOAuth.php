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
	const SECRET_TOKEN = 'secretToken';

	protected static $errorCode;
	
	protected static function getHeaderData()
	{
		return array();
	}
	
	protected static function curlRetrieveTokensData($url, $userPwd, $header, $postFields)
	{
		self::$errorCode = 0;

		$curlWrapper = new KCurlWrapper();
		$curlWrapper->setOpt(CURLOPT_POST, 1);
		$curlWrapper->setOpt(CURLOPT_HTTPHEADER, $header);
		$curlWrapper->setOpt(CURLOPT_POSTFIELDS, $postFields);
		$response = $curlWrapper->exec($url);
		self::$errorCode = $curlWrapper->getHttpCode();

		return $response;
	}
	
	public static function requestAuthorizationTokens($authCode)
	{
		return null;
	}
	
	/**
	 * @param $response
	 * @return array $tokensData
	 * @throws Exception
	 */
	protected static function retrieveTokensDataFromResponse($response)
	{
		$tokensData = self::parseTokensResponse($response);
		if (!self::validateTokens($tokensData))
		{
			throw new KalturaAPIException(KalturaVendorIntegrationErrors::TOKEN_PARSING_FAILED);
		}
		$tokensData = self::extractTokensFromData($tokensData);
		$tokensData[self::EXPIRES_IN] = self::getTokenExpiryRelativeTime($tokensData[self::EXPIRES_IN]);
		return $tokensData;
	}

	/**
	 * @param $response
	 * @return array $tokensData
	 * @throws Exception
	 */
	protected static function retrieveAccessTokenDataFromResponse($response)
	{
		$tokensData = self::parseTokensResponse($response);
		if (!$tokensData || !isset($tokensData[self::ACCESS_TOKEN]) || !isset($tokensData[self::EXPIRES_IN]))
		{
			throw new KalturaAPIException(KalturaVendorIntegrationErrors::TOKEN_PARSING_FAILED);
		}
		$tokensData = array(self::ACCESS_TOKEN => $tokensData[self::ACCESS_TOKEN], self::EXPIRES_IN => $tokensData[self::EXPIRES_IN]);
		$tokensData[self::EXPIRES_IN] = self::getTokenExpiryRelativeTime($tokensData[self::EXPIRES_IN]);
		return $tokensData;
	}
	
	/**
	 * @param string $response
	 * @return array
	 * @throws Exception
	 */
	public static function parseTokensResponse($response)
	{
		$dataAsArray = json_decode($response, true);
		return $dataAsArray;
	}
	
	public static function validateTokens($tokensData)
	{
		if (!$tokensData || !isset($tokensData[self::REFRESH_TOKEN]) || !isset($tokensData[self::ACCESS_TOKEN]) ||
			!isset($tokensData[self::EXPIRES_IN]))
		{
			return false;
		}
		
		return true;
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
	 * Set two minutes off the token expiration, avoid 401 response from vendor
	 * @param int $expiresIn
	 * @return int $expiresIn
	 */
	public static function getTokenExpiryRelativeTime($expiresIn)
	{
		$expiresIn = time() + $expiresIn - kTimeConversion::MINUTE * 2;
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
		$tokens = self::retrieveTokensDataFromResponse($tokensResponse);
		if (!$tokens)
		{
			KExternalErrors::dieGracefully();
		}
		
		return $tokens;
	}
}
