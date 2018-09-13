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

	const PARTICIPANT = '/v2/metrics/meetings/@meetingId@/participants';

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
			$zoomIntegration = VendorIntegrationPeer::retrieveSingleVendorPerAccountAndType($accountId, VendorTypeEnum::ZOOM_ACCOUNT);
			if($zoomIntegration && $zoomIntegration->getStatus() === VendorStatus::DELETED)
				$zoomIntegration->setStatus(VendorStatus::ACTIVE);
			$this->saveNewTokenData($tokens, $accountId, $zoomIntegration);
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
		list($tokens, $zoomUserData) = $retrieveDataFromZoom->retrieveZoomDataAsArray(self::USERS_ME, false, $tokens, null);
		$accountId = $zoomUserData[self::ACCOUNT_ID];
		$zoomIntegration = VendorIntegrationPeer::retrieveSingleVendorPerAccountAndType($accountId,
			VendorTypeEnum::ZOOM_ACCOUNT);
		if ($accessToken !== $tokens[kZoomOauth::ACCESS_TOKEN]) // token changed -> refresh tokens
		{
			$this->saveNewTokenData($tokens, $accountId, $zoomIntegration);
		}
		$partnerId = kCurrentContext::getCurrentPartnerId();
		if ($zoomIntegration && intval($partnerId) !==  $zoomIntegration->getPartnerId() && $partnerId !== 0)
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
		$zoomIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartner($accountId,
			VendorTypeEnum::ZOOM_ACCOUNT, $partnerId);
		if(is_null($zoomIntegration))
		{
			$zoomIntegration = new VendorIntegration();
			$zoomIntegration->setAccountId($accountId);
			$zoomIntegration->setVendorType(VendorTypeEnum::ZOOM_ACCOUNT);
			$zoomIntegration->setPartnerId($partnerId);
		}
		$zoomIntegration->setStatus(VendorStatus::ACTIVE);
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
		KalturaLog::info('starting upload token to Kaltura server');
		$zoomConfiguration = kConf::get('ZoomAccount', 'vendor');
		$verificationToken = $zoomConfiguration['verificationToken'];

		if($verificationToken !== $verificationToken) //todo:: Roie get recived token
			KExternalErrors::dieGracefully('Received verification token is different from existing token');
		$accountId = 1; //todo:: Roie retrive account Id
		$zoomIntegration = VendorIntegrationPeer::retrieveSingleVendorPerAccountAndType($accountId, VendorTypeEnum::ZOOM_ACCOUNT);

		// user logged in - need to re-init kPermissionManager in order to determine current user's permissions
		$ks = null;
		// todo: take user email from data
		$dbUser = kuserPeer::getKuserByPartnerAndUid($zoomIntegration->getPartnerId(), $zoomIntegration->getDefaultUserEMail());
		kSessionUtils::createKSessionNoValidations($dbUser->getPartnerId() , $dbUser->getPuserId() , $ks, 86400 , false , "" , '*' );
		kCurrentContext::initKsPartnerUser($ks);
		kPermissionManager::init();
		$entry = $this->createEntryForZoom($dbUser, $zoomIntegration->getZoomCategory(),$this->parseUrl());

	}

	private function parseUrl()
	{
	}

	/**
	 * @param kuser $dbUser
	 * @param string $zoomCategory
	 * @param $url
	 * @return entry
	 * @throws Exception
	 */
	private function createEntryForZoom($dbUser, $zoomCategory, $url)
	{
		$entry = new entry();
		$entry->setType(entryType::MEDIA_CLIP);
		$entry->setMediaType(entry::ENTRY_MEDIA_TYPE_VIDEO);
		$entry->setName('ZOOM_'.time());
		$entry->setPartnerId($dbUser->getPartnerId());
		$entry->setStatus(entryStatus::NO_CONTENT);
		$entry->setSourceType(EntrySourceType::ZOOM);
		$entry->setPuserId($dbUser->getPuserId());
		$entry->setkuser($dbUser->getKuserId());
		$entry->setConversionProfileId(myPartnerUtils::getConversionProfile2ForPartner($dbUser->getPartnerId())->getId());
		$entry->setAdminTags('zoom');
		$entry->setCategories($zoomCategory);
		$entry->save();
		KalturaLog::info('Zoom Entry Created, Entry ID:  ' . $entry->getId());
		kJobsManager::addImportJob(null, $entry->getId(), $entry->getPartnerId(), $url);
		return $entry;
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
	 * @param $tokensDataAsArray
	 * @param $accountId
	 * @param VendorIntegration $zoomClientData
	 */
	private function saveNewTokenData($tokensDataAsArray, $accountId, $zoomClientData = null)
	{
		if (!$zoomClientData) // create new vendorIntegration during oauth first time
		{
			$zoomClientData = new VendorIntegration();
			$zoomClientData->setStatus(VendorStatus::DISABLED);
		}
		$zoomClientData->saveNewTokenData($tokensDataAsArray,$accountId);
	}
}