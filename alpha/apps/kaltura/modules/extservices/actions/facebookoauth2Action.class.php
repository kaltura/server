<?php
/**
 * @package Core
 * @subpackage externalServices
 */
class facebookoauth2Action extends sfAction
{
	const SUB_ACTION_REDIRECT_SCREEN = 'redirect-screen';
	const SUB_ACTION_PROCESS_OAUTH2_RESPONSE = 'process-oauth2-response';
	const SUB_ACTION_LOGIN_SCREEN = 'login-screen';
	
	const APP_ID_PARAM = 'app_id';
	const APP_SECRET_PARAM = 'app_secret';

	public function execute()
	{
		set_include_path(get_include_path().PATH_SEPARATOR.KALTURA_ROOT_PATH.'/infra/general/');
		require_once 'FacebookGraphSdkUtils.php';
		
		$nextAction = $this->getRequestParameter('next_action');

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
		$params['next_action'] = self::SUB_ACTION_REDIRECT_SCREEN;
		$this->nextUrl = $this->getController()->genUrl('extservices/facebookoauth2?'.http_build_query($params, null, '&')).'?ks=';
	}

	/**
	 *  display a message to the user before redirecting him to facebook
	 */
	protected function executeRedirectScreen()
	{
		$appId = $this->getFromConfig(self::APP_ID_PARAM);
		$appSecret = $this->getFromConfig(self::APP_SECRET_PARAM);
		$permissions = explode(',',$this->getRequestParameter('permissions'));

		$this->ksError = null;
		$ksValid = $this->processKs($this->getRequestParameter('ks'));
		if (!$ksValid)
		{
			$this->ksError = true;
			return;
		}

		$params = $this->getForwardParameters();
		$params['next_action'] = self::SUB_ACTION_PROCESS_OAUTH2_RESPONSE;
		
		$redirectUrl = $this->getController()->genUrl('extservices/facebookoauth2?'.http_build_query($params, null, '&'), true);
		$this->oauth2Url = FacebookGraphSdkUtils::getLoginUrl($appId, $appSecret, $redirectUrl, $permissions, $this->getRequestParameter('reRequestPermissions'));
	}

	/**
	 * validate the response from google
	 */
	protected function executeProcessOAuth2Response()
	{
		$this->tokenError = null;

		$appId = $this->getFromConfig(self::APP_ID_PARAM);
		$appSecret = $this->getFromConfig(self::APP_SECRET_PARAM);
		$pageId = $this->getRequestParameter('pageId');
		$permissions = explode(',',$this->getRequestParameter('permissions'));
		
		try 
		{
			$userAccessToken = FacebookGraphSdkUtils::getLongLivedUserAccessToken($appId, $appSecret, $permissions);
			if($userAccessToken)
			{
				$pageAccessToken = FacebookGraphSdkUtils::getPageAccessToken($appId, $appSecret, $userAccessToken, $pageId);
				if($pageAccessToken)
				{
					KalturaLog::debug('Page access token: '.$pageAccessToken);	
					$this->doUpdateCallback($userAccessToken, $pageAccessToken);			
				}
			}			
		}
		catch(Exception $e)
		{
			$this->tokenError = true;
			$this->errorMessage = $e->getMessage();
			$this->doUpdateCallback(null, null, true);
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
			'permissions' => $this->getRequestParameter('permissions'),
			'pageId' => $this->getRequestParameter('pageId'),
			'reRequestPermissions' => $this->getRequestParameter('reRequestPermissions'),
			'callbackUrl' => $this->getRequestParameter('callbackUrl'),
		);
		
		return $params;
	}
	
	protected function doUpdateCallback($userAccessToken, $pageAccessToken, $reRequestPermissions = null)
	{
		$callbackUrl = $this->getRequestParameter('callbackUrl');
		if($callbackUrl)
		{
			$params = array(
				'pageAccessToken' => $pageAccessToken,
				'userAccessToken' => $userAccessToken,
				'reRequestPermissions' => $reRequestPermissions,
			);
			$callbackUrl = $callbackUrl.'?'.http_build_query($params, null, '&');
			$ch = curl_init();		
			curl_setopt($ch, CURLOPT_URL, $callbackUrl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			$response = curl_exec($ch);	
				
			curl_close($ch);				
		}
	}
}