<?php
class NodeClientGenerator extends ClientGeneratorFromXml
{
	/**
	 * @var SimpleXMLElement
	 */
	private $schemaXml;
	
	protected $enumTypes = '';
	protected $voClasses = '';
	protected $serviceClasses = '';
	protected $mainClass = '';
	
	/**
	 * Constructor.
	 * @param string $xmlPath path to schema xml.
	 * @link http://www.kaltura.com/api_v3/api_schema.php
	 */
	function __construct($xmlPath, Zend_Config $config, $sourcePath = "sources/node")
	{
		parent::__construct($xmlPath, $sourcePath, $config);
	}
	
	function getSingleLineCommentMarker()
	{
		return '//';
	}
	
	/**
	 * Parses the higher-level of the schema, divide parsing to five steps:
	 * Enum creation, Object (VO) classes, Services and actions, Main, and project file.
	 */
	public function generate()
	{
		parent::generate();
		
		$this->schemaXml = new SimpleXMLElement(file_get_contents($this->_xmlFile));
		
		$services = null;
		$configurations = null;
		
		//parse object types
		foreach($this->schemaXml->children() as $reflectionType)
		{
			switch($reflectionType->getName())
			{
				case 'enums':
					//create enum classes
					foreach($reflectionType->children() as $enums_node)
					{
						$this->writeEnum($enums_node);
					}
					break;
					
				case 'classes':
					$this->echoLine($this->voClasses, "var util = require('util');");
					$this->echoLine($this->voClasses, "var kaltura = require('./KalturaClientBase');");
					$this->echoLine($this->voClasses, "");
					
					//create object classes
					foreach($reflectionType->children() as $classes_node)
					{
						$this->writeObjectClass($classes_node);
					}
					break;
					
				case 'services':
					$this->echoLine($this->serviceClasses, "var util = require('util');");
					$this->echoLine($this->serviceClasses, "var kaltura = require('./KalturaClientBase');");
					$this->echoLine($this->serviceClasses, "");
					
					//implement services (api actions)
					foreach($reflectionType->children() as $services_node)
					{
						$this->writeService($services_node);
					}
					
					$services = $reflectionType->children();
					break;
					
				case 'configurations':
					$configurations = $reflectionType->children();
					break;
			}
		}
		
		//write main class (if needed, this can also be included in the static sources folder if not dynamic)
		$this->writeMainClass($services, $configurations);
		
		$this->addFile('KalturaTypes.js', $this->enumTypes);
		$this->addFile('KalturaVO.js', $this->voClasses);
		$this->addFile('KalturaServices.js', $this->serviceClasses);
		$this->addFile('KalturaClient.js', $this->mainClass);
		//write project file (if needed, this can also be included in the static sources folder if not dynamic)
		$this->writeProjectFile();
	}
	
	/**
	 * dump a given text to a given variable and end one line.
	 * @param $addto the parameter to add the text to.
	 * @param $text the text to add.
	 */
	protected function echoLine(&$addto, $text = '')
	{
		$addto .= $text . "\n";
	}
	
	/**
	 * util function to capitalize the first letter of a given text.
	 * @param $wordtxt the text to capitalize.
	 */
	protected function upperCaseFirstLetter($wordtxt)
	{
		if(strlen($wordtxt) > 0)
			$wordtxt = strtoupper(substr($wordtxt, 0, 1)) . substr($wordtxt, 1);
		return $wordtxt;
	}
	
	/**
	 * Parses Enum (aka. types) classes.
	 * @param $enumNode the xml node from the api schema representing an enum.
	 */
	protected function writeEnum(SimpleXMLElement $enumNode)
	{
		$className = $enumNode->attributes()->name;
		$this->echoLine($this->enumTypes, "\nvar $className = module.exports.$className = {");
		//parse the constants
		foreach($enumNode->children() as $child)
		{
			switch($enumNode->attributes()->enumType)
			{
				case 'string':
					$this->echoLine($this->enumTypes, $child->attributes()->name . " : '" . $child->attributes()->value . "',");
					break;
				default:
					$this->echoLine($this->enumTypes, $child->attributes()->name . " : " . $child->attributes()->value . ",");
					break;
			}
		}
		$this->echoLine($this->enumTypes, "};");
	}
	
	/**
	 * Parses Object (aka. VO) classes.
	 * @param $classNode the xml node from the api schema representing a type class (Value Object).
	 */
	protected function writeObjectClass(SimpleXMLElement $classNode)
	{
		$classDesc = "/**\n";
		$classCode = "";
		$clasName = $classNode->attributes()->name;
		$this->echoLine($classCode, "function $clasName(){");
		$this->echoLine($classCode, "	$clasName.super_.call(this);");
		//parse the class properties
		foreach($classNode->children() as $classProperty)
		{
			$propType = $classProperty->attributes()->type;
			$propType = $this->getJSType($propType);
			$propName = $classProperty->attributes()->name;
			
			// to format multi-line descriptions
			$description = array_map('trim', explode("\n", trim($classProperty->attributes()->description, ".\n\t\r "))); 
			$description = implode("\n * ", $description);
			
			$vardesc = " * @param $propName $propType $description";
			if($classProperty->attributes()->readOnly == '1')
				$vardesc .= " (readOnly)";
			else if($classProperty->attributes()->insertOnly == '1')
				$vardesc .= " (insertOnly)";
			$classDesc .= "$vardesc.\n";
			$this->echoLine($classCode, "	this.$propName = null;");
		}
		$classDesc .= " */";
		$classCode .= "}\n";
		$classCode .= "module.exports.$clasName = $clasName;\n";
		$classCode = $classDesc . "\n" . $classCode;
		$this->echoLine($this->voClasses, $classCode);
		//parse the class base class (parent in heritage)
		if($classNode->attributes()->base)
		{
			$parentClass = $classNode->attributes()->base;
			$this->echoLine($this->voClasses, "util.inherits($clasName, $parentClass);");
		} else
		{
			$this->echoLine($this->voClasses, "util.inherits($clasName, kaltura.KalturaObjectBase);");
		}
		$this->echoLine($this->voClasses, "\n");
	}
	
	/**
	 * Parses Services and actions (calls that can be performed on the objects).
	 * @param $serviceNodes the xml node from the api schema representing an api service.
	 */
	protected function writeService(SimpleXMLElement $serviceNodes)
	{
		$serviceName = $serviceNodes->attributes()->name;
		$serviceId = $serviceNodes->attributes()->id;
		$serviceClassName = 'Kaltura' . $this->upperCaseFirstLetter($serviceName) . 'Service';
		$serviceClass = "function $serviceClassName(client){\n";
		$serviceClass .= "	$serviceClassName.super_.call(this);\n";
		$serviceClass .= "	this.init(client);\n";
		$serviceClass .= "}\n";
		$serviceClass .= "\n";
		$serviceClass .= "util.inherits($serviceClassName, kaltura.KalturaServiceBase);\n";
		$serviceClass .= "module.exports.$serviceClassName = $serviceClassName;\n";
		$serviceClass .= "\n";
		
		$serviceClassDesc = "/**\n";
		$serviceClassDesc .= " *Class definition for the Kaltura service: $serviceName.\n";
		$actionsList = " * The available service actions:\n";
		
		//parse the service actions
		foreach($serviceNodes->children() as $action)
		{
			
			if($action->result->attributes()->type == 'file')
				continue;
			
			$actionDesc = "/**\n";
			// to format multi-line descriptions
			$description = array_map('trim', explode("\n", trim($action->attributes()->description, ".\n\t\r "))); 
			$description = implode("\n * ", $description);
			$actionDesc .= " * $description.\n";
			$actionsList .= " * @action " . $action->attributes()->name . " $description.\n";
			
			foreach($action->children() as $actionParam)
			{
				if($actionParam->getName() == 'param')
				{
					$paramType = $actionParam->attributes()->type;
					$paramType = $this->getJSType($paramType);
					$paramName = $actionParam->attributes()->name;
					$optionalp = (boolean) $actionParam->attributes()->optional;
					$defaultValue = trim($actionParam->attributes()->default);
					$enumType = $actionParam->attributes()->enumType;
					 
					// to format multi-line descriptions
					$description = array_map('trim', explode("\n", trim($actionParam->attributes()->description, ".\n\t\r "))); 
					$description = implode("\n * ", $description);
					
					$info = array();
					if($optionalp)
						$info[] = 'optional';
					if($enumType && $enumType != '')
						$info[] = "enum: $enumType";
					if($defaultValue && $defaultValue != '')
						$info[] = "default: $defaultValue";
					if(count($info) > 0)
						$infoTxt = ' (' . join(', ', $info) . ')';
					$vardesc = " * @param $paramName $paramType {$description}{$infoTxt}";
					$actionDesc .= "$vardesc.\n";
				} else
				{
					$rettype = $actionParam->attributes()->type;
					$actionDesc .= " * @return $rettype.\n";
				}
			}
			
			$actionDesc .= " */";
			$actionClass = $actionDesc . "\n";
			
			//create a service action
			$actionName = $action->attributes()->name;
			
			$paramNames = array('callback');
			foreach($action->children() as $actionParam)
				if($actionParam->getName() == 'param')
					$paramNames[] = $actionParam->attributes()->name;
			$paramNames = join(', ', $paramNames);
			
			// action method signature
			if(in_array($actionName, array('list', 'clone', 'delete', 'export'))) // because list & clone are preserved in PHP
				$actionSignature = "$serviceClassName.prototype." . $actionName . "Action = function($paramNames)";
			else
				$actionSignature = "$serviceClassName.prototype." . $actionName . " = function($paramNames)";
			
			$actionClass .= $actionSignature . "{\n";
			
			//validate parameter default values
			foreach($action->children() as $actionParam)
			{
				if($actionParam->getName() != 'param')
					continue;
				if($actionParam->attributes()->optional == '0')
					continue;
				
				$paramName = $actionParam->attributes()->name;
				switch($actionParam->attributes()->type)
				{
					case 'string':
					case 'float':
					case 'int':
					case 'bigint':
					case 'bool':
					case 'array':
						$defaultValue = strtolower($actionParam->attributes()->default);
						if($defaultValue == 'false' || $defaultValue == 'true' || $defaultValue == 'null' || is_numeric($defaultValue))
							$defaultValue = $defaultValue;
						else
							$defaultValue = "'" . $actionParam->attributes()->default . "'";
						break;
					default: //is Object
						$defaultValue = 'null';
						break;
				}
				
				$actionClass .= "	if(!$paramName){\n";
				$actionClass .= "		$paramName = $defaultValue;\n";
				$actionClass .= "	}\n";
			}
			
			$actionClass .= "	var kparams = {};\n";
			
			$haveFiles = false;
			//parse the actions parameters and result types
			foreach($action->children() as $actionParam)
			{
				if($actionParam->getName() != 'param')
					continue;
				$paramName = $actionParam->attributes()->name;
				if($haveFiles === false && $actionParam->attributes()->type == 'file')
				{
					$haveFiles = true;
					$actionClass .= "	var kfiles = {};\n";
				}
				switch($actionParam->attributes()->type)
				{
					case 'string':
					case 'float':
					case 'int':
					case 'bigint':
					case 'bool':
						$actionClass .= "	this.client.addParam(kparams, '$paramName', $paramName);\n";
						break;
					case 'file':
						$actionClass .= "	this.client.addParam(kfiles, '$paramName', $paramName);\n";
						break;
					case 'array':
						$extraTab = '';
						if($actionParam->attributes()->optional == '1')
						{
							$actionClass .= "	if($paramName !== null){\n";
							$extraTab = '	';
						}
						$actionClass .= "{$extraTab}for(var index in $paramName)\n";
						$actionClass .= "{$extraTab}{\n";
						$actionClass .= "{$extraTab}	var obj = ${paramName}[index];\n";
						$actionClass .= "{$extraTab}	this.client.addParam(kparams, '$paramName:' + index, kaltura.toParams(obj));\n";
						$actionClass .= "{$extraTab}}\n";
						if($actionParam->attributes()->optional == '1')
						{
							$actionClass .= "	}\n";
						}
						break;
					default: //is Object
						$extraTab = '';
						if($actionParam->attributes()->optional == '1')
						{
							$actionClass .= "	if ($paramName !== null){\n";
							$extraTab = '	';
						}
						$actionClass .= "$extraTab	this.client.addParam(kparams, '$paramName', kaltura.toParams($paramName));\n";
						if($actionParam->attributes()->optional == '1')
						{
							$actionClass .= "	}\n";
						}
						break;
				}
			}
			if($haveFiles)
				$actionClass .= "	this.client.queueServiceActionCall('$serviceId', '$actionName', kparams, kfiles);\n";
			else
				$actionClass .= "	this.client.queueServiceActionCall('$serviceId', '$actionName', kparams);\n";
			$actionClass .= "	if (!this.client.isMultiRequest()){\n";
			$actionClass .= "		this.client.doQueue(callback);\n";
			$actionClass .= "	}\n";
			$actionClass .= "};";
			$this->echoLine($serviceClass, $actionClass);
		}
		$serviceClassDesc .= $actionsList;
		$serviceClassDesc .= " */";
		$serviceClass = $serviceClassDesc . "\n" . $serviceClass;
		$this->echoLine($this->serviceClasses, $serviceClass);
	}
	
	/**
	 * Create the main class of the client library, may parse Services and actions.
	 * initialize the service and assign to client to provide access to servcies and actions through the Kaltura client object.
	 */
	protected function writeMainClass(SimpleXMLElement $servicesNodes, SimpleXMLElement $configurationNodes)
	{
		$apiVersion = $this->schemaXml->attributes()->apiVersion;
		$date = date('y-m-d');
		
		$this->echoLine($this->mainClass, "/**");
		$this->echoLine($this->mainClass, " * The Kaltura Client - this is the facade through which all service actions should be called.");
		$this->echoLine($this->mainClass, " * @param config the Kaltura configuration object holding partner credentials (type: KalturaConfiguration).");
		$this->echoLine($this->mainClass, " */");
		$this->echoLine($this->mainClass, "var util = require('util');");
		$this->echoLine($this->mainClass, "var kaltura = require('./KalturaClientBase');");
		$this->echoLine($this->mainClass, "kaltura.objects = require('./KalturaVO');");
		$this->echoLine($this->mainClass, "kaltura.services = require('./KalturaServices');");
		$this->echoLine($this->mainClass, "kaltura.enums = require('./KalturaTypes');");
		$this->echoLine($this->mainClass, "");
		$this->echoLine($this->mainClass, "function KalturaClient(config) {");
		$this->echoLine($this->mainClass, "	this.setApiVersion('$apiVersion');");
		$this->echoLine($this->mainClass, "	this.setClientTag('node:$date');");
		$this->echoLine($this->mainClass, "	this.init(config);");
		$this->echoLine($this->mainClass, "}");
		$this->echoLine($this->mainClass, "");
		$this->echoLine($this->mainClass, "module.exports = kaltura;");
		$this->echoLine($this->mainClass, "module.exports.KalturaClient = KalturaClient;");
		$this->echoLine($this->mainClass, "");
		$this->echoLine($this->mainClass, "util.inherits(KalturaClient, kaltura.KalturaClientBase);");
		$this->echoLine($this->mainClass, "");
		
		foreach($servicesNodes as $serviceNode)
		{
			$serviceName = $serviceNode->attributes()->name;
			$serviceClassName = 'kaltura.services.Kaltura' . $this->upperCaseFirstLetter($serviceName) . 'Service';
			$this->echoLine($this->mainClass, "/**");
			
			// to format multi-line descriptions
			$description = array_map('trim', explode("\n", trim($serviceNode->attributes()->description, ".\n\t\r "))); 
			$description = implode("\n * ", $description);
			
			$this->echoLine($this->mainClass, " * " . $description);
			$this->echoLine($this->mainClass, " * @param $serviceClassName");
			$this->echoLine($this->mainClass, " */");
			$this->echoLine($this->mainClass, "KalturaClient.prototype.$serviceName = null;");
		}
		$this->echoLine($this->mainClass, "/**");
		$this->echoLine($this->mainClass, " * The client constructor.");
		$this->echoLine($this->mainClass, " * @param config the Kaltura configuration object holding partner credentials (type: KalturaConfiguration).");
		$this->echoLine($this->mainClass, " */");
		$this->echoLine($this->mainClass, "KalturaClient.prototype.init = function(config){");
		$this->echoLine($this->mainClass, "	//call the super constructor:");
		$this->echoLine($this->mainClass, "	kaltura.KalturaClientBase.prototype.init.apply(this, arguments);");
		$this->echoLine($this->mainClass, "	//initialize client services:");
		foreach($servicesNodes as $serviceNode)
		{
			$serviceName = $serviceNode->attributes()->name;
			$serviceClassName = 'kaltura.services.Kaltura' . $this->upperCaseFirstLetter($serviceName) . 'Service';
			$this->echoLine($this->mainClass, "	this.$serviceName = new $serviceClassName(this);");
		}
		foreach($configurationNodes as $configurationName => $configurationNode)
		{
			$attributeName = lcfirst($configurationName) . "Configuration";
			$this->echoLine($this->mainClass, "	this.$attributeName = {};");
		}
		$this->echoLine($this->mainClass, "};");
		
		$volatileProperties = array();
		foreach($configurationNodes as $configurationName => $configurationNode)
		{
			/* @var $configurationNode SimpleXMLElement */
			$attributeName = lcfirst($configurationName) . "Configuration";
			$volatileProperties[$attributeName] = array();
		
			foreach($configurationNode->children() as $configurationProperty => $configurationPropertyNode)
			{
				/* @var $configurationPropertyNode SimpleXMLElement */
				
				if($configurationPropertyNode->attributes()->volatile)
				{
					$volatileProperties[$attributeName][] = $configurationProperty;
				}
				
				$type = $configurationPropertyNode->attributes()->type;
				$description = null;
				
				if($configurationPropertyNode->attributes()->description)
				{
					$description = $configurationPropertyNode->attributes()->description;
				}
				
				$this->writeConfigurationProperty($configurationName, $configurationProperty, $configurationProperty, $type, $description);
				
				if($configurationPropertyNode->attributes()->alias)
				{
					$this->writeConfigurationProperty($configurationName, $configurationPropertyNode->attributes()->alias, $configurationProperty, $type, $description);					
				}
			}
		}
		
		$this->echoLine($this->mainClass, "/**");
		$this->echoLine($this->mainClass, " * Clear all volatile configuration parameters");
		$this->echoLine($this->mainClass, " */");
		$this->echoLine($this->mainClass, "KalturaClient.prototype.resetRequest = function(){");
		foreach($volatileProperties as $attributeName => $properties)
		{
			foreach($properties as $propertyName)
				$this->echoLine($this->mainClass, "	delete this.{$attributeName}['{$propertyName}'];");
		}
		$this->echoLine($this->mainClass, "};");
		$this->echoLine($this->mainClass, "");
	
	}
	
	protected function writeConfigurationProperty($configurationName, $name, $paramName, $type, $description)
	{
		$methodsName = ucfirst($name);
		
		$this->echoLine($this->mainClass, "/**");
		if($description)
		{
			$this->echoLine($this->mainClass, " * $description");
			$this->echoLine($this->mainClass, " * ");
		}
		$this->echoLine($this->mainClass, " * @param $type $name");
		$this->echoLine($this->mainClass, " */");
		$this->echoLine($this->mainClass, "KalturaClient.prototype.set{$methodsName} = function($name){");
		$this->echoLine($this->mainClass, "	this.{$configurationName}Configuration['{$paramName}'] = {$name};");
		$this->echoLine($this->mainClass, "};");
		$this->echoLine($this->mainClass, "");
	
		
		$this->echoLine($this->mainClass, "/**");
		if($description)
		{
			$this->echoLine($this->mainClass, " * $description");
			$this->echoLine($this->mainClass, " * ");
		}
		$this->echoLine($this->mainClass, " * @return $type");
		$this->echoLine($this->mainClass, " */");
		$this->echoLine($this->mainClass, "KalturaClient.prototype.get{$methodsName} = function(){");
		$this->echoLine($this->mainClass, "	return this.{$configurationName}Configuration['{$paramName}'];");
		$this->echoLine($this->mainClass, "};");
		$this->echoLine($this->mainClass, "");
	}
	
	/**
	 * Create the project file (when needed).
	 */
	protected function writeProjectFile()
	{
		//override to implement the parsing and file creation.
	//to add a new file, use: $this->addFile('path to new file', 'file contents');
	//echo "Create Project File.\n";
	}
	
	public function getJSType($propType)
	{
		switch($propType)
		{
			case 'bigint':
				return 'int';
			
			default:
				return $propType;
		}
	}
}
