<?php

class kCloudFrontUrlTokenizer extends kUrlTokenizer
{
	/**
	 * @var string
	 */
	protected $keyPairId;
	
	/**
	 * @var string
	 */
	protected $rootDir;

	static function urlSafeBase64Encode($value)
	{
		$encoded = base64_encode($value);
		return str_replace(
				array('+', '=', '/'),
				array('-', '_', '~'),
				$encoded);
	}
	
	function rsaSha1Sign($policy)
	{
		$signature = "";
		$pkeyid = openssl_get_privatekey($this->key);
		openssl_sign($policy, $signature, $pkeyid);
		openssl_free_key($pkeyid);
		return $signature;
	}
	
	protected function getAcl($baseUrl, array $urls)
	{
		require_once( dirname(__FILE__). '/../../../../../../infra/general/kString.class.php');
	
		// strip the filenames of all urls
		foreach ($urls as &$url)
		{
			$slashPos = strrpos($url, '/');
			if ($slashPos !== false)
			{
				$url = substr($url, 0, $slashPos + 1);
			}
		}
		
		$acl = kString::getCommonPrefix($urls);
	
		// the first comma in csmil denotes the beginning of the non-common URL part
		$commaPos = strpos($acl, ',');
		if ($commaPos !== false)
		{
			$acl = substr($acl, 0, $commaPos);
		}
	
		// if the base url has a port, remove it
		$parsedUrl = parse_url($baseUrl);
		if (isset($parsedUrl['port']))
		{
			$baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
			if (isset($parsedUrl['path']))
			{
				$baseUrl .= $parsedUrl['path'];
			}
		}
		
		$acl = $baseUrl . $acl . '*';
	
		return $acl;
	}
	
	protected function generateToken($acl)
	{
		$DateLessThan = time() + $this->window;
		$conditions = array('"DateLessThan":{"AWS:EpochTime":'.$DateLessThan.'}');
		$ipStr = "";
		if ($this->getLimitIpAddress())
		{
			// When multiple equivalent requests hit cloudfront at once, cloudfront may respond with the same response although
			// caching headers instruct not to cache the response. When content is secured by IP, one clinet may receive content
			// tokenized using the IP of another client.   
			$ip = self::getRemoteAddress();
			$ipStr = "ip=$ip&";
			$conditions[] = '"IpAddress":{"AWS:SourceIp":"'.$ip.'/32"}';
		}
		$policy = '{"Statement":[{"Resource":"'.$acl.'","Condition":{'.implode(',', $conditions).'}}]}';
		$signature = $this->rsaSha1Sign($policy);
		
		$policy = self::urlSafeBase64Encode($policy);
		$signature = self::urlSafeBase64Encode($signature);
		
		return $ipStr . 'Policy=' . $policy . '&Signature=' . $signature . '&Key-Pair-Id=' . $this->keyPairId;
	}
	
	protected function appendToken($url, $token)
	{
		if (strpos($url, '?') === false)
			$url .= '?';
		else
			$url .= '&';
		return $url . $token;
	}
	
	public function tokenizeSingleUrl($url, $urlPrefix = null)
	{
		if ($this->rootDir)
		    $url = rtrim($this->rootDir, '/') . '/' . ltrim($url, '/');

		$acl = $this->getAcl($urlPrefix,  array($url));
		if (!$acl)
			return $url;
		
		return $this->appendToken($url, $this->generateToken($acl));
	}
	
	public function tokenizeMultiUrls(&$baseUrl, &$flavors)
	{
		$urls = array();
		foreach($flavors as &$flavor)
		{
			if ($this->rootDir)
				$flavor["url"] = rtrim($this->rootDir, '/') . '/' . ltrim($flavor["url"], '/');
			$urls[] = $flavor["url"];
		}
	
		$acl = $this->getAcl($baseUrl, $urls);
		if (!$acl)
			return;
	
		$token = $this->generateToken($acl);
		
		foreach($flavors as &$flavor)
		{
			$flavor["url"] = $this->appendToken($flavor["url"], $token);
		}
	}
	
	/**
	 * @return the $keyPairId
	 */
	public function getKeyPairId() 
	{
		return $this->keyPairId;
	}
	
	/**
	 * @param string $param
	 */
	public function setKeyPairId($keyPairId) 
	{
		$this->keyPairId = $keyPairId;
	}
	
	/**
	 * @return the $rootDir
	 */
	public function getRootDir() 
	{
		return $this->rootDir;
	}
	
	/**
	 * @param string $rootDir
	 */
	public function setRootDir($rootDir) 
	{
		$this->rootDir = $rootDir;
	}
}
