<?php
/**
 * @package plugins.venodr
 * @subpackage model.zoom
 */
class RetrieveDataFromZoom
{

	/**
	 * @param $apiPath
	 * @param bool $forceNewToken
	 * @param null $tokens
	 * @param null $accountId
	 * @return array
	 * @throws Exception
	 */
	public function retrieveZoomDataAsArray($apiPath, $forceNewToken = false, $tokens = null, $accountId = null)
	{
		KalturaLog::info("Calling zoom api: " . $apiPath);
		$zoomAuth = new kZoomOauth();
		$zoomConfiguration = kConf::get('ZoomAccount', 'vendor');
		$zoomBaseURL = $zoomConfiguration['ZoomBaseUrl'];
		if (!$tokens || $forceNewToken)
			$tokens = $zoomAuth->retrieveTokensData($forceNewToken, $accountId);
		list($response, $tokens, $refreshed) = $this->executeZoomCall($apiPath, $tokens, $accountId, $zoomBaseURL);
		if ($refreshed)
		{
			list($response, $tokens, ) = $this->executeZoomCall($apiPath, $tokens, $accountId, $zoomBaseURL);
		}
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
	private function executeZoomCall($apiPath, $tokens, $accountId, $zoomBaseURL)
	{
		$accessToken = $tokens[kZoomOauth::ACCESS_TOKEN];
		$curlWrapper = new KCurlWrapper();
		$url = $zoomBaseURL . $apiPath . '?' . 'access_token=' . $accessToken;
		$response = $curlWrapper->exec($url);
		$httpCode = $curlWrapper->getInfo(CURLINFO_HTTP_CODE);
		list($tokens, $refreshed) = $this->handelCurlResponse($response, $httpCode, $curlWrapper, $accountId, $tokens, $apiPath);
		return array($response, $tokens, $refreshed);
	}

	/**
	 * @param $response
	 * @param int $httpCode
	 * @param KCurlWrapper $curlWrapper
	 * @param $accountId
	 * @param $tokens
	 * @param $apiPath
	 * @return array<array,bool> token refreshed
	 * @throws Exception
	 */
	private function handelCurlResponse(&$response, $httpCode, $curlWrapper, $accountId, $tokens, $apiPath)
	{
		//access token invalid and need to be refreshed
		if (($httpCode === 400 || $httpCode === 401) && $accountId)
		{
			KalturaLog::err("Zoom Curl returned  $httpCode, with massage: {$response} " . $curlWrapper->getError());
			$zoomClientData = VendorIntegrationPeer::retrieveSingleVendorPerAccountAndType($accountId, VendorTypeEnum::ZOOM_ACCOUNT);
			$zoomAuth = new kZoomOauth();
			return array($zoomAuth->refreshTokens($zoomClientData->getRefreshToken(), $zoomClientData), true);
		}
		//could Not find meeting -> zoom bug
		if ($httpCode === 404 && (strpos($apiPath,'participants') !== false))
		{
			$response = null;
			return array($tokens, false);
		}
		//other error -> dieGracefully
		if (!$response || $httpCode !== 200 || $curlWrapper->getError())
		{
			KalturaLog::err('Zoom Curl returned error, Tokens were not received, Error: ' . $curlWrapper->getError());
			KExternalErrors::dieGracefully();
		}
		return array($tokens, false);
	}
}