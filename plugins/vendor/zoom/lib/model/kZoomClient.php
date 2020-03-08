<?php
/**
 * @package plugins.venodr
 * @subpackage zoom.model
 */
class kZoomClient
{
	const ZOOM_BASE_URL = 'ZoomBaseUrl';
	const MAP_NAME = 'vendor';
	const CONFIGURATION_PARAM_NAME = 'ZoomAccount';
	const PARTICIPANTS = 'participants';

	/** API */
	const API_USERS_ME = '/v2/users/me';
	const API_PARTICIPANT = '/v2/report/meetings/@meetingId@/participants';
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

	/**
	 * @param $response
	 * @param int $httpCode
	 * @param KCurlWrapper $curlWrapper
	 * @param $apiPath
	 */
	protected function handelCurlResponse(&$response, $httpCode, $curlWrapper)
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
		$this->handelCurlResponse($response, $httpCode, $curlWrapper);
		$data = json_decode($response, true);
		return $data;
	}
}