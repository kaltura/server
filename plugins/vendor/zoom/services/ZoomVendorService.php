<?php
/**
 * @service zoomVendor
 * @package plugins.vendor
 * @subpackage api.services
 */
class ZoomVendorService extends KalturaBaseService
{
	const MAP_NAME = 'vendor';
	const CONFIGURATION_PARAM_NAME = 'ZoomAccount';

	protected static $PARTNER_NOT_REQUIRED_ACTIONS = array('oauthValidation', 'recordingComplete');

	/**
	 * no partner will be provided by vendors as this called externally and not from kaltura
	 * @param string $actionName
	 * @return bool
	 */
	protected function partnerRequired($actionName)
	{
		return in_array ($actionName, self::$PARTNER_NOT_REQUIRED_ACTIONS);
	}

	/**
	 * @return array
	 * @throws KalturaAPIException
	 * @throws Exception
	 */
	public static function getZoomConfiguration()
	{
		if(!kConf::hasMap(self::MAP_NAME))
		{
			throw new KalturaAPIException(KalturaZoomErrors::NO_VENDOR_CONFIGURATION);
		}

		return kConf::get(self::CONFIGURATION_PARAM_NAME, self::MAP_NAME);
	}

	/**
	 * @param $serviceId
	 * @param $serviceName
	 * @param $actionName
	 * @throws KalturaAPIException
	 */
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
	}
	
/********** For Testing Zoom Integration *******/
	private function dummyAddZoomIntegration()
	{

		$zoomIntegration = new ZoomVendorIntegration();
		$zoomIntegration->setAccountId('LtY7sEaRQaCGNzOqy5GEA');
//		$zoomIntegration->setAccountSecret('temp');
		$zoomIntegration->setExpiresIn('9999999999');
		$zoomIntegration->setAccessToken('Access_Token');
		$zoomIntegration->setRefreshToken('eyJhbGciOiJIUzUxMiIsInYiOiIyLjAiLCJraWQiOiJmODBmNDQxNi0zN2Q5LTRlNjQtYTc0My1jNDhiMjgzZjJmY2EifQ.eyJ2ZXIiOjcsImF1aWQiOiIzZjFkOTJjZDcxNDA2NGZlOGMxMDBkYThlNzdjZGQ3MiIsImNvZGUiOiJqRm5EOVF1T2FFX2JsTGJYU2pTU3pLd1JrNmRRTks2bWciLCJpc3MiOiJ6bTpjaWQ6THRZN3NFYVJRYUNHTnpPcXk1R0VBIiwiZ25vIjowLCJ0eXBlIjoxLCJ0aWQiOjExMiwiYXVkIjoiaHR0cHM6Ly9vYXV0aC56b29tLnVzIiwidWlkIjoiYmxMYlhTalNTekt3Ums2ZFFOSzZtZyIsIm5iZiI6MTYxNDgwOTk4OSwiZXhwIjoyMDg3ODQ5OTg5LCJpYXQiOjE2MTQ4MDk5ODksImFpZCI6Im9xZ2F0V0JkUUhhUlZVb2FtVHlOWXciLCJqdGkiOiJmYWNjNjhiYS00ZjYzLTRkMTYtYmQ2ZS1lYTMwMDI1Nzc3ZWUifQ.DeVhJi5S-81B5nnoDdWi5JNgH7dKd1zcEADjUsXaYSCBJFfZygDUsP0s7zn0nYVagWXmV6L6_tWb3PJPdgxkHg');
		$zoomIntegration->setJwtToken('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhdWQiOm51bGwsImlzcyI6IkU1NXk2eU5oVHlPRFg4OFk4Y2V3N0EiLCJleHAiOjE2MTUyNzY0MjksImlhdCI6MTYxNDY3MTYyOX0.L7vLT02hkjvNOHGA3Bcad78jcMxxawanYYv2CQP12fA');
		$zoomIntegration->setPartnerId('102');
		$zoomIntegration->setStatus('2');
		$zoomIntegration->setVendorType(VendorTypeEnum::ZOOM_ACCOUNT);
		$zoomIntegration->save();
		ZoomHelper::setZoomIntegration($zoomIntegration);

	}
	/*TODO:
	public function jwtValidationAction($apiKey, $secret)
	this function will generate the jwt token and create a zoomIntegrationObject
	need to set: the correct accountId, partnerId, wanted status, expiration
	should also get access and refresh token at this point or not?
	
	to generate the jwt_token we can use jwtHelper.php but it does not comply with zoom needs. need to check with
	Moshe.
	
	Another question is how we get the api_key and secret. Will they be in an external file for each partner?
	
	*/
	
	
	/**
	 *
	 * @action oauthValidation
	 * @return string
	 * @throws Exception
	 */
	public function oauthValidationAction()
	{
		echo 'ppp';
		return $this->dummyAddZoomIntegration();
		
		
		KalturaResponseCacher::disableCache();
		$zoomConfiguration = self::getZoomConfiguration();
		$clientId = $zoomConfiguration['clientId'];
		$zoomBaseURL = $zoomConfiguration[kZoomClient::ZOOM_BASE_URL];
		$redirectUrl = $zoomConfiguration['redirectUrl'];
		$isAdmin = false;
		$tokens = null;
		if(!array_key_exists('code', $_GET))
		{
			$url = $zoomBaseURL . '/oauth/authorize?' . 'response_type=code' . '&client_id=' . $clientId .  '&redirect_uri=' . $redirectUrl;
			ZoomHelper::redirect($url);
		}
		else
		{
			$authCode = $_GET['code'];
			$tokens  = kZoomOauth::requestAccessToken($authCode);
			$accessToken = $tokens[kZoomOauth::ACCESS_TOKEN];
			$client = new kZoomClient($zoomBaseURL);
			$permissions = $client->retrieveTokenZoomUserPermissions();
			$user = $client->retrieveTokenZoomUser();
			$accountId = $user[ZoomHelper::ACCOUNT_ID];
			$zoomIntegration = ZoomHelper::getZoomIntegrationByAccountId($accountId, true);
			if(!$zoomIntegration)
			{
				$zoomIntegration = new ZoomVendorIntegration();
				$zoomIntegration->setAccountId($accountId);
				ZoomHelper::setZoomIntegration($zoomIntegration);
			}

			$zoomIntegration->setTokensData($tokens);
			$zoomIntegration->save();
			$permissions = $permissions['permissions'];
			$isAdmin = ZoomHelper::canConfigureEventSubscription($permissions);
		}

		if($isAdmin)
		{
			ZoomHelper::loadLoginPage($tokens, $zoomConfiguration);
		}

		throw new KalturaAPIException(KalturaZoomErrors::ZOOM_ADMIN_REQUIRED);
	}

	/**
	 * @action deAuthorization
	 * @return string
	 * @throws Exception
	 */
	public function deAuthorizationAction()
	{
		http_response_code(KCurlHeaderResponse::HTTP_STATUS_BAD_REQUEST);
		KalturaResponseCacher::disableCache();
		myPartnerUtils::resetAllFilters();
		kZoomOauth::verifyHeaderToken(self::getZoomConfiguration());
		$data = ZoomHelper::getPayloadData();
		$accountId = ZoomHelper::extractAccountIdFromDeAuthPayload($data);
		KalturaLog::info("Zoom changing account id: $accountId status to deleted, user de-authorized the app");
		$zoomIntegration = ZoomHelper::getZoomIntegrationByAccountId($accountId);
		if(!$zoomIntegration)
		{
			throw new KalturaAPIException(KalturaZoomErrors::NO_INTEGRATION_DATA);
		}

		$zoomIntegration->setStatus(VendorStatus::DELETED);
		$zoomIntegration->save();
		http_response_code(KCurlHeaderResponse::HTTP_STATUS_OK);
		return true;
	}

	/**
	 * @action fetchRegistrationPage
	 * @param string $tokensData
	 * @param string $iv
	 * @throws KalturaAPIException
	 * @throws PropelException
	 * @throws Exception
	 */
	public function fetchRegistrationPageAction($tokensData, $iv)
	{
		KalturaResponseCacher::disableCache();
		$tokensData = base64_decode($tokensData);
		$iv = base64_decode($iv);
		$zoomConfiguration = self::getZoomConfiguration();
		$tokens = $this->handleEncryptTokens($tokensData, $iv, $zoomConfiguration);
		$zoomBaseURL = $zoomConfiguration[kZoomClient::ZOOM_BASE_URL];
		$client = new kZoomClient($zoomBaseURL);
		$zoomUserData = $client->retrieveTokenZoomUser();
		$accountId = $zoomUserData[ZoomHelper::ACCOUNT_ID];
		$zoomIntegration = ZoomHelper::getZoomIntegrationByAccountId($accountId);
		$partnerId = kCurrentContext::getCurrentPartnerId();
		if($zoomIntegration && intval($partnerId) !==  $zoomIntegration->getPartnerId() && $partnerId !== 0)
		{
			KalturaLog::info("Zoom changing account id: $accountId partner to $partnerId");
			$zoomIntegration->setPartnerId($partnerId);
			$zoomIntegration->setTokensData($tokens);
			$zoomIntegration->save();
		}

		ZoomHelper::loadSubmitPage($zoomIntegration, $accountId, $this->getKs());
	}

	/**
	 * @param string $tokensData
	 * @param string $iv
	 * @param array $zoomConfiguration
	 * @return array
	 * @throws Exception
	 */
	protected function handleEncryptTokens($tokensData, $iv, $zoomConfiguration)
	{
		$verificationToken = $zoomConfiguration[kZoomOauth::VERIFICATION_TOKEN];
		$tokensResponse = AESEncrypt::decrypt($verificationToken, $tokensData, $iv);
		$tokens = kZoomOauth::parseTokensResponse($tokensResponse);
		kZoomOauth::validateToken($tokens);
		$tokens = kZoomOauth::extractTokensFromData($tokens);
		$expiresIn = $tokens[kZoomOauth::EXPIRES_IN];
		$tokens[kZoomOauth::EXPIRES_IN] = kZoomOauth::getTokenExpiryAbsoluteTime($expiresIn);
		if(!$tokens)
		{
			KExternalErrors::dieGracefully();
		}

		return $tokens;
	}

	/**
	 * @action submitRegistration
	 * @param string $accountId
	 * @param KalturaZoomIntegrationSetting $integrationSetting
	 * @return string
	 * @throws KalturaAPIException
	 * @throws PropelException
	 */
	public function submitRegistrationAction($accountId, $integrationSetting)
	{
		KalturaResponseCacher::disableCache();
		$partnerId = kCurrentContext::getCurrentPartnerId();

		/** @var ZoomVendorIntegration $zoomIntegration */
		$zoomIntegration = ZoomHelper::getZoomIntegrationByAccountId($accountId);
		if(!$zoomIntegration || $zoomIntegration->getPartnerId() != $partnerId)
		{
			throw new KalturaAPIException(KalturaZoomErrors::NO_INTEGRATION_DATA);
		}

		kuserPeer::createKuserForPartner($partnerId, $integrationSetting->defaultUserId);
		$this->configureZoomCategories($integrationSetting, $zoomIntegration);
		$integrationSetting->toInsertableObject($zoomIntegration);
		$zoomIntegration->save();
		return true;
	}

	/**
	 * @param KalturaZoomIntegrationSetting $integrationSetting
	 * @param ZoomVendorIntegration $zoomIntegration
	 * @throws PropelException
	 */
	protected function configureZoomCategories($integrationSetting, &$zoomIntegration)
	{
		if($integrationSetting->zoomCategory)
		{
			if(ZoomHelper::createCategoryForZoom($zoomIntegration->getPartnerId(), $integrationSetting->zoomCategory))
			{
				$zoomIntegration->setZoomCategory($integrationSetting->zoomCategory);
			}
		}
		else
		{
			$zoomIntegration->unsetCategory();
		}

		if($integrationSetting->zoomWebinarCategory)
		{
			if(ZoomHelper::createCategoryForZoom($zoomIntegration->getPartnerId(), $integrationSetting->zoomWebinarCategory))
			{
				$zoomIntegration->setZoomWebinarCategory($integrationSetting->zoomWebinarCategory);
			}
		}
		else
		{
			$zoomIntegration->unsetWebinarCategory();
		}
	}

	/**
	 * @action recordingComplete
	 * @throws Exception
	 */
	public function recordingCompleteAction()
	{
		KalturaResponseCacher::disableCache();
		myPartnerUtils::resetAllFilters();
		$kZoomEventHandler = new kZoomEventHanlder(self::getZoomConfiguration());
		$event = $kZoomEventHandler->parseEvent();
		$zoomIntegration = ZoomHelper::getZoomIntegrationByAccountId($event->accountId);
		ZoomHelper::verifyZoomIntegration($zoomIntegration);
		$this->setPartnerFilters($zoomIntegration->getPartnerId());
		$kZoomEventHandler->processEvent($event);
	}

	/**
	 * Retrieve zoom integration setting object by partner id
	 *
	 * @action get
	 * @param int $partnerId
	 * @return KalturaZoomIntegrationSetting
	 * @throws APIErrors::INVALID_PARTNER_ID
	 */
	public function getAction($partnerId)
	{
		/** @var ZoomVendorIntegration $zoomIntegration */
		$zoomIntegration = VendorIntegrationPeer::retrieveSingleVendorByPartner($partnerId, VendorTypeEnum::ZOOM_ACCOUNT);
		if($zoomIntegration)
		{
			$integrationSetting = new KalturaZoomIntegrationSetting();
			$integrationSetting->fromObject($zoomIntegration);
			return $integrationSetting;
		}

		throw new KalturaAPIException(KalturaErrors::INVALID_PARTNER_ID, $partnerId);
	}
}
