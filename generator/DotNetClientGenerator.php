<?php
class DotNetClientGenerator extends ClientGeneratorFromXml
{
	private $_doc = null;
	private $_csprojIncludes = array();
	private $_classInheritance = array();
	private $_enums = array();
	
	function DotNetClientGenerator($xmlPath)
	{
		parent::ClientGeneratorFromXml($xmlPath, realpath("sources/dotnet"));
		$this->_doc = new DOMDocument();
		$this->_doc->load($this->_xmlFile);
	}
	
	function generate() 
	{
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
		
	    $this->writeMainClient($serviceNodes);
	     
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
		$this->startNewTextBlock();
		$this->appendLine("using System;");
		$this->appendLine("using System.Xml;");
		$this->appendLine("using System.Collections.Generic;");
		$this->appendLine();
		$this->appendLine("namespace Kaltura");
        $this->appendLine("{");
		$type = $classNode->getAttribute("name");
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
			    $dotNetPropType = $propertyNode->getAttribute("enumType");
		    else if ($propType == "array")
		        $dotNetPropType = "IList<".$propertyNode->getAttribute("arrayType").">";
	        else if ($propType == "bool")
				$dotNetPropType  = "bool?";
			else
	            $dotNetPropType = $propType;
	            
            $property["type"] = $dotNetPropType;
            if ($isFilter && $dotNetPropName == "OrderBy")
            	$property["isNew"] = true;
				
			switch($propType)
			{
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
					$property["default"] = "false";
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
			$propertyLine = "private";
			
			$propertyLine .= " {$property['type']} _{$property['name']}";
			
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
		
		// CTor For XmlElement
	    if ($classNode->hasAttribute("base"))
		{
			$this->appendLine("		public $type(XmlElement node) : base(node)");
		}
		else
		{
			$this->appendLine("		public $type(XmlElement node)");
		}
		
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
    				case "int":
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
    					$this->appendLine("				        this.$dotNetPropName = new List<$arrayType>();");
    				    $this->appendLine("						foreach(XmlElement arrayNode in propertyNode.ChildNodes)");
    				    $this->appendLine("						{");
    				    $this->appendLine("					        this.$dotNetPropName.Add(($arrayType)KalturaObjectFactory.Create(arrayNode));");
    				    $this->appendLine("						}");
    					break;
    				default: // sub object
    				    $this->appendLine("				        this.$dotNetPropName = ($propType)KalturaObjectFactory.Create(propertyNode);");
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
		foreach($classNode->childNodes as $propertyNode)
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
				
			$propType = $propertyNode->getAttribute("type");
			$propName = $propertyNode->getAttribute("name");
			$isEnum = $propertyNode->hasAttribute("enumType");
			$dotNetPropName = $this->upperCaseFirstLetter($propName);
			
			switch($propType)
			{
				case "int":
				    if ($isEnum)
				        $this->appendLine("			kparams.AddEnumIfNotNull(\"$propName\", this.$dotNetPropName);");
				    else
					    $this->appendLine("			kparams.AddIntIfNotNull(\"$propName\", this.$dotNetPropName);");
					break;
				case "string":
					if ($isEnum)
						$this->appendLine("			kparams.AddStringEnumIfNotNull(\"$propName\", this.$dotNetPropName);");
					else
						$this->appendLine("			kparams.AddStringIfNotNull(\"$propName\", this.$dotNetPropName);");
					break;
				case "bool":
					$this->appendLine("			kparams.AddBoolIfNotNull(\"$propName\", this.$dotNetPropName);");
					break;
				case "float":
					$this->appendLine("			kparams.AddFloatIfNotNull(\"$propName\", this.$dotNetPropName);");
					break;
				case "enum":
					$this->appendLine("			kparams.AddEnumIfNotNull(\"$propName\", this.$dotNetPropName);");
					break;
				case "array":
					$arrayType = $propertyNode->getAttribute("arrayType");
					$this->appendLine("			if (this.$dotNetPropName != null)");
		            $this->appendLine("			{");
	                $this->appendLine("				int i = 0;");
	                $this->appendLine("				foreach ($arrayType item in this.$dotNetPropName)");
                	$this->appendLine("				{");
                    $this->appendLine("					kparams.Add(\"".$propName.":\" + i + \":objectType\", item.GetType().Name);");
                    $this->appendLine("					kparams.Add(\"".$propName.":\" + i, item.ToParams());");
                    $this->appendLine("					i++;");
	                $this->appendLine("				}");
		            $this->appendLine("			}");
		            break;
			}
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
        $this->appendLine("		public static object Create(XmlElement xmlElement)");
        $this->appendLine("		{");
        $this->appendLine("			switch (xmlElement[\"objectType\"].InnerText)");
        $this->appendLine("			{");
        foreach($classNodes as $classNode)
        {
            $this->appendLine("				case \"".$classNode->getAttribute("name")."\":");
            $this->appendLine("					return new ".$classNode->getAttribute("name")."(xmlElement);");    
        }
        $this->appendLine("			}");
        $this->appendLine("			throw new SerializationException(\"Invalid object type\");");
        $this->appendLine("		}");
        $this->appendLine("	}");
        $this->appendLine("}");
        
        $file = "KalturaObjectFactory.cs";
		$this->addFile("KalturaClient/".$file, $this->getTextBlock());
        $this->_csprojIncludes[] = $file;
	}
	
	function writeCsproj()
	{
	    $csprojDoc = new DOMDocument();
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
		$this->addFile("KalturaClient/KalturaClient.csproj", $csprojDoc->saveXML());
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
		
		$dotNetServiceName = $this->upperCaseFirstLetter($serviceName)."Service";
		$dotNetServiceType = "Kaltura" . $dotNetServiceName;
		
		$this->appendLine();
		$this->appendLine("    public class $dotNetServiceType : KalturaServiceBase");
		$this->appendLine("	{");
		$this->appendLine("    public $dotNetServiceType(KalturaClient client)");
		$this->appendLine("			: base(client)");
		$this->appendLine("		{");
		$this->appendLine("		}");	   
		 
		
		$actionNodes = $serviceNode->childNodes;
		foreach($actionNodes as $actionNode)
		{
		    if ($actionNode->nodeType != XML_ELEMENT_NODE)
				continue;
				
		    $this->writeAction($serviceName, $actionNode);
		}
		$this->appendLine("	}");
		$this->appendLine("}");
		
		$file = "Services/".$dotNetServiceName.".cs";
		$this->addFile("KalturaClient/".$file, $this->getTextBlock());
		$this->_csprojIncludes[] = $file; 
	}
	
	function writeAction($serviceName, DOMElement $actionNode)
	{
	    $action = $actionNode->getAttribute("name");
	    $resultNode = $actionNode->getElementsByTagName("result")->item(0);
	    $resultType = $resultNode->getAttribute("type");
		
		switch($resultType)
		{
		    case null:
		        $dotNetOutputType = "void";
		        break;
	        case "array":
	            $arrayType = $resultNode->getAttribute("arrayType"); 
	            $dotNetOutputType = "IList<".$arrayType.">";
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
			$paramNumber = 0;
			foreach($defaultParams as $paramNode)
			{
				$paramNumber++;
				$optional = $paramNode->getAttribute("optional");
				if ($optional == "1" && $paramNumber == count($defaultParams))
				{
					$type = $paramNode->getAttribute("type");
					if ($type == "string")
						$paramsStr .=  "\"".$paramNode->getAttribute("default")."\"";
					else if ($type == "int" && $paramNode->hasAttribute("enumType"))
					{
						$value = $paramNode->getAttribute("default");
						if ($value == "")
							$value = "Int32.MinValue";
						$paramsStr .=  "(".$paramNode->getAttribute("enumType").")(".$value.")";
					}
					elseif ($type == "int" && $paramNode->getAttribute("default") == "") // because Partner.GetUsage has an int field with empty default value
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
			$this->appendLine("			return this.".$this->upperCaseFirstLetter($action)."($paramsStr);");
			$this->appendLine("		}");
		}
		
		$signature = $this->getSignature(array_merge($mandatoryParams, $optionalParams));
		
		$this->appendLine();	
		$this->appendLine("		$signaturePrefix$signature");
		$this->appendLine("		{");
		
		$this->appendLine("			KalturaParams kparams = new KalturaParams();");
		$haveFiles = false;
		foreach($paramNodes as $paramNode)
		{
			$paramType = $paramNode->getAttribute("type");
		    $paramName = $paramNode->getAttribute("name");
		    $isEnum = $paramNode->hasAttribute("enumType");
		    
		    if ($haveFiles === false && $paramType === "file")
		    {
		        $haveFiles = true;
		        $this->appendLine("			KalturaFiles kfiles = new KalturaFiles();");
		    }     

	        switch ($paramType)
	        {
                case "string":
                    if ($isEnum)
                		$this->appendLine("			kparams.AddStringEnumIfNotNull(\"$paramName\", ".$this->fixParamName($paramName).");");
                	else
                		$this->appendLine("			kparams.AddStringIfNotNull(\"$paramName\", ".$this->fixParamName($paramName).");");
                    break;
                case "float":
                        $this->appendLine("			kparams.AddFloatIfNotNull(\"$paramName\", ".$this->fixParamName($paramName).");");
                    break;
               	case "int":
	                if ($isEnum)
				        $this->appendLine("			kparams.AddEnumIfNotNull(\"$paramName\", ".$this->fixParamName($paramName).");");
			        else
                        $this->appendLine("			kparams.AddIntIfNotNull(\"$paramName\", ".$this->fixParamName($paramName).");");
                    break;
                case "bool":
                    $this->appendLine("			kparams.AddBoolIfNotNull(\"$paramName\", ".$this->fixParamName($paramName).");");
                    break;
                case "array":
                    $this->appendLine("			foreach(".$paramNode->getAttribute("arrayType")." obj in ".$this->fixParamName($paramName).")");
                    $this->appendLine("			{");
                    $this->appendLine("				kparams.Add(\"$paramName\", obj.ToParams());");
                    $this->appendLine("			}");
                    break;
                case "file":
                    $this->appendLine("			kfiles.Add(\"$paramName\", ".$this->fixParamName($paramName).");");
                    break;
                default: // for objects
                	$this->appendLine("			if (".$this->fixParamName($paramName)." != null)");
                    $this->appendLine("				kparams.Add(\"$paramName\", ".$this->fixParamName($paramName).".ToParams());");
                    break;
		    }
		}
		
		if ($haveFiles)
			$this->appendLine("			_Client.QueueServiceCall(\"$serviceName\", \"$action\", kparams, kfiles);");
		else
			$this->appendLine("			_Client.QueueServiceCall(\"$serviceName\", \"$action\", kparams);");
		
		$this->appendLine("			if (this._Client.IsMultiRequest)");
		if (!$resultType) 
			$this->appendLine("				return;");
		else if ($resultType == "int" || $resultNode == "float")
			$this->appendLine("				return 0;");
		else if ($resultType == "bool")
			$this->appendLine("				return false;");
		else
			$this->appendLine("				return null;");

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
    				$this->appendLine("				list.Add(($arrayType)KalturaObjectFactory.Create(node));");
    				$this->appendLine("			}");
    				$this->appendLine("			return list;");
				    break;
			    case "int":
            	    $this->appendLine("			return int.Parse(result.InnerText);");
            	    break;
			    case "float":
            	    $this->appendLine("			return Single.Parse(result.InnerText);");
            	    break;
			    case "bool":
            	    $this->appendLine("			return bool.Parse(result.InnerText);");
            	    break;
			    case "string":
            	    $this->appendLine("			return result.InnerText;");
            	    break;
			    default:
            	    $this->appendLine("			return ($resultType)KalturaObjectFactory.Create(result);");
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
		        case "file":
		            $dotNetType = "FileStream";
		            break;
		        case "int":
		            if ($isEnum)
		                $dotNetType = $paramNode->getAttribute("enumType");
	                else 
                        $dotNetType = $paramType;	        
                    break;            
		        default:
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
	
	function writeMainClient(DOMNodeList  $serviceNodes)
	{
	    $this->startNewTextBlock();
	    
	    $this->appendLine("using System;");
	    $this->appendLine();
	    
		$this->appendLine("namespace Kaltura");
		$this->appendLine("{");
	    $this->appendLine("    public class KalturaClient : KalturaClientBase");
        $this->appendLine("    {");
        $this->appendLine("        public KalturaClient(KalturaConfiguration config)");
        $this->appendLine("            : base(config)");
        $this->appendLine("        {");
        $this->appendLine("        }");
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
		$this->appendLine("	}");
		$this->appendLine("}");
		
		$this->addFile("KalturaClient/KalturaClient.cs", $this->getTextBlock());

		// not needed because it is included in the sources
	    //$this->_csprojIncludes[] = "KalturaClient.cs";
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
		if ($param == "event")
			return "kevent";
		else 
			return $param;
	}
}
?>