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
		
		return $url."?".http_build_query($urlTokenParameters);
	}
	
	/**
	 * Get prefixed parameters
	 * 
	 * @return array
	 */
	private function prefixParams($prefix, $rawParameters)
	{
		$prefixedParameters = array();
		
		if(empty($rawParameters))
		{
			return $prefixedParameters;
		}
		
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
		
		$params[$tokenKey] = "";
		
		if($this->getLimitIpAddress())
		{
			$params[self::getRemoteAddress()] = "";
		}
		
		ksort($params);
		
		$query = '';
		foreach($params as $k => $v)
		{
			$query .= '&' . $k;
			if(isset($v) && !empty($v))
			{
				$query .= '=' . $v;
			}
		}
		
		$query = trim($query,'&');
		
		$path = '';
		
		if($parsed_url = parse_url($url))
		{
			$path = $parsed_url["path"];
		}
		
		$finalSlash = strrpos($path, '/');
		
		$filepath = $path = substr($path, 0, $finalSlash);
		
		$path = trim($path,'/') . "?" . $query;
		
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