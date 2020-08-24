<?php

require_once 'oauth2Action.class.php';

/**
 * @package Core
 * @subpackage externalServices
 */
class facebookoauth2Action extends oauth2Action
{
	const FACEBOOK_DISTRIBUTION_ACCESS_URL = "/api_v3/index.php?service=contentdistribution_distributionprofile&distributionProfile%3AobjectType=KalturaFacebookDistributionProfile";

	private $authDataCache;

	private function getAuthDataCache()
	{
		if(!$this->authDataCache)
			$this->authDataCache = new kAuthDataCache();

		return $this->authDataCache;
	}


	public function execute()
	{
		set_include_path(get_include_path().PATH_SEPARATOR.KALTURA_ROOT_PATH.'/infra/general/');
		require_once 'FacebookGraphSdkUtils.php';
		require_once 'kDistributionPersistentDataHandler.php';

		$nextAction = base64_decode($this->getRequestParameter(FacebookConstants::FACEBOOK_NEXT_ACTION_REQUEST_PARAM));

		// understand the sub action based on our url parameters
		if ($nextAction == FacebookConstants::SUB_ACTION_REDIRECT_SCREEN)
		{
			$this->subAction = FacebookConstants::SUB_ACTION_REDIRECT_SCREEN;
			$this->executeRedirectScreen();
		}
		elseif ($nextAction == FacebookConstants::SUB_ACTION_PROCESS_OAUTH2_RESPONSE)
		{
			$this->subAction = FacebookConstants::SUB_ACTION_PROCESS_OAUTH2_RESPONSE;
			$this->executeProcessOAuth2Response();
		}
		else
		{
			$this->subAction = FacebookConstants::SUB_ACTION_LOGIN_SCREEN;
			$this->executeLoginScreen();
		}

		return sfView::SUCCESS;
	}

	/**
	 * display login form
	 */
	protected function executeLoginScreen()
	{
		$this->loginError = null;
		$this->partnerError = null;
		$this->serviceUrl = requestUtils::getHost();
		$params = $this->getForwardParameters();
		$params[FacebookConstants::FACEBOOK_NEXT_ACTION_REQUEST_PARAM] = base64_encode(FacebookConstants::SUB_ACTION_REDIRECT_SCREEN);
		$this->nextUrl = $this->getController()->genUrl('extservices/facebookoauth2?'.http_build_query($params, null, '&')).'?ks=';
	}

	/**
	 *  display a message to the user before redirecting him to facebook
	 */
	protected function executeRedirectScreen()
	{
		$appId = $this->getFromConfig(FacebookConstants::FACEBOOK_APP_ID_REQUEST_PARAM);
		$appSecret = $this->getFromConfig(FacebookConstants::FACEBOOK_APP_SECRET_REQUEST_PARAM);
		$permissions = explode(',',base64_decode($this->getRequestParameter(FacebookConstants::FACEBOOK_PERMISSIONS_REQUEST_PARAM)));
		$providerId = base64_decode($this->getRequestParameter(FacebookConstants::FACEBOOK_PROVIDER_ID_REQUEST_PARAM));
		$requestPartnerId = base64_decode($this->getRequestParameter(FacebookConstants::FACEBOOK_PARTNER_ID_REQUEST_PARAM));

		$ksStr = $this->getRequestParameter(FacebookConstants::FACEBOOK_KS_REQUEST_PARAM);
		$this->ksError = null;
		$this->partnerError = null;
		$ksValid = $this->processKs($ksStr);
		if (!$ksValid)
		{
			$this->ksError = true;
			return;
		}

		$ks = kCurrentContext::$ks_object;
		$contextPartnerId = $ks->partner_id;
		if ( empty($requestPartnerId) || $contextPartnerId != $requestPartnerId)
		{
			$this->partnerError = true;
			return;
		}

		$ks = $this->generateTimeLimitedKs($contextPartnerId);
		$params = $this->getForwardParameters();
		$params[FacebookConstants::FACEBOOK_KS_REQUEST_PARAM] = $ks;
		$accessURL = $this->getFacebookDistributionAccessURL($providerId, $ks);
		$dataHandler = new kDistributionPersistentDataHandler($accessURL);
		$redirectUrl = FacebookGraphSdkUtils::getKalturaRedirectUrl();
		$reRequestPermissions = base64_decode($this->getRequestParameter(FacebookConstants::FACEBOOK_RE_REQUEST_PERMISSIONS_REQUEST_PARAM));
		$fb = FacebookGraphSdkUtils::createFacebookInstance($appId, $appSecret, $dataHandler);
		$loginHelper = $fb->getRedirectLoginHelper();
		$this->oauth2Url = FacebookGraphSdkUtils::getLoginUrl($loginHelper, $redirectUrl, $permissions, $reRequestPermissions);
		$persistentDataHandler = $loginHelper->getPersistentDataHandler();
		$state = $persistentDataHandler->get(FacebookConstants::FACEBOOK_LOGIN_STATE);
		$authDataCache = $this->getAuthDataCache();
		$authDataCache->store($state, $params);
	}

	/**
	 * validate the response from facebook
	 */
	protected function executeProcessOAuth2Response()
	{
		$this->tokenError = null;
		$appId = $this->getFromConfig(FacebookConstants::FACEBOOK_APP_ID_REQUEST_PARAM);
		$appSecret = $this->getFromConfig(FacebookConstants::FACEBOOK_APP_SECRET_REQUEST_PARAM);
		$state = $this->getRequestParameter(FacebookConstants::FACEBOOK_LOGIN_STATE);
		$authDataCache = $this->getAuthDataCache();
		$data = $authDataCache->retrieve($state);
		if(!$data)
		{
			$msg = "Error while retrieving auth data for facebook with id ".$state;
			throw new Exception($msg);
		}

		$pageId = base64_decode($data[FacebookConstants::FACEBOOK_PAGE_ID_REQUEST_PARAM]);
		$providerId = base64_decode($data[FacebookConstants::FACEBOOK_PROVIDER_ID_REQUEST_PARAM]);
		$ks = $data[FacebookConstants::FACEBOOK_KS_REQUEST_PARAM];
		$permissions = explode(',',base64_decode($data[FacebookConstants::FACEBOOK_PERMISSIONS_REQUEST_PARAM]));

		try
		{
			$accessURL = $this->getFacebookDistributionAccessURL($providerId, $ks);
			$dataHandler = new kDistributionPersistentDataHandler($accessURL);

			$userAccessToken = FacebookGraphSdkUtils::getLongLivedUserAccessToken($appId, $appSecret, $dataHandler, $permissions);

			if($userAccessToken)
			{
				$pageAccessToken = FacebookGraphSdkUtils::getPageAccessToken($appId, $appSecret, $userAccessToken, $pageId, $dataHandler);
				if($pageAccessToken)
				{
					$dataHandler->set('userAccessToken', $userAccessToken);
					$dataHandler->set('pageAccessToken', $pageAccessToken);
				}
			}
		}
		catch(Exception $e)
		{
			$this->tokenError = true;
			$this->errorMessage = $e->getMessage();
		}
	}

	protected function getFromConfig($paramName, $default = null)
	{
		return kConf::get($paramName, 'facebook', $default);
	}


	protected function getForwardParameters()
	{
		$params = array(
			FacebookConstants::FACEBOOK_PERMISSIONS_REQUEST_PARAM =>
				$this->getRequestParameter(FacebookConstants::FACEBOOK_PERMISSIONS_REQUEST_PARAM),
			FacebookConstants::FACEBOOK_PAGE_ID_REQUEST_PARAM =>
				$this->getRequestParameter(FacebookConstants::FACEBOOK_PAGE_ID_REQUEST_PARAM),
			FacebookConstants::FACEBOOK_RE_REQUEST_PERMISSIONS_REQUEST_PARAM =>
				$this->getRequestParameter(FacebookConstants::FACEBOOK_RE_REQUEST_PERMISSIONS_REQUEST_PARAM),
			FacebookConstants::FACEBOOK_PROVIDER_ID_REQUEST_PARAM =>
				$this->getRequestParameter(FacebookConstants::FACEBOOK_PROVIDER_ID_REQUEST_PARAM),
			FacebookConstants::FACEBOOK_PARTNER_ID_REQUEST_PARAM =>
				$this->getRequestParameter(FacebookConstants::FACEBOOK_PARTNER_ID_REQUEST_PARAM),
			FacebookConstants::FACEBOOK_KS_REQUEST_PARAM =>
				$this->getRequestParameter(FacebookConstants::FACEBOOK_KS_REQUEST_PARAM)
		);

		return $params;
	}

	private function getFacebookDistributionAccessURL($providerId, $ks)
	{
		$host = requestUtils::getHost();
		return $host.self::FACEBOOK_DISTRIBUTION_ACCESS_URL."&id=".$providerId."&ks=".$ks;
	}
}