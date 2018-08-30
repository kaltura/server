<?php
/**
 * @service zoomVendor
 * @package plugins.vendor
 * @subpackage api.services
 */
class ZoomVendorService extends KalturaBaseService
{



	/**
	 * no partner will be provided by vendors as this called externally and not from kaltura
	 * @param string $actionName
	 * @return bool
	 */
	protected function partnerRequired($actionName)
	{
		return false;
	}

	/**
	 * @action testInsert
	 * @return bool
	 * @throws PropelException
	 */
	function testInsertAction()
	{
		$vendorInteg = new VendorIntegration();
		$vendorInteg->setAccountId('accountID');
		$vendorInteg->setPartnerId(100);
		$vendorInteg->setVendorType(VendorTypeEnum::ZOOM);
		VendorIntegrationPeer::doInsert($vendorInteg);
		return true;
	}

	/**
	 *
	 * @action oauthValidation
	 * @return string
	 * @throws Exception
	 */
	function oauthValidationAction()
	{
		if(!kConf::hasMap('vendor'))
		{
			throw new kCoreException("vendor configuration file (vendor.ini) wasn't found!");
		}
		$zoomConfiguration = kConf::get('ZoomAccount', 'vendor');
		$clientId = $zoomConfiguration['clientId'];
		$zoomBaseURL = $zoomConfiguration['ZoomBaseUrl'];
		$redirectUrl = $zoomConfiguration['redirectUrl'];
		if (!array_key_exists('code', $_GET))
		{
			$url = $zoomBaseURL . 'oauth/authorize?' . 'response_type=code' . '&client_id=' . $clientId .  '&redirect_uri=' . $redirectUrl;
			$this->redirect($url);
		}
		else
		{
			$zoomAuth = new kZoomOauth();
			$data = $zoomAuth->retrieveTokensData();
			$tokens = $zoomAuth->extractTokensFromResponse($data);
			$vendorIntegration = new VendorIntegration();
			$vendorIntegration->setVendorType(VendorTypeEnum::ZOOM);
			$accessToken = $tokens[kZoomOauth::ACCESS_TOKEN];
			$zoomUserData = $this->retrieveZoomUserDataAsArray($accessToken, $zoomBaseURL);
			$zoomUserPermissions = $this->retrieveZoomUserpremissionsAsArray($accessToken, $zoomBaseURL);
			//encrypt and send to webpage

		}
		return true;
	}


	/**
	 * redirects to new URL
	 * @param $url
	 */
	private function redirect($url)
	{
		$redirect  = new kRendererRedirect($url);
		$redirect->output();
		KExternalErrors::dieGracefully();
	}

	/**
	 * @param string $accessToken
	 * @param string $zoomBaseURL
	 * @return array
	 * @throws Exception
	 */
	private function retrieveZoomUserDataAsArray($accessToken, $zoomBaseURL)
	{
		KalturaLog::info('Calling zoom auth');
		$curlWrapper = new KCurlWrapper();
		$url = $zoomBaseURL . '/v2/users/me?' . 'access_token=' . $accessToken;
		$response = $curlWrapper->exec($url);
		$httpCode = $curlWrapper->getInfo(CURLINFO_HTTP_CODE);
		if (!$response || $httpCode !== 200 || $curlWrapper->getError())
		{
			KalturaLog::err('Zoom Curl returned error, Tokens were not received, Error: ' . $curlWrapper->getError());
			KExternalErrors::dieGracefully();
		}
		return json_decode($response, true);
	}

	/**
	 * @param string $accessToken
	 * @param string $zoomBaseURL
	 * @return array
	 * @throws Exception
	 */
	private function retrieveZoomUserpremissionsAsArray($accessToken, $zoomBaseURL)
	{
		KalturaLog::info('Calling zoom auth');
		$curlWrapper = new KCurlWrapper();
		$url = $zoomBaseURL . '/v2/users/me/permissions?' . 'access_token=' . $accessToken;
		$response = $curlWrapper->exec($url);
		$httpCode = $curlWrapper->getInfo(CURLINFO_HTTP_CODE);
		if (!$response || $httpCode !== 200 || $curlWrapper->getError())
		{
			KalturaLog::err('Zoom Curl returned error, Tokens were not received, Error: ' . $curlWrapper->getError());
			KExternalErrors::dieGracefully();
		}
		return json_decode($response, true);
	}



}