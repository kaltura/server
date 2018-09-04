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
		if ($actionName == 'oauthValidation' || $actionName == 'recordingComplete')
			return false;
		return true;
	}

	/**
	 *
	 * @action oauthValidation
	 * @return string
	 * @throws Exception
	 */
	public function oauthValidationAction()
	{
		if(!kConf::hasMap('vendor'))
		{
			throw new kCoreException("vendor configuration file (vendor.ini) wasn't found!");
		}
		$zoomConfiguration = kConf::get('ZoomAccount', 'vendor');
		$clientId = $zoomConfiguration['clientId'];
		$zoomBaseURL = $zoomConfiguration['ZoomBaseUrl'];
		$redirectUrl = $zoomConfiguration['redirectUrl'];
		$isAdmin = false;
		if (!array_key_exists('code', $_GET))
		{
			$url = $zoomBaseURL . '/oauth/authorize?' . 'response_type=code' . '&client_id=' . $clientId .  '&redirect_uri=' . $redirectUrl;
			$this->redirect($url);
		}
		else
		{
			$permissions = $this->retrieveZoomUserPermissionsAsArray();
			$isAdmin = $this->canConfigureEventSubscription($permissions);
		}
		if ($isAdmin)
		{
			$this->loadLoginPage();
		}
		return false;
	}

	/**
	 * @action fetchRegistrationPage
	 * @return string
	 * @throws Exception
	 */
	public function fetchRegistrationPageAction()
	{
		$zoomUserData = $this->retrieveZoomUserDataAsArray();
		$accountId = $zoomUserData["account_id"];
		$partnerId = kCurrentContext::getCurrentPartnerId();
		$vendorIntegrationPeer = new VendorIntegrationPeer();
		$zoomIntegration = $vendorIntegrationPeer->retrieveSingleVendorPerPartner($accountId,
					VendorTypeEnum::ZOOM_ACCOUNT, $partnerId);
		$this->loadSubmitPage($zoomIntegration);
		return false;
	}


	/**
	 * @action submitRegistration
	 * @param string $defaultUserId
	 * @param bool $uploadEnabled
	 * @param string $zoomCategory
	 * @return string
	 * @throws Exception
	 */
	public function submitRegistrationAction($defaultUserId, $uploadEnabled, $zoomCategory)
	{
		$zoomUserData = $this->retrieveZoomUserDataAsArray();
		$accountId = $zoomUserData["account_id"];
		$partnerId = kCurrentContext::getCurrentPartnerId();
		$vendorIntegrationPeer = new VendorIntegrationPeer();
		$zoomIntegration = $vendorIntegrationPeer->retrieveSingleVendorPerPartner($accountId,
			VendorTypeEnum::ZOOM_ACCOUNT, $partnerId);
		if(is_null($zoomIntegration))
		{
			$zoomIntegration = new VendorIntegration();
			$zoomIntegration->setAccountId($accountId);
			$zoomIntegration->setVendorType(VendorTypeEnum::ZOOM_ACCOUNT);
			$zoomIntegration->setPartnerId($partnerId);
		}
		$zoomIntegration->setEnableUpload($uploadEnabled);
		$zoomIntegration->setDefaultUserEMail($defaultUserId);
		$zoomIntegration->setZoomCategory($zoomCategory);
		$zoomIntegration->save();
		return true;
	}

	/**
	 * @action recordingComplete
	 * @throws Exception
	 */
	public function recordingCompleteAction()
	{

		$zoomConfiguration = kConf::get('ZoomAccount', 'vendor');
		$zoomAuth = new kZoomOauth();
		$zoomBaseURL = $zoomConfiguration['ZoomBaseUrl'];
		$zoomPeer = new VendorIntegrationPeer();
		$account = $zoomPeer->retrieveSingleVendorPerPartner('9ekf-VdORCWVWXTCIKLIlw',1,100);
		$zoomUserData = $this->retrieveZoomUserDataAsArray($account->getAccessToken(), $zoomBaseURL);
		$test = 1;
	}

	/**
	 * @param array $zoomUserPermissions
	 * @return bool
	 */
	private function canConfigureEventSubscription($zoomUserPermissions)
	{
		if (in_array('Recording:Read', $zoomUserPermissions) && in_array('Recording:Edit', $zoomUserPermissions))
			return true;
		return false;
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
	 * @return array
	 * @throws Exception
	 */
	private function retrieveZoomUserDataAsArray()
	{
		KalturaLog::info('Calling zoom api : get user data');
		$zoomConfiguration = kConf::get('ZoomAccount', 'vendor');
		$zoomOauth = new kZoomOauth();
		$dataAsArray = $zoomOauth->retrieveTokensData();
		$zoomBaseURL = $zoomConfiguration['ZoomBaseUrl'];
		$accessToken = $dataAsArray[kZoomOauth::ACCESS_TOKEN];
		$curlWrapper = new KCurlWrapper();
		$url = $zoomBaseURL . '/v2/users/me?' . 'access_token=' . $accessToken;
		$response = $curlWrapper->exec($url);
		$httpCode = $curlWrapper->getInfo(CURLINFO_HTTP_CODE);
		$this->handelCurlResponse($response, $httpCode, $curlWrapper);
		return json_decode($response, true);
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	private function retrieveZoomUserPermissionsAsArray()
	{
		KalturaLog::info('Calling zoom api : get user permissions');
		$zoomAuth = new kZoomOauth();
		$zoomConfiguration = kConf::get('ZoomAccount', 'vendor');
		$zoomBaseURL = $zoomConfiguration['ZoomBaseUrl'];
		$tokens = $zoomAuth->retrieveTokensData(true);
		$accessToken = $tokens[kZoomOauth::ACCESS_TOKEN];
		$curlWrapper = new KCurlWrapper();
		$url = $zoomBaseURL . '/v2/users/me/permissions?' . 'access_token=' . $accessToken;
		$response = $curlWrapper->exec($url);
		$httpCode = $curlWrapper->getInfo(CURLINFO_HTTP_CODE);
		$this->handelCurlResponse($response, $httpCode, $curlWrapper);
		$data = json_decode($response, true);
		$permissions = $data['permissions'];
		return $permissions;
	}

	/**
	 * @throws Exception
	 */
	private function loadLoginPage()
	{
		$file_path = dirname(__FILE__) . "/../lib/api/webPage/zoom/kalturaZoomLoginPage.html";
		if (file_exists($file_path)) {
			$page = file_get_contents($file_path);
			$page = str_replace('@BaseServiceUrl@', requestUtils::getHost(), $page);
			echo $page;
			die();
		}
	}

	/**
	 * @param VendorIntegration $zoomIntegration
	 */
	private function loadSubmitPage($zoomIntegration)
	{
		$file_path = dirname(__FILE__) . "/../lib/api/webPage/zoom/KalturaZoomRegistrationPage.html";
		if (file_exists($file_path)) {
			$page = file_get_contents($file_path);
			$page = str_replace('@ks@', $this->getKs()->getOriginalString(), $page);
			$page = str_replace('@BaseServiceUrl@', requestUtils::getHost(), $page);
			if (!is_null($zoomIntegration))
			{
				$page = str_replace('@defaultUserID@', $zoomIntegration->getDefaultUserEMail() , $page);
				$page = str_replace('@zoomCategory@', $zoomIntegration->getZoomCategory()  , $page);
				$page = str_replace('@checked@', $zoomIntegration->getEnableUpload() ? 'checked' : '' , $page);
			}
			else {
				$page = str_replace('@defaultUserID@', '' , $page);
				$page = str_replace('@zoomCategory@', 'Zoom Recordings' , $page);
				$page = str_replace('@checked@', '' , $page);
			}
			echo $page;
			die();
		}
	}

	/**
	 * @param $response
	 * @param int $httpCode
	 * @param KCurlWrapper $curlWrapper
	 */
	private function handelCurlResponse($response, $httpCode, $curlWrapper)
	{
		if (!$response || $httpCode !== 200 || $curlWrapper->getError()) {
			KalturaLog::err('Zoom Curl returned error, Tokens were not received, Error: ' . $curlWrapper->getError());
			KExternalErrors::dieGracefully();
		}
	}

}