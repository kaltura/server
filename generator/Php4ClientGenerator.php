<?php
class Php4ClientGenerator extends ClientGeneratorFromXml
{
	/**
	 * @var DOMDocument
	 */
	private $_doc = null;
	
	function Php4ClientGenerator($xmlPath)
	{
		parent::ClientGeneratorFromXml($xmlPath, realpath("sources/php4"));
		$this->_doc = new DOMDocument();
		$this->_doc->load($this->_xmlFile);
	}
	
	function generate() 
	{
		$xpath = new DOMXPath($this->_doc);
		
		$this->appendLine('<?php');
		$this->appendLine('require_once("KalturaClientBase.php");');
		$this->appendLine('');
	    
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
	    
    	$this->addFile("KalturaClient.php", $this->getTextBlock());
	}
	
	function writeEnum(DOMElement $enumNode)
	{
		$enumName = $enumNode->getAttribute("name");
		foreach($enumNode->childNodes as $constNode)
		{
			if ($constNode->nodeType != XML_ELEMENT_NODE)
				continue;
				
			$propertyName = $constNode->getAttribute("name");
			$propertyValue = $constNode->getAttribute("value");
			if ($enumNode->getAttribute("enumType") == "string")
				$this->appendLine("define(\"".$enumName."_".$propertyName."\", \"$propertyValue\");");
			else
				$this->appendLine("define(\"".$enumName."_".$propertyName."\", $propertyValue);");			
		}
		$this->appendLine();
	}
	
	function writeClass(DOMElement $classNode)
	{
		$type = $classNode->getAttribute("name");
		
		// class definition
		if ($classNode->hasAttribute("base"))
			$this->appendLine("class $type extends " . $classNode->getAttribute("base"));
		else
			$this->appendLine("class $type extends KalturaObjectBase");
		$this->appendLine("{");
		// class properties
		foreach($classNode->childNodes as $propertyNode)
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			$propName = $propertyNode->getAttribute("name");
			$isReadyOnly = $propertyNode->getAttribute("readOnly") == 1;
			$isInsertOnly = $propertyNode->getAttribute("insertOnly") == 1;
			$isEnum = $propertyNode->hasAttribute("enumType");
			if ($isEnum)
				$propType = $propertyNode->getAttribute("enumType");
			else
				$propType = $propertyNode->getAttribute("type");
			$propDescription = $propertyNode->getAttribute("description");
			
			$this->appendLine("	/**");
			$description = $propDescription;
			$description = str_replace("\n", "\n	 * ", $propDescription); // to format multiline descriptions
			$this->appendLine("	 * " . $description);
			$this->appendLine("	 *");
			if ($propType == "array")
				$this->appendLine("	 * @var $propType of {$propertyNode->getAttribute("arrayType")}");
			else
				$this->appendLine("	 * @var $propType");
			if ($isReadyOnly )
				$this->appendLine("	 * @readonly");
			if ($isInsertOnly)
				$this->appendLine("	 * @insertonly");
			$this->appendLine("	 */");
			
			$propertyLine =	"var $$propName";
			
			if ($this->isSimpleType($propType) || $isEnum)
			{
				$propertyLine .= " = null";
			}
			
			$this->appendLine("	$propertyLine;");
			$this->appendLine("");
		}
		$this->appendLine();

		// close class
		$this->appendLine("}");
		$this->appendLine();
	}
	
	function writeService(DOMElement $serviceNode)
	{
		$serviceName = $serviceNode->getAttribute("name");
		
		$serviceClassName = "Kaltura".$this->upperCaseFirstLetter($serviceName)."Service";
		$this->appendLine();		
		$this->appendLine("class $serviceClassName extends KalturaServiceBase");
		$this->appendLine("{");
		$this->appendLine("	function $serviceClassName(&\$client)");
		$this->appendLine("	{");
		$this->appendLine("		parent::KalturaServiceBase(\$client);");
		$this->appendLine("	}");
		
		$actionNodes = $serviceNode->childNodes;
		foreach($actionNodes as $actionNode)
		{
		    if ($actionNode->nodeType != XML_ELEMENT_NODE)
				continue;
				
		    $this->writeAction($serviceName, $actionNode);
		}
		$this->appendLine("}");
	}
	
	function writeAction($serviceName, DOMElement $actionNode)
	{
		$action = $actionNode->getAttribute("name");
	    $resultNode = $actionNode->getElementsByTagName("result")->item(0);
	    $resultType = $resultNode->getAttribute("type");
		
		// method signature
		$signature = "";
		if (in_array($action, array("list", "clone"))) // because list & clone are preserved in PHP
			$signature .= "function ".$action."Action(";
		else
			$signature .= "function ".$action."(";
			
		$paramNodes = $actionNode->getElementsByTagName("param");
		$signature .= $this->getSignature($paramNodes);
		
		$this->appendLine();	
		$this->appendLine("	$signature");
		$this->appendLine("	{");
		
		$this->appendLine("		\$kparams = array();");
		foreach($paramNodes as $paramNode)
		{
			$paramType = $paramNode->getAttribute("type");
		    $paramName = $paramNode->getAttribute("name");
		    $isEnum = $paramNode->hasAttribute("enumType");
		    $isOptional = $paramNode->getAttribute("optional");
			
			if (!$this->isSimpleType($paramType))
			{
				if ($isEnum)
				{
					$this->appendLine("		\$this->client->addParam(\$kparams, \"$paramName\", \$$paramName);");
				}
				else if ($paramType == "array")
				{
					$extraTab = "";
					if ($isOptional)
					{
						$this->appendLine("		if (\$$paramName !== null)");
						$extraTab = "	";
					}
					$this->appendLine("$extraTab		foreach($paramName as \$obj)");
					$this->appendLine("$extraTab		{");
					$this->appendLine("$extraTab			\$this->client->addParam(\$kparams, \"$paramName\", \$obj->toParams());");
					$this->appendLine("$extraTab		}");
				}
				else
				{
					$extraTab = "";
					if ($isOptional)
					{
						$this->appendLine("		if (\$$paramName !== null)");
						$extraTab = "	";
					}
					$this->appendLine("$extraTab		\$this->client->addParam(\$kparams, \"$paramName\", \$$paramName"."->toParams());");
				}
			}
			else
			{
				$this->appendLine("		\$this->client->addParam(\$kparams, \"$paramName\", \$$paramName);");
			}
		}
		$this->appendLine("		\$resultObject = \$this->client->callService(\"".strtolower($serviceName)."\", \"$action\", \$kparams);");
		$this->appendLine("		\$this->client->checkForError(\$resultObject);");
		
		if (!$resultType)
			$resultType = "null";
		
		if ($resultType == 'int')
			$resultType = "integer";
			
		$this->appendLine("		\$this->client->validateObjectType(\$resultObject, \"$resultType\");");
		$this->appendLine("		return \$resultObject;");
		$this->appendLine("	}");
	}
	
	function getSignature($paramNodes)
	{
		$signature = "";
		foreach($paramNodes as $paramNode)
		{
			$paramName = $paramNode->getAttribute("name");
			$paramType = $paramNode->getAttribute("type");
			$defaultValue = $paramNode->getAttribute("default");

			$signature .= "$".$paramName;
			
			if ($paramNode->getAttribute("optional"))
			{
				if ($this->isSimpleType($paramType))
				{
					if ($defaultValue === "false")
						$signature .= " = false";
					else if ($defaultValue === "true")
						$signature .= " = true";
					else if ($defaultValue === "null")
						$signature .= " = null";
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
					$signature .= " = null";
			}
				
			$signature .= ", ";
		}
		if ($this->endsWith($signature, ", "))
			$signature = substr($signature, 0, strlen($signature) - 2);
		$signature .= ")";
		
		return $signature;
	}
	
	function writeMainClient(DOMNodeList $serviceNodes)
	{
		$apiVersion = $this->_doc->documentElement->getAttribute('apiVersion');
	
		$this->appendLine("class KalturaClient extends KalturaClientBase");
		$this->appendLine("{");
		$this->appendLine("	/**");
		$this->appendLine("	 * @var string");
		$this->appendLine("	 */");
		$this->appendLine("	var \$apiVersion = '$apiVersion';");
		$this->appendLine("");
		
		foreach($serviceNodes as $serviceNode)
		{
			$serviceName = $serviceNode->getAttribute("name");
			$description = $serviceNode->getAttribute("description");
			$serviceClassName = "Kaltura".$this->upperCaseFirstLetter($serviceName)."Service";
			$this->appendLine("	/**");
			$description = str_replace("\n", "\n	 * ", $description); // to format multiline descriptions
			$this->appendLine("	 * " . $description);
			$this->appendLine("	 *");
			$this->appendLine("	 * @var $serviceClassName");
			$this->appendLine("	 */");
			$this->appendLine("	var \$$serviceName = null;");
			$this->appendLine("");
		}
		
		$this->appendLine("");
		$this->appendLine("	function KalturaClient(\$config)");
		$this->appendLine("	{");
		$this->appendLine("		parent::KalturaClientBase(/*KalturaConfiguration*/ \$config);");
		foreach($serviceNodes as $serviceNode)
		{
			$serviceName = $serviceNode->getAttribute("name");
			$serviceClassName = "Kaltura".$this->upperCaseFirstLetter($serviceName)."Service";
			$this->appendLine("		\$this->$serviceName = new $serviceClassName(\$this);");
		}
		$this->appendLine("	}");
		$this->appendLine("}");
	}
}
?>