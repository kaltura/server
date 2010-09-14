<?php
class RubyClientGenerator extends ClientGeneratorFromXml
{
	private $_doc = null;
	
	function RubyClientGenerator($xmlPath)
	{
		parent::ClientGeneratorFromXml($xmlPath, realpath("sources/ruby"));
		$this->_doc = new DOMDocument();
		$this->_doc->load($this->_xmlFile);
	}
	
	function generate() 
	{
		$xpath = new DOMXPath($this->_doc);
		
	    $this->appendLine("require 'kaltura_client_base.rb'");
	    $this->appendLine();
	    $this->appendLine("module Kaltura");
	    $this->appendLine();
	    
		// enumes
		$enumNodes = $xpath->query("/xml/enums/enum");
		foreach($enumNodes as $enumNode)
		{
			$this->writeEnum($enumNode);
		}
		
		// classes
		$classNodes = $xpath->query("/xml/classes/class");
		foreach($classNodes as $classNode)
		{
			$this->writeClass($classNode);
		}
		
		
		$serviceNodes = $xpath->query("/xml/services/service");
		foreach($serviceNodes as $serviceNode)
		{
		    $this->writeService($serviceNode);
		}
		$this->appendLine();
	    $this->writeMainClient($serviceNodes);
	    $this->appendLine();
      	$this->appendLine("end");
	    
    	$this->addFile("kaltura_client.rb", $this->getTextBlock());
	}
	
	function writeEnum(DOMElement $enumNode)
	{
		$enumName = $enumNode->getAttribute("name");
	 	$this->appendLine("	class $enumName");		
		
		foreach($enumNode->childNodes as $constNode)
		{
			if ($constNode->nodeType != XML_ELEMENT_NODE)
				continue;
				
			$propertyName = $constNode->getAttribute("name");
			$propertyValue = $constNode->getAttribute("value");
			if ($enumNode->getAttribute("enumType") == "string")
			$this->appendLine("		$propertyName = \"$propertyValue\"");
			else
			$this->appendLine("		$propertyName = $propertyValue");
		}
		
		$this->appendLine("	end");
		$this->appendLine();
	}
	
	function writeClass(DOMElement $classNode)
	{
		$type = $classNode->getAttribute("name");
		
		// class definition
		if ($classNode->hasAttribute("base"))
			$this->appendLine("	class $type < ".$classNode->getAttribute("base"));
		else
			$this->appendLine("	class $type < KalturaObjectBase");
		
		foreach($classNode->childNodes as $propertyNode)
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			$propName = $propertyNode->getAttribute("name");
			
			$this->appendLine("		attr_accessor :".$this->camelCaseToUnderscoreAndLower($propName));
		}
		
		$this->appendLine();
		
		foreach($classNode->childNodes as $propertyNode)
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			$propName = $propertyNode->getAttribute("name");
			$propType = $propertyNode->getAttribute("type");
			if (!in_array($propType, array("int", "float", "bool")))
				continue;
				
			$this->appendLine("		def ".$this->camelCaseToUnderscoreAndLower($propName)."=(val)");
			switch($propType)
			{
				case "int":
					$this->appendLine("			@".$this->camelCaseToUnderscoreAndLower($propName)." = val.to_i");
					break;
				case "float":
					$this->appendLine("			@".$this->camelCaseToUnderscoreAndLower($propName)." = val.to_f");
					break;
				case "bool":
					$this->appendLine("			@".$this->camelCaseToUnderscoreAndLower($propName)." = to_b(val)");
					break;
			}
			$this->appendLine("		end");
		}
		$this->appendLine("	end");
		$this->appendLine();
	}
	
	function writeService(DOMElement $serviceNode)
	{
		$serviceName = $serviceNode->getAttribute("name");
		$serviceClassName = "Kaltura".$this->upperCaseFirstLetter($serviceName)."Service";
		
		$this->appendLine();
		$this->appendLine("	class $serviceClassName < KalturaServiceBase");
		$this->appendLine("		def initialize(client)");
		$this->appendLine("			super(client)");
		$this->appendLine("		end");	   
		 
		
		$actionNodes = $serviceNode->childNodes;
		foreach($actionNodes as $actionNode)
		{
		    if ($actionNode->nodeType != XML_ELEMENT_NODE)
				continue;
				
		    $this->writeAction($serviceName, $actionNode);
		}
		$this->appendLine("	end");
	}
	
	function writeAction($serviceName, DOMElement $actionNode)
	{
	    $action = $actionNode->getAttribute("name");
	    $resultNode = $actionNode->getElementsByTagName("result")->item(0);
	    $resultType = $resultNode->getAttribute("type");
		
		$signaturePrefix = "def ".$this->camelCaseToUnderscoreAndLower($action)."(";
			
		$paramNodes = $actionNode->getElementsByTagName("param");
		$signature = $this->getSignature($paramNodes);
		
		$this->appendLine();	
		$this->appendLine("		$signaturePrefix$signature");
		$this->appendLine("			kparams = {}");
		foreach($paramNodes as $paramNode)
		{
			$paramType = $paramNode->getAttribute("type");
		    $paramName = $paramNode->getAttribute("name");
		    $isEnum = $paramNode->hasAttribute("enumType");
		    
	        switch ($paramType)
	        {
                case "array":
                    $this->appendLine("			$paramName.each do |obj|");
                    $this->appendLine("				client.add_param(kparams, '$paramName', obj);");
                    $this->appendLine("			end");
                    break;
                default: 
                    $this->appendLine("			client.add_param(kparams, '$paramName', ".$this->camelCaseToUnderscoreAndLower($paramName).");");
                    break;
		    }
		}
		
		$this->appendLine("			client.queue_service_action_call('$serviceName', '$action', kparams);");
		$this->appendLine("			if (client.is_multirequest)");
		$this->appendLine("				return nil;");
		$this->appendLine("			end");
		$this->appendLine("			return client.do_queue();"); 
		$this->appendLine("		end");
	}
	
	function getSignature($paramNodes)
	{
		$signature = "(";
		$params = array();
		foreach($paramNodes as $paramNode)
		{
		    $paramName = $paramNode->getAttribute("name");
		    if ($paramNode->getAttribute("optional"))
		    {
		    	$default = $paramNode->getAttribute("default");
		    	if ($paramNode->getAttribute("type") == "string")
		    		$default = "'".$default."'";
	    		else if ($default === "")
	    			$default = "''";
	    		elseif ($default === "null")
	    			$default = "nil";
		    	$params[] = $this->camelCaseToUnderscoreAndLower($paramName) . "=" . $default;
		    }
		    else
		    	$params[] = $this->camelCaseToUnderscoreAndLower($paramName);
		}
		$signature = implode(", ", $params);
		$signature .= ")";
		
		return $signature;
	}
	
	function writeMainClient(DOMNodeList  $serviceNodes)
	{
	    $this->appendLine("    class KalturaClient < KalturaClientBase");
	    foreach($serviceNodes as $serviceNode)
		{
		    $serviceName = $serviceNode->getAttribute("name");
		    $rubyServiceName = $this->camelCaseToUnderscoreAndLower($serviceName."Service");
		    $serviceClass = "Kaltura".$this->upperCaseFirstLetter($serviceName)."Service";
			$this->appendLine("		attr_reader :$rubyServiceName");
    		$this->appendLine("		def $rubyServiceName");
    		$this->appendLine("			if (@$rubyServiceName == nil)");
    		$this->appendLine("				@$rubyServiceName = $serviceClass.new(self)");
    		$this->appendLine("			end");
    		$this->appendLine("			return @$rubyServiceName");
    		$this->appendLine("		end");
		}
		$this->appendLine("    end");
	}
}
?>