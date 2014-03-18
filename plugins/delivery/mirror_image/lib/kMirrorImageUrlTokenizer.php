<?php
// @_!! Extenalize
class kMirrorImageUrlTokenizer extends kUrlTokenizer
{
    const DEFAULT_START_TIME_PAST_OFFSET = 86400; // yesterday

	protected $useDummyHost;
	protected $baseUrl;
	
	/**
	 * @param string $url
	 * @return string
	 */
	public function tokenizeSingleUrl($url)
	{
		// get parameters
		$startTime = time();	
				
		// build the full file sync URL to decorate
		$urlToDecorate = $this->baseUrl.'/'. ltrim($url, '/');
		
		// decorate URLs
		$decoratedUrl = self::decorateUrl($urlToDecorate, $this->key, time(), $this->window, $this->useDummyHost);
		
		// remove the base URL from the decorated URL
		return substr($decoratedUrl, strlen($this->baseUrl.'/'));
	}
	
	/**
	 * @param string $baseUrl
	 * @param array $flavors
	 */
	public function tokenizeMultiUrls(&$baseUrl, &$flavors)
	{
		$startTime = time();
		foreach($flavors as &$flavor)
		{			    
			$bareUrl = $baseUrl.'/'.ltrim($flavor["url"],'/');
			$decoratedUrl = self::decorateUrl($bareUrl, $this->key, $startTime, $this->window, $this->useDummyHost);
			$flavor["url"] = substr($decoratedUrl, strlen($baseUrl.'/'));
		}
	}
	
	/**
	 * Decorate guardian URL
	 * @param unknown_type $bareUrl
	 * @param unknown_type $key
	 * @param unknown_type $startTime in seconds
	 * @param unknown_type $window in seconds
	 * @param unknown_type $useDummyHost
	 */
	private static function decorateUrl($bareUrl, $key, $startTime, $window, $useDummyHost)
	{
        // extract url prefix
        if ($useDummyHost)
        {
            $matches = array();
            $urlPrefix = '';     
            $matched = preg_match('/^.+:\/\/[^\/]+\//', $bareUrl, $matches);
            if ($matched > 0) {
                $urlPrefix = rtrim($matches[0],'/');
            }
            $bareUrl = str_replace($urlPrefix, '', $bareUrl);       
        }
	    
	    // set $window and $endTime
		$endTime = $startTime + $window;
		$startTime = $startTime - self::DEFAULT_START_TIME_PAST_OFFSET;
	    
		
	    // NOTE - the following code was copied from the samples provided by mirror image and was slightly edited to fit
	    
	    $prepUrl = null;
	    $hashUrl = null;
	    
	    $dummyHost = $useDummyHost ? 'http://guardian.mii/' : '';
	    	    
    	// remove Fragment if it exists.
    	$fragment = null;
        $offset  = strrpos( $bareUrl, "#" );
        if ( $offset === false )
        {
            // no fragment - most common case
            $prepUrl  = $bareUrl;
            $fragment = null;
        }
        else
        {
            // fragment exists, remove it
            $prepUrl  = substr( $bareUrl, 0, $offset );
            $fragment = substr( $bareUrl, $offset );
        }
        
        // determine if a query string exists in the URL
        $hasQuery = strpos( $prepUrl, "?" ) !== false;
        
        // create MIIAuth parameter
        $miiAuth = $hasQuery ? $miiAuth = "&MIIAuth=" : $miiAuth = "?MIIAuth=";
    	$miiAuthValue = 'a'.$endTime.';b'.$startTime.';';
		
		$hashSource = $dummyHost.$prepUrl.$miiAuth.$miiAuthValue.$key;
		
        $prepUrl .= $miiAuth;     
        $prepUrl .= urlencode($miiAuthValue);
        
        // Generate a string Hash value (32 character HEX string)
        $hashString  = sha1( $hashSource );
        
        // convert HEX string to Binary String of 16 bytes
        $hashBinary = pack("H*", $hashString);
        
        // convert binary string into a printable string using Base64
        $hashBase64  = self::base64url_encode( $hashBinary );           
        
        // Remove trailing Equal Signs ("=")
        $hashBase64 = rtrim( $hashBase64, "=" );
        
        // make Base64 encode string safe to send
        $hashValue   = urlencode( $hashBase64 );        
        
        // generate decorated URL
        $decoratedUrl  = $prepUrl . "&MIIHash=" . $hashValue;
        
    	if (!is_null($fragment))
        {
            // append fragment to decorated URL before returning it
        	// NOTE $fragment starts with the "#" character so there is no need to add it here
            $decoratedUrl .= $fragment;        
        }	    
	    
        // attach back the url prefix
        $decoratedUrl = $urlPrefix.$decoratedUrl;
        
	    return $decoratedUrl;
	}
		
    private static function base64url_encode($text)
    {
        $b64 = base64_encode($text);
        // now convert + to - and / to _ to make this URL safe and avoid percent-encoding
        $b64 = str_replace("+", "-", $b64);
        $b64 = str_replace("/", "_", $b64);
        
        return $b64;
    }
    


	/**
	 * @return the $useDummyHost
	 */
	public function getUseDummyHost() {
		return $this->useDummyHost;
	}

	/**
	 * @return the $baseUrl
	 */
	public function getBaseUrl() {
		return $this->baseUrl;
	}
	
	/**
	 * @param field_type $useDummyHost
	 */
	public function setUseDummyHost($useDummyHost) {
		$this->useDummyHost = $useDummyHost;
	}

	/**
	 * @param field_type $baseUrl
	 */
	public function setBaseUrl($baseUrl) {
		$this->baseUrl = $baseUrl;
	}


    
    
}
