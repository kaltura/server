<?php
/**
 * @service webexAPI
 * @package plugins.WebexAPIDropFolder
 * @subpackage api.services
 */
class WebexAPIService extends KalturaBaseService
{
	const AUTH_CODE = 'code';
	const STATE = 'state';
	const INTEGRATION_CODE = 'integrationCode';
	const REGISTRATION_PAGE_PATH = '/../lib/api/webPage/KalturaWebexAPIRegistrationPage.html';
	const LOGIN_PAGE_PATH = '/../lib/api/webPage/KalturaWebexAPILoginPage.html';
	const REGIONAL_REDIRECT_PAGE_PATH = '/../lib/api/webPage/KalturaWebexAPIRegionalRedirectPage.html';
	
	protected static $PARTNER_NOT_REQUIRED_ACTIONS = array();
	
	protected static $webexIntegration;
	
	/**
	 * no partner will be provided by vendors as this called externally and not from kaltura
	 * @param string $actionName
	 * @return bool
	 */
	protected function partnerRequired($actionName)
	{
		return in_array($actionName, self::$PARTNER_NOT_REQUIRED_ACTIONS);
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
	 * @param $accountId
	 * @param bool $includeDeleted
	 * @return null|WebexAPIVendorIntegration
	 * @throws PropelException
	 */
	protected static function getWebexAPIIntegrationByAccountId($accountId, $includeDeleted = false)
	{
		if ($includeDeleted)
		{
			self::$webexIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartnerNoFilter($accountId, VendorTypeEnum::WEBEX_API_ACCOUNT);
		}
		else
		{
			self::$webexIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartner($accountId, VendorTypeEnum::WEBEX_API_ACCOUNT);
		}
		
		return self::$webexIntegration;
	}
	
	/**
	 * @param WebexAPIVendorIntegration $webexIntegration
	 * @param $accountId
	 * @param $ks
	 * @throws Exception
	 */
	protected static function loadSubmitPage(WebexAPIVendorIntegration $webexIntegration, $accountId, $ks)
	{
		$file_path = dirname(__FILE__) . self::REGISTRATION_PAGE_PATH;
		if (file_exists($file_path))
		{
			$page = file_get_contents($file_path);
			$page = str_replace('@ks@', $ks->getOriginalString(), $page);
			$page = str_replace('@BaseServiceUrl@', requestUtils::getHost(), $page);
			$page = str_replace( '@partnerId@',$webexIntegration->getPartnerId(),$page);
			$page = str_replace('@accountId@', $accountId , $page);
			
			echo $page;
			die();
		}
		
		throw new KalturaAPIException(KalturaWebexAPIErrors::SUBMIT_PAGE_NOT_FOUND);
	}
	
	/**
	 * @param array $tokens
	 * @param array $webexConfiguration
	 * @throws Exception
	 */
	protected static function loadLoginPage($tokens, $webexConfiguration)
	{
		$file_path = dirname(__FILE__) . self::LOGIN_PAGE_PATH;
		if (file_exists($file_path))
		{
			$page = file_get_contents($file_path);
			$tokensString = json_encode($tokens); // Might be already decoded
			//$verificationToken = $webexConfiguration[kZoomOauth::VERIFICATION_TOKEN];
			//list($enc, $iv) = AESEncrypt::encrypt($verificationToken, $tokensString);
			$page = str_replace('@BaseServiceUrl@', requestUtils::getHost(), $page);
			//$page = str_replace('@encryptData@', base64_encode($enc), $page);
			//$page = str_replace('@iv@', base64_encode($iv), $page);
			echo $page;
			die();
		}
	}
	
	/**
	 * @action preOauthValidation
	 * @return string
	 * @throws Exception
	 */
	public function preOauthValidationAction()
	{
		KalturaResponseCacher::disableCache();
		$webexConfiguration = WebexAPIDropFolderPlugin::getWebexConfiguration();
		
		if (!array_key_exists(self::AUTH_CODE, $_GET) || !$_GET[self::AUTH_CODE])
		{
			self::requestAuthCode($webexConfiguration);
		}
		else
		{
			$ks = self::getKSFromIntegrationCode();
			$tokens = kWebexAPIOauth::requestAuthorizationTokens($_GET[self::AUTH_CODE]);
			$webexIntegration = self::createNewWebexAPIVendorIntegration($ks, $webexConfiguration['baseUrl'], $tokens);
			self::redirectAfterNewVendorIntegration($ks, $webexIntegration, $tokens);
		}
		
		throw new KalturaAPIException(KalturaWebexAPIErrors::WEBEX_ADMIN_REQUIRED); // Is there any use case for this?
	}
	
	protected static function requestAuthCode($webexConfiguration)
	{
		$clientId = $webexConfiguration['clientId'];
		$webexBaseURL = $webexConfiguration['baseUrl'];
		$redirectUrl = $webexConfiguration['redirectUrl'];
		$redirectUri = urlencode($redirectUrl);
		$scope = $webexConfiguration['scope'];
		$state = $webexConfiguration['state'];
		
		$url = "$webexBaseURL/authorize?response_type=code&client_id=$clientId&redirect_uri=$redirectUri&scope=$scope&state=$state";
		self::redirect($url);
	}
	
	/**
	 * Redirects to new URL
	 * @param $url
	 */
	protected static function redirect($url)
	{
		$redirect  = new kRendererRedirect($url);
		$redirect->output();
		KExternalErrors::dieGracefully();
	}
	
	protected static function getKSFromIntegrationCode()
	{
		try
		{
			return isset($_GET[self::INTEGRATION_CODE]) ? ks::fromSecureString($_GET[self::INTEGRATION_CODE]) : null;
		}
		catch (Exception $e)
		{
			throw new KalturaAPIException($e->getMessage());
		}
	}
	
	protected static function createNewWebexAPIVendorIntegration($ks, $webexBaseURL, $tokens)
	{
		$accessToken = $tokens[kOauth::ACCESS_TOKEN];
		$client = new kWebexAPIClient($webexBaseURL, null, null, null, $accessToken);
		$user = $client->retrieveWebexUser();
		$accountId = $user['account_id'];
		$webexIntegration = self::getWebexAPIIntegrationByAccountId($accountId, true);
		if (!$webexIntegration)
		{
			$webexIntegration = new WebexAPIVendorIntegration();
			$webexIntegration->setAccountId($accountId);
		}
		else if ($webexIntegration->getStatus() == VendorIntegrationStatus::ACTIVE && $webexIntegration->getPartnerId() != $ks->getPartnerId())
		{
			throw new KalturaAPIException(KalturaWebexAPIErrors::INTEGRATION_ALREADY_EXIST, $webexIntegration->getPartnerId());
		}
		
		$webexIntegration->saveTokensData($tokens);
		
		return $webexIntegration;
	}
	
	protected static function redirectAfterNewVendorIntegration($ks, $webexIntegration, $tokens)
	{
		if ($ks)
		{
			$webexIntegration->setPartnerId($ks->getPartnerId());
			$webexIntegration->setVendorType(VendorTypeEnum::WEBEX_API_ACCOUNT);
			$webexIntegration->save();
			self::loadSubmitPage($webexIntegration, $webexIntegration->getAccountId(), $ks);
		}
		else
		{
			self::loadLoginPage($tokens, $webexIntegration);
		}
	}
	
	/**
	 * @action oauthValidation
	 * @throws Exception
	 */
	public function oauthValidationAction()
	{
		$webexConfiguration = WebexAPIDropFolderPlugin::getWebexConfiguration();
		if ($webexConfiguration['state'] != $_GET[self::STATE])
		{
			throw new KalturaAPIException(KalturaWebexAPIErrors::INCORRECT_OAUTH_STATE);
		}
		$authCode = $_GET[self::AUTH_CODE];
		$domain = $webexConfiguration['domain'];
		self::loadRegionalCloudRedirectionPage($authCode, $domain);
	}
	
	/**
	 * @param $authCode
	 * @throws Exception
	 */
	protected static function loadRegionalCloudRedirectionPage($authCode, $domain)
	{
		$file_path = dirname(__FILE__) . self::REGIONAL_REDIRECT_PAGE_PATH;
		if (file_exists($file_path))
		{
			$page = file_get_contents($file_path);
			$page = str_replace('@authCode@', $authCode, $page);
			$page = str_replace('@domain@', $domain, $page);
			
			echo $page;
			die();
		}
		
		throw new KalturaAPIException(KalturaWebexAPIErrors::REGIONAL_REDIRECT_PAGE_NOT_FOUND);
	}
	
	/**
	 * List KalturaWebexAPIIntegrationSetting objects
	 *
	 * @action list
	 * @param KalturaFilterPager $pager Pager
	 * @return KalturaWebexAPIIntegrationSettingResponse
	 */
	public function listAction(KalturaFilterPager $pager = null)
	{
		if (!$pager)
		{
			$pager = new KalturaFilterPager();
		}
		
		$c = KalturaCriteria::create(VendorIntegrationPeer::OM_CLASS);
		$c->addAnd(VendorIntegrationPeer::VENDOR_TYPE,VendorTypeEnum::WEBEX_API_ACCOUNT);
		$c->addAnd(VendorIntegrationPeer::PARTNER_ID, kCurrentContext::getCurrentPartnerId());
		$totalCount = VendorIntegrationPeer::doCount($c);
		$pager->attachToCriteria($c);
		$list = VendorIntegrationPeer::doSelect($c);
		$newList = KalturaWebexAPIIntegrationSettingArray::fromDbArray($list);
		$response = new KalturaWebexAPIIntegrationSettingResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		return $response;
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
		$webexConfiguration = WebexAPIDropFolderPlugin::getWebexConfiguration();
		$tokens = kOAuth::handleEncryptTokens($tokensData, $iv, $webexConfiguration);
		$webexBaseURL = $webexConfiguration['baseUrl'];
		$client = new kWebexAPIClient($webexBaseURL, $tokens[kOAuth::REFRESH_TOKEN],null, null, $tokens[kOAuth::ACCESS_TOKEN]);
		$user = $client->retrieveWebexUser();
		$accountId = $user['account_id'];
		$webexIntegration = self::getWebexAPIIntegrationByAccountId($accountId, true);
		$partnerId = kCurrentContext::getCurrentPartnerId();
		if ($webexIntegration && $partnerId !==  $webexIntegration->getPartnerId() && $partnerId !== 0)
		{
			KalturaLog::info("Webex changing account id: $accountId partner to $partnerId");
			$webexIntegration->setPartnerId($partnerId);
			$webexIntegration->setTokensData($tokens);
			$webexIntegration->save();
		}
		
		self::loadSubmitPage($webexIntegration, $accountId, $this->getKs());
	}
	
	/**
	 * @action submitRegistration
	 * @param string $accountId
	 * @param KalturaWebexAPIIntegrationSetting $integrationSetting
	 * @return string
	 * @throws KalturaAPIException
	 * @throws PropelException
	 */
	public function submitRegistrationAction($accountId, $integrationSetting)
	{
		KalturaResponseCacher::disableCache();
		/** @var WebexAPIVendorIntegration $webexIntegration */
		$webexIntegration = self::getWebexAPIIntegrationByAccountId($accountId, true);
		$partnerId = kCurrentContext::getCurrentPartnerId();
		if (!$webexIntegration || $webexIntegration->getPartnerId() != $partnerId)
		{
			throw new KalturaAPIException(KalturaWebexAPIErrors::NO_INTEGRATION_DATA);
		}
		
		kuserPeer::createKuserForPartner($partnerId, $integrationSetting->defaultUserId);
		//$this->configureZoomCategories($integrationSetting, $webexIntegration); // Create category for Webex
		
		$integrationSetting->toInsertableObject($webexIntegration);
		$webexIntegration->save();
		
		return true;
	}
}
