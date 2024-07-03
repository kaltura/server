<?php
/**
 * @package plugins.httpNotification
 * @subpackage model.enum
 */

class KalturaSecureHashingAlgo implements SecureHashingAlgo, IKalturaPluginEnum
{
	const SECURE_HASHING_ALGO = 'SecureHashingAlgo';

	public static function getAdditionalValues()
	{
		return array(
			'SECURE_HASHING_ALGO' => self::SECURE_HASHING_ALGO,
		);
	}

	public static function getAdditionalDescriptions()
	{
		return array();
	}
}
