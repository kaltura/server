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
			return false;
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
		if(!kConf::hasMap('vendor'))
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
			list(, $user) =  ZoomWrapper::retrieveZoomDataAsArray(ZoomHelper::API_USERS_ME, false, $tokens, null);
			$accountId = $user[ZoomHelper::ACCOUNT_ID];
			$zoomIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartner($accountId, VendorTypeEnum::ZOOM_ACCOUNT);
			if($zoomIntegration && $zoomIntegration->getStatus() === VendorStatus::DELETED)
				$zoomIntegration->setStatus(VendorStatus::ACTIVE);
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
		KalturaResponseCacher::disableCache();
		myPartnerUtils::resetAllFilters();
		ZoomHelper::verifyHeaderToken();
		$data = ZoomHelper::getPayloadData();
		$accountId = ZoomHelper::extractAccountIdFromDeAuthPayload($data);
		KalturaLog::info("Zoom changing account id: $accountId status to deleted , user de-authorized the app");
		$zoomIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartner($accountId,
			VendorTypeEnum::ZOOM_ACCOUNT);
		if (!$zoomIntegration)
			throw new KalturaAPIException('Zoom Integration data Does Not Exist for current Partner');
		$zoomIntegration->setStatus(VendorStatus::DELETED);
		$zoomIntegration->save();
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
		$tokens = ABSEncrypt::aesDecrypt($verificationToken, $tokensData, $iv);
		$tokens = json_decode($tokens, true);
		$accessToken = $tokens[kZoomOauth::ACCESS_TOKEN];
		list($tokens, $zoomUserData) = ZoomWrapper::retrieveZoomDataAsArray(ZoomHelper::API_USERS_ME, false, $tokens, null);
		$accountId = $zoomUserData[ZoomHelper::ACCOUNT_ID];
		/** @var ZoomVendorIntegration $zoomIntegration */
		$zoomIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartner($accountId,
			VendorTypeEnum::ZOOM_ACCOUNT);
		if ($accessToken !== $tokens[kZoomOauth::ACCESS_TOKEN]) // token changed -> refresh tokens
			ZoomHelper::saveNewTokenData($tokens, $accountId, $zoomIntegration);
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
	 * @return string
	 * @throws PropelException
	 */
	public function submitRegistrationAction($defaultUserId, $zoomCategory, $accountId)
	{
		KalturaResponseCacher::disableCache();
		$partnerId = kCurrentContext::getCurrentPartnerId();
		/** @var ZoomVendorIntegration $zoomIntegration */
		$zoomIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartner($accountId,
			VendorTypeEnum::ZOOM_ACCOUNT);
		if(!$zoomIntegration)
		{
			$zoomIntegration = new ZoomVendorIntegration();
			$zoomIntegration->setAccountId($accountId);
			$zoomIntegration->setVendorType(VendorTypeEnum::ZOOM_ACCOUNT);
			$zoomIntegration->setPartnerId($partnerId);
		}
		$zoomIntegration->setStatus(VendorStatus::ACTIVE);
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
		KalturaResponseCacher::disableCache();
		myPartnerUtils::resetAllFilters();
		ZoomHelper::verifyHeaderToken();
		$data = ZoomHelper::getPayloadData();
		list($accountId, $downloadToken, $hostEmail, $downloadURL, $meetingId) = ZoomHelper::extractDataFromRecordingCompletePayload($data);
		/** @var ZoomVendorIntegration $zoomIntegration */
		$zoomIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartner($accountId, VendorTypeEnum::ZOOM_ACCOUNT);
		if (!$zoomIntegration)
			throw new KalturaAPIException('Zoom Integration data Does Not Exist for current Partner');
		$emails = ZoomHelper::extractCoHosts($meetingId, $zoomIntegration, $accountId);
		// user logged in - need to re-init kPermissionManager in order to determine current user's permissions
		$ks = null;
		$dbUser = kuserPeer::getKuserByPartnerAndUid($zoomIntegration->getPartnerId(), $hostEmail, true);
		if (!$dbUser) //if not go to default user
		{
			$emails[] = $hostEmail;
			$dbUser = kuserPeer::getKuserByPartnerAndUid($zoomIntegration->getPartnerId(), $zoomIntegration->getDefaultUserEMail(), true);
		}
		kSessionUtils::createKSessionNoValidations($dbUser->getPartnerId() , $dbUser->getPuserId() , $ks, 86400 , false , "" , '*' );
		kCurrentContext::initKsPartnerUser($ks);
		kPermissionManager::init();
		$url = ZoomHelper::parseDownloadUrl($downloadURL, $downloadToken);
		$entryId = ZoomHelper::createEntryForZoom($dbUser, $zoomIntegration->getZoomCategory(), $emails, $meetingId);
		kJobsManager::addImportJob(null, $entryId, $dbUser->getPartnerId(), $url);
		KalturaLog::debug('Zoom - upload entry to kaltura started, partner id: '. $zoomIntegration->getPartnerId() . 'host email: ' . $hostEmail . 'emails: ' . print_r($emails, true) .
		'meeting Id: ' . $meetingId . 'entry Id: ' . $entryId);
	}

}