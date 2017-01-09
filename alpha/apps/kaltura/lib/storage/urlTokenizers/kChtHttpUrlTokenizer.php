<?php
class kChtHttpUrlTokenizer extends kUrlTokenizer
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
	 * @param string $url
	 * @param string $baseUrl
	 * @param string $fileExtension
	 * @return string
	 */
	public function tokenizeUrl($url)
	{
		$expiryTime = time() + $this->window;

		$hashit = dirname($url) . "/";
		$hashData = $hashit . $this->key . $expiryTime	;
		$token = base64_encode(md5($hashData, true));
		
		//remove = character from the token
		$token = strtr($token, '+/', '-_');
		$token = str_replace('=', '', $token);
		
		if (strpos($url, '?') !== false)
			$s = '&';
		else
			$s = '?';
		
		return $url.$s.'token='.$token.'&expires='.$expiryTime;
	}
}