<?php
class kMirrorImageUrlManager extends kUrlManager
{
    
    const DEFAULT_ACCESS_WINDOW_SECONDS = 900; // 900 seconds = 15 minutes
    const DEFAULT_START_TIME_PAST_OFFSET = 86400; // yesterday
    
    // **************************************************************
    // TODO: should start and end time be in seconds or milliseconds ??
	// **************************************************************
	
	/**
	 * @param FileSync $fileSync
	 * @return string
	 */
	public function getFileSyncUrl(FileSync $fileSync)
	{
		$url = parent::getFileSyncUrl($fileSync);
		
        // decoration for HTTP URLs is done here instead of in finalizeUrls since playManifest doesn't currently allow calling finalizeURLs for HTTP
		if ($this->protocol == StorageProfile::PLAY_FORMAT_HTTP && @$this->params['http_auth_salt'])
		{
		   $storageProfile = StorageProfilePeer::retrieveByPK($fileSync->getDc());
			if ($storageProfile)
			{
			    // get parameters
    		    $window = $this->params['http_auth_seconds'];
    			$secret = $this->params['http_auth_salt'];
    			$startTime = time();	
    			$useDummyHost = false;	
					    
    			// build the full file sync URL to decorate
    			$httpBaseUrl = rtrim($storageProfile->getDeliveryHttpBaseUrl(), '/');
    			$urlToDecorate = $httpBaseUrl.'/'. ltrim($url, '/');
    			
			    // decorate URLs
			    $decoratedUrl = self::decorateUrl($urlToDecorate, $secret, $startTime, $window, $useDummyHost);
			    
			    // remove the base URL from the decorated URL
			    $url = substr($decoratedUrl, strlen($httpBaseUrl.'/'));
			}
		}
		
		return $url;
	}
	
    
	/**
	 * @param string baseUrl
	 * @param array $flavorUrls
	 */
	public function finalizeUrls(&$baseUrl, &$flavorsUrls)
	{
		if (!count($flavorsUrls))
		{
			return;
		}
			
		$secret = null;

		// grab token parameters for RTMP
		if ($this->protocol == StorageProfile::PLAY_FORMAT_RTMP && @$this->params['rtmp_auth_salt'])
		{
			$window = $this->params['rtmp_auth_seconds'];
			$secret = $this->params['rtmp_auth_salt'];
			$useDummyHost = true;      	
		}
				
		// tokenize URLs
		if (!is_null($secret))
		{
		    $startTime = time();
			foreach($flavorsUrls as &$flavor)
			{			    
			    $bareUrl = $baseUrl.'/'.ltrim($flavor["url"],'/');
			    $decoratedUrl = self::decorateUrl($bareUrl, $secret, $startTime, $window, $useDummyHost);
        	    $flavor["url"] = substr($decoratedUrl, strlen($baseUrl.'/'));
			}
		}
	}
	
	
	/**
	 * Decorate guardian URL
	 * @param unknown_type $bareUrl
	 * @param unknown_type $secret
	 * @param unknown_type $startTime in seconds
	 * @param unknown_type $window in seconds
	 * @param unknown_type $useDummyHost
	 */
	private static function decorateUrl($bareUrl, $secret, $startTime, $window, $useDummyHost)
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
	    if (is_null($window) || !is_int($window))
	    {
		    $window = self::DEFAULT_ACCESS_WINDOW_SECONDS;
		}		
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
		
		$hashSource = $dummyHost.$prepUrl.$miiAuth.$miiAuthValue.$secret;
		
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
    
}
