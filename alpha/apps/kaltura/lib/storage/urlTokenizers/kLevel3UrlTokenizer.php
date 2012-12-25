<?php
class kLevel3UrlTokenizer extends kUrlTokenizer
{
	/**
	 * @var string
	 */
	public $name = '';

	/**
	 * @var string
	 */
	public $key = false;

	/**
	 * @var string
	 */
	public $gen = false;

	/**
	 * @var bool
	 */
	public $includeExtension = false;

	/**
	 * @var int
	 */
	public $window = 0;
	
	/**
	 * @var string
	 */
	public $expiryName = false;
	
	/**
	 * @param string $name
	 * @param string $key
	 * @param string $gen
	 * @param bool $includeExtension
	 * @param int $window
	 */
	public function __construct($name, $key, $gen, $includeExtension, $expiryName = null, $window = 0)
	{
		$this->name = $name;
		$this->key = $key;
		$this->gen = $gen;
		$this->includeExtension = $includeExtension;
		$this->expiryName = $expiryName;
		$this->window = $window;
	}
	
	static private function hmac($hashfunc, $key, $data)
	{
		$blocksize=64;

		if (strlen($key) > $blocksize)
		{
			$key = pack('H*', $hashfunc($key));
		}

		$key = str_pad($key, $blocksize, chr(0x00));
		$ipad = str_repeat(chr(0x36), $blocksize);
		$opad = str_repeat(chr(0x5c), $blocksize);
		$hmac = pack('H*', $hashfunc(($key ^ $opad) . pack('H*', $hashfunc(($key ^ $ipad) . $data))));

		return bin2hex($hmac);
	}
	
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
	 * @param string $param
	 * @return string
	 */
	protected function addQueryStringParameter($url, $param)
	{
		$parsedUrl = parse_url($url);
		if (isset($parsedUrl['query']) && strlen($parsedUrl['query']) > 0)
			$url .= '&';
		else
			$url .= '?';
		$url .= $param;
		return $url;
	}
	
	/**
	 * @param string $url
	 * @param string $baseUrl
	 * @param string $fileExtension
	 * @return string
	 */
	public function tokenizeUrl($url, $baseUrl = null, $fileExtension = null)
	{
		$url = preg_replace('/([^:])\/\//','$1/', $url);
		$fullUrl = trim(str_replace('mp4:', '', $url), '/');
		if (!is_null($baseUrl)) 
		{
			$fullUrl = rtrim($baseUrl, '/').'/'.$fullUrl;
		}

		if ($this->includeExtension && $fileExtension == 'flv')
		{
			$fullUrl .= ".$fileExtension";
		}

		if ($this->window)
		{
			$expiry = "{$this->expiryName}=" . strftime("%Y%m%d%H%M%S", time() - date("Z") + $this->window);
			$url = $this->addQueryStringParameter($url, $expiry);
			$fullUrl = $this->addQueryStringParameter($fullUrl, $expiry);
		}
		
		$parsedUrl = parse_url($fullUrl);
		$pathString = '/'.ltrim($parsedUrl['path'],'/');
		if (isset($parsedUrl['query']) && strlen($parsedUrl['query']) > 0)
			$pathString .= '?'.$parsedUrl['query'];
		
		$token = substr(self::hmac('sha1', $this->key, $pathString), 0, 20);
		
		$url = $this->addQueryStringParameter($url, "{$this->name}={$this->gen}".$token);
		return $url;
	}	
}
