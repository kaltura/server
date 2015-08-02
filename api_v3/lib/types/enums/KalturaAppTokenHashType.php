<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaAppTokenHashType extends KalturaEnum
{

	const SHA1 = 1;
	const MD5 = 2;
	const SHA256 = 3;
	const SHA512 = 4;

	public static $HASH_MAP = array(self::SHA1 => 'SHA1', self::MD5 => 'MD5', self::SHA256 => 'SHA256', self::SHA512 => 'SHA512');
//	$hashMap = array(1 => "SHA1", 2 => "MD5", 3 => "SHA256", 4 => "SHA512");
}
