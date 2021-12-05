<?php

class myKuserUtils
{
	const NON_EXISTING_USER_ID = -1;
	const USERS_DELIMITER = ',';
	const DOT_CHAR = '.';

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

	public static function sanitizeFields(array $values)
	{
		$sanitizedValues = array();
		foreach ($values as $val)
		{
			$sanitizedVal = self::sanitizeField($val);
			if(!$sanitizedVal)
			{
				$sanitizedVal = 'Unknown';
			}
			$sanitizedValues[] = $sanitizedVal;
		}
		return $sanitizedValues;
	}

	public static function sanitizeField($val)
	{
		$sanitizedStr = '';
		$strParts = explode(' ', $val);
		foreach ($strParts as $strPart)
		{
			// remove all parts that contain . char
			if(strpos($strPart, self::DOT_CHAR) === false)
			{
				$sanitizedStr .= $strPart . ' ';
			}
		}

		return trim($sanitizedStr);
	}
}