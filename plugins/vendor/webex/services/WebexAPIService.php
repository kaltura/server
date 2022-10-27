<?php
/**
 * @service webexAPI
 * @package plugins.WebexAPIDropFolder
 * @subpackage api.services
 */
class WebexAPIService extends KalturaBaseService
{
	const AUTH_CODE = 'code';
	const INTEGRATION_CODE = 'integrationCode';
	
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
	
	public static function getWebexConfiguration()
	{
		if (!kConf::hasMap(kConfMapNames::VENDOR))
		{
			throw new KalturaAPIException(KalturaWebexAPIErrors::NO_VENDOR_CONFIGURATION);
		}
		
		return kConf::get(WebexAPIDropFolderPlugin::CONFIGURATION_PARAM_NAME, kConfMapNames::VENDOR);
	}
	
	/**
	 * @action webex
	 * @return string
	 * @throws Exception
	 */
	public function webexAction()
	{
		// create webex account section on vendor conf map
		// remove consts
		// add dedicated function that gets the header data on class named kWebexOauth call the function getHeaderData
		// create kOauth interface class that declears getHeaderData
		// rename zoom getZoomHeaderData, add the class it is implemanting and change places that call it
		// add curlRetrieveTokensData to the interface and implemant for webex
		// move all generic funcion to the interface
		// save integration to db
		
		
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
			self::$webexIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartnerNoFilter($accountId, VendorTypeEnum::WEBEX_ACCOUNT);
		}
		else
		{
			self::$webexIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartner($accountId, VendorTypeEnum::WEBEX_ACCOUNT);
		}
		
		return self::$webexIntegration;
	}
	
	/**
	 * redirects to new URL
	 * @param $url
	 */
	private static function redirect($url)
	{
		$redirect  = new kRendererRedirect($url);
		$redirect->output();
		KExternalErrors::dieGracefully();
	}
	
	/**
	 * @param WebexAPIVendorIntegration $webexIntegration
	 * @param $accountId
	 * @param $ks
	 * @throws Exception
	 */
	public static function loadSubmitPage($webexIntegration, $accountId, $ks)
	{
		$file_path = dirname(__FILE__) . '/../lib/api/webPage/KalturaWebexAPIRegistrationPage.html';
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
		
		throw new KalturaAPIException('unable to find submit page, please contact support');
	}
	
	/**
	 * @param array $tokens
	 * @param array $webexConfiguration
	 * @throws Exception
	 */
	public static function loadLoginPage($tokens, $webexConfiguration)
	{
		$file_path = dirname(__FILE__) . '/../api/webPage/KalturaWebexAPILoginPage.html';
		if (file_exists($file_path))
		{
			$page = file_get_contents($file_path);
			$tokensString = json_encode($tokens);
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
		$webexConfiguration = self::getWebexConfiguration();
		$tokens = kOAuth::handleEncryptTokens($tokensData, $iv, $webexConfiguration);
		$webexBaseURL = $webexConfiguration['baseUrl'];
		//$client = new kZoomClient($webexBaseURL,null, $tokens[kOAuth::REFRESH_TOKEN],null,null,$tokens[kOAuth::ACCESS_TOKEN]);
		//$accountId = $this->getAccountId($client->retrieveTokenZoomUser());
		//$zoomIntegration = ZoomHelper::getZoomIntegrationByAccountId($accountId, true);
//		$partnerId = kCurrentContext::getCurrentPartnerId();
//		if($zoomIntegration && intval($partnerId) !==  $zoomIntegration->getPartnerId() && $partnerId !== 0)
//		{
//			KalturaLog::info("Zoom changing account id: $accountId partner to $partnerId");
//			$zoomIntegration->setPartnerId($partnerId);
//			$zoomIntegration->setTokensData($tokens);
//			$zoomIntegration->save();
//		}
//
//		ZoomHelper::loadSubmitPage($zoomIntegration, $accountId, $this->getKs());
	}
	
	/**
	 * @action preOauthValidation
	 * @return string
	 * @throws Exception
	 */
	public function preOauthValidation()
	{
		KalturaResponseCacher::disableCache();
		$webexConfiguration = self::getWebexConfiguration();
		$clientId = $webexConfiguration['clientId'];
		$webexBaseURL = $webexConfiguration['baseUrl'];
		$redirectUrl = $webexConfiguration['redirectUrl'];
		$redirectUri = urlencode($redirectUrl);
		$scope = $webexConfiguration['scope'];
		$state = $webexConfiguration['state']; // Must have?
		
		if (!array_key_exists(self::AUTH_CODE, $_GET) || !$_GET[self::AUTH_CODE])
		{
			$url = "$webexBaseURL/authorize?response_type=code&client_id=$clientId&redirect_uri=$redirectUri&scope=$scope&state=$state";
			self::redirect($url);
		}
		else
		{
			try
			{
				$ks = isset($_GET[self::INTEGRATION_CODE]) ? ks::fromSecureString($_GET[self::INTEGRATION_CODE]) : null;
			}
			catch (Exception $e)
			{
				throw new KalturaAPIException($e->getMessage());
			}
			
			$authCode = $_GET[self::AUTH_CODE];
			$tokens = kWebexAPIOauth::requestAccessToken($authCode);
			$accessToken = $tokens[kOauth::ACCESS_TOKEN];
			$client = new kWebexAPIClient($webexBaseURL, null, null, null, null, $accessToken );
			//$user = $client->retrieveTokenZoomxUser();
			$accountId = 0; //$user[ZoomHelper::ACCOUNT_ID];
			$webexIntegration = self::getWebexAPIIntegrationByAccountId($accountId, true);
			if (!$webexIntegration)
			{
				$webexIntegration = new WebexAPIVendorIntegration();
			}
			else if ($webexIntegration->getStatus() == VendorIntegrationStatus::ACTIVE && $webexIntegration->getPartnerId() != $ks->getPartnerId())
			{
				throw new KalturaAPIException(KalturaWebexAPIErrors::INTEGRATION_ALREADY_EXIST, $webexIntegration->getPartnerId());
			}
			
			$webexIntegration->setTokensData($tokens);
			$webexIntegration->save();
			
			if ($ks)
			{
				$webexIntegration->setPartnerId($ks->getPartnerId());
				$webexIntegration->setVendorType(VendorTypeEnum::WEBEX_ACCOUNT);
				$webexIntegration->save();
				self::loadSubmitPage($webexIntegration, $accountId, $ks);
			}
			else
			{
				self::loadLoginPage($tokens, $webexIntegration);
			}
		}
		
		throw new KalturaAPIException(KalturaWebexAPIErrors::WEBEX_ADMIN_REQUIRED);
	}
	
	/**
	 * @action oauthValidation
	 * @throws Exception
	 */
	public function oauthValidationAction()
	{
		$authCode = $_GET[self::AUTH_CODE];
		self::loadRegionalCloudRedirectionPage($authCode);
	}
	
	/**
	 * @param $authCode
	 * @throws Exception
	 */
	public static function loadRegionalCloudRedirectionPage($authCode)
	{
		$file_path = dirname(__FILE__) . '/../lib/api/webPage/KalturaRegionalRedirectPage.html';
		if (file_exists($file_path))
		{
			$page = file_get_contents($file_path);
			$page = str_replace('@authCode@', $authCode, $page);
			
			echo $page;
			die();
		}
		
		throw new KalturaAPIException('unable to find regional redirect page, please contact support');
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
	 * @action list
	 * @return string
	 * @throws Exception
	 */
	public function listAction()
	{
		$webexConfiguration = self::getWebexConfiguration();
		$webexBaseURL = $webexConfiguration['baseUrl'];
		
		$hostEmail = $webexConfiguration['hostEmail']; //todo
		
		$url = $webexBaseURL . 'recordings?hostEmail=' . $hostEmail;
		
		$accessToken = '';
		$authorizationHeader = 'Authorization: Bearer ' . $accessToken;
		$requestHeaders = array($authorizationHeader);
		$curlWrapper = new KCurlWrapper();
		$curlWrapper->setOpt(CURLOPT_POST, 1);
		$curlWrapper->setOpt(CURLOPT_HEADER, true);
		$curlWrapper->setOpt(CURLOPT_HTTPHEADER, $requestHeaders);
		$response = $curlWrapper->exec($url);
		
		$dataAsArray = json_decode($response, true);
		KalturaLog::debug(print_r($dataAsArray, true));
		
		return print_r($dataAsArray, true);
	}
}
