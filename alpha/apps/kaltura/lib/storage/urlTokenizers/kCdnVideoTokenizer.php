<?php
class kCdnVideoTokenizer extends kUrlTokenizer
{
	/**
	 * @var string
	 */
	public $urlPrefix;
	
	/**
	 * @param string $url
	 * @param string $urlPrefix
	 * @return string
	 */
	public function tokenizeSingleUrl($url, $urlPrefix = null)
	{
		/// TODO: call to other method
		// seek parameter (fs) must be added after the token
//		$seekParam = '';
//		$seekParamPos = strpos($url, '&fs=');
//		if ($seekParamPos !== false)
//		{
//			$seekParam = substr($url, $seekParamPos);
//			$url = substr($url, 0, $seekParamPos);
//		}
//
//		$url .= '&e=' . (time() + 120);
//		$fullUrl = $this->urlPrefix . $url;
//		$url .= '&h=' . md5($this->key . $fullUrl);
//		$url .= $seekParam;
		return $url;
	}
	
	/**
	 * @return the $urlPrefix
	 */
	public function getUrlPrefix() {
		return $this->urlPrefix;
	}

	/**
	 * @param string $urlPrefix
	 */
	public function setUrlPrefix($urlPrefix) {
		$this->urlPrefix = $urlPrefix;
	}

	
	
}
