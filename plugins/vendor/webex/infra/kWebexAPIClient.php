<?php
/**
 * @package plugins.WebexAPIDropFolder
 * @subpackage model
 */
class kWebexAPIClient extends kVendorClient
{
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
		
		$this->baseURL = $webexBaseURL;
		$this->refreshToken = $refreshToken;
		$this->accessToken = $accessToken;
		$this->clientId = $clientId;
		$this->clientSecret = $clientSecret;
		$this->accessExpiresIn = $accessExpiresIn;
	}
	
	protected function sendRequest($request, $isRequestPost = false, $isRequestDelete = false)
	{
		$this->errorCode = 0;
		
		$requestUrl = $this->baseURL . $request;
		$authorizationHeader = 'Authorization: Bearer ' . $this->accessToken;
		$requestHeaders = array($authorizationHeader);
		
		$curlWrapper = new KCurlWrapper();
		$curlWrapper->setOpt(CURLOPT_POST, $isRequestPost);
		if ($isRequestDelete)
		{
			$curlWrapper->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
		}
		$curlWrapper->setOpt(CURLOPT_HEADER, true);
		$curlWrapper->setOpt(CURLOPT_HTTPHEADER, $requestHeaders);
		$response = $curlWrapper->exec($requestUrl);
		
		$this->errorCode = $curlWrapper->getErrorNumber();
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
	
	public function getRecordingsList($startTime, $endTime)
	{
		$dateFormat = 'Y-m-d';
		$hourFormat = 'H:i:s';
		$startDate = date($dateFormat, $startTime);
		$startHour = date($hourFormat, $startTime);
		$endDate = date($dateFormat, $endTime);
		$endHour = date($hourFormat, $endTime);
		$request = "recordings?from=$startDate" . "T$startHour" . "&to=$endDate" . "T$endHour";
		return $this->sendRequest($request);
	}
	
	public function getRecording($recordingId)
	{
		$request = "recordings/$recordingId";
		return $this->sendRequest($request);
	}
	
	public function deleteRecording($recordingId)
	{
		$request = "recordings/$recordingId";
		return $this->sendRequest($request, false, true);
	}
	
	public function retrieveWebexUser()
	{
		$request = 'people/me';
		$response = $this->sendRequest($request);
		if (!isset($response['emails']))
		{
			KalturaLog::warning("Retrieve user from Webex failed (Code {$this->errorCode}), response from Webex: ". print_r($response, true));
			throw new KalturaAPIException(KalturaWebexAPIErrors::RETRIEVE_USER_FAILED);
		}
		return $response['emails'][0];
	}
}
