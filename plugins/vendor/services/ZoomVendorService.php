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
	protected function partnerRequired($actionName)
	{
		if ($actionName == 'oauthValidation' || $actionName == 'recordingComplete')
		{
			return false;
		}
		return true;
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
		if (!kConf::hasMap('vendor'))
		{
			throw new KalturaAPIException("Vendor configuration file wasn't found!");
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
	 * @throws Exception
	 */
	public function fetchRegistrationPageAction($tokensData, $iv)
	{
		KalturaResponseCacher::disableCache();
		$tokensData = base64_decode($tokensData);
		$iv = base64_decode($iv);
		$zoomConfiguration = kConf::get('ZoomAccount', 'vendor');
		$verificationToken = $zoomConfiguration['verificationToken'];
		$tokens = AESEncrypt::decrypt($verificationToken, $tokensData, $iv);
		$tokens = json_decode($tokens, true);
		$accessToken = $tokens[kZoomOauth::ACCESS_TOKEN];
		list($tokens, $zoomUserData) = ZoomWrapper::retrieveZoomDataAsArray(ZoomHelper::API_USERS_ME, false, $tokens, null);
		$accountId = $zoomUserData[ZoomHelper::ACCOUNT_ID];
		/** @var ZoomVendorIntegration $zoomIntegration */
		$zoomIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartner($accountId,
			VendorTypeEnum::ZOOM_ACCOUNT);
		if ($accessToken !== $tokens[kZoomOauth::ACCESS_TOKEN])
		{
			// token changed -> refresh tokens
			ZoomHelper::saveNewTokenData($tokens, $accountId, $zoomIntegration);
		}
		$partnerId = kCurrentContext::getCurrentPartnerId();
		if ($zoomIntegration && intval($partnerId) !==  $zoomIntegration->getPartnerId() && $partnerId !== 0)
		{
			$zoomIntegration->setPartnerId($partnerId);
			$zoomIntegration->save();
		}
		ZoomHelper::loadSubmitPage($zoomIntegration, $accountId, $this->getKs());
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
		$dbUser = kuserPeer::createKuserForPartner($partnerId, $defaultUserId);

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
		$eventType = $data[ZoomHelper::EVENT];
		list($accountId, $downloadToken, $hostEmail, $downloadURLs, $meetingId) = ZoomHelper::extractDataFromRecordingCompletePayload($data);
		$zoomIntegration = ZoomHelper::getIntegrationVendor($accountId);

		if($eventType == ZoomHelper::RECORDING_VIDEO_COMPLETE)
		{
			$this->recordingVideoComplete($zoomIntegration, $accountId, $downloadToken, $hostEmail, $downloadURLs, $meetingId);
		}
		else if($eventType == ZoomHelper::RECORDING_TRANSCRIPT_COMPLETE)
		{
			$this->recordingTranscriptComplete($zoomIntegration, $accountId, $downloadToken, $hostEmail, $downloadURLs, $meetingId);
		}
	}


	public function recordingVideoComplete($zoomIntegration, $accountId, $downloadToken, $hostEmail, $downloadURLs, $meetingId)
	{
		$emails = ZoomHelper::extractCoHosts($meetingId, $zoomIntegration, $accountId);
		$emails = ZoomHelper::getValidatedUsers($emails, $zoomIntegration->getPartnerId(), $zoomIntegration->getCreateUserIfNotExist());
		$dbUser = ZoomHelper::getEntryOwner($hostEmail, $zoomIntegration->getDefaultUserEMail(), $zoomIntegration->getPartnerId(), $zoomIntegration->getCreateUserIfNotExist());
		// user logged in - need to re-init kPermissionManager in order to determine current user's permissions
		$ks = null;
		$this->setPartnerFilters($zoomIntegration->getPartnerId());
		kSessionUtils::createKSessionNoValidations($dbUser->getPartnerId() , $dbUser->getPuserId() , $ks, 86400 , false , "" , '*' );
		kCurrentContext::initKsPartnerUser($ks);
		kPermissionManager::init();
		$urls = ZoomHelper::parseDownloadUrls($downloadURLs, $downloadToken);
		ZoomHelper::uploadToKaltura($urls, $dbUser, $zoomIntegration, $emails, $meetingId, $hostEmail);
	}

	public function recordingTranscriptComplete($zoomIntegration, $accountId, $downloadToken, $hostEmail, $downloadURLs, $meetingId)
	{
		$entry = entryPeer::retrieveByReferenceIdAndPartnerId ('Zoom_'. $meetingId, $zoomIntegration->getPartnerId());
		if(!$entry)
		{
			throw new KalturaAPIException('could not find entry for meeting: ' . $meetingId);
		}

		$captionAssetService = new CaptionAssetService();
		$captionAssetService->initService('caption_captionasset', 'captionasset', 'setContent');
		foreach($downloadURLs as $transcriptUrl)
		{
			$captionAsset = ZoomHelper::createAssetForTranscription($entry);
			$captionAssetResource = new KalturaUrlResource();
			$captionAssetResource->url = $transcriptUrl;
			$captionAssetService->setContentAction($captionAsset->getId(), $captionAssetResource);
		}
	}



}