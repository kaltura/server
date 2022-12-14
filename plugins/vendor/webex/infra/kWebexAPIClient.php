<?php
/**
 * @package plugins.WebexAPIDropFolder
 * @subpackage model
 */
class kWebexAPIClient extends kVendorClient
{
	const DELETE_SUCCESSFUL_CODE = 204;
	
	protected $responseHeader;
	
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
		$request = "admin/recordings?from=$startDate" . "T$startHour" . "&to=$endDate" . "T$endHour";
		return $this->sendRequest($request);
	}
	
	public function getRecording($recordingId, $hostEmail)
	{
		$request = "recordings/$recordingId" . "?hostEmail=$hostEmail";
		return $this->sendRequest($request);
	}
	
	public function deleteRecording($recordingId, $hostEmail)
	{
		$request = "recordings/$recordingId" . "?hostEmail=$hostEmail";
		$response = $this->sendRequest($request, false, true);
		if (!$this->errorCode == self::DELETE_SUCCESSFUL_CODE)
		{
			KalturaLog::warning("Deleting recording from Webex failed (Code {$this->errorCode}, response from Webex: " . print_r($response, true));
			return null;
		}
		return $response;
	}
	
	public function getMeeting($meetingId)
	{
		$request = "meetings/$meetingId";
		return $this->sendRequest($request);
	}
	
	public function getMeetingParticipants($meetingId, $hostEmail)
	{
		$request = "meetingParticipants?meetingId=$meetingId" . "&hostEmail=$hostEmail";
		return $this->sendRequest($request);
	}
	
	public function retrieveWebexUser()
	{
		$request = 'people/me';
		$response = $this->sendRequest($request);
		if (!isset($response['emails']))
		{
			KalturaLog::warning("Retrieve user from Webex failed (Code {$this->errorCode}), response from Webex: " . print_r($response, true));
			return null;
		}
		return $response['emails'][0];
	}
}
