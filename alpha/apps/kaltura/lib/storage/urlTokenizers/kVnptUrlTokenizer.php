<?php

require_once(dirname(__file__) . '/../../../../../../vendor/akamai/token/StreamTokenFactory.php');

class kVnptUrlTokenizer extends kUrlTokenizer
{
	const HTTP_VOD_TOKEN_FORMAT = 0;
	const VOD_LIVE_TOKEN_FORMAT = 1;

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
		$tokenKey = $this->key;
		$expiryTimeFrame = $this->window;
		$tokenizationFormat = $this->getTokenizationFormat();
		

		$clientIp = infraRequestUtils::getIpFromHttpHeader('HTTP_X_FORWARDED_FOR',false);
		if (!$clientIp)
		{
			$clientIp = $_SERVER['REMOTE_ADDR'];
		}

		$expiredTime = time() + $expiryTimeFrame;

		$tokenizationSuffix = '';
		switch($tokenizationFormat)
		{
			case self::HTTP_VOD_TOKEN_FORMAT:
			default:
				$tokenizationSuffix = $url;
				break;
			case self::VOD_LIVE_TOKEN_FORMAT:
				preg_match_all('/\//', $url,$matches, PREG_OFFSET_CAPTURE);
				$lastSlashLocationIndex = end($matches[0]);
				$tokenizationSuffix = substr($url, 0, $lastSlashLocationIndex[1]);
				break;
		}
		$url = md5($clientIp . ":$tokenKey" . ":$expiredTime" . ":$tokenizationSuffix") . $expiredTime . $url;
		return $url;
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
