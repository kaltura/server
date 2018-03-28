<?php
class XmlClientGenerator extends ClientGeneratorFromPhp  
{
	/**
	 * @var DOMDocument
	 */
	private $_doc = null;
	
	/**
	 * @var DOMElement
	 */
	private $_xmlElement = null;
	
	/**
	 * @var array
	 */
	private $_requiredPlugins = array();
	
	/**
	 * @var array
	 */
	private $_errorClasses = array('KalturaErrors');
	
	public function __construct()
	{
		parent::__construct();
		$this->_doc = new DOMDocument();
		$this->_doc->formatOutput = true; 
	}
	
	/**
	 * {@inheritDoc}
	 * @see ClientGeneratorFromPhp::generate()
	 */
	public function generate() 
	{
		$this->load();
		
		$this->_xmlElement = $this->_doc->createElement("xml");
		$this->_xmlElement->setAttribute('apiVersion', KALTURA_API_VERSION);
		$this->_xmlElement->setAttribute('generatedDate', time());
		exec("which svnversion 2>/dev/null",$out,$rc);
		if ($rc === 0){
			$apiV3Path = realpath(dirname(__FILE__) . '/../api_v3');
			$svnVersion = shell_exec("svnversion $apiV3Path");
			if ($svnVersion === null)
				KalturaLog::warning("Failed to get svn revision number");
			else
				$this->_xmlElement->setAttribute('revision', trim($svnVersion));
		}
		
		$this->_doc->appendChild($this->_xmlElement);
		$this->_xmlElement->appendChild(new DOMComment(" Generated on date " . strftime("%d/%m/%y %H:%M:%S" , time()) . " "));
		
		$enumsElement = $this->_doc->createElement("enums");
		$classesElement = $this->_doc->createElement("classes");
		
		foreach($this->_types as $typeReflector)
		{
			if ($typeReflector->isEnum() || $typeReflector->isStringEnum())
			{
				$enumElement = $this->getEnumElement($typeReflector);
				$enumsElement->appendChild($enumElement);
			}
			else if (!$typeReflector->isArray())
			{
				$classElement = $this->getClassElement($typeReflector);
				$classesElement->appendChild($classElement);
			}
		}		
		
		$servicesElement = $this->_doc->createElement("services");
		foreach($this->_services as $serviceId => $serviceActionItem)
		{
			/* @var $serviceActionItem KalturaServiceActionItem */
				
			$serviceElement = $this->_doc->createElement("service");
			$serviceElement->setAttribute("id", $serviceId);
			$serviceElement->setAttribute("name", $serviceActionItem->serviceInfo->serviceName);
			$description = $serviceActionItem->serviceInfo->description;
			$description = $this->fixDescription($description);
			$serviceElement->setAttribute("description", $description);

			if($serviceActionItem->serviceInfo->deprecated)
				$serviceElement->setAttribute("deprecated", "1");
			
			$plugin = $this->extractPluginNameFromPackage($serviceActionItem->serviceInfo->package);
			if($plugin)
			{
				$serviceElement->setAttribute("plugin", $plugin);
			}
			ksort($serviceActionItem->actionMap);
			foreach($serviceActionItem->actionMap as $actionId => $actionReflector)
			{
				/* @var $actionReflector KalturaActionReflector */
				$actionInfo = $actionReflector->getActionInfo();
				
				if($actionInfo->serverOnly)
					continue;
					
				if (strpos($actionInfo->clientgenerator, "ignore") !== false)
					continue;
					
				$serviceActionElement = $this->getServiceActionElement($actionReflector);
				$serviceElement->appendChild($serviceActionElement);
			}
			
			$servicesElement->appendChild($serviceElement);
		}

		$pluginsElement = $this->_doc->createElement("plugins");
		$this->appendPlugins($pluginsElement);

		$errorsElement = $this->_doc->createElement("errors");
		$this->appendErrors($errorsElement);
		
		$configurationsElement = $this->_doc->createElement("configurations");
		$this->appendConfigurations($configurationsElement);
		
		$this->_xmlElement->appendChild($enumsElement);
		$this->_xmlElement->appendChild($classesElement);
		$this->_xmlElement->appendChild($servicesElement);
		$this->_xmlElement->appendChild($pluginsElement);
		$this->_xmlElement->appendChild($errorsElement);
		$this->_xmlElement->appendChild($configurationsElement);
		
		$this->addFile("KalturaClient.xml", $this->_doc->saveXML());
	}
	
	private function pluginHasServices($pluginInstance)
	{
		$servicesInterface = $pluginInstance->getInstance('IKalturaServices');
		if (!$servicesInterface)
			return false;
			
		$pluginName = $pluginInstance->getPluginName();
		return count($servicesInterface->getServicesMap());
	}
	
	private function appendErrors(DOMElement $errorsElement)
	{
		$appended = array();
		foreach($this->_errorClasses as $errorsClass)
		{
			$apiErrorsReflected = new ReflectionClass($errorsClass);
			$apiErrors = $apiErrorsReflected->getConstants();

			foreach($apiErrors as $constName => $errorData)
			{
				$errorParts = explode(';', $errorData);
				if(count($errorParts) != 3)
					throw new Exception("Missing error info in $errorsClass::$constName: $errorData");

				list($errorCode, $errorParams, $errorMessage) = $errorParts;
					
				if(isset($appended[$errorCode]))
					continue;
				
				$errorElement = $this->_doc->createElement('error');
				$errorElement->setAttribute('name', $errorCode);
				$errorElement->setAttribute('code', $errorCode);
				$errorElement->setAttribute('message', $errorMessage);
				
				if(trim($errorParams))
				{
					$errorParameters = explode(',', $errorParams);
					foreach($errorParameters as $errorParameter)
					{
						$errorParameterElement = $this->_doc->createElement('parameter');
						$errorParameterElement->setAttribute('name', $errorParameter);
						$errorElement->appendChild($errorParameterElement);
					}
				}
				
				$errorsElement->appendChild($errorElement);
				
				$appended[$errorCode] = true;
			}
		}
	}
	
	private function appendPlugins(DOMElement $pluginsElement)
	{
		// Add all the plugins that offer services to the list of required plugins
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaPlugin');
		foreach($pluginInstances as $pluginInstance)
		{
			if (!$this->pluginHasServices($pluginInstance))
				continue;

			$pluginName = $pluginInstance->getPluginName();
			if (!in_array($pluginName, $this->_requiredPlugins))
				$this->_requiredPlugins[] =  $pluginName;
		}
		
		// Add plugin tags to the XML
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaPlugin');
		foreach($pluginInstances as $pluginInstance)
		{
			if (!in_array($pluginInstance->getPluginName(), $this->_requiredPlugins))
				continue;
			
			$this->appendPlugin($pluginsElement, $pluginInstance);
		}
	}
	
	private function appendConfiguration(DOMElement $configurationsElement, $name, $class)
	{
		$configurationElement = $this->_doc->createElement($name);
		$configurationElement->setAttribute('type', $class);
		
		$reflectClass = new ReflectionClass($class);
		$properties = $reflectClass->getProperties(ReflectionProperty::IS_PUBLIC);
		foreach($properties as $property)
		{
			if ($property->getDeclaringClass() == $reflectClass) // only properties defined in the current class, ignore the inherited
			{
				$parsedDocComment = new KalturaDocCommentParser($property->getDocComment());
				$paramElement = $this->_doc->createElement($property->name);
				$paramElement->setAttribute('type', $parsedDocComment->varType);
			
				if($parsedDocComment->alias)
				{
					$paramElement->setAttribute('alias', $parsedDocComment->alias);
				}
				
				if($parsedDocComment->volatile)
				{
					$paramElement->setAttribute('volatile', true);
				}
				
				if($parsedDocComment->description)
				{
					$paramElement->setAttribute('description', trim($parsedDocComment->description));
				}
				
				$configurationElement->appendChild($paramElement);
			}
		}
						
		$configurationsElement->appendChild($configurationElement);
	}
	
	private function appendConfigurations(DOMElement $configurationsElement)
	{
		$this->appendConfiguration($configurationsElement, 'client', 'KalturaClientConfiguration');
		$this->appendConfiguration($configurationsElement, 'request', 'KalturaRequestConfiguration');
	}
	
	private function appendPlugin(DOMElement $pluginsElement, IKalturaPlugin $pluginInstance)
	{  
		$pluginElement = $this->_doc->createElement("plugin");
		$pluginElement->setAttribute('name', $pluginInstance->getPluginName());
		
		$dependencyInterface = $pluginInstance->getInstance('IKalturaPending');
		if ($dependencyInterface)
		{
			$dependencyList = $dependencyInterface->dependsOn();
			
			foreach ($dependencyList as $dependency)
			{
				$dependencyElement = $this->_doc->createElement("dependency");
				
				if (!in_array($dependency->getPluginName(), $this->_requiredPlugins))
					continue;		// don't care about dependencies on plugins not generated in the client lib 
				
				$dependencyElement->setAttribute('pluginName', $dependency->getPluginName());				
				$pluginElement->appendChild($dependencyElement);
			}
		}

		$pluginServices = $pluginInstance->getInstance('IKalturaServices');
		if ($pluginServices)
		{
			$this->appendPluginServices($pluginInstance->getPluginName(), $pluginElement, $pluginServices);
		}
		
		$pluginsElement->appendChild($pluginElement);
	}
	
	private function appendPluginServices($pluginName, DOMElement &$pluginElement, IKalturaServices $pluginInstance)
	{
		$servicesMap = $pluginInstance->getServicesMap();
		foreach($servicesMap as $service => $serviceClass)
		{
			$pluginServiceElement = $this->_doc->createElement("pluginService");
			$pluginServiceElement->setAttribute('name', $service);
			$pluginElement->appendChild($pluginServiceElement);
		}
	}
	
	protected function extractPluginNameFromPackage($package)
	{ 
		if(!is_string($package))
			return null;
			
		$packages = explode('.', $package, 2);
		if(count($packages) != 2 || $packages[0] != 'plugins')
			return null;
			
		$pluginName = $packages[1];
		if (!in_array($pluginName, $this->_requiredPlugins))
			$this->_requiredPlugins[] = $pluginName;
		
		return $pluginName;
	}
	
	private function getEnumElement(KalturaTypeReflector $typeReflector)
	{
		$enumElement = $this->_doc->createElement("enum");
		$enumElement->setAttribute("name", $typeReflector->getType());
		if ($typeReflector->isEnum())
			$enumElement->setAttribute("enumType", "int");
		else if ($typeReflector->isStringEnum())
			$enumElement->setAttribute("enumType", "string");
			
		$plugin = $this->extractPluginNameFromPackage($typeReflector->getPackage());
		if($plugin)
		{
			$enumElement->setAttribute("plugin", $plugin);
		}

		if($typeReflector->isDeprecated())
			$enumElement->setAttribute("deprecated", "1");

		$constants = array();
		foreach($typeReflector->getConstants() as $contant)
		{
			$name = $contant->getName();
			$value = $contant->getDefaultValue();
			$constants[$name] = $value;
		}
		asort($constants);
		foreach ($constants as $name => $value)
		{
			if(!is_string($value) && !is_int($value))
				throw new Exception("Invalid enum value [" . $typeReflector->getType() . "::$name]");
			
			$const = $this->_doc->createElement("const");
			$const->setAttribute("name", $name);
			$const->setAttribute("value", $value);
			$enumElement->appendChild($const);
		}
		
		return $enumElement;
	}
	
	private function getClassElement(KalturaTypeReflector $typeReflector)
	{
		$properties = $typeReflector->getProperties();
				
		$classElement = $this->_doc->createElement("class");
		$classElement->setAttribute("name", $typeReflector->getType()); 

		$parentTypeReflector = $typeReflector->getParentTypeReflector();
		
		if($typeReflector->isAbstract())
		{
			$classElement->setAttribute("abstract", true);	
		}
		
		if ($parentTypeReflector)
		{
			$parentType = $parentTypeReflector->getType();
			$classElement->setAttribute("base", $parentType);					
		}
		
		$plugin = $this->extractPluginNameFromPackage($typeReflector->getPackage());
		if($plugin)
		{
			$classElement->setAttribute("plugin", $plugin);
		}
		
		$description = $typeReflector->getDescription();
		$description = $this->fixDescription($description);
		$classElement->setAttribute("description", $description);

		if($typeReflector->isDeprecated())
			$classElement->setAttribute("deprecated", "1");

		if($typeReflector->getRequiredPermissions())
			$classElement->setAttribute("requiresPermissions", implode(',', $typeReflector->getRequiredPermissions()));
		
		$properties = $typeReflector->getCurrentProperties();
		foreach($properties as $property)
		{
			/* @var $property KalturaPropertyInfo */
			if ($property->isServerOnly())
			{
				continue;
			}
			
			$propType = $property->getType();
			$propName = $property->getName();
			
			$propertyElement = $this->_doc->createElement("property");
			$propertyElement->setAttribute("name", $propName);
			
			if ($property->isAssociativeArray())
			{
				$propertyElement->setAttribute("type", "map");
				$propertyElement->setAttribute("arrayType", $property->getArrayType());
			}
			else if ($property->isArray())
			{
				$propertyElement->setAttribute("type", "array");
				$propertyElement->setAttribute("arrayType", $property->getArrayType());
			}
			else if ($property->isEnum())
			{
				$propertyElement->setAttribute("type", "int");
				$propertyElement->setAttribute("enumType", $property->getType());
			}
			else if ($property->isStringEnum())
			{
				$propertyElement->setAttribute("type", "string");
				$propertyElement->setAttribute("enumType", $property->getType());
			}
			else if ($propType == 'KalturaObject')
			{
				$propertyElement->setAttribute("type", 'KalturaObjectBase');
			}
			else
			{
				$propertyElement->setAttribute("type", $propType);
				if($property->isTime())
					$propertyElement->setAttribute("isTime", "1");
			}
			
			$propertyElement->setAttribute("readOnly", $property->isReadOnly() ? "1" : "0");
			$propertyElement->setAttribute("insertOnly", $property->isInsertOnly() ? "1" : "0");
			$propertyElement->setAttribute("writeOnly", $property->isWriteOnly() ? "1" : "0");

			if($property->getDynamicType())
				$propertyElement->setAttribute("valuesEnumType", $property->getDynamicType());

			if($property->getPermissions())
				$propertyElement->setAttribute("requiresPermissions", implode(',', $property->getPermissions()));
						
			$description = $property->getDescription();
			$description = $this->fixDescription($description);
			$propertyElement->setAttribute("description", $description);

			foreach($property->getConstraints() as $constraint => $value)
				$propertyElement->setAttribute($constraint, $value);

			if($property->isDeprecated())
				$propertyElement->setAttribute("deprecated", "1");
					
			$classElement->appendChild($propertyElement);
		}
		
		return $classElement;
	}
	
	private function getServiceActionElement(KalturaActionReflector $actionReflector)
	{
		$outputTypeReflector = $actionReflector->getActionOutputType();
		$actionInfo = $actionReflector->getActionInfo();
		$actionParams = $actionReflector->getActionParams();
		
		$outputType = null;
		if ($outputTypeReflector)
			$outputType = $outputTypeReflector->getType();
		
		$actionElement = $this->_doc->createElement("action");
		$actionElement->setAttribute("name", $actionReflector->getActionName());

		if($actionInfo->deprecated)
			$actionElement->setAttribute("deprecated", "1");
		
		foreach($actionParams as $actionParam)
		{
			/* @var $actionParam KalturaParamInfo */
			$actionParamElement = $this->_doc->createElement("param");
			$actionParamElement->setAttribute("name", $actionParam->getName());
			
			if ($actionParam->isAssociativeArray())
			{
				$actionParamElement->setAttribute("type", "map");
				$actionParamElement->setAttribute("arrayType", $actionParam->getArrayType());
			}
			elseif ($actionParam->isArray())
			{
				$actionParamElement->setAttribute("type", "array");
				$actionParamElement->setAttribute("arrayType", $actionParam->getArrayType());
			}
			elseif ($actionParam->isEnum())
			{
				$actionParamElement->setAttribute("type", "int");
				$actionParamElement->setAttribute("enumType", $actionParam->getType());
			}
			else if ($actionParam->isStringEnum())
			{
				$actionParamElement->setAttribute("type", "string");
				$actionParamElement->setAttribute("enumType", $actionParam->getType());
			}
			else
			{
				$actionParamElement->setAttribute("type", $actionParam->getType());
				if($actionParam->isTime())
					$actionParamElement->setAttribute("isTime", "1");
			}
			$actionParamElement->setAttribute("optional", $actionParam->isOptional() ? "1" : "0");
			if ($actionParam->isOptional())
			{
				$defaultValue = $actionParam->getDefaultValue();
				if ($defaultValue === null)
					$defaultValue = "null";
					
				switch($actionParam->getType())
				{
					case "bool":
						if ($defaultValue === true)
							$actionParamElement->setAttribute("default", "true");
						else if ($defaultValue === false)
							$actionParamElement->setAttribute("default", "false");
						break;
					case "bigint":
					case "int":
					case "float":
					case "string":
						$actionParamElement->setAttribute("default", $defaultValue);
						break;
					default:
						if ($actionParam->isEnum())							
							$actionParamElement->setAttribute("default", $defaultValue);
						else
							$actionParamElement->setAttribute("default", "null");
				}
			}
			
			$description = $actionParam->getDescription();
			$description = $this->fixDescription($description);
			$actionParamElement->setAttribute("description", $description);
			
			$actionElement->appendChild($actionParamElement);
		}
		
		$resultElement = $this->_doc->createElement("result");
		
		$arrayType = null;
		if ($outputTypeReflector)
		{
			if($outputTypeReflector->isAssociativeArray())
			{
				$resultElement->setAttribute("type", "map");
				$arrayType = $outputTypeReflector->getArrayType();
				$resultElement->setAttribute("arrayType", $arrayType);
			}
			else if($outputTypeReflector->isArray())
			{
				$resultElement->setAttribute("type", "array");
				$arrayType = $outputTypeReflector->getArrayType();
				$resultElement->setAttribute("arrayType", $arrayType);
			}
			else 
			{
				$resultElement->setAttribute("type", $outputType);
				if($outputTypeReflector->isTime())
					$resultElement->setAttribute("isTime", "1");
			}
		}

		$description = $actionInfo->description;
		$description = $this->fixDescription($description);
		$actionElement->setAttribute("description", kString::stripUtf8InvalidChars($description));
		$actionElement->setAttribute("enableInMultiRequest", ($outputType === 'file' ? "0" : "1"));

		$actionElement->appendChild($resultElement);

		if(!empty($actionInfo->actionAlias)) 
		{
			$package = $actionReflector->getActionClassInfo()->package;
			list($prefix, $pluginName) = explode('.', $package);
			$actionElement->setAttribute('actionAlias', $actionInfo->actionAlias);
			$actionElement->setAttribute('plugin', $pluginName);
		}

		if ($actionInfo->beta)
		{
			$actionElement->setAttribute('beta', true);
		}

		foreach($actionInfo->errors as $error)
		{
			list($errorCode, $content, $errorClass) = $error;
			if(!in_array($errorClass, $this->_errorClasses))
				$this->_errorClasses[] = $errorClass;

			$throws = $this->_doc->createElement("throws");
			$throws->setAttribute("name", $errorCode);
			$actionElement->appendChild($throws);
		}

		$actionElement->setAttribute('sessionRequired', ($actionInfo->ksNeeded ? 'always' : (is_null($actionInfo->ksNeeded) ? 'optional' : 'none')));
		
		return $actionElement;
	}
	
	private function fixDescription($description)
	{
		$description = str_replace("\r", '', trim($description, " \t\r\n"));
		//$description = substr($description, 0, strlen($description) - 1);
		return $description;
	}
	
	protected function writeHeader() { }

	protected function writeFooter() { }
	
	protected function writeBeforeServices() { }
	
	protected function writeBeforeService(KalturaServiceActionItem $serviceReflector) { }
	
	protected function writeServiceAction($serviceId, $serviceName, $action, $actionParams, $outputTypeReflector) { }
	
	protected function writeAfterService(KalturaServiceActionItem $serviceReflector) { }
	
	protected function writeAfterServices() { }
	
	protected function writeBeforeTypes() { }
	
	protected function writeType(KalturaTypeReflector $type) { }
	
	protected function writeAfterTypes() { }
}
