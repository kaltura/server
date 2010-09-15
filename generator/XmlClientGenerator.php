<?php
class XmlClientGenerator extends ClientGeneratorFromPhp  
{
    private $_doc = null;
    private $_xmlElement = null;
    
    public function XmlClientGenerator()
    {
        parent::ClientGeneratorFromPhp();
        $this->_doc = new DOMDocument();
        $this->_doc->formatOutput = true; 
    }
    
	public function generate() 
	{
		$this->load();
		
	    $this->_xmlElement = $this->_doc->createElement("xml");
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
		foreach($this->_services as $serviceReflector)
		{
			if(!isset($this->_includeList[strtolower($serviceReflector->getServiceId())]))
			{
				KalturaLog::debug("Service [" . $serviceReflector->getServiceName() . "] exluded");
				continue;
			}
				
		    $serviceIncludeList = $this->_includeList[strtolower($serviceReflector->getServiceId())];
		    $serviceElement = $this->_doc->createElement("service");
			$serviceElement->setAttribute("id", $serviceReflector->getServiceId());
			$serviceElement->setAttribute("name", $serviceReflector->getServiceName());
			$description = $serviceReflector->getServiceDescription();
		    $description = $this->fixDescription($description);
		    
			$serviceElement->setAttribute("description", $description);
			
			$actions = $serviceReflector->getActions();
			$actions = array_keys($actions);
			foreach($actions as $action)
			{
				if(!isset($serviceIncludeList[strtolower($action)]))
				{
					KalturaLog::debug("Action [" . $serviceReflector->getServiceName() . ".$action] exluded");
					continue;
				}
					
				$actionInfo = $serviceReflector->getActionInfo($action);
				
				if($actionInfo->deprecated || $actionInfo->serverOnly)
					continue;
					
				if (strpos($actionInfo->clientgenerator, "ignore") !== false)
					continue;
					
				$serviceActionElement = $this->getServiceActionElement($serviceReflector, $action);
				$serviceElement->appendChild($serviceActionElement);			
			}
			
			$servicesElement->appendChild($serviceElement);
		}
		
		$this->_xmlElement->appendChild($enumsElement);
		$this->_xmlElement->appendChild($classesElement);
		$this->_xmlElement->appendChild($servicesElement);
		
		$this->addFile("KalturaClient.xml", $this->_doc->saveXML());
	}
	
	private function getEnumElement(KalturaTypeReflector $typeReflector)
	{
	    $enumElement = $this->_doc->createElement("enum");
	    $enumElement->setAttribute("name", $typeReflector->getType()); 
    	if ($typeReflector->isEnum())
			$enumElement->setAttribute("enumType", "int");
		else if ($typeReflector->isStringEnum())
			$enumElement->setAttribute("enumType", "string");
			
		$contants = $typeReflector->getConstants();
		foreach($contants as $contant)
		{
			$name = $contant->getName();
			$value = $contant->getDefaultValue();
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
	    
	    if ($parentTypeReflector)
	    {
            $parentType = $parentTypeReflector->getType();
            $classElement->setAttribute("base", $parentType);    		        
	    }
	    
	    $properties = $typeReflector->getCurrentProperties();
		foreach($properties as $property)
		{
			$propType = $property->getType();
			$propName = $property->getName();
			
			$propertyElement = $this->_doc->createElement("property");
			$propertyElement->setAttribute("name", $propName);
			
			if ($property->isArray())
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
			else
			{
			    $propertyElement->setAttribute("type", $propType);
			}
			
		    $propertyElement->setAttribute("readOnly", $property->isReadOnly() ? "1" : "0");
		    $propertyElement->setAttribute("insertOnly", $property->isInsertOnly() ? "1" : "0");
		    
			$description = $property->getDescription();
		    $description = $this->fixDescription($description);
		    $propertyElement->setAttribute("description", $description);
			
			$classElement->appendChild($propertyElement);
		}
		
		return $classElement;
	}
	
	private function getServiceActionElement(KalturaServiceReflector $serviceReflector, $actionId)
	{
	    $outputTypeReflector = $serviceReflector->getActionOutputType($actionId);
	    $actionInfo = $serviceReflector->getActionInfo($actionId);
		$actionParams = $serviceReflector->getActionParams($actionId);
		
		$outputType = null;
		if ($outputTypeReflector)
			$outputType = $outputTypeReflector->getType();
		
		$actionElement = $this->_doc->createElement("action");
		$actionElement->setAttribute("name", $actionInfo->action);
		
		foreach($actionParams as $actionParam)
		{
			$actionParamElement = $this->_doc->createElement("param");
			$actionParamElement->setAttribute("name", $actionParam->getName());
			if ($actionParam->isArray())
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
			}
			$actionParamElement->setAttribute("optional", $actionParam->isOptional() ? "1" : "0");
			if ($actionParam->isOptional())
			{
			    switch($actionParam->getType())
			    {
			        case "bool":
			            if ($actionParam->getDefaultValue() === true)
		                    $actionParamElement->setAttribute("default", "true");
	                    else if ($actionParam->getDefaultValue() === false)
	                        $actionParamElement->setAttribute("default", "false");
                        break;
			        case "int":
		            case "float":
	                case "string":
		                $actionParamElement->setAttribute("default", $actionParam->getDefaultValue());
                        break;
	                default:
	                    if ($actionParam->isEnum())
                            $actionParamElement->setAttribute("default", $actionParam->getDefaultValue());
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
		    if($outputTypeReflector->isArray())
		    {
		        $resultElement->setAttribute("type", "array");
			    $arrayType = $outputTypeReflector->getArrayType();
		        $resultElement->setAttribute("arrayType", $arrayType);
		    }
		    else 
		    {
		        $resultElement->setAttribute("type", $outputType);
		    }
		}
		
		
		$description = $actionInfo->description;
		$description = $this->fixDescription($description);
	    $actionElement->setAttribute("description", $description);
		
		$actionElement->appendChild($resultElement);
		
		return $actionElement;
	}
	
	private function fixDescription($description)
	{
	    $description = str_replace("\n\r", "\n", $description);
	    $description = str_replace("\r\n", "\n", $description);
	    //$description = substr($description, 0, strlen($description) - 1);
	    return $description;
	}
	
	protected function writeHeader() { }

	protected function writeFooter() { }
	
	protected function writeBeforeServices() { }
	
	protected function writeBeforeService(KalturaServiceReflector $serviceReflector) { }
	
	protected function writeServiceAction($serviceId, $serviceName, $action, $actionParams, $outputTypeReflector) { }
	
	protected function writeAfterService(KalturaServiceReflector $serviceReflector) { }
	
	protected function writeAfterServices() { }
	
	protected function writeBeforeTypes() { }
	
	protected function writeType(KalturaTypeReflector $type) { }
	
	protected function writeAfterTypes() { }
}