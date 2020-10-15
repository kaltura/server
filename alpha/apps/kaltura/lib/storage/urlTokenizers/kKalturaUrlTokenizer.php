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
		$path = substr($url, 0, strrpos($url, "/"));
		$fileName = substr($url, strrpos($url, "/") - strlen($url) + 1);
		$signature = urlencode(base64_encode(hash_hmac('sha256', $path, $this->secret)));
		return $path . '/sig/' . $signature . '/' . $fileName;
	}

}