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

	/**
	 * @param $response
	 * @param int $httpCode
	 * @param KCurlWrapper $curlWrapper
	 * @param $apiPath
	 */
	protected function handelCurlResponse(&$response, $httpCode, $curlWrapper, $apiPath)
	{
		//access token invalid and need to be refreshed
		if($httpCode === 401)
		{
			ZoomHelper::exitWithError(kZoomErrorMessages::TOKEN_EXPIRED);
		}

		// Sometimes we get  response 400, with massage: {"code":1010,"message":"User not belong to this account}
		//in this case do not refresh tokens, they are valid --> return null
		if($httpCode === 400 && strpos($response, '1010') !== false)
		{
			ZoomHelper::exitWithError(kZoomErrorMessages::USER_NOT_BELONG_TO_ACCOUNT);
		}

		//Could not find meeting -> zoom bug
		else if($httpCode === 404 && (strpos($apiPath, self::PARTICIPANTS) !== false))
		{
			KalturaLog::debug('Zoom participants api returned 404');
			KalturaLog::debug(print_r($response, true));
			$response = null;
		}

		//other error -> dieGracefully
		else if(!$response || $httpCode !== 200 || $curlWrapper->getError())
		{
			$errMsg = "Zoom Curl returned error, Error code : $httpCode, Error: {$curlWrapper->getError()} ";
			ZoomHelper::exitWithError($errMsg);
		}
	}

	/**
	 * @param string $apiPath
	 * @param string $accessToken
	 * @return mixed
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