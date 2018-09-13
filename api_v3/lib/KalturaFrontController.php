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
	private $serializer;
	
	private function __construct()
	{
		$this->dispatcher = KalturaDispatcher::getInstance();
		
		$this->params = requestUtils::getRequestParams();
		
		$this->service = isset($this->params["service"]) ? (string)$this->params["service"] : null;
		$this->action = isset($this->params["action"]) ? (string)$this->params["action"] : null;
		
		kCurrentContext::$serializeCallback = array($this, 'serialize');
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
			'clientTag' => '"' . (isset($params['clientTag']) ? $params['clientTag'] : null) . '"',
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
		try {	
			$this->setSerializerByFormat();
		}
		catch (Exception $e) {
			return new kRendererDieError($e->getCode(), $e->getMessage());	
		}
		
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
				$result = $this->getExceptionObject($ex, $this->service, $this->action);
			}
			catch (Error $ex) 
			{
				$success = false;
				$errorCode = $ex->getCode();
				$result = $this->getExceptionObject($ex, $this->service, $this->action);
			}
			
			$this->onRequestEnd($success, $errorCode);
		}

		ob_end_clean();
		
		$this->end = microtime(true);
		
		return $this->serializeResponse($result);
	}
	
	public function getMultiRequestResultsPaths($params)
	{
		$paths = array();
		foreach($params as $key => $value)
		{
			if(is_array($value))
			{
				$parsedPaths = $this->getMultiRequestResultsPaths($value);
				if(count($parsedPaths))
				{
					$paths[$key] = $parsedPaths;
				}
			}
			elseif (preg_match('/\{[0-9]+:result:?(.*)?\}/', $value, $matches))
			{
				$path = $matches[0];
				$paths[$key] = $path;
			}
		}
		return $paths;
	}
	
	public function replaceMultiRequestResults($index, array $keys, $params, $result)
	{
		foreach($keys as $key => $path)
		{
			if(isset($params[$key]))
			{
				if(is_array($path))
				{
					$params[$key] = $this->replaceMultiRequestResults($index, $path, $params[$key], $result);
				}
				elseif (preg_match('/^\{([0-9]+):result:?(.*)?\}$/', $path, $matches))
				{
					if(intval($matches[1]) == $index)
					{
						$attributePath = explode(':', $matches[2]);
						$valueFromObject = $this->getValueFromObject($result, $attributePath);
						if(!$valueFromObject)
							KalturaLog::debug("replaceMultiRequestResults: Empty value returned from object");
							
						$params[$key] = str_replace($path, $valueFromObject, $params[$key]);
					}
				}
			}
		}
		
		return $params;
	}
	
	public function handleMultiRequest()
	{
		// arrange the parameters by request index
		$commonParams = array();
		$listOfRequests = array();
		$requestStartIndex = 1;
		$requestEndIndex = 1;
		$ksArray = array();

		foreach ($this->params as $paramName => $paramValue)
		{
			if(is_numeric($paramName))
			{
				$paramName = intval($paramName);
				$requestStartIndex = min($requestStartIndex, $paramName);
				$requestEndIndex = max($requestEndIndex, $paramName);
				$listOfRequests[$paramName] = $paramValue;
				if (isset($paramValue['ks'])) {
					$ksArray[$paramValue['ks']] = true;
				}

				continue;
			}
			
			$explodedName = explode(':', $paramName, 2);
			if (count($explodedName) <= 1 || !is_numeric($explodedName[0]))
			{
				$commonParams[$paramName] = $paramValue;
			}
			else
			{
				$requestIndex = (int)$explodedName[0];
				$requestStartIndex = min($requestStartIndex, $requestIndex);
				$requestEndIndex = max($requestEndIndex, $requestIndex);
				$paramName = $explodedName[1];
				if (!array_key_exists($requestIndex, $listOfRequests))
				{
					$listOfRequests[$requestIndex] = array();
				}
				$listOfRequests[$requestIndex][$paramName] = $paramValue;
			}

			if ($paramName == 'ks') {
				$ksArray[$paramValue] = true;
			}
		}

		//enable multi deferred events only if all ks's are the same
		if ( count($ksArray)==1 ) {
			kEventsManager::enableMultiDeferredEvents(true);
		} else {
			kEventsManager::enableMultiDeferredEvents(false);
		}
		
		$multiRequestResultsPaths = $this->getMultiRequestResultsPaths($listOfRequests);

		// process the requests
		$results = array();
		kCurrentContext::$multiRequest_index = 0;

		for($i = $requestStartIndex; $i <= $requestEndIndex; $i++)
		{
			$currentParams = $listOfRequests[$i];  
			
			if (!isset($currentParams["service"]) || !isset($currentParams["action"]))
				break;

			kCurrentContext::$multiRequest_index++;
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
			
			// cached parameters should be different when the request is part of a multirequest
			// as part of multirequest - the cached data is a serialized php object
			// when not part of multirequest - the cached data is the actual response
			$currentParams['multirequest'] = true;
			if (is_array($currentParams))
			{
				unset($currentParams['format']);
			}
			else
			{
				throw new KalturaAPIException(APIErrors::INTERNAL_SERVERL_ERROR, "Malformed request");
			}
							
			$cache = new KalturaResponseCacher($currentParams);
			
			$success = true;
			$errorCode = null;
			$this->onRequestStart($currentService, $currentAction, $currentParams, kCurrentContext::$multiRequest_index, true);
			$cachedResult = $cache->checkCache('X-Kaltura-Part-Of-MultiRequest');
			if ($cachedResult)
			{
				$currentResult = unserialize($cachedResult);
			}
			else
			{
				if (kCurrentContext::$multiRequest_index != 1)
				{
					kMemoryManager::clearMemory();
					KalturaCriterion::clearTags();
				}
							
				try
				{
					if(isset($listOfRequests[$i]['error']))
						throw $listOfRequests[$i]['error'];
					
					$currentResult = $this->dispatcher->dispatch($currentService, $currentAction, $currentParams);
				}
				catch(Exception $ex)
				{
					$success = false;
					$errorCode = $ex->getCode();
					$currentResult = $this->getExceptionObject($ex, $currentService, $currentAction);
				}
				catch (Error $ex)
				{
					$success = false;
					$errorCode = $ex->getCode();
					$currentResult = $this->getExceptionObject($ex, $currentService, $currentAction);
				}
				$cache->storeCache($currentResult, array(), true);
			}
			$this->onRequestEnd($success, $errorCode, kCurrentContext::$multiRequest_index);
			
			for($nextMultiRequestIndex = ($i + 1); $nextMultiRequestIndex <= count($listOfRequests); $nextMultiRequestIndex++)
			{
				if(isset($multiRequestResultsPaths[$nextMultiRequestIndex]))
				{
					try 
					{
						$listOfRequests[$nextMultiRequestIndex] = $this->replaceMultiRequestResults(kCurrentContext::$multiRequest_index, $multiRequestResultsPaths[$nextMultiRequestIndex], $listOfRequests[$nextMultiRequestIndex], $currentResult);
					}
					catch(Exception $ex)
					{
						$listOfRequests[$nextMultiRequestIndex]['error'] = $ex;
					}
				}
			}
			
			$results[kCurrentContext::$multiRequest_index] = $this->serializer->serialize($currentResult);
			
			// in case a serve action is included in a multirequest, return only the result of the serve action
			// in order to avoid serializing the kRendererBase object and returning the internal server paths to the client
			if ($currentResult instanceof kRendererBase)
				return $currentResult;
		}

		kEventsManager::flushEvents(true);
		return $results;
	}
	
	private function getValueFromObject($object, $path)
	{
		$currentProperty = array_shift($path);
		if (is_null($currentProperty) || !strlen($currentProperty))
		{
			if (!is_object($object))
			{
				return $object;
			}
			
			return null;
		}
		
		if (is_array($object) && isset($object[$currentProperty]))
		{
			return $this->getValueFromObject($object[$currentProperty], $path);
		}
		
		if (property_exists($object, $currentProperty))
		{
			return $this->getValueFromObject($object->$currentProperty, $path);
		}
		
		if ($object instanceof KalturaTypedArray && $object->offsetExists($currentProperty))
		{
			return $this->getValueFromObject($object->offsetGet($currentProperty), $path);
		}
		
		return null;
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
			case E_DEPRECATED:
				KalturaLog::log(sprintf($errorFormat, $errFile, $errLine, $errStr), KalturaLog::NOTICE);
				break;
			default: // throw it as an exception
				throw new ErrorException($errStr, 0, $errNo, $errFile, $errLine);
		}
	}
	
	public function getExceptionObject($ex, $service, $action)
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
				case kCoreException::USER_BLOCKED:
					$object = new KalturaAPIException(KalturaErrors::USER_BLOCKED);
					break;
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
					$object = new KalturaAPIException(KalturaErrors::MAX_CATEGORIES_FOR_ENTRY_REACHED, $ex->getData());
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

				case kCoreException::ID_NOT_FOUND:
					$object = new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $ex->getData());
					break;
				case kCoreException::FILE_PENDING:
					$object = new KalturaAPIException(KalturaErrors::FILE_PENDING);
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
		
		return $this->handleErrorMapping($object, $service, $action);
	}
	
	
	protected function setSerializer($format, $ignoreNull = true)
	{
		// Return a serializer according to the given format
		switch ($format)
		{
			case KalturaResponseType::RESPONSE_TYPE_XML:
				return new KalturaXmlSerializer($ignoreNull);
		
			case KalturaResponseType::RESPONSE_TYPE_PHP:
				return new KalturaPhpSerializer();
		
			case KalturaResponseType::RESPONSE_TYPE_JSON:
				return new KalturaJsonSerializer();
					
			case KalturaResponseType::RESPONSE_TYPE_JSONP:
				return new KalturaJsonProcSerializer();
				 
			default:
				return KalturaPluginManager::loadObject('KalturaSerializer', $format);
		}		
	}
	
	public function setSerializerByFormat()
	{
		if (isset($this->params["ignoreNull"]))
		{
			$ignoreNull = ($this->params["ignoreNull"] === "1" || $this->params["ignoreNull"] === true) ? true : false;
		}
		else
		{
			$ignoreNull = false;
		}
		
		// Determine the output format (or default to XML)
		$format = isset($this->params["format"]) ? $this->params["format"] : KalturaResponseType::RESPONSE_TYPE_XML;
		
		// Create a serializer according to the given format
		$serializer = $this->setSerializer($format, $ignoreNull);
		if(empty($serializer))
			throw new KalturaAPIException(APIErrors::UNKNOWN_RESPONSE_FORMAT, $format);
		
		$this->serializer = $serializer;
	}
	
	public function serializeResponse($object)
	{
		if ($object instanceof kRendererBase)
		{
			return $object;
		}
		
		$start = microtime(true);
		KalturaLog::debug("Serialize start");

		// Set HTTP headers
		if(isset($this->params['content-type']))
		{
			header('Content-Type: ' . $this->params['content-type']);
		}
		else
		{
			$this->serializer->setHttpHeaders();
		}

		// Check if this is multi request if yes than object are already serialized so we will skip otherwise serialize the object
		if ($this->service != "multirequest")
			$serializedObject = $this->serializer->serialize($object);
		else
			$serializedObject = $this->handleSerializedObjectArray($object);
			
		// Post processing (handle special cases)
		$result = $this->serializer->getHeader() . $serializedObject . $this->serializer->getFooter($this->end - $this->start);
		
		KalturaLog::debug("Serialize took - " . (microtime(true) - $start));
		return $result;
	}
	
	public function handleSerializedObjectArray($objects)
	{
		$objectsCount = count($objects);
		$serializedObject = '';
		$serializedObject .= $this->serializer->getMulitRequestHeader($objectsCount);
		for($i = 1 ; $i <= $objectsCount; $i++)
		{
			$serializedObject .= $this->serializer->getItemHeader($i-1);
			$serializedObject .= $objects[$i];
			//check if item is the last one to avoid putting footer chars in json and jsonp serializers
			$lastItem = ($i == $objectsCount);
			$serializedObject .= $this->serializer->getItemFooter($lastItem);
		}
		$serializedObject .= $this->serializer->getMulitRequestFooter();
		
		return $serializedObject;
	}

	protected function handleErrorMapping(KalturaAPIException $apiException, $service, $action)
	{
		if (!kConf::hasParam('api_strict_error_map'))
		{
			KalturaLog::err('api_strict_error_map was not found in kConf and is mandatory!');
			return new KalturaAPIException(KalturaErrors::INTERNAL_SERVERL_ERROR);
		}

		$map = kConf::get('api_strict_error_map');
		if (!is_array($map))
			return $apiException;

		$mapKey = strtolower($service).'_'.strtolower($action);
		if (!isset($map[$mapKey]))
			return $apiException;

		$mapParams = $map[$mapKey];
		$defaultError = isset($mapParams['defaultError']) ? $mapParams['defaultError'] : null;
		$defaultNull = isset($mapParams['defaultNull']) ? $mapParams['defaultNull'] : null;
		$whiteListedErrors = isset($mapParams['whitelisted']) ? $mapParams['whitelisted'] : array();
		if (!is_array($whiteListedErrors))
			$whiteListedErrors = array();

		if (array_search($apiException->getCode(), $whiteListedErrors, true) !== false)
		{
			KalturaLog::debug('Returning white-listed error: '.$apiException->getCode());
			return $apiException;
		}

		// finally, replace the error or return null as default
		if ($defaultNull)
		{
			KalturaLog::debug('Replacing error code "' . $apiException->getCode() . '" with null result');
			return null;
		}
		else
		{
			$reflectionException = new ReflectionClass("KalturaAPIException");
			$errorStr = constant($defaultError);
			$args = array_merge(array($errorStr), $apiException->getArgs());
			/** @var KalturaAPIException $replacedException */
			$replacedException = $reflectionException->newInstanceArgs($args);
			KalturaLog::debug('Replacing error code "' . $apiException->getCode() . '" with error code "' . $replacedException->getCode() . '"');
			return $replacedException;
		}
	}

	public function serialize($object, $className, $serializerType, IResponseProfile $coreResponseProfile = null)
	{
		if (!class_exists($className)) {
			KalturaLog::err("Class [$className] was not found!");
			return null;
		}
		
		$apiObject = null;
		$responseProfile = null;
		
		if($coreResponseProfile)
		{
			$responseProfile = KalturaBaseResponseProfile::getInstance($coreResponseProfile);
		}
			
		// if KalturaBaseEntry, KalturaCuePoint, KalturaAsset
		if (is_subclass_of($className, 'IApiObjectFactory')) 
		{
			$apiObject = $className::getInstance($object, $responseProfile);
		}		

		// if KalturaObject
		elseif (is_subclass_of($className, 'IApiObject')) 
		{
			$apiObject = new $className();
			$apiObject->fromObject($object, $responseProfile);
		}
		
		// return serialized object according to required type
		$serializer = $this->setSerializer($serializerType);
		return $serializer->serialize($apiObject);
	}
}
