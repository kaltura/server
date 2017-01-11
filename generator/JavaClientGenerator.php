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
	protected $_baseClientPath = "src/main/java/com/kaltura/client";
	protected $_usePrivateAttributes;
	
	function __construct($xmlPath, Zend_Config $config, $sourcePath = "sources/java")
	{
		parent::__construct($xmlPath, $sourcePath, $config);
		$this->_usePrivateAttributes = isset($config->usePrivateAttributes) ? $config->usePrivateAttributes : false;
		$this->_doc = new KDOMDocument ();
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
		
		$configurationNodes = $xpath->query("/xml/configurations/*");
	    $this->writeMainClient($serviceNodes, $configurationNodes);
	}
	
	//Private functions
	/////////////////////////////////////////////////////////////
	private function addDescription($propertyNode, $prefix) {
		
		if($propertyNode->hasAttribute ( "description" ))
		{
			$desc = $propertyNode->getAttribute ( "description" );
			$formatDesc = wordwrap(str_replace(array("\t", "\n", "\r"), " ", $desc) , 80, "\n" . $prefix . "  ");
			if($desc)
				return ( $prefix . "/**  $formatDesc  */" );
		}
		return "";
	}
	
	function writeEnum(DOMElement $enumNode) 
	{
		$enumName = $enumNode->getAttribute ( "name" );
		$enumType = $enumNode->getAttribute ( "enumType" );
		$baseInterface = ($enumType == "string") ? "KalturaEnumAsString" : "KalturaEnumAsInt";
		
		$str = "";
		$str = "package com.kaltura.client.enums;\n";
		$str .= "\n";
		$str .= $this->getBanner ();
		
		$desc = $this->addDescription($enumNode, "");
		if($desc)
			$str .= $desc . "\n";
		$str .= "public enum $enumName implements $baseInterface {\n";
		
		// Print enum values
		$enumCount = $this->generateEnumValues($enumNode, $str);
		
		// Generate hash code function
		$this->generateEnumHashCodeFunctions($str, $enumType, $enumName);
		
		// Generate get function if needed
		if($enumCount) 
		{
			$this->generateEnumGetFunction($str, $enumNode, $enumType,  $enumName);
		}
		else 
		{
			$this->generateEmptyEnumGetFunction($str, $enumNode, $enumType,  $enumName);
		}
		
		$str .= "}\n";
		$file = $this->_baseClientPath . "/enums/$enumName.java";
		$this->addFile ( $file, $str );
	}
	
	function generateEnumValues($enumNode, &$str)
	{
		$enumType = $enumNode->getAttribute ( "enumType" );
		$enumCount = 0;
		$enumValues = array();
		$processedValues = array();
		
		foreach ( $enumNode->childNodes as $constNode )
		{
			if ($constNode->nodeType != XML_ELEMENT_NODE)
				continue;
				
			$propertyName = $constNode->getAttribute ( "name" );
			$propertyValue = $constNode->getAttribute ( "value" );
			
			if (in_array($propertyValue, $processedValues))
				continue;			// Java does not allow duplicate values in enums
			$processedValues[] = $propertyValue;
			
			if ($enumType == "string")
			{
				$propertyValue = "\"" . $propertyValue . "\"";
			}
			$enumValues[] = "$propertyName ($propertyValue)";
		}
		
		if(count($enumValues) == 0)
			$str .= "    /** Place holder for future values */";
		else  {
			$enums = implode(",\n    ", $enumValues);
			$str .= "    $enums";
		}
		
		$str .= ";\n\n";
		return count($enumValues);
	}
	
	function generateEnumHashCodeFunctions(&$str, $enumType, $enumName)
	{
		$type = 'int';
		if ($enumType == "string"){
			$type = 'String';
		}
		
		$visibility = 'public';
		if($this->_usePrivateAttributes){
			$visibility = 'private';
		}
		
		$str .= "    $visibility $type hashCode;\n\n";
		$str .= "    $enumName($type hashCode) {\n";
		$str .= "        this.hashCode = hashCode;\n";
		$str .= "    }\n\n";
		$str .= "    public $type getHashCode() {\n";
		$str .= "        return this.hashCode;\n";
		$str .= "    }\n\n";
		$str .= "    public void setHashCode($type hashCode) {\n";
		$str .= "        this.hashCode = hashCode;\n";
		$str .= "    }\n\n";
	}
		
	function generateEmptyEnumGetFunction(&$str, $enumNode, $enumType,  $enumName) 
	{
		$str .= "    public static $enumName get(String hashCode) {\n";
		$str .= "    	return null;\n";
		$str .= "    }\n";
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

		$processedValues = array();
		
		$defaultPropertyName = "";
		foreach ( $enumNode->childNodes as $constNode )
		{
			if ($constNode->nodeType != XML_ELEMENT_NODE)
				continue;
	
			$propertyName = $constNode->getAttribute ( "name" );
			$propertyValue = $constNode->getAttribute ( "value" );
			
			if (in_array($propertyValue, $processedValues))
				continue;			// Java does not allow duplicate values in enums
			$processedValues[] = $propertyValue;
			
			if ($defaultPropertyName == "")
				$defaultPropertyName = $propertyName;
	
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
		$file = $this->_baseClientPath . "/types/$type.java";
		
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
		$this->appendLine ( '@SuppressWarnings("serial")' );
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
		$needsHashMap = false;
		$arrImportsEnums = array();
		$arrFunctions = array();

		foreach ( $classNode->childNodes as $propertyNode ) 
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			$propName = $propertyNode->getAttribute ( "name" );
			$propType = $propertyNode->getAttribute ( "type" );
			$isEnum = $propertyNode->hasAttribute ( "enumType" );
			
			$javaType = $this->getJavaType($propertyNode);

			if ($isEnum) 
				$arrImportsEnums[] = $javaType; 
			
			if ($propType == "array")
				$needsArrayList = true;
			
			if ($propType == "map")
				$needsHashMap = true;
				
			if ($propType == "KalturaObjectBase")
				$imports.= "import com.kaltura.client.KalturaObjectBase;\n";
						
			if($this->_usePrivateAttributes){
				$propertyLine = "private";
				
				$functionName = ucfirst($propName);
				$arrFunctions[] = "    public $javaType get{$functionName}(){";
				$arrFunctions[] = "        return this.$propName;";
				$arrFunctions[] = "    }";
				$arrFunctions[] = "    ";
				$arrFunctions[] = "    public void set{$functionName}($javaType $propName){";
				$arrFunctions[] = "        this.$propName = $propName;";
				$arrFunctions[] = "    }";
			}
			else{
				$propertyLine = "public";
			}
			
			$propertyLine .= " $javaType $propName";
			
			$initialValue = $this->getInitialPropertyValue($propertyNode);
			if ($initialValue != "") 
				$propertyLine .= " = " . $initialValue;
			
			$desc = $this->addDescription($propertyNode,"\t");
			if($desc)
				$this->appendLine ( $desc );
			
			$this->appendLine ( "    $propertyLine;" );
		}
		
		foreach($arrFunctions as $arrFunctionsLine){		
			$this->appendLine($arrFunctionsLine);
		}
		
		$arrImportsEnums = array_unique($arrImportsEnums);
		foreach($arrImportsEnums as $import) 
			$imports.= "import com.kaltura.client.enums.$import;\n";
		
		if ($needsArrayList)
			$imports .= "import java.util.ArrayList;\n";
		if ($needsHashMap)
			$imports .= "import java.util.HashMap;\n";
	}
	
	public function generateToParamsMethod($classNode) 
	{	
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

	public function generateFullConstructor(&$imports, $classNode, $needsSuperConstructor) 
	{	
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
			case "bigint" :
			case "int" :
			case "string" :
			case "bool" :
			case "float" :
				if ( $propType == "float" )
				{
					$propType = "double";
				}

				$txtIsUsed = true;
				$parsedProperty = "ParseUtils.parse".ucfirst($propType)."(txt)";
				if ($isEnum) 
				{
					$enumType = $propertyNode->getAttribute ( "enumType" );
					$propBlock .= "$enumType.get($parsedProperty);\n";
				} 
				else
				{
					$propBlock .= "$parsedProperty;\n";
				}
				break;
				
			case "array" :
				$arrayType = $propertyNode->getAttribute ( "arrayType" );
				$propBlock .= "ParseUtils.parseArray($arrayType.class, aNode);\n";
				break;
				
			case "map" :
				$arrayType = $propertyNode->getAttribute ( "arrayType" );
				$propBlock .= "ParseUtils.parseMap($arrayType.class, aNode);\n";
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
		
		$this->appendLine ( '@SuppressWarnings("serial")' );
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
		foreach($serviceImports as $import) 
			$imports .= "import $import;\n";
		
		$file = $this->_baseClientPath . "/services/" . $javaServiceType . ".java";
		$this->addFile ( $file, $imports . $this->getTextBlock () );
	}
	
	function writeAction($serviceId, DOMElement $actionNode, &$serviceImports) 
	{
		$action = $actionNode->getAttribute ( "name" );
		$action = $this->replaceReservedWords($action);
		
		$resultNode = $actionNode->getElementsByTagName ( "result" )->item ( 0 );
		$resultType = $resultNode->getAttribute ( "type" );
		
		$arrayType = '';
		$fallbackClass = null;
		if ($resultType == "array" || $resultType == "map") {
			$arrayType = $resultNode->getAttribute ( "arrayType" );
			$fallbackClass = $arrayType;
		}
    	else if($resultType && ($resultType != 'file') && !$this->isSimpleType($resultType))
    		$fallbackClass = $resultType;
		
	  	$javaOutputType = $this->getResultType($resultType, $arrayType, $serviceImports);
		
		$signaturePrefix = "public $javaOutputType " . $action . "(";
		
		$paramNodes = $actionNode->getElementsByTagName ( "param" );
		$paramNodesArr = array();
		foreach ( $paramNodes as $paramNode ) 
		{
			$paramNodesArr[] = $paramNode;
		}
		
		$this->writeActionOverloads($signaturePrefix, $action, $resultType, $paramNodesArr, $serviceImports);
		
		$signature = $this->getSignature ( $paramNodesArr , array('' => 'KalturaFile'), $serviceImports);
		
		$this->appendLine ();
		
		$desc = $this->addDescription($actionNode, "\t");
		if($desc)
			$this->appendLine ( $desc );
		$this->appendLine ( "    $signaturePrefix$signature throws KalturaApiException {" );
		
		$this->generateActionBodyServiceCall($serviceId, $action, $paramNodesArr, $serviceImports, $fallbackClass);
				
		if($resultType == 'file')
		{
			$this->appendLine ( "        return this.kalturaClient.serve();");
		}
		else
		{
			$serviceImports[] = "org.w3c.dom.Element";
			
			// Handle multi request
			$this->appendLine ( "        if (this.kalturaClient.isMultiRequest())" );
			$defaultValue = $this->getDefaultValue($resultType);
			$this->appendLine ( "            return $defaultValue;" );
						
			// Queue request
			if ($resultType)
				$this->appendLine ( "        Element resultXmlElement = this.kalturaClient.doQueue();" );
			else 
				$this->appendLine ( "        this.kalturaClient.doQueue();" );
			
			// Handle result type
			if ($resultType) 
				$this->handleResultType($resultType, $arrayType, $serviceImports);
		}
		
		$this->appendLine ( "    }" );
		
		$serviceImports[] = "com.kaltura.client.KalturaParams";
		$serviceImports[] = "com.kaltura.client.KalturaApiException";
	}

	public function writeActionOverloads($signaturePrefix, $action, $resultType, $paramNodes, &$serviceImports)
	{
		$returnStmt = '';
		if ($resultType)
			$returnStmt = 'return ';
			
		// split the parameters into mandatory and optional
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
		
		for($overloadNumber = 0; $overloadNumber < count ( $optionalParams ) + 1; $overloadNumber ++) 
		{
			$prototypeParams = array_slice ( $paramNodes, 0, count ( $mandatoryParams ) + $overloadNumber );
			$callParams = array_slice ( $paramNodes, 0, count ( $mandatoryParams ) + $overloadNumber + 1 );
			
			// find which file overloads need to be generated
			$hasFiles = false;
			foreach ($prototypeParams as $paramNode)
			{
				if ($paramNode->getAttribute ( "type" ) == "file")
					$hasFiles = true;
			}

			if ($hasFiles)
			{
				$fileOverloads = array(    
					array('' => 'KalturaFile'),
					array('' => 'File'),
					array('' => 'InputStream', 'Name' => 'String', 'Size' => 'long'),
					array('' => 'FileInputStream', 'Name' => 'String'),
				);
			}
			else
			{
				$fileOverloads = array(
					array('' => 'KalturaFile'),
				);
			}

			foreach ($fileOverloads as $fileOverload)
			{
				if (reset($fileOverload) == 'KalturaFile' && $overloadNumber == count($optionalParams))
					continue;			// this is the main overload
				
				// build the function prototype
				$signature = $this->getSignature ( $prototypeParams, $fileOverload, $serviceImports);
								
				// build the call parameters
				$params = array();
				foreach ( $callParams as $paramNode ) 
				{
					$optional = $paramNode->getAttribute ( "optional" );
					$paramName = $paramNode->getAttribute ( "name" );
					$paramType = $paramNode->getAttribute ( "type" );
					
					if ($optional == "1" && ! in_array ( $paramNode, $prototypeParams, true )) 
					{
						$params[] = $this->getDefaultParamValue($paramNode);
						continue;
					} 
						
					if ($paramType != "file" || reset($fileOverload) == 'KalturaFile')
					{
						$params[] = $paramName;
						continue;
					}
					
					$fileParams = array();
					foreach ($fileOverload as $namePostfix => $paramType)
					{
						$fileParams[] = $paramName . $namePostfix;
					}
					$params[] = "new KalturaFile(" . implode(', ', $fileParams) . ")";
				}				
				$paramsStr = implode(', ', $params);
				
				// write the result
				$this->appendLine ();
				$this->appendLine ( "    $signaturePrefix$signature throws KalturaApiException {" );
				$this->appendLine ( "        {$returnStmt}this.$action($paramsStr);" );
				$this->appendLine ( "    }" );
			}
		}
	}
	
	public function generateActionBodyServiceCall($serviceId, $action, $paramNodes, &$serviceImports, $fallbackClass) 
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
				$serviceImports[] = "com.kaltura.client.KalturaFiles";
				$serviceImports[] = "com.kaltura.client.KalturaFile";
				$haveFiles = true;
				$this->appendLine ( "        KalturaFiles kfiles = new KalturaFiles();" );
			}
			
			if($paramType == "file")
			{
				$this->appendLine ( "        kfiles.add(\"$paramName\", $paramName);" );
			}
			else 
				$this->appendLine ( "        kparams.add(\"$paramName\", $paramName);" );
		}
		
		// Add files to call
		if ($haveFiles)
			if(is_null($fallbackClass))
				$this->appendLine ( "        this.kalturaClient.queueServiceCall(\"$serviceId\", \"$action\", kparams, kfiles);" );
			else
				$this->appendLine ( "        this.kalturaClient.queueServiceCall(\"$serviceId\", \"$action\", kparams, kfiles, $fallbackClass.class);" );
		else
			if(is_null($fallbackClass))
				$this->appendLine ( "        this.kalturaClient.queueServiceCall(\"$serviceId\", \"$action\", kparams);" );
			else
				$this->appendLine ( "        this.kalturaClient.queueServiceCall(\"$serviceId\", \"$action\", kparams, $fallbackClass.class);" );
	}
	
	public function handleResultType($resultType, $arrayType, &$serviceImports) 
	{
		$serviceImports[] = "com.kaltura.client.utils.ParseUtils";
		$returnCall = "        ";
		switch ($resultType)
		{
			case "array" :
				$returnCall .= "return ParseUtils.parseArray($arrayType.class, resultXmlElement);";
				break;
			case "map" :
				$returnCall .= "return ParseUtils.parseMap($arrayType.class, resultXmlElement);";
				break;
			case "bigint":
			case "int" :
			case "float" :
			case "bool" :
			case "string" :
				if ( $resultType == "float" )
				{
					$resultType = "double";
				}

				$this->appendLine ( "        String resultText = resultXmlElement.getTextContent();" );
				$returnCall .= "return ParseUtils.parse" . ucwords($resultType) . "(resultText);";
				break;
			default :
				$returnCall .= "return ParseUtils.parseObject($resultType.class, resultXmlElement);";
				break;
		}		
		$this->appendLine($returnCall);
	}
	
	function writeMainClient(DOMNodeList $serviceNodes, DOMNodeList $configurationNodes) 
	{
		$apiVersion = $this->_doc->documentElement->getAttribute('apiVersion');
		$date = date('y-m-d');
		
		$imports = "";
		$imports .= "package com.kaltura.client;\n";
		
		$this->startNewTextBlock ();
		$this->appendLine ( $this->getBanner () );
		$this->appendLine ( '@SuppressWarnings("serial")' );
		$this->appendLine ( "public class KalturaClient extends KalturaClientBase {" );
		$this->appendLine ( "	" );
		$this->appendLine ( "	public KalturaClient(KalturaConfiguration config) {" );
		$this->appendLine ( "		super(config);" );
		$this->appendLine ( "		");
		$this->appendLine ( "		this.setClientTag(\"java:$date\");");
		$this->appendLine ( "		this.setApiVersion(\"$apiVersion\");");
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
				
				$type = $configurationPropertyNode->getAttribute("type");
				if(!$this->isSimpleType($type) && !$this->isArrayType($type))
				{
					$imports .= "import com.kaltura.client.types.$type;\n";
				}
				
				$type = $this->getJavaType($configurationPropertyNode, true);
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
		$this->appendLine ( "	protected void resetRequest(){");
		foreach($volatileProperties as $attributeName => $properties)
		{
			foreach($properties as $propertyName)
			{
				$this->appendLine("		this.{$attributeName}.remove(\"$propertyName\");");
			}
		}
		$this->appendLine ( "	}");
	
		
		$this->appendLine ( "}" );
		
		$imports .= "\n";
		
		$this->addFile ( $this->_baseClientPath . "/KalturaClient.java", $imports . $this->getTextBlock () );
	}
	
	protected function writeConfigurationProperty($configurationName, $name, $paramName, $type, $description)
	{
		$methodsName = ucfirst($name);
		
		$this->appendLine("	/**");
		if($description)
		{
			$this->appendLine("	 * $description");
			$this->appendLine("	 * ");
		}
		$this->appendLine("	 * @param $type \${$name}");
		$this->appendLine("	 */");
		$this->appendLine("	public void set{$methodsName}($type $name){");
		$this->appendLine("		this.{$configurationName}Configuration.put(\"$paramName\", $name);");
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
		$this->appendLine("	public $type get{$methodsName}(){");
		$this->appendLine("		if(this.{$configurationName}Configuration.containsKey(\"{$paramName}\")){");
		$this->appendLine("			return ($type) this.{$configurationName}Configuration.get(\"{$paramName}\");");
		$this->appendLine("		}");
		$this->appendLine("		");
		$this->appendLine("		return null;");
		$this->appendLine("	}");
		$this->appendLine("	");
	}
	
	function getSignature($paramNodes, $fileOverload, &$serviceImports) 
	{
		$signature = array();
		foreach ( $paramNodes as $paramNode ) 
		{
			$paramType = $paramNode->getAttribute ( "type" );
			$paramName = $paramNode->getAttribute ( "name" );
			$isEnum = $paramNode->hasAttribute ( "enumType" );

			if ($paramType == "array")
			{
				$serviceImports[] = "java.util.ArrayList";
				$serviceImports[] = "com.kaltura.client.types.*";
			}	
			elseif ($paramType == "map")
			{
				$serviceImports[] = "java.util.HashMap";
				$serviceImports[] = "com.kaltura.client.types.*";
			}	
			elseif ($isEnum)
				$serviceImports[] = "com.kaltura.client.enums.*";
			
			if ($paramType == "file")
			{
				$serviceImports = array_merge(
					$serviceImports, 
					array("java.io.File", "java.io.FileInputStream", "java.io.InputStream"));
 
				foreach ($fileOverload as $namePostfix => $paramType)
				{
					$signature[] = "{$paramType} {$paramName}{$namePostfix}";
				}
				continue;
			}
			
			if (strpos($paramType, 'Kaltura') === 0 && !$isEnum)
				$serviceImports[] = "com.kaltura.client.types.*";
			
			$javaType = $this->getJavaType($paramNode);
			
			$signature[] = "$javaType $paramName";
		}
		return implode(', ', $signature) . ")";
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

	protected function replaceReservedWords($name)
	{
		switch ($name)
		{
		case "goto":
			return "{$name}_";
		default:
			return $name;
		}
	}

	public function getInitialPropertyValue($propertyNode)
	{
		$propType = $propertyNode->getAttribute ( "type" );
		switch ($propType) 
		{
		case "float" :
			return "Double.MIN_VALUE";
			
		case "bigint" :
			return "Long.MIN_VALUE";
		case "int" :
			if ($propertyNode->hasAttribute ("enumType")) 
				return ""; // we do not want to initialize enums
			else 
				return "Integer.MIN_VALUE";
					
		default :
			return "";
		}
	}

	public function getDefaultValue($resultType) 
	{
		switch ($resultType)
		{
		case "":
			return '';
		
		case "int":
		case "float":
		case "bigint":
			return '0';
			
		case "bool":
			return 'false';
			
		default:
			return 'null';				
		}
	}
	
	public function getDefaultParamValue($paramNode)
	{
		$type = $paramNode->getAttribute ( "type" );
		$defaultValue = $paramNode->getAttribute ( "default" );
		
		switch ($type)
		{
		case "string": 
			if ($defaultValue == 'null')
				return 'null';
			else
				return "\"" . $defaultValue . "\"";
		case "bigint":
			$value = trim ( $defaultValue );
			if ($value == 'null')
				$value = "Long.MIN_VALUE";
			return $value;
		case "int": 
			$value = trim ( $defaultValue );
			if ($value == 'null')
				$value = "Integer.MIN_VALUE";
			
			if ($paramNode->hasAttribute ( "enumType" )) 
				return $paramNode->getAttribute ( "enumType" ) . ".get(" . $value . ")";
			else 
				return $value;
				
		case "file":
			return '(KalturaFile)null';
		
		default:
			return $defaultValue;
		}
	}
	
	public function getResultType($resultType, $arrayType, &$serviceImports) 
	{
		switch ($resultType)
		{
		case null :
			return "void";
			
		case "array" :
			$serviceImports[] = "java.util.List";
			$serviceImports[] = "com.kaltura.client.types.*";
				
			return ("List<" . $arrayType . ">");
			
		case "map" :
			$serviceImports[] = "java.util.Map";
			$serviceImports[] = "com.kaltura.client.types.*";
				
			return ("Map<String, " . $arrayType . ">");

		case "bigint" :
			return "long";

		case "bool" :
			return "boolean";
			
		case "file":
		case "string" :
			return "String";
			
		default :
			$serviceImports[] = "com.kaltura.client.types.*";
			return $resultType;
		}
	}
	
	public function getJavaType($propertyNode, $enforceObject = false)
	{
		$propType = $propertyNode->getAttribute ( "type" );
		$isEnum = $propertyNode->hasAttribute ( "enumType" );
		
		switch ($propType) 
		{
		case "bool" :
			return $enforceObject ? "Boolean" : "boolean";

		case "float" :
			return $enforceObject ? "Double" : "double";

		case "bigint" :
			return $enforceObject ? "Long" : "long";
			
		case "int" :
			if ($isEnum) 
				return $propertyNode->getAttribute ( "enumType" );
			else 
				return $enforceObject ? "Integer" : "int";

		case "string" :
			if ($isEnum) 
				return $propertyNode->getAttribute ( "enumType" );
			else 
				return "String";

		case "array" :
			$arrayType = $propertyNode->getAttribute ( "arrayType" );
			return "ArrayList<$arrayType>";

		case "map" :
			$arrayType = $propertyNode->getAttribute ( "arrayType" );
			return "HashMap<String, $arrayType>";

		case "file" :
			$javaType = "File";
			break;
			
		default :
			return $propType;
		}
	}
}
