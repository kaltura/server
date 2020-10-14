<?php


class kKalturaUrlTokenizer extends kUrlTokenizer
{

	/**
	 * @var string
	 */
	protected $secret;

	/**
	 * @return string
	 */
	public function getSecret()
	{
		return $this->secret;
	}

	/**
	 * @param string $secret
	 */
	public function setSecret($secret)
	{
		$this->secret = $secret;
	}

	/**
	 * @param string $url
	 * @param string $urlPrefix
	 * @return string
	 */
	public function tokenizeSingleUrl($url, $urlPrefix = null)
	{
		$signature = urlencode(base64_encode(hash_hmac('sha256', $url, $this->secret)));
		return $url . '/sig/' . $signature;
	}

}