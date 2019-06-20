<?php
/**
 * @package plugins.venodr
 * @subpackage model.zoom
 */
class ZoomWrapper
{
	const ZOOM_BASE_URL = 'ZoomBaseUrl';
	const MAP_NAME = 'vendor';
	const CONFIGURATION_PARAM_NAME = 'ZoomAccount';
	const PARTICIPANTS = 'participants';

	/**
	 * @param $apiPath
	 * @param bool $forceNewToken
	 * @param null $tokens
	 * @param null $accountId
	 * @return array
	 * @throws Exception
	 */
	public static function retrieveZoomDataAsArray($apiPath, $forceNewToken = false, $tokens = null, $accountId = null)
	{
		KalturaLog::info('Calling zoom api: ' . $apiPath);
		$zoomAuth = new kZoomOauth();
		$zoomConfiguration = kConf::get(self::CONFIGURATION_PARAM_NAME, self::MAP_NAME);
		$zoomBaseURL = $zoomConfiguration[self::ZOOM_BASE_URL];
		if (!$tokens || $forceNewToken)
		{
			$tokens = $zoomAuth->retrieveTokensData($forceNewToken, $accountId);
		}

		list($response, $tokens) = self::callZoom($apiPath, $tokens, $accountId, $zoomBaseURL);
		$data = json_decode($response, true);
		return array($tokens, $data);
	}

	public static function retrieveZoomUserData($accessToken)
	{
		KalturaLog::info('Calling zoom api: ' . ZoomHelper::API_USERS_ME);
		$zoomConfiguration = kConf::get(self::CONFIGURATION_PARAM_NAME, self::MAP_NAME);
		$zoomBaseURL = $zoomConfiguration[self::ZOOM_BASE_URL];
		$response = self::callZoom(ZoomHelper::API_USERS_ME, $accessToken, $zoomBaseURL);
		$data = json_decode($response, true);
		return $data;
	}

	/**
	 * @param $apiPath
	 * @param ZoomVendorIntegration $zoomIntegration
	 * @return array
	 * @throws Exception
	 */
	public static function retrieveZoomData($apiPath, $zoomIntegration)
	{
		KalturaLog::info('Calling zoom api: ' . $apiPath);
		$zoomAuth = new kZoomOauth();
		$zoomConfiguration = kConf::get(self::CONFIGURATION_PARAM_NAME, self::MAP_NAME);
		$zoomBaseURL = $zoomConfiguration[self::ZOOM_BASE_URL];
		$accessToken = $zoomAuth->getValidAccessToken($zoomIntegration);
		$response = self::callZoom($apiPath, $accessToken, $zoomBaseURL);
		$data = json_decode($response, true);
		return $data;
	}

	/**
	 * @param $response
	 * @param int $httpCode
	 * @param KCurlWrapper $curlWrapper
	 * @param $apiPath
	 * @return array<array, bool> token refreshed
	 * @throws Exception
	 */
	private static function handelCurlResponse(&$response, $httpCode, $curlWrapper, $apiPath)
	{
		//access token invalid and need to be refreshed
		if($httpCode === 401)
		{
			KalturaLog::warning("Zoom Curl returned  $httpCode, with massage: {$response} " . $curlWrapper->getError());
			throw new kVendorException(kVendorException::TOKEN_EXPIRED);
		}

		// Sometimes we get  response 400, with massage: {"code":1010,"message":"User not belong to this account}
		//in this case do not refresh tokens, they are valid --> return null
		if($httpCode === 400 && strpos($response, '1010') !== false)
		{
			KalturaLog::warning("Zoom Curl returned  $httpCode, with massage: {$response} " . $curlWrapper->getError());
			$response = null;
		}

		//Could not find meeting -> zoom bug
		else if($httpCode === 404 && (strpos($apiPath, self::PARTICIPANTS) !== false))
		{
			KalturaLog::info('Zoom participants api returned 404');
			KalturaLog::info(print_r($response, true));
			$response = null;
		}

		//other error -> dieGracefully
		else if(!$response || $httpCode !== 200 || $curlWrapper->getError())
		{
			KalturaLog::err("Zoom Curl returned error, Error code : $httpCode, Error: {$curlWrapper->getError()} ");
			KExternalErrors::dieGracefully();
		}
	}

	/**
	 * @param string $apiPath
	 * @param string $accessToken
	 * @param string $zoomBaseURL
	 * @return array
	 * @throws Exception
	 */
	private static function callZoom($apiPath, $accessToken, $zoomBaseURL)
	{
		$curlWrapper = new KCurlWrapper();
		$url = $zoomBaseURL . $apiPath . '?' . 'access_token=' . $accessToken;
		$response = $curlWrapper->exec($url);
		$httpCode = $curlWrapper->getHttpCode();
		self::handelCurlResponse($response, $httpCode, $curlWrapper, $apiPath);
		return $response;
	}
}