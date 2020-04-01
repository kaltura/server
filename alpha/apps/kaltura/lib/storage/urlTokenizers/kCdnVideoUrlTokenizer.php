<?php
class kCdnVideoUrlTokenizer extends kUrlTokenizer
{
	/**
	 * @param string $url
	 * @param string $urlPrefix
	 * @return string
	 */
	public function tokenizeSingleUrl($url, $urlPrefix = null)
	{
		return $this->tokenizeUrl($url);
	}
	/**
	 * @param $url
	 * @return string
	 */
	private function tokenizeUrl($url)
	{
		$expiryTime = time()+$this->getWindow();
		$baseUrl = parse_url($url);
		$hashData = $this->getKey().":$expiryTime:$baseUrl[path]";
		$token = $this->getTokenByData($hashData);

		if (strpos($url, '?') !== false)
			$s = '&';
		else
			$s = '?';

		return $url.$s.'md5='.$token.'&e='.$expiryTime;
	}
	/**
	 * @param $hashData
	 * @return string
	 */
	private function getTokenByData($hashData) {
		$token = base64_encode(md5($hashData, true));

		//remove character from the token
		$token = strtr($token, '+/', '-_');
		$token = str_replace('=', '', $token);
		return $token;
	}
}
