<?php
/**
 * @service zoomVendor
 * @package plugins.vendor
 * @subpackage api.services
 */
class ZoomVendorService extends KalturaBaseService
{

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
	 *
	 * @action oauthValidation
	 * @return string
	 * @throws Exception
	 */
	public function oauthValidationAction()
	{
		KalturaResponseCacher::disableCache();
		if (!kConf::hasMap(ZoomWrapper::MAP_NAME))
		{
			throw new KalturaAPIException("Vendor configuration file wasn't found!");
		}
		$zoomConfiguration = kConf::get(ZoomWrapper::CONFIGURATION_PARAM_NAME, ZoomWrapper::MAP_NAME);
		$clientId = $zoomConfiguration['clientId'];
		$zoomBaseURL = $zoomConfiguration['ZoomBaseUrl'];
		$redirectUrl = $zoomConfiguration['redirectUrl'];
		$isAdmin = false;
		$tokens = null;
		if (!array_key_exists('code', $_GET))
		{
			$url = $zoomBaseURL . '/oauth/authorize?' . 'response_type=code' . '&client_id=' . $clientId .  '&redirect_uri=' . $redirectUrl;
			ZoomHelper::redirect($url);
		}
		else
		{
			list($tokens, $permissions) = ZoomWrapper::retrieveZoomDataAsArray(ZoomHelper::API_USERS_ME_PERMISSIONS, true);
			list(, $user) = ZoomWrapper::retrieveZoomDataAsArray(ZoomHelper::API_USERS_ME, false, $tokens, null);
			$accountId = $user[ZoomHelper::ACCOUNT_ID];
			$zoomIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartner($accountId, VendorTypeEnum::ZOOM_ACCOUNT);
			if ($zoomIntegration && $zoomIntegration->getStatus() === VendorStatus::DELETED)
			{
				$zoomIntegration->setStatus(VendorStatus::ACTIVE);
			}
			ZoomHelper::saveNewTokenData($tokens, $accountId, $zoomIntegration);
			$permissions = $permissions['permissions'];
			$isAdmin = ZoomHelper::canConfigureEventSubscription($permissions);
		}
		if ($isAdmin)
		{
			ZoomHelper::loadLoginPage($tokens);
		}
		throw new KalturaAPIException('Only Zoom admins are allowed to access kaltura configuration page, please check your user account');
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
		ZoomHelper::verifyHeaderToken();
		$data = ZoomHelper::getPayloadData();
		$accountId = ZoomHelper::extractAccountIdFromDeAuthPayload($data);
		KalturaLog::info("Zoom changing account id: $accountId status to deleted , user de-authorized the app");
		$zoomIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartner($accountId, VendorTypeEnum::ZOOM_ACCOUNT);
		if (!$zoomIntegration)
		{
			throw new KalturaAPIException('Zoom Integration data Does Not Exist for current Partner');
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
	 */
	public function fetchRegistrationPageAction($tokensData, $iv)
	{
		KalturaResponseCacher::disableCache();
		$tokensData = base64_decode($tokensData);
		$iv = base64_decode($iv);
		$tokens = $this->handleEncryptTokens($tokensData, $iv);
		$zoomUserData = ZoomWrapper::retrieveZoomUserData($tokens[kZoomOauth::ACCESS_TOKEN]);

		$accountId = $zoomUserData[ZoomHelper::ACCOUNT_ID];
		/** @var ZoomVendorIntegration $zoomIntegration */
		$zoomIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartner($accountId,VendorTypeEnum::ZOOM_ACCOUNT);
		$partnerId = kCurrentContext::getCurrentPartnerId();
		if ($zoomIntegration && intval($partnerId) !==  $zoomIntegration->getPartnerId() && $partnerId !== 0)
		{
			KalturaLog::info("Zoom changing account id: $accountId partner to $partnerId");
			$zoomIntegration->setPartnerId($partnerId);
			$zoomIntegration->setTokensData();
			$zoomIntegration->save();
		}

		ZoomHelper::loadSubmitPage($zoomIntegration, $accountId, $this->getKs());
	}

	protected function handleEncryptTokens($tokensData, $iv)
	{
		$zoomConfiguration = kConf::get(ZoomWrapper::CONFIGURATION_PARAM_NAME, ZoomWrapper::MAP_NAME);
		$verificationToken = $zoomConfiguration[kZoomOauth::VERIFICATION_TOKEN];
		$tokens = AESEncrypt::decrypt($verificationToken, $tokensData, $iv);
		$tokens = json_decode($tokens, true);
		$tokens = kZoomOauth::parseTokens($tokens);
		return $tokens;
	}

	/**
	 * @action submitRegistration
	 * @param string $defaultUserId
	 * @param string $zoomCategory
	 * @param string $accountId
	 * @param bool $enableRecordingUpload
	 * @param bool $createUserIfNotExist
	 * @return string
	 * @throws PropelException
	 * @throws Exception
	 */
	public function submitRegistrationAction($defaultUserId, $zoomCategory = null, $accountId, $enableRecordingUpload, $createUserIfNotExist)
	{
		KalturaResponseCacher::disableCache();
		$partnerId = kCurrentContext::getCurrentPartnerId();
		kuserPeer::createKuserForPartner($partnerId, $defaultUserId);

		/** @var ZoomVendorIntegration $zoomIntegration */
		$zoomIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartner($accountId, VendorTypeEnum::ZOOM_ACCOUNT);
		if (!$zoomIntegration)
		{
			$zoomIntegration = new ZoomVendorIntegration();
			$zoomIntegration->setAccountId($accountId);
			$zoomIntegration->setVendorType(VendorTypeEnum::ZOOM_ACCOUNT);
			$zoomIntegration->setPartnerId($partnerId);
		}

		$zoomIntegration->setCreateUserIfNotExist($createUserIfNotExist);

		if($enableRecordingUpload)
		{
			$zoomIntegration->setStatus(VendorStatus::ACTIVE);
		}
		else
		{
			$zoomIntegration->setStatus(VendorStatus::DISABLED);
		}

		$zoomIntegration->setDefaultUserEMail($defaultUserId);
		if ($zoomCategory)
		{
			$zoomIntegration->setZoomCategory($zoomCategory);
			$categoryId = ZoomHelper::createCategoryForZoom($partnerId, $zoomCategory);
			if($categoryId)
			{
				$zoomIntegration->setZoomCategoryId($categoryId);
			}
		}
		if (!$zoomCategory && $zoomIntegration->getZoomCategory() && $zoomIntegration->getZoomCategoryId())
		{
			$zoomIntegration->unsetCategory();
			$zoomIntegration->unsetCategoryId();
		}

		$zoomIntegration->save();
		return true;
	}

	/**
	 * @action recordingComplete
	 * @throws Exception
	 */
	public function recordingCompleteAction()
	{
		KalturaResponseCacher::disableCache();
		myPartnerUtils::resetAllFilters();
		ZoomHelper::verifyHeaderToken();
		$data = ZoomHelper::getPayloadData();
		list($accountId, $downloadToken, $hostEmail, $downloadURLs, $meetingId, $topic) = ZoomHelper::extractDataFromRecordingCompletePayload($data);
		/** @var ZoomVendorIntegration $zoomIntegration */
		$zoomIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartner($accountId, VendorTypeEnum::ZOOM_ACCOUNT);
		if (!$zoomIntegration)
		{
			throw new kVendorException(kVendorException::NO_INTEGRATION_DATA);
		}

		if($zoomIntegration->getStatus()==VendorStatus::DISABLED)
		{
			KalturaLog::info("Received recording complete event from Zoom account {$accountId} while upload is disabled.");
			throw new KalturaAPIException('Uploads are disabled for current Partner');
		}

		$emails = ZoomHelper::extractCoHosts($meetingId, $zoomIntegration);
		$validatedEmails = ZoomHelper::getValidatedUsers($emails, $zoomIntegration->getPartnerId(), $zoomIntegration->getCreateUserIfNotExist());
		$dbUser = ZoomHelper::getEntryOwner($hostEmail, $zoomIntegration->getDefaultUserEMail(), $zoomIntegration->getPartnerId(), $zoomIntegration->getCreateUserIfNotExist());
		// user logged in - need to re-init kPermissionManager in order to determine current user's permissions
		$ks = null;
		$this->setPartnerFilters($zoomIntegration->getPartnerId());
		kSessionUtils::createKSessionNoValidations($dbUser->getPartnerId() , $dbUser->getPuserId() , $ks, 86400 , false , "" , '*' );
		kCurrentContext::initKsPartnerUser($ks);
		kPermissionManager::init();
		$urls = ZoomHelper::parseDownloadUrls($downloadURLs, $downloadToken);
		ZoomHelper::uploadToKaltura($urls, $dbUser, $zoomIntegration, $validatedEmails, $meetingId, $hostEmail, $topic);
	}
}