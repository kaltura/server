<?php
require_once 'oauth2Action.class.php';

/**
 * @package Core
 * @subpackage externalServices
 */
class googleoauth2Action extends oauth2Action
{
	const SUB_ACTION_REDIRECT_SCREEN = 'redirect-screen';
	const SUB_ACTION_PROCESS_OAUTH2_RESPONSE = 'process-oauth2-response';
	const SUB_ACTION_STATUS = 'success';
	const SUB_ACTION_LOGIN_SCREEN = 'login-screen';

	/**
	 */
	public function execute()
	{
		// add google client library to include path
		set_include_path(get_include_path().PATH_SEPARATOR.KALTURA_ROOT_PATH.'/vendor/google-api-php-client-1.1.2/src/Google');
		require_once 'autoload.php';
		require_once 'Client.php';

		$ks    = $this->getRequestParameter('ks');
		$state = $this->getRequestParameter('state');
		$status = $this->getRequestParameter('status');

		// understand the sub action based on our url parameters
		if ($status)
		{
			$this->subAction = self::SUB_ACTION_STATUS;
			$this->executeStatus();
		}
		elseif ($ks)
		{
			$this->subAction = self::SUB_ACTION_REDIRECT_SCREEN;
			$this->executeRedirectScreen();
		}
		elseif ($state)
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
		/**
		 * the google api project that we are granting access to
		 */
		$appId = $this->getRequestParameter('ytid');

		/**
		 * sub id is needed when we want to authorize different google accounts for several distribution profiles
		 * the distribution profile can then look for its specific access token by the 'subid'
		 */
		$subId = $this->getRequestParameter('subid');

		$appConfig = $this->getFromGoogleAuthConfig($appId);
		$this->invalidConfig = null;
		$this->loginError = null;
		$this->serviceUrl = requestUtils::getHost();
		$params = array(
			'ytid' => $appId,
			'subid' => $subId,
		);
		$this->nextUrl = $this->getController()->genUrl('extservices/googleoauth2?'.http_build_query($params, null, '&')).'?ks=';
		if ($appConfig === null)
		{
			$this->invalidConfig = true;
		}
	}

	/**
	 *  display a message to the user before redirecting him to google
	 */
	protected function executeRedirectScreen()
	{
		$appId = $this->getRequestParameter('ytid');
		$subId = $this->getRequestParameter('subid');
		$ksStr = $this->getRequestParameter('ks');

		$appConfig = $this->getFromGoogleAuthConfig($appId);
		$client = $this->getGoogleClient();
		$this->ksError = null;
		$ksValid = $this->processKs($ksStr);
		if (!$ksValid)
		{
			$this->ksError = true;
			return;
		}

		/** @var ks $ks */
		$ks = kCurrentContext::$ks_object;
		$partnerId = $ks->partner_id;

		$state = array(
			'ytid' => $appId,
			'subid' => $subId,
		);

 		// let's create a limited ks and pass it as a state parameter to google
		$limitedKs = $this->generateTimeLimitedKsWithData($partnerId, $state);

		$state = $limitedKs;
		$redirect = $this->getController()->genUrl('extservices/googleoauth2', true);
		$client->setRedirectUri($redirect);

		$client->setState($state);
		$client->setScopes($appConfig['scopes']);
		$client->setApprovalPrompt('force');
		$client->setAccessType('offline');
		$this->oauth2Url = $client->createAuthUrl();
	}

	/**
	 * validate the response from google
	 */
	protected function executeProcessOAuth2Response()
	{
		$this->ksError = null;
		$this->tokenError = null;

		$limitedKsStr = $this->getRequestParameter('state');
		$ksValid = $this->processKs($limitedKsStr);
		if (!$ksValid)
		{
			$this->ksError = true;
			return;
		}
		$limitedKs = kCurrentContext::$ks_object;
		$additionalData = $limitedKs->additional_data;
		$stateObject = json_decode($additionalData);
		if (!$stateObject)
		{
			$this->ksError = true;
			return;
		}
		$appId = isset($stateObject->ytid) ? $stateObject->ytid : null;
		$subId = isset($stateObject->subid) ? $stateObject->subid : null;
		$partner = $this->getPartner($limitedKs->partner_id);
		$client = $this->getGoogleClient($appId);
		$redirect = $this->getController()->genUrl('extservices/googleoauth2', true);
		$client->setRedirectUri($redirect);
		try
		{
			$code = $this->getRequestParameter('code');
			$client->authenticate($code);
		}
		catch(Google_AuthException $ex)
		{
			KalturaLog::err($ex);
			$this->tokenError = true;
			return;
		}

		$tokenJsonStr = $client->getAccessToken();
		
		$origTimeZone = date_default_timezone_get();
		date_default_timezone_set(kConf::get( "date_default_timezone"));
		
		$partner->setGoogleOAuth2($appId, $tokenJsonStr, $subId);
		$partner->save();

		date_default_timezone_set($origTimeZone);
		
		$params = array(
			'ytid' => $appId,
			'status' => 1,
			'ks' => $limitedKsStr
		);
		if ($subId)
			$params['subid'] = $subId;

		$this->redirect('extservices/googleoauth2?'.http_build_query($params, null, '&'));
	}

	protected function executeStatus()
	{
		$this->paramsError = null;
		$this->tokenError = null;
		$this->ksError = null;
		$ksStr = $this->getRequestParameter('ks');
		$appId = $this->getRequestParameter('ytid');
		$subId = $this->getRequestParameter('subid');
		$appConfig = $this->getFromGoogleAuthConfig($appId);
		$ksValid = $this->processKs($ksStr);
		if (!$ksValid)
		{
			$this->ksError = true;
			return;
		}

		$ks = kCurrentContext::$ks_object;
		if ($ks == null || $appConfig == null)
		{
			$this->paramsError = true;
			return;
		}

		$partnerId = $ks->partner_id;
		$partner = $this->getPartner($partnerId);
		$tokenData = $partner->getGoogleOAuth2($appId, $subId);
		$client = $this->getGoogleClient($appId);
		try
		{
			$client->setAccessToken(json_encode($tokenData));
			$http = new Google_Http_Request('https://www.googleapis.com/oauth2/v1/tokeninfo?access_token='.$tokenData['access_token']);
			$request = $client->getIo()->makeRequest($http);
			$code = $request->getResponseHttpCode();
			$body = $request->getResponseBody();
			if ($code != 200)
			{
				KalturaLog::err('Google API returned: ' . $body);
				$this->tokenError = true;
			}
		}
		catch(Exception $ex)
		{
			KalturaLog::err($ex);
			$this->tokenError = true;
		}
	}

	protected function getFromGoogleAuthConfig($paramName, $default = null)
	{
		return kConf::get($paramName, 'google_auth', $default);
	}

	/**
	 * @param null $appId
	 * @return Google_Client
	 * @throws Exception
	 */
	protected function getGoogleClient($appId = null)
	{
		if ($appId === null)
			$appId = $this->getRequestParameter('ytid');

		$appConfig = $this->getFromGoogleAuthConfig($appId);
		if (!is_array($appConfig))
			throw new Exception('Google auth configuration not found for app id '.$appId);

		$clientId = isset($appConfig['clientId']) ? $appConfig['clientId'] : null;
		$clientSecret = isset($appConfig['clientSecret']) ? $appConfig['clientSecret'] : null;

		$client = new Google_Client();
		$client->setClientId($clientId);
		$client->setClientSecret($clientSecret);

		return $client;
	}


}