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
	 * @action preOauthValidation
	 * @throws Exception
	 */
	public function preOauthValidation()
	{
		$authCode = $_GET['code'];
		ZoomHelper::loadRegionalCloudRedirectionPage($authCode);
	}
	
	/**
	 *
	 * @action oauthValidation
	 * @return int
	 * @throws Exception
	 */
	public function oauthValidationAction()
	{
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
			
			$zoomIntegration->setTokensData($tokens);
			$zoomIntegration->save();
			$permissions = $permissions['permissions'];
			$isAdmin = ZoomHelper::canConfigureEventSubscription($permissions);
		}
		
		if($isAdmin)
		{
			return $zoomIntegration->getId();
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
	 * @param int $vendorIntegrationId
	 * @throws KalturaAPIException
	 * @throws PropelException
	 * @throws Exception
	 */
	public function fetchRegistrationPageAction($vendorIntegrationId)
	{
		KalturaResponseCacher::disableCache();
		$zoomIntegration = VendorIntegrationPeer::retrieveByPK($vendorIntegrationId);
		$accountId = $zoomIntegration->getAccountId();
		$partnerId = kCurrentContext::getCurrentPartnerId();
		if($zoomIntegration && intval($partnerId) !==  $zoomIntegration->getPartnerId() && $partnerId !== 0)
		{
			KalturaLog::info("Zoom changing account id: $accountId partner to $partnerId");
			$zoomIntegration->setPartnerId($partnerId);
			$zoomIntegration->save();
		}
		
		ZoomHelper::loadSubmitPage($zoomIntegration, $accountId, $this->getKs());
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
		
		ZoomHelper::loadSubmitPage($zoomIntegration, $zoomAccountId, $this->getKs());
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
