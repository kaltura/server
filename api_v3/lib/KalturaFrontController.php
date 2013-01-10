<?php
/**
 * @package api
 * @subpackage v3
 */
class KalturaFrontController 
{
    private static $instance;
	private $requestStart = null;
	private $start = null;
	private $end = null;
	private $params = array();
	private $service = "";
	private $action = "";
	private $disptacher = null;
	
    private function KalturaFrontController() 
    {
        $this->dispatcher = KalturaDispatcher::getInstance();
        
        $this->params = requestUtils::getRequestParams();
        
        $this->service = isset($this->params["service"]) ? (string)$this->params["service"] : null;
		$this->action = isset($this->params["action"]) ? (string)$this->params["action"] : null;
    }
        
	/**
	 * Return a singleton KalturaFrontController instance
	 *
	 * @return KalturaFrontController
	 */
	public static function getInstance()
	{
		if (!self::$instance)
		{
			self::$instance = new self();
		}
		    
		return self::$instance;
	}
	
	public function onRequestStart($service, $action, array $params, $requestIndex = 0, $isInMultiRequest = false)
	{
		$this->requestStart = microtime(true);
		KalturaLog::analytics(array(
			'request_start',
			'pid' => getmypid(),
			'agent' => '"' . (isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : null) . '"',
			'host' => (isset($_SERVER["HOSTNAME"]) ? $_SERVER["HOSTNAME"] : gethostname()),
			'clientTag' => '"' . (isset($_REQUEST['clientTag']) ? $_REQUEST['clientTag'] : null) . '"',
			'time' => $this->requestStart,
			'service' => $service, 
			'action' => $action, 
			'requestIndex' => $requestIndex,
			'isInMultiRequest' => intval($isInMultiRequest)
		));
	}
	
	
	public function onRequestEnd($success = true, $errorCode = null, $requestIndex = 0)
	{
		$duration = microtime(true) - $this->requestStart;
		
		KalturaLog::analytics(array(
			'request_end',
			'partnerId' => kCurrentContext::$partner_id,
			'masterPartnerId' => kCurrentContext::$master_partner_id,
			'ks' => kCurrentContext::$ks,
			'isAdmin' => intval(kCurrentContext::$is_admin_session),
			'kuserId' => '"' . str_replace('"', '\\"', (kCurrentContext::$uid ? kCurrentContext::$uid : kCurrentContext::$ks_uid)) . '"',
			'duration' => $duration,
			'success' => intval($success),
			'errorCode' => $errorCode,
			'requestIndex' => $requestIndex,
		));
	}
	
	public function run()
	{
	    $this->start = microtime(true);
	    
		ob_start();
		error_reporting(E_STRICT & E_ALL);
		error_reporting(E_STRICT | E_ALL);
		
		set_error_handler(array(&$this, "errorHandler"));
		set_exception_handler(array(&$this, "exceptionHandler"));
		
		KalturaLog::debug("Params [" . print_r($this->params, true) . "]");
		if ($this->service == "multirequest")
		{
		    set_exception_handler(null);
		    $result = $this->handleMultiRequest();
		}
		else
		{
			$success = true;
			$errorCode = null;
			$this->onRequestStart($this->service, $this->action, $this->params);
			try
			{
		    	$result = $this->dispatcher->dispatch($this->service, $this->action, $this->params);
			}
			catch(Exception $ex)
			{
				$success = false;
				$errorCode = $ex->getCode();				
				$result = $this->getExceptionObject($ex);
			}
	        $this->onRequestEnd($success, $errorCode);
		}
		
		if (isset($_REQUEST["ignoreNull"]))
			$ignoreNull = ($_REQUEST["ignoreNull"] === "1") ? true : false;
		else
			$ignoreNull = false;

		ob_end_clean();
		
		$this->end = microtime(true);
		
		$this->serializeResponse($result, $ignoreNull);
	}
	
	public function handleMultiRequest() 
	{
	    $listOfRequests = array(); 
	    $results = array();
	    $found = true;
	    $i = 1;
	    while ($found)
	    {
	        $currentService = isset($this->params[$i.":service"]) ? $this->params[$i.":service"] : null;
	        $currentAction = isset($this->params[$i.":action"]) ? $this->params[$i.":action"] : null;
	        $found = ($currentAction && $currentService);
	        if ($found)
	        {
	            $listOfRequests[$i]["service"] = $currentService;
	            $listOfRequests[$i]["action"] = $currentAction;
	            
	            // find all the parameters for this request
	            foreach($this->params as $key => $val)
	            {
	                // the key "1:myparam" mean that we should input value of this key to request "1", for param "myparam" 
                    $keyArray = explode(":", $key);
                    if ($keyArray[0] == $i) 
                    {
                        array_shift($keyArray); // remove the request number
                        $requestKey = implode(":",$keyArray);
                        
                        /* remarked by Dor - 13/10/2010
                         * There is no need to remove service and action from the params in case of multirequest
                         * while they are needed in KalturaResponseCacher
                         
                        if (in_array($requestKey, array("service", "action"))) // don't add service name and action name to the params
                            continue;
                        
                        */ 
                              
                        $listOfRequests[$i]["params"][$requestKey] = $val; // store the param                
                    }                                        
	            }
	            
	            // clientTag param might be used in KalturaResponseCacher
	            if (isset($this->params['clientTag']) && !isset($listOfRequests[$i]["params"]['clientTag'])) {
	            	$listOfRequests[$i]["params"]['clientTag'] = $this->params['clientTag'];
	            }
	            
	            // if ks is not set for a specific request, copy the ks from the top params
	            $currentKs = isset($listOfRequests[$i]["params"]["ks"]) ? $listOfRequests[$i]["params"]["ks"] : null;
	            if (!$currentKs)
	            {
	                $mainKs = isset($this->params["ks"]) ? $this->params["ks"] : null;
                    if ($mainKs)
                        $listOfRequests[$i]["params"]["ks"] = $mainKs;
	            }
	            
				$currentPartner = isset($listOfRequests[$i]["params"]["partnerId"]) ? $listOfRequests[$i]["params"]["partnerId"] : null;
	        	if (!$currentPartner)
	            {
	                $mainPartner = isset($this->params["partnerId"]) ? $this->params["partnerId"] : null;
                    if ($mainPartner)
                        $listOfRequests[$i]["params"]["partnerId"] = $mainPartner;
	            }
	            
	            $i++;
	        }
	        else
	        {
	            // will break the loop
	        }
	    }
	    
	    $i = 1;
	    foreach($listOfRequests as $currentRequest)
	    {
	    	kCurrentContext::$multiRequest_index = $i;
	        $currentService = $currentRequest["service"];
	        $currentAction = $currentRequest["action"];
	        $currentParams = $currentRequest["params"];
	        
	        // check if we need to replace params with prev results
	        foreach($currentParams as $key => &$val)
	        {
	        	$matches = array();
		// keywords: multirequest, result, depend, pass
		// figuring out if requested params should be extracted from previous result
		// example: if you want to use KalturaPlaylist->playlistContent result from the first request
		// in your second request, the second request will contain the following value:
		// {1:result:playlistContent}
                if (preg_match('/\{([0-9]*)\:result\:?(.*)?\}/', $val, $matches))
                {
                    $resultIndex = $matches[1];
                    $resultKey = $matches[2];
                    
                    if (count($results) >= $resultIndex) // if the result index is valid
                    {
                        if (strlen(trim($resultKey)) > 0)
                            $resultPathArray = explode(":",$resultKey);
                        else
                            $resultPathArray = array();
                            
                        $val = $this->getValueFromObject($results[$resultIndex], $resultPathArray);
                    }
                }	            
	        }
	        
	        
			// cached parameters should be different when the request is part of a multirequest
			// as part of multirequest - the cached data is a serialized php object
			// when not part of multirequest - the cached data is the actual response
			$currentParams['multirequest'] = true;
			unset($currentParams['format']);
			
			$cache = new KalturaResponseCacher($currentParams);
			if(!isset($currentParams['ks']) && kCurrentContext::$ks) {
				$cache->setKS(kCurrentContext::$ks);
			}
			
			$success = true;
			$errorCode = null;
			$this->onRequestStart($currentService, $currentAction, $currentParams, $i, true);
			$cachedResult = $cache->checkCache('X-Kaltura-Part-Of-MultiRequest');
			if ($cachedResult)
			{
				$currentResult = unserialize($cachedResult);
			}
			else
			{
				if ($i != 1)
				{
					kMemoryManager::clearMemory();
					KalturaCriterion::clearTags();
				}
			
				try 
				{  
					$currentResult = $this->dispatcher->dispatch($currentService, $currentAction, $currentParams);
				}
				catch(Exception $ex)
				{
					$success = false;
					$errorCode = $ex->getCode();
					$currentResult = $this->getExceptionObject($ex);
				}
				$cache->storeCache($currentResult, "", true);
			}
			$this->onRequestEnd($success, $errorCode, $i);
	        
            $results[$i] = $currentResult;	        
	        $i++;
	    }
	    
	    return $results;
	}
	
	private function getValueFromObject($object, $path)
	{
	    if ($path === null || count($path) == 0)
	    {
	        if (!is_object($object))
	            return $object;  
	        else
	            return null;
	    }
	    else
	    {
	        $currentProperty = @$path[0];
	        array_shift($path);
	        if (property_exists($object, $currentProperty))
                return $this->getValueFromObject($object->$currentProperty, $path);
            else if ($object instanceof KalturaTypedArray && $object->offsetExists($currentProperty))
            	return $this->getValueFromObject($object->offsetGet($currentProperty), $path);
            else
                return null;
	    }
	}
	
    public function errorHandler($errNo, $errStr, $errFile, $errLine)
	{
	    
		$errorFormat = "%s line %d - %s";
		switch ($errNo)
		{
			case E_NOTICE:
			case E_STRICT:
			case E_USER_NOTICE:
				KalturaLog::log(sprintf($errorFormat, $errFile, $errLine, $errStr), KalturaLog::NOTICE);
				break;
			case E_USER_WARNING:
			case E_WARNING:
				KalturaLog::log(sprintf($errorFormat, $errFile, $errLine, $errStr), KalturaLog::WARN);
				break;
			default: // throw it as an exception
				throw new ErrorException($errStr, 0, $errNo, $errFile, $errLine); 
		}
	}
	
	public function exceptionHandler($ex)
	{
		$object = $this->getExceptionObject($ex);
		
		$this->end = microtime(true);
		
		$this->serializeResponse($object);
	}
	
	public function getExceptionObject($ex)
	{
		$this->adjustApiCacheForException($ex);
		
	    if ($ex instanceof KalturaAPIException)
		{
		    KalturaLog::err($ex);
			$object = $ex;
		}
		else if ($ex instanceof APIException)  // don't let unwanted exception to be serialized
		{
		    $args = $ex->extra_data;
	        $reflectionException = new ReflectionClass("KalturaAPIException");
	        $ex = $reflectionException->newInstanceArgs($args);
		    KalturaLog::err($ex);
			$object = $ex;
		}
		else if ($ex instanceof kCoreException)
		{
			switch($ex->getCode())
			{	
				case kCoreException::INVALID_KS:
					$object = new KalturaAPIException(KalturaErrors::INVALID_KS, $ex->getData(), ks::INVALID_STR, 'INVALID_STR');
					break;
					
				case kCoreException::MAX_NUMBER_OF_ACCESS_CONTROLS_REACHED:
					$object = new KalturaAPIException(KalturaErrors::MAX_NUMBER_OF_ACCESS_CONTROLS_REACHED, $ex->getData());
					break;
					
				case kCoreException::MAX_CATEGORIES_PER_ENTRY:
					$object = new KalturaAPIException(KalturaErrors::MAX_CATEGORIES_FOR_ENTRY_REACHED, entry::MAX_CATEGORIES_PER_ENTRY);
					break;
				
				case kCoreException::SEARCH_TOO_GENERAL:
					throw new KalturaAPIException(KalturaErrors::SEARCH_TOO_GENERAL);
					break;
					
				case kCoreException::SOURCE_FILE_NOT_FOUND:
					$object = new KalturaAPIException(KalturaErrors::SOURCE_FILE_NOT_FOUND);
					break;
					
				case APIErrors::INVALID_ACTIONS_LIMIT:
					$object = new KalturaAPIException(APIErrors::INVALID_ACTIONS_LIMIT);
					break;
					
				case APIErrors::PRIVILEGE_IP_RESTRICTION:
					$object = new KalturaAPIException(APIErrors::PRIVILEGE_IP_RESTRICTION);
					break;
					
				case APIErrors::INVALID_SET_ROLE:
					$object = new KalturaAPIException(APIErrors::INVALID_SET_ROLE);
					break;
					
				case APIErrors::UNKNOWN_ROLE_ID:
					$object = new KalturaAPIException(APIErrors::UNKNOWN_ROLE_ID);
					break;
					
				case APIErrors::SEARCH_ENGINE_QUERY_FAILED:
					$object = new KalturaAPIException(APIErrors::SEARCH_ENGINE_QUERY_FAILED);
					break;
					
				case kCoreException::FILE_NOT_FOUND:
					$object = new KalturaAPIException(KalturaErrors::FILE_NOT_FOUND);
					break;
					
				default:
		    		KalturaLog::crit($ex);
					$object = new KalturaAPIException(KalturaErrors::INTERNAL_SERVERL_ERROR);
			}
		}
		else if ($ex instanceof PropelException)
		{
		    KalturaLog::alert($ex);
			$object = new KalturaAPIException(KalturaErrors::INTERNAL_DATABASE_ERROR);
		}
		else
		{ 
		    KalturaLog::crit($ex);
			$object = new KalturaAPIException(KalturaErrors::INTERNAL_SERVERL_ERROR);
		}
		
		return $object;
	}
	
	public function adjustApiCacheForException($ex)
	{
		KalturaResponseCacher::setExpiry(120);
		
		$cacheConditionally = false;
		if ($ex instanceof KalturaAPIException && kConf::hasParam("v3cache_conditional_cached_errors"))
		{
			$cacheConditionally = in_array($ex->getCode(), kConf::get("v3cache_conditional_cached_errors"));
		}
		if (!$cacheConditionally)
		{
			KalturaResponseCacher::disableConditionalCache();
		}
	}
	
	public function serializeResponse($object, $ignoreNull = false)
	{
		$start = microtime(true);
		KalturaLog::debug("Serialize start");
		
		KalturaResponseCacher::endCacheIfDisabled();
		
		$format = isset($this->params["format"]) ? $this->params["format"] : KalturaResponseType::RESPONSE_TYPE_XML;
	
		if(isset($this->params['content-type']))
		{
			header('Content-Type: ' . $this->params['content-type']);
		}
		else
		{
			switch($format)
			{
				case KalturaResponseType::RESPONSE_TYPE_XML:
					header("Content-Type: text/xml");
					break;
					
				case KalturaResponseType::RESPONSE_TYPE_JSON:
					header("Content-Type: application/json");
					break;
				    
				case KalturaResponseType::RESPONSE_TYPE_JSONP:
					header("Content-Type: application/javascript");
					break;
			}
		}
		
		switch($format)
		{
			case KalturaResponseType::RESPONSE_TYPE_XML:
				$serializer = new KalturaXmlSerializer($ignoreNull);
				
				echo '<?xml version="1.0" encoding="utf-8"?>';
				echo "<xml>";
					echo "<result>";
						$serializer->serialize($object);
					echo "</result>";
					echo "<executionTime>" . ($this->end - $this->start) ."</executionTime>";
				echo "</xml>";
				break;
				
			case KalturaResponseType::RESPONSE_TYPE_PHP:
				$serializer = new KalturaPhpSerializer($ignoreNull);
				$serializer->serialize($object);
				echo $serializer->getSerializedData();
				break;
				
			case KalturaResponseType::RESPONSE_TYPE_JSON:
				$serializer = new KalturaJsonSerializer($ignoreNull);
				$serializer->serialize($object);
				echo $serializer->getSerializedData();
				break;
			    
			case KalturaResponseType::RESPONSE_TYPE_JSONP:
				$callback = isset($_GET["callback"]) ? $_GET["callback"] : null;
				if (is_null($callback))
					die("Expecting \"callback\" parameter for jsonp format");
				$serializer = new KalturaJsonSerializer($ignoreNull);
				$serializer->serialize($object);
				$response = array();
				echo $callback, "(", $serializer->getSerializedData(), ");";
				break;
		}
		
		KalturaLog::debug("Serialize took - " . (microtime(true) - $start));
	}
}
