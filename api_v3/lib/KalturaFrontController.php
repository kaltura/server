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
		
		KalturaMonitorClient::monitorApiEnd($errorCode);
	}
	
	public function run()
	{
	    $this->start = microtime(true);
	    
		ob_start();
		error_reporting(E_STRICT & E_ALL);
		error_reporting(E_STRICT | E_ALL);
		
		set_error_handler(array(&$this, "errorHandler"));
		
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
		
		return $this->serializeResponse($result, $ignoreNull);
	}
	
	public function handleMultiRequest()
	{
		// arrange the parameters by request index
		$commonParams = array();
		$listOfRequests = array();
		foreach ($this->params as $paramName => $paramValue)
		{
			$explodedName = explode(':', $paramName, 2);
			if (count($explodedName) <= 1 || !is_numeric($explodedName[0]))
			{
				$commonParams[$paramName] = $paramValue;
				continue;
			}
		
			$requestIndex = (int)$explodedName[0];
			$paramName = $explodedName[1];
			if (!array_key_exists($requestIndex, $listOfRequests))
			{
				$listOfRequests[$requestIndex] = array();
			}
			$listOfRequests[$requestIndex][$paramName] = $paramValue;
		}
			
		// process the requests
		$results = array();
		for ($i = 1; isset($listOfRequests[$i]); $i++)
		{
			$currentParams = $listOfRequests[$i];
			if (!isset($currentParams["service"]) || !isset($currentParams["action"]))
				break;

			kCurrentContext::$multiRequest_index = $i;
			$currentService = $currentParams["service"];
			$currentAction = $currentParams["action"];
		
			// copy derived common params to current params
			if (isset($commonParams['clientTag']) && !isset($currentParams['clientTag']))
			{
				$currentParams['clientTag'] = $commonParams['clientTag'];
			}

			if (isset($commonParams['ks']) && !isset($currentParams['ks']))
			{
				$currentParams['ks'] = $commonParams['ks'];
			}

			if (isset($commonParams['partnerId']) && !isset($currentParams['partnerId']))
			{
				$currentParams['partnerId'] = $commonParams['partnerId'];
			}
					        
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
            
            // in case a serve action is included in a multirequest, return only the result of the serve action
            // in order to avoid serializing the kRendererBase object and returning the internal server paths to the client
            if ($currentResult instanceof kRendererBase)
            	return $currentResult;
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
	
	public function getExceptionObject($ex)
	{
		KalturaResponseCacher::adjustApiCacheForException($ex);
		
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
				case kCoreException::PARTNER_BLOCKED:
					$object = new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN_CONTENT_BLOCKED);
					break;
					
				case kCoreException::INVALID_KS:
					$object = new KalturaAPIException(KalturaErrors::INVALID_KS, $ex->getData(), ks::INVALID_STR, 'INVALID_STR');
					break;
					
				case kCoreException::MAX_NUMBER_OF_ACCESS_CONTROLS_REACHED:
					$object = new KalturaAPIException(KalturaErrors::MAX_NUMBER_OF_ACCESS_CONTROLS_REACHED, $ex->getData());
					break;
					
				case kCoreException::MAX_CATEGORIES_PER_ENTRY:
					$object = new KalturaAPIException(KalturaErrors::MAX_CATEGORIES_FOR_ENTRY_REACHED, entry::MAX_CATEGORIES_PER_ENTRY);
					break;
					
				case kCoreException::MAX_ASSETS_PER_ENTRY:
					$object = new KalturaAPIException(KalturaErrors::MAX_ASSETS_FOR_ENTRY_REACHED, asset::MAX_ASSETS_PER_ENTRY);
					break;
				
				case kCoreException::SEARCH_TOO_GENERAL:
					$object = new KalturaAPIException(KalturaErrors::SEARCH_TOO_GENERAL);
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
					
				case kCoreException::LOCK_TIMED_OUT:
					$object = new KalturaAPIException(KalturaErrors::LOCK_TIMED_OUT);
					break;
					
				case kCoreException::SPHINX_CRITERIA_EXCEEDED_MAX_MATCHES_ALLOWED:
					$object = new KalturaAPIException(KalturaErrors::SPHINX_CRITERIA_EXCEEDED_MAX_MATCHES_ALLOWED);
					break;

				case kCoreException::INVALID_ENTRY_ID:
					$object = new KalturaAPIException(KalturaErrors::INVALID_ENTRY_ID, $ex->getData());
					break;
					
				case kCoreException::MAX_FILE_SYNCS_FOR_OBJECT_PER_DAY_REACHED:
					$object = new KalturaAPIException(KalturaErrors::MAX_FILE_SYNCS_FOR_OBJECT_PER_DAY_REACHED, $ex->getData());
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
	
	
	public function serializeResponse($object, $ignoreNull = false)
	{
		if ($object instanceof kRendererBase)
		{
			return $object;
		}
		
		$start = microtime(true);
		KalturaLog::debug("Serialize start");

		// Determine the output format (or default to XML)
		$format = isset($this->params["format"]) ? $this->params["format"] : KalturaResponseType::RESPONSE_TYPE_XML;
		
		// Create a serializer according to the given format
		switch($format)
		{
			case KalturaResponseType::RESPONSE_TYPE_XML:
				$serializer = new KalturaXmlSerializer($ignoreNull);
				break;
				
			case KalturaResponseType::RESPONSE_TYPE_PHP:
				$serializer = new KalturaPhpSerializer();
				break;
				
			case KalturaResponseType::RESPONSE_TYPE_JSON:
				$serializer = new KalturaJsonSerializer();
				break;
			
			case KalturaResponseType::RESPONSE_TYPE_JSONP:
				$serializer = new KalturaJsonProcSerializer();
				break;
			    
			default:
				$serializer = KalturaPluginManager::loadObject('KalturaSerializer', $format);
				break;
		}

		if ( ! empty( $serializer ) ) // Got a serializer for the given format?
		{
			// Set HTTP headers
			if(isset($this->params['content-type']))
			{
				header('Content-Type: ' . $this->params['content-type']);
			}
			else
			{
				$serializer->setHttpHeaders();
			}

			// Serialize the object
			$serializedObject = $serializer->serialize($object);
			
			// Post processing (handle special cases)
			switch($format)
			{
				case KalturaResponseType::RESPONSE_TYPE_XML:
					$result = 
						'<?xml version="1.0" encoding="utf-8"?>' .
						'<xml>' .
							'<result>' .
								$serializedObject .
							'</result>' .
							'<executionTime>' . ($this->end - $this->start) . '</executionTime>' .
						'</xml>';
					break;

				case KalturaResponseType::RESPONSE_TYPE_JSONP:
					$callback = isset($_GET["callback"]) ? $_GET["callback"] : null;
					if (is_null($callback))
						die("Expecting \"callback\" parameter for jsonp format");
					$response = array();
					$result = $callback . "(" . $serializedObject . ");";
					break;
					
				default:
					$result = $serializedObject;
					break;
			}
		}
		
		KalturaLog::debug("Serialize took - " . (microtime(true) - $start));
		return $result;
	}
}
