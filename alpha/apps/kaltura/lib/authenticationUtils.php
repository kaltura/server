<?php

require_once (KALTURA_ROOT_PATH .'/vendor/phpGangsta/GoogleAuthenticator.php');

class authenticationUtils
{

	const KALTURA_EXISTING_USER_2FA_EMAIL = 139;

	public static function generateQRCodeUrl($kuser)
	{
		return str_replace ("|", "M%7C", GoogleAuthenticator::getQRCodeGoogleUrl($kuser->getPuserId() . ' ' . kConf::get ('www_host') . ' KAC', $kuser->getLoginData()->getSeedFor2FactorAuth()));
	}

	public static function getQRImage($kuser)
	{
		$qrUrl = self::generateQRCodeUrl($kuser);
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

	public static function generateNewSeed($kuser)
	{
		$userLoginData = $kuser->getLoginData();
		$userLoginData->setSeedFor2FactorAuth(GoogleAuthenticator::createSecret());
		$userLoginData->save();
	}

	public static function add2FAMailJob($kuser)
	{
		$kmcngParams = kConf::get('kmcng');
		$qrUrl = $kmcngParams['kaltura']['qrUrl'];
		if(!$qrUrl)
		{
			return null;
		}
		$bodyParams = array($kuser->getFullName(), $kuser->getPartnerId(), $qrUrl);

		$job = kJobsManager::addMailJob(
			null,
			0,
			$kuser->getPartnerId(),
			self::KALTURA_EXISTING_USER_2FA_EMAIL,
			kMailJobData::MAIL_PRIORITY_NORMAL,
			kConf::get ("partner_registration_confirmation_email" ),
			kConf::get ("partner_registration_confirmation_name" ),
			$kuser->getEmail(),
			$bodyParams
		);

		return $job;
	}
}