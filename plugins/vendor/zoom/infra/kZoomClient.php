<?php

/**
 * @package plugins.vendor
 * @subpackage zoom.model
 */
class kZoomClient extends kVendorClient
{
	const ZOOM_BASE_URL = 'ZoomBaseUrl';
	/** API */
	const API_USERS_ME          = 'me';
	const API_USERS             = '/v2/users/@userId@';
	const API_REPORT_PARTICIPANT       = '/v2/report/meetings/@meetingId@/participants';
	const API_METRICS_MEETINGS_PARTICIPANT       = '/v2/metrics/meetings/@meetingId@/participants';
	const API_METRICS_WEBINARS_PARTICIPANT       = '/v2/metrics/webinars/@webinarId@/participants';
	const API_PANELISTS         = '/v2/webinars/@webinarId@/panelists';
	const API_WEBINAR         = '/v2/webinars/@webinarId@';
	const API_USERS_PERMISSIONS = '/v2/users/@userId@/permissions';
	const API_DELETE_RECORDING_FILE = '/v2/meetings/@meetingId@/recordings/@recordingId@';
	const API_LIST_RECORDING = '/v2/accounts/@accountId@/recordings';
	const API_GET_MEETING_RECORDING = '/v2/meetings/@meetingId@/recordings';
	const API_GET_MEETING = '/v2/meetings/@meetingId@';

	protected $accountId;
	protected $zoomAuthType;
	protected $zoomTokensHelper;
	
	/**
	 * kZoomClient constructor.
	 * @param $zoomBaseURL
	 * @param null $accountId
	 * @param null $refreshToken
	 * @param null $accessToken
	 * @param null $accessExpiresIn
	 * @param null $zoomAuthType
	 * @throws KalturaAPIException
	 */
	public function __construct($zoomBaseURL, $accountId = null, $refreshToken = null,
								$accessToken = null, $accessExpiresIn = null, $zoomAuthType = null)
	{
		$this->baseURL = $zoomBaseURL;
		// check if at least one is available, otherwise throw exception
		if ($zoomAuthType == kZoomAuthTypes::OAUTH && $refreshToken == null && $accessToken == null)
		{
			throw new KalturaAPIException (KalturaZoomErrors::UNABLE_TO_AUTHENTICATE_OAUTH);
		}
		$this->accountId = $accountId;
		$this->refreshToken = $refreshToken;
		$this->accessToken = $accessToken;
		$this->accessExpiresIn = $accessExpiresIn;
		$this->zoomAuthType = $zoomAuthType;
		$this->zoomTokensHelper = new kZoomTokens($zoomBaseURL, $accountId, $zoomAuthType);
	}
	
	
	public function retrieveTokenZoomUserPermissions()
	{
		return $this->retrieveZoomUserPermissions(self::API_USERS_ME);
	}
	
	public function retrieveTokenZoomUser()
	{
		return $this->retrieveZoomUser(self::API_USERS_ME);
	}
	
	public function retrieveReportMeetingParticipant($meetingId)
	{
		$apiPath = str_replace('@meetingId@', $meetingId, self::API_REPORT_PARTICIPANT);
		return $this->callZoom($apiPath);
	}

	public function retrieveMetricsMeetingParticipant($meetingId)
	{
		$apiPath = str_replace('@meetingId@', $meetingId, self::API_METRICS_MEETINGS_PARTICIPANT);
		return $this->callZoom($apiPath);
	}

	public function retrieveMetricsWebinarParticipant($meetingId)
	{
		$apiPath = str_replace('@webinarId@', $meetingId, self::API_METRICS_WEBINARS_PARTICIPANT);
		return $this->callZoom($apiPath);
	}
	
	public function retrieveWebinarPanelists($webinarId)
	{
		$apiPath = str_replace('@webinarId@', $webinarId, self::API_PANELISTS);
		return $this->callZoom($apiPath);
	}

	public function retrieveWebinar($webinarId)
	{
		$apiPath = str_replace('@webinarId@', $webinarId, self::API_WEBINAR);
		return $this->callZoom($apiPath);
	}
	
	public function retrieveZoomUser($userName)
	{
		$apiPath = str_replace('@userId@', $userName, self::API_USERS);
		return $this->callZoom($apiPath);
	}
	
	public function retrieveZoomUserPermissions($userName)
	{
		$apiPath = str_replace('@userId@', $userName, self::API_USERS_PERMISSIONS);
		return $this->callZoom($apiPath);
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
	
	public function listRecordings($accountId, $dayToScan, $nextPageToken, $pageSize)
	{
		$apiPath = str_replace('@accountId@', $accountId, self::API_LIST_RECORDING);
		$apiPath .= '?page_size=' . $pageSize . '&next_page_token=' . $nextPageToken . '&from=' . $dayToScan . '&to=' . $dayToScan;
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
	 */
	protected function handleCurlResponse(&$response, $httpCode, $curlWrapper)
	{
		if (!$response || KCurlHeaderResponse::isError($httpCode) || $curlWrapper->getError())
		{
			$errMsg = "Zoom Curl returned error, Error code : $httpCode, Error: {$curlWrapper->getError()}";
			KalturaLog ::debug($errMsg);
			if($response)
			{
				KalturaLog::debug(print_r($response, true));
			}
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
		$token = $this->accessToken;

		$curlWrapper->setOpt(CURLOPT_HTTPHEADER , array(
			"authorization: Bearer {$token}",
			"content-type: application/json"
		));
		$response = $curlWrapper->exec($url);
		$httpCode = $curlWrapper->getHttpCode();
		$this->handleCurlResponse($response, $httpCode, $curlWrapper);
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

	protected function generateContextualUrl($apiPath)
	{
		return $this->baseURL . $apiPath . '?';
	}
	
	public function getRefreshToken()
	{
		return $this->refreshToken;
	}
	
	public function getAccessToken()
	{
		return $this->accessToken;
	}

	public function getAccessExpiresIn()
	{
		return $this->accessExpiresIn;
	}
}
