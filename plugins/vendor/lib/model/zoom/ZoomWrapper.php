<?php
/**
 * @package plugins.venodr
 * @subpackage model.zoom
 */
class ZoomWrapper
{

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
		KalturaLog::info("Calling zoom api: " . $apiPath);
		$zoomAuth = new kZoomOauth();
		$zoomConfiguration = kConf::get('ZoomAccount', 'vendor');
		$zoomBaseURL = $zoomConfiguration['ZoomBaseUrl'];
		if (!$tokens || $forceNewToken)
		{
			$tokens = $zoomAuth->retrieveTokensData($forceNewToken, $accountId);
		}
		list($response, $tokens) = self::callZoom($apiPath, $tokens, $accountId, $zoomBaseURL);
		$data = json_decode($response, true);
		return array($tokens, $data);
	}

	/**
	 * @param $apiPath
	 * @param $tokens
	 * @param $accountId
	 * @param $zoomBaseURL
	 * @return array
	 * @throws Exception
	 */
	private static function executeZoomCall($apiPath, $tokens, $accountId, $zoomBaseURL)
	{
		$accessToken = $tokens[kZoomOauth::ACCESS_TOKEN];
		$curlWrapper = new KCurlWrapper();
		$url = $zoomBaseURL . $apiPath . '?' . 'access_token=' . $accessToken;
		$response = $curlWrapper->exec($url);
		$httpCode = $curlWrapper->getInfo(CURLINFO_HTTP_CODE);
		list($tokens, $refreshed) = self::handelCurlResponse($response, $httpCode, $curlWrapper, $accountId, $tokens, $apiPath);
		return array($response, $tokens, $refreshed);
	}

	/**
	 * @param $response
	 * @param int $httpCode
	 * @param KCurlWrapper $curlWrapper
	 * @param $accountId
	 * @param $tokens
	 * @param $apiPath
	 * @return array<array, bool> token refreshed
	 * @throws Exception
	 */
	private static function handelCurlResponse(&$response, $httpCode, $curlWrapper, $accountId, $tokens, $apiPath)
	{
		//access token invalid and need to be refreshed
		if ($httpCode === 401 && $accountId)
		{
			KalturaLog::err("Zoom Curl returned  $httpCode, with massage: {$response} " . $curlWrapper->getError());
			/** @var ZoomVendorIntegration $zoomClientData */
			$zoomClientData = VendorIntegrationPeer::retrieveSingleVendorPerPartner($accountId, VendorTypeEnum::ZOOM_ACCOUNT);
			if (!$zoomClientData)
			{
				throw new KalturaAPIException('Zoom Integration data Does Not Exist for current Partner');
			}
			$zoomAuth = new kZoomOauth();
			return array($zoomAuth->refreshTokens($zoomClientData->getRefreshToken(), $zoomClientData), true);
		}
		// Sometimes we get  response 400, with massage: {"code":1010,"message":"User not belong to this account}
		//in this case do not refresh tokens, they are valid --> return null
		if ($httpCode === 400 && strpos($response, '1010') !== false)
		{
			KalturaLog::err(print_r($response, true));
			$response = null;
			return array($tokens, false);
		}
		//could Not find meeting -> zoom bug
		if ($httpCode === 404 && (strpos($apiPath, 'participants') !== false))
		{
			KalturaLog::info('participants api returned 404');
			KalturaLog::info(print_r($response, true));
			$response = null;
			return array($tokens, false);
		}
		//other error -> dieGracefully
		if (!$response || $httpCode !== 200 || $curlWrapper->getError())
		{
			KalturaLog::err("Zoom Curl returned error, Error code : $httpCode, Error: {$curlWrapper->getError()} ");
			KExternalErrors::dieGracefully();
		}
		return array($tokens, false);
	}

	/**
	 * @param $apiPath
	 * @param $tokens
	 * @param $accountId
	 * @param $zoomBaseURL
	 * @return array
	 * @throws Exception
	 */
	private static function callZoom($apiPath, $tokens, $accountId, $zoomBaseURL)
	{
		list($response, $tokens, $refreshed) = self::executeZoomCall($apiPath, $tokens, $accountId, $zoomBaseURL);
		if ($refreshed)
		{
			// in case we receive 401
			list($response, $tokens, ) = self::executeZoomCall($apiPath, $tokens, $accountId, $zoomBaseURL);
		}
		return array($response, $tokens);
	}
}