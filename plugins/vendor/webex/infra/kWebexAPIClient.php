<?php

/**
 * @package plugins.WebexAPIDropFolder
 * @subpackage model
 */
class kWebexAPIClient
{
	protected $webexBaseURL;
	protected $refreshToken;
	protected $accessToken;
	protected $clientId;
	protected $clientSecret;
	protected $accessExpiresIn;
	protected $zoomTokensHelper;
	
	/**
	 * kWebexAPIClient constructor.
	 * @param $webexBaseURL
	 * @param null $refreshToken
	 * @param null $clientId
	 * @param null $clientSecret
	 * @param null $accessToken
	 * @param null $accessExpiresIn
	 * @throws KalturaAPIException
	 */
	public function __construct($webexBaseURL, $refreshToken = null, $clientId = null,
								$clientSecret= null, $accessToken = null, $accessExpiresIn = null)
	{
		if ($refreshToken == null && $accessToken == null)
		{
			throw new KalturaAPIException (KalturaWebexAPIErrors::UNABLE_TO_AUTHENTICATE);
		}
		
		$this->webexBaseURL = $webexBaseURL;
		$this->refreshToken = $refreshToken;
		$this->accessToken = $accessToken;
		$this->clientId = $clientId;
		$this->clientSecret = $clientSecret;
		$this->accessExpiresIn = $accessExpiresIn;
		$this->zoomTokensHelper = new kZoomTokens($webexBaseURL, $clientId, $clientSecret);
	}
	
	public function getRecordings()
	{
		$earliestTime = 1662034849;
		$dateFormat = 'Y-m-d';
		$startDate = date($dateFormat, $earliestTime);
		$endDate = date($dateFormat, time());
		$request = "recordings?from=$startDate&to=$endDate";
		return $this->sendRequest($request);
	}
	
	protected function sendRequest($request, $isRequestPost = false)
	{
		$webexConfiguration = WebexAPIDropFolderPlugin::getWebexConfiguration();
		$webexBaseURL = $webexConfiguration['baseUrl'];
		
		$requestUrl = $webexBaseURL . $request;
		$authorizationHeader = 'Authorization: Bearer ' . $this->accessToken;
		$requestHeaders = array($authorizationHeader);
		$curlWrapper = new KCurlWrapper();
		$curlWrapper->setOpt(CURLOPT_POST, $isRequestPost);
		$curlWrapper->setOpt(CURLOPT_HEADER, true);
		$curlWrapper->setOpt(CURLOPT_HTTPHEADER, $requestHeaders);
		$response = $curlWrapper->exec($requestUrl);
		
		return $response;
	}
	
	public function retrieveWebexUser()
	{
		$user = array('account_id' => 1);
		return $user;
	}
}
