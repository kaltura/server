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
	const API_USERS_ME = '/v2/users/me';
	const API_USERS = '/v2/users/@userId@';
	const API_PARTICIPANT = '/v2/report/meetings/@meetingId@/participants';
	const API_PANELISTS = '/v2/webinars/@webinarId@/panelists';
	const API_USERS_ME_PERMISSIONS = '/v2/users/me/permissions';

	protected $zoomBaseURL;

	/**
	 * kZoomClient constructor.
	 * @param $zoomBaseURL
	 */
	public function __construct($zoomBaseURL)
	{
		$this->zoomBaseURL = $zoomBaseURL;
	}


	public function retrieveZoomUserPermissions($accessToken)
	{
		return $this->callZoom(self::API_USERS_ME_PERMISSIONS, $accessToken);
	}

	public function retrieveZoomUserData($accessToken)
	{
		return $this->callZoom(self::API_USERS_ME, $accessToken);
	}

	public function retrieveMeetingParticipant($accessToken, $meetingId)
	{
		$apiPath = str_replace('@meetingId@', $meetingId, self::API_PARTICIPANT);
		return $this->callZoom($apiPath, $accessToken);
	}

	public function retrieveWebinarPanelists($accessToken, $webinarId)
	{
		$apiPath = str_replace('@webinarId@', $webinarId, self::API_PANELISTS);
		return $this->callZoom($apiPath, $accessToken);
	}

	public function retrieveZoomUser($userName, $accessToken)
	{
		$apiPath = str_replace('@userId@', $userName, self::API_USERS);
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