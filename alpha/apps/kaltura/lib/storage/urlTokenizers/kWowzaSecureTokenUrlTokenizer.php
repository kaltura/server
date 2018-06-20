<?php

class kWowzaSecureTokenUrlTokenizer extends kUrlTokenizer
{
	/**
	 * @var string
	 */
	protected $paramPrefix;
	
	/**
	 * @var string
	 */
	protected $hashAlgorithm;
	
	public function tokenizeSingleUrl($url, $urlPrefix = null)
	{
		$tokenPrefix = $this->getParamPrefix();
		
		$expiryTimeFrame = $this->getWindow();
		
		$params = array();
		
		$params['starttime'] = time();
		$params['endtime'] = time() + $expiryTimeFrame;
		
		$urlTokenParameters = $this->prefixParams($tokenPrefix,$params);
		
		$urlTokenParameters[$tokenPrefix.'hash'] = $this->getHash($urlTokenParameters,$url);
		
		return $url.'?'.http_build_query($urlTokenParameters, '', '&');
	}
	
	/**
	 * Get prefixed parameters
	 * 
	 * @return array
	 */
	private function prefixParams($prefix, $rawParameters)
	{
		$prefixedParameters = array();
				
		foreach($rawParameters as $key => $param)
		{
			$prefixedParameters[$prefix.$key] = $param;
		}

		return $prefixedParameters;
	}
	
	/**
	 * Get hash token
	 * 
	 * @return string
	 */
	public function getHash($params, $url)
	{
		$tokenKey = $this->getKey();
		
		// Key is added as parameter name with no value
		$params[$tokenKey] = '';
		
		if($this->getLimitIpAddress())
		{
			// Client IP is added as parameter name with no value
			$params[self::getRemoteAddress()] = '';
		}
		
		// Wowza requires parameters be in alpha order when hashed
		ksort($params);
		
		$query = '';
		
		// Building parameters.
		// Cannot use 'http_build_query' here as it includes the equals sign for parameters that have no value.
		foreach($params as $k => $v)
		{
			$query .= '&' . $k;
			if(isset($v) && !empty($v))
			{
				$query .= '=' . $v;
			}
		}
		
		$query = trim($query,'&');
		
		$path = parse_url($url,PHP_URL_PATH);
		
		// In Wowza url format, the final slash character of path should denote the end of the VOD filename.
		$finalSlash = strrpos($path, '/');
		
		$path = substr($path, 0, $finalSlash);
		
		$path = trim($path,'/') . '?' . $query;
		
		return strtr(base64_encode(hash($this->getHashAlgorithm(), $path, true)),'+/','-_');
	}
	
	/**
	 * @return the $paramPrefix value
	 */
	public function getParamPrefix() 
	{
		return $this->paramPrefix;
	}
	
	/**
	 * @param string $paramPrefix
	 */
	public function setParamPrefix($paramPrefix) 
	{
		$this->paramPrefix = $paramPrefix;
	}
	
	/**
	 * @return the $hashAlgorithm value
	 */
	public function getHashAlgorithm()
	{
		return $this->hashAlgorithm;
	}
	
	/**
	 * param string $hashAlgorithm
	 */
	public function setHashAlgorithm($hashAlgorithm)
	{
		$this->hashAlgorithm = $hashAlgorithm;
	}
}
