<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.model
 */
class kZoomClient
{
	const ZOOM_BASE_URL = 'ZoomBaseUrl';
	const PARTICIPANTS = 'participants';

	/** API */
	const API_USERS_ME = 'me';
	const API_USERS = '/v2/users/@userId@';
	const API_PARTICIPANT = '/v2/report/meetings/@meetingId@/participants';
	const API_PANELISTS = '/v2/webinars/@webinarId@/panelists';
	const API_USERS_PERMISSIONS = '/v2/users/@userId@/permissions';
	const API_DELETE_RECORDING_FILE = 'v2/meetings/@meetingId@/recordings/@recordingId@';
	const MEETING_ID_PLACEHOLDER = '@meetingId@';
	const RECORDING_ID_PLACEHOLDER = '@recordingId@';
	const WEBINAR_ID_PLACEHOLDER = '@webinderId@';


	protected $zoomBaseURL;

	/**
	 * kZoomClient constructor.
	 * @param $zoomBaseURL
	 */
	public function __construct($zoomBaseURL)
	{
		$this->zoomBaseURL = $zoomBaseURL;
	}


	public function retrieveTokenZoomUserPermissions($accessToken)
	{
		return $this->retrieveZoomUserPermissions(self::API_USERS_ME, $accessToken);
	}

	public function retrieveTokenZoomUser($accessToken)
	{
		return $this->retrieveZoomUser(self::API_USERS_ME, $accessToken);
	}

	public function retrieveMeetingParticipant($accessToken, $meetingId)
	{
		$apiPath = str_replace(self::MEETING_ID_PLACEHOLDER, $meetingId, self::API_PARTICIPANT);
		return $this->callZoom($apiPath, $accessToken);
	}

	public function retrieveWebinarPanelists($accessToken, $webinarId)
	{
		$apiPath = str_replace(self::WEBINAR_ID_PLACEHOLDER, $webinarId, self::API_PANELISTS);
		return $this->callZoom($apiPath, $accessToken);
	}

	public function retrieveZoomUser($userName, $accessToken)
	{
		$apiPath = str_replace('@userId@', $userName, self::API_USERS);
		return $this->callZoom($apiPath, $accessToken);
	}

	public function retrieveZoomUserPermissions($userName, $accessToken)
	{
		$apiPath = str_replace('@userId@', $userName, self::API_USERS_PERMISSIONS);
		return $this->callZoom($apiPath, $accessToken);
	}

	public function deleteRecordingFile($accessToken, $meetingUUid, $recodingId)
	{
		$apiPath = str_replace(self::MEETING_ID_PLACEHOLDER, $meetingUUid, self::API_DELETE_RECORDING_FILE);
		$apiPath = str_replace(self::RECORDING_ID_PLACEHOLDER, $recodingId, $apiPath);
		return $this->callZoom($apiPath, $accessToken);
	}

	/**
	 * @param $response
	 * @param int $httpCode
	 * @param KCurlWrapper $curlWrapper
	 * @param $apiPath
	 */
	protected function handelCurlResponse(&$response, $httpCode, $curlWrapper, $apiPath)
	{
		if(!$response || $httpCode !== 200 || $curlWrapper->getError())
		{
			$errMsg = "Zoom Curl returned error, Error code : $httpCode, Error: {$curlWrapper->getError()} ";
			KalturaLog::debug($errMsg);
			$response = null;
		}
	}

	/**
	 * @param string $apiPath
	 * @param string $accessToken
	 * @return mixed
	 * @throws Exception
	 */
	public function callZoom($apiPath, $accessToken)
	{
		KalturaLog::info('Calling zoom api: ' . $apiPath);
		$curlWrapper = new KCurlWrapper();
		$url = $this->zoomBaseURL . $apiPath . '?' . 'access_token=' . $accessToken;
		$response = $curlWrapper->exec($url);
		$httpCode = $curlWrapper->getHttpCode();
		$this->handelCurlResponse($response, $httpCode, $curlWrapper, $apiPath);
		$data = json_decode($response, true);
		return $data;
	}
}