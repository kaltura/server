<?php
/**
 * A helper class to access service actions, action params and does the real invocation.
 * 
 * @package api
 * @subpackage v3
 */
class KalturaServiceReflector extends KalturaReflector
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
	 * @var array<KalturaServiceActionItem>
	 */
	private $_servicesMap = null;
	
	/**
	 * @var array
	 */
	private $_actions = array();
	
	/**
	 * @var KalturaDocCommentParser
	 */
	private $_serviceInfo = null;
	
	/**
	 * @var KalturaBaseService
	 */
	private $_serviceInstance = null;
	
	/**
	 * @var string
	 */
	protected $_action;
	
	/**
	 * @param string $service
	 */
	protected function __construct()
	{
		
	}
	/**
	 * Static instantiator - create the reflector with serviceId and optional action name
	 * @param string $service
	 * @param string $action
	 * @return KalturaServiceReflector
	 */
	public static function constructFromServiceId ($service)
	{
	    $newInstance = new KalturaServiceReflector();
	    $newInstance->_serviceId = strtolower($service);
		$newInstance->_servicesMap = KalturaServicesMap::getMap();
		
		if (!$newInstance->isServiceExists($newInstance->_serviceId))
			throw new Exception("Service [$service] does not exists in service list [" . print_r(array_keys($newInstance->_servicesMap), true) . "]");
			
		$serviceActionItem = $newInstance->_servicesMap[$newInstance->_serviceId];
		/* @var $serviceActionItem KalturaServiceActionItem */
		$newInstance->_serviceClass = $serviceActionItem->serviceClass;
		
		if (!class_exists($newInstance->_serviceClass))
			throw new Exception("Service class [$newInstance->_serviceClass] for service [$service] does not exists");
		
		$reflectionClass = new ReflectionClass($newInstance->_serviceClass);
		$newInstance->_serviceInfo = new KalturaDocCommentParser($reflectionClass->getDocComment());
		
		return $newInstance;
	}
	
	/**
	 * 
	 * Static instantiator - create the reflector with service class name
	 * @param string $serviceClass
	 * @return KalturaServiceReflector
	 */
	public static function constructFromClassName ($serviceClass)
	{
	   $newInstance = new KalturaServiceReflector();
	   if ( !class_exists( $serviceClass ) || !in_array("KalturaBaseService", class_parents($serviceClass)))
        {
            throw new Exception("Service class [$serviceClass] does not exists, or is not an instance of KalturaBaseService");
        }
        
        $newInstance->_serviceClass = $serviceClass;
        
        $reflectionClass = new ReflectionClass($serviceClass);
        $newInstance->_serviceInfo = new KalturaDocCommentParser($reflectionClass->getDocComment());
        $newInstance->_serviceId = $newInstance->_serviceInfo->serviceName;
        return $newInstance;
	}
	
	public function getServiceInfo()
	{
	    return $this->_serviceInfo;
	}
	
    public function getServiceId()
	{
		return $this->_serviceId;
	}
	
	public function getPackage()
	{
		return $this->_serviceInfo->package;
	}
	
	
	public function getPluginName()
	{
		if(!is_string($this->_serviceInfo->package) || !strlen($this->_serviceInfo->package))
			return null;
			
		$packages = explode('.', $this->_serviceInfo->package, 2);
		if(count($packages) != 2 || $packages[0] != 'plugins')
			return null;
			
		return $packages[1];
	}
	
	public function isDeprecated()
	{
		return $this->_serviceInfo->deprecated;
	}
	
	public function isServerOnly()
	{
		return $this->_serviceInfo->serverOnly;
	}
	
	public function getServiceName()
	{
		return $this->_serviceInfo->serviceName;
	}
	
	public function getServiceDescription()
	{
		return $this->_serviceInfo->description;
	}
	
	public function isFromPlugin()
	{
		return (strpos($this->_serviceId, '_') > 0);
	}
	
	public function isServiceExists($serviceId)
	{
		if(array_key_exists($serviceId, $this->_servicesMap))
			return true;
			
		if(strpos($serviceId, '_') <= 0)
			return false;

		$serviceId = strtolower($serviceId);
		list($servicePlugin, $serviceName) = explode('_', $serviceId);
		
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaServices');
		if(!isset($pluginInstances[$servicePlugin]))
			return false;
			
		$pluginInstance = $pluginInstances[$servicePlugin];
		$servicesMap = $pluginInstance->getServicesMap();
		foreach($servicesMap as $name => $class)
		{
			if(strtolower($name) == $serviceName)
			{
				$class = $servicesMap[$name];
				KalturaServicesMap::addService($serviceId, $class);
				$this->_servicesMap = KalturaServicesMap::getMap();
				return true;
			}
		}
			
		return false;
	}
	
	public function isActionExists($actionName, $ignoreAliasActions = true)
	{
		$actions = $this->getActions(false, $ignoreAliasActions);
		return array_key_exists($actionName, $actions);
	}
	
	public function getActionMethodName($actionId, $ignoreAliasActions = true)
	{
		$actions = $this->getActions(false, $ignoreAliasActions);
		if(isset($actions[$actionId]))
			return $actions[$actionId];
			
		return null;
	}
	
	/**
	 * @param bool $ignoreDeprecated
	 * @param bool $ignoreAliasActions - NOTE: if this parameter is passed as "false", the method will ignore the regular actions which have no @aliasAction annotation
	 * @return array
	 */
	public function getActions($ignoreDeprecated = false, $ignoreAliasActions = true)
	{
	    $actionsArrayType = "{$ignoreDeprecated}_{$ignoreAliasActions}";
		if (isset($this->_actions[$actionsArrayType]) && is_array($this->_actions[$actionsArrayType]))
			return $this->_actions[$actionsArrayType];
		
		$reflectionClass = new ReflectionClass($this->_serviceClass);
		
		$reflectionMethods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
		
		$actions = array();
		foreach($reflectionMethods as $reflectionMethod)
		{
			$docComment = $reflectionMethod->getDocComment();
			$parsedDocComment = new KalturaDocCommentParser( $docComment );
			if ($parsedDocComment->action)
			{
			    if($ignoreDeprecated && $parsedDocComment->deprecated)
					continue;
				if($ignoreAliasActions && $parsedDocComment->actionAlias)
					continue;	
					
				$actionName = $parsedDocComment->action;
				$actions[$actionName] = $reflectionMethod->getName(); // key is the action id (action name lower cased), value is the method name
			}
		}
		
		$this->_actions[$actionsArrayType] = $actions;
		
		return $this->_actions[$actionsArrayType];
	}
	
	/**
	 * Function returns an array of all actions that should be shown under aliases.
	 * @return array
	 */
	public function getAliasActions ()
	{
	    $actions = $this->getActions(false, false);
	    
	    $aliasActions = array();
	    foreach ($actions as $actionId => $actionName)
	    {
	        $actionDoccomment = $this->getActionInfo($actionId, false);
	        if ($actionDoccomment->actionAlias)
	        {
	            $aliasActions[$actionDoccomment->actionAlias] = $actionName;
	        }
	    }
	    
	    return $aliasActions;
	}
	
	/**
	 * @param string $actionName
	 * @return KalturaDocCommentParser
	 */
	public function getActionInfo($actionName, $ignoreAliasActions = true)
	{
		if (!$this->isActionExists($actionName, $ignoreAliasActions))
			throw new Exception("Action [$actionName] does not exists for service [$this->_serviceId]");
		
		$methodName = $this->getActionMethodName($actionName, $ignoreAliasActions);
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
			
		$methodName = $this->getActionMethodName($actionName);
		
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
			
			$actionParams[$name] = $paramInfo;
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

		$methodName = $this->getActionMethodName($actionName);
		
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
		$methodName = $this->getActionMethodName($actionName);
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
	
	public function getServiceClass()
	{
		return $this->_serviceClass;
	}
}
