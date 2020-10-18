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

		$matches = null;
		if (!preg_match('#/sig/([^/]+)/#', $uri, $matches, PREG_OFFSET_CAPTURE))
		{
			return false;
		}
		$partToSign = substr($uri, 0, $matches[0][1]);
		$requestSignature = $matches[1][0];
		$calculatedSignature = kDeliveryUtils::urlsafeB64Encode(hash_hmac('sha256', $partToSign, $this->key));
		if($calculatedSignature !== $requestSignature)
		{
			return false;
		}

		return true;
	}

}