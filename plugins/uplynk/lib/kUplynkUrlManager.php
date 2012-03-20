<?php
class kUplynkUrlManager extends kUrlManager
{
    
    const DEFAULT_EXPIRY_WINDOW_SECONDS = 120; // 120 seconds = 2 minutes
    const DEFAULT_IOS_APPLICATION_KEY = 'default';
    	
	
	/**
	 * @param FileSync $fileSync
	 * @return string
	 */
	public function getFileSyncUrl(FileSync $fileSync)
	{
		// get url manager params
		list($expiryWindow, $apiKey, $accountId) = $this->getUplynkAccountParams();
		
		// verify required parameters
		if (strlen($accountId) <= 0) {
		    //TODO: error - no account id
		}
	    if (strlen($apiKey) <= 0) {
		    //TODO: error - no api key
		}
		
		// get playback cotext params (parameters passed by the player
		$playbackParams = $this->getPlaybackParamsArrays();
		
		$device = isset($playbackParams['device']) ? $playbackParams['device'] : null;
		$isIosDevice = $device == '001' ? false : true;
		
		// get query string parameters
		$queryParams = $this->getQueryStringParams($fileSync, $expiryWindow, $accountId, $playbackParams, $isIosDevice);
				
		// build query string
        $queryString = $this->getQueryString($queryParams);
		
		// generate hash key
		$hashedQuery = hash_hmac('sha256', $queryString, $apiKey);
		$queryString .= '&sig='.$hashedQuery;
		
		// decide about file extension according to device		
		$fileExtension = $isIosDevice ? 'json' : 'm3u8';
		
		// add extension and query string to the final url
		$url = parent::getFileSyncUrl($fileSync);
		$url .= '.'.$fileExtension .'?'. $queryString;		     
				
		return $url;
	}
	
    
	
	/**
	 * Finalize URLs
	 * @param string baseUrl
	 * @param array $flavorUrls
	 */
	public function finalizeUrls(&$baseUrl, &$flavorsUrls)
	{
		if (!count($flavorsUrls))
		{
			return;
		}
		
		// concat baseUrl to all flavor Urls
		$baseUrl = rtrim($baseUrl,'/');
		foreach($flavorsUrls as &$flavor)
		{			    
		    $flavor["url"] = $baseUrl . '/' . ltrim($flavor["url"],'/');
		}
		
		// empty baseUrl so it will not be sent in the manifest
		$baseUrl = '';
	}
	
	
    /**
     * @return uplyink account parameters from the url parameters array
     */
	protected function getUplynkAccountParams()
	{
	    $expiryWindow = null;
	    $apiKey = null;
	    $accountId = null;
	     
	    if ($this->protocol == StorageProfile::PLAY_FORMAT_HTTP )
		{
		    $expiryWindow = isset($this->params['http_auth_window'])     ? $this->params['http_auth_window']     : self::DEFAULT_EXPIRY_WINDOW_SECONDS;
		    $apiKey       = isset($this->params['http_auth_api_key'])    ? $this->params['http_auth_api_key']    : null;
		    $accountId    = isset($this->params['http_auth_account_id']) ? $this->params['http_auth_account_id'] : null;    	
		}
	    else if ($this->protocol == StorageProfile::PLAY_FORMAT_RTMP )
		{
		    $expiryWindow = isset($this->params['rtmp_auth_window'])     ? $this->params['rtmp_auth_window']     : self::DEFAULT_EXPIRY_WINDOW_SECONDS;
		    $apiKey       = isset($this->params['rtmp_auth_api_key'])    ? $this->params['rtmp_auth_api_key']    : null;
		    $accountId    = isset($this->params['rtmp_auth_account_id']) ? $this->params['rtmp_auth_account_id'] : null;    	
		}
		
		return array($expiryWindow, $apiKey, $accountId);
	}
	
	
    /**
     * 
     * @return an array of parameters sent by the player organized for uplyink
     */
	protected function getPlaybackParamsArrays()
	{
	    $playbackContext = urldecode($this->getPlaybackContext());
	    $playbackContext = trim($playbackContext, '&=');
	    $playbackContext = explode('&', $playbackContext);
	    
		$playbackParams = array();
		
		foreach ($playbackContext as $keyValue)
		{
		    $keyEnd = strpos($keyValue, '=');
		    if ($keyEnd !== false) {
		        $key   = substr($keyValue, 0, $keyEnd);
		        $value = substr($keyValue, $keyEnd+1);
		        
		        if (strpos($key, 'ad.') === 0) {
		            $playbackParams['adParams'][$key] = $value;  
		        }
		        else if (strpos($key, 'cb.') === 0) {
		            $playbackParams['cbParams'][$key] = $value;  
		        }
		        else {
		            $playbackParams[$key] = $value;
		        }
		    }
		}
		
		return $playbackParams;
	}
	
	
	/**
	 * @return an array of parameters to use for the uplyink query string
	 * @param FileSync $fileSync
	 * @param int $expiryWindow
	 * @param string $accountId
	 * @param array $playbackParams
	 * @param boolean $isIosDevice
	 */
	protected function getQueryStringParams($fileSync, $expiryWindow, $accountId, $playbackParams, $isIosDevice)
	{
	    $queryParams = array();
	    
	    // add required query string parameters
		
		$queryParams['exp'] = time() + $expiryWindow; // token expiry time
		$queryParams['ct']  = isset($playbackParams['ct']) ?  $playbackParams['ct'] : null; // content type
		if (is_null($queryParams['ct'])) {
		    //TODO: error	    
		}
		
		$queryParams['oid'] = $accountId; // account id
		$queryParams['eid'] = isset($playbackParams['eid']) ? $playbackParams['eid'] : $this->getWorkFlowId($fileSync); // workflow id of the entry
		$queryParams['iph'] = hash('sha256', kCurrentContext::$user_ip); // user's ip address hashed by sha256
	    
		if ($isIosDevice) {
		    $queryParams['ak']  = isset($playbackParams['ak']) ?  $playbackParams['ak'] : self::DEFAULT_IOS_APPLICATION_KEY; // application key
		}
		
		// add optional query string parameters
						
		if (isset($playbackParams['rays'])) {
		    $queryParams['rays'] = $playbackParams['rays']; // rays bitrate restriction
		}		
		
		$userId = isset($playbackParams['euid']) ? $playbackParams['euid'] : null; //TODO: decide about user id
	    if (!is_null($userId)) {
	        $queryParams['euid'] = $userId; // external user id
		}
		
	    if (isset($playbackParams['delay'])) {
	        $queryParams['delay'] = $playbackParams['delay']; // time delay 
		}
		
	    if (isset($playbackParams['ad'])) {
	        $queryParams['ad'] = $playbackParams['ad']; // ad server definition name 
		    foreach ($queryParams['adParams'] as $adKey => $adValue)
		    {
		        $queryParams[$adKey] = $playbackParams[$adValue];
		    }
		}
		
	    if (isset($playbackParams['cb'])) {
	        $queryParams['cb'] = $playbackParams['cb']; // call-back parameter 
	        foreach ($queryParams['cbParams'] as $cbKey => $cbValue)
		    {
		        $queryParams[$cbKey] = $playbackParams[$cbValue];
		    }
		}	
	    
	    return $queryParams;
	}
	
	
	/**
	 * @return the given parameters as a query string
	 * @param array $queryParams
	 */
	protected function getQueryString($queryParams)
	{
	    $queryString = '';
		foreach ($queryParams as $key => $value)
		{
		    $queryString .= '&'.$key.'='.$value;
		}
		$queryString = trim($queryString, '&');
		return $queryString;
	}
	

	/**
	 * @return workFlowId from the related entry's referenceId field
	 * @param FileSync $fileSync
	 */
	protected function getWorkFlowId(FileSync $fileSync)
	{
	    //TODO: implement - get entry's reference id
	    
	}
    
}
