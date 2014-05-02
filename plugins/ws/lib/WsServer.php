<?php
/**
 * @package plugins.ws
 * @subpackage lib
 */
class WsServer extends nusoap_server
{
	protected $serviceId;
	protected $serviceName;
	protected $pluginName;
	
	/**
	 * @var KalturaServiceActionItem
	 */
	protected $serviceMap;
	
	/**
	 * @var KalturaServiceReflector
	 */
	protected $serviceReflector;
	
	protected $addedTypes = array();
	
	public function __construct($serviceName, $pluginName = null)
	{
		$this->serviceName = $serviceName;
		$this->pluginName = $pluginName;
		$this->serviceId = strtolower($serviceName);
		if($pluginName)
		{
			$this->serviceId = strtolower("{$serviceName}_{$pluginName}");
		}
		
		KalturaTypeReflector::setClassInheritMapPath(KAutoloader::buildPath(kConf::get("cache_root_path"), "api_v3", "WsdlInheritMap.cache"));
		if(!KalturaTypeReflector::hasClassInheritMapCache())
		{
			$clientGenerator = new DummyForDocsClientGenerator();
			$clientGenerator->setIncludeOrExcludeList(null, null);
			$clientGenerator->load();
			
			$objects = $clientGenerator->getTypes();
			KalturaTypeReflector::setClassMap(array_keys($objects));
		}
		
		$ns = "urn:$this->serviceId";
		$this->configureWSDL($serviceName, $ns);
		$this->wsdl->schemaTargetNamespace = $ns;
		
		$this->serviceMap = KalturaServicesMap::getService($this->serviceId);
		$this->serviceReflector = KalturaServiceReflector::constructFromServiceId($this->serviceId);
		$actions = $this->serviceReflector->getActions(false, false);
		foreach($actions as $actionId => $actionName)
		{
			$actionReflector = $this->serviceReflector->getActionInfo($actionId);
			$actionParams = $this->serviceReflector->getActionParams($actionId);
			
			$inputs = array();
			foreach($actionParams as $paramName => $paramInfo)
			{
				/* @var $paramInfo KalturaParamInfo */
				$inputs[$paramName] = $this->getType($paramInfo->getType());
			}
			
			$output = array();
			if($actionReflector->returnType)
			{
				$output['return'] = $this->getType($actionReflector->returnType);
			}
			
			$this->register($actionId, $inputs, $output, $ns, "$ns#$actionId", 'rpc', 'encoded', $actionReflector->description);
		}
	}
	
	protected function getType($type)
	{
		switch($type)
		{
			case 'int':
			case 'time':
				return 'xsd:int';
				
			case 'string':
			case 'file':
				return 'xsd:string';
				
			case 'bool':
				return 'xsd:boolean';
				
			case 'float':
				return  'xsd:float';
				
			case 'bigint':
				return 'xsd:long';
		}
		
		if(in_array($type, $this->addedTypes))
		{
			return "tns:$type";
		}
		$this->addedTypes[] = $type;
		
		$typeReflector = KalturaTypeReflectorCacher::get($type);
		if(!$typeReflector)
			return null;
		
		if(is_subclass_of($type, 'IKalturaEnum'))
		{
			$xsdType = 'xsd:int';
			if($typeReflector->isStringEnum())
			{
				$xsdType = 'xsd:string';
			}
			$this->wsdl->addSimpleType($type, $xsdType, 'SimpleType', 'scalar', $typeReflector->getConstantsValues());
			
			return "tns:$type";
		}
	
		if($typeReflector->isArray())
		{
			$arrayType = $this->getType($typeReflector->getArrayType());
			$attributes = array(
		        array(
		        	'ref' => 'SOAP-ENC:arrayType',
		        	'wsdl:arrayType' => "{$arrayType}[]"
				)
    		);
    		
			$this->wsdl->addComplexType($type, 'complexType', 'array', '', 'SOAP-ENC:Array', array(), $attributes, $arrayType);
			return "tns:$type";
		}
	
		if(is_subclass_of($type, 'KalturaObject'))
		{
			$parent = $typeReflector->getParentTypeReflector();
			if($parent)
			{
				$this->getType($parent->getType());
			}
			
			$attributes = array(
				'objectType' => array(
					'name' => 'objectType',
					'type' => 'xsd:string',
					'default' => $type,
				)
			);
			$properties = $typeReflector->getProperties();
			foreach($properties as $name => $property)
			{
				/* @var $property KalturaPropertyInfo */
				$attributes[$name] = array(
					'name' => $name,
					'type' => $this->getType($property->getType()),
				);
			}
			$this->wsdl->addComplexType($type, 'complexType', 'struct', 'all', '', $attributes);
			
			$subTypes = $typeReflector->getSubTypesNames();
			foreach($subTypes as $subType)
			{
				$this->getType($subType);
			}
			
			return "tns:$type";
		}
	}
	
	/* (non-PHPdoc)
	 * @see nusoap_base::appendDebug()
	 */
	public function appendDebug($string)
	{
		KalturaLog::debug($string);
	}
	
	/* (non-PHPdoc)
	 * @see nusoap_base::debug()
	 */
	public function debug($string)
	{
		KalturaLog::debug($string);
	}
	
	/* (non-PHPdoc)
	 * @see nusoap_server::invoke_method()
	 */
	public function invoke_method()
	{
		$this->debug('in invoke_method, methodname=' . $this->methodname . ' methodURI=' . $this->methodURI . ' SOAPAction=' . $this->SOAPAction);
		
		if($this->wsdl)
		{
			if($this->opData = $this->wsdl->getOperationData($this->methodname))
			{
				$this->debug('in invoke_method, found WSDL operation=' . $this->methodname);
				$this->appendDebug('opData=' . $this->varDump($this->opData));
			} elseif($this->opData = $this->wsdl->getOperationDataForSoapAction($this->SOAPAction))
			{
				// Note: hopefully this case will only be used for doc/lit, since rpc services should have wrapper element
				$this->debug('in invoke_method, found WSDL soapAction=' . $this->SOAPAction . ' for operation=' . $this->opData['name']);
				$this->appendDebug('opData=' . $this->varDump($this->opData));
				$this->methodname = $this->opData['name'];
			} else
			{
				$this->debug('in invoke_method, no WSDL for operation=' . $this->methodname);
				$this->fault('SOAP-ENV:Client', "Operation '" . $this->methodname . "' is not defined in the WSDL for this service");
				return;
			}
		} else
		{
			$this->debug('in invoke_method, no WSDL to validate method');
		}
		
		$actionId = strtolower($this->methodname);
		if(!isset($this->serviceMap->actionMap[$actionId]))
		{
			$this->debug("in invoke_method, action '$this->methodname' not found in service '$this->serviceId'!");
			$this->result = 'fault: action not found';
			$this->fault('SOAP-ENV:Client', "action '$this->methodname' not defined in service '$this->serviceId'");
			return;
		}
		
		// array ("serviceClass" => $serviceClass, "actionMethodName" => $actionName, "serviceId" => $serviceId, "actionName" => $actionId);
		$action = $this->serviceMap->actionMap[$actionId];
		$class = $action['serviceClass'];
		$method = $action['actionMethodName'];
		if(!method_exists($class, $method))
		{
			$this->debug("in invoke_method, method '$method' not found in class '$class'!");
			$this->result = 'fault: method not found';
			$this->fault('SOAP-ENV:Client', "method '$method' not defined in service '$class'");
			return;
		}
		
		// evaluate message, getting back parameters
		// verify that request parameters match the method's signature
		if(!$this->verify_method($this->methodname, $this->methodparams))
		{
			// debug
			$this->debug('ERROR: request not verified against method signature');
			$this->result = 'fault: request failed validation against method signature';
			// return fault
			$this->fault('SOAP-ENV:Client', "Operation '$this->methodname' not defined in service.");
			return;
		}
		
		// if there are parameters to pass
		$this->debug('in invoke_method, params:');
		$this->appendDebug($this->varDump($this->methodparams));
		$this->debug("in invoke_method, calling '$this->methodname'");
		
		$instance = new $class();
		/* @var $instance KalturaBaseService */
		$instance->initService($this->serviceId, $this->serviceName, $action['actionName']);
		$call_arg = array(&$instance, $method);
	
        try
        {
	        $actionReflector = new KalturaActionReflector($this->serviceId, $this->methodname, $action);
        }
        catch (Exception $e)
        {
			KalturaLog::err($e);
			$this->result = 'fault: ' . $e->getMessage();
			$this->fault($e->getCode(), $e->getMessage());
			return;
        }
        
		$actionParams = $actionReflector->getActionParams();
		$inputParams = $this->methodparams;
		foreach($actionParams as $paramName => $paramInfo)
		{
			/* @var $paramInfo KalturaParamInfo */
			if($paramInfo->isFile() && isset($inputParams[$paramName]))
			{
				$filePath = tempnam(myContentStorage::getFSUploadsPath(), 'soap_');
				file_put_contents($filePath, base64_decode($inputParams[$paramName]));
				$inputParams[$paramName] = array(
					'name' => basename($filePath),
					'type' => '',
					'tmp_name' => $filePath,
					'error' => UPLOAD_ERR_OK,
					'size' => filesize($filePath),
					'disable_validation' => kConf::get('disable_validation_token', 'local', 'disable_validation'),
				);
			}
		}
		KalturaLog::debug("inputParams [" . print_r($inputParams, true) . "]");
		$requestDeserializer = new KalturaRequestDeserializer($inputParams);
		$arguments = $requestDeserializer->buildActionArguments($actionParams);
		try
		{
			if(is_array($this->methodparams))
			{
				$this->methodreturn = call_user_func_array($call_arg, $arguments);
			} else
			{
				$this->methodreturn = call_user_func_array($call_arg, array());
			}
		}
		catch(Exception $e)
		{
			KalturaLog::err($e);
			$this->result = 'fault: ' . $e->getMessage();
			// return fault
			$this->fault($e->getCode(), $e->getMessage());
			return;
		}
		
		$this->debug('in invoke_method, methodreturn:');
		$this->appendDebug($this->varDump($this->methodreturn));
		$this->debug("in invoke_method, called method $this->methodname, received data of type " . gettype($this->methodreturn));
	}
}
