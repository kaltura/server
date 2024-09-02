<?php

class myKuserUtils
{
	const NON_EXISTING_USER_ID = -1;
	const USERS_DELIMITER = ',';
	const DOT_CHAR = '.';
	const SPACE_CHAR = ' ';

	public static function preparePusersToKusersFilter( $puserIdsCsv )
	{
		$kuserIdsArr = array();
		$puserIdsArr = explode(self::USERS_DELIMITER, $puserIdsCsv);
		$kuserArr = kuserPeer::getKuserByPartnerAndUids(kCurrentContext::getCurrentPartnerId(), $puserIdsArr);

		foreach($kuserArr as $kuser)
		{
			$kuserIdsArr[] = $kuser->getId();
		}

		if(!empty($kuserIdsArr))
		{
			return implode(self::USERS_DELIMITER, $kuserIdsArr);
		}

		return self::NON_EXISTING_USER_ID; // no result will be returned if no puser exists
	}

	public static function startsWithSpecialChar($str)
	{
		return $str && in_array($str[0], array('+', '=', '-', '@', ','));
	}

	public static function sanitizeFields(array $values)
	{
		$sanitizedValues = array();
		foreach ($values as $val)
		{
			$sanitizedVal = str_replace(self::DOT_CHAR, self::SPACE_CHAR, $val);
			$sanitizedValues[] = $sanitizedVal;
		}
		return $sanitizedValues;
	}
	
	public static function getHashedUserId($userId)
	{
		$encKey = kConf::get("user_id_enc_key", kConfMapNames::RUNTIME_CONFIG, null);
		if(!$encKey)
		{
			return $userId;
		}
		
		$userId = self::normalizeKuserId($userId);
		return hash_hmac("sha256", kCurrentContext::getCurrentPartnerId() . "_" . $userId, $encKey);
	}
	
	public static function normalizeKuserId($userId)
	{
		return str_replace(' ', '', trim(strtolower($userId)));
	}

	public static function isAllowedAdminEmailDomain($email, $emailDomains)
	{
		return in_array(substr(strrchr($email, "@"), 1), $emailDomains);
	}
}
