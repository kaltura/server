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
	public function tokenizeUrl($url, $baseUrl = null, $fileExtension = null)
	{
		$expiryTime = time() + $this->window;

		$hashit = $matches[1];
		// when using remote storage and rtmp playback, 'mp4:' is being appended to the url and shouldn't be part of the hash
		if (strpos($hashit, 'mp4:') === 0)
			$hashit = str_replace('mp4:', '', $hashit);

		$hashData = $url . $this->key . $expiryTime	;
		$hash = base64_encode(md5($hashData, true));
		
		if (strpos($url, '?') !== false)
			$s = '&';
		else
			$s = '?';
		
		return $url.$s.'token='.$hash.'&expires='.$expiryTime;
	}
}