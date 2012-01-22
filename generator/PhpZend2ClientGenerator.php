<?php
class PhpZend2ClientGenerator extends ClientGeneratorFromXml
{
	private $cacheEnums = array();
	private $cacheTypes = array();
	
	/**
	 * @var DOMDocument
	 */
	protected $_doc = null;
	
	function PhpZend2ClientGenerator($xmlPath, $sourcePath = null)
	{
		if(!$sourcePath)
			$sourcePath = realpath("sources/zend2");
			
		parent::ClientGeneratorFromXml($xmlPath, $sourcePath);
		$this->_doc = new DOMDocument();
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
		
		$classInfo = new PhpZend2ClientGeneratorClassInfo();
		$classInfo->className = $enumCacheName;
	if($enumNode->hasAttribute('plugin'))
	{
		$pluginName = ucfirst($enumNode->getAttribute('plugin'));
		$classInfo->fullyQualifiedName = "\Kaltura\Client\Plugin\\{$pluginName}\Enum\\{$enumCacheName}";
	}
	else
	{
			$classInfo->fullyQualifiedName = "\Kaltura\Client\Enum\\{$enumCacheName}";	
		}
		
		$this->cacheEnums[$enumName] = $classInfo;
	} 
	
	private function cacheType(DOMElement $classNode)
	{
		$className = $classNode->getAttribute('name');
		$classCacheName = preg_replace('/^Kaltura(.+)$/', '$1', $className); 
		
		$classInfo = new PhpZend2ClientGeneratorClassInfo();
		$classInfo->className = $classCacheName;
		if($classNode->hasAttribute('plugin'))
		{
			$pluginName = ucfirst($classNode->getAttribute('plugin'));
			$classInfo->fullyQualifiedName = "\Kaltura\Client\Plugin\\{$pluginName}\Type\\{$classCacheName}";
		}
		else
		{
			$classInfo->fullyQualifiedName = "\Kaltura\Client\Type\\{$classCacheName}";	
		}
		$this->cacheTypes[$className] = $classInfo;
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
			$this->appendLine(' * @namespace');
			$this->appendLine(' */');
		}
		$this->appendLine('namespace Kaltura\Client;');
		$this->appendLine();
		
		if($this->generateDocs)
		{
			$this->appendLine('/**');
			$this->appendLine(" * @package $this->package");
			$this->appendLine(" * @subpackage $this->subpackage");
			$this->appendLine(' */');
		}
			
		$this->appendLine('class TypeMap');
		$this->appendLine('{');
		
		$classNodes = $xpath->query("/xml/classes/class");
		$this->appendLine('	private static $map = array(');
		foreach($classNodes as $classNode)
		{
			$kalturaType = $classNode->getAttribute('name');
			$zendType = $this->getTypeClassInfo($kalturaType);
			$fullyQualifiedNameNoPrefixSlash = substr($zendType->fullyQualifiedName, 1);
			$this->appendLine("		'$kalturaType' => '$fullyQualifiedNameNoPrefixSlash',");
		}
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
		return "Kaltura/Client/Plugin/{$pluginName}/Enum/{$enumName}.php";
	}
	
	protected function getTypePath(DOMElement $classNode)
	{
		$className = $classNode->getAttribute('name');
		$className = preg_replace('/^Kaltura(.+)$/', '$1', $className); 
			
		if(!$classNode->hasAttribute('plugin'))
			return "Kaltura/Client/Type/{$className}.php";

		$pluginName = ucfirst($classNode->getAttribute('plugin'));
		return "Kaltura/Client/Plugin/{$pluginName}/Type/{$className}.php";
	}
	
	protected function getServicePath($serviceNode)
	{
		$serviceName = ucfirst($serviceNode->getAttribute('name'));
			
		if(!$serviceNode->hasAttribute('plugin'))
			return "Kaltura/Client/Service/{$serviceName}Service.php";

		$pluginName = ucfirst($serviceNode->getAttribute('plugin'));
		return "Kaltura/Client/Plugin/{$pluginName}/Service/{$serviceName}Service.php";
	}
	
	protected function getPluginPath($pluginName)
	{
		$pluginName = ucfirst($pluginName);
		return "Kaltura/Client/Plugin/{$pluginName}/{$pluginName}.php";
	}
	
	protected function getMainPath()
	{
		return 'Kaltura/Client/Client.php';
	}
	
	protected function getMapPath()
	{
		return 'Kaltura/Client/TypeMap.php';
	}
	
	/**
	 * @return PhpZend2ClientGeneratorClassInfo
	 */
	protected function getEnumClassInfo($enumName)
	{
		if(!isset($this->cacheEnums[$enumName]))
			throw new Exception("Enum info for {$enumName} not found"); 
		
		return $this->cacheEnums[$enumName];
	}
	
	/**
	 * @return PhpZend2ClientGeneratorClassInfo
	 */
	protected function getTypeClassInfo($className)
	{
		if(!isset($this->cacheTypes[$className]))
			throw new Exception("Class info for {$className} not found");
		
		return $this->cacheTypes[$className];
	}
	
	protected function getServiceClass(DOMElement $serviceNode)
	{
		$serviceName = ucfirst($serviceNode->getAttribute('name'));
		
		return "{$serviceName}Service";
	}
	
	protected function getPluginClass($pluginName)
	{
		$pluginName = ucfirst($pluginName);
		return "{$pluginName}";
	}
	
	protected function formatMultiLineComment($description, $ident = 1)
	{
		$tabs = "";
		for($i = 0; $i < $ident; $i++)
			$tabs .= "\t";
		return str_replace("\n", "\n$tabs * ", $description); // to format multiline descriptions
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
		
		$this->appendLine("class $pluginClassName extends \Kaltura\Client\Plugin");
		$this->appendLine('{');
		$this->appendLine('	/**');
		$this->appendLine("	 * @var $pluginClassName");
		$this->appendLine('	 */');
		$this->appendLine('	protected static $instance;');
		$this->appendLine('');
	
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
		
		$this->appendLine('	protected function __construct(\Kaltura\Client\Client $client)');
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
		$this->appendLine('	public static function get(\Kaltura\Client\Client $client)');
		$this->appendLine('	{');
		$this->appendLine('		if(!self::$instance)');
		$this->appendLine("			self::\$instance = new $pluginClassName(\$client);");
		$this->appendLine('		return self::$instance;');
		$this->appendLine('	}');
		$this->appendLine('');
		$this->appendLine('	/**');
		$this->appendLine('	 * @return array<\Kaltura\Client\ServiceBase>');
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
		$enumClassInfo = $this->getEnumClassInfo($enumNode->getAttribute('name'));
		
		if($this->generateDocs)
		{
			$this->appendLine('/**');
			$this->appendLine(' * @namespace');
			$this->appendLine(' */');
		}
		$this->appendLine('namespace Kaltura\Client\Enum;');
		$this->appendLine();
		
		if($this->generateDocs)
		{
			$this->appendLine('/**');
			$this->appendLine(" * @package $this->package");
			$this->appendLine(" * @subpackage $this->subpackage");
			$this->appendLine(' */');
		}
		
	 	$this->appendLine("class $enumClassInfo->className");		
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
		$description = $classNode->getAttribute("description");
		$type = $this->getTypeClassInfo($kalturaType);
		
		$abstract = '';
		if ($classNode->hasAttribute("abstract"))
			$abstract = 'abstract ';
			
		if($this->generateDocs)
		{
			$this->appendLine('/**');
			$this->appendLine(' * @namespace');
			$this->appendLine(' */');
		}
		$this->appendLine('namespace Kaltura\Client\Type;');
		$this->appendLine();
		
		if($this->generateDocs)
		{
			$this->appendLine('/**');
			if ($description)
				$this->appendLine(" * " . $this->formatMultiLineComment($description, 0));
			$this->appendLine(" * @package $this->package");
			$this->appendLine(" * @subpackage $this->subpackage");
			$this->appendLine(' */');
		}
		
		// class definition
		$baseClass = '\Kaltura\Client\ObjectBase';
		if ($classNode->hasAttribute('base'))
		{
			$baseClassInfo = $this->getTypeClassInfo($classNode->getAttribute('base'));
			$baseClass = $baseClassInfo->className;
		}
			
		$this->appendLine($abstract . "class $type->className extends $baseClass");
		$this->appendLine("{");
		$this->appendLine("	public function getKalturaObjectType()");
		$this->appendLine("	{");
		$this->appendLine("		return '$kalturaType';");
		$this->appendLine("	}");
		$this->appendLine("	");
	
		$this->appendLine('	public function __construct(\SimpleXMLElement $xml = null)');
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
			$propType = $propertyNode->getAttribute("type");
		
			switch ($propType) 
			{
				case "int" :
				case "float" :
					$this->appendLine("		if(count(\$xml->{$propName}))");
					$this->appendLine("			\$this->$propName = ($propType)\$xml->$propName;");
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
					$this->appendLine("			\$this->$propName = \Kaltura\Client\Client::unmarshalItem(\$xml->$propName);");
					break;
					
				default : // sub object
					$this->appendLine("		if(!empty(\$xml->{$propName}))");
					$this->appendLine("			\$this->$propName = \Kaltura\Client\Client::unmarshalItem(\$xml->$propName);");
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
			$description = $propertyNode->getAttribute("description");
			
			$this->appendLine("	/**");
			$this->appendLine("	 * " . $this->formatMultiLineComment($description));
			if ($propType == "array")
				$this->appendLine("	 * @var $propType of {$propertyNode->getAttribute("arrayType")}");
			elseif ($this->isSimpleType($propType))
				$this->appendLine("	 * @var $propType");
			elseif ($isEnum) 
			{
				$propClassInfo = $this->getEnumClassInfo($propType);
				$this->appendLine("	 * @var $propClassInfo->fullyQualifiedName");
			} 
			else
			{
				$propClassInfo = $this->getTypeClassInfo($propType);
				$this->appendLine("	 * @var $propClassInfo->fullyQualifiedName");
			}
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

		// close class
		$this->appendLine("}");
	}
	
	function writeService(DOMElement $serviceNode)
	{
		$plugin = null;
		if($serviceNode->hasAttribute('plugin'))
			$plugin = $serviceNode->getAttribute('plugin');
			
		$serviceName = $serviceNode->getAttribute("name");
		$serviceId = $serviceNode->getAttribute("id");
		$description = $serviceNode->getAttribute("description");
					
		$serviceClassName = $this->getServiceClass($serviceNode, $plugin);
		$this->appendLine();
		
		if($this->generateDocs)
		{
			$this->appendLine('/**');
			$this->appendLine(' * @namespace');
			$this->appendLine(' */');
		}
		$this->appendLine('namespace Kaltura\Client\Service;');
		$this->appendLine();

		if($this->generateDocs)
		{
			$this->appendLine('/**');
			if ($description)
				$this->appendLine(" * " . $this->formatMultiLineComment($description, 0));
			$this->appendLine(" * @package $this->package");
			$this->appendLine(" * @subpackage $this->subpackage");
			$this->appendLine(' */');
		}
		
		$this->appendLine("class $serviceClassName extends \Kaltura\Client\ServiceBase");
		$this->appendLine("{");
		$this->appendLine("	function __construct(\\Kaltura\\Client\\Client \$client = null)");
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
		$description = $actionNode->getAttribute("description");
		
		// method signature
		$signature = "";
		if (in_array($action, array("list", "clone", "goto"))) // because list & clone are preserved in PHP
			$signature .= "function ".$action."Action(";
		else
			$signature .= "function ".$action."(";
			
		$paramNodes = $actionNode->getElementsByTagName("param");
		$signature .= $this->getSignature($paramNodes);
		
		$this->appendLine();
		$this->appendLine("	/**");
		if ($description)
			$this->appendLine("	 * " . $this->formatMultiLineComment($description));
		$this->appendLine("	 */");
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
			$this->appendLine("			return \$this->client->getMultiRequestResult();;");
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
						$resultTypeClassInfo = $this->getTypeClassInfo($resultType);
						$this->appendLine("		\$this->client->validateObjectType(\$resultObject, \"$resultTypeClassInfo->fullyQualifiedName\");");
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
				$typeClass = $this->getTypeClassInfo($paramType);
				$signature .= $typeClass->fullyQualifiedName." $".$paramName;
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
		
		if($this->generateDocs)
		{
			$this->appendLine('/**');
			$this->appendLine(' * @namespace');
			$this->appendLine(' */');
		}
		$this->appendLine('namespace Kaltura\Client;');
		$this->appendLine();
		
		if($this->generateDocs)
		{
			$this->appendLine('/**');
			$this->appendLine(" * @package $this->package");
			$this->appendLine(" * @subpackage $this->subpackage");
			$this->appendLine(' */');
		}
		
		$this->appendLine("class Client extends Base");
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
			$serviceClassName = "\\Kaltura\\Client\\Service\\".ucfirst($serviceName)."Service";
			$this->appendLine("	/**");
			$this->appendLine("	 * @var $serviceClassName");
			$this->appendLine("	 */");
			$this->appendLine("	protected \$$serviceName = null;");
			$this->appendLine("");
		}
		
		$this->appendLine("	/**");
		$this->appendLine("	 * Kaltura client constructor");
		$this->appendLine("	 *");
		$this->appendLine("	 * @param \\Kaltura\\Client\\Configuration \$config");
		$this->appendLine("	 */");
		$this->appendLine("	public function __construct(Configuration \$config)");
		$this->appendLine("	{");
		$this->appendLine("		parent::__construct(\$config);");
		$this->appendLine("	}");
		$this->appendLine("	");
	
		foreach($serviceNodes as $serviceNode)
		{
			if($serviceNode->hasAttribute("plugin"))
				continue;
				
			$serviceName = $serviceNode->getAttribute("name");
			$description = $serviceNode->getAttribute("description");
			$serviceClassName = "\\Kaltura\\Client\\Service\\".ucfirst($serviceName)."Service";
			
			$this->appendLine("	/**");
			$this->appendLine("	 * @var $serviceClassName");
			$this->appendLine("	 */");
			$this->appendLine("	public function get".ucfirst($serviceName)."Service()");
			$this->appendLine("	{");
			$this->appendLine("		if (is_null(\$this->$serviceName))");
			$this->appendLine("			\$this->$serviceName = new $serviceClassName(\$this);");
			$this->appendLine("		return \$this->$serviceName;");
			$this->appendLine("	}");
		}
		
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

class PhpZend2ClientGeneratorClassInfo
{
	public $className;
	
	public $fullyQualifiedName;
}