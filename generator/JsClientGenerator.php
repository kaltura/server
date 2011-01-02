<?php
class JsClientGenerator extends ClientGeneratorFromXml
{
	/**
	 * @var SimpleXMLElement
	 */
	private $schemaXml;
	
	protected $enumTypes = "";
	protected $voClasses = "";
	protected $serviceClasses = "";
	protected $mainClass = "";
	
	/**
	* Constructor.
	* @param string $xmlPath path to schema xml.
	* @link http://www.kaltura.com/api_v3/api_schema.php
	*/
	function JsClientGenerator($xmlPath)
	{
		//set up the generator paths; path to schema xml and path to static source code files to copy.
		parent::ClientGeneratorFromXml($xmlPath, 'sources/js');
	}
	
	/**
	* Parses the higher-level of the schema, divide parsing to five steps:
	* Enum creation, Object (VO) classes, Services and actions, Main, and project file.
	*/
	public function generate() 
	{	
		$this->schemaXml = new SimpleXMLElement( $this->_xmlFile , NULL, TRUE);
		
		//parse object types
		foreach ($this->schemaXml->children() as $reflectionType) 
		{
			switch($reflectionType->getName())
			{
				case "enums":
					//create enum classes
					foreach($reflectionType->children() as $enums_node)
					{
						$this->writeEnum($enums_node);
					}
				break;
				case "classes":
					//create object classes
					foreach($reflectionType->children() as $classes_node)
					{
						$this->writeObjectClass($classes_node);
					}
				break;
				case "services":
					//implement services (api actions)
					foreach($reflectionType->children() as $services_node)
					{
						$this->writeService($services_node);
					}
					//write main class (if needed, this can also be included in the static sources folder if not dynamic)
					$this->writeMainClass($reflectionType->children());
				break;	
			}
		}
		$this->addFile('KalturaTypes.js', $this->enumTypes);
		$this->addFile('KalturaVO.js', $this->voClasses);
		$this->addFile('KalturaServices.js', $this->serviceClasses);
		$this->addFile('KalturaClient.js', $this->mainClass);
		//write project file (if needed, this can also be included in the static sources folder if not dynamic)
		$this->writeProjectFile();
	}
	
	/**
	 * dump a given text to a given variable and end one line.
	 * @param $addto 	the parameter to add the text to.
	 * @param $text 	the text to add.
	 */
	protected function echoLine(&$addto, $text = "")
	{
		$addto .= $text . "\r\n";
	}
	
	/**
	 * util function to capitalize the first letter of a given text.
	 * @param $wordtxt		the text to capitalize.
	 */
	protected function upperCaseFirstLetter($wordtxt)
	{
		if (strlen($wordtxt) > 0)
			$wordtxt = strtoupper(substr($wordtxt, 0, 1)).substr($wordtxt, 1);
		return $wordtxt;
	}
	
	/**
	* Parses Enum (aka. types) classes.
	* @param $enumNode		the xml node from the api schema representing an enum.  
	*/
	protected function writeEnum(SimpleXMLElement $enumNode)
	{
		$className = $enumNode->attributes()->name;
		$this->echoLine ($this->enumTypes, "\r\nfunction " . $className . "(){");
		$this->echoLine ($this->enumTypes, "}");
		//parse the constants
		foreach($enumNode->children() as $child) {
			switch($enumNode->attributes()->enumType)
			{
				case "string":
					$this->echoLine ($this->enumTypes, $className . '.' . $child->attributes()->name . " = \"" . $child->attributes()->value . "\";");
					break;
				default:
					$this->echoLine ($this->enumTypes, $className . '.' . $child->attributes()->name . " = " . $child->attributes()->value . ";");
					break;
			} 
		}
	}
	
	/**
	* Parses Object (aka. VO) classes.
	* @param $classNode		the xml node from the api schema representing a type class (Value Object).
	*/
	protected function writeObjectClass(SimpleXMLElement $classNode)
	{
		$classDesc = "/**\r\n";
		$classCode = "";
		$clasName = $classNode->attributes()->name;
		$this->echoLine ($classCode, "function $clasName(){");
		//parse the class properties
		foreach($classNode->children() as $classProperty) {
			$propType = $classProperty->attributes()->type;
			$propName = $classProperty->attributes()->name;
			$description = str_replace("\n", "\n *\t", $classProperty->attributes()->description); // to format multi-line descriptions
			$vardesc = " * @param\t$propName\t$propType\t\t$description";
			if ($classProperty->attributes()->readOnly == '1')
				$vardesc .= " (readOnly)";
			else if ($classProperty->attributes()->insertOnly == '1')
				$vardesc .= " (insertOnly)";
			$classDesc .= "$vardesc.\r\n";
			$this->echoLine ($classCode, "\tthis.$propName = null;");
		}
		$classDesc .= " */";
		$classCode .= "}";
		$classCode = $classDesc . "\r\n" . $classCode;
		$this->echoLine ($this->voClasses, $classCode);
		//parse the class base class (parent in heritage)
		if($classNode->attributes()->base) {
			$parentClass = $classNode->attributes()->base;
			//$this->echoLine ($this->voClasses, "$clasName.prototype = new " . $parentClass . "();");
			$this->echoLine ($this->voClasses, "$clasName.inheritsFrom ($parentClass);");
		} else {
			//$this->echoLine ($this->voClasses, "$clasName.prototype = new KalturaObjectBase();");
			$this->echoLine ($this->voClasses, "$clasName.inheritsFrom (KalturaObjectBase);");
		}
		//$this->echoLine ($this->voClasses, "$clasName.prototype.constructor = $clasName;");
		$this->echoLine ($this->voClasses, "\r\n");
	}
	
	/**
	* Parses Services and actions (calls that can be performed on the objects).
	* @param $serviceNodes		the xml node from the api schema representing an api service.
	*/
	protected function writeService(SimpleXMLElement $serviceNodes)
	{
		$serviceName = $serviceNodes->attributes()->name;
		$serviceClassName = "Kaltura".$this->upperCaseFirstLetter($serviceName)."Service";
		$serviceClass = "function $serviceClassName(client){\r\n";
		$serviceClass .= "\tthis.init(client);\r\n";
		$serviceClass .= "}\r\n";
		//$serviceClass .= "$serviceClassName.prototype = new KalturaServiceBase();\r\n";
		//$serviceClass .= "$serviceClassName.prototype.constructor = $serviceClassName;\r\n";
		$serviceClass .= "$serviceClassName.inheritsFrom (KalturaServiceBase);\r\n";
		
		$serviceClassDesc = "/**\r\n";
		$serviceClassDesc .= " *Class definition for the Kaltura service: $serviceName.\r\n";
		$actionsList = " * The available service actions:\r\n";
		
		//parse the service actions
		foreach($serviceNodes->children() as $action) {
			$actionDesc = "/**\r\n";
			$description = str_replace("\n", "\n *\t", $action->attributes()->description); // to format multi-line descriptions
			$actionDesc .= " * $description.\r\n";
			$actionsList .= " * @action\t".$action->attributes()->name."\t$description.\r\n";
			
			foreach($action->children() as $actionParam) {
				if($actionParam->getName() == "param" ) {
					$paramType = $actionParam->attributes()->type;
					$paramName = $actionParam->attributes()->name;
					$optionalp = (boolean)$actionParam->attributes()->optional;
					$defaultValue = trim($actionParam->attributes()->default);
					$enumType = $actionParam->attributes()->enumType;
					$description = str_replace("\n", "\n *\t", $actionParam->attributes()->description); // to format multi-line descriptions
					$info = array();
					if ($optionalp)
						$info[] = "optional";
					if ($enumType && $enumType != '')
						$info[] = "enum: $enumType";
					if ($defaultValue && $defaultValue != '')
						$info[] = "default: $defaultValue";
					if (count($info)>0)
						$infoTxt = ' ('.join(', ', $info).')';
					$vardesc = " * @param\t$paramName\t$paramType\t\t{$description}{$infoTxt}";
					$actionDesc .= "$vardesc.\r\n";
				} else {
					$rettype = $actionParam->attributes()->type;
					$actionDesc .= " * @return\t$rettype.\r\n";
				}
			}
			
			$actionDesc .= " */";
			$actionClass = $actionDesc . "\r\n";
			
			//create a service action
			$actionName = $action->attributes()->name;
			
			$paramNames = array('callback');
			foreach($action->children() as $actionParam)
				if($actionParam->getName() == "param" ) 
					$paramNames[] = $actionParam->attributes()->name;
			$paramNames = join(', ', $paramNames);
			
			// action method signature
			if (in_array($actionName, array("list", "clone", "delete"))) // because list & clone are preserved in PHP
				$actionSignature = "$serviceClassName.prototype.".$actionName."Action = function($paramNames)";
			else
				$actionSignature = "$serviceClassName.prototype.".$actionName." = function($paramNames)";
			
			$actionClass .= $actionSignature."{\r\n";
			
			//validate parameter default values
			foreach($action->children() as $actionParam) {
				if($actionParam->getName() != "param" )
					continue;
				if ($actionParam->attributes()->optional == '0')
					continue;
				
				$paramName = $actionParam->attributes()->name;
				switch($actionParam->attributes()->type)
				{
					case "string":
					case "float":
					case "int":
					case "bool":
					case "array":
						$defaultValue = strtolower($actionParam->attributes()->default);
						if ($defaultValue == 'false' || 
							$defaultValue == 'true' || 
							$defaultValue == 'null' || 
							is_numeric($defaultValue) )
								$defaultValue = $defaultValue;
						else 
							$defaultValue = '"'.$actionParam->attributes()->default.'"';
						break;
					default: //is Object
						$defaultValue = "null";
						break;
				}
				
				$actionClass .= "\tif(!$paramName)\r\n";
				$actionClass .= "\t\t$paramName = $defaultValue;\r\n";
			}
			 
			$actionClass .= "\tvar kparams = new Object();\r\n";
			
			$haveFiles = false;
			//parse the actions parameters and result types
			foreach($action->children() as $actionParam) {
				if($actionParam->getName() != "param" ) 
					continue;
				$paramName = $actionParam->attributes()->name;
				if ($haveFiles === false && $actionParam->attributes()->type == "file") {
			        $haveFiles = true;
		        	$actionClass .= "\tkfiles = new Object();\r\n";
		    	}
				switch($actionParam->attributes()->type)
				{
					case "string":
					case "float":
					case "int":
					case "bool":
						$actionClass .= "\tthis.client.addParam(kparams, \"$paramName\", $paramName);\r\n";
						break;
					case "file":
						$actionClass .= "\tthis.client.addParam(kfiles, \"$paramName\", $paramName);\r\n";
						break;
					case "array":
						$extraTab = "";
						if ($actionParam->attributes()->optional == '1') {
							$actionClass .= "\tif($paramName != null)\r\n";
							$extraTab = "\t";
						}
						$actionClass .= "{$extraTab}for(var index in $paramName)\r\n";
						$actionClass .= "{$extraTab}{\r\n";
						$actionClass .= "{$extraTab}\tvar obj = ${paramName}[index];\r\n";
						$actionClass .= "{$extraTab}\tthis.client.addParam(kparams, \"$paramName:\" + index, toParams(obj));\r\n";
						$actionClass .= "$extraTab}\r\n";
						break;
					default: //is Object
						if ($actionParam->attributes()->optional == '1') {
							$actionClass .= "\tif ($paramName != null)\r\n\t";
						}
						$actionClass .= "\tthis.client.addParam(kparams, \"$paramName\", toParams($paramName));\r\n";
						break;
				}
			}
			if ($haveFiles)
				$actionClass .= "\tthis.client.queueServiceActionCall(\"$serviceName\", \"$actionName\", kparams, kfiles);\r\n";
			else
				$actionClass .= "\tthis.client.queueServiceActionCall(\"$serviceName\", \"$actionName\", kparams);\r\n";
			$actionClass .= "\tif (!this.client.isMultiRequest())\r\n";
			$actionClass .= "\t\tthis.client.doQueue(callback);\r\n";
			$actionClass .= "}";
			$this->echoLine ($serviceClass, $actionClass);
		}
		$serviceClassDesc .= $actionsList;
		$serviceClassDesc .= "*/";
		$serviceClass = $serviceClassDesc . "\r\n" . $serviceClass;
		$this->echoLine ($this->serviceClasses, $serviceClass);
	}
	
	/**
	* Create the main class of the client library, may parse Services and actions.
	* initialize the service and assign to client to provide access to servcies and actions through the Kaltura client object.
	*/
	protected function writeMainClass(SimpleXMLElement $servicesNodes)
	{
		$apiVersion = $this->schemaXml->attributes()->apiVersion;
	
		$this->echoLine($this->mainClass, "/**");
		$this->echoLine($this->mainClass, " * The Kaltura Client - this is the facade through which all service actions should be called.");
		$this->echoLine($this->mainClass, " * @param config the Kaltura configuration object holding partner credentials (type: KalturaConfiguration).");
		$this->echoLine($this->mainClass, " */");
		$this->echoLine($this->mainClass, "function KalturaClient(config){");
		$this->echoLine($this->mainClass, "\tthis.init(config);");
		$this->echoLine($this->mainClass, "}");
		//$this->echoLine($this->mainClass, "KalturaClient.prototype = new KalturaClientBase();");
		//$this->echoLine($this->mainClass, "KalturaClient.prototype.constructor = KalturaClient;");
		$this->echoLine ($this->mainClass, "KalturaClient.inheritsFrom (KalturaClientBase);");
		$this->echoLine ($this->mainClass, "KalturaClient.prototype.apiVersion = \"$apiVersion\";");
		
		foreach($servicesNodes as $service_node)
		{
			$serviceName = $service_node->attributes()->name;
			$serviceClassName = "Kaltura".$this->upperCaseFirstLetter($serviceName)."Service";
			$this->echoLine($this->mainClass, "/**");
			$description = str_replace("\n", "\n *\t", $service_node->attributes()->description); // to format multi-line descriptions
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
		$this->echoLine($this->mainClass, "\t//call the super constructor:");
		$this->echoLine($this->mainClass, "\tKalturaClientBase.prototype.init.apply(this, arguments);");
		$this->echoLine($this->mainClass, "\t//initialize client services:");
		foreach($servicesNodes as $service_node)
		{
			$serviceName = $service_node->attributes()->name;
			$serviceClassName = "Kaltura".$this->upperCaseFirstLetter($serviceName)."Service";
			$this->echoLine($this->mainClass, "\tthis.$serviceName = new $serviceClassName(this);");
		}
		$this->echoLine($this->mainClass, "}");
	}
	
	/**
	* Create the project file (when needed).
	*/
	protected function writeProjectFile()
	{
		//override to implement the parsing and file creation.
		//to add a new file, use: $this->addFile('path to new file', 'file contents');
		//echo "Create Project File.\r\n";
	}
}
?>