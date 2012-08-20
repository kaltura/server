<?php
class kLimeLightUrlTokenizer extends kUrlTokenizer
{
	/**
	 * @var string
	 */
	public $urlPrefix;

	/**
	 * @var string
	 */
	public $key;
	
	/**
	 * @param string $urlPrefix
	 * @param string $key
	 */
	public function __construct($urlPrefix, $key)
	{
		$this->urlPrefix = $urlPrefix;
		$this->key = $key;
	}

	/**
	 * @param string $url
	 * @return string
	 */
	public function tokenizeSingleUrl($url)
	{
		// seek parameter (fs) must be added after the token
		$seekParam = '';
		$seekParamPos = strpos($url, '&fs=');
		if ($seekParamPos !== false)
		{
			$seekParam = substr($url, $seekParamPos);
			$url = substr($url, 0, $seekParamPos);
		}
	
		$url .= '&e=' . (time() + 120);
		$fullUrl = $this->urlPrefix . $url;
		$url .= '&h=' . md5($this->key . $fullUrl);
		$url .= $seekParam;
		return $url;
	}
}
