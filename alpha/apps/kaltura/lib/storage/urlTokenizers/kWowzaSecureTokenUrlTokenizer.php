<?php

class kWowzaSecureTokenUrlTokenizer extends kUrlTokenizer
{
	
	/**
	 * @var string
	 */
	protected $paramPrefix;
	
	/**
	 * @var bool
	 */
	protected $shouldIncludeClientIp;
	
	/**
	 * @var string
	 */
	protected $hashAlgorithm;

	
	public function tokenizeSingleUrl($url, $urlPrefix = null)
	{
		$tokenPrefix = $this->getParamPrefix();
		$expiryTimeFrame = $this->getWindow();
		
		$params = array();
		
		$params["starttime"] = time();
		$params["endtime"] = time() + $expiryTimeFrame;
		
		$prefixedParams = $this->prefixParams($tokenPrefix,$params);
		
		return $url."?".http_build_query($prefixedParams,'','&').'&'.$tokenPrefix.'hash='.$this->getHash($prefixedParams,$url);
		
	}
	
	/**
     * Get prefixed parameters
     * 
     * @return array
     */
	protected function prefixParams($prefix,$params){
		
		if(!empty($prefix))
        {
            foreach($params as $key => $param)
            {
                if(strpos($key, $prefix) === false)
                {
                    $params[$prefix . $key] = $param;
                    unset($params[$key]);
                }
            }
        }
        
        return $params;
	}
		
	/**
     * Get hash token
     * 
     * @return string
     */
    public function getHash($params,$url){
	
		$tokenKey = $this->getKey();
		$shouldIncludeClientIp = $this->getShouldIncludeClientIp();
				
		$params[$tokenKey] = "";
		
		if($shouldIncludeClientIp == true){
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
		
        $query = trim($query, '&');
		
		$urlInfo = parse_url($url);
		
        $path = ltrim($urlInfo['path'], '/');
        $pathItems = explode('/', $path);

        $path = "";
        foreach ($pathItems as $k => $pathItem) {
            if(1 === preg_match('/(^Manifest|\.m3u8|\.f4m|\.mpd)/',$pathItem)){
                break;
            }
            $path .= $pathItem;
            if(count($pathItems)-1 != $k) {
                $path .= '/';
            }
        }
        if(strrpos($path, '/') === strlen($path)-1) {
            $path = substr($path, 0, -1);
        }
				
        $path .= "?".$query;
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
	 * @return the $shouldIncludeClientIp value
	 */
	public function getShouldIncludeClientIp() {
		return $this->shouldIncludeClientIp;
	}

	/**
	 * param bool $shouldIncludeClientIp
	 */
	public function setShouldIncludeClientIp($shouldIncludeClientIp) {
		$this->shouldIncludeClientIp = $shouldIncludeClientIp;
	}
		
	/**
	 * @return the $hashAlgorithm value
	 */
	public function getHashAlgorithm() {
		return $this->hashAlgorithm;
	}

	/**
	 * param bool $hashAlgorithm
	 */
	public function setHashAlgorithm($hashAlgorithm) {
		$this->hashAlgorithm = $hashAlgorithm;
	}
	
}
