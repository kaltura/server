<?php
/**
 * @package plugins.WebexAPIDropFolder
 * @subpackage model
 */
class kWebexAPIClient extends kVendorClient
{
	const DELETE_RECORDING_SUCCESSFUL_CODE = 204;
	const GET_TRANSCRIPT_SUCCESSFUL_CODE = 200;
	const GET_CHAT_SUCCESSFUL_CODE = 200;
	
	protected $nextPageLink;
	
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
	
	protected function initCurl($requestUrl, $requestHeaders, &$responseHeaders, $isRequestPost = false, $isRequestDelete = false)
	{
		$ch = curl_init();
		$protocol = '';
		$host = '';
		KCurlWrapper::setSourceUrl($ch, $requestUrl, $protocol, $host);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
		curl_setopt($ch, CURLOPT_POST, $isRequestPost);
		if ($isRequestDelete)
		{
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		}
		
		curl_setopt($ch, CURLOPT_HEADERFUNCTION,
			function($curl, $header) use (&$responseHeaders)
			{
				$len = strlen($header);
				$header = explode(':', $header, 2);
				if (count($header) < 2) // ignore invalid headers
					return $len;
				
				$responseHeaders[strtolower(trim($header[0]))][] = trim($header[1]);
				
				return $len;
			}
		);
		
		return $ch;
	}
	
	protected function sendRequest($request, $isRequestPost = false, $isRequestDelete = false, $decodeJson = true)
	{
		$this->httpCode = 0;
		
		$requestUrl = $this->addBaseUrlToRequest($request);
		$authorizationHeader = 'Authorization: Bearer ' . $this->accessToken;
		$requestHeaders = array($authorizationHeader);
		
		$ch = $this->initCurl($requestUrl, $requestHeaders, $responseHeaders, $isRequestPost, $isRequestDelete);
		
		$response = curl_exec($ch);
		$this->saveNextPageLinkFromHeaders($responseHeaders);
		$this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		if (!$response)
		{
			$response = curl_error($ch);
		}
		else
		{
			$response = $decodeJson ? json_decode($response, true) : $response;
		}
		return $response;
	}
	
	protected function addBaseUrlToRequest($request)
	{
		if (strpos($request, $this->baseURL) === false)
		{
			$request = $this->baseURL . $request;
		}
		return $request;
	}
	
	public function getNextPageLinkFromLastRequest()
	{
		return $this->nextPageLink;
	}
	
	protected function saveNextPageLinkFromHeaders($responseHeaders)
	{
		if (!isset($responseHeaders['link']) || !is_array($responseHeaders['link']))
		{
			$this->nextPageLink = null;
			return;
		}
		
		$matchResults = array();
		preg_match('/<(.*)>; rel="next"/', $responseHeaders['link'][0], $matchResults);
		if (isset($matchResults[1]))
		{
			$this->nextPageLink = $matchResults[1];
		}
	}
	
	protected function convertTimeToWebexFormat($time)
	{
		$dateFormat = 'Y-m-d';
		$hourFormat = 'H:i:s';
		$formattedDate = gmdate($dateFormat, $time);
		$formattedHour = gmdate($hourFormat, $time);
		return $formattedDate . 'T' . $formattedHour;
	}
	
	public function getRecordingsList($startTime, $endTime)
	{
		$formattedStartTime = $this->convertTimeToWebexFormat($startTime);
		$formattedEndTime = $this->convertTimeToWebexFormat($endTime);
		$request = "admin/recordings?from=$formattedStartTime&to=$formattedEndTime";
		return $this->sendRequest($request);
	}
	
	public function getMeetingRecordingsList($meetingId)
	{
		$request = "admin/recordings?meetingId=$meetingId";
		return $this->sendRequest($request);
	}
	
	public function sendRequestUsingDirectLink($directLink, $decodeJson = true)
	{
		return $this->sendRequest($directLink, false, false, $decodeJson);
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
		if ($this->httpCode != self::DELETE_RECORDING_SUCCESSFUL_CODE)
		{
			KalturaLog::warning("Deleting recording from Webex failed (Code {$this->httpCode}), response from Webex: " . print_r($response, true));
			return false;
		}
		return true;
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
			KalturaLog::warning("Retrieve user from Webex failed (Code {$this->httpCode}), response from Webex: " . print_r($response, true));
			return null;
		}
		return $response['emails'][0];
	}
	
	public function getMeetingTranscripts($meetingId, $hostEmail)
	{
		$request = "meetingTranscripts?meetingId=$meetingId&hostEmail=$hostEmail";
		return $this->sendRequest($request);
	}
	
	public function getTranscriptsBetweenTimes($siteUrl, $startTime, $endTime)
	{
		$formattedStartTime = $this->convertTimeToWebexFormat($startTime);
		$formattedEndTime = $this->convertTimeToWebexFormat($endTime);
		$request = "admin/meetingTranscripts?siteUrl=$siteUrl&from=$formattedStartTime&to=$formattedEndTime";
		return $this->sendRequest($request);
	}
	
	public function downloadTranscript($transcriptId)
	{
		$request = "meetingTranscripts/$transcriptId/download";
		$response = $this->sendRequest($request, false, false, false);
		if ($this->httpCode != self::GET_TRANSCRIPT_SUCCESSFUL_CODE)
		{
			return null;
		}
		return $response;
	}
	
	public function getMeetingChats($meetingId)
	{
		$request = "meetings/postMeetingChats?meetingId=$meetingId";
		$response = $this->sendRequest($request, false, false, false);
		if ($this->httpCode != self::GET_CHAT_SUCCESSFUL_CODE)
		{
			return null;
		}
		return $response;
	}
}
