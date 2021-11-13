<?php

/**
 * @package plugins.vendor
 * @subpackage zoom.model
 */
class kZoomClient
{
	const ZOOM_BASE_URL = 'ZoomBaseUrl';
	const PARTICIPANTS  = 'participants';
	
	/** API */
	const API_USERS_ME          = 'me';
	const API_USERS             = '/v2/users/@userId@';
	const API_PARTICIPANT       = '/v2/report/meetings/@meetingId@/participants';
	const API_PANELISTS         = '/v2/webinars/@webinarId@/panelists';
	const API_USERS_PERMISSIONS = '/v2/users/@userId@/permissions';
	const API_DELETE_RECORDING_FILE = '/v2/meetings/@meetingId@/recordings/@recordingId@';
	const API_LIST_RECORDING = '/v2/accounts/@accountId@/recordings';
	const API_GET_MEETING_RECORDING = '/v2/meetings/@meetingId@/recordings';
	const API_GET_MEETING = '/v2/meetings/@meetingId@';
	
	protected $zoomBaseURL;
	protected $refreshToken;
	protected $accessToken;
	protected $jwtToken;
	protected $clientId;
	protected $clientSecret;
	protected $zoomTokensHelper;
	
	/**
	 * kZoomClient constructor.
	 * @param $zoomBaseURL
	 * @param null $jwtToken
	 * @param null $refreshToken
	 * @param null $clientId
	 * @param null $clientSecret
	 * @param null $accessToken
	 * @throws KalturaAPIException
	 */
	public function __construct($zoomBaseURL, $jwtToken = null, $refreshToken = null, $clientId = null,
	                            $clientSecret= null, $accessToken = null)
	{
		$this -> zoomBaseURL = $zoomBaseURL;
		// check if at least one is available, otherwise throw exception
		if ($refreshToken == null && $jwtToken == null && $accessToken == null)
		{
			throw new KalturaAPIException (KalturaZoomErrors::UNABLE_TO_AUTHENTICATE);
		}
		$this -> refreshToken = $refreshToken;
		$this -> jwtToken = $jwtToken;
		$this->clientId = $clientId;
		$this->clientSecret = $clientSecret;
		$this->accessToken = $accessToken;
		$this->zoomTokensHelper = new kZoomTokens($zoomBaseURL, $clientId, $clientSecret);
	}
	
	
	public function retrieveTokenZoomUserPermissions()
	{
		return $this -> retrieveZoomUserPermissions(self::API_USERS_ME);
	}
	
	public function retrieveTokenZoomUser()
	{
		return $this -> retrieveZoomUser(self::API_USERS_ME);
	}
	
	public function retrieveMeetingParticipant($meetingId)
	{
		$apiPath = str_replace('@meetingId@', $meetingId, self::API_PARTICIPANT);
		return $this -> callZoom($apiPath);
	}
	
	public function retrieveWebinarPanelists($webinarId)
	{
		$apiPath = str_replace('@webinarId@', $webinarId, self::API_PANELISTS);
		return $this -> callZoom($apiPath);
	}
	
	public function retrieveZoomUser($userName)
	{
		$apiPath = str_replace('@userId@', $userName, self::API_USERS);
		return $this -> callZoom($apiPath);
	}
	
	public function retrieveZoomUserPermissions($userName)
	{
		$apiPath = str_replace('@userId@', $userName, self::API_USERS_PERMISSIONS);
		return $this -> callZoom($apiPath);
	}
	
	protected function resolveMeetingUUId($meetingUUid)
	{
		if ($meetingUUid[0] == '/' || strpos($meetingUUid, '//') !== false)
		{
			$meetingUUid = urlencode(urlencode($meetingUUid));
		}
		return $meetingUUid;
	}
	
	public function deleteRecordingFile($meetingUUid, $recodingId)
	{
		$meetingUUid = self::resolveMeetingUUId($meetingUUid);
		$apiPath = str_replace('@meetingId@', $meetingUUid, self::API_DELETE_RECORDING_FILE);
		$apiPath = str_replace('@recordingId@', $recodingId, $apiPath);
		$apiPath .= '?action=trash';
		$options = array(CURLOPT_CUSTOMREQUEST => 'DELETE');
		return $this->callZoom($apiPath, $options);
	}
	
	public function listRecordings($accountId, $from, $to, $nextPageToken, $pageSize)
	{
		$apiPath = str_replace('@accountId@', $accountId, self::API_LIST_RECORDING);
		$apiPath .= '?page_size=' . $pageSize . '&next_page_token=' . $nextPageToken . '&from=' . $from . '&to=' . $to;
		return $this->callZoom($apiPath);
	}
	
	public function getMeetingRecordings($meetingUUid)
	{
		$meetingUUid = self::resolveMeetingUUId($meetingUUid);
		$apiPath = str_replace('@meetingId@', $meetingUUid, self::API_GET_MEETING_RECORDING);
		return $this->callZoom($apiPath);
	}
	
	public function getFileSize($meetingUUid, $recodingId)
	{
		$meetingRecordings = $this->getMeetingRecordings($meetingUUid);
		if ($meetingRecordings && isset($meetingRecordings[kZoomRecording::RECORDING_FILES]))
		{
			$recordingFiles = $meetingRecordings[kZoomRecording::RECORDING_FILES];
			foreach ($recordingFiles as $recordingFile)
			{
				if ($recordingFile[kZoomRecordingFile::ID] === $recodingId)
				{
					return $recordingFile[kZoomRecordingFile::FILE_SIZE];
				}
			}
		}
		return 0;
	}
	
	
	public function retrieveMeeting($meetingId)
	{
		$apiPath = str_replace('@meetingId@', $meetingId, self::API_GET_MEETING);
		return $this->callZoom($apiPath);
	}
	
	public function retrieveTrackingField($meetingId)
	{
		$meeting = $this->retrieveMeeting($meetingId);
		$categoryName = '';
		$categoryPath = '';
		if ($meeting && isset($meeting[kZoomRecording::TRACKING_FIELDS]))
		{
			foreach ($meeting[kZoomRecording::TRACKING_FIELDS] as $trackingField)
			{
				if ($trackingField[kZoomRecording::FIELD] === kZoomRecording::KALTURA_CATEGORY)
				{
					$categoryName = $trackingField[kZoomRecording::VALUE];
				}
				if ($trackingField[kZoomRecording::FIELD] === kZoomRecording::KALTURA_CATEGORY_PATH)
				{
					$categoryPath = $trackingField[kZoomRecording::VALUE];
					if(trim($categoryPath))
					{
						$categoryPath = (substr($categoryPath, -1) !== '>') ? $categoryPath . '>' : $categoryPath;
					}
				}
			}
		}
		KalturaLog::debug('Tracking field are: path: ' . $categoryPath . ' name: ' . $categoryName);
		if ($categoryPath && !$categoryName)
		{
			KalturaLog::debug('Tracking field path without category name could not be published');
			return null;
		}
		return $categoryPath . $categoryName;
	}
	
	/**
	 * @param $response
	 * @param int $httpCode
	 * @param KCurlWrapper $curlWrapper
	 * @param $apiPath
	 */
	protected function handleCurlResponse(&$response, $httpCode, $curlWrapper, $apiPath)
	{
		$JWTResponse = $this->checkJWTResponse($response);
		if ($JWTResponse != $response)
		{
			return 'JWT error: ' . $JWTResponse;
		}
		if (!$response || KCurlHeaderResponse::isError($httpCode) || $curlWrapper -> getError())
		{
			$errMsg = "Zoom Curl returned error, Error code : $httpCode, Error: {$curlWrapper->getError()} ";
			KalturaLog ::debug($errMsg);
			$response = null;
		}
	}
	
	/**
	 * @param string $apiPath
	 * @return mixed
	 * @throws Exception
	 */
	public function callZoom(string $apiPath, array $options = array())
	{
		KalturaLog::info('Calling zoom api: ' . $apiPath);
		$curlWrapper = new KCurlWrapper();
		$curlWrapper->setOpts($options);
		
		$url = $this->generateContextualUrl($apiPath);
		if ($this->jwtToken != null) // if we have a jwt we need to use it to make the call
		{
			$token = $this->jwtToken;
		}
		else
		{
			$token = $this->accessToken;
		}
		$curlWrapper->setOpt(CURLOPT_HTTPHEADER , array(
			"authorization: Bearer {$token}",
			"content-type: application/json"
		));
		$response = $curlWrapper -> exec($url);
		$httpCode = $curlWrapper -> getHttpCode();
		$this -> handleCurlResponse($response, $httpCode, $curlWrapper, $apiPath);
		if (!$response)
		{
			$data = $curlWrapper->getErrorMsg();
		}
		else
		{
			$data = json_decode($response, true);
		}
		return $data;
	}
	
	protected function checkJWTResponse($response)
	{
		if ($this->jwtToken != null)
		{
			$decodedResponse = json_decode($response, true);
			if (isset($decodedResponse['code']))
			{
				KalturaLog::ERR('Error calling Zoom - Code: ' . $decodedResponse['code'] . ' Reason: ' .
				                $decodedResponse['message']);
				return $decodedResponse['message'];
			}
		}
		return $response;
	}
	
	protected function generateContextualUrl($apiPath)
	{
		$url = $this -> zoomBaseURL . $apiPath . '?';
		if ($this->refreshToken)
		{
			if (!$this->accessToken)
			{
				$this->refreshTokens();
			}
		}
		return $url;
	}
	
	public function refreshTokens()
	{
		$tokens = null;
		if (!$this->jwtToken)
		{
			$tokens = $this -> zoomTokensHelper -> refreshTokens($this -> refreshToken);
			$this -> accessToken = $tokens[kZoomTokens::ACCESS_TOKEN];
			$this -> refreshToken = $tokens[kZoomTokens::REFRESH_TOKEN];
		}
		return $tokens;
		
	}
	
	public function getRefreshToken()
	{
		return $this->refreshToken;
	}
	
	public function getAccessToken()
	{
		return $this->accessToken;
	}
}