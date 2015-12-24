<?php

require_once(KALTURA_ROOT_PATH.'/plugins/content_distribution/providers/facebook/lib/KalturaFacebookPersistentDataHandler.php');
require_once(KALTURA_ROOT_PATH.'/plugins/content_distribution/providers/facebook/lib/model/FacebookRequestParameters.php');


/**
 * @package Core
 * @subpackage externalServices
 */
class facebookoauth2Action extends sfAction
{
	const SUB_ACTION_REDIRECT_SCREEN = 'redirect-screen';
	const SUB_ACTION_PROCESS_OAUTH2_RESPONSE = 'process-oauth2-response';
	const SUB_ACTION_LOGIN_SCREEN = 'login-screen';



	public function execute()
	{
		set_include_path(get_include_path().PATH_SEPARATOR.KALTURA_ROOT_PATH.'/infra/general/');
		require_once 'FacebookGraphSdkUtils.php';

		$nextAction = base64_decode($this->getRequestParameter(FacebookRequestParameters::FACEBOOK_NEXT_ACTION_REQUEST_PARAM));

		// understand the sub action based on our url parameters
		if ($nextAction == self::SUB_ACTION_REDIRECT_SCREEN)
		{
			$this->subAction = self::SUB_ACTION_REDIRECT_SCREEN;
			$this->executeRedirectScreen();
		}
		elseif ($nextAction == self::SUB_ACTION_PROCESS_OAUTH2_RESPONSE)
		{
			$this->subAction = self::SUB_ACTION_PROCESS_OAUTH2_RESPONSE;
			$this->executeProcessOAuth2Response();
		}
		else
		{
			$this->subAction = self::SUB_ACTION_LOGIN_SCREEN;
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
		$this->serviceUrl = requestUtils::getHost();
		$params = $this->getForwardParameters();
		$params[FacebookRequestParameters::FACEBOOK_NEXT_ACTION_REQUEST_PARAM] = base64_encode(self::SUB_ACTION_REDIRECT_SCREEN);
		$this->nextUrl = $this->getController()->genUrl('extservices/facebookoauth2?'.http_build_query($params, null, '&')).'?ks=';
	}

	/**
	 *  display a message to the user before redirecting him to facebook
	 */
	protected function executeRedirectScreen()
	{
		$appId = $this->getFromConfig(FacebookRequestParameters::FACEBOOK_APP_ID_REQUEST_PARAM);
		$appSecret = $this->getFromConfig(FacebookRequestParameters::FACEBOOK_APP_SECRET_REQUEST_PARAM);
		$permissions = explode(',',base64_decode($this->getRequestParameter(FacebookRequestParameters::FACEBOOK_PERMISSIONS_REQUEST_PARAM)));
		$providerId = base64_decode($this->getRequestParameter(FacebookRequestParameters::FACEBOOK_PROVIDER_ID_REQUEST_PARAM));
		$this->ksError = null;
		$ksValid = $this->processKs($this->getRequestParameter('ks'));
		if (!$ksValid)
		{
			$this->ksError = true;
			return;
		}

		$params = $this->getForwardParameters();
		$params[FacebookRequestParameters::FACEBOOK_NEXT_ACTION_REQUEST_PARAM] = base64_encode(self::SUB_ACTION_PROCESS_OAUTH2_RESPONSE);
		$provider = DistributionProfilePeer::retrieveByPK($providerId);
		$dataHandler = new KalturaFacebookPersistentDataHandler($provider);
		$redirectUrl = $this->getController()->genUrl('extservices/facebookoauth2?'.http_build_query($params, null, '&'), true);
		$reRequestPermissions = base64_decode($this->getRequestParameter(FacebookRequestParameters::FACEBOOK_RE_REQUEST_PERMISSIONS_REQUEST_PARAM));
		$this->oauth2Url = FacebookGraphSdkUtils::getLoginUrl($appId, $appSecret, $redirectUrl, $permissions, $dataHandler, $reRequestPermissions);
	}

	/**
	 * validate the response from facebook
	 */
	protected function executeProcessOAuth2Response()
	{
		$this->tokenError = null;
		$appId = $this->getFromConfig(FacebookRequestParameters::FACEBOOK_APP_ID_REQUEST_PARAM);
		$appSecret = $this->getFromConfig(FacebookRequestParameters::FACEBOOK_APP_SECRET_REQUEST_PARAM);
		$pageId = base64_decode($this->getRequestParameter(FacebookRequestParameters::FACEBOOK_PAGE_ID_REQUEST_PARAM));
		$providerId = base64_decode($this->getRequestParameter(FacebookRequestParameters::FACEBOOK_PROVIDER_ID_REQUEST_PARAM));
		$permissions = explode(',',base64_decode($this->getRequestParameter(FacebookRequestParameters::FACEBOOK_PERMISSIONS_REQUEST_PARAM)));

		try
		{
			/**
			 * @var FacebookDistributionProfile facebookProfile
			 */
			$facebookProfile = DistributionProfilePeer::retrieveByPK($providerId);
			$dataHandler = new KalturaFacebookPersistentDataHandler($facebookProfile);

			$userAccessToken = FacebookGraphSdkUtils::getLongLivedUserAccessToken($appId, $appSecret, $dataHandler, $permissions);

			if($userAccessToken)
			{
				$pageAccessToken = FacebookGraphSdkUtils::getPageAccessToken($appId, $appSecret, $userAccessToken, $pageId, $dataHandler);
				if($pageAccessToken)
				{
					$facebookProfile->setReRequestPermissions(true);
					$facebookProfile->setPageAccessToken($pageAccessToken);
					$facebookProfile->setUserAccessToken($userAccessToken);
					$facebookProfile->save();
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

	protected function processKs($ksStr, $requiredPermission = null)
	{
		try
		{
			kCurrentContext::initKsPartnerUser($ksStr);
		}
		catch(Exception $ex)
		{
			KalturaLog::err($ex);
			return false;
		}

		if (kCurrentContext::$ks_object->type != ks::SESSION_TYPE_ADMIN)
		{
			KalturaLog::err('Ks is not admin');
			return false;
		}

		try
		{
			kPermissionManager::init(kConf::get('enable_cache'));
		}
		catch(Exception $ex)
		{
			if (strpos($ex->getCode(), 'INVALID_ACTIONS_LIMIT') === false) // allow using limited ks
			{
				KalturaLog::err($ex);
				return false;
			}
		}
		if ($requiredPermission)
		{
			if (!kPermissionManager::isPermitted(PermissionName::ADMIN_PUBLISHER_MANAGE))
			{
				KalturaLog::err('Ks is missing "ADMIN_PUBLISHER_MANAGE" permission');
				return false;
			}
		}

		return true;
	}

	protected function getForwardParameters()
	{
		$params = array(
			FacebookRequestParameters::FACEBOOK_PERMISSIONS_REQUEST_PARAM =>
				$this->getRequestParameter(FacebookRequestParameters::FACEBOOK_PERMISSIONS_REQUEST_PARAM),
			FacebookRequestParameters::FACEBOOK_PAGE_ID_REQUEST_PARAM =>
				$this->getRequestParameter(FacebookRequestParameters::FACEBOOK_PAGE_ID_REQUEST_PARAM),
			FacebookRequestParameters::FACEBOOK_RE_REQUEST_PERMISSIONS_REQUEST_PARAM =>
				$this->getRequestParameter(FacebookRequestParameters::FACEBOOK_RE_REQUEST_PERMISSIONS_REQUEST_PARAM),
			FacebookRequestParameters::FACEBOOK_PROVIDER_ID_REQUEST_PARAM =>
				$this->getRequestParameter(FacebookRequestParameters::FACEBOOK_PROVIDER_ID_REQUEST_PARAM)
		);

		return $params;
	}

}