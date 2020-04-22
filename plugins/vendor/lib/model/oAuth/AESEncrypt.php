<?php
/**
 * @package plugins.vendor
 * @subpackage model.zoomOauth
 */

class AESEncrypt
{
	/**
	 * @param $key
	 * @param $message
	 * @return array
	 * @throws Exception
	 */
	public static function encrypt($key, $message)
	{
		$key = substr(sha1($key, true), 0, 16);
		$iv = self::genIv();
		/** @noinspection PhpUndefinedMethodInspection */
		return array(KCryptoWrapper::encrypt_aes($message, $key, $iv), $iv);
	}

	/**
	 * @param $key
	 * @param $message
	 * @param $iv
	 * @return string
	 */
	public static function decrypt($key, $message, $iv)
	{
		$key = substr(sha1($key, true), 0, 16);
		/** @noinspection PhpUndefinedMethodInspection */
		return rtrim(KCryptoWrapper::decrypt_aes($message, $key, $iv), "\0");
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	private static function genIv()
	{
		$efforts = 0;
		$maxEfforts = 50;
		$wasItSecure = false;
		do
		{
			$efforts += 1;
			$iv = openssl_random_pseudo_bytes(16, $wasItSecure);
			if ($efforts > $maxEfforts)
			{
				throw new KalturaAPIException('Unable to genereate secure iv for tokens.');
			}
		} while (!$wasItSecure);
		return $iv;
	}
}