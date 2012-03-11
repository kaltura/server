<?php
/**
 * This is a port of Kaltura's DotNetClientGenerator to Java.
 * 8/2009
 * jpotts, Optaros
 * 1/2010
 * oferc
 */

class JavaClientGenerator extends ClientGeneratorFromXml 
{
	/**
	 * @var DOMDocument
	 */
	private $_doc = null;
	private $_csprojIncludes = array ();
	
	public function JavaClientGenerator($xmlPath) 
	{
		parent::ClientGeneratorFromXml ( $xmlPath, realpath ( "sources/java" ) );
		$this->_doc = new DOMDocument ();
		$this->_doc->load ( $this->_xmlFile );
	}
	
	function getSingleLineCommentMarker()
	{
		return '//';
	}
	
	public function generate() 
	{
		
		parent::generate();
		
		$xpath = new DOMXPath ( $this->_doc );
		$enumNodes = $xpath->query ( "/xml/enums/enum" );
		foreach ( $enumNodes as $enumNode ) 
		{
			$this->writeEnum ( $enumNode );
		}
		
		$classNodes = $xpath->query ( "/xml/classes/class" );
		foreach ( $classNodes as $classNode ) 
		{
			$this->writeClass ( $classNode );
		}
		
		$serviceNodes = $xpath->query ( "/xml/services/service" );
		
		foreach ( $serviceNodes as $serviceNode ) 
		{
			$this->writeService ( $serviceNode );
		}
		
		$this->writeMainClient ( $serviceNodes );
	
	}
	
	//Private functions
	/////////////////////////////////////////////////////////////
	private function addDescription($propertyNode, $prefix) {
		
		if($propertyNode->hasAttribute ( "description" )) {
			$desc = $propertyNode->getAttribute ( "description" );
			$formatDesc = wordwrap (  str_replace(array("\t","\n", "\r"), " ", $desc) , 80, "\n" . $prefix . "  ");
			if($desc)
				return ( $prefix . "/**  $formatDesc  */" );
		}
		return "";
	}
	
	function writeEnum(DOMElement $enumNode) 
	{
		$enumName = $enumNode->getAttribute ( "name" );
		$enumType = $enumNode->getAttribute ( "enumType" );
		$baseIfc = ($enumType == "string") ? "KalturaEnumAsString" : "KalturaEnumAsInt";
		$encumCount = 0;
		
		$str = "";
		$str = "package com.kaltura.client.enums;\n";
		$str .= "\n";
		$str .= $this->getBanner ();
		
		$desc = $this->addDescription($enumNode, "");
		if($desc)
			$str .= $desc . "\n";
		$str .= "public enum $enumName implements $baseIfc {\n";
		
		// Print enum values
		$enumCount = $this->generateEnumValues($enumNode, $str);
		
		// Generate hash code function
		$this->generateHashValueFunction($str, $enumType, $enumName);
		
		// Generate get function if needed
		if($enumCount > 0) 
			$this->generateEnumGetFunction($str, $enumNode, $enumType,  $enumName);
		
		$str .= "}\n";
		$file = "src/com/kaltura/client/enums/$enumName.java";
		$this->addFile ( $file, $str );
	}
	
	function generateEnumValues($enumNode, &$str)
	{
		$enumType = $enumNode->getAttribute ( "enumType" );
		$enumCount = 0;
		$enum2Val = array();
		
		foreach ( $enumNode->childNodes as $constNode )
		{
			if ($constNode->nodeType != XML_ELEMENT_NODE)
				continue;
				
			$propertyName = $constNode->getAttribute ( "name" );
			$propertyValue = $constNode->getAttribute ( "value" );
			if ($enumType == "string")
			{
				$propertyValue = "\"" . $propertyValue . "\"";
			}
			$enum2Val[] = "$propertyName ($propertyValue)";
		}
		
		if(count($enum2Val) == 0)
			$str .= "    /** Place holder for future values */";
		else  {
			$enums = implode(",\n    ", $enum2Val);
			$str .= "    $enums";
		}
		
		$str .= ";\n\n";
		return count($enum2Val);
	}
	
	function generateHashValueFunction(&$str, $enumType, $enumName)
	{
		if ($enumType == "string")
		{
			$str .= "    public String hashCode;\n\n";
			$str .= "    $enumName(String hashCode) {\n";
			$str .= "        this.hashCode = hashCode;\n";
			$str .= "    }\n\n";
			$str .= "    public String getHashCode() {\n";
			$str .= "        return this.hashCode;\n";
			$str .= "    }\n\n";
		} else
		{
			$str .= "    public int hashCode;\n\n";
			$str .= "    $enumName(int hashCode) {\n";
			$str .= "        this.hashCode = hashCode;\n";
			$str .= "    }\n\n";
			$str .= "    public int getHashCode() {\n";
			$str .= "        return this.hashCode;\n";
			$str .= "    }\n\n";
		}
	}
		
	function generateEnumGetFunction(&$str, $enumNode, $enumType,  $enumName) 
	{
		if ($enumType == "string")
		{
			$str .= "    public static $enumName get(String hashCode) {\n";
		} else
		{
			$str .= "    public static $enumName get(int hashCode) {\n";
			$str .= "        switch(hashCode) {\n";
		}
			
		$defaultPropertyName = "";
		foreach ( $enumNode->childNodes as $constNode )
		{
			if ($constNode->nodeType != XML_ELEMENT_NODE)
				continue;
	
			$propertyName = $constNode->getAttribute ( "name" );
	
			if ($defaultPropertyName == "")
				$defaultPropertyName = $propertyName;
	
			$propertyValue = $constNode->getAttribute ( "value" );
			if ($enumType == "string")
			{
				$propertyValue = "\"" . $propertyValue . "\"";
				$str .= "        if (hashCode.equals({$propertyValue}))\n";
				$str .= "        {\n";
				$str .= "           return {$propertyName};\n";
				$str .= "        }\n";
				$str .= "        else \n";
			}
			else
			{
				$str .= "            case $propertyValue: return $propertyName;\n";
			}
		}
			
		if ($enumType == "string")
		{
			$str .= "        {\n";
			$str .= "           return {$defaultPropertyName};\n";
			$str .= "        }\n";
		}
		else
		{
			$str .= "            default: return $defaultPropertyName;\n";
			$str .= "        }\n";
		}
		$str .= "    }\n";
		
	}
	
	function writeClass(DOMElement $classNode) 
	{
		$type = $classNode->getAttribute ( "name" );
		
		// File name
		$file = "src/com/kaltura/client/types/$type.java";
		
		// Basic imports
		$imports = "";
		$imports .= "package com.kaltura.client.types;\n\n";
		$imports .= "import org.w3c.dom.Element;\n";
		$imports .= "import com.kaltura.client.KalturaParams;\n";
		$imports .= "import com.kaltura.client.KalturaApiException;\n";

		// Add Banner
		$this->startNewTextBlock ();
		$this->appendLine ( "" );
		$this->appendLine ( $this->getBanner () );
		
		$desc = $this->addDescription($classNode, "");
		if($desc)
			$this->appendLine ( $desc );
		
		// class definition
		$abstract = '';
		if ($classNode->hasAttribute("abstract"))
			$abstract = ' abstract';
		
		$needsSuperConstructor = false;
		if ($classNode->hasAttribute ( "base" )) 
		{
			$this->appendLine ( "public{$abstract} class $type extends " . $classNode->getAttribute ( "base" ) . " {" );
			$needsSuperConstructor = true;
		} 
		else 
		{
			$imports .= "import com.kaltura.client.KalturaObjectBase;\n";
			$this->appendLine ( "public{$abstract} class $type extends KalturaObjectBase {" );
		}
		
		// Generate parameters declaration
		$this->generateParametersDeclaration ( $imports, $classNode);
		$this->appendLine ( "" );
		
		// Generate empty constructor
		$this->appendLine ( "    public $type() {" );
		$this->appendLine ( "    }" );
		$this->appendLine ( "" );
		
		// Generate Full constructor
		$this->generateFullConstructor ( $imports , $classNode, $needsSuperConstructor);
		$this->appendLine ( "" );

		// Generate to params method
		$this->generateToParamsMethod ($classNode);
		$this->appendLine ( "" );
		
		// close class
		$this->appendLine ( "}" );
		$this->appendLine ();
		
		$this->addFile ( $file, $imports . "\n" . $this->getTextBlock () );
	}
	
	public function generateParametersDeclaration(&$imports, $classNode) {

		$needsArrayList = false;
		$arrImportsEnums = array();
		//
		foreach ( $classNode->childNodes as $propertyNode ) 
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			$propType = $propertyNode->getAttribute ( "type" );
			$propName = $propertyNode->getAttribute ( "name" );
			$isEnum = $propertyNode->hasAttribute ( "enumType" );
			
			switch ($propType) 
			{
				case "string" :
					if ($isEnum) 
					{
						$javaType = $propertyNode->getAttribute ( "enumType" );
						$initial_value = ""; // we do not want to initialize enums
						$arrImportsEnums[] = $javaType; 
					} 
					else 
					{
						$javaType = "String";
						$initial_value = "";
					}
					break;
				case "float" :
					$javaType = "float";
					$initial_value = "Float.MIN_VALUE";
					break;
				case "int" :
					if ($isEnum) 
					{
						$javaType = $propertyNode->getAttribute ( "enumType" );
						$initial_value = ""; // we do not want to initialize enums
						$arrImportsEnums[] = $javaType; 
					} 
					else 
					{
						$javaType = "int";
						$initial_value = "Integer.MIN_VALUE";
					}
					break;
				case "bool" :
					$javaType = "boolean";
					$initial_value = "";
					break;
				case "array" :
					$arrayType = $propertyNode->getAttribute ( "arrayType" );
					$javaType = "ArrayList<$arrayType>";
					$initial_value = "";
					$needsArrayList = true;
					break;
				default :
					$javaType = $propType;
					$initial_value = "";
					
					break;
			}
			
			$propertyLine = "public $javaType $propName";
			
			if ($initial_value != "") 
			{
				$propertyLine .= " = " . $initial_value;
			}
			
			$desc = $this->addDescription($propertyNode,"\t");
			if($desc)
				$this->appendLine ( $desc );
			
			$this->appendLine ( "    $propertyLine;" );
		}
		
		// Fix imports - 
		// Add property imports, make sure that we add each one only once
		$arrImportsEnums = array_unique($arrImportsEnums);
		foreach($arrImportsEnums as $import) 
			$imports.= "import com.kaltura.client.enums.$import;\n";
		
		if ($needsArrayList)
			$imports .= "import java.util.ArrayList;\n";
	}

	public function generateToParamsMethod($classNode) {
		
		$type = $classNode->getAttribute ( "name" );
		$this->appendLine ( "    public KalturaParams toParams() {" );
		$this->appendLine ( "        KalturaParams kparams = super.toParams();" );
		$this->appendLine ( "        kparams.add(\"objectType\", \"$type\");" );
		
		foreach ( $classNode->childNodes as $propertyNode ) 
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			$propReadOnly = $propertyNode->getAttribute ( "readOnly" );
			if ($propReadOnly == "1")
				continue;
			
			$propType = $propertyNode->getAttribute ( "type" );
			$propName = $propertyNode->getAttribute ( "name" );
			$this->appendLine ( "        kparams.add(\"$propName\", this.$propName);" );
		}
		$this->appendLine ( "        return kparams;" );
		$this->appendLine ( "    }" );
	}

	
	
	public function generateFullConstructor(&$imports, $classNode, $needsSuperConstructor) {
		
		$type = $classNode->getAttribute ( "name" );
		$this->appendLine ( "    public $type(Element node) throws KalturaApiException {" );
		if ($needsSuperConstructor)
			$this->appendLine ( "        super(node);" );
			
		if ($classNode->childNodes->length) 
		{
			$imports .= "import com.kaltura.client.utils.ParseUtils;\n";
			$imports .= "import org.w3c.dom.Node;\n";
			$imports .= "import org.w3c.dom.NodeList;\n";
			
			$this->appendLine ( "        NodeList childNodes = node.getChildNodes();" );
			$this->appendLine ( "        for (int i = 0; i < childNodes.getLength(); i++) {" );
			$this->appendLine ( "            Node aNode = childNodes.item(i);" );
			$this->appendLine ( "            String nodeName = aNode.getNodeName();" );
			$propBlock = "            ";
			
			$isFirst = true;
			$txtIsUsed = false;

			foreach ( $classNode->childNodes as $propertyNode ) 
			{
				if ($propertyNode->nodeType != XML_ELEMENT_NODE)
					continue;
				
				$propName = $propertyNode->getAttribute ( "name" );
			
				if($isFirst) {
					$isFirst = false;
				} else { 
					$propBlock .= "else ";
				}
				$propBlock .= "if (nodeName.equals(\"$propName\")) {\n";
				$propBlock .= "                ";
				$this->handleSinglePropByType ( $propertyNode , $propBlock, $txtIsUsed);
				$propBlock .= "                continue;\n";
				$propBlock .= "            } ";
			}
			
			if($txtIsUsed) 
				$this->appendLine ( "            String txt = aNode.getTextContent();" );
			
			$this->appendLine ( $propBlock );
			$this->appendLine ( "        }" );
		}
		$this->appendLine ( "    }" );
	}

	/**
	 * @param propType
	 */
	public function handleSinglePropByType($propertyNode, &$propBlock, &$txtIsUsed) {
		
		$propType = $propertyNode->getAttribute ( "type" );
		$propName = $propertyNode->getAttribute ( "name" );
		$isEnum = $propertyNode->hasAttribute ( "enumType" );
		$propBlock .= "this.$propName = ";
		
		switch ($propType) 
		{
			case "int" :
				$txtIsUsed = true;
				if ($isEnum) 
				{
					$enumType = $propertyNode->getAttribute ( "enumType" );
					$propBlock .= "$enumType.get(ParseUtils.parseInt(txt));\n";
				} 
				else
				{
					$propBlock .= "ParseUtils.parseInt(txt);\n";
				}
				break;
			case "string" :
				$txtIsUsed = true;
				if ($isEnum) 
				{
					$enumType = $propertyNode->getAttribute ( "enumType" );
					$propBlock .= "$enumType.get(ParseUtils.parseString(txt));\n";
				} 
				else
				{
					$propBlock .= "ParseUtils.parseString(txt);\n";
				}
				break;
			case "bool" :
				$txtIsUsed = true;
				$propBlock .= "ParseUtils.parseBool(txt);\n";
				break;
			case "float" :
				$txtIsUsed = true;
				$propBlock .= "ParseUtils.parseFloat(txt);\n";
				break;
			case "array" :
				$arrayType = $propertyNode->getAttribute ( "arrayType" );
				$propBlock .= "ParseUtils.parseArray($arrayType.class, aNode);\n";
				break;
			default : // sub object
				$propBlock .= "ParseUtils.parseObject($propType.class, aNode);\n";
				break;
		}
	}

	function writeService(DOMElement $serviceNode) 
	{
		$imports = "";
		$imports .= "package com.kaltura.client.services;\n\n";
		$imports .= "import com.kaltura.client.KalturaClient;\n";
		$imports .= "import com.kaltura.client.KalturaServiceBase;\n";
		$serviceId = $serviceNode->getAttribute ( "id" );
		$serviceName = $serviceNode->getAttribute ( "name" );
		
		$javaServiceName = $this->upperCaseFirstLetter ( $serviceName ) . "Service";
		$javaServiceType = "Kaltura" . $javaServiceName;
		
		$this->startNewTextBlock ();
		$this->appendLine ();
		$this->appendLine ( $this->getBanner () );
		$desc = $this->addDescription($serviceNode, "");
		if($desc)
			$this->appendLine ( $desc );
		
		$this->appendLine ( "public class $javaServiceType extends KalturaServiceBase {" );
		$this->appendLine ( "    public $javaServiceType(KalturaClient client) {" );
		$this->appendLine ( "        this.kalturaClient = client;" );
		$this->appendLine ( "    }" );
		
		$actionNodes = $serviceNode->childNodes;
		$serviceImports = array();
		
		foreach ( $actionNodes as $actionNode ) 
		{
			if ($actionNode->nodeType != XML_ELEMENT_NODE) 
				continue;
			
			$this->writeAction ( $serviceId, $actionNode, $serviceImports);
		}
		$this->appendLine ( "}" );
		
		// Update imports
		$serviceImports = array_unique($serviceImports);
		$importsString = implode("\n", $serviceImports);
		$imports .= $importsString . "\n";
		
		
		
		$file = "src/com/kaltura/client/services/" . $javaServiceType . ".java";
		$this->addFile ( $file, $imports . $this->getTextBlock () );
	}
	
	function writeAction($serviceId, DOMElement $actionNode, &$serviceImports) 
	{
		$action = $actionNode->getAttribute ( "name" );
		if($action == "goto")
			$action .= "_";
		
		$resultNode = $actionNode->getElementsByTagName ( "result" )->item ( 0 );
		$resultType = $resultNode->getAttribute ( "type" );
	    
	  	$javaOutputType = $this->getResultType($resultType, $resultNode, $serviceImports);
		
		$signaturePrefix = "public $javaOutputType " . $action . "(";
		
		$paramNodes = $actionNode->getElementsByTagName ( "param" );
		
		// check for needed overloads
		$mandatoryParams = array ();
		$optionalParams = array ();
		foreach ( $paramNodes as $paramNode ) 
		{
			$optional = $paramNode->getAttribute ( "optional" );
			if ($optional == "1")
				$optionalParams [] = $paramNode;
			else
				$mandatoryParams [] = $paramNode;
		}
		
		for($overloadNumber = 0; $overloadNumber < count ( $optionalParams ); $overloadNumber ++) 
		{
			$currentOptionalParams = array_slice ( $optionalParams, 0, $overloadNumber );
			$defaultParams = array_slice ( array_merge ( $mandatoryParams, $optionalParams ), 0, count ( $mandatoryParams ) + $overloadNumber + 1 );
			$signature = $this->getSignature ( array_merge ( $mandatoryParams, $currentOptionalParams ) , $serviceImports);
			
			// write the overload
			$this->appendLine ();
			$desc = $this->addDescription($actionNode, "\t");
			if($desc)
				$this->appendLine ( $desc );
			$this->appendLine ( "    $signaturePrefix$signature throws KalturaApiException {" );
			$paramsStr = "";
			foreach ( $defaultParams as $paramNode ) 
			{
				$optional = $paramNode->getAttribute ( "optional" );
				$paramName = $paramNode->getAttribute ( "name" );
				
				//				$paramsStr .= count($currentOptionalParams) > 0 ? $currentOptionalParams[0] : "";
				//				$paramsStr .= (in_array($paramNode  ,$currentOptionalParams, true) ? "1-" : "0-") . count($currentOptionalParams);
				if ($optional == "1" && ! in_array ( $paramNode, $currentOptionalParams, true )) 
				{
					$type = $paramNode->getAttribute ( "type" );
					if ($type == "string") 
					{
						if ($paramNode->getAttribute ( "default" ) == 'null')
							$paramsStr .= 'null';
						else
							$paramsStr .= "\"" . $paramNode->getAttribute ( "default" ) . "\"";
					} 
					else if ($type == "int") 
					{
						$value = trim ( $paramNode->getAttribute ( "default" ) );
						if ($value == 'null')
							$value = "Integer.MIN_VALUE";
						
						if ($paramNode->hasAttribute ( "enumType" )) 
						{
							$paramsStr .= $paramNode->getAttribute ( "enumType" ) . ".get(" . $value . ")";
						} 
						else 
						{
							$paramsStr .= $value;
						}
					} 
					else 
					{
						$paramsStr .= $paramNode->getAttribute ( "default" );
					}
				} 
				else 
				{
					$paramName = $paramNode->getAttribute ( "name" );
					$paramsStr .= $paramName;
				}
				
				$paramsStr .= ", ";
			}
			if ($this->endsWith ( $paramsStr, ", " ))
				$paramsStr = substr ( $paramsStr, 0, strlen ( $paramsStr ) - 2 );
			if ($resultType)
				$this->appendLine ( "        return this." . $action . "($paramsStr);" );
			else
				$this->appendLine ( "        this." . $action . "($paramsStr);" );
			$this->appendLine ( "    }" );
		}
		
		$signature = $this->getSignature ( array_merge ( $mandatoryParams, $optionalParams ) , $serviceImports);
		
		$this->appendLine ();
		
		$desc = $this->addDescription($actionNode, "\t");
		if($desc)
			$this->appendLine ( $desc );
		$this->appendLine ( "    $signaturePrefix$signature throws KalturaApiException {" );
		
		$this->generateActionBodyServiceCall($serviceId, $action, $paramNodes, $serviceImports);
				
		if($resultType == 'file')
		{
			$this->appendLine ( "        return this.kalturaClient.serve();");
		} else {
			
			$serviceImports[] = "import org.w3c.dom.Element;";
			
			// Handle multi request
			$this->handleMultiRequest($resultType, $resultNode);
			
			// Queue request
			if ($resultType)
				$this->appendLine ( "        Element resultXmlElement = this.kalturaClient.doQueue();" );
			else 
				$this->appendLine ( "        this.kalturaClient.doQueue();" );
			
			// Handle result type
			if ($resultType) 
				$this->handleResultType($resultType, $resultNode, $serviceImports);
		}
		
		$this->appendLine ( "    }" );
		
		$serviceImports[] ="import com.kaltura.client.KalturaParams;";
		$serviceImports[] ="import com.kaltura.client.KalturaApiException;";
	}
	
	public function getResultType($resultType, $resultNode, &$serviceImports) 
	{
		switch ($resultType)
		{
			case null :
				return "void";
			case "array" :
				$serviceImports[] ="import java.util.List;";
				$arrayType = $resultNode->getAttribute ( "arrayType" );
				return ("List<" . $arrayType . ">");
			case "bool" :
				return "boolean";
			case "file":
			case "string" :
				return "String";
			default :
				$serviceImports[] ="import com.kaltura.client.types.*;";
				return $resultType;
		}
	}
	
	public function generateActionBodyServiceCall($serviceId, $action, $paramNodes, &$serviceImports) 
	{
		$this->appendLine ( "        KalturaParams kparams = new KalturaParams();" );
		$haveFiles = false;
		foreach ( $paramNodes as $paramNode )
		{
			$paramType = $paramNode->getAttribute ( "type" );
			$paramName = $paramNode->getAttribute ( "name" );
			$isEnum = $paramNode->hasAttribute ( "enumType" );
				
			if ($haveFiles === false && $paramType === "file")
			{
				$serviceImports[] ="import com.kaltura.client.KalturaFiles;";
				$serviceImports[] ="import java.io.File;";
				$haveFiles = true;
				$this->appendLine ( "        KalturaFiles kfiles = new KalturaFiles();" );
			}
			
			if($paramType == "file")
				$this->appendLine ( "        kfiles.put(\"$paramName\", " . $paramName . ");" );
			else 
				$this->appendLine ( "        kparams.add(\"$paramName\", " . $paramName . ");" );
		}
		
		// Add files to call
		if ($haveFiles)
			$this->appendLine ( "        this.kalturaClient.queueServiceCall(\"$serviceId\", \"$action\", kparams, kfiles);" );
		else
			$this->appendLine ( "        this.kalturaClient.queueServiceCall(\"$serviceId\", \"$action\", kparams);" );
	}
	
	public function handleMultiRequest($resultType, $resultNode) 
	{
		$this->appendLine ( "        if (this.kalturaClient.isMultiRequest())" );
		if (! $resultType)
			$this->appendLine ( "            return;" );
		else if ($resultType == "int" || $resultNode == "float")
			$this->appendLine ( "            return 0;" );
		else if ($resultType == "bool")
			$this->appendLine ( "            return false;" );
		else
			$this->appendLine ( "            return null;" );
	}
	
	public function handleResultType($resultType, $resultNode, &$serviceImports) 
	{
		$serviceImports[] ="import com.kaltura.client.utils.ParseUtils;";
		$importXml = false;
		$returnCall = "        ";
		switch ($resultType)
		{
			case "array" :
				$arrayType = $resultNode->getAttribute ( "arrayType" );
				$returnCall .= "return ParseUtils.parseArray($arrayType.class, resultXmlElement);";
				break;
			case "int" :
			case "float" :
			case "bool" :
			case "string" :
				$importXml = true;
				break;
			default :
				$returnCall .= "return ParseUtils.parseObject($resultType.class, resultXmlElement);";
				break;
		}
		
		if($importXml) {
			$serviceImports[] ="import com.kaltura.client.utils.XmlUtils;";
			$this->appendLine ( "        String resultText = XmlUtils.getTextValue(resultXmlElement, \"result\");" );
			$returnCall .= "return ParseUtils.parse" . ucwords($resultType) . "(resultText);";
		}
		$this->appendLine($returnCall);
	}
	
	function writeMainClient(DOMNodeList $serviceNodes) 
	{
		$apiVersion = $this->_doc->documentElement->getAttribute('apiVersion');
		
		$imports = "";
		$imports .= "package com.kaltura.client;\n";
		
		$this->startNewTextBlock ();
		$this->appendLine ( $this->getBanner () );
		$this->appendLine ( "public class KalturaClient extends KalturaClientBase {" );
		$this->appendLine ( "	" );
		$this->appendLine ( "	protected String apiVersion = \"$apiVersion\";" );
		$this->appendLine ( "	" );
		$this->appendLine ( "	public KalturaClient(KalturaConfiguration config) {" );
		$this->appendLine ( "		super(config);" );
		$this->appendLine ( "	}" );
		$this->appendLine ( "	" );
		$this->appendLine ( "	@Override" );
		$this->appendLine ( "	public String getApiVersion(){" );
		$this->appendLine ( "		return apiVersion;" );
		$this->appendLine ( "	}" );
		$this->appendLine ( "	" );
		
		foreach ( $serviceNodes as $serviceNode ) 
		{
			$serviceName = $serviceNode->getAttribute ( "name" );
			$javaServiceName = $serviceName . "Service";
			$javaServiceType = "Kaltura" . $this->upperCaseFirstLetter ( $javaServiceName );
			$imports .= "import com.kaltura.client.services.$javaServiceType;\n";
			
			$this->appendLine ( "	protected $javaServiceType $javaServiceName;" );
			$this->appendLine ( "	public $javaServiceType get" . $this->upperCaseFirstLetter ( $javaServiceName ) . "() {" );
			$this->appendLine ( "		if(this.$javaServiceName == null)" );
			$this->appendLine ( "			this.$javaServiceName = new $javaServiceType(this);" );
			$this->appendLine ( "	" );
			$this->appendLine ( "		return this.$javaServiceName;" );
			$this->appendLine ( "	}" );
			$this->appendLine ( "	" );
		}
		$this->appendLine ( "}" );
		
		$imports .= "\n";
		
		$this->addFile ( "src/com/kaltura/client/KalturaClient.java", $imports . $this->getTextBlock () );
	
	}
	
	function getSignature($paramNodes, &$serviceImports) 
	{
		$signature = "";
		foreach ( $paramNodes as $paramNode ) 
		{
			$paramType = $paramNode->getAttribute ( "type" );
			$paramName = $paramNode->getAttribute ( "name" );
			$isEnum = $paramNode->hasAttribute ( "enumType" );
			
			switch ($paramType) 
			{
				case "array" :
					$serviceImports[] ="import java.util.ArrayList;";
					$javaType = "ArrayList<" . $paramNode->getAttribute ( "arrayType" ) . ">";
					break;
				case "bool" :
					$javaType = "boolean";
					break;
				case "file" :
					$javaType = "File";
					break;
				case "int" :
					if ($isEnum) {
						$serviceImports[] ="import com.kaltura.client.enums.*;";
						$javaType = $paramNode->getAttribute ( "enumType" );
					}
					else
						$javaType = $paramType;
					break;
				case "string" :
					$javaType = "String";
					break;
				default :
					$javaType = $paramType;
					break;
			}
			
			$signature .= "$javaType " . $paramName . ", ";
		}
		if ($this->endsWith ( $signature, ", " ))
			$signature = substr ( $signature, 0, strlen ( $signature ) - 2 );
		$signature .= ")";
		
		return $signature;
	}
	
	private function getBanner() 
	{
		$currentFile = $_SERVER ["SCRIPT_NAME"];
		$parts = Explode ( '/', $currentFile );
		$currentFile = $parts [count ( $parts ) - 1];
		
		$banner = "";
		$banner .= "/**\n";
		$banner .= " * This class was generated using $currentFile\n";
		$banner .= " * against an XML schema provided by Kaltura.\n";
		$banner .= " * @date " . date ( DATE_RFC822 ) . "\n";
		$banner .= " * \n";
		$banner .= " * MANUAL CHANGES TO THIS CLASS WILL BE OVERWRITTEN.\n";
		$banner .= " */\n";
		
		return $banner;
	}

}
?>