<?php
/**
 *
 * @package Core
 * @subpackage model
 */

class kKalturaUrlRecognizer extends kUrlRecognizer
{

	const DOWNLOAD_NOT_RECOGNIZED = 0;
	const DOWNLOAD_RECOGNIZED_OK = 1;
	const DOWNLOAD_RECOGNIZED_NOT_OK = 2;

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

		$matches = null;
		if (!preg_match('#/sig/([^/]+)/#', $uri, $matches, PREG_OFFSET_CAPTURE))
		{
			return self::DOWNLOAD_NOT_RECOGNIZED;
		}
		$partToSign = substr($uri, 0, $matches[0][1]);
		$requestSignature = $matches[1][0];

		if (!preg_match('#/exp/([^/]+)/#', $uri, $expMatches, PREG_OFFSET_CAPTURE))
		{
			return self::DOWNLOAD_NOT_RECOGNIZED;
		}
		$expiry = $expMatches[1][0];
		if($expiry && (time() > $expiry))
		{
			return self::DOWNLOAD_RECOGNIZED_NOT_OK;
		}

		$partToSign = $this->encodeFileName($partToSign);

		$calculatedSignature = kDeliveryUtils::urlsafeB64Encode(hash_hmac('sha256', $partToSign, $this->key, true));
		if($calculatedSignature !== $requestSignature)
		{
			return self::DOWNLOAD_RECOGNIZED_NOT_OK;
		}

		return self::DOWNLOAD_RECOGNIZED_OK;
	}

	protected function encodeFileName($partToSign)
	{
		if(preg_match('#/fileName/([^/]+)/#', $partToSign, $fileNameMatches, PREG_OFFSET_CAPTURE))
		{
			$prefix = substr($partToSign, 0, $fileNameMatches[1][1]);
			$newFileName = rawurlencode($fileNameMatches[1][0]);
			$suffix = substr($partToSign, $fileNameMatches[1][1] + strlen($fileNameMatches[1][0]));
			$partToSign = $prefix . $newFileName . $suffix;
		}

		return $partToSign;
	}

}