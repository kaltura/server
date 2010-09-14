<?php
class JsClientGenerator extends ClientGeneratorFromPhp 
{
	protected $_text = "";
	
	public function JsClientGenerator()
	{
		parent::ClientGeneratorFromPhp(realpath("sources/js"));
	}
	
	public function generate() 
	{
		$this->load();
		
		$this->writeHeader();

		$this->writeBeforeTypes();

		$this->sortTypesForPhp();
		
		foreach($this->_types as $typeReflector)
		{
			$this->writeType($typeReflector);
		}
		$this->writeAfterTypes();
		
		// services
		foreach($this->_services as $serviceReflector)
		{
			$this->writeBeforeService($serviceReflector);
			$serviceId = $serviceReflector->getServiceId();
			$serviceName = $serviceReflector->getServiceName();
			$actions = $serviceReflector->getActions();
			$actions = array_keys($actions);
			foreach($actions as $actionId)
			{
				$actionInfo = $serviceReflector->getActionInfo($actionId);
				
				if($actionInfo->deprecated || $actionInfo->serverOnly)
					continue;
					
				if (strpos($actionInfo->clientgenerator, "ignore") !== false)
					continue;
					
				$actionName = $actionInfo->action;
				$outputTypeReflector = $serviceReflector->getActionOutputType($actionId);
				$actionParams = $serviceReflector->getActionParams($actionId);
				$this->writeServiceAction($serviceId, $serviceName, $actionName, $actionParams, $outputTypeReflector);				
			}
			$this->writeAfterService($serviceReflector);
		}
		
		$this->writeMainClassDeclaration();
		foreach($this->_services as $serviceReflector)
		{
			$this->writeMainClassServiceDeclaration($serviceReflector);
		}
		$this->writeMainClassConstructorDeclaration();
		foreach($this->_services as $serviceReflector)
		{
			$this->writeMainClassServiceInitialization($serviceReflector);
		}
		$this->writeMainClassConstructorClosure();
		$this->writeMainClassClosure();
		
		$this->writeFooter();
		
		$this->addFile("KalturaClient.js", $this->_text);
	}
	
	protected function writeHeader()
	{
	}
	
	protected function writeFooter()
	{
	}
	
	protected function writeBeforeTypes()
	{
	}
	
	protected function writeType(KalturaTypeReflector $typeReflector)
	{
		$type = $typeReflector->getType();
		if ($typeReflector->isEnum() || $typeReflector->isStringEnum())
		{
			$contants = $typeReflector->getConstants();
			$this->echoLine("function $type()");
			$this->echoLine("{");
			$this->echoLine("}");
			foreach($contants as $contant)
			{
				$name = $contant->getName();
				$value = $contant->getDefaultValue();
				if ($typeReflector->isEnum())
					$this->echoLine("$type.prototype.$name = $value;");
				else
					$this->echoLine("$type.prototype.$name = \"$value\";");
			}
			$this->echoLine();
		}
		else if (!$typeReflector->isArray())
		{
			// class definition
			$properties = $typeReflector->getCurrentProperties();
			$parentTypeReflector = $typeReflector->getParentTypeReflector();
			
			$this->echoLine("function $type()");
			$this->echoLine("{");
			$this->echoLine("}");
			
			if ($parentTypeReflector)
			    $this->echoLine("$type.prototype = new " . $parentTypeReflector->getType() . "();");
		    else
			    $this->echoLine("$type.prototype = new KalturaObjectBase();");
			    
			// class properties
			foreach($properties as $property)
			{
				$propType = $property->getType();
				$propName = $property->getName();
				$this->echoLine("/**");
				$description = str_replace("\n", "\n	 * ", $property->getDescription()); // to format multiline descriptions
				$this->echoLine(" * " . $description);
				$this->echoLine(" *");
				$this->echoLine(" * @var $propType");
				if ($property->isReadOnly())
					$this->echoLine(" * @readonly");
				if ($property->isInsertOnly())
					$this->echoLine(" * @insertonly");
				$this->echoLine(" */");
				$this->echoLine("$type.prototype.$propName = null;");
				$this->echoLine("");
			}
			$this->echoLine();

			/*$this->echoLine("	public function toParams()");
			$this->echoLine("	{");
			$this->echoLine("		\$kparams = parent::toParams();");
			foreach($properties as $property)
			{
				$propType = $property->getType();
				$propName = $property->getName();
				
				if ($property->isSimpleType() || $property->isEnum())
				{
					$this->echoLine("		\$this->addIfNotNull(\$kparams, \"$propName\", \$this->$propName);");
				}
				else
				{ 
					continue; // ignore sub objects and arrays
				}
			}
			$this->echoLine("		return \$kparams;");
			$this->echoLine("	}");*/
		}
	}
	
	protected function writeAfterTypes()
	{
	}
	
	protected function writeBeforeServices()
	{
	}

	protected function writeBeforeService(KalturaServiceReflector $serviceReflector)
	{
		$serviceName = $serviceReflector->getServiceName();
		
		$serviceClassName = "Kaltura".$this->upperCaseFirstLetter($serviceName)."Service";
		$this->echoLine();		
		$this->echoLine("function $serviceClassName(client)");
		$this->echoLine("{");
		$this->echoLine("	this.init(client);");
		$this->echoLine("}");
		
		$this->echoLine();
			
		$this->echoLine("$serviceClassName.prototype = new KalturaServiceBase();");
	}
	
	protected function writeServiceAction($serviceId, $serviceName, $action, $actionParams, $outputTypeReflector)
	{
		$serviceClassName = "Kaltura".$this->upperCaseFirstLetter($serviceName)."Service";
	
		$outputType = null;
		if ($outputTypeReflector)
			$outputType = $outputTypeReflector->getType();
		
		$paramNames = array('callback');
		foreach($actionParams as $actionParam)
			$paramNames[] = $actionParam->getName();
		$paramNames = join(', ', $paramNames);	
		
		// method signature
		$signature = "";
		if (in_array($action, array("list", "clone"))) // because list & clone are preserved in PHP
			$signature .= "$serviceClassName.prototype.${action}Action = function($paramNames)";
		else
			$signature .= "$serviceClassName.prototype.$action = function($paramNames)";
			
		$this->echoLine();	
		$this->echoLine("$signature");
		$this->echoLine("{");
		
		foreach($actionParams as $actionParam)
		{
			if (!$actionParam->isOptional())
				continue;
				
			$paramName = $actionParam->getName();
			if ($actionParam->isSimpleType() || $actionParam->isEnum() || $actionParam->isStringEnum())
			{
				$defaultValue = $actionParam->getDefaultValue();
				if ($defaultValue === false)
					$defaultValue = "false";
				else if ($defaultValue === true)
					$defaultValue = "true";
				else if ($defaultValue === null)
					$defaultValue = "null";
				else if (is_string($defaultValue))
					$defaultValue = "\"$defaultValue\"";
				else if (is_numeric($defaultValue))
					$defaultValue = $defaultValue; 
			}
			else
			{
				$defaultValue = "null";
			}
			$this->echoLine("	if(!$paramName)");
			$this->echoLine("		$paramName = $defaultValue;");
		}
		$this->echoLine();	
		
		$this->echoLine("	kparams = new Object();");
		$haveFiles = false;
		foreach($actionParams as $actionParam)
		{
			$paramName = $actionParam->getName();
			
		    if ($haveFiles === false && $actionParam->isFile())
	    	{
		        $haveFiles = true;
	        	$this->echoLine("	kfiles = new Object();");
	    	}
	    
			if ($actionParam->isComplexType())
			{
				if ($actionParam->isEnum() || $actionParam->isStringEnum())
				{
					$this->echoLine("	this.client.addParam(kparams, \"$paramName\", $paramName);");
				}
				else if ($actionParam->isFile())
				{
					$this->echoLine("	this.client.addParam(kfiles, \"$paramName\", $paramName);");
				}
				else if ($actionParam->isArray())
				{
					$extraTab = "";
					if ($actionParam->isOptional())
					{
						$this->echoLine("	if($paramName != null)");
						$extraTab = "	";
					}
					$this->echoLine("$extraTab	for(var index in $paramName)");
					$this->echoLine("$extraTab	{");
					$this->echoLine("$extraTab		var obj = ${paramName}[index];");
					$this->echoLine("$extraTab		this.client.addParam(kparams, \"$paramName:\" + index, toParams(obj));");
					$this->echoLine("$extraTab	}");
				}
				else
				{
					$extraTab = "";
					if ($actionParam->isOptional())
					{
						$this->echoLine("	if ($paramName != null)");
						$extraTab = "	";
					}
					$this->echoLine("$extraTab	this.client.addParam(kparams, \"$paramName\", toParams($paramName));");
				}
			}
			else
			{
				$this->echoLine("	this.client.addParam(kparams, \"$paramName\", $paramName);");
			}
		}
		if ($haveFiles)
			$this->echoLine("	this.client.queueServiceActionCall(\"$serviceId\", \"$action\", kparams, kfiles);");
		else
			$this->echoLine("	this.client.queueServiceActionCall(\"$serviceId\", \"$action\", kparams);");
		$this->echoLine("	if (!this.client.isMultiRequest())");
		$this->echoLine("		this.client.doQueue(callback);");
		$this->echoLine("};");	
	}
	
	protected function writeAfterService(KalturaServiceReflector $serviceReflector)
	{
	}
	
	protected function writeAfterServices()
	{
	}
	
	private function writeMainClassDeclaration()
	{
		$this->echoLine();
		$this->echoLine("function KalturaClient(config)");
		$this->echoLine("{");
		$this->echoLine("	this.init(config);");
		$this->echoLine("}");
		
		$this->echoLine();
		$this->echoLine("KalturaClient.prototype = new KalturaClientBase()");
	}
	
	private function writeMainClassServiceDeclaration(KalturaServiceReflector $serviceReflector)
	{
		$docComment = $serviceReflector->getServiceInfo();
		
		$serviceName = $serviceReflector->getServiceName();
		$serviceClassName = "Kaltura".$this->upperCaseFirstLetter($serviceName)."Service";
		$this->echoLine("/**");
		$description = str_replace("\n", "\n	 * ", $docComment->description); // to format multiline descriptions
		$this->echoLine(" * " . $description);
		$this->echoLine(" *");
		$this->echoLine(" * @var $serviceClassName");
		$this->echoLine(" */");
		$this->echoLine("KalturaClient.prototype.$serviceName = null;");
		$this->echoLine("");
	}
	
	private function writeMainClassConstructorDeclaration()
	{
		$this->echoLine("");
		$this->echoLine("KalturaClient.prototype.init = function(config)");
		$this->echoLine("{");
		$this->echoLine("	KalturaClientBase.prototype.init.apply(this, arguments);");
	}
	
	private function writeMainClassServiceInitialization(KalturaServiceReflector $serviceReflector)
	{
		$serviceName = $serviceReflector->getServiceName();
		$serviceClassName = "Kaltura".$this->upperCaseFirstLetter($serviceName)."Service";
		$this->echoLine("	this.$serviceName = new $serviceClassName(this);");
	}
	
	private function writeMainClassConstructorClosure()
	{
		$this->echoLine("}");
	}
	
	private function writeMainClassClosure()
	{
	}
	
	protected function echoLine($text = "")
	{
		$this->_text .= ($text . "\n");
	}
	
	protected function upperCaseFirstLetter($text)
	{
		if (strlen($text) > 0)
			$text[0] = strtoupper($text[0]);
		return $text;
	}
	
	protected function sortTypesForPhp()
	{
	    // types are order alphabetically, but in php, base types should be defined before inherited types
	    $newTypesList = array();
	    foreach ($this->_types as $typeReflector)
	    {
	        $parentTypeReflector = null;
	        $tempList = array();
	        $tempList[$typeReflector->getType()] = $typeReflector;
	        while(true)
	        {
	            if ($typeReflector->getParentTypeReflector())
	            {
	                $typeReflector = $typeReflector->getParentTypeReflector();
	                $tempList[$typeReflector->getType()] = $typeReflector;
	            }
                else
                    break;
	        }
	        $tempList = array_reverse($tempList);
	        
	        $newTypesList = array_merge($newTypesList, $tempList);
	    }
	    
	    $this->_types = $newTypesList;
	}
}