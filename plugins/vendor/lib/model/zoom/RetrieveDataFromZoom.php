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
		KalturaLog::info('Calling zoom api : get user permissions');
		$zoomAuth = new kZoomOauth();
		$zoomConfiguration = kConf::get('ZoomAccount', 'vendor');
		$zoomBaseURL = $zoomConfiguration['ZoomBaseUrl'];
		if (!$tokens)
			$tokens = $zoomAuth->retrieveTokensData($forceNewToken, $accountId);
		$accessToken = $tokens[kZoomOauth::ACCESS_TOKEN];
		$curlWrapper = new KCurlWrapper();
		$url = $zoomBaseURL . $apiPath . '?' . 'access_token=' . $accessToken;
		$response = $curlWrapper->exec($url);
		$httpCode = $curlWrapper->getInfo(CURLINFO_HTTP_CODE);
		$tokens = $this->handelCurlResponse($response, $httpCode, $curlWrapper, $accountId, $tokens);
		$data = json_decode($response, true);
		return array($tokens, $data);
	}


	/**
	 * @param $response
	 * @param int $httpCode
	 * @param KCurlWrapper $curlWrapper
	 * @param $accountId
	 * @param $tokens
	 * @return array
	 * @throws Exception
	 */
	private function handelCurlResponse($response, $httpCode, $curlWrapper, $accountId, $tokens)
	{
		if (($httpCode === 400 || $httpCode === 401) && $accountId)
		{
			$zoomClientData = VendorIntegrationPeer::retrieveSingleVendorPerAccountAndType($accountId, VendorTypeEnum::ZOOM_ACCOUNT);
			$zoomAuth = new kZoomOauth();
			return $zoomAuth->refreshTokens($zoomClientData->getRefreshToken(), $zoomClientData);
		}
		if (!$response || $httpCode !== 200 || $curlWrapper->getError())
		{
			KalturaLog::err('Zoom Curl returned error, Tokens were not received, Error: ' . $curlWrapper->getError());
			KExternalErrors::dieGracefully();
		}
		return $tokens;
	}
}