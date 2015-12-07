<?php
class RubyClientGenerator extends ClientGeneratorFromXml
{
	private $_doc = null;
	
	function __construct($xmlPath, Zend_Config $config, $sourcePath = "sources/ruby")
	{
		parent::__construct($xmlPath, $sourcePath, $config);
		$this->_doc = new KDOMDocument();
		$this->_doc->load($this->_xmlFile);
	}
	
	function getSingleLineCommentMarker()
	{
		return '#';
	}
	
	function generate() 
	{
		parent::generate();
	
		$xpath = new DOMXPath($this->_doc);
		
	    $this->appendLine("require 'kaltura_client_base.rb'");
	    $this->appendLine();
	    $this->appendLine("module Kaltura");
	    $this->appendLine();
	    
		// enumes
		$enumNodes = $xpath->query("/xml/enums/enum[not(@plugin)]");
		foreach($enumNodes as $enumNode)
		{
			$this->writeEnum($enumNode);
		}
		
		// classes
		$classNodes = $xpath->query("/xml/classes/class[not(@plugin)]");
		foreach($classNodes as $classNode)
		{
			$this->writeClass($classNode);
		}
		
		
		$serviceNodes = $xpath->query("/xml/services/service[not(@plugin)]");
		foreach($serviceNodes as $serviceNode)
		{
		    $this->writeService($serviceNode);
		}
		$this->appendLine();
		$serviceNodes = $xpath->query("/xml/services/service[not(@plugin)]");
		$this->writeMainClient($serviceNodes);
	    $this->appendLine();
      	$this->appendLine("end");
	    
    	$this->addFile("lib/kaltura_client.rb", $this->getTextBlock());

		// writing plugins
		$pluginNodes = $xpath->query("/xml/plugins/plugin");
		foreach($pluginNodes as $pluginNode)
		{
			$pluginName = $pluginNode->getAttribute("name");
			
			$this->startNewTextBlock();
			$this->appendLine("require 'kaltura_client.rb'");

			$dependencies = $pluginNode->getElementsByTagName("dependency");		
			foreach($dependencies as $dependency)
			{
				$this->appendLine("require File.dirname(__FILE__) + '/". $this->camelCaseToUnderscoreAndLower("Kaltura".$this->upperCaseFirstLetter($dependency->getAttribute("pluginName"))."ClientPlugin.rb")."'");	
			}
			
			$this->appendLine();			
		    $this->appendLine("module Kaltura");
		    $this->appendLine();
							
			$enumNodes = $xpath->query("/xml/enums/enum[@plugin='$pluginName']");
			foreach($enumNodes as $enumNode)
			{
				$this->writeEnum($enumNode);
			}
			
			$classNodes = $xpath->query("/xml/classes/class[@plugin='$pluginName']");
			foreach($classNodes as $classNode)
			{
				$this->writeClass($classNode);
			}
			
			$serviceNodes = $xpath->query("/xml/services/service[@plugin='$pluginName']");
			foreach($serviceNodes as $serviceNode)
			{
			    $this->writeService($serviceNode);
			}
			
			if($serviceNodes->length > 0){
				$this->appendLine();
				$this->writeMainClient($serviceNodes);
			}
			
			$this->appendLine();
	      	$this->appendLine("end");
			$this->addFile($this->camelCaseToUnderscoreAndLower("lib/kaltura_plugins/Kaltura".$this->upperCaseFirstLetter($pluginName)."ClientPlugin.rb"), $this->getTextBlock());	
		}
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
		
		// comments
		$this->writeComments("	# ", $classNode);

		// class definition
		if ($classNode->hasAttribute("base"))
			$this->appendLine("	class $type < ".$classNode->getAttribute("base"));
		else
			$this->appendLine("	class $type < KalturaObjectBase");
		
		foreach($classNode->childNodes as $propertyNode)
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			// comments
			$this->writeComments("		# ", $propertyNode);

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
			if (!in_array($propType, array("int", "float", "bool", "bigint")))
				continue;
				
			$this->appendLine("		def ".$this->camelCaseToUnderscoreAndLower($propName)."=(val)");
			switch($propType)
			{
				case "bigint":
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
		$serviceId = $serviceNode->getAttribute("id");
		$serviceClassName = "Kaltura".$this->upperCaseFirstLetter($serviceName)."Service";
		
		$this->appendLine();
		// comments
		$this->writeComments("	# ", $serviceNode);

		$this->appendLine("	class $serviceClassName < KalturaServiceBase");
		$this->appendLine("		def initialize(client)");
		$this->appendLine("			super(client)");
		$this->appendLine("		end");	   
		 
		
		$actionNodes = $serviceNode->childNodes;
		foreach($actionNodes as $actionNode)
		{
		    if ($actionNode->nodeType != XML_ELEMENT_NODE)
				continue;
				
		    $this->writeAction($serviceId, $actionNode);
		}
		$this->appendLine("	end");
	}
	
	function writeAction($serviceId, DOMElement $actionNode)
	{
	    $action = $actionNode->getAttribute("name");
	    $resultNode = $actionNode->getElementsByTagName("result")->item(0);
	    $resultType = $resultNode->getAttribute("type");
		
		$signaturePrefix = "def ".$this->camelCaseToUnderscoreAndLower($action)."(";
			
		$paramNodes = $actionNode->getElementsByTagName("param");
		$signature = $this->getSignature($paramNodes);
		
		$this->appendLine();
		// comments
		$this->writeComments("		# ", $actionNode);
	
		$this->appendLine("		$signaturePrefix$signature");
		$this->appendLine("			kparams = {}");
		foreach($paramNodes as $paramNode)
		{
			$paramType = $paramNode->getAttribute("type");
		    $paramName = $paramNode->getAttribute("name");
		    $isEnum = $paramNode->hasAttribute("enumType");
		    
			// comments
			$this->writeComments("			# ", $paramNode);
			
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
		
		$this->appendLine("			client.queue_service_action_call('$serviceId', '$action', kparams);");
		if($resultType == 'file'){
			$this->appendLine("			return client.get_serve_url();");			
		}
		else{
			$this->appendLine("			if (client.is_multirequest)");
			$this->appendLine("				return nil;");
			$this->appendLine("			end");
			$this->appendLine("			return client.do_queue();");			
		}
		$this->appendLine("		end");
	}

	function writeComments($padding, DOMElement $node)
	{
		if($node->hasAttribute("description") && strlen($node->getAttribute("description")) > 0){
			$decoded_comments = html_entity_decode($node->getAttribute("description"));
			if($decoded_comments != "\n" && $decoded_comments != "\r")
			{
				$tok = strtok($decoded_comments, "\n");

				while ($tok !== false) {
					$this->appendLine($padding.$tok);
					$tok = strtok("\n");
				}
			}
		}
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
	    		if ($default === "null")
	    			$default = "KalturaNotImplemented";
	    		else if ($default === "")
	    			$default = "''";
	    		else if ($paramNode->getAttribute("type") == "string")
		    		$default = "'".$default."'";
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
	    $this->appendLine("	class KalturaClient < KalturaClientBase");
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
		$this->appendLine("	end");
	}
}
