<?php
class PhpZendClientGenerator extends ClientGeneratorFromXml
{
	private $cacheTypes = array();
	
	/**
	 * @var DOMDocument
	 */
	protected $_doc = null;
	
	function PhpZendClientGenerator($xmlPath, $sourcePath = null)
	{
		if(!$sourcePath)
			$sourcePath = realpath("sources/zend");
			
		parent::ClientGeneratorFromXml($xmlPath, $sourcePath);
		$this->_doc = new KDOMDocument();
		$this->_doc->load($this->_xmlFile);
	}
	
	function getSingleLineCommentMarker()
	{
		return '//';
	}
	
	private function cacheEnum(DOMElement $enumNode)
	{
		$enumName = $enumNode->getAttribute('name');
		$enumCacheName = preg_replace('/^Kaltura(.+)$/', '$1', $enumName); 
		
		if($enumNode->hasAttribute('plugin'))
		{
			$pluginName = ucfirst($enumNode->getAttribute('plugin'));
			$this->cacheTypes[$enumName] = "Kaltura_Client_{$pluginName}_Enum_{$enumCacheName}";
		}
		else
		{
			$this->cacheTypes[$enumName] = "Kaltura_Client_Enum_{$enumCacheName}";	
		}
	} 
	
	private function cacheType(DOMElement $classNode)
	{
		$className = $classNode->getAttribute('name');
		$classCacheName = preg_replace('/^Kaltura(.+)$/', '$1', $className); 
		
		if($classNode->hasAttribute('plugin'))
		{
			$pluginName = ucfirst($classNode->getAttribute('plugin'));
			$this->cacheTypes[$className] = "Kaltura_Client_{$pluginName}_Type_{$classCacheName}";
		}
		else
		{
			$this->cacheTypes[$className] = "Kaltura_Client_Type_{$classCacheName}";	
		}
	} 
	
	function generate() 
	{
		parent::generate();
	
		$xpath = new DOMXPath($this->_doc);
		
		$enumNodes = $xpath->query("/xml/enums/enum");
		foreach($enumNodes as $enumNode)
			$this->cacheEnum($enumNode);
			
		$classNodes = $xpath->query("/xml/classes/class");
		foreach($classNodes as $classNode)
			$this->cacheType($classNode);
		
    	$this->startNewTextBlock();
		$this->appendLine('<?php');
		
		if($this->generateDocs)
		{
			$this->appendLine('/**');
			$this->appendLine(" * @package $this->package");
			$this->appendLine(" * @subpackage $this->subpackage");
			$this->appendLine(' */');
		}
			
		$this->appendLine('class Kaltura_Client_TypeMap');
		$this->appendLine('{');
		
		$classNodes = $xpath->query("/xml/classes/class");
		$this->appendLine('	private static $map = array(');
		$typeMap = array();
		foreach($classNodes as $classNode)
		{
			$kalturaType = $classNode->getAttribute('name');
			$zendType = $this->getTypeClass($kalturaType);
			$typeMap[$kalturaType] = $zendType;
		}
		ksort($typeMap);
		foreach ($typeMap as $kalturaType => $zendType)
			$this->appendLine("		'$kalturaType' => '$zendType',");
		
		$this->appendLine('	);');
		$this->appendLine('	');
		
		$this->appendLine('	public static function getZendType($kalturaType)');
		$this->appendLine('	{');
		$this->appendLine('		if(isset(self::$map[$kalturaType]))');
		$this->appendLine('			return self::$map[$kalturaType];');
		$this->appendLine('		return null;');
		$this->appendLine('	}');
		$this->appendLine('}');
		
    	$this->addFile($this->getMapPath(), $this->getTextBlock());
			
		// enumes
		$enumNodes = $xpath->query("/xml/enums/enum");
		foreach($enumNodes as $enumNode)
		{
    		$this->startNewTextBlock();
			$this->appendLine('<?php');
			$this->writeEnum($enumNode);
    		$this->addFile($this->getEnumPath($enumNode), $this->getTextBlock());
		}
    	
		// classes
		$classNodes = $xpath->query("/xml/classes/class");
		foreach($classNodes as $classNode)
		{
	    	$this->startNewTextBlock();
			$this->appendLine('<?php');
			$this->writeClass($classNode);
    		$this->addFile($this->getTypePath($classNode), $this->getTextBlock());
		}
		
		// services
		$serviceNodes = $xpath->query("/xml/services/service");
		foreach($serviceNodes as $serviceNode)
		{
	    	$this->startNewTextBlock();
			$this->appendLine('<?php');
		    $this->writeService($serviceNode);
    		$this->addFile($this->getServicePath($serviceNode), $this->getTextBlock());
		}
		
    	$this->startNewTextBlock();
		$this->appendLine('<?php');
	    $this->writeMainClient($serviceNodes);
    	$this->addFile($this->getMainPath(), $this->getTextBlock());
    	
    	
		// plugins
		$pluginNodes = $xpath->query("/xml/plugins/plugin");
		foreach($pluginNodes as $pluginNode)
		{
		    $this->writePlugin($pluginNode);
		}
	}
	
	protected function getEnumPath(DOMElement $enumNode)
	{
		$enumName = $enumNode->getAttribute('name');
		$enumName = preg_replace('/^Kaltura(.+)$/', '$1', $enumName); 
			
		if(!$enumNode->hasAttribute('plugin'))
			return "Kaltura/Client/Enum/{$enumName}.php";

		$pluginName = ucfirst($enumNode->getAttribute('plugin'));
		return "Kaltura/Client/{$pluginName}/Enum/{$enumName}.php";
	}
	
	protected function getTypePath(DOMElement $classNode)
	{
		$className = $classNode->getAttribute('name');
		$className = preg_replace('/^Kaltura(.+)$/', '$1', $className); 
			
		if(!$classNode->hasAttribute('plugin'))
			return "Kaltura/Client/Type/{$className}.php";

		$pluginName = ucfirst($classNode->getAttribute('plugin'));
		return "Kaltura/Client/{$pluginName}/Type/{$className}.php";
	}
	
	protected function getServicePath($serviceNode)
	{
		$serviceName = ucfirst($serviceNode->getAttribute('name'));
			
		if(!$serviceNode->hasAttribute('plugin'))
			return "Kaltura/Client/{$serviceName}Service.php";

		$pluginName = ucfirst($serviceNode->getAttribute('plugin'));
		return "Kaltura/Client/{$pluginName}/{$serviceName}Service.php";
	}
	
	protected function getPluginPath($pluginName)
	{
		$pluginName = ucfirst($pluginName);
		return "Kaltura/Client/{$pluginName}/Plugin.php";
	}
	
	protected function getMainPath()
	{
		return 'Kaltura/Client/Client.php';
	}
	
	protected function getMapPath()
	{
		return 'Kaltura/Client/TypeMap.php';
	}
	
	protected function getEnumClass($enumName)
	{
		if(!isset($this->cacheTypes[$enumName]))
			return $enumName; 
		
		return $this->cacheTypes[$enumName];
	}
	
	protected function getTypeClass($className)
	{
		if(!isset($this->cacheTypes[$className]))
			return $className; 
		
		return $this->cacheTypes[$className];
	}
	
	protected function getServiceClass(DOMElement $serviceNode)
	{
		$serviceName = ucfirst($serviceNode->getAttribute('name'));
		
		if(!$serviceNode->hasAttribute('plugin'))
			return "Kaltura_Client_{$serviceName}Service";
		
		$pluginName = ucfirst($serviceNode->getAttribute('plugin'));
		return "Kaltura_Client_{$pluginName}_{$serviceName}Service";
	}
	
	protected function getPluginClass($pluginName)
	{
		$pluginName = ucfirst($pluginName);
		return "Kaltura_Client_{$pluginName}_Plugin";
	}
	
	function writePlugin(DOMElement $pluginNode)
	{
		$xpath = new DOMXPath($this->_doc);
		
		$pluginName = $pluginNode->getAttribute("name");
		$pluginClassName = $this->getPluginClass($pluginName);
		
    	$this->startNewTextBlock();
		$this->appendLine('<?php');
		
		if($this->generateDocs)
		{
			$this->appendLine('/**');
			$this->appendLine(" * @package $this->package");
			$this->appendLine(" * @subpackage $this->subpackage");
			$this->appendLine(' */');
		}
		
		$this->appendLine("class $pluginClassName extends Kaltura_Client_Plugin");
		$this->appendLine('{');
	
		$serviceNodes = $xpath->query("/xml/services/service[@plugin = '$pluginName']");
//		$serviceNodes = $xpath->query("/xml/plugins/plugin[@name = '$pluginName']/pluginService");
		foreach($serviceNodes as $serviceNode)
		{
			$serviceAttribute = $serviceNode->getAttribute("name");
			$serviceClass = $this->getServiceClass($serviceNode);
			$this->appendLine('	/**');
			$this->appendLine("	 * @var $serviceClass");
			$this->appendLine('	 */');
			$this->appendLine("	public \${$serviceAttribute} = null;");
			$this->appendLine('');
		}
		
		$this->appendLine('	protected function __construct(Kaltura_Client_Client $client)');
		$this->appendLine('	{');
		$this->appendLine('		parent::__construct($client);');
		foreach($serviceNodes as $serviceNode)
		{
			$serviceAttribute = $serviceNode->getAttribute("name");
			$serviceClass = $this->getServiceClass($serviceNode);
			$this->appendLine("		\$this->$serviceAttribute = new $serviceClass(\$client);");
		}
		$this->appendLine('	}');
		$this->appendLine('');
		$this->appendLine('	/**');
		$this->appendLine("	 * @return $pluginClassName");
		$this->appendLine('	 */');
		$this->appendLine('	public static function get(Kaltura_Client_Client $client)');
		$this->appendLine('	{');
		$this->appendLine("		return new $pluginClassName(\$client);");
		$this->appendLine('	}');
		$this->appendLine('');
		$this->appendLine('	/**');
		$this->appendLine('	 * @return array<Kaltura_Client_ServiceBase>');
		$this->appendLine('	 */');
		$this->appendLine('	public function getServices()');
		$this->appendLine('	{');
		$this->appendLine('		$services = array(');
		foreach($serviceNodes as $serviceNode)
		{
			$serviceAttribute = $serviceNode->getAttribute("name");
			$this->appendLine("			'$serviceAttribute' => \$this->$serviceAttribute,");
		}
		$this->appendLine('		);');
		$this->appendLine('		return $services;');
		$this->appendLine('	}');
		$this->appendLine('');
		$this->appendLine('	/**');
		$this->appendLine('	 * @return string');
		$this->appendLine('	 */');
		$this->appendLine('	public function getName()');
		$this->appendLine('	{');
		$this->appendLine("		return '$pluginName';");
		$this->appendLine('	}');
		$this->appendLine('}');
		$this->appendLine('');
		
    	$this->addFile($this->getPluginPath($pluginName), $this->getTextBlock());
	}

	
	function writeEnum(DOMElement $enumNode)
	{
		$enumName = $this->getEnumClass($enumNode->getAttribute('name'));
		
		if($this->generateDocs)
		{
			$this->appendLine('/**');
			$this->appendLine(" * @package $this->package");
			$this->appendLine(" * @subpackage $this->subpackage");
			$this->appendLine(' */');
		}
		
	 	$this->appendLine("class $enumName");		
		$this->appendLine("{");
		foreach($enumNode->childNodes as $constNode)
		{
			if ($constNode->nodeType != XML_ELEMENT_NODE)
				continue;
				
			$propertyName = $constNode->getAttribute("name");
			$propertyValue = $constNode->getAttribute("value");
			if ($enumNode->getAttribute("enumType") == "string")
				$this->appendLine("	const $propertyName = \"$propertyValue\";");
			else
				$this->appendLine("	const $propertyName = $propertyValue;");
		}
		$this->appendLine("}");
		$this->appendLine();
	}
	
	function writeClass(DOMElement $classNode)
	{
		$kalturaType = $classNode->getAttribute('name');
		$type = $this->getTypeClass($kalturaType);
		
		$abstract = '';
		if ($classNode->hasAttribute("abstract"))
			$abstract = 'abstract ';
			
		if($this->generateDocs)
		{
			$this->appendLine('/**');
			$this->appendLine(" * @package $this->package");
			$this->appendLine(" * @subpackage $this->subpackage");
			$this->appendLine(' */');
		}
		
		// class definition
		$baseClass = 'Kaltura_Client_ObjectBase';
		if ($classNode->hasAttribute('base'))
			$baseClass = $this->getTypeClass($classNode->getAttribute('base'));
			
		$this->appendLine($abstract . "class $type extends $baseClass");
		$this->appendLine("{");
		$this->appendLine("	public function getKalturaObjectType()");
		$this->appendLine("	{");
		$this->appendLine("		return '$kalturaType';");
		$this->appendLine("	}");
		$this->appendLine("	");
	
		$this->appendLine('	public function __construct(SimpleXMLElement $xml = null)');
		$this->appendLine('	{');
		$this->appendLine('		parent::__construct($xml);');
		$this->appendLine('		');
		$this->appendLine('		if(is_null($xml))');
		$this->appendLine('			return;');
		$this->appendLine('		');
		
		foreach($classNode->childNodes as $propertyNode)
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			$propName = $propertyNode->getAttribute("name");
			$isEnum = $propertyNode->hasAttribute("enumType");
			$propType = $this->getTypeClass($propertyNode->getAttribute("type"));
		
			switch ($propType) 
			{
				case "int" :
				case "float" :
					$this->appendLine("		if(count(\$xml->{$propName}))");
					$this->appendLine("			\$this->$propName = ($propType)\$xml->$propName;");
					break;
					
				case "bigint" :
					$this->appendLine("		if(count(\$xml->{$propName}))");
					$this->appendLine("			\$this->$propName = (int)\$xml->$propName;");
					break;
					
				case "bool" :
					$this->appendLine("		if(!empty(\$xml->{$propName}))");
					$this->appendLine("			\$this->$propName = true;");
					break;
					
				case "string" :
					$this->appendLine("		\$this->$propName = ($propType)\$xml->$propName;");
					break;
					
				case "array" :
					$this->appendLine("		if(empty(\$xml->{$propName}))");
					$this->appendLine("			\$this->$propName = array();");
					$this->appendLine("		else");
					$this->appendLine("			\$this->$propName = Kaltura_Client_Client::unmarshalItem(\$xml->$propName);");
					break;
					
				default : // sub object
					$this->appendLine("		if(!empty(\$xml->{$propName}))");
					$this->appendLine("			\$this->$propName = Kaltura_Client_Client::unmarshalItem(\$xml->$propName);");
					break;
			}
			
			
		}
		
		$this->appendLine('	}');
		
		// class properties
		foreach($classNode->childNodes as $propertyNode)
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			$propName = $propertyNode->getAttribute("name");
			$isReadyOnly = $propertyNode->getAttribute("readOnly") == 1;
			$isInsertOnly = $propertyNode->getAttribute("insertOnly") == 1;
			$isEnum = $propertyNode->hasAttribute("enumType");
			$propType = null;
			if ($isEnum)
				$propType = $propertyNode->getAttribute("enumType");
			else
				$propType = $propertyNode->getAttribute("type");
			$propType = $this->getTypeClass($propType);
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
			
			$propertyLine =	"public $$propName";
			
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
		$plugin = null;
		if($serviceNode->hasAttribute('plugin'))
			$plugin = $serviceNode->getAttribute('plugin');
			
		$serviceName = $serviceNode->getAttribute("name");
		$serviceId = $serviceNode->getAttribute("id");
					
		$serviceClassName = $this->getServiceClass($serviceNode, $plugin);
		$this->appendLine();
		
		if($this->generateDocs)
		{
			$this->appendLine('/**');
			$this->appendLine(" * @package $this->package");
			$this->appendLine(" * @subpackage $this->subpackage");
			$this->appendLine(' */');
		}
		
		$this->appendLine("class $serviceClassName extends Kaltura_Client_ServiceBase");
		$this->appendLine("{");
		$this->appendLine("	function __construct(Kaltura_Client_Client \$client = null)");
		$this->appendLine("	{");
		$this->appendLine("		parent::__construct(\$client);");
		$this->appendLine("	}");
		
		$actionNodes = $serviceNode->childNodes;
		foreach($actionNodes as $actionNode)
		{
		    if ($actionNode->nodeType != XML_ELEMENT_NODE)
				continue;
				
		    $this->writeAction($serviceId, $serviceName, $actionNode);
		}
		$this->appendLine("}");
	}
	
	function writeAction($serviceId, $serviceName, DOMElement $actionNode, $plugin = null)
	{
		$action = $actionNode->getAttribute("name");
	    $resultNode = $actionNode->getElementsByTagName("result")->item(0);
	    $resultType = $resultNode->getAttribute("type");
		
		// method signature
		$signature = "";
		if (in_array($action, array("list", "clone", "goto"))) // because list & clone are preserved in PHP
			$signature .= "function ".$action."Action(";
		else
			$signature .= "function ".$action."(";
			
		$paramNodes = $actionNode->getElementsByTagName("param");
		$signature .= $this->getSignature($paramNodes);
		
		$this->appendLine();	
		$this->appendLine("	$signature");
		$this->appendLine("	{");
		
		$this->appendLine("		\$kparams = array();");
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
	        	$this->appendLine("		\$kfiles = array();");
	    	}
	    
			if (!$this->isSimpleType($paramType))
			{
				if ($isEnum)
				{
					$this->appendLine("		\$this->client->addParam(\$kparams, \"$paramName\", \$$paramName);");
				}
				else if ($paramType == "file")
				{
					$this->appendLine("		\$this->client->addParam(\$kfiles, \"$paramName\", \$$paramName);");
				}
				else if ($paramType == "array")
				{
					$extraTab = "";
					if ($isOptional)
					{
						$this->appendLine("		if (\$$paramName !== null)");
						$extraTab = "	";
					}
					$this->appendLine("$extraTab		foreach(\$$paramName as \$index => \$obj)");
					$this->appendLine("$extraTab		{");
					$this->appendLine("$extraTab			\$this->client->addParam(\$kparams, \"$paramName:\$index\", \$obj->toParams());");
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
		
	    if($resultType == 'file')
	    {
			$this->appendLine("		\$this->client->queueServiceActionCall('" . strtolower($serviceId) . "', '$action', \$kparams);");
			$this->appendLine('		$resultObject = $this->client->getServeUrl();');
	    }
	    else
	    {
			if ($haveFiles)
				$this->appendLine("		\$this->client->queueServiceActionCall(\"".strtolower($serviceId)."\", \"$action\", \$kparams, \$kfiles);");
			else
				$this->appendLine("		\$this->client->queueServiceActionCall(\"".strtolower($serviceId)."\", \"$action\", \$kparams);");
			$this->appendLine("		if (\$this->client->isMultiRequest())");
			$this->appendLine("			return \$this->client->getMultiRequestResult();");
			$this->appendLine("		\$resultObject = \$this->client->doQueue();");
			$this->appendLine("		\$this->client->throwExceptionIfError(\$resultObject);");
			
			switch($resultType)
			{
				case 'int':
					$this->appendLine("		\$this->client->validateObjectType(\$resultObject, \"integer\");");
					break;
				
				case 'bool':
					$this->appendLine("		\$resultObject = (bool) \$resultObject;");
					break;
				
				case 'string':
				case 'array':
					$this->appendLine("		if(!\$resultObject)");
					$this->appendLine("			\$resultObject = array();");
					$this->appendLine("		\$this->client->validateObjectType(\$resultObject, \"$resultType\");");
					break;
				
				default:
					if ($resultType)
					{
						$resultType = $this->getTypeClass($resultType);
						$this->appendLine("		\$this->client->validateObjectType(\$resultObject, \"$resultType\");");
					}
			}
	    }
			
		$this->appendLine("		return \$resultObject;");
		$this->appendLine("	}");
	}
	
	function getSignature($paramNodes, $plugin = null)
	{
		$signature = "";
		foreach($paramNodes as $paramNode)
		{
			$paramName = $paramNode->getAttribute("name");
			$paramType = $paramNode->getAttribute("type");
			$defaultValue = $paramNode->getAttribute("default");
						
			if ($this->isSimpleType($paramType) || $paramType == "file")
			{
				$signature .= "$".$paramName;
			}
			else if ($paramType == "array")
			{
				$signature .= "array $".$paramName;
			}
			else
			{
				$typeClass = $this->getTypeClass($paramType);
				$signature .= $typeClass." $".$paramName;
			}
			
			
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
					else if ($paramType == "int" || $paramType == "bigint" || $paramType == "float")
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
		$mainClassName = 'Kaltura_Client_Client';
		$apiVersion = $this->_doc->documentElement->getAttribute('apiVersion');
		
		if($this->generateDocs)
		{
			$this->appendLine('/**');
			$this->appendLine(" * @package $this->package");
			$this->appendLine(" * @subpackage $this->subpackage");
			$this->appendLine(' */');
		}
		
		$this->appendLine("class $mainClassName extends Kaltura_Client_ClientBase");
		$this->appendLine("{");
		$this->appendLine("	/**");
		$this->appendLine("	 * @var string");
		$this->appendLine("	 */");
		$this->appendLine("	protected \$apiVersion = '$apiVersion';");
		$this->appendLine("");
	
		foreach($serviceNodes as $serviceNode)
		{
			if($serviceNode->hasAttribute("plugin"))
				continue;
				
			$serviceName = $serviceNode->getAttribute("name");
			$description = $serviceNode->getAttribute("description");
			$serviceClassName = "Kaltura_Client_".ucfirst($serviceName)."Service";
			$this->appendLine("	/**");
			$description = str_replace("\n", "\n	 * ", $description); // to format multiline descriptions
			$this->appendLine("	 * " . $description);
			$this->appendLine("	 * @var $serviceClassName");
			$this->appendLine("	 */");
			$this->appendLine("	public \$$serviceName = null;");
			$this->appendLine("");
		}
		
		$this->appendLine("	/**");
		$this->appendLine("	 * Kaltura client constructor");
		$this->appendLine("	 *");
		$this->appendLine("	 * @param Kaltura_Client_Configuration \$config");
		$this->appendLine("	 */");
		$this->appendLine("	public function __construct(Kaltura_Client_Configuration \$config)");
		$this->appendLine("	{");
		$this->appendLine("		parent::__construct(\$config);");
		$this->appendLine("		");
		
		foreach($serviceNodes as $serviceNode)
		{
			if($serviceNode->hasAttribute("plugin"))
				continue;
				
			$serviceName = $serviceNode->getAttribute("name");
			$serviceClassName = "Kaltura_Client_".ucfirst($serviceName)."Service";
			$this->appendLine("		\$this->$serviceName = new $serviceClassName(\$this);");
		}
		$this->appendLine("	}");
		$this->appendLine("	");
	
		$this->appendLine("}");
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
