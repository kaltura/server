<?php
class KalturaFrontController 
{
    private static $instance;
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
        
        $this->service = isset($this->params["service"]) ? $this->params["service"] : null;
		$this->action = isset($this->params["action"]) ? $this->params["action"] : null;
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
	
	public function run()
	{
	    $this->start = microtime(true);
	    
		ob_start();
		error_reporting(E_STRICT & E_ALL);
		error_reporting(E_STRICT | E_ALL);
		
		set_error_handler(array(&$this, "errorHandler"));
		set_exception_handler(array(&$this, "exceptionHandler"));
		
		if ($this->service == "multirequest")
		{
		    set_exception_handler(null);
		    $result = $this->handleMultiRequest();
		}
		else
		{
			try
			{
		    	$result = $this->dispatcher->dispatch($this->service, $this->action, $this->params);
			}
			catch(Exception $ex)
			{
				$result = $this->getExceptionObject($ex);
			}
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
	        
	        
	        try 
	        {  
	        	// cached parameters should be different when the request is part of a multirequest
	        	// as part of multirequest - the cached data is a serialized php object
	        	// when not part of multirequest - the cached data is the actual response
	        	$currentParams['multirequest'] = true;
	        	unset($currentParams['format']);
	        	
	        	$cache = new KalturaResponseCacher($currentParams);
	        	if(!isset($currentParams['ks']) && kCurrentContext::$ks) {
	        		$cache->setKS(kCurrentContext::$ks);
	        	}
	        		
				$cachedResult = $cache->checkCache();
				if ($cachedResult)
				{
					$currentResult = unserialize($cachedResult);
				}
				else
				{		
	        		$currentResult = $this->dispatcher->dispatch($currentService, $currentAction, $currentParams);
	        		// store serialized resposne in cache
	        		$cache->storeCache(serialize($currentResult));
				}	
	        	
	        }
	        catch(Exception $ex)
	        {
	            $currentResult = $this->getExceptionObject($ex);
	        }
	        
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
		else
		{ 
		    KalturaLog::crit($ex);
			$object = new KalturaAPIException(KalturaErrors::INTERNAL_SERVERL_ERROR);
		}
		
		return $object;
	}
	
	public function serializeResponse($object, $ignoreNull = false)
	{
		$start = microtime(true);
		KalturaLog::debug("Serialize start");
		$format = isset($this->params["format"]) ? $this->params["format"] : KalturaResponseType::RESPONSE_TYPE_XML;
		
		switch($format)
		{
			case KalturaResponseType::RESPONSE_TYPE_XML:
				header("Content-Type: text/xml");
				$serializer = new KalturaXmlSerializer($ignoreNull);
				$serializer->serialize($object);
				
				echo '<?xml version="1.0" encoding="utf-8"?>';
				echo "<xml>";
					echo "<result>";
						echo $serializer->getSerializedData();
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
				header("Content-Type: application/json");
				$serializer = new KalturaJsonSerializer($ignoreNull);
				$serializer->serialize($object);
				echo $serializer->getSerializedData();
				break;
			    
			case KalturaResponseType::RESPONSE_TYPE_JSONP:
				$callback = isset($_GET["callback"]) ? $_GET["callback"] : null;
				if (is_null($callback))
					die("Expecting \"callback\" parameter for jsonp format");
				header("Content-Type: application/javascript");
				$serializer = new KalturaJsonSerializer($ignoreNull);
				$serializer->serialize($object);
				$response = array();
				echo $callback, "(", $serializer->getSerializedData(), ");";
				break;
		}
		
		KalturaLog::debug("Serialize took - " . (microtime(true) - $start));
	}
}