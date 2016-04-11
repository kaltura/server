
<?php
class CSharpClientGenerator extends ClientGeneratorFromXml
{
	private $_doc = null;
	private $_csprojIncludes = array();
	private $_classInheritance = array();
	private $_enums = array();
	
	function __construct($xmlPath, Zend_Config $config, $sourcePath = "sources/csharp")
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
		
		$this->removeFilesFromSource();
		
		$xpath = new DOMXPath($this->_doc);
		$this->loadClassInheritance($xpath->query("/xml/classes/class"));
		$this->loadEnums($xpath->query("/xml/enums/enum"));
		
		// enumes $ types
		$enumNodes = $xpath->query("/xml/enums/enum");
		foreach($enumNodes as $enumNode)
		{
			$this->writeEnum($enumNode);
		}
		
		$classNodes = $xpath->query("/xml/classes/class");
		foreach($classNodes as $classNode)
		{
			$this->writeClass($classNode);
		}
		
		$this->writeObjectFactoryClass($classNodes);
		
		$serviceNodes = $xpath->query("/xml/services/service");
		
		$this->startNewTextBlock();
		foreach($serviceNodes as $serviceNode)
		{
			$this->writeService($serviceNode);
		}
		
		$configurationNodes = $xpath->query("/xml/configurations/*");
		$this->writeMainClient($serviceNodes, $configurationNodes);
		
		$this->writeCsproj();
	}
	
	function writeEnum(DOMElement $enumNode)
	{
		$enumName = $enumNode->getAttribute("name");
		$s = "";
		$s .= "namespace Kaltura"."\n";
		$s .= "{"."\n";
		
		if ($enumNode->getAttribute("enumType") == "string")
		{
			$s .= "	public sealed class $enumName : KalturaStringEnum"."\n";
			$s .= "	{"."\n";
			
			foreach($enumNode->childNodes as $constNode)
			{
				if ($constNode->nodeType != XML_ELEMENT_NODE)
					continue;
					
				$propertyName = $constNode->getAttribute("name");
				$propertyValue = $constNode->getAttribute("value");
				$s .= "		public static readonly $enumName $propertyName = new $enumName(\"$propertyValue\");"."\n";
			}
			$s .= "\n";
			$s .= "		private $enumName(string name) : base(name) { }"."\n";
			$s .= "	}"."\n";
			$s .= "}"."\n";
		}
		else
		{
			$s .= "	public enum $enumName"."\n";
			$s .= "	{"."\n";
			
			foreach($enumNode->childNodes as $constNode)
			{
				if ($constNode->nodeType != XML_ELEMENT_NODE)
					continue;
					
				$propertyName = $constNode->getAttribute("name");
				$propertyValue = $constNode->getAttribute("value");
				$s .= "		$propertyName = $propertyValue,"."\n";
			}
			$s .= "	}"."\n";
			$s .= "}"."\n";
		}
		$file = "Enums/$enumName.cs";
		$this->addFile("KalturaClient/".$file, $s);
		$this->_csprojIncludes[] = $file; 
	}
	
	function writeClass(DOMElement $classNode)
	{
		$type = $classNode->getAttribute("name");
		if($type == 'KalturaObject')
			return;
		
		$this->startNewTextBlock();
		$this->appendLine("using System;");
		$this->appendLine("using System.Xml;");
		$this->appendLine("using System.Collections.Generic;");
		$this->appendLine();
		$this->appendLine("namespace Kaltura");
		$this->appendLine("{");
		
		// class definition
		if ($classNode->hasAttribute("base"))
		{
			$this->appendLine("	public class $type : ".$classNode->getAttribute("base"));
		}
		else
		{
			$this->appendLine("	public class $type : KalturaObjectBase");
		}
		$this->appendLine("	{");
		
		// we want to make the orderBy property strongly typed with the corresponding string enum
		$isFilter = false;
		if ($this->isClassInherit($type, "KalturaFilter")) 
		{
			$orderByType = str_replace("Filter", "OrderBy", $type);
			if ($this->enumExists($orderByType)) 
			{
				$orderByElement = $classNode->ownerDocument->createElement("property");
				$orderByElement->setAttribute("name", "orderBy");
				$orderByElement->setAttribute("type", "string");
				$orderByElement->setAttribute("enumType", $orderByType);
				$classNode->appendChild($orderByElement);
				$isFilter = true;
			}
		}

		$properties = array();
		foreach($classNode->childNodes as $propertyNode)
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			$property = array(
				"name" => null,
				"type" => null,
				"default" => null,
				"isNew" => false
			);
			
			$propType = $propertyNode->getAttribute("type");
			$propName = $propertyNode->getAttribute("name");
			$isEnum = $propertyNode->hasAttribute("enumType");
			$dotNetPropName = $this->upperCaseFirstLetter($propName);
			$property["name"] = $dotNetPropName;
			
			if ($isEnum)
			{
				$dotNetPropType = $propertyNode->getAttribute("enumType");
			}
			else if ($propType == "array")
			{
				$arrayObjectType = $propertyNode->getAttribute("arrayType");
				if($arrayObjectType == 'KalturaObject')
					$arrayObjectType = 'KalturaObjectBase';
				$dotNetPropType = "IList<$arrayObjectType>";
			}
			else if ($propType == "map")
			{
				$arrayObjectType = $propertyNode->getAttribute("arrayType");
				if($arrayObjectType == 'KalturaObject')
					$arrayObjectType = 'KalturaObjectBase';
				$dotNetPropType = "IDictionary<string, $arrayObjectType>";
			}
			else if ($propType == "bool")
			{
				$dotNetPropType  = "bool?";
			}
			else if ($propType == "bigint")
			{
				$dotNetPropType  = "long";
			}
			else if ($propType == "time")
			{
				$dotNetPropType  = "int";
			}
			else 
			{
				$dotNetPropType = $propType;
			}
				
			$property["type"] = $dotNetPropType;
			if ($isFilter && $dotNetPropName == "OrderBy")
				$property["isNew"] = true;
				
			switch($propType)
			{
				case "bigint":
					$property["default"] = "long.MinValue";
					break;
				case "int":
					if ($isEnum)
						$property["default"] = "($dotNetPropType)Int32.MinValue";
					else
						$property["default"] = "Int32.MinValue";
					break;
				case "string":
					$property["default"] = "null";
					break;
				case "bool":
					$property["default"] = "null";
					break;
				case "float":
					$property["default"] = "Single.MinValue";
					break;
			}
			
			$properties[] = $property;
		}
		// private fields
		$this->appendLine("		#region Private Fields");
		foreach($properties as $property)
		{
			$propertyLine = "private {$property['type']} _{$property['name']}";
			
			if (!is_null($property["default"]))
				$propertyLine .= " = {$property['default']}";
				
			$propertyLine .= ";";
			
			$this->appendLine("		" . $propertyLine);
		}
		$this->appendLine("		#endregion");
		
		$this->appendLine();
		
		// properties 
		$this->appendLine("		#region Properties");
		foreach($properties as $property)
		{
			$propertyLine = "public";
			if ($property['isNew'])
				$propertyLine .= " new";
			
			$propertyLine .= " {$property['type']} {$property['name']}";
			
			$this->appendLine("		" . $propertyLine);
			$this->appendLine("		{");
			$this->appendLine("			get { return _{$property['name']}; }");
			$this->appendLine("			set ");
			$this->appendLine("			{ ");
			$this->appendLine("				_{$property['name']} = value;");
			$this->appendLine("				OnPropertyChanged(\"{$property['name']}\");");
			$this->appendLine("			}");
			$this->appendLine("		}");
		}
		$this->appendLine("		#endregion");
		$this->appendLine();
		
		$this->appendLine("		#region CTor");
		// CTor
		$this->appendLine("		public $type()");
		$this->appendLine("		{");
		$this->appendLine("		}");
		$this->appendLine("");
		
		$this->appendLine("		public $type(XmlElement node) : base(node)");		
		$this->appendLine("		{");
		if ($classNode->childNodes->length)
		{
			$this->appendLine("			foreach (XmlElement propertyNode in node.ChildNodes)"); 
			$this->appendLine("			{");
			$this->appendLine("				string txt = propertyNode.InnerText;");
			$this->appendLine("				switch (propertyNode.Name)");
			$this->appendLine("				{");
			foreach($classNode->childNodes as $propertyNode)
			{
				if ($propertyNode->nodeType != XML_ELEMENT_NODE)
					continue;
					
				$propType = $propertyNode->getAttribute("type");
				$propName = $propertyNode->getAttribute("name");
				$isEnum = $propertyNode->hasAttribute("enumType");
				$dotNetPropName = $this->upperCaseFirstLetter($propName);
				$this->appendLine("					case \"$propName\":");
				switch($propType)
				{
					case "bigint":
						$this->appendLine("						this.$dotNetPropName = ParseLong(txt);");
						break;
					case "int":
					case "time":
						if ($isEnum)
						{
							$enumType = $propertyNode->getAttribute("enumType");
							$this->appendLine("						this.$dotNetPropName = ($enumType)ParseEnum(typeof($enumType), txt);");
						}
						else
							$this->appendLine("						this.$dotNetPropName = ParseInt(txt);");
						break;
					case "string":
						if ($isEnum)
						{
							$enumType = $propertyNode->getAttribute("enumType");
							$this->appendLine("						this.$dotNetPropName = ($enumType)KalturaStringEnum.Parse(typeof($enumType), txt);");
						}
						else
							$this->appendLine("						this.$dotNetPropName = txt;");
						break;
					case "bool":
						$this->appendLine("						this.$dotNetPropName = ParseBool(txt);");
						break;
					case "float":
						$this->appendLine("						this.$dotNetPropName = ParseFloat(txt);");
						break;
					case "array":
						$arrayType = $propertyNode->getAttribute("arrayType");
						if($arrayType == 'KalturaObject')
							$arrayType = 'KalturaObjectBase';
							
						$this->appendLine("						this.$dotNetPropName = new List<$arrayType>();");
						$this->appendLine("						foreach(XmlElement arrayNode in propertyNode.ChildNodes)");
						$this->appendLine("						{");
						$this->appendLine("							this.$dotNetPropName.Add(($arrayType)KalturaObjectFactory.Create(arrayNode, \"$arrayType\"));");
						$this->appendLine("						}");
						break;
					case "map":
						$arrayType = $propertyNode->getAttribute("arrayType");
						if($arrayType == 'KalturaObject')
							$arrayType = 'KalturaObjectBase';
							
						$this->appendLine("						{");		// TODO: remove the index once the keys are added to the response
						$this->appendLine("							string key;");
						$this->appendLine("							this.$dotNetPropName = new Dictionary<string, $arrayType>();");
						$this->appendLine("							foreach(XmlElement arrayNode in propertyNode.ChildNodes)");
						$this->appendLine("							{");
						$this->appendLine("								key = arrayNode[\"itemKey\"].InnerText;;");
						$this->appendLine("								this.{$dotNetPropName}[key] = ($arrayType)KalturaObjectFactory.Create(arrayNode, \"$arrayType\");");
						$this->appendLine("							}");
						$this->appendLine("						}");
						break;
					default: // sub object
						$this->appendLine("						this.$dotNetPropName = ($propType)KalturaObjectFactory.Create(propertyNode, \"$propType\");");
						break;
				}
				$this->appendLine("						continue;");
			}
			$this->appendLine("				}");
			$this->appendLine("			}");
		}
		$this->appendLine("		}");
		$this->appendLine("		#endregion");
		$this->appendLine("");
		
		$this->appendLine("		#region Methods");
		// ToParams method
		$this->appendLine("		public override KalturaParams ToParams()");
		$this->appendLine("		{");
		$this->appendLine("			KalturaParams kparams = base.ToParams();");
		$this->appendLine("			kparams.AddReplace(\"objectType\", \"$type\");");
		foreach($classNode->childNodes as $propertyNode)
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
				
			$propName = $propertyNode->getAttribute("name");
			$dotNetPropName = $this->upperCaseFirstLetter($propName);			
			$this->appendLine("			kparams.AddIfNotNull(\"$propName\", this.$dotNetPropName);");
		}
		$this->appendLine("			return kparams;");
		$this->appendLine("		}");
		$this->appendLine("		#endregion");
		
		// close class
		$this->appendLine("	}");
		$this->appendLine("}");
		$this->appendLine();
		
		$file = "Types/$type.cs";
		$this->addFile("KalturaClient/".$file, $this->getTextBlock());
		$this->_csprojIncludes[] = $file; 
	}
	
	function writeObjectFactoryClass(DOMNodeList $classNodes)
	{
		$this->startNewTextBlock();
		$this->appendLine("using System;");
		$this->appendLine("using System.Text;");
		$this->appendLine("using System.Xml;");
		$this->appendLine("using System.Runtime.Serialization;");
		$this->appendLine();
		$this->appendLine("namespace Kaltura");
		$this->appendLine("{");
		$this->appendLine("	public static class KalturaObjectFactory");
		$this->appendLine("	{");
		$this->appendLine("		public static object Create(XmlElement xmlElement, string fallbackClass)");
		$this->appendLine("		{");
		$this->appendLine("			if (xmlElement[\"objectType\"] == null)");
		$this->appendLine("			{");
		$this->appendLine("				return null;");
		$this->appendLine("			}");
		$this->appendLine("			string className = xmlElement[\"objectType\"].InnerText;");
		$this->appendLine("			Type type = Type.GetType(\"Kaltura.\" + className);");
		$this->appendLine("			if (type == null)");
		$this->appendLine("			{");
		$this->appendLine("				if (fallbackClass != null)");
		$this->appendLine("					type = Type.GetType(fallbackClass);");
		$this->appendLine("			}");
		$this->appendLine("			");
		$this->appendLine("			if(type == null)");
		$this->appendLine("				throw new SerializationException(\"Invalid object type\");");
		$this->appendLine("			return System.Activator.CreateInstance(type, xmlElement);");
		$this->appendLine("		}");
		$this->appendLine("	}");
		$this->appendLine("}");
		
		$file = "KalturaObjectFactory.cs";
		$this->addFile("KalturaClient/".$file, $this->getTextBlock());
		$this->_csprojIncludes[] = $file;
	}
	
	function writeCsproj()
	{
		$csprojDoc = new KDOMDocument();
		$csprojDoc->formatOutput = true;
		$csprojDoc->load($this->_sourcePath."/KalturaClient/KalturaClient.csproj");
		$csprojXPath = new DOMXPath($csprojDoc);
		$csprojXPath->registerNamespace("m", "http://schemas.microsoft.com/developer/msbuild/2003");
		$compileNodes = $csprojXPath->query("//m:ItemGroup/m:Compile/..");
		$compileItemGroupElement = $compileNodes->item(0); 
		
		foreach($this->_csprojIncludes as $include)
		{
			$compileElement = $csprojDoc->createElement("Compile");
			$compileElement->setAttribute("Include", str_replace("/","\\", $include));
			$compileItemGroupElement->appendChild($compileElement);
		}
		$this->addFile("KalturaClient/KalturaClient.csproj", $csprojDoc->saveXML(), false);
	}
	
	function writeService(DOMElement $serviceNode)
	{
		$this->startNewTextBlock();
		$this->appendLine("using System;");
		$this->appendLine("using System.Xml;");
		$this->appendLine("using System.Collections.Generic;");
		$this->appendLine("using System.IO;");
		$this->appendLine();
		$this->appendLine("namespace Kaltura");
		$this->appendLine("{");
		$serviceName = $serviceNode->getAttribute("name");
		$serviceId = $serviceNode->getAttribute("id");

		
		$dotNetServiceName = $this->upperCaseFirstLetter($serviceName)."Service";
		$dotNetServiceType = "Kaltura" . $dotNetServiceName;
		
		$this->appendLine();
		$this->appendLine("	public class $dotNetServiceType : KalturaServiceBase");
		$this->appendLine("	{");
		$this->appendLine("	public $dotNetServiceType(KalturaClient client)");
		$this->appendLine("			: base(client)");
		$this->appendLine("		{");
		$this->appendLine("		}");	   
		 
		
		$actionNodes = $serviceNode->childNodes;
		foreach($actionNodes as $actionNode)
		{
			if ($actionNode->nodeType != XML_ELEMENT_NODE)
				continue;
				
			$this->writeAction($serviceId, $actionNode);
		}
		$this->appendLine("	}");
		$this->appendLine("}");
		
		$file = "Services/".$dotNetServiceName.".cs";
		$this->addFile("KalturaClient/".$file, $this->getTextBlock());
		$this->_csprojIncludes[] = $file; 
	}
	
	function writeAction($serviceId, DOMElement $actionNode)
	{
		$action = $actionNode->getAttribute("name");
		$resultNode = $actionNode->getElementsByTagName("result")->item(0);
		$resultType = $resultNode->getAttribute("type");
		$arrayObjectType = ($resultType == 'array') ? $resultNode->getAttribute("arrayType" ) : null;
	    
	    if($resultType == 'file')
	    	return;
	
		$enableInMultiRequest = true;
		if($actionNode->hasAttribute("enableInMultiRequest"))
		{
			$enableInMultiRequest = intval($actionNode->getAttribute("enableInMultiRequest"));
		}
		
		switch($resultType)
		{
			case null:
				$dotNetOutputType = "void";
				break;
			case "array":
				$arrayType = $resultNode->getAttribute("arrayType"); 
				$dotNetOutputType = "IList<".$arrayType.">";
				break;
			case "map":
				$arrayType = $resultNode->getAttribute("arrayType");
				$dotNetOutputType = "IDictionary<string, ".$arrayType.">";
				break;
			case "bigint":
				$dotNetOutputType = "long";
				break;
			default:
				$dotNetOutputType = $resultType;
				break;
		}
			
		$signaturePrefix = "public $dotNetOutputType ".$this->upperCaseFirstLetter($action)."(";
			
		$paramNodes = $actionNode->getElementsByTagName("param");
		
		// check for needed overloads
		$mandatoryParams = array();
		$optionalParams = array();
		foreach($paramNodes as $paramNode)
		{
			$optional = $paramNode->getAttribute("optional");
			if ($optional == "1")
				$optionalParams[] = $paramNode;
			else
				$mandatoryParams[] = $paramNode;
		}
		
		for($overloadNumber = 0; $overloadNumber < count($optionalParams); $overloadNumber++)
		{
			$currentOptionalParams = array_slice($optionalParams, 0, $overloadNumber);
			$defaultParams = array_slice(array_merge($mandatoryParams, $optionalParams), 0, count($mandatoryParams) + $overloadNumber + 1);
			$signature = $this->getSignature(array_merge($mandatoryParams, $currentOptionalParams));
			
			// write the overload
			$this->appendLine();	
			$this->appendLine("		$signaturePrefix$signature");
			$this->appendLine("		{");
			
			$paramsStr = "";
			foreach($defaultParams as $paramNode)
			{
				$optional = $paramNode->getAttribute("optional");
				if ($optional == "1" && ! in_array ( $paramNode, $currentOptionalParams, true ))
				{
					$type = $paramNode->getAttribute("type");
					if ($type == "string")
					{
						$value = $paramNode->getAttribute("default");
						if($value == 'null')
							$paramsStr .=  "null";
						else
							$paramsStr .=  "\"".$paramNode->getAttribute("default")."\"";
					}
					else if ($type == "int" && $paramNode->hasAttribute("enumType"))
					{
						$value = $paramNode->getAttribute("default");
						if ($value == "null")
							$value = "Int32.MinValue";
						$paramsStr .=  "(".$paramNode->getAttribute("enumType").")(".$value.")";
					}
					elseif ($type == "int" && $paramNode->getAttribute("default") == "null") // because Partner.GetUsage has an int field with empty default value
						$paramsStr .= "Int32.MinValue";
					else
						$paramsStr .=  $paramNode->getAttribute("default");
				}
				else
				{
					$paramName = $paramNode->getAttribute("name");
					$paramsStr .=  $this->fixParamName($paramName);
				}
				
				$paramsStr .= ", ";
			}
			
			if ($this->endsWith($paramsStr, ", "))
				$paramsStr = substr($paramsStr, 0, strlen($paramsStr) - 2);
				
			if($resultType)
				$this->appendLine("			return this.".$this->upperCaseFirstLetter($action)."($paramsStr);");
			else
				$this->appendLine("			this.".$this->upperCaseFirstLetter($action)."($paramsStr);");
				
			$this->appendLine("		}");
		}
		
		$signature = $this->getSignature(array_merge($mandatoryParams, $optionalParams));
		
		$this->appendLine();	
		$this->appendLine("		$signaturePrefix$signature");
		$this->appendLine("		{");
		
		if(!$enableInMultiRequest)
		{
			$this->appendLine("			if (this._Client.IsMultiRequest)");
			$this->appendLine("				throw new Exception(\"Action is not supported as part of multi-request.\");");
		}
		
		$this->appendLine("			KalturaParams kparams = new KalturaParams();");
		$haveFiles = false;
		foreach($paramNodes as $paramNode)
		{
			$paramType = $paramNode->getAttribute("type");
			$paramName = $paramNode->getAttribute("name");
			$isEnum = $paramNode->hasAttribute("enumType");
			
			if ($paramType === "file")
			{
				if ($haveFiles === false)
				{
					$haveFiles = true;
					$this->appendLine("			KalturaFiles kfiles = new KalturaFiles();");
				}	 
				$this->appendLine("			kfiles.Add(\"$paramName\", ".$this->fixParamName($paramName).");");
			}
			else
			{
				$this->appendLine("			kparams.AddIfNotNull(\"$paramName\", ".$this->fixParamName($paramName).");");
			}
		}
		
		$fallbackClass = 'null';
    	if($resultType == 'array')
    		$fallbackClass = "\"$arrayObjectType\"";
    	else if($resultType && !$this->isSimpleType($resultType))
    		$fallbackClass = "\"$resultType\"";
		
		if ($haveFiles)
			$this->appendLine("			_Client.QueueServiceCall(\"$serviceId\", \"$action\", $fallbackClass, kparams, kfiles);");
		else
			$this->appendLine("			_Client.QueueServiceCall(\"$serviceId\", \"$action\", $fallbackClass, kparams);");
			
		if($enableInMultiRequest)
		{
			$this->appendLine("			if (this._Client.IsMultiRequest)");
			if (!$resultType) 
			{
				$this->appendLine("				return;");
			}
			else if ($resultType == "int" || $resultType == "bigint" || $resultNode == "float")
			{
				$this->appendLine("				return 0;");
			}
			else if ($resultType == "bool")
			{
				$this->appendLine("				return false;");
			}
			else
			{
				$this->appendLine("				return null;");
			}
		}

		$this->appendLine("			XmlElement result = _Client.DoQueue();"); 
		
		if ($resultType)
		{
			switch ($resultType)
			{
				case "array":
					$arrayType = $resultNode->getAttribute("arrayType");
					$this->appendLine("			IList<$arrayType> list = new List<$arrayType>();");
					$this->appendLine("			foreach(XmlElement node in result.ChildNodes)");
					$this->appendLine("			{");
					$this->appendLine("				list.Add(($arrayType)KalturaObjectFactory.Create(node, \"$arrayType\"));");
					$this->appendLine("			}");
					$this->appendLine("			return list;");
					break;
				case "map":
					$arrayType = $resultNode->getAttribute("arrayType");
					$this->appendLine("			string key;");
					$this->appendLine("			IDictionary<string, $arrayType> map = new Dictionary<string, $arrayType>();");
					$this->appendLine("			foreach(XmlElement node in result.ChildNodes)");
					$this->appendLine("			{");
					$this->appendLine("				key = xmlElement[\"itemKey\"]");
					$this->appendLine("				map.Add(key, ($arrayType)KalturaObjectFactory.Create(node, \"$arrayType\"));");
					$this->appendLine("			}");
					$this->appendLine("			return map;");
					break;
				case "bigint":
					$this->appendLine("			return long.Parse(result.InnerText);");
					break;
				case "int":
					$this->appendLine("			return int.Parse(result.InnerText);");
					break;
				case "float":
					$this->appendLine("			return Single.Parse(result.InnerText);");
					break;
				case "bool":
					$this->appendLine("			if (result.InnerText == \"1\")");
					$this->appendLine("				return true;");
					$this->appendLine("			return false;");
					break;
				case "string":
					$this->appendLine("			return result.InnerText;");
					break;
				default:
					$this->appendLine("			return ($resultType)KalturaObjectFactory.Create(result, \"$resultType\");");
					break;
			}
		}
		$this->appendLine("		}");
	}
	
	function getSignature($paramNodes)
	{
		$signature = "";
		foreach($paramNodes as $paramNode)
		{
			$paramType = $paramNode->getAttribute("type");
			$paramName = $paramNode->getAttribute("name");
			$isEnum = $paramNode->hasAttribute("enumType");
			
			switch($paramType)
			{
				case "array":
					$dotNetType = "IList<".$paramNode->getAttribute("arrayType").">";
					break;
				case "map":
					$dotNetType = "IDictionary<string, ".$paramNode->getAttribute("arrayType").">";
					break;
				case "file":
					$dotNetType = "Stream";
					break;
				case "bigint":
					$dotNetType = "long";
					break;
				case "int":
					if ($isEnum)
						$dotNetType = $paramNode->getAttribute("enumType");
					else 
						$dotNetType = $paramType;			
					break;			
				default:
					if ($isEnum)
						$dotNetType = $paramNode->getAttribute("enumType");
					else 
						$dotNetType = $paramType;		 
					break;
			}

			$signature .= "$dotNetType ".$this->fixParamName($paramName).", ";
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
	
		$this->startNewTextBlock();
		
		$this->appendLine("using System;");
		$this->appendLine();
		
		$this->appendLine("namespace Kaltura");
		$this->appendLine("{");
		$this->appendLine("	public class KalturaClient : KalturaClientBase");
		$this->appendLine("	{");
		$this->appendLine("		public KalturaClient(KalturaConfiguration config) : base(config)");
		$this->appendLine("		{");
		$this->appendLine("				ApiVersion = \"$apiVersion\";");
		$this->appendLine("				ClientTag = \"dotnet:$date\";");
		$this->appendLine("		}");
		foreach($serviceNodes as $serviceNode)
		{
			$serviceName = $serviceNode->getAttribute("name");
			$dotNetServiceName = $this->upperCaseFirstLetter($serviceName)."Service";
			$dotNetServiceType = "Kaltura" . $dotNetServiceName;
			
			$this->appendLine();		
			$this->appendLine("		$dotNetServiceType _$dotNetServiceName;");
			$this->appendLine("		public $dotNetServiceType $dotNetServiceName");
			$this->appendLine("		{");
			$this->appendLine("			get");
			$this->appendLine("			{");
			$this->appendLine("				if (_$dotNetServiceName == null)");
			$this->appendLine("					_$dotNetServiceName = new $dotNetServiceType(this);");
			$this->appendLine("");
			$this->appendLine("				return _$dotNetServiceName;");
			$this->appendLine("			}");
			$this->appendLine("		}");
		}
		$this->appendLine("	");
		
	
		$this->appendLine("		#region Properties");
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
		$this->appendLine("		#endregion");
		$this->appendLine("		");
		
		$this->appendLine("		new protected void resetRequest()");
		$this->appendLine("		{");
		$this->appendLine("			base.resetRequest();");
		foreach($volatileProperties as $attributeName => $properties)
		{
			foreach($properties as $propertyName)
			{
				$this->appendLine("			this.{$attributeName}.Remove(\"{$propertyName}\");");
			}
		}
		$this->appendLine("		}");
		$this->appendLine("	}");
		$this->appendLine("}");
		
		$this->addFile("KalturaClient/KalturaClient.cs", $this->getTextBlock());

		// not needed because it is included in the sources
		//$this->_csprojIncludes[] = "KalturaClient.cs";
	}
	
	protected function writeConfigurationProperty($configurationName, $name, $paramName, $type, $description)
	{
		$methodsName = ucfirst($name);
		if($name == 'ks')
			$methodsName = 'KS';
			
		$null = 'null';
		switch($type)
		{
			case 'int':
				$null = 'int.MinValue';
				break;
				
			case 'float':
				$null = 'float.MinValue';
				break;
				
			case 'bigint':
				$type = 'long';
				$null = 'long.MinValue';
				break;
		}
		
		$this->appendLine(" 	public $type $methodsName");
		$this->appendLine(" 	{");
		$this->appendLine(" 		get");
		$this->appendLine(" 		{");
		$this->appendLine(" 			if (requestConfiguration.ContainsKey(\"{$paramName}\"))");
		$this->appendLine(" 				return ($type) {$configurationName}Configuration[\"{$paramName}\"];");
		$this->appendLine(" 			return $null;");
		$this->appendLine(" 		}");
		$this->appendLine(" 		set");
		$this->appendLine(" 		{");
		$this->appendLine(" 			if (requestConfiguration.ContainsKey(\"{$paramName}\"))");
		$this->appendLine(" 				{$configurationName}Configuration.Remove(\"{$paramName}\");");
		$this->appendLine(" 			{$configurationName}Configuration.Add(\"{$paramName}\", value);");
		$this->appendLine(" 		}");
		$this->appendLine(" 	}");
		$this->appendLine("	");
	}
	
	private function loadEnums(DOMNodeList $enums)
	{
		foreach($enums as $item)
		{
			$this->_enums[$item->getAttribute("name")] = null;
		}
	}
	
	private function loadClassInheritance(DOMNodeList $classes)
	{
		// first fill the base classes
		foreach($classes as $item)
		{
			$class = $item->getAttribute("name");
			if (!$item->hasAttribute("base"))
			{
				$this->_classInheritance[$class] = array();
			}
		}
		
		// now fill recursively the childs
		foreach($this->_classInheritance as $class => $null)
		{
			$this->loadChildsForInheritance($classes, $class, $this->_classInheritance);
		}
	}
	
	private function loadChildsForInheritance(DOMNodeList $classes, $baseClass, array &$baseClassChilds)
	{
		$baseClassChilds[$baseClass] = $this->getChildsForParentClass($classes, $baseClass);
		
		foreach($baseClassChilds[$baseClass] as $childClass => $null)
		{
			$this->loadChildsForInheritance($classes, $childClass, $baseClassChilds[$baseClass]);
		}
	}
	
	private function getChildsForParentClass(DOMNodeList $classes, $parentClass)
	{
		$childs = array();
		foreach($classes as $item2)
		{
			$currentParentClass = $item2->getAttribute("base");
			$class = $item2->getAttribute("name");
			if ($currentParentClass === $parentClass) 
			{
				$childs[$class] = array();
			}
		}
		return $childs;
	}
	
	private function isClassInherit($class, $baseClass)
	{
		$classTree = $this->getClassChildsTree($this->_classInheritance, $baseClass);
		if (is_null($classTree))
			return false;
		else 
		{
			if (is_null($this->getClassChildsTree($classTree, $class)))
				return false;
			else
				return true;
		}
	}
	
	/**
	 * Finds the class in the multidimensional array and returns a multidimensional array with its child classes
	 * Null if not found
	 * 
	 * @param array $classes
	 * @param string $class
	 */
	private function getClassChildsTree(array $classes, $class)
	{
		foreach($classes as $tempClass => $null)
		{
			if ($class === $tempClass)
			{
				return $classes[$class];
			}
			else 
			{
				$subArray = $this->getClassChildsTree($classes[$tempClass], $class);
				if (!is_null($subArray))
					return $subArray;
			}
		}
		return null;
	}
	
	private function enumExists($enum)
	{
		return array_key_exists($enum, $this->_enums);
	}
	
	private function removeFilesFromSource()
	{
		$files = array_keys($this->_files);
		foreach($files as $file)
		{
			if ($file == "KalturaClient.suo")
				unset($this->_files["KalturaClient.suo"]);
			$dirname = pathinfo($file, PATHINFO_DIRNAME);
			if ($this->endsWith($dirname, "Debug"))
				unset($this->_files[$file]);
			if ($this->endsWith($dirname, "Release"))
				unset($this->_files[$file]);
		}
	}
	
	/**
	 * Fix .net reserved words
	 *
	 * @param string $param
	 * @return string
	 */
	private function fixParamName($param)
	{
		switch ($param)
		{
			case "event":
				return "kevent";
			case "params":
				return "params_";
			case "override":
				return "override_";
			default:
				return $param;
		}
	}
}
