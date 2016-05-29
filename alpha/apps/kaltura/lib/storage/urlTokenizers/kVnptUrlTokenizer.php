<?php

require_once(dirname(__file__) . '/../../../../../../vendor/akamai/token/StreamTokenFactory.php');

class kVnptUrlTokenizer extends kUrlTokenizer
{
	const VOD_TOKEN_FORMAT = 0;
	const LIVE_TOKEN_FORMAT = 1;
	const HTTP_VOD_TOKEN_FORMAT = 2;

	/**
	 * @var int
	 */
	protected $tokenizationFormat;

	/**
	 * @var bool
	 */
	protected $shouldIncludeClientIp;

	/**
	 * @param string $url
	 * @param string $urlPrefix
	 */
	public function tokenizeSingleUrl($url, $urlPrefix = null)
	{
		$tokenKey = $this->key;
		$expiryTimeFrame = $this->window;
		$tokenizationFormat = $this->getTokenizationFormat();
		$shouldIncludeClientIp = $this->getShouldIncludeClientIp();
		

		$clientIp = infraRequestUtils::getIpFromHttpHeader('HTTP_X_FORWARDED_FOR',false);
		if (!$clientIp)
		{
			$clientIp = $_SERVER['REMOTE_ADDR'];
		}

		$expiredTime = time() + $expiryTimeFrame;

		$tokenizationSuffix = '';
		switch($tokenizationFormat)
		{
			case self::VOD_TOKEN_FORMAT:
			case self::LIVE_TOKEN_FORMAT:
				preg_match_all('/\//', $url,$matches, PREG_OFFSET_CAPTURE);
				$lastSlashLocationIndex = end($matches[0]);
				$tokenizationSuffix = substr($url, 0, $lastSlashLocationIndex[1]);
				break;
			case self::HTTP_VOD_TOKEN_FORMAT:
			default:
				$tokenizationSuffix = $url;
				break;
		}
		$stringForTokenization = ":$tokenKey" . ":$expiredTime" . ":$tokenizationSuffix";
		if($shouldIncludeClientIp == true)
		{
			$stringForTokenization = $clientIp . $stringForTokenization;
		}

		$url = md5($stringForTokenization) . $expiredTime . $url;
		if($tokenizationFormat == self::LIVE_TOKEN_FORMAT)
			$url = "/" . $url;

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

	/**
	 * @return the $shouldIncludeClientIp value
	 */
	public function getShouldIncludeClientIp() {
		return $this->shouldIncludeClientIp;
	}

	/**
	 * param bool $shouldIncludeClientIp
	 */
	public function setShouldIncludeClientIp($shouldIncludeClientIp) {
		$this->shouldIncludeClientIp = $shouldIncludeClientIp;
	}

}
