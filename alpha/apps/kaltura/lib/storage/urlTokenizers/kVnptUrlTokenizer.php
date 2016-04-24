<?php

require_once(dirname(__file__) . '/../../../../../../vendor/akamai/token/StreamTokenFactory.php');

class kVnptUrlTokenizer extends kUrlTokenizer
{
	/**
	 * @var string
	 */
	protected $tokenKey;

	/**
	 * @var int
	 */
	protected $expiryTimeFrame;

	/**
	 * @var int
	 */
	protected $tokenizationFormat;

	/**
	 * @param string $url
	 * @param string $urlPrefix
	 */
	public function tokenizeSingleUrl($url, $urlPrefix = null)
	{
		$tokenKey = $this->tokenKey;
		$expiryTimeFrame = $this->expiryTimeFrame;
		$tokenizationFormat = $this->tokenizationFormat;
		

		$clientIp = infraRequestUtils::getIpFromHttpHeader('HTTP_X_FORWARDED_FOR',false);
		if (!$clientIp)
		{
			$clientIp = $_SERVER['REMOTE_ADDR'];
		}
		$expiredTime = time() + $expiryTimeFrame;

		$tokenizationSuffix = '';
		switch($tokenizationFormat)
		{
			case 0:
				$tokenizationSuffix = $url;
				break;
			case 1:
				preg_match_all('/\//', $url,$matches, PREG_OFFSET_CAPTURE);
				$lastSlashLocationIndex = end($matches[0]);
				$tokenizationSuffix = substr($url, 0, $lastSlashLocationIndex[1]);
				break;
		}
		$url = md5($clientIp . ":$tokenKey" . ":$expiredTime" . ":$tokenizationSuffix") . $expiredTime . $url;
		return $url;
	}

	/**
	 * @return the Token key
	 */
	public function getTokenKey() {
		return $this->tokenKey;
	}

	/**
	 * @param string $tokenKey
	 */
	public function setTokenKey($tokenKey) {
		$this->tokenKey = $tokenKey;
	}

	/**
	 * return the Expiry time frame
	 */
	public function getExpiryTimeFrame() {
		return $this->expiryTimeFrame;
	}

	/**
	 * @param int $expiryTimeFrame
	 */
	public function setExpiryTimeFrame($expiryTimeFrame) {
		$this->expiryTimeFrame = $expiryTimeFrame;
	}

	/**
	 * @return the $tokenization format
	 */
	public function getTokenizationFormat() {
		return $this->tokenizationFormat;
	}
	
	/**
	 * @param string $tokenizationFormat
	 */
	public function setTokenizationFormat($tokenizationFormat) {
		$this->tokenizationFormat = $tokenizationFormat;
	}
}
