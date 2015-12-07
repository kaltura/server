<?php
class Php5ClientGenerator extends ClientGeneratorFromXml
{
	/**
	 * @var DOMDocument
	 */
	protected $_doc = null;
	
	function __construct($xmlPath, Zend_Config $config, $sourcePath = "sources/php5")
	{
		parent::__construct($xmlPath, $sourcePath, $config);
		$this->_doc = new KDOMDocument();
		$this->_doc->load($this->_xmlFile);
	}
	
	function getSingleLineCommentMarker()
	{
		return '//';
	}
	
	function generate()
	{
		parent::generate();
	
		$xpath = new DOMXPath($this->_doc);
		
		// enumes
		$this->appendLine('<?php');
			
		if($this->generateDocs)
		{
			$this->appendLine('/**');
			$this->appendLine(" * @package $this->package");
			$this->appendLine(" * @subpackage $this->subpackage");
			$this->appendLine(' */');
		}
		
		$this->appendLine('require_once(dirname(__FILE__) . "/KalturaClientBase.php");');
		$this->appendLine('');
	    
		$enumNodes = $xpath->query("/xml/enums/enum");
		foreach($enumNodes as $enumNode)
		{
			if(!$enumNode->hasAttribute('plugin'))
				$this->writeEnum($enumNode);
		}
    	$this->addFile("KalturaEnums.php", $this->getTextBlock());
    	
    	
		// classes
    	$this->startNewTextBlock();
		$this->appendLine('<?php');
		
		if($this->generateDocs)
		{
			$this->appendLine('/**');
			$this->appendLine(" * @package $this->package");
			$this->appendLine(" * @subpackage $this->subpackage");
			$this->appendLine(' */');
		}
		
		$this->appendLine('require_once(dirname(__FILE__) . "/KalturaClientBase.php");');
		$this->appendLine('');
		
		$classNodes = $xpath->query("/xml/classes/class");
		foreach($classNodes as $classNode)
		{
			if(!$classNode->hasAttribute('plugin'))
				$this->writeClass($classNode);
		}
    	$this->addFile("KalturaTypes.php", $this->getTextBlock());
		
		
		// services
    	$this->startNewTextBlock();
		$this->appendLine('<?php');
		
		if($this->generateDocs)
		{
			$this->appendLine('/**');
			$this->appendLine(" * @package $this->package");
			$this->appendLine(" * @subpackage $this->subpackage");
			$this->appendLine(' */');
		}
		
		$this->appendLine('require_once(dirname(__FILE__) . "/KalturaClientBase.php");');
		$this->appendLine('require_once(dirname(__FILE__) . "/KalturaEnums.php");');
		$this->appendLine('require_once(dirname(__FILE__) . "/KalturaTypes.php");');
		$this->appendLine('');
		
		$serviceNodes = $xpath->query("/xml/services/service");
		foreach($serviceNodes as $serviceNode)
		{
			if(!$serviceNode->hasAttribute('plugin'))
		    	$this->writeService($serviceNode);
		}
		$this->appendLine();
		
		$configurationNodes = $xpath->query("/xml/configurations/*");
	    $this->writeMainClient($serviceNodes, $configurationNodes);
	    $this->appendLine();
	    
    	$this->addFile("KalturaClient.php", $this->getTextBlock());
    	
		// plugins
    	$plugins = KalturaPluginManager::getPluginInstances();
		foreach($plugins as $plugin)
		    $this->writePlugin($plugin);
	}
	
	function getPluginClassName($pluginName)
	{
		return "Kaltura" . ucfirst($pluginName) . "ClientPlugin";
	}
	
	function writePlugin(KalturaPlugin $plugin)
	{
		$pluginName = $plugin->getPluginName();
		
		$xpath = new DOMXPath($this->_doc);
		$pluginClassName = $this->getPluginClassName($pluginName);
		
    	$this->startNewTextBlock();
		$this->appendLine('<?php');
		
		if($this->generateDocs)
		{
			$this->appendLine('/**');
			$this->appendLine(" * @package $this->package");
			$this->appendLine(" * @subpackage $this->subpackage");
			$this->appendLine(' */');
		}
		
		$this->appendLine('require_once(dirname(__FILE__) . "/../KalturaClientBase.php");');
		$this->appendLine('require_once(dirname(__FILE__) . "/../KalturaEnums.php");');
		$this->appendLine('require_once(dirname(__FILE__) . "/../KalturaTypes.php");');

		$dependencyNodes = $xpath->query("/xml/plugins/plugin[@name = '$pluginName']/dependency");
		foreach($dependencyNodes as $dependencyNode)
			$this->appendLine('require_once(dirname(__FILE__) . "/' .
				$this->getPluginClassName($dependencyNode->getAttribute("pluginName")) . '.php");');

		$this->appendLine('');
		
		$classsAdded = false;
		$enumNodes = $xpath->query("/xml/enums/enum[@plugin = '$pluginName']");
		foreach($enumNodes as $enumNode)
		{
			$classsAdded = true;
			$this->writeEnum($enumNode);
		}
	
		$classNodes = $xpath->query("/xml/classes/class[@plugin = '$pluginName']");
		foreach($classNodes as $classNode)
		{
			$classsAdded = true;
			$this->writeClass($classNode);
		}
	
		$serviceNodes = $xpath->query("/xml/services/service[@plugin = '$pluginName']");
		foreach($serviceNodes as $serviceNode)
		{
			$classsAdded = true;
		    $this->writeService($serviceNode);
		}
		if(!$classsAdded)
			return;
		
		$serviceNodes = $xpath->query("/xml/plugins/plugin[@name = '$pluginName']/pluginService");
		$services = array();
		foreach($serviceNodes as $serviceNode)
			$services[] = $serviceNode->getAttribute("name");
			
		if($this->generateDocs)
		{
			$this->appendLine('/**');
			$this->appendLine(" * @package $this->package");
			$this->appendLine(" * @subpackage $this->subpackage");
			$this->appendLine(' */');
		}
		
		$this->appendLine("class $pluginClassName extends KalturaClientPlugin");
		$this->appendLine('{');
	
		foreach($services as $service)
		{
			$serviceName = ucfirst($service);
			$this->appendLine('	/**');
			$this->appendLine("	 * @var Kaltura{$serviceName}Service");
			$this->appendLine('	 */');
			$this->appendLine("	public \${$service} = null;");
			$this->appendLine('');
		}
		
		$this->appendLine('	protected function __construct(KalturaClient $client)');
		$this->appendLine('	{');
		$this->appendLine('		parent::__construct($client);');
		foreach($services as $service)
		{
			$serviceName = ucfirst($service);
			$this->appendLine("		\$this->$service = new Kaltura{$serviceName}Service(\$client);");
		}
		$this->appendLine('	}');
		$this->appendLine('');
		$this->appendLine('	/**');
		$this->appendLine("	 * @return $pluginClassName");
		$this->appendLine('	 */');
		$this->appendLine('	public static function get(KalturaClient $client)');
		$this->appendLine('	{');
		$this->appendLine("		return new $pluginClassName(\$client);");
		$this->appendLine('	}');
		$this->appendLine('');
		$this->appendLine('	/**');
		$this->appendLine('	 * @return array<KalturaServiceBase>');
		$this->appendLine('	 */');
		$this->appendLine('	public function getServices()');
		$this->appendLine('	{');
		$this->appendLine('		$services = array(');
		foreach($services as $service)
			$this->appendLine("			'$service' => \$this->$service,");
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
		
    	$this->addFile("KalturaPlugins/$pluginClassName.php", $this->getTextBlock());
	}
	
	function writeEnum(DOMElement $enumNode)
	{
		$enumName = $enumNode->getAttribute("name");
		
		if($this->generateDocs)
		{
			$this->appendLine('/**');
			$this->appendLine(" * @package $this->package");
			$this->appendLine(" * @subpackage $this->subpackage");
			$this->appendLine(' */');
		}
		
	 	$this->appendLine("class $enumName extends KalturaEnumBase");
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
		$type = $classNode->getAttribute("name");;
		
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
		if ($classNode->hasAttribute("base"))
			$this->appendLine($abstract . "class $type extends " . $classNode->getAttribute("base"));
		else
			$this->appendLine($abstract . "class $type extends KalturaObjectBase");
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

			$propType = $this->getPHPType($propType);
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
	
	function writeService(DOMElement $serviceNode, $serviceName = null, $serviceId = null, $actionPrefix = "", $extends = "KalturaServiceBase")
	{
		$serviceName = $serviceName ? $serviceName : $serviceNode->getAttribute("name");
		$serviceId = $serviceId ? $serviceId : $serviceNode->getAttribute("id");
		
		
		$servicePlugins = $serviceNode->getElementsByTagName("servicePlugin");
			
		foreach ($servicePlugins as $servicePlugin)
		{
		    $this->writeServicePluginClasses($servicePlugin, $serviceName, $serviceId);
		}
		
		$serviceClassName = "Kaltura".$this->upperCaseFirstLetter($serviceName)."Service";
		$this->appendLine();
		
		if($this->generateDocs)
		{
			$this->appendLine('/**');
			$this->appendLine(" * @package $this->package");
			$this->appendLine(" * @subpackage $this->subpackage");
			$this->appendLine(' */');
		}
		
		$this->appendLine("class $serviceClassName extends $extends");
		$this->appendLine("{");
		
		if ($servicePlugins && count($servicePlugins))
		{
		    foreach ($servicePlugins as $servicePlugin)
		    {
		        $this->appendLine("");
		        $this->appendLine('	/**');
			    $this->appendLine("	* @param Kaltura".$this->upperCaseFirstLetter($servicePlugin->getAttribute("name"))."ServicePlugin");
			    $this->appendLine('	*/');
			    $this->appendLine("	public $".$servicePlugin->getAttribute("name").";");
		    }
		}
		
		$this->appendLine("	function __construct(KalturaClient \$client = null)");
		$this->appendLine("	{");
		$this->appendLine("		parent::__construct(\$client);");
		
		
		$this->appendLine("	}");
		
		$actionNodes = $serviceNode->getElementsByTagName("action");
		foreach($actionNodes as $actionNode)
		{
            $this->writeAction($serviceId, $actionNode, $actionPrefix);
		}
		
		$this->appendLine("}");
	}
	
	function writeAction($serviceId, DOMElement $actionNode, $actionPrefix = "")
	{
		$action = $actionNode->getAttribute("name");
		$method = $action;
		if (in_array($action, array("list", "clone", "goto"))) // because list & clone are preserved in PHP
			$method .= 'Action';
			
	    $resultNode = $actionNode->getElementsByTagName("result")->item(0);
	    $resultType = $resultNode->getAttribute("type");
			
		$paramNodes = $actionNode->getElementsByTagName("param");
		
		$enableInMultiRequest = true;
		if($actionNode->hasAttribute("enableInMultiRequest"))
		{
			$enableInMultiRequest = intval($actionNode->getAttribute("enableInMultiRequest"));
		}
		
		$this->appendLine();
		
		if($this->generateDocs)
		{
			$this->appendLine('	/**');
			$this->appendLine("	 * " . ucfirst(trim($actionNode->getAttribute("description"), " \t\r\n")));
			$this->appendLine("	 * ");
		
			foreach($paramNodes as $paramNode)
			{
				$paramName = $paramNode->getAttribute("name");
				$paramType = $paramNode->getAttribute("type");
				$paramDescription = ucfirst(trim($paramNode->getAttribute("description"), " \t\r\n"));
				
				$this->appendLine("	 * @param $paramType \${$paramName} $paramDescription");
			}
			
			if($resultType && $resultType != 'null')
			{
				$this->appendLine("	 * @return $resultType");
			}
			$this->appendLine('	 */');
		}
				
		$argumentsSignature = $this->getSignature($paramNodes, $resultType == 'file');
		
		// method signature
		$signature = "function $method($argumentsSignature)";
		
		
		$this->appendLine("	$signature");
		$this->appendLine("	{");
		
		if(!$enableInMultiRequest)
		{
			$this->appendLine("		if (\$this->client->isMultiRequest())");
			$this->appendLine("			throw new KalturaClientException(\"Action is not supported as part of multi-request.\", KalturaClientException::ERROR_ACTION_IN_MULTIREQUEST);");
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
		
		if ($haveFiles)
			$this->appendLine("		\$this->client->queueServiceActionCall(\"".strtolower($serviceId)."\", \"$actionPrefix$action\", \$kparams, \$kfiles);");
		else
			$this->appendLine("		\$this->client->queueServiceActionCall(\"".strtolower($serviceId)."\", \"$actionPrefix$action\", \$kparams);");
			
		if($resultType == 'file')
		{
			$this->appendLine('		if(!$this->client->getDestinationPath() && !$this->client->getReturnServedResult())');
			$this->appendLine('			return $this->client->getServeUrl();');
			$this->appendLine('		return $this->client->doQueue();');
		}
		else
		{
			if($enableInMultiRequest)
			{
				$this->appendLine("		if (\$this->client->isMultiRequest())");
				$this->appendLine("			return \$this->client->getMultiRequestResult();");
			}
			$this->appendLine("		\$resultObject = \$this->client->doQueue();");
			$this->appendLine("		\$this->client->throwExceptionIfError(\$resultObject);");
			
			if (!$resultType)
			{
				$resultType = "null";
			}
			
			if ($resultType == 'int')
			{
				$resultType = "integer";
			}

			if ($resultType == 'bigint')
			{
				$resultType = "double";
			}

			if ($resultType == 'bool')
			{
				$this->appendLine("		\$resultObject = (bool) \$resultObject;");
			}
			else
			{
				$this->appendLine("		\$this->client->validateObjectType(\$resultObject, \"$resultType\");");
			}
				
			if($resultType && $resultType != 'null')
			{
				$this->appendLine("		return \$resultObject;");
			}
		}
		
		$this->appendLine("	}");
	}
	
	function writeServicePluginClasses (DOMElement $extendingPluginNode, $extendedServiceName, $extendedServiceId)
	{
	    $extendingPluginName = $extendingPluginNode->getAttribute("name");
	    
	    $servicePluginClassName = "Kaltura". $this->upperCaseFirstLetter($extendingPluginName) ."ServicePlugin";
	    
	    $this->appendLine("");
	    $this->appendLine("class ". $servicePluginClassName . " extends BaseKalturaServicePlugin");
	    $this->appendLine("{");
	    
	    $serviceNodes = $extendingPluginNode->getElementsByTagName("service");
	    foreach ($serviceNodes as $serviceNode)
	    {
	        if ($serviceNode->nodeName == "service")
	        {
	            $this->appendLine("	/**");
	            $this->appendLine("	* @param Kaltura". $this->upperCaseFirstLetter($serviceNode->getAttribute("name")) ."ExtendedService");
	            $this->appendLine("	*/");
	            $this->appendLine("	public $".$serviceNode->getAttribute("name"));
	            $this->appendLine("");
	        }
	    }
	    
	    $this->appendLine("	public function __construct(KalturaClient \$client = null)");
	    $this->appendLine("	{");
	    $this->appendLine("		parent::__construct(\$client);");
	    
	    foreach ($serviceNodes as $serviceNode)
	    {
	        $extendingServiceName = $serviceNode->getAttribute("name");
	        $this->appendLine("		$".$extendingServiceName." = new Kaltura".$this->upperCaseFirstLetter($extendingServiceName)."ExtendedService();");
	    }
	    
	    $this->appendLine("	}");
	    $this->appendLine("}");
	    $this->appendLine("");
	    
	    foreach ($serviceNodes as $serviceNode)
	    {
	        $extendingServiceName = $serviceNode->getAttribute("name");
	        
	        $extendingServiceClassName = "Kaltura".$this->upperCaseFirstLetter($extendingServiceName)."Extended";
	        
	        $this->writeService($serviceNode, $extendingServiceClassName , $extendedServiceId, $extendingPluginName."_".$extendingServiceName."_"   ,"KalturaExtendedService");
	    }
	}
	
	/**
	 * @param array<DOMElement> $paramNodes
	 */
	function getSignature($paramNodes)
	{
		$arguments = array();
		
		foreach($paramNodes as $paramNode)
		{
			$signature = '';
			$paramName = $paramNode->getAttribute("name");
			$paramType = $paramNode->getAttribute("type");
			$defaultValue = $paramNode->getAttribute("default");
						
			if ($this->isSimpleType($paramType) || $paramType == "file")
				$signature .= "$".$paramName;
			else if ($paramType == "array" || $paramType == "map")
				$signature .= "array $".$paramName;
			else
				$signature .= $paramType." $".$paramName;
			
			
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
			
			$arguments[] = $signature;
		}
		
		return implode(', ', $arguments);
	}
	
	function writeMainClient(DOMNodeList $serviceNodes, DOMNodeList $configurationNodes)
	{
		$mainClassName = 'KalturaClient';
		$apiVersion = $this->_doc->documentElement->getAttribute('apiVersion');
		$date = date('y-m-d');
		
		if($this->generateDocs)
		{
			$this->appendLine('/**');
			$this->appendLine(" * @package $this->package");
			$this->appendLine(" * @subpackage $this->subpackage");
			$this->appendLine(' */');
		}
		
		$this->appendLine("class $mainClassName extends KalturaClientBase");
		$this->appendLine("{");
	
		foreach($serviceNodes as $serviceNode)
		{
			if($serviceNode->hasAttribute("plugin"))
				continue;
				
			$serviceName = $serviceNode->getAttribute("name");
			$description = ucfirst(trim($serviceNode->getAttribute("description"), " \t\r\n"));
			$serviceClassName = "Kaltura".$this->upperCaseFirstLetter($serviceName)."Service";
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
		$this->appendLine("	 * @param KalturaConfiguration \$config");
		$this->appendLine("	 */");
		$this->appendLine("	public function __construct(KalturaConfiguration \$config)");
		$this->appendLine("	{");
		$this->appendLine("		parent::__construct(\$config);");
		$this->appendLine("		");
		$this->appendLine("		\$this->setClientTag('php5:$date');");
		$this->appendLine("		\$this->setApiVersion('$apiVersion');");
		$this->appendLine("		");
		
		foreach($serviceNodes as $serviceNode)
		{
			if($serviceNode->hasAttribute("plugin"))
				continue;
				
			$serviceName = $serviceNode->getAttribute("name");
			$serviceClassName = "Kaltura".$this->upperCaseFirstLetter($serviceName)."Service";
			$this->appendLine("		\$this->$serviceName = new $serviceClassName(\$this);");
		}
		$this->appendLine("	}");
		$this->appendLine("	");
	
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
