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
	const INTEGRATION_CODE = 'integrationCode';
	const AUTH_CODE = 'code';
	const REGISTRATION_PAGE_PATH = '/../lib/api/webPage/KalturaZoomRegistrationPage.html';
	
	protected static $PARTNER_NOT_REQUIRED_ACTIONS = array('oauthValidation', 'recordingComplete', 'preOauthValidation');
	
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
	 * @return bool
	 * @throws KalturaAPIException
	 * @throws Exception
	 */
	public static function shouldUseOAuth2AuthenticationMethod()
	{
		$zoomConfiguration = self::getZoomConfiguration();
		return isset($zoomConfiguration['clientSecret']);
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
	
	/**
	 * load html page the that will ask the user for its KMC URL, derive the region of the user from it,
	 * and redirect to the registration page in the correct region, while forwarding the necessary code for registration
	 * @action oauthValidation
	 * @throws Exception
	 */
	public function oauthValidationAction()
	{
		$authCode = $_GET[self::AUTH_CODE];
		ZoomHelper::loadRegionalCloudRedirectionPage($authCode);
	}
	
	/**
	 *
	 * @action preOauthValidation
	 * @return string
	 * @throws Exception
	 */
	public function preOauthValidation()
	{
		KalturaResponseCacher::disableCache();
		$zoomConfiguration = self::getZoomConfiguration();
		$clientId = $zoomConfiguration['clientId'];
		$zoomBaseURL = $zoomConfiguration[kZoomClient::ZOOM_BASE_URL];
		$redirectUrl = $zoomConfiguration['redirectUrl'];
		if(!array_key_exists(self::AUTH_CODE, $_GET) || !$_GET[self::AUTH_CODE])
		{
			$url = $zoomBaseURL . '/oauth/authorize?' . 'response_type=code' . '&client_id=' . $clientId .  '&redirect_uri=' . $redirectUrl;
			VendorHelper::redirect($url);
		}
		else
		{
			try
			{
				$ks = isset($_GET[self::INTEGRATION_CODE]) ? ks::fromSecureString ($_GET[self::INTEGRATION_CODE]) : null;
			}
			catch (Exception $e)
			{
				throw new KalturaAPIException($e->getMessage());
			}
			$authCode = $_GET[self::AUTH_CODE];
			$tokens  = kZoomOauth::requestAuthorizationTokens($authCode);
			$accessToken = $tokens[kOAuth::ACCESS_TOKEN];
			$client = new kZoomClient($zoomBaseURL, null, null, null, null, $accessToken );
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
			else if ($zoomIntegration->getStatus() == VendorIntegrationStatus::ACTIVE && $zoomIntegration->getPartnerId() != $ks->getPartnerId())
			{
				throw new KalturaAPIException(KalturaZoomErrors::INTEGRATION_ALREADY_EXIST, $zoomIntegration->getPartnerId());
			}
			
			$zoomIntegration->setTokensData($tokens);
			$zoomIntegration->save();
			$permissions = $permissions['permissions'];
			$isAdmin = ZoomHelper::canConfigureEventSubscription($permissions);
			if($isAdmin)
			{
				if($ks)
				{
					$zoomIntegration->setPartnerId($ks->getPartnerId());
					$zoomIntegration->setVendorType(VendorTypeEnum::ZOOM_ACCOUNT);
					$zoomIntegration->save();
					$filePath = dirname(__FILE__) . self::REGISTRATION_PAGE_PATH;
					VendorHelper::loadSubmitPage($zoomIntegration->getPartnerId(), $accountId, $ks, $filePath);
				}
				else
				{
					ZoomHelper::loadLoginPage($tokens, $zoomConfiguration);
				}
			}
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
		
		$zoomIntegration->setStatus(VendorIntegrationStatus::DELETED);
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
		$client = new kZoomClient($zoomBaseURL,null,$tokens[kZoomTokens::REFRESH_TOKEN],null,null,$tokens[kZoomTokens::ACCESS_TOKEN]);
		$accountId = $this->getAccountId($client->retrieveTokenZoomUser());
		$zoomIntegration = ZoomHelper::getZoomIntegrationByAccountId($accountId, true);
		$partnerId = kCurrentContext::getCurrentPartnerId();
		if($zoomIntegration && intval($partnerId) !==  $zoomIntegration->getPartnerId() && $partnerId !== 0)
		{
			KalturaLog::info("Zoom changing account id: $accountId partner to $partnerId");
			$zoomIntegration->setPartnerId($partnerId);
			$zoomIntegration->setTokensData($tokens);
			$zoomIntegration->save();
		}
		
		$filePath = dirname(__FILE__) . self::REGISTRATION_PAGE_PATH;
		VendorHelper::loadSubmitPage($zoomIntegration->getPartnerId(), $accountId, $this->getKs(), $filePath);
	}
	
	/**
	 * @action localRegistrationPage
	 * @param string $jwt
	 * @throws KalturaAPIException
	 * @throws PropelException
	 * @throws Exception
	 */
	public function localRegistrationPageAction($jwt)
	{
		$isOAuth2Authentication = self::shouldUseOAuth2AuthenticationMethod();
		
		if ($isOAuth2Authentication)
		{
			throw new KalturaAPIException(KalturaZoomErrors::NOT_ALLOWED_ON_THIS_INSTANCE);
		}
		$zoomConfiguration = self::getZoomConfiguration();
		$zoomBaseURL = $zoomConfiguration[kZoomClient::ZOOM_BASE_URL];
		$client = new kZoomClient($zoomBaseURL,$jwt,null,null,null,null);
		$zoomAccountId = $this->getAccountId($client->retrieveTokenZoomUser());
		$zoomIntegration = ZoomHelper::getZoomIntegrationByAccountId($zoomAccountId);
		if(!$zoomIntegration)
		{
			$zoomIntegration = new ZoomVendorIntegration();
			$zoomIntegration->setAccountId($zoomAccountId);
			$zoomIntegration->setPartnerId(kCurrentContext::getCurrentPartnerId());
			$zoomIntegration->setVendorType(VendorTypeEnum::ZOOM_ACCOUNT);
		}
		$zoomIntegration->setJwtToken($jwt);
		$zoomIntegration->save();
		
		$filePath = dirname(__FILE__) . self::REGISTRATION_PAGE_PATH;
		VendorHelper::loadSubmitPage($zoomIntegration->getPartnerId(), $zoomAccountId, $this->getKs(), $filePath);
	}
	
	protected function getAccountId($jsonDataAsArray)
	{
		$accountId = $jsonDataAsArray[ZoomHelper::ACCOUNT_ID];
		KalturaLog::debug(print_r($accountId, true));
		return $accountId;
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
		$verificationToken = $zoomConfiguration[kOAuth::VERIFICATION_TOKEN];
		$tokensResponse = AESEncrypt::decrypt($verificationToken, $tokensData, $iv);
		$tokens = kOAuth::parseTokensResponse($tokensResponse);
		if (!kOAuth::validateTokens($tokens))
		{
			ZoomHelper::exitWithError(kZoomErrorMessages::TOKEN_PARSING_FAILED . print_r($tokensData, true));
		}
		$tokens = kOAuth::extractTokensFromData($tokens);
		$expiresIn = $tokens[kOAuth::EXPIRES_IN];
		$tokens[kOAuth::EXPIRES_IN] = kZoomOauth::getTokenExpiryAbsoluteTime($expiresIn);
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
		$zoomIntegration = ZoomHelper::getZoomIntegrationByAccountId($accountId, true);
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
		if ($integrationSetting->zoomCategory)
		{
			if (VendorHelper::createCategoryForVendorIntegration($zoomIntegration->getPartnerId(), $integrationSetting->zoomCategory, $zoomIntegration))
			{
				$zoomIntegration->setZoomCategory($integrationSetting->zoomCategory);
			}
		}
		else
		{
			$zoomIntegration->unsetCategory();
		}
		
		if ($integrationSetting->zoomWebinarCategory)
		{
			if (VendorHelper::createCategoryForVendorIntegration($zoomIntegration->getPartnerId(), $integrationSetting->zoomWebinarCategory, $zoomIntegration))
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
	 * @return KalturaEndpointValidationResponse|null
	 */
	public function recordingCompleteAction()
	{
		$eventResponse = null;
		KalturaResponseCacher::disableCache();
		myPartnerUtils::resetAllFilters();
		$kZoomEventHandler = new kZoomEventHanlder(self::getZoomConfiguration());
		$data = $kZoomEventHandler->getRequestData();

		if($data[kZoomEvent::EVENT] == kZoomEvent::ENDPOINT_URL_VALIDATION)
		{
			$eventResponse = $kZoomEventHandler->processUrlValidationEvent($data);
		}
		else
		{
			$event = $kZoomEventHandler->parseEvent($data);
			$zoomIntegration = ZoomHelper::getZoomIntegrationByAccountId($event->accountId);
			ZoomHelper::verifyZoomIntegration($zoomIntegration);
			$this->setPartnerFilters($zoomIntegration->getPartnerId());
			$kZoomEventHandler->processEvent($event);
		}

		return $eventResponse;
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
	
	/**
	 * List KalturaZoomIntegrationSetting objects
	 *
	 * @action list
	 * @param KalturaFilterPager $pager Pager
	 * @return KalturaZoomIntegrationSettingResponse
	 */
	public function listAction(KalturaFilterPager $pager = null)
	{
		if (!$pager)
		{
			$pager = new KalturaFilterPager();
		}
		
		$c = KalturaCriteria::create(VendorIntegrationPeer::OM_CLASS);
		$c->addAnd(VendorIntegrationPeer::VENDOR_TYPE,VendorTypeEnum::ZOOM_ACCOUNT);
		$c->addAnd(VendorIntegrationPeer::PARTNER_ID, kCurrentContext::getCurrentPartnerId());
		$totalCount = VendorIntegrationPeer::doCount($c);
		$pager->attachToCriteria($c);
		$list = VendorIntegrationPeer::doSelect($c);
		$newList = KalturaZoomIntegrationSettingArray::fromDbArray($list);
		$response = new KalturaZoomIntegrationSettingResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		return $response;
	}
	
}
