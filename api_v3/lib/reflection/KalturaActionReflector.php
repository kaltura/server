<?php
/**
 * Class is used for reflecting actions in a service class whether or not it is currently generated into the 
 * KalturaServiceMap.cache, so long as the service class can be found. Internal use only.
 * 
 * @package api
 * @subpackage v3
 */
class KalturaActionReflector extends KalturaReflector
{
	/**
	 * @var string
	 */
	protected $_actionMethodName;
	
	/**
	 * @var string
	 */
	protected $_actionId;
	
	/**
	 * @var string
	 */
	protected $_actionClass;
	
	/**
	 * @var string
	 */
	protected $_serviceId;
	
	/**
	 * @var string
	 */
	protected $_actionServiceId;
	
	/**
	 * @var string
	 */
	protected $_actionName;
	
	/**
	 * @var KalturaDocCommentParser
	 */
	protected $_actionInfo;
	
	/**
	 * @var KalturaDocCommentParser
	 */
	protected $_actionClassInfo;
	
	/**
	 * @var KalturaBaseService
	 */
	protected $_actionClassInstance;
	
	
	/**
	 * @var array
	 */
	protected $_actionParams;
	
	protected $cache = null;
	
	/**
	 * 
	 * @param string $serviceId
	 * @param array $serviceCallback
	 */
	public function __construct($serviceId, $actionId, $serviceCallback)
	{
		list($this->_actionClass, $this->_actionMethodName, $this->_actionServiceId, $this->_actionName) = array_values($serviceCallback);
		
		$this->_serviceId = $serviceId;
		$this->_actionId = $actionId;
		$this->cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_APC_LCAL);
		
		$fetchFromAPCSuccess = null;
		if($this->cache)
		{
			$actionFromCache = $this->cache->get("{$this->_serviceId}_{$this->_actionId}");
			if($actionFromCache && $actionFromCache[KalturaServicesMap::SERVICES_MAP_MODIFICATION_TIME] == KalturaServicesMap::getServiceMapModificationTime())
			{
				$this->_actionInfo = $actionFromCache["actionInfo"];
				$this->_actionParams = $actionFromCache["actionParams"];
				$this->_actionClassInfo = $actionFromCache["actionClassInfo"];
			}
			else
			{
				$this->cacheReflectionValues();
			}
		}
	}
	
	/**
	 * Function returns the parsed doccomment of the action method.
	 * @return KalturaDocCommentParser
	 */
	public function getActionInfo ( )
	{
		if (is_null($this->_actionInfo))
		{
			$reflectionClass = new ReflectionClass($this->_actionClass);
			$reflectionMethod = $reflectionClass->getMethod($this->_actionMethodName);
			$this->_actionInfo = new KalturaDocCommentParser($reflectionMethod->getDocComment());
		}
		return $this->_actionInfo;
	}
	
	/**
	 * Action returns array of the parameters the action method expects
	 * @return array<KalturaParamInfo>
	 */
	public function getActionParams ( )
	{
		if (is_null($this->_actionParams))
		{
			// reflect the service 
			$reflectionClass = new ReflectionClass($this->_actionClass);
			$reflectionMethod = $reflectionClass->getMethod($this->_actionMethodName);
			
			$docComment = $reflectionMethod->getDocComment();
			$reflectionParams = $reflectionMethod->getParameters();
			$this->_actionParams = array();
			foreach($reflectionParams as $reflectionParam)
			{
				$name = $reflectionParam->getName();
				if (in_array($name, $this->_reservedKeys))
					throw new Exception("Param [$name] in action [$this->_actionMethodName] is a reserved key");
					
				$parsedDocComment = new KalturaDocCommentParser( $docComment, array(
					KalturaDocCommentParser::DOCCOMMENT_REPLACENET_PARAM_NAME => $name , ) );
				$paramClass = $reflectionParam->getClass(); // type hinting for objects
				if ($paramClass)
				{
					$type = $paramClass->getName();
				}
				else //
				{
					$result = null;
					if ($parsedDocComment->param)
						$type = $parsedDocComment->param;
					else 
					{
						throw new Exception("Type not found in doc comment for param [".$name."] in action [".$this->_actionMethodName."] in service [".$this->_serviceId."]");
					}
				}
				
				$paramInfo = new KalturaParamInfo($type, $name);
				$paramInfo->setDescription($parsedDocComment->paramDescription);
				
				if ($reflectionParam->isOptional()) // for normal parameters
				{
					$paramInfo->setDefaultValue($reflectionParam->getDefaultValue());
					$paramInfo->setOptional(true);
				}
				else if ($reflectionParam->getClass() && $reflectionParam->allowsNull()) // for object parameter
				{
					$paramInfo->setOptional(true);
				}
				
				if(array_key_exists($name, $parsedDocComment->validateConstraints))
					$paramInfo->setConstraints($parsedDocComment->validateConstraints[$name]);

				if(in_array($name, $parsedDocComment->disableRelativeTimeParams, true))
					$paramInfo->setDisableRelativeTime(true);
				
				$this->_actionParams[$name] = $paramInfo;
			}
		}
		
		return $this->_actionParams;
	}
	
	/**
	 * @param string $actionName
	 * @return KalturaParamInfo
	 */
	public function getActionOutputType()
	{
		// reflect the service
		$reflectionClass = new ReflectionClass($this->_actionClass);
		$reflectionMethod = $reflectionClass->getMethod($this->_actionMethodName);
		
		$docComment = $reflectionMethod->getDocComment();
		$parsedDocComment = new KalturaDocCommentParser($docComment);
		if ($parsedDocComment->returnType)
			return new KalturaParamInfo($parsedDocComment->returnType, "output");
		
		return null;
	}
	/**
	 * @return the $_serviceId
	 */
	public function getServiceId ()
	{
		return $this->_serviceId;
	}
	/**
	 * @return KalturaDocCommentParser
	 */
	public function getActionClassInfo ()
	{
		if (is_null($this->_actionClassInfo))
		{
			$reflectionClass = new ReflectionClass($this->_actionClass);
			$this->_actionClassInfo = new KalturaDocCommentParser($reflectionClass->getDocComment());
		}
		return $this->_actionClassInfo;
	}
	
	/**
	 * @return string
	 */
	public function getActionId ()
	{
		return $this->_actionId;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getServiceInstance()
	{
		if ( ! $this->_actionClassInstance ) 
		{
			 $this->_actionClassInstance = new $this->_actionClass();
		}
		
		return $this->_actionClassInstance;
	}
	
	public function invoke( $arguments )
	{
		$instance = $this->getServiceInstance();
		return call_user_func_array(array($instance, $this->_actionMethodName), $arguments);
	}
	/**
	 * @return the $_actionName
	 */
	public function getActionName ()
	{
		return $this->_actionName;
	}
	
	/**
	 * Transparently call the initService() of the real service class.
	 */
	public function initService(KalturaDetachedResponseProfile $responseProfile = null)
	{
		$instance = $this->getServiceInstance();
		
		if($responseProfile)
		{
			$instance->setResponseProfile($responseProfile);
		}
		
		$instance->initService($this->_actionServiceId, $this->getActionClassInfo()->serviceName, $this->getActionInfo()->action);
	}
	/**
	 * @return string
	 */
	public function getActionServiceId ()
	{
		return $this->_actionServiceId;
	}

	/**
	 * Save the following attributes into cache
	 * actionInfo, actionParams, actionClassInfo
	 */
	protected function cacheReflectionValues()
	{
		if(!$this->cache)
			return;
			
		$servicesMapLastModTime = KalturaServicesMap::getServiceMapModificationTime();
		
		$cacheValue = array(
			KalturaServicesMap::SERVICES_MAP_MODIFICATION_TIME => $servicesMapLastModTime, 
			"actionInfo" => $this->getActionInfo(), 
			"actionParams" => $this->getActionParams(),
			"actionClassInfo" => $this->getActionClassInfo(),
		);
		
		$this->cache->set("{$this->_serviceId}_{$this->_actionId}", $cacheValue);
	}


}
