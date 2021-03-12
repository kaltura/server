<?php
/**
 *
 * @package Core
 * @subpackage model
 */

class kKalturaUrlRecognizer extends kUrlRecognizer
{

	/**
	 * @var string
	 */
	protected $key;

	/**
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * @param string $key
	 */
	public function setKey($key)
	{
		$this->key = $key;
	}

	public function isRecognized($requestOrigin)
	{
		$uri = $_SERVER["REQUEST_URI"];

		if (!preg_match('#/exp/([^/]+)/sig/([^/]+)/#', $uri, $matches, PREG_OFFSET_CAPTURE))
		{
			return self::NOT_RECOGNIZED;
		}

		$expiry = $matches[1][0];
		$partToSign = substr($uri, 0, $matches[1][1] + strlen($expiry));
		$requestSignature = $matches[2][0];

		$currentTime = time();
		if($expiry && ($currentTime > $expiry))
		{
			KalturaLog::debug("Request expired, expiry value: [$expiry] current time: [$currentTime]");
			return self::RECOGNIZED_NOT_OK;
		}

		$calculatedSignature = kDeliveryUtils::urlsafeB64Encode(hash_hmac('sha256', $partToSign, $this->key, true));
		if($calculatedSignature !== $requestSignature)
		{
			return self::RECOGNIZED_NOT_OK;
		}

		return self::RECOGNIZED_OK;
	}

}