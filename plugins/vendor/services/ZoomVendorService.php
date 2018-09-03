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
	public function testInsertAction()
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
		$data = null;
		$dyc = null;
		if (!array_key_exists('code', $_GET))
		{
			$url = $zoomBaseURL . 'oauth/authorize?' . 'response_type=code' . '&client_id=' . $clientId .  '&redirect_uri=' . $redirectUrl;
			$this->redirect($url);
		}
		else
		{
			list($data, $isAdmin) = $this->extractDataFromZoom($zoomBaseURL);
		}
		if ($isAdmin)
		{
			$this->loadLoginPage($zoomConfiguration, $data);
		}
		return false;
	}

	/**
	 * @action fetchRegistrationPage
	 * @param string $data
	 * @param string $iv
	 * @return string
	 * @throws Exception
	 */
	public function fetchRegistrationPageAction($data, $iv)
	{
		$zoomUserData = $this->retrieveZoomUserData($data, $iv);
		$accountId = $zoomUserData["account_id"];
		$partnerId = kCurrentContext::getCurrentPartnerId();
		$vendorIntegrationPeer = new VendorIntegrationPeer();
		$zoomIntegration = $vendorIntegrationPeer->retrieveSingleVendorPerPartner($accountId,
					VendorTypeEnum::ZOOM, $partnerId);
		$this->loadSubmitPage($data, $iv, $zoomIntegration);
		return false;
	}

	/**
	 * @action submitRegistration
	 * @param string $defaultUserId
	 * @param bool $uploadEnabled
	 * @param string $zoomCategory
	 * @param string $tokenData
	 * @param string $iv
	 * @return string
	 * @throws Exception
	 */
	public function submitRegistrationAction($defaultUserId, $uploadEnabled, $zoomCategory, $tokenData, $iv)
	{
		$zoomUserData = $this->retrieveZoomUserData($tokenData, $iv);
		$accountId = $zoomUserData["account_id"];
		$partnerId = kCurrentContext::getCurrentPartnerId();
		$vendorIntegrationPeer = new VendorIntegrationPeer();
		$zoomIntegration = $vendorIntegrationPeer->retrieveSingleVendorPerPartner($accountId,
			VendorTypeEnum::ZOOM, $partnerId);
		if(is_null($zoomIntegration))
		{
			$zoomIntegration = new VendorIntegration();
			$zoomIntegration->setAccountId($accountId);
			$zoomIntegration->setVendorType(VendorTypeEnum::ZOOM);
			$zoomIntegration->setPartnerId($partnerId);
		}
		$zoomConfiguration = kConf::get('ZoomAccount', 'vendor');
		$dataArray = $this->extractTokenData($tokenData, $iv, $zoomConfiguration);
		$zoomIntegration->setEnableUpload($uploadEnabled);
		$zoomIntegration->setDefaultUserEMail($defaultUserId);
		$accessToken = $dataArray[kZoomOauth::ACCESS_TOKEN];
		$refreshToken = $dataArray[kZoomOauth::REFRESH_TOKEN];
		$expiresIn = $dataArray[kZoomOauth::EXPIRES_IN];
		$zoomIntegration->setAccessToken($accessToken);
		$zoomIntegration->setRefreshToken($refreshToken);
		$zoomIntegration->setExpiresIn($expiresIn);
		$zoomIntegration->setZoomCategory($zoomCategory);
		$zoomIntegration->save();
		return true;
	}

	/**
	 * @param string $zoomBaseURL
	 * @return array
	 * @throws Exception
	 */
	private function extractDataFromZoom($zoomBaseURL)
	{
		$zoomAuth = new kZoomOauth();
		$data = $zoomAuth->retrieveTokensData();
		$data = $this->setValidUntil($data);
		$tokens = $zoomAuth->extractTokensFromResponse($data);
		$vendorIntegration = new VendorIntegration();
		$vendorIntegration->setVendorType(VendorTypeEnum::ZOOM);
		$accessToken = $tokens[kZoomOauth::ACCESS_TOKEN];
		$zoomUserPermissions = $this->retrieveZoomUserPermissionsAsArray($accessToken, $zoomBaseURL);
		$isAdmin = $this->canConfigureEventSubscription($zoomUserPermissions);
		return array($data, $isAdmin);
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
	 * @param string $accessToken
	 * @param string $zoomBaseURL
	 * @return array
	 * @throws Exception
	 */
	private function retrieveZoomUserDataAsArray($accessToken, $zoomBaseURL)
	{
		KalturaLog::info('Calling zoom api : get user data');
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
	private function retrieveZoomUserPermissionsAsArray($accessToken, $zoomBaseURL)
	{
		KalturaLog::info('Calling zoom api : get user permissions');
		$curlWrapper = new KCurlWrapper();
		$url = $zoomBaseURL . '/v2/users/me/permissions?' . 'access_token=' . $accessToken;
		$response = $curlWrapper->exec($url);
		$httpCode = $curlWrapper->getInfo(CURLINFO_HTTP_CODE);
		if (!$response || $httpCode !== 200 || $curlWrapper->getError())
		{
			KalturaLog::err('Zoom Curl returned error, Tokens were not received, Error: ' . $curlWrapper->getError());
			KExternalErrors::dieGracefully();
		}
		$data = json_decode($response, true);
		$permissions = $data['permissions'];
		return $permissions;
	}

	/**
	 * @param $data
	 * @return string
	 */
	private function setValidUntil($data)
	{
		$decode = json_decode($data, true);
		//apptoken is valid for 58 minutes(after that refresh)
		$expiresIn = $decode['expires_in'];
		$decode['expires_in'] = time() + $expiresIn - 120;
		return json_encode($decode);
	}

	/**
	 * @param $zoomConfiguration
	 * @param $data token data
	 * @throws Exception
	 */
	private function loadLoginPage($zoomConfiguration, $data)
	{
		$file_path = dirname(__FILE__) . "/../lib/api/webPage/zoom/kalturaZoomLoginPage.html";
		if (file_exists($file_path)) {
			$verificationToken = $zoomConfiguration['verificationToken'];
			list($enc, $iv) = aESHelper::aesEncrypt($verificationToken, $data);
			$page = file_get_contents($file_path);
			$page = str_replace('@encryptData@', base64_encode($enc), $page);
			$page = str_replace('@iv@', base64_encode($iv), $page);
			$page = str_replace('@BaseServiceUrl@', requestUtils::getHost(), $page);
			echo $page;
			die();
		}
	}

	/**
	 * @param $enc
	 * @param $iv
	 * @param VendorIntegration $zoomIntegration
	 */
	private function loadSubmitPage($enc, $iv, $zoomIntegration)
	{
		$file_path = dirname(__FILE__) . "/../lib/api/webPage/zoom/KalturaZoomRegistrationPage.html";
		if (file_exists($file_path)) {
			$page = file_get_contents($file_path);
			$page = str_replace('@encryptData@', $enc, $page);
			$page = str_replace('@iv@', $iv, $page);
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
	 * @param $data
	 * @param $iv
	 * @param $zoomConfiguration
	 * @return array
	 */
	private function extractTokenData($data, $iv, $zoomConfiguration)
	{
		$verificationToken = $zoomConfiguration['verificationToken'];
		$data = base64_decode($data);
		$iv = base64_decode($iv);
		$dec = aESHelper::aesDecrypt($verificationToken, $data, $iv);
		$dec = rtrim($dec, "\0");
		$dec = json_decode($dec, true);
		return $dec;
	}

	/**
	 * @param $data
	 * @param $iv
	 * @return array
	 * @throws Exception
	 */
	private function retrieveZoomUserData($data, $iv)
	{
		$zoomConfiguration = kConf::get('ZoomAccount', 'vendor');
		$dataAsArray = $this->extractTokenData($data, $iv, $zoomConfiguration);
		$zoomBaseURL = $zoomConfiguration['ZoomBaseUrl'];
		$accessToken = $dataAsArray[kZoomOauth::ACCESS_TOKEN];
		$zoomUserData = $this->retrieveZoomUserDataAsArray($accessToken, $zoomBaseURL);
		return $zoomUserData;
	}

}