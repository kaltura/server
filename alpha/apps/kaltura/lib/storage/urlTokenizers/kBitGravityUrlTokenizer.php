<?php
class kBitGravityUrlTokenizer extends kUrlTokenizer
{
	/**
	 * Regex pattern to find the part of the URL that should be hashed
	 *
	 * @var string
	 */
	protected $hashPatternRegex;

	/**
	 * @param string $url
	 * @return string
	 */
	public function tokenizeSingleUrl($url)
	{
		return $this->tokenizeUrl($url);
	}
	
	/**
	 * @param string $baseUrl
	 * @param array $flavors
	 */
	public function tokenizeMultiUrls(&$baseUrl, &$flavors)
	{
	   foreach($flavors as $flavorKey => $flavor)
		{
			if (isset($flavor['url']) && $flavor['url'])
			{
				$fileExtension = isset($flavor['ext']) ? $flavor['ext'] : null;
				$flavors[$flavorKey]['url'] = $this->tokenizeUrl($flavor['url'], $baseUrl, $fileExtension);
			}
		} 
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
		if (!$this->hashPatternRegex)
			return $url;

		if (preg_match($this->hashPatternRegex, $url, $matches))
		{
			$hashit = $matches[1];
			// when using remote storage and rtmp playback, 'mp4:' is being appended to the url and shouldn't be part of the hash
			if (strpos($hashit, 'mp4:') === 0)
				$hashit = str_replace('mp4:', '', $hashit);

			$hashData = $this->key.'/'.ltrim($hashit, '/').'?e='.$expiryTime;
			$hash = md5($hashData);
			if (strpos($url, '?') !== false)
				$s = '&';
			else
				$s = '?';
			return $url.$s.'e='.$expiryTime.'&h='.$hash;
		}
		return $url;
	}
	
	/**
	 * @return the $hashPatternRegex
	 */
	public function getHashPatternRegex() {
		return $this->hashPatternRegex;
	}

	/**
	 * @param string $hashPatternRegex
	 */
	public function setHashPatternRegex($hashPatternRegex) {
		$this->hashPatternRegex = $hashPatternRegex;
	}

}
