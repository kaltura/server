<?php
class Php53ClientGenerator extends ClientGeneratorFromXml
{
	private $cacheEnums = array();
	private $cacheTypes = array();
	
	/**
	 * @var DOMDocument
	 */
	protected $_doc = null;
	
	function __construct($xmlPath, Zend_Config $config, $sourcePath = "sources/php53")
	{
		parent::__construct($xmlPath, $sourcePath, $config);
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
		
		$classInfo = new PhpZend2ClientGeneratorClassInfo();
		$classInfo->setClassName($enumCacheName);
		if($enumNode->hasAttribute('plugin'))
		{
			$pluginName = ucfirst($enumNode->getAttribute('plugin'));
			$classInfo->setNamespace("Kaltura\\Client\\Plugin\\{$pluginName}\\Enum");
		}
		else
		{
			$classInfo->setNamespace("Kaltura\\Client\\Enum");
		}
		$this->cacheEnums[$enumName] = $classInfo;
	} 
	
	private function cacheType(DOMElement $classNode)
	{
		$className = $classNode->getAttribute('name');
		$classCacheName = preg_replace('/^Kaltura(.+)$/', '$1', $className); 
		
		$classInfo = new PhpZend2ClientGeneratorClassInfo();
		$classInfo->setClassName($classCacheName);
		if($classNode->hasAttribute('plugin'))
		{
			$pluginName = ucfirst($classNode->getAttribute('plugin'));
			$classInfo->setNamespace("Kaltura\\Client\\Plugin\\{$pluginName}\\Type");
		}
		else
		{
			$classInfo->setNamespace("Kaltura\\Client\\Type");	
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
		
		$this->appendLine('/**');
		$this->appendLine(' * @namespace');
		$this->appendLine(' */');
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
			$this->appendLine("		'$kalturaType' => '{$zendType->getFullyQualifiedNameNoPrefixSlash()}',");
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
		
		$configurationNodes = $xpath->query("/xml/configurations/*");
	    $this->writeMainClient($serviceNodes, $configurationNodes);
	    
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
			return "library/Kaltura/Client/Enum/{$enumName}.php";

		$pluginName = ucfirst($enumNode->getAttribute('plugin'));
		return "library/Kaltura/Client/Plugin/{$pluginName}/Enum/{$enumName}.php";
	}
	
	protected function getTypePath(DOMElement $classNode)
	{
		$className = $classNode->getAttribute('name');
		$className = preg_replace('/^Kaltura(.+)$/', '$1', $className); 
			
		if(!$classNode->hasAttribute('plugin'))
			return "library/Kaltura/Client/Type/{$className}.php";

		$pluginName = ucfirst($classNode->getAttribute('plugin'));
		return "library/Kaltura/Client/Plugin/{$pluginName}/Type/{$className}.php";
	}
	
	protected function getServicePath($serviceNode)
	{
		$serviceName = ucfirst($serviceNode->getAttribute('name'));
			
		if(!$serviceNode->hasAttribute('plugin'))
			return "library/Kaltura/Client/Service/{$serviceName}Service.php";

		$pluginName = ucfirst($serviceNode->getAttribute('plugin'));
		return "library/Kaltura/Client/Plugin/{$pluginName}/Service/{$serviceName}Service.php";
	}
	
	protected function getPluginPath($pluginName)
	{
		$pluginName = ucfirst($pluginName);
		return "library/Kaltura/Client/Plugin/{$pluginName}/".$this->getPluginClass($pluginName).".php";
	}
	
	protected function getMainPath()
	{
		return 'library/Kaltura/Client/Client.php';
	}
	
	protected function getMapPath()
	{
		return 'library/Kaltura/Client/TypeMap.php';
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
		if($className == 'KalturaObjectBase')
		{
			$classInfo = new PhpZend2ClientGeneratorClassInfo();
			$classInfo->setClassName($className);
			$classInfo->setNamespace("Kaltura\\Client\\Type");
			return $classInfo;
		}
		
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
		return "{$pluginName}Plugin";
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
		
		$this->appendLine('/**');
		$this->appendLine(' * @namespace');
		$this->appendLine(' */');
		$this->appendLine("namespace Kaltura\\Client\\Plugin\\".ucfirst($pluginName).";");
		$this->appendLine();
		
		if($this->generateDocs)
		{
			$this->appendLine('/**');
			$this->appendLine(" * @package $this->package");
			$this->appendLine(" * @subpackage $this->subpackage");
			$this->appendLine(' */');
		}
		
		$this->appendLine("class $pluginClassName extends \Kaltura\Client\Plugin");
		$this->appendLine('{');
	
		$serviceNodes = $xpath->query("/xml/services/service[@plugin = '$pluginName']");
		foreach($serviceNodes as $serviceNode)
		{
			$serviceName = $serviceNode->getAttribute("name");
			$serviceClass = $this->getServiceClass($serviceNode);
			$serviceRelativeClassName = "Service\\{$serviceClass}";
			$this->appendLine('	/**');
			$this->appendLine("	 * @var $serviceRelativeClassName");
			$this->appendLine('	 */');
			$this->appendLine("	protected \${$serviceName} = null;");
			$this->appendLine('');
		}
		
		$this->appendLine('	protected function __construct(\Kaltura\Client\Client $client)');
		$this->appendLine('	{');
		$this->appendLine('		parent::__construct($client);');
		$this->appendLine('	}');
		$this->appendLine('');
		$this->appendLine('	/**');
		$this->appendLine("	 * @return $pluginClassName");
		$this->appendLine('	 */');
		$this->appendLine('	public static function get(\Kaltura\Client\Client $client)');
		$this->appendLine('	{');
		$this->appendLine("		return new $pluginClassName(\$client);");
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
			$serviceName = $serviceNode->getAttribute("name");
			$serviceClass = $this->getServiceClass($serviceNode);
			$this->appendLine("			'$serviceName' => \$this->get{$serviceClass}(),");
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
		
		foreach($serviceNodes as $serviceNode)
		{
			$serviceName = $serviceNode->getAttribute("name");
			$description = $serviceNode->getAttribute("description");
			$serviceClass = $this->getServiceClass($serviceNode);
			$serviceRelativeClassName = "Service\\{$serviceClass}";
			
			$this->appendLine("	/**");
			$this->appendLine("	 * @return \\Kaltura\\Client\\Plugin\\".ucfirst($pluginName)."\\$serviceRelativeClassName");
			$this->appendLine("	 */");
			$this->appendLine("	public function get{$serviceClass}()");
			$this->appendLine("	{");
			$this->appendLine("		if (is_null(\$this->$serviceName))");
			$this->appendLine("			\$this->$serviceName = new $serviceRelativeClassName(\$this->_client);");
			$this->appendLine("		return \$this->$serviceName;");
			$this->appendLine("	}");
			
			
		}
		$this->appendLine('}');
		$this->appendLine('');
		
    	$this->addFile($this->getPluginPath($pluginName), $this->getTextBlock());
	}

	
	function writeEnum(DOMElement $enumNode)
	{
		$enumClassInfo = $this->getEnumClassInfo($enumNode->getAttribute('name'));
		
		$this->appendLine('/**');
		$this->appendLine(' * @namespace');
		$this->appendLine(' */');
		$this->appendLine("namespace {$enumClassInfo->getNamespace()};");
		$this->appendLine();
		
		if($this->generateDocs)
		{
			$this->appendLine('/**');
			$this->appendLine(" * @package $this->package");
			$this->appendLine(" * @subpackage $this->subpackage");
			$this->appendLine(' */');
		}
		
	 	$this->appendLine("class {$enumClassInfo->getClassName()} extends \Kaltura\Client\EnumBase");		
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
			
		$this->appendLine('/**');
		$this->appendLine(' * @namespace');
		$this->appendLine(' */');
		$this->appendLine("namespace {$type->getNamespace()};");
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
			$baseClass = $baseClassInfo->getFullyQualifiedName();
		}
			
		$this->appendLine($abstract . "class {$type->getClassName()} extends $baseClass");
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

				case "bigint" :
					$this->appendLine("		if(count(\$xml->{$propName}))");
					$this->appendLine("			\$this->$propName = (string)\$xml->$propName;");
					break;
					
				case "bool" :
					$this->appendLine("		if(count(\$xml->{$propName}))");
					$this->appendLine("		{");
					$this->appendLine("			if(!empty(\$xml->{$propName}))");
					$this->appendLine("				\$this->$propName = true;");
					$this->appendLine("			else");
					$this->appendLine("				\$this->$propName = false;");
					$this->appendLine("		}");
					break;
					
				case "string" :
					$this->appendLine("		if(count(\$xml->{$propName}))");
					$this->appendLine("			\$this->$propName = ($propType)\$xml->$propName;");
					break;
					
				case "array" :
					$arrayType = $propertyNode->getAttribute ( "arrayType" );
					$this->appendLine("		if(count(\$xml->{$propName}))");
					$this->appendLine("		{");
					$this->appendLine("			if(empty(\$xml->{$propName}))");
					$this->appendLine("				\$this->$propName = array();");
					$this->appendLine("			else");
					$this->appendLine("				\$this->$propName = \Kaltura\Client\ParseUtils::unmarshalArray(\$xml->$propName, \"$arrayType\");");
					$this->appendLine("		}");
					break;
					
				case "map" :
					$arrayType = $propertyNode->getAttribute ( "arrayType" );
					$this->appendLine("		if(count(\$xml->{$propName}))");
					$this->appendLine("		{");
					$this->appendLine("			if(empty(\$xml->{$propName}))");
					$this->appendLine("				\$this->$propName = array();");
					$this->appendLine("			else");
					$this->appendLine("				\$this->$propName = \Kaltura\Client\ParseUtils::unmarshalMap(\$xml->$propName, \"$arrayType\");");
					$this->appendLine("		}");
					break;
					
				default : // sub object
					$this->appendLine("		if(count(\$xml->{$propName}) && !empty(\$xml->{$propName}))");
					$this->appendLine("			\$this->$propName = \Kaltura\Client\ParseUtils::unmarshalObject(\$xml->$propName, \"{$propType}\");");
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
				
			$propType = $this->getPHPType($propType);
			$description = $propertyNode->getAttribute("description");
			
			$this->appendLine("	/**");
			$this->appendLine("	 * " . $this->formatMultiLineComment($description));
			if ($propType == "array")
				$this->appendLine("	 * @var array<{$propertyNode->getAttribute("arrayType")}>");
			elseif ($propType == "map")
				$this->appendLine("	 * @var array<string, {$propertyNode->getAttribute("arrayType")}>");
			elseif ($this->isSimpleType($propType))
				$this->appendLine("	 * @var $propType");
			elseif ($isEnum) 
			{
				$propClassInfo = $this->getEnumClassInfo($propType);
				$this->appendLine("	 * @var {$propClassInfo->getFullyQualifiedName()}");
			} 
			else
			{
				$propClassInfo = $this->getTypeClassInfo($propType);
				$this->appendLine("	 * @var {$propClassInfo->getFullyQualifiedName()}");
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
		
		$this->appendLine('/**');
		$this->appendLine(' * @namespace');
		$this->appendLine(' */');
		if ($plugin)
			$this->appendLine("namespace Kaltura\\Client\\Plugin\\".ucfirst($plugin)."\\Service;");
		else
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
	    $arrayObjectType = ($resultType == 'array') ? $resultNode->getAttribute ( "arrayType" ) : null;
		$description = $actionNode->getAttribute("description");
		
		$enableInMultiRequest = true;
		if($actionNode->hasAttribute("enableInMultiRequest"))
		{
			$enableInMultiRequest = intval($actionNode->getAttribute("enableInMultiRequest"));
		}
		
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
		$this->appendLine("	 * ");			
		if ($resultType && $resultType != 'null')
		{
			if ($this->isSimpleType($resultType) || $resultType == 'array' || $resultType == 'file')
			{
				$this->appendLine("	 * @return $resultType");
			}
			else
			{
				$resultTypeClassInfo = $this->getTypeClassInfo($resultType);
				$this->appendLine("	 * @return {$resultTypeClassInfo->getFullyQualifiedName()}");
			}
		}
		$this->appendLine("	 */");
		$this->appendLine("	$signature");
		$this->appendLine("	{");
		
		if(!$enableInMultiRequest)
		{
			$this->appendLine("		if (\$this->client->isMultiRequest())");
			$this->appendLine("			throw \$this->client->getClientException(\"Action is not supported as part of multi-request.\", ClientException::ERROR_ACTION_IN_MULTIREQUEST);");
			$this->appendLine("		");
		}
		
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
				else if ($paramType == "array" || $paramType == "map")
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
			$this->appendLine("		\$this->client->queueServiceActionCall('" . strtolower($serviceId) . "', '$action', null, \$kparams);");
			$this->appendLine('		$resultObject = $this->client->getServeUrl();');
	    }
	    else
	    {
	    	$fallbackClass = 'null';
	    	if($resultType == 'array')
	    	{
	    		$fallbackClass = "\"$arrayObjectType\"";
	    	}
	    	else if($resultType && !$this->isSimpleType($resultType))
	    	{
	    		$fallbackClass = "\"$resultType\"";
	    	}
	    	
			if ($haveFiles)
			{
				$this->appendLine("		\$this->client->queueServiceActionCall(\"".strtolower($serviceId)."\", \"$action\", $fallbackClass, \$kparams, \$kfiles);");
			}
			else
			{
				$this->appendLine("		\$this->client->queueServiceActionCall(\"".strtolower($serviceId)."\", \"$action\", $fallbackClass, \$kparams);");
			}
			
			if($enableInMultiRequest)
			{
				$this->appendLine("		if (\$this->client->isMultiRequest())");
				$this->appendLine("			return \$this->client->getMultiRequestResult();");
			}
			
			$this->appendLine("		\$resultXml = \$this->client->doQueue();");
			$this->appendLine("		\$resultXmlObject = new \\SimpleXMLElement(\$resultXml);");
			$this->appendLine("		\$this->client->checkIfError(\$resultXmlObject->result);");
			switch($resultType)
			{
				
				case 'int':
					$this->appendLine("		\$resultObject = (int)\\Kaltura\\Client\\ParseUtils::unmarshalSimpleType(\$resultXmlObject->result);");
					break;
				case 'bool':
					$this->appendLine("		\$resultObject = (bool)\\Kaltura\\Client\\ParseUtils::unmarshalSimpleType(\$resultXmlObject->result);");
					break;
				case 'bigint':
				case 'string':
					$this->appendLine("		\$resultObject = (String)\\Kaltura\\Client\\ParseUtils::unmarshalSimpleType(\$resultXmlObject->result);");
					break;
				case 'array':
					$this->appendLine("		\$resultObject = \\Kaltura\\Client\\ParseUtils::unmarshalArray(\$resultXmlObject->result, \"$arrayObjectType\");");
					$this->appendLine("		\$this->client->validateObjectType(\$resultObject, \"$resultType\");");
					break;
				default:
					if ($resultType)
					{
						$resultTypeClassInfo = $this->getTypeClassInfo($resultType);
						$resultObjectTypeEscaped = str_replace("\\", "\\\\", $resultTypeClassInfo->getFullyQualifiedName());
						$this->appendLine("		\$resultObject = \\Kaltura\\Client\\ParseUtils::unmarshalObject(\$resultXmlObject->result, \"{$resultType}\");");
						$this->appendLine("		\$this->client->validateObjectType(\$resultObject, \"{$resultObjectTypeEscaped}\");");
					}
			}
	    }
			
	    if($resultType && $resultType != 'null')
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
			else if ($paramType == "array" || $paramType == "map")
			{
				$signature .= "array $".$paramName;
			}
			else
			{
				$typeClass = $this->getTypeClassInfo($paramType);
				$signature .= $typeClass->getFullyQualifiedName()." $".$paramName;
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
	
	function writeMainClient(DOMNodeList $serviceNodes, DOMNodeList $configurationNodes)
	{
		$apiVersion = $this->_doc->documentElement->getAttribute('apiVersion');
		$date = date('y-m-d');
		
		$this->appendLine('/**');
		$this->appendLine(' * @namespace');
		$this->appendLine(' */');
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
		$this->appendLine("		");
		$this->appendLine("		\$this->setClientTag('php5:$date');");
		$this->appendLine("		\$this->setApiVersion('$apiVersion');");
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
			$this->appendLine("	 * @return $serviceClassName");
			$this->appendLine("	 */");
			$this->appendLine("	public function get".ucfirst($serviceName)."Service()");
			$this->appendLine("	{");
			$this->appendLine("		if (is_null(\$this->$serviceName))");
			$this->appendLine("			\$this->$serviceName = new $serviceClassName(\$this);");
			$this->appendLine("		return \$this->$serviceName;");
			$this->appendLine("	}");
		}
	
		$volatileProperties = array();
		foreach($configurationNodes as $configurationNode)
		{
			/* @var $configurationNode DOMElement */
			$configurationName = $configurationNode->nodeName;
			$attributeName = lcfirst($configurationName) . "Configuration";
			$volatileProperties[$attributeName] = array();
		
			foreach($configurationNode->childNodes as $configurationPropertyNode)
			{
				/* @var $configurationPropertyNode DOMElement */
				
				if ($configurationPropertyNode->nodeType != XML_ELEMENT_NODE)
					continue;
			
				$configurationProperty = $configurationPropertyNode->localName;
				
				if($configurationPropertyNode->hasAttribute('volatile') && $configurationPropertyNode->getAttribute('volatile'))
				{
					$volatileProperties[$attributeName][] = $configurationProperty;
				}
				
				$type = $configurationPropertyNode->getAttribute('type');
				if(!$this->isSimpleType($type))
					$type = $this->getTypeClassInfo($type)->getFullyQualifiedName();
				$description = null;
				
				if($configurationPropertyNode->hasAttribute('description'))
				{
					$description = $configurationPropertyNode->getAttribute('description');
				}
				
				$this->writeConfigurationProperty($configurationName, $configurationProperty, $configurationProperty, $type, $description);
				
				if($configurationPropertyNode->hasAttribute('alias'))
				{
					$this->writeConfigurationProperty($configurationName, $configurationPropertyNode->getAttribute('alias'), $configurationProperty, $type, $description);					
				}
			}
		}
		
		$this->appendLine ( "	/**");
		$this->appendLine ( "	 * Clear all volatile configuration parameters");
		$this->appendLine ( "	 */");
		$this->appendLine ( "	protected function resetRequest()");
		$this->appendLine ( "	{");
		$this->appendLine ( "		parent::resetRequest();");
		foreach($volatileProperties as $attributeName => $properties)
		{
			foreach($properties as $propertyName)
			{
				$this->appendLine("		unset(\$this->{$attributeName}['{$propertyName}']);");
			}
		}
		$this->appendLine ( "	}");
		
		$this->appendLine("}");
	}
	
	protected function writeConfigurationProperty($configurationName, $name, $paramName, $type, $description)
	{
		$methodsName = ucfirst($name);
		$signitureType = $this->isSimpleType($type) ? '' : "$type ";
	
		
		$this->appendLine("	/**");
		if($description)
		{
			$this->appendLine("	 * $description");
			$this->appendLine("	 * ");
		}
		$this->appendLine("	 * @param $type \${$name}");
		$this->appendLine("	 */");
		$this->appendLine("	public function set{$methodsName}({$signitureType}\${$name})");
		$this->appendLine("	{");
		$this->appendLine("		\$this->{$configurationName}Configuration['{$paramName}'] = \${$name};");
		$this->appendLine("	}");
		$this->appendLine("	");
	
		
		$this->appendLine("	/**");
		if($description)
		{
			$this->appendLine("	 * $description");
			$this->appendLine("	 * ");
		}
		$this->appendLine("	 * @return $type");
		$this->appendLine("	 */");
		$this->appendLine("	public function get{$methodsName}()");
		$this->appendLine("	{");
		$this->appendLine("		if(isset(\$this->{$configurationName}Configuration['{$paramName}']))");
		$this->appendLine("		{");
		$this->appendLine("			return \$this->{$configurationName}Configuration['{$paramName}'];");
		$this->appendLine("		}");
		$this->appendLine("		");
		$this->appendLine("		return null;");
		$this->appendLine("	}");
		$this->appendLine("	");
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
	
	public function getPHPType($propType)
	{		
		switch ($propType) 
		{	
			case "bigint" :
				return "int";
				
			default :
				return $propType;
		}
	}
}

class PhpZend2ClientGeneratorClassInfo
{
	private $className;
	
	private $namespace;
	
	public function getClassName()
	{
		return $this->className;
	}

	public function getNamespace()
	{
		return $this->namespace;
	}

	public function getFullyQualifiedName()
	{
		return '\\'.$this->getNamespace().'\\'.$this->getClassName();
	}

	public function getFullyQualifiedNameNoPrefixSlash()
	{
		return $this->getNamespace().'\\'.$this->getClassName();
	}

	public function setClassName($className)
	{
		$this->className = $className;
	}

	public function setNamespace($namespace)
	{
		$this->namespace = $namespace;
	}

}