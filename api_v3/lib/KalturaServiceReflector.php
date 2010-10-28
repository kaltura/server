<?php
/**
 * A helper class to access service actions, action params and does the real invocation.
 *
 */
class KalturaServiceReflector
{
	/**
	 * @var string
	 */
	private $_serviceId = null;
	
	/**
	 * @var string
	 */
	private $_serviceClass = null;
	
	/**
	 * @var array
	 */
	private $_servicesMap = null;
	
	/**
	 * @var array
	 */
	private $_actions = null;
	
	/**
	 * @var KalturaDocCommentParser
	 */
	private $_docCommentParser = null;
	
	/**
	 * @var KalturaBaseService
	 */
	private $_serviceInstance = null;
	
	/**
	 * @var array
	 */
	private $_reservedKeys = array("service", "action", "format", "ks", "callback");
	
	/**
	 * @param string $service
	 */
	public function KalturaServiceReflector($service)
	{
		$this->_serviceId = strtolower($service);
		$this->_servicesMap = KalturaServicesMap::getMap();
		
		if (!$this->isServiceExists($this->_serviceId))
			throw new Exception("Service [$service] does not exists");
			
		$this->_serviceClass = $this->_servicesMap[$this->_serviceId];
		
		if (!class_exists($this->_serviceClass))
			throw new Exception("Service class [$this->_serviceClass] for service [$service] does not exists");
			
		$reflectionClass = new ReflectionClass($this->_serviceClass);
		$this->_docCommentParser = new KalturaDocCommentParser($reflectionClass->getDocComment()); 
	}
	
	public function getServiceInfo()
	{
	    return $this->_docCommentParser;
	}
	
    public function getServiceId()
	{
		return $this->_serviceId;
	}
	
	public function isDeprecated()
	{
		return $this->_docCommentParser->deprecated;
	}
	
	public function isServerOnly()
	{
		return $this->_docCommentParser->serverOnly;
	}
	
	public function getServiceName()
	{
		return $this->_docCommentParser->serviceName;
	}
	
	public function getServiceDescription()
	{
		return $this->_docCommentParser->description;
	}
	
	public function isServiceExists($serviceId)
	{
		if(array_key_exists($serviceId, $this->_servicesMap))
			return true;
			
		if(strpos($serviceId, '_') <= 0)
			return false;

		$serviceId = strtolower($serviceId);
		list($servicePlugin, $serviceName) = explode('_', $serviceId);
		
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaServicesPlugin');
		if(!isset($pluginInstances[$servicePlugin]))
			return false;
			
		$pluginInstance = $pluginInstances[$servicePlugin];
		$servicesMap = $pluginInstance->getServicesMap();
		foreach($servicesMap as $name => $class)
		{
			if(strtolower($name) == $serviceName)
			{
				$class = $servicesMap[$serviceName];
				KalturaServicesMap::addService($serviceId, $class);
				$this->_servicesMap = KalturaServicesMap::getMap();
				return true;
			}
		}
			
		return false;
	}
	
	public function isActionExists($actionName)
	{
		$actions = $this->getActions();
		$actionId = strtolower($actionName);
		return array_key_exists($actionId, $actions);
	}
	
	public function getActions()
	{
		if ($this->_actions !== null)
			return $this->_actions;
		
		$reflectionClass = new ReflectionClass($this->_serviceClass);
		
		$reflectionMethods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
		
		$actions = array();
		foreach($reflectionMethods as $reflectionMethod)
		{
			$docComment = $reflectionMethod->getDocComment();
			$parsedDocComment = new KalturaDocCommentParser( $docComment );
			if ($parsedDocComment->action)
			{
				$actionName = $parsedDocComment->action;
				$actionId = strtolower($actionName);
				$actions[$actionId] = $reflectionMethod->getName(); // key is the action id (action name lower cased), value is the method name
			}
		}
		
		$this->_actions = $actions;
		
		return $this->_actions;
	}
	
	/**
	 * @param string $actionName
	 * @return KalturaDocCommentParser
	 */
	public function getActionInfo($actionName)
	{
		if (!$this->isActionExists($actionName))
			throw new Exception("Action [$actionName] does not exists for service [$this->_serviceId]");
		
		$actionId = strtolower($actionName);
		$methodName = $this->_actions[$actionId];
		// reflect the service 
		$reflectionClass = new ReflectionClass($this->_serviceClass);
		$reflectionMethod = $reflectionClass->getMethod($methodName);
		
		$docComment = $reflectionMethod->getDocComment();
		$parsedDocComment = new KalturaDocCommentParser( $docComment );
		return $parsedDocComment;
	}
	
	public function getActionParams($actionName)
	{
		if (!$this->isActionExists($actionName))
			throw new Exception("Action [$actionName] does not exists for service [$this->_serviceId]");
			
		$actionId = strtolower($actionName);
		$methodName = $this->_actions[$actionId];
		
		// reflect the service 
		$reflectionClass = new ReflectionClass($this->_serviceClass);
		$reflectionMethod = $reflectionClass->getMethod($methodName);
		
		$docComment = $reflectionMethod->getDocComment();
		$reflectionParams = $reflectionMethod->getParameters();
		$actionParams = array();
		foreach($reflectionParams as $reflectionParam)
		{
			$name = $reflectionParam->getName();
			if (in_array($name, $this->_reservedKeys))
				throw new Exception("Param [$name] in action [$actionName] is a reserved key");
				
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
					throw new Exception("Type not found in doc comment for param [".$name."] in action [".$actionName."] in service [".$this->_serviceId."]");
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
			
			$actionParams[] = $paramInfo;
		}
		
		return $actionParams;
	}
	
	/**
	 * @param unknown_type $actionName
	 * @return KalturaParamInfo
	 */
	public function getActionOutputType($actionName)
	{
		if (!$this->isActionExists($actionName))
			throw new Exception("Action [$actionName] does not exists for service [$this->_serviceId]");

		$actionId = strtolower($actionName);
		$methodName = $this->_actions[$actionId];
		
		// reflect the service
		$reflectionClass = new ReflectionClass($this->_serviceClass);
		$reflectionMethod = $reflectionClass->getMethod($methodName);
		
		$docComment = $reflectionMethod->getDocComment();
		$parsedDocComment = new KalturaDocCommentParser($docComment);
		if ($parsedDocComment->returnType)
			return new KalturaParamInfo($parsedDocComment->returnType, "output");
		
		return null;
	}
	
	public function invoke($actionName, $arguments)
	{
	    $actionId = strtolower($actionName);
		$methodName = $this->_actions[$actionId];
		$instance = $this->getServiceInstance();
		return call_user_func_array(array($instance, $methodName), $arguments);
	}
	
	public function getServiceInstance()
	{
		if ( ! $this->_serviceInstance ) 
		{
			 $this->_serviceInstance = new $this->_serviceClass();
		}
		
		return $this->_serviceInstance;
	}
	
	public function removeAction($actionName)
	{
		$actionId = strtolower($actionName);
		unset($this->_actions[$actionId]);
	}
}