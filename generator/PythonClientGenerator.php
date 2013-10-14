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
		if ($pluginName == '')
		{
			$pluginClassName = "KalturaCoreClient";
			$outputFileName = "KalturaClient/Plugins/Core.py";
		}
		else 
		{
			$pluginClassName = "Kaltura" . ucfirst($pluginName) . "ClientPlugin";
			$outputFileName = "KalturaClient/Plugins/".ucfirst($pluginName).".py";
		}
		
    	$this->startNewTextBlock();
		
		if($this->generateDocs)
		{
			$this->appendLine("# @package $this->package");
			$this->appendLine("# @subpackage $this->subpackage");
		}
		
		if ($pluginName != '')
		{
			$this->appendLine('from Core import *');

			$xpath = new DOMXPath($this->_doc);
			$dependencyNodes = $xpath->query("/xml/plugins/plugin[@name = '$pluginName']/dependency");
			foreach($dependencyNodes as $dependencyNode)
				$this->appendLine('from ' .
					ucfirst($dependencyNode->getAttribute("pluginName")) . 
					' import *');
		}
		$this->appendLine('from ..Base import *');
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
		
		$this->appendLine("    # @return $pluginClassName");
		$this->appendLine('    @staticmethod');
		$this->appendLine('    def get():');
		$this->appendLine("        if $pluginClassName.instance == None:");
		$this->appendLine("            $pluginClassName.instance = $pluginClassName()");
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
		$enumBase = "object";
		
		if($this->generateDocs)
		{
			$this->appendLine("# @package $this->package");
			$this->appendLine("# @subpackage $this->subpackage");
		}
		
	 	$this->appendLine("class $enumName($enumBase):");
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
	
	static function buildMultilineComment($description, $indent = "")
	{
		$description = trim($description);
		if (!$description)
		{
			return "";
		}
		
		$description = str_replace("\n", "\n$indent# ", $description);
		return "$indent# " . $description;
	}
	
	static function buildMultilineString($description, $indent = "")
	{
		$description = trim($description);
		if (!$description)
		{
			return "";
		}
		
		$description = str_replace("\n", "\n$indent", $description);
		
		# make sure the description does not start or end with '"'
		if (kString::beginsWith($description, '"'))
		{
			$description = " " . $description;
		}
		if (kString::endsWith($description, '"'))
		{
			$description .= " ";
		}
		
		return $indent . '"""' . $description . '"""';
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
			
		$description = self::buildMultilineString($classNode->getAttribute("description"), "    ");
		if ($description)
			$this->appendLine($description . "\n");
			
		$this->writeClassCtor($classNode);
		$this->writeClassFromXmlFunc($classNode);
		$this->writeClassToParamsFunc($classNode);
		$this->writeClassGettersAndSetters($classNode);
		
		// close class
		$this->appendLine();
	}
	
	function getParentClassNode(DOMElement $classNode)
	{
		if (!$classNode->hasAttribute("base"))
		{
			return null;
		}

		$base = $classNode->getAttribute("base");
		$xpath = new DOMXPath($this->_doc);
		$parentClass = $xpath->query("/xml/classes/class[@name = '$base']");
		return $parentClass->item(0);
	}
	
	function getCtorArguments(DOMElement $classNode = null, $delimiter = ", ", $argumentPostfix = "")
	{
		if (!$classNode)
		{
			return 'self';
		}
		
		$parentNode = $this->getParentClassNode($classNode);
		if ($parentNode)
		{
			$result = $this->getCtorArguments($parentNode, $delimiter, $argumentPostfix);
		}
		else
		{
			$result = 'self';
		}
		
		foreach($classNode->childNodes as $propertyNode)
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			$propName = $this->replaceReservedWords($propertyNode->getAttribute("name"));
			
			$result .= $delimiter.$propName.$argumentPostfix;
		}
		return $result;
	}
	
	function writeClassCtor(DOMElement $classNode)
	{
		if ($classNode->hasAttribute("base"))
			$base = $classNode->getAttribute ( "base" );
		else
			$base = "KalturaObjectBase";

		$initParams = $this->getCtorArguments($classNode, ",\n            ", "=NotImplemented");
		$baseInitParams = $this->getCtorArguments($this->getParentClassNode($classNode), ",\n            ");
			
		$this->appendLine("    def __init__($initParams):");
		$this->appendLine("        $base.__init__($baseInitParams)");
		$this->appendLine();
		// class properties
		foreach($classNode->childNodes as $propertyNode)
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			$propName = $this->replaceReservedWords($propertyNode->getAttribute("name"));
			$isReadOnly = $propertyNode->getAttribute("readOnly") == 1;
			$isInsertOnly = $propertyNode->getAttribute("insertOnly") == 1;
			$isEnum = $propertyNode->hasAttribute("enumType");
			if ($isEnum)
				$propType = $propertyNode->getAttribute("enumType");
			else
				$propType = $propertyNode->getAttribute("type");
				
			$description = self::buildMultilineComment($propertyNode->getAttribute("description"), "        ");
			if ($description)
				$this->appendLine($description);
			
			if ($propType == "array")
				$this->appendLine("        # @var $propType of {$propertyNode->getAttribute("arrayType")}");
			else
				$this->appendLine("        # @var $propType");
			if ($isReadOnly )
				$this->appendLine("        # @readonly");
			if ($isInsertOnly)
				$this->appendLine("        # @insertonly");
			
			$this->appendLine("        self.$propName = $propName");
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
			
			$propName = $this->replaceReservedWords($propertyNode->getAttribute("name"));
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
			$memberName = $this->replaceReservedWords($propName);
			$isEnum = $propertyNode->hasAttribute ( "enumType" );
			switch ($propType) 
			{
				case "int" :
					if ($isEnum)
					{
						$this->appendLine ( "        kparams.addIntEnumIfDefined(\"$propName\", self.$memberName)" );
					}
					else
						$this->appendLine ( "        kparams.addIntIfDefined(\"$propName\", self.$memberName)" );
					break;
				case "string" :
					if ($isEnum)
					{
						$this->appendLine ( "        kparams.addStringEnumIfDefined(\"$propName\", self.$memberName)" );
					}
					else
						$this->appendLine ( "        kparams.addStringIfDefined(\"$propName\", self.$memberName)" );
					break;
				case "bool" :
					$this->appendLine ( "        kparams.addBoolIfDefined(\"$propName\", self.$memberName)" );
					break;
				case "float" :
					$this->appendLine ( "        kparams.addFloatIfDefined(\"$propName\", self.$memberName)" );
					break;
				case "array":
					$this->appendLine("        kparams.addArrayIfDefined(\"$propName\", self.$memberName)");
					break;
				default :
					$this->appendLine ( "        kparams.addObjectIfDefined(\"$propName\", self.$memberName)" );
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
		
		$description = self::buildMultilineString($serviceNode->getAttribute("description"), "    ");
		if ($description)
			$this->appendLine($description . "\n");
			
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
		
		$description = self::buildMultilineString($actionNode->getAttribute("description"), "        ");
		if ($description)
			$this->appendLine($description . "\n");
			
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
	    
			switch ($paramType) 
			{
				case "string" :
					$this->appendLine ( "        kparams.addStringIfDefined(\"$paramName\", " . $paramName . ")" );
					break;
				case "float" :
					$this->appendLine ( "        kparams.addFloatIfDefined(\"$paramName\", " . $paramName . ")" );
					break;
				case "int" :
					$this->appendLine ( "        kparams.addIntIfDefined(\"$paramName\", " . $paramName . ");" );
					break;
				case "bool" :
					$this->appendLine ( "        kparams.addBoolIfDefined(\"$paramName\", " . $paramName . ");" );
					break;
				case "array" :
					$this->appendLine("        kparams.addArrayIfDefined(\"$paramName\", $paramName)");
					break;
				case "file" :
					$this->appendLine ( "        kfiles.put(\"$paramName\", " . $paramName . ");" );
					break;
				default : // for objects
					$this->appendLine("        kparams.addObjectIfDefined(\"$paramName\", $paramName)");
					break;
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
						$signature .= " = NotImplemented";
					else if ($paramType == "string")
						$signature .= " = \"$defaultValue\"";
					else
					{
						if ($defaultValue == "")
							$signature .= " = \"\""; // hack for partner.getUsage
						else
							$signature .= " = $defaultValue";
					} 
				}
				else
					$signature .= " = NotImplemented";
			}
				
			$signature .= ", ";
		}
		if ($this->endsWith($signature, ", "))
			$signature = substr($signature, 0, strlen($signature) - 2);
		
		return $signature;
	}	
	
	protected function replaceReservedWords($propertyName)
	{
		switch ($propertyName)
		{
			case "not":
				return "{$propertyName}_";
			default:
				return $propertyName;
		}
	}
	
	protected function addFile($fileName, $fileContents, $addLicense = true)
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
		parent::addFile($fileName, $fileContents, $addLicense);
	}
}
