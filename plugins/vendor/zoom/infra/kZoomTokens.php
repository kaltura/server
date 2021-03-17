<?php

class kZoomTokens
{
	const OAUTH_TOKEN_PATH = '/oauth/token';
	const ACCESS_TOKEN = 'access_token';
	const REFRESH_TOKEN = 'refresh_token';
	const AUTHORIZATION_HEADER = 'Authorization';
	const EXPIRES_IN = 'expires_in';
	const SCOPE = 'scope';
	
	protected $zoomBaseURL;
	protected $clientId;
	protected $clientSecret;
	
	public function __construct($zoomBaseURL, $clientId, $clientSecret)
	{
		$this->zoomBaseURL = $zoomBaseURL;
		$this->clientId = $clientId;
		$this->clientSecret = $clientSecret;
	}
	
	public function generateAccessToken($refreshToken)
	{
		KalturaLog::debug('Refreshing access token from token ' . $refreshToken);
		$postFields = "grant_type=refresh_token&refresh_token=$refreshToken";
		$response = $this->curlRetrieveTokensData($postFields);
		$tokensData = $this->parseTokensResponse($response);
		KalturaLog::debug('New tokens response' . print_r($tokensData, true));
		return $tokensData[self::ACCESS_TOKEN];
	}
	
	public function refreshTokens($oldRefreshToken)
	{
		KalturaLog::info('Refreshing Zoom Tokens');
		$postFields = "grant_type=refresh_token&refresh_token=$oldRefreshToken";
		$response = $this->curlRetrieveTokensData($postFields);
		$tokensData = $this->retrieveTokenData($response);
		return $tokensData;
	}
	
	protected function retrieveTokenData($response)
	{
		$tokensData = $this->parseTokensResponse($response);
		$this->validateToken($tokensData);
		$tokensData = $this->extractTokensFromData($tokensData);
		return $tokensData;
	}
	
	protected function validateToken($tokensData)
	{
		if (!$tokensData || !isset($tokensData[self::REFRESH_TOKEN]) || !isset($tokensData[self::ACCESS_TOKEN]) ||
			!isset($tokensData[self::EXPIRES_IN]))
		{
			ZoomHelper::exitWithError(kZoomErrorMessages::TOKEN_PARSING_FAILED . print_r($tokensData));
		}
	}
	
	public function extractTokensFromData($data)
	{
		return array(self::ACCESS_TOKEN => $data[self::ACCESS_TOKEN], self::REFRESH_TOKEN => $data[self::REFRESH_TOKEN],
		             self::EXPIRES_IN => $data[self::EXPIRES_IN]);
	}
	
	protected function parseTokensResponse($response)
	{
		$dataAsArray = json_decode($response, true);
		if(strpos($response, 'error'))
		{
			KalturaLog::ERR('Error calling Zoom: ' . $dataAsArray['reason']);
			throw new KalturaAPIException ('Error calling Zoom: ' . $dataAsArray['reason']);
		}
		KalturaLog::debug(print_r($dataAsArray, true));
		return $dataAsArray;
	}
	
	protected function curlRetrieveTokensData($postFields)
	{
		$clientId = $this->clientId;
		$clientSecret = $this->clientSecret;
		$userPwd = "$clientId:$clientSecret";
		$curlWrapper = new KCurlWrapper();
		$curlWrapper->setOpt(CURLOPT_POST, 1);
		$curlWrapper->setOpt(CURLOPT_HEADER, true);
		$curlWrapper->setOpt(CURLOPT_HTTPHEADER, "authorization: Basic {$userPwd}");
		$curlWrapper->setOpt(CURLOPT_USERPWD, $userPwd);
		$curlWrapper->setOpt(CURLOPT_POSTFIELDS, $postFields);
		return $curlWrapper->exec($this->zoomBaseURL . self::OAUTH_TOKEN_PATH);
	}
	
}