<?php

require_once (KALTURA_ROOT_PATH .'/vendor/phpGangsta/GoogleAuthenticator.php');

class authenticationUtils
{

	public static function generateQRCodeUrl($kuser, $loginData)
	{
		return str_replace ("|", "M%7C", GoogleAuthenticator::getQRCodeGoogleUrl($kuser->getPuserId() . '_' . kConf::get ('www_host') . '_KMC', $loginData->getSeedFor2FactorAuth()));
	}

	public static function getQRImage($kuser, $loginData)
	{
		$qrUrl = self::generateQRCodeUrl($kuser, $loginData);
		$curlWrapper = new KCurlWrapper();
		$response = $curlWrapper->exec($qrUrl);
		if (!$response || $curlWrapper->getHttpCode() !== 200 || $curlWrapper->getError())
		{
			KalturaLog::err("Google Authenticator Curl returned error, Error code : {$curlWrapper->getHttpCode()}, Error: {$curlWrapper->getError()} ");
			return null;
		}
		$encodedQr = base64_encode($response);
		return $encodedQr;
	}

	public static function generateNewSeed($userLoginData)
	{
		$userLoginData->setSeedFor2FactorAuth(GoogleAuthenticator::createSecret());
		$userLoginData->save();
	}

	public static function addAuthMailJob($partner, $kuser, $mailType)
	{
		$loginData = $kuser->getLoginData();
		$loginData->setPasswordHashKey($loginData->newPassHashKey(kConf::get('user_login_qr_page_hash_key_validity')));
		$loginData->save();
		$partnerId = $partner->getId();
		
		$roleNames = $kuser->getUserRoleNames();
		$roleNameToUseDynamicEmailTemplate = kEmails::getDynamicEmailUserRoleName($roleNames);
		if ($roleNameToUseDynamicEmailTemplate)
		{
			return self::addDynamicContentAuthMailJob($partnerId, $kuser, $loginData, $roleNameToUseDynamicEmailTemplate, $mailType);
		}
		
		$publisherName = $partner->getName();
		$resetPasswordLink = UserLoginDataPeer::getAuthInfoLink($loginData->getPasswordHashKey());
		if(!$resetPasswordLink)
		{
			return null;
		}
		$bodyParams = array($kuser->getFullName(), $partnerId, $resetPasswordLink, $kuser->getEmail(), $partnerId, $publisherName, $publisherName, $kuser->getUserRoleNames(), $publisherName, $kuser->getPuserId());
		$job = kJobsManager::addMailJob(
			null,
			0,
			$kuser->getPartnerId(),
			$mailType,
			kMailJobData::MAIL_PRIORITY_NORMAL,
			kConf::get("partner_registration_confirmation_email"),
			kConf::get("partner_registration_confirmation_name"),
			$kuser->getEmail(),
			$bodyParams
		);

		return $job;
	}

	public static function addSsoMailJob($partner, $kuser, $mailType)
	{
		$partnerId = $partner->getId();
		$publisherName = $partner->getName();
		$loginLink = kConf::get('login_link','sso');
		$bodyParams = array($kuser->getFullName(), $partnerId, $loginLink, $kuser->getEmail(), $partnerId, $publisherName, $publisherName, $kuser->getUserRoleNames(), $publisherName, $kuser->getPuserId());

		$job = kJobsManager::addMailJob(
			null,
			0,
			$kuser->getPartnerId(),
			$mailType,
			kMailJobData::MAIL_PRIORITY_NORMAL,
			kConf::get ("partner_registration_confirmation_email" ),
			kConf::get ("partner_registration_confirmation_name" ),
			$kuser->getEmail(),
			$bodyParams
		);

		return $job;
	}

	public static function verify2FACode($loginData, $otp)
	{
		$userSeed = $loginData->getSeedFor2FactorAuth();
		return GoogleAuthenticator::verifyCode ($userSeed, $otp);
	}
	
	protected static function addDynamicContentAuthMailJob($partnerId, $kuser, $loginData, $userRole, $mailType)
	{
		$dynamicQrPageBaseLink = kEmails::getDynamicTemplateBaseLink($userRole, kEmails::DYNAMIC_EMAIL_2FA_BASE_LINK);
		$qrPageLink = UserLoginDataPeer::getAuthInfoLink($loginData->getPasswordHashKey(), $dynamicQrPageBaseLink);
		
		$associativeBodyParams = array(
			kEmails::TAG_USER_NAME           => $kuser->getFullName(),
			kEmails::TAG_LOGIN_EMAIL         => $kuser->getEmail(),
			kEmails::TAG_ROLE_NAME           => $userRole,
			kEmails::TAG_QR_CODE_LINK        => $qrPageLink,
			kEmails::TAG_PARTNER_ID          => $partnerId);
		$dynamicEmailContents = kEmails::getDynamicEmailData($mailType, $userRole);
		$dynamicEmailContents->setEmailBody(kEmails::populateCustomEmailBody($dynamicEmailContents->getEmailBody(), $associativeBodyParams));
		return kJobsManager::addDynamicEmailJob(
			$partnerId,
			$mailType,
			kMailJobData::MAIL_PRIORITY_NORMAL,
			$kuser->getEmail(),
			'partner_registration_confirmation_email',
			'partner_registration_confirmation_name',
			$dynamicEmailContents
		);
	}
}