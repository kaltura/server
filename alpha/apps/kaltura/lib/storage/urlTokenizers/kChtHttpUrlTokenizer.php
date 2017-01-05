<?php
class kChtHttpUrlTokenizer extends kHashPatternUrlTokenizer
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
		if (!$this->hashPatternRegex)
			return $url;
		
		if (preg_match($this->hashPatternRegex, $url, $matches))
		{
			$expiryTime = time() + $this->window;
	
			$hashData = $matches[0] . $this->key . $expiryTime	;
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
		
		return $url;
	}
}