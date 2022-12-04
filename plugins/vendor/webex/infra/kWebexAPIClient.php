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
		
		if (!$response)
		{
			$response = $curlWrapper->getErrorMsg();
		}
		else
		{
			$response = json_decode($response, true);
		}
		return $response;
	}
	
	public function getRecordingsList($lastFileTimestamp)
	{
		$dateFormat = 'Y-m-d';
		$startDate = date($dateFormat, $lastFileTimestamp);
		$endDate = date($dateFormat, time() + kTimeConversion::DAY);
		$request = "recordings?from=$startDate&to=$endDate";
		return $this->sendRequest($request);
	}
	
	public function getRecording($recordingId)
	{
		$request = "recordings/$recordingId";
		return $this->sendRequest($request);
	}
	
	public function retrieveWebexUser()
	{
		$request = 'people/me';
		$user = array('account_id' => 1);
		return $user;
	}
}
