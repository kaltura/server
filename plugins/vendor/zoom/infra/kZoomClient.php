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
	
	protected $zoomBaseURL;
	protected $refreshToken;
	protected $jwtToken;
	
	/**
	 * kZoomClient constructor.
	 * @param $zoomBaseURL
	 */
	public function __construct($zoomBaseURL, $refreshToken = null, $jwtToken = null)
	{
		$this -> zoomBaseURL = $zoomBaseURL;
		// check if at least one is available, otherwise throw exception
		if ($refreshToken == null && $jwtToken == null) {
			throw new KalturaAPIException (KalturaZoomErrors::UNABLE_TO_AUTHENTICATE);
		}
		$this -> refreshToken = $refreshToken;
		$this -> jwtToken = $jwtToken;
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
	
	/**
	 * @param $response
	 * @param int $httpCode
	 * @param KCurlWrapper $curlWrapper
	 * @param $apiPath
	 */
	protected function handelCurlResponse(&$response, $httpCode, $curlWrapper, $apiPath)
	{
		if (!$response || $httpCode !== 200 || $curlWrapper -> getError())
		{
			$errMsg = "Zoom Curl returned error, Error code : $httpCode, Error: {$curlWrapper->getError()} ";
			KalturaLog ::debug($errMsg);
			$response = null;
		}
	}
	
	/**
	 * @param string $apiPath
	 * @param string $accessToken
	 * @return mixed
	 * @throws Exception
	 */
	public function callZoom($apiPath)
	{
		KalturaLog ::info('Calling zoom api: ' . $apiPath);
		$curlWrapper = new KCurlWrapper();
		if ($this->jwtToken != null) // if we have a jwt we need to use it to make the call
		{
			$url = $this -> zoomBaseURL . $apiPath . '?';
			$curlWrapper->setOpt(CURLOPT_HTTPHEADER , array(
			                           "authorization: Bearer {$this->jwtToken}",
				                     "content-type: application/json"
			                     ));
		}
		else
		{
			$tokens = kZoomOauth::refreshTokensViaRefreshToken($this->refreshToken);
			$accessToken = $tokens[kZoomOauth::REFRESH_TOKEN];
			$url = $this -> zoomBaseURL . $apiPath . '?' . 'access_token=' . $accessToken;
		}
		$response = $curlWrapper -> exec($url);
		$httpCode = $curlWrapper -> getHttpCode();
		$this -> handelCurlResponse($response, $httpCode, $curlWrapper, $apiPath);
		$data = json_decode($response, true);
		return $data;
	}
}