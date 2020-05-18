<?php
/**
 * @package server-infra
 * @subpackage request
 */
class kNetworkUtils
{
	const KALTURA_AUTH_HEADER = 'HTTP_X_KALTURA_AUTH';
	/**
	 * @return bool
	 * @throws Exception
	 */
	public static function isAuthenticatedURI()
	{
		if (!isset($_SERVER[self::KALTURA_AUTH_HEADER]))
		{
			KalturaLog::warning("Missing Header Parameter - ". self::KALTURA_AUTH_HEADER);
			return false;
		}
		$xKalturaAuth = $_SERVER[self::KALTURA_AUTH_HEADER];
		$parts = explode(',', $xKalturaAuth);
		if (count($parts) != 3)
		{
			KalturaLog::warning('Invalid Fromat for ' . self::KALTURA_AUTH_HEADER);
			return false;
		}

		$version = $parts[0];
		$timestamp = $parts[1];
		$expectedSignature = $parts[2];
		$url = $_SERVER['REQUEST_URI'];
		$secret = kConf::get('authentication_secret','secrets', null);
		if (!$secret)
		{
			KalturaLog::warning("Missing authentication_secret in configuration");
			return false;
		}

		$actualSignature = base64_encode(hash_hmac('sha256', "$version,$timestamp,$url", $secret, true));
		KalturaLog::debug("Actual Signature [$actualSignature] - Expected Signature [$expectedSignature]" );
		if ( $actualSignature !== $expectedSignature)
		{
			KalturaLog::warning("Could not authenticate X-Kaltura-Auth");
			return false;
		}

		return true;
	}
}