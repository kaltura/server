<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_KavaHelper
{
	
	public static function generateSignedKavaDashboardUrl($kavaDashboardUrl, $jwtKey, $partnerId, $sessionExpiry)
	{
		$jwtPayload = array(
			'partnerId' => $partnerId,
			'userId' => Infra_AuthHelper::getAuthInstance()->getIdentity()->getUser()->id,
			'iat' => time(),
			'exp' => time() + $sessionExpiry,
		);
		$jwt = self::encode($jwtPayload, $jwtKey);
		return rtrim($kavaDashboardUrl, "/") . "/?jwt=" . $jwt;
	}
	
	public static function urlsafeB64Encode($input)
	{
		return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
	}
	
	private static function encode($payload, $key)
	{
		$header = array('typ' => 'JWT', 'alg' => 'HS256');
		$result = self::urlsafeB64Encode(json_encode($header)) . '.' .
			self::urlsafeB64Encode(json_encode($payload));
		$signature = hash_hmac('sha256', $result, $key, true);
		$result .= '.' . self::urlsafeB64Encode($signature);
		return $result;
	}
}