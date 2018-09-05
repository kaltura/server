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
	const ACCOUNT_ID = "account_id";

	const USERS_ME = '/v2/users/me';

	const USERS_ME_PERMISSIONS = '/v2/users/me/permissions';

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
		$tokens = null;
		if (!array_key_exists('code', $_GET))
		{
			$url = $zoomBaseURL . '/oauth/authorize?' . 'response_type=code' . '&client_id=' . $clientId .  '&redirect_uri=' . $redirectUrl;
			$this->redirect($url);
		}
		else
		{
			$dataRetriever = new RetrieveDataFromZoom();
			list($tokens, $permissions) = $dataRetriever->retrieveZoomDataAsArray(self::USERS_ME_PERMISSIONS, true);
			list(, $user) = $dataRetriever->retrieveZoomDataAsArray(self::USERS_ME, false, $tokens, null);
			$accountId = $user[self::ACCOUNT_ID];
			$this->saveNewTokenData($tokens, $accountId); // table is empty
			$permissions = $permissions['permissions'];
			$isAdmin = $this->canConfigureEventSubscription($permissions);
		}
		if ($isAdmin)
		{
			$this->loadLoginPage($tokens);
		}
		return false;
	}

	/**
	 * @action fetchRegistrationPage
	 * @param string $tokensData
	 * @param string $iv
	 * @return string
	 * @throws Exception
	 */
	public function fetchRegistrationPageAction($tokensData, $iv)
	{
		$tokensData = base64_decode($tokensData);
		$iv = base64_decode($iv);
		$zoomConfiguration = kConf::get('ZoomAccount', 'vendor');
		$verificationToken = $zoomConfiguration['verificationToken'];
		$tokens = AESOauthZoom::aesDecrypt($verificationToken, $tokensData, $iv);
		$tokens = json_decode($tokens, true);
		$accessToken = $tokens[kZoomOauth::ACCESS_TOKEN];
		$retrieveDataFromZoom = new RetrieveDataFromZoom();
		list($tokens,$zoomUserData) = $retrieveDataFromZoom->retrieveZoomDataAsArray(self::USERS_ME, false, $tokens, null) ;
		$accountId = $zoomUserData[self::ACCOUNT_ID];
		if ($accessToken !== $tokens[kZoomOauth::ACCESS_TOKEN])
			$this->saveNewTokenData($tokens, $accountId);
		$partnerId = kCurrentContext::getCurrentPartnerId();
		$vendorIntegrationPeer = new VendorIntegrationPeer();
		$zoomIntegration = $vendorIntegrationPeer->retrieveSingleVendorPerAccountAndType($accountId,
					VendorTypeEnum::ZOOM_ACCOUNT);
		if (intval($partnerId) !== $zoomIntegration->getPartnerId() && $partnerId !== 0)
		{
			$zoomIntegration->setPartnerId($partnerId);
			$zoomIntegration->save();
		}
		$this->loadSubmitPage($zoomIntegration, $accountId);
		return false;
	}


	/**
	 * @action submitRegistration
	 * @param string $defaultUserId
	 * @param bool $uploadEnabled
	 * @param string $zoomCategory
	 * @param string $accountId
	 * @return string
	 * @throws PropelException
	 */
	public function submitRegistrationAction($defaultUserId, $uploadEnabled, $zoomCategory, $accountId)
	{
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
	 * @param array $tokens
	 * @throws Exception
	 */
	private function loadLoginPage($tokens)
	{
		$file_path = dirname(__FILE__) . "/../lib/api/webPage/zoom/kalturaZoomLoginPage.html";
		if (file_exists($file_path)) {
			$page = file_get_contents($file_path);
			$tokensString = json_encode($tokens);
			$zoomConfiguration = kConf::get('ZoomAccount', 'vendor');
			$verificationToken = $zoomConfiguration['verificationToken'];
			list($enc, $iv) = AESOauthZoom::aesEncrypt($verificationToken, $tokensString);
			$page = str_replace('@BaseServiceUrl@', requestUtils::getHost(), $page);
			$page = str_replace('@encryptData@', base64_encode($enc), $page);
			$page = str_replace('@iv@', base64_encode($iv), $page);
			echo $page;
			die();
		}
	}

	/**
	 * @param VendorIntegration $zoomIntegration
	 * @param $accountId
	 */
	private function loadSubmitPage($zoomIntegration, $accountId)
	{
		$file_path = dirname(__FILE__) . "/../lib/api/webPage/zoom/KalturaZoomRegistrationPage.html";
		if (file_exists($file_path)) {
			$page = file_get_contents($file_path);
			$page = str_replace('@ks@', $this->getKs()->getOriginalString(), $page);
			$page = str_replace('@BaseServiceUrl@', requestUtils::getHost(), $page);
			if (!is_null($zoomIntegration))
			{
				$page = str_replace('@defaultUserID@', $zoomIntegration->getDefaultUserEMail() , $page);
				$page = str_replace('@zoomCategory@', $zoomIntegration->getZoomCategory() ? $zoomIntegration->getZoomCategory()  : 'Zoom Recordings'  , $page);
				$page = str_replace('@checked@', $zoomIntegration->getEnableUpload() ? 'checked' : '' , $page);
			}
			else {
				$page = str_replace('@defaultUserID@', '' , $page);
				$page = str_replace('@zoomCategory@', 'Zoom Recordings' , $page);
				$page = str_replace('@checked@', '' , $page);
			}
			$page = str_replace('@accountId@', $accountId , $page);
			echo $page;
			die();
		}
	}

	/**
	 * @param $dataAsArray
	 * @param $accountId
	 * @throws PropelException
	 */
	private function saveNewTokenData($dataAsArray, $accountId)
	{
		$zoomClientData = VendorIntegrationPeer::retrieveSingleVendorPerAccountAndType($accountId, VendorTypeEnum::ZOOM_ACCOUNT);
		if (!$zoomClientData)
			$zoomClientData = new VendorIntegration();
		$zoomClientData->setExpiresIn($dataAsArray[kZoomOauth::EXPIRES_IN]);
		$zoomClientData->setAccessToken($dataAsArray[kZoomOauth::ACCESS_TOKEN]);
		$zoomClientData->setRefreshToken($dataAsArray[kZoomOauth::REFRESH_TOKEN]);
		$zoomClientData->setAccountId($accountId);
		$zoomClientData->setVendorType(VendorTypeEnum::ZOOM_ACCOUNT);
		$zoomClientData->save();
	}
}