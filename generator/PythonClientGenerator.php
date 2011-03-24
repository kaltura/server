<?php
class PythonClientGenerator extends ClientGeneratorFromXml
{
	/**
	 * @var DOMDocument
	 */
	protected $_doc = null;
	
	function PythonClientGenerator($xmlPath, $sourcePath = null)
	{
		if(!$sourcePath)
			$sourcePath = realpath("sources/python");
			
		parent::ClientGeneratorFromXml($xmlPath, $sourcePath);
		$this->_doc = new DOMDocument();
		$this->_doc->load($this->_xmlFile);
	}
	
	function generate() 
	{
		parent::generate();
	
		$xpath = new DOMXPath($this->_doc);
				
		$enumNodes = $xpath->query("/xml/enums/enum");
		$classNodes = $xpath->query("/xml/classes/class");
		$serviceNodes = $xpath->query("/xml/services/service");
		$this->writePlugin('', $enumNodes, $classNodes, $serviceNodes, $serviceNodes);
		
		// plugins
		$pluginNodes = $xpath->query("/xml/plugins/plugin");
		foreach($pluginNodes as $pluginNode)
		{
			$pluginName = $pluginNode->getAttribute("name");
			$enumNodes = $xpath->query("/xml/enums/enum[@plugin = '$pluginName']");
			$classNodes = $xpath->query("/xml/classes/class[@plugin = '$pluginName']");
			$serviceNodes = $xpath->query("/xml/services/service[@plugin = '$pluginName']");
			$serviceNamesNodes = $xpath->query("/xml/plugins/plugin[@name = '$pluginName']/pluginService");
			$this->writePlugin($pluginName, $enumNodes, $classNodes, $serviceNodes, $serviceNamesNodes);
		}
	}
	
	function writePlugin($pluginName, $enumNodes, $classNodes, $serviceNodes, $serviceNamesNodes)
	{
		$xpath = new DOMXPath($this->_doc);

		if ($pluginName == '')
		{
			$pluginClassName = "KalturaCoreClient";
			$outputFileName = "$pluginClassName.py";
		}
		else 
		{
			$pluginClassName = "Kaltura" . ucfirst($pluginName) . "ClientPlugin";
			$outputFileName = "KalturaPlugins/$pluginClassName.py";
		}
		
    	$this->startNewTextBlock();
		
		if($this->generateDocs)
		{
			$this->appendLine("# @package $this->package");
			$this->appendLine("# @subpackage $this->subpackage");
		}
		
		if ($pluginName != '')
		{
			$this->appendLine('import os.path');
			$this->appendLine('import sys');
			$this->appendLine('');
			$this->appendLine("clientRoot = os.path.normpath(os.path.join(os.path.dirname(__file__), '..'))");
			$this->appendLine("if not clientRoot in sys.path:");
			$this->appendLine("    sys.path.append(clientRoot)");
			$this->appendLine('');
			$this->appendLine('from KalturaCoreClient import *');
		}
		$this->appendLine('from KalturaClientBase import *');
		$this->appendLine('');

		if ($pluginName == '')
		{
			$apiVersion = $this->_doc->documentElement->getAttribute('apiVersion');
			$this->appendLine("API_VERSION = '$apiVersion'");
			$this->appendLine('');
		}
		
		$this->appendLine('########## enums ##########');
		$enums = array();
		foreach($enumNodes as $enumNode)
		{
			if($enumNode->hasAttribute('plugin') && $pluginName == '')
				continue;
				
			$this->writeEnum($enumNode);
			$enums[] = $enumNode->getAttribute("name");
		}
	
		$this->appendLine('########## classes ##########');
		$classes = array();
		foreach($classNodes as $classNode)
		{
			if($classNode->hasAttribute('plugin') && $pluginName == '')
				continue;
				
			$this->writeClass($classNode);
			$classes[] = $classNode->getAttribute("name");
		}
	
		$this->appendLine('########## services ##########');
		foreach($serviceNodes as $serviceNode)
		{
			if($serviceNode->hasAttribute('plugin') && $pluginName == '')
				continue;
				
			$this->writeService($serviceNode);
		}
		
		$services = array();
		foreach($serviceNamesNodes as $serviceNode)
		{
			if($serviceNode->hasAttribute('plugin') && $pluginName == '')
				continue;
				
			$services[] = $serviceNode->getAttribute("name");
		}
			
		$this->appendLine('########## main ##########');
				
		$this->appendLine("class $pluginClassName(KalturaClientPlugin):");
		$this->appendLine("    # $pluginClassName");
		$this->appendLine('    instance = None');
		$this->appendLine('');
		
		$this->appendLine('    def __init__(self, client):');
		$this->appendLine('        KalturaClientPlugin.__init__(self, client)');
		$this->appendLine('');
		$this->appendLine("    # @return $pluginClassName");
		$this->appendLine('    @staticmethod');
		$this->appendLine('    def get(client):');
		$this->appendLine("        if $pluginClassName.instance == None:");
		$this->appendLine("            $pluginClassName.instance = $pluginClassName(client)");
		$this->appendLine("        return $pluginClassName.instance");
		$this->appendLine('');
		$this->appendLine('    # @return array<KalturaServiceBase>');
		$this->appendLine('    def getServices(self):');
		$this->appendLine('        return {');
		foreach($services as $service)
		{
			$serviceName = ucfirst($service);
			$this->appendLine("            '$service': Kaltura{$serviceName}Service,");
		}
		$this->appendLine('        }');
		$this->appendLine('');
		$this->appendLine('    def getEnums(self):');
		$this->appendLine('        return {');
		foreach($enums as $enumName)
		{
			$this->appendLine("            '$enumName': $enumName,");
		}
		$this->appendLine('        }');
		$this->appendLine('');
		$this->appendLine('    def getTypes(self):');
		$this->appendLine('        return {');
		foreach($classes as $className)
		{
			$this->appendLine("            '$className': $className,");
		}
		$this->appendLine('        }');
		$this->appendLine('');
		$this->appendLine('    # @return string');
		$this->appendLine('    def getName(self):');
		$this->appendLine("        return '$pluginName'");
		$this->appendLine('');
		
    	$this->addFile($outputFileName, $this->getTextBlock());
	}
	
	function writeEnum(DOMElement $enumNode)
	{
		$enumName = $enumNode->getAttribute("name");
		
		if($this->generateDocs)
		{
			$this->appendLine("# @package $this->package");
			$this->appendLine("# @subpackage $this->subpackage");
		}
		
	 	$this->appendLine("class $enumName:");
	 	foreach($enumNode->childNodes as $constNode)
		{
			if ($constNode->nodeType != XML_ELEMENT_NODE)
				continue;
				
			$propertyName = $constNode->getAttribute("name");
			$propertyValue = $constNode->getAttribute("value");
			if ($enumNode->getAttribute("enumType") == "string")
				$this->appendLine("    $propertyName = \"$propertyValue\"");
			else
				$this->appendLine("    $propertyName = $propertyValue");
		}		
		$this->appendLine();
		$this->appendLine("    def __init__(self, value):");
		$this->appendLine("        self.value = value");
		$this->appendLine();
		$this->appendLine("    def getValue(self):");
		$this->appendLine("        return self.value");
		$this->appendLine();
	}
	
	function writeClass(DOMElement $classNode)
	{
		$type = $classNode->getAttribute("name");
		
		if($this->generateDocs)
		{
			$this->appendLine("# @package $this->package");
			$this->appendLine("# @subpackage $this->subpackage");
		}
		
		// class definition
		if ($classNode->hasAttribute("base"))
			$this->appendLine("class $type(" . $classNode->getAttribute("base") . "):");
		else
			$this->appendLine("class $type(KalturaObjectBase):");

		$this->writeClassCtor($classNode);
		$this->writeClassFromXmlFunc($classNode);
		$this->writeClassToParamsFunc($classNode);
		$this->writeClassGettersAndSetters($classNode);
		
		// close class
		$this->appendLine();
	}
	
	function writeClassCtor(DOMElement $classNode)
	{
		if ($classNode->hasAttribute("base"))
			$base = $classNode->getAttribute ( "base" );
		else
			$base = "KalturaObjectBase";
			
		$this->appendLine("    def __init__(self):");
		$this->appendLine("        $base.__init__(self)");
		$this->appendLine();
		// class properties
		foreach($classNode->childNodes as $propertyNode)
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			$propName = $propertyNode->getAttribute("name");
			$isReadOnly = $propertyNode->getAttribute("readOnly") == 1;
			$isInsertOnly = $propertyNode->getAttribute("insertOnly") == 1;
			$isEnum = $propertyNode->hasAttribute("enumType");
			if ($isEnum)
				$propType = $propertyNode->getAttribute("enumType");
			else
				$propType = $propertyNode->getAttribute("type");
			$propDescription = $propertyNode->getAttribute("description");
			
			$description = str_replace("\n", "\n        # ", trim($propDescription)); // to format multiline descriptions
			if ($description != "")
				$this->appendLine("        # " . $description);
			if ($propType == "array")
				$this->appendLine("        # @var $propType of {$propertyNode->getAttribute("arrayType")}");
			else
				$this->appendLine("        # @var $propType");
			if ($isReadOnly )
				$this->appendLine("        # @readonly");
			if ($isInsertOnly)
				$this->appendLine("        # @insertonly");
			
			$this->appendLine("        self.$propName = None");
			$this->appendLine("");
		}
		$this->appendLine();
	}

	function writeClassGettersAndSetters(DOMElement $classNode)
	{
		// class properties
		foreach($classNode->childNodes as $propertyNode)
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			$propName = $propertyNode->getAttribute("name");
			$ucPropName = ucfirst($propName);
			$isReadOnly = $propertyNode->getAttribute("readOnly") == 1;
			
			$this->appendLine("    def get$ucPropName(self):");
			$this->appendLine("        return self.$propName");
			$this->appendLine("");
			
			if (!$isReadOnly)
			{
				$this->appendLine("    def set$ucPropName(self, new$ucPropName):");
				$this->appendLine("        self.$propName = new$ucPropName");
				$this->appendLine("");
			}
		}
	}

	function writeClassFromXmlFunc(DOMElement $classNode)
	{
		$type = $classNode->getAttribute("name");
		if ($classNode->hasAttribute("base"))
			$base = $classNode->getAttribute ( "base" );
		else
			$base = "KalturaObjectBase";
			
		$this->appendLine("    PROPERTY_LOADERS = {");
		
		// class properties
		$isFirst = true;
		foreach($classNode->childNodes as $propertyNode)
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
							
			$propType = $propertyNode->getAttribute ( "type" );
			$propName = $propertyNode->getAttribute ( "name" );
			$isEnum = $propertyNode->hasAttribute ( "enumType" );
			
			$curLine = "        '$propName': ";
			
			$isFirst = false;
			
			switch ($propType) 
			{
				case "int" :
					if ($isEnum) 
					{
						$enumType = $propertyNode->getAttribute ( "enumType" );
						$curLine .= "(KalturaEnumsFactory.createInt, \"$enumType\")";
					} 
					else
					{
						$curLine .= "getXmlNodeInt";
					}
					break;
				case "string" :
					if ($isEnum) 
					{
						$enumType = $propertyNode->getAttribute ( "enumType" );
						$curLine .= "(KalturaEnumsFactory.createString, \"$enumType\")";
					} 
					else
					{
						$curLine .= "getXmlNodeText";
					}
					break;
				case "bool" :
					$curLine .= "getXmlNodeBool";
					break;
				case "float" :
					$curLine .= "getXmlNodeFloat";
					break;
				case "array" :
					$arrayType = $propertyNode->getAttribute ( "arrayType" );
					$curLine .= "(KalturaObjectFactory.createArray, $arrayType)";
					break;
				default : // sub object
					$curLine .= "(KalturaObjectFactory.create, $propType)";
					break;
			}
			$curLine .= ", ";
			$this->appendLine($curLine);
		}
		$this->appendLine("    }");
		$this->appendLine();
		$this->appendLine("    def fromXml(self, node):");
		$this->appendLine("        $base.fromXml(self, node)");
		$this->appendLine("        self.fromXmlImpl(node, $type.PROPERTY_LOADERS)");
		$this->appendLine();
	}
	
	function writeClassToParamsFunc(DOMElement $classNode)
	{
		$type = $classNode->getAttribute ( "name" );
		if ($classNode->hasAttribute("base"))
			$base = $classNode->getAttribute ( "base" );
		else
			$base = "KalturaObjectBase";
		
		$this->appendLine ( "    def toParams(self):" );
		$this->appendLine ( "        kparams = $base.toParams(self)" );
		$this->appendLine ( "        kparams.put(\"objectType\", \"$type\")" );
		
		foreach ( $classNode->childNodes as $propertyNode ) 
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			$propReadOnly = $propertyNode->getAttribute ( "readOnly" );
			if ($propReadOnly == "1")
				continue;
			
			$propType = $propertyNode->getAttribute ( "type" );
			$propName = $propertyNode->getAttribute ( "name" );
			$isEnum = $propertyNode->hasAttribute ( "enumType" );
			switch ($propType) 
			{
				case "int" :
					if ($isEnum)
					{
						$this->appendLine ( "        kparams.addIntEnumIfNotNone(\"$propName\", self.$propName)" );
					}
					else
						$this->appendLine ( "        kparams.addIntIfNotNone(\"$propName\", self.$propName)" );
					break;
				case "string" :
					if ($isEnum)
					{
						$this->appendLine ( "        kparams.addStringEnumIfNotNone(\"$propName\", self.$propName)" );
					}
					else
						$this->appendLine ( "        kparams.addStringIfNotNone(\"$propName\", self.$propName)" );
					break;
				case "bool" :
					$this->appendLine ( "        kparams.addBoolIfNotNone(\"$propName\", self.$propName)" );
					break;
				case "float" :
					$this->appendLine ( "        kparams.addFloatIfNotNone(\"$propName\", self.$propName)" );
					break;
				default :
					$this->appendLine ( "        kparams.addObjectIfNotNone(\"$propName\", self.$propName)" );
					break;
			}
		}
		$this->appendLine ( "        return kparams" );
		$this->appendLine ();
	}
	
	function writeService(DOMElement $serviceNode)
	{
		$serviceName = $serviceNode->getAttribute("name");
		$serviceId = $serviceNode->getAttribute("id");
		
		$serviceClassName = "Kaltura".$this->upperCaseFirstLetter($serviceName)."Service";
		$this->appendLine();
		
		if($this->generateDocs)
		{
			$this->appendLine("# @package $this->package");
			$this->appendLine("# @subpackage $this->subpackage");
		}
		
		$this->appendLine("class $serviceClassName(KalturaServiceBase):");
		$this->appendLine("    def __init__(self, client = None):");
		$this->appendLine("        KalturaServiceBase.__init__(self, client)");
		
		$actionNodes = $serviceNode->childNodes;
		foreach($actionNodes as $actionNode)
		{
		    if ($actionNode->nodeType != XML_ELEMENT_NODE)
				continue;
				
		    $this->writeAction($serviceId, $serviceName, $actionNode);
		}
		$this->appendLine("");
	}
	
	function writeAction($serviceId, $serviceName, DOMElement $actionNode)
	{
		$action = $actionNode->getAttribute("name");
	    $resultNode = $actionNode->getElementsByTagName("result")->item(0);
	    $resultType = $resultNode->getAttribute("type");
		
		// method signature
		$signature = "def ".$action."(";
		
		$paramNodes = $actionNode->getElementsByTagName("param");
		$signature .= $this->getSignature($paramNodes);
		$signature .= "):";
		
		$this->appendLine();	
		$this->appendLine("    $signature");		
		$this->appendLine("        kparams = KalturaParams()");
		$haveFiles = false;
		foreach($paramNodes as $paramNode)
		{
			$paramType = $paramNode->getAttribute("type");
		    $paramName = $paramNode->getAttribute("name");
		    $isEnum = $paramNode->hasAttribute("enumType");
		    $isOptional = $paramNode->getAttribute("optional");
			
		    if ($haveFiles === false && $paramType == "file")
	    	{
		        $haveFiles = true;
	        	$this->appendLine("        kfiles = KalturaFiles()");
	    	}
	    
			if (!$this->isSimpleType($paramType))
			{
				if ($isEnum)
				{
					$this->appendLine("        kparams.put(\"$paramName\", $paramName)");
				}
				else if ($paramType == "file")
				{
					$this->appendLine("        kfiles.put(\"$paramName\", $paramName)");
				}
				else if ($paramType == "array")
				{
					$this->appendLine("        kparams.addArrayIfNotNone(\"$paramName\", $paramName)");
				}
				else
				{
					$this->appendLine("        kparams.addObjectIfNotNone(\"$paramName\", $paramName)");
				}
			}
			else
			{
				$this->appendLine("        kparams.put(\"$paramName\", $paramName)");
			}
		}
		
	    if($resultType == 'file')
	    {
			$this->appendLine("        self.client.queueServiceActionCall('" . strtolower($serviceId) . "', '$action', kparams)");
			$this->appendLine('        return self.client.getServeUrl()');
	    }
	    else
	    {
			if ($haveFiles)
				$this->appendLine("        self.client.queueServiceActionCall(\"".strtolower($serviceId)."\", \"$action\", kparams, kfiles)");
			else
				$this->appendLine("        self.client.queueServiceActionCall(\"".strtolower($serviceId)."\", \"$action\", kparams)");
			$this->appendLine("        if self.client.isMultiRequest():");
			$this->appendLine("            return self.client.getMultiRequestResult()");
			$this->appendLine("        resultNode = self.client.doQueue()");
			
			if ($resultType) 
			{
				switch ($resultType) 
				{
					case "array" :
						$arrayType = $resultNode->getAttribute ( "arrayType" );
						$this->appendLine ( "        return KalturaObjectFactory.createArray(resultNode, $arrayType)" );
						break;
					case "int" :
						$this->appendLine ( "        return getXmlNodeInt(resultNode)" );
						break;
					case "float" :
						$this->appendLine ( "        return getXmlNodeFloat(resultNode)" );
						break;
					case "bool" :
						$this->appendLine ( "        return getXmlNodeBool(resultNode)" );
						break;
					case "string" :
						$this->appendLine ( "        return getXmlNodeText(resultNode)" );
						break;
					default :
						$this->appendLine ( "        return KalturaObjectFactory.create(resultNode, $resultType)" );
						break;
				}
			}
	    }
	}
	
	function getSignature($paramNodes)
	{
		$signature = "self, ";
		foreach($paramNodes as $paramNode)
		{
			$paramName = $paramNode->getAttribute("name");
			$paramType = $paramNode->getAttribute("type");
			$defaultValue = $paramNode->getAttribute("default");
						
			$signature .= $paramName;			
			
			if ($paramNode->getAttribute("optional"))
			{
				if ($this->isSimpleType($paramType))
				{
					if ($defaultValue === "false")
						$signature .= " = False";
					else if ($defaultValue === "true")
						$signature .= " = True";
					else if ($defaultValue === "null")
						$signature .= " = None";
					else if ($paramType == "string")
						$signature .= " = \"$defaultValue\"";
					else if ($paramType == "int")
					{
						if ($defaultValue == "")
							$signature .= " = \"\""; // hack for partner.getUsage
						else
							$signature .= " = $defaultValue";
					} 
				}
				else
					$signature .= " = None";
			}
				
			$signature .= ", ";
		}
		if ($this->endsWith($signature, ", "))
			$signature = substr($signature, 0, strlen($signature) - 2);
		
		return $signature;
	}	
	
	protected function addFile($fileName, $fileContents)
	{
		$patterns = array(
			'/@package\s+.+/',
			'/@subpackage\s+.+/',
		);
		$replacements = array(
			'@package ' . $this->package,
			'@subpackage ' . $this->subpackage,
		);
		$fileContents = preg_replace($patterns, $replacements, $fileContents);
		parent::addFile($fileName, $fileContents);
	}
}
