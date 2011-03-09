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
		
		$this->writeObjectFactoryClass ( $classNodes );
		
		$serviceNodes = $xpath->query ( "/xml/services/service" );
		
		foreach ( $serviceNodes as $serviceNode ) 
		{
			$this->writeService ( $serviceNode );
		}
		
		$this->writeMainClient ( $serviceNodes );
	
	}
	
	//Private functions
	/////////////////////////////////////////////////////////////
	function writeEnum(DOMElement $enumNode) 
	{
		$enumName = $enumNode->getAttribute ( "name" );
		$enumType = $enumNode->getAttribute ( "enumType" );
		$encumCount = 0;
		
		$str = "";
		$str = "package com.kaltura.client.enums;\n";
		$str .= "\n";
		$str .= $this->getBanner ();
		$str .= "public enum $enumName" . " {\n";
		
		foreach ( $enumNode->childNodes as $constNode ) 
		{
			if ($constNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			$encumCount ++;
			$propertyName = $constNode->getAttribute ( "name" );
			$propertyValue = $constNode->getAttribute ( "value" );
			if ($enumType == "string") 
			{
				$propertyValue = "\"" . $propertyValue . "\"";
			}
			$str .= "    $propertyName ($propertyValue)," . "\n";
		}
		
		if ($encumCount > 0) 
		{
			if (substr ( $str, strlen ( $str ) - 2, 1 ) == ",") 
			{
				$str = substr ( $str, 0, strlen ( $str ) - 2 ) . ";\n\n";
			}
			
			if ($enumType == "string") 
			{
				$str .= "    String hashCode;\n\n";
				$str .= "    $enumName(String hashCode) {\n";
				$str .= "        this.hashCode = hashCode;\n";
				$str .= "    }\n\n";
				$str .= "    public String getHashCode() {\n";
				$str .= "        return this.hashCode;\n";
				$str .= "    }\n\n";
				$str .= "    public static $enumName get(String hashCode) {\n";
			} else 
			{
				$str .= "    int hashCode;\n\n";
				$str .= "    $enumName(int hashCode) {\n";
				$str .= "        this.hashCode = hashCode;\n";
				$str .= "    }\n\n";
				$str .= "    public int getHashCode() {\n";
				$str .= "        return this.hashCode;\n";
				$str .= "    }\n\n";
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
		$str .= "}\n";
		$file = "com/kaltura/client/enums/$enumName.java";
		$this->addFile ( $file, $str );
	}
	
	function writeClass(DOMElement $classNode) 
	{
		$imports = "";
		
		$imports .= "package com.kaltura.client.types;\n\n";
		$imports .= "import org.w3c.dom.Element;\n";
		$imports .= "import org.w3c.dom.Node;\n";
		$imports .= "import org.w3c.dom.NodeList;\n";
		$imports .= "import com.kaltura.client.KalturaObjectBase;\n";
		$imports .= "import com.kaltura.client.KalturaParams;\n";
		$imports .= "import com.kaltura.client.KalturaApiException;\n";
		$imports .= "import com.kaltura.client.KalturaObjectFactory;\n";
		
		$this->startNewTextBlock ();
		$this->appendLine ( "" );
		$type = $classNode->getAttribute ( "name" );
		
		$this->appendLine ( $this->getBanner () );
		
		$abstract = '';
		if ($classNode->hasAttribute("abstract"))
			$abstract = ' abstract';
		
		// class definition
		$needsSuperConstructor = false;
		if ($classNode->hasAttribute ( "base" )) 
		{
			$this->appendLine ( "public{$abstract} class $type extends " . $classNode->getAttribute ( "base" ) . " {" );
			$needsSuperConstructor = true;
		} 
		else 
		{
			$this->appendLine ( "public{$abstract} class $type extends KalturaObjectBase {" );
		}
		
		// class properties
		$needsArrayList = false;
		$needsObjectFactory = false;
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
					$javaType = "String";
					$initial_value = "";
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
						$imports .= "import com.kaltura.client.enums.$javaType;\n";
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
					$needsObjectFactory = true;
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
			
			$this->appendLine ( "    $propertyLine;" );
		}
		
		$this->appendLine ( "" );
		
		// Constructor
		$this->appendLine ( "    public $type() {" );
		$this->appendLine ( "    }" );
		$this->appendLine ( "" );
		
		// Constructor for Element
		$this->appendLine ( "    public $type(Element node) throws KalturaApiException {" );
		if ($needsSuperConstructor)
			$this->appendLine ( "        super(node);" );
			
		if ($classNode->childNodes->length) 
		{
			$this->appendLine ( "        NodeList childNodes = node.getChildNodes();" );
			$this->appendLine ( "        for (int i = 0; i < childNodes.getLength(); i++) {" );
			$this->appendLine ( "            Node aNode = childNodes.item(i);" );
			$this->appendLine ( "            String txt = aNode.getTextContent();" );
			$this->appendLine ( "            String nodeName = aNode.getNodeName();" );
			$this->appendLine ( "            if (false) {" );
			$this->appendLine ( "                // noop" );
			$propBlock = "            } ";
			foreach ( $classNode->childNodes as $propertyNode ) 
			{
				if ($propertyNode->nodeType != XML_ELEMENT_NODE)
					continue;
				
				$propType = $propertyNode->getAttribute ( "type" );
				$propName = $propertyNode->getAttribute ( "name" );
				$isEnum = $propertyNode->hasAttribute ( "enumType" );
				
				$propBlock .= "else if (nodeName.equals(\"$propName\")) {\n";
				
				switch ($propType) 
				{
					case "int" :
						if ($isEnum) 
						{
							$enumType = $propertyNode->getAttribute ( "enumType" );
							$propBlock .= "                try {\n";
							$propBlock .= "                    if (!txt.equals(\"\")) this.$propName = $enumType.get(Integer.parseInt(txt));\n";
							$propBlock .= "                } catch (NumberFormatException nfe) {}\n";
						} 
						else
						{
							$propBlock .= "                try {\n";
							$propBlock .= "                    if (!txt.equals(\"\")) this.$propName = Integer.parseInt(txt);\n";
							$propBlock .= "                } catch (NumberFormatException nfe) {}\n";
						}
						break;
					case "string" :
						$propBlock .= "                this.$propName = txt;\n";
						break;
					case "bool" :
						$propBlock .= "                if (!txt.equals(\"\")) this.$propName = ((txt.equals(\"0\")) ? false : true);\n";
						break;
					case "float" :
						$propBlock .= "                try {\n";
						$propBlock .= "                    if (!txt.equals(\"\")) this.$propName = Float.parseFloat(txt);\n";
						$propBlock .= "                } catch (NumberFormatException nfe) {}\n";
						break;
					case "array" :
						$arrayType = $propertyNode->getAttribute ( "arrayType" );
						$propBlock .= "                this.$propName = new ArrayList<$arrayType>();\n";
						$propBlock .= "                NodeList subNodeList = aNode.getChildNodes();\n";
						$propBlock .= "                for (int j = 0; j < subNodeList.getLength(); j++) {\n";
						$propBlock .= "                    Node arrayNode = subNodeList.item(j);\n";
						$propBlock .= "                    this.$propName.add(($arrayType)KalturaObjectFactory.create((Element)arrayNode));\n";
						$propBlock .= "                }\n";
						break;
					default : // sub object
						$propBlock .= "                this.$propName = ($propType)KalturaObjectFactory.create((Element)aNode);\n";
						break;
				}
				
				$propBlock .= "                continue;\n";
				$propBlock .= "            } ";
			}
			$propBlock .= "\n";
			$this->appendLine ( $propBlock );
			$this->appendLine ( "        }" );
		}
		$this->appendLine ( "    }" );
		$this->appendLine ( "" );
		
		// ToParams method
		$this->appendLine ( "    public KalturaParams toParams() {" );
		$this->appendLine ( "        KalturaParams kparams = super.toParams();" );
		
		foreach ( $classNode->childNodes as $propertyNode ) 
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			$propReadOnly = $propertyNode->getAttribute ( "readOnly" );
			if ($propReadOnly == "1")
				continue;
			
			$propType = $propertyNode->getAttribute ( "type" );
			$propName = $propertyNode->getAttribute ( "name" );
			$isEnum = $propertyNode->hasAttribute ( "enumType" );
			switch ($propType) 
			{
				case "int" :
					if ($isEnum)
						$this->appendLine ( "        if ($propName != null) kparams.addIntIfNotNull(\"$propName\", this.$propName.getHashCode());" );
					else
						$this->appendLine ( "        kparams.addIntIfNotNull(\"$propName\", this.$propName);" );
					break;
				case "string" :
					$this->appendLine ( "        kparams.addStringIfNotNull(\"$propName\", this.$propName);" );
					break;
				case "bool" :
					$this->appendLine ( "        kparams.addBoolIfNotNull(\"$propName\", this.$propName);" );
					break;
				case "float" :
					$this->appendLine ( "        kparams.addFloatIfNotNull(\"$propName\", this.$propName);" );
					break;
			}
		}
		$this->appendLine ( "        return kparams;" );
		$this->appendLine ( "    }" );
		
		// close class
		$this->appendLine ( "}" );
		$this->appendLine ();
		
		if ($needsArrayList)
			$imports .= "import java.util.ArrayList;\n";
		if ($needsObjectFactory)
			$imports .= "import com.kaltura.client.KalturaObjectFactory;\n";
		
		$file = "com/kaltura/client/types/$type.java";
		$this->addFile ( $file, $imports . "\n" . $this->getTextBlock () );
	}
	
	function writeService(DOMElement $serviceNode) 
	{
		$imports = "";
		$imports .= "package com.kaltura.client.services;\n\n";
		$imports .= "import org.w3c.dom.Element;\n";
		$imports .= "import com.kaltura.client.KalturaApiException;\n";
		$imports .= "import com.kaltura.client.KalturaClient;\n";
		$imports .= "import com.kaltura.client.KalturaObjectFactory;\n";
		$imports .= "import com.kaltura.client.KalturaParams;\n";
		$imports .= "import com.kaltura.client.KalturaServiceBase;\n";
		$imports .= "import com.kaltura.client.utils.XmlUtils;\n";
		// TODO we can make this better--the following imports aren't always used
		// or could be more specific. trick is that the writeService function may
		// not always know. as it is currently written, the writeAction function may
		// know about some of them.
		$imports .= "import com.kaltura.client.enums.*;\n";
		$imports .= "import com.kaltura.client.types.*;\n";
		$imports .= "import java.util.List;\n";
		$imports .= "import java.util.ArrayList;\n";
		$imports .= "import java.io.File;\n";
		$imports .= "import com.kaltura.client.KalturaFiles;\n";
		
		$serviceId = $serviceNode->getAttribute ( "id" );
		$serviceName = $serviceNode->getAttribute ( "name" );
		
		$javaServiceName = $this->upperCaseFirstLetter ( $serviceName ) . "Service";
		$javaServiceType = "Kaltura" . $javaServiceName;
		
		$this->startNewTextBlock ();
		$this->appendLine ();
		$this->appendLine ( $this->getBanner () );
		$this->appendLine ( "public class $javaServiceType extends KalturaServiceBase {" );
		$this->appendLine ( "    public $javaServiceType(KalturaClient client) {" );
		$this->appendLine ( "        this.kalturaClient = client;" );
		$this->appendLine ( "    }" );
		
		$actionNodes = $serviceNode->childNodes;
		foreach ( $actionNodes as $actionNode ) 
		{
			if ($actionNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			$this->writeAction ( $serviceId, $serviceName, $actionNode );
		}
		$this->appendLine ( "}" );
		
		$file = "com/kaltura/client/services/" . $javaServiceType . ".java";
		$this->addFile ( $file, $imports . $this->getTextBlock () );
	}
	
	function writeAction($serviceId, $serviceName, DOMElement $actionNode) 
	{
		$action = $actionNode->getAttribute ( "name" );
		$resultNode = $actionNode->getElementsByTagName ( "result" )->item ( 0 );
		$resultType = $resultNode->getAttribute ( "type" );
	    
	    if($resultType == 'file')
	    	return;
		
		switch ($resultType) 
		{
			case null :
				$javaOutputType = "void";
				break;
			case "array" :
				$arrayType = $resultNode->getAttribute ( "arrayType" );
				$javaOutputType = "List<" . $arrayType . ">";
				break;
			case "bool" :
				$javaOutputType = "boolean";
				break;
			case "string" :
				$javaOutputType = "String";
				break;
			default :
				$javaOutputType = $resultType;
				break;
		}
		
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
			$signature = $this->getSignature ( array_merge ( $mandatoryParams, $currentOptionalParams ) );
			
			// write the overload
			$this->appendLine ();
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
						$paramsStr .= "\"" . $paramNode->getAttribute ( "default" ) . "\"";
					} 
					else if ($type == "int") 
					{
						$value = trim ( $paramNode->getAttribute ( "default" ) );
						if (! strlen ( $value ))
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
			$this->appendLine ( "        return this." . $action . "($paramsStr);" );
			$this->appendLine ( "    }" );
		}
		
		$signature = $this->getSignature ( array_merge ( $mandatoryParams, $optionalParams ) );
		
		$this->appendLine ();
		$this->appendLine ( "    $signaturePrefix$signature throws KalturaApiException {" );
		
		$this->appendLine ( "        KalturaParams kparams = new KalturaParams();" );
		$haveFiles = false;
		foreach ( $paramNodes as $paramNode ) 
		{
			$paramType = $paramNode->getAttribute ( "type" );
			$paramName = $paramNode->getAttribute ( "name" );
			$isEnum = $paramNode->hasAttribute ( "enumType" );
			
			if ($haveFiles === false && $paramType === "file") 
			{
				$haveFiles = true;
				$this->appendLine ( "        KalturaFiles kfiles = new KalturaFiles();" );
			}
			
			switch ($paramType) 
			{
				case "string" :
					$this->appendLine ( "        kparams.addStringIfNotNull(\"$paramName\", " . $paramName . ");" );
					break;
				case "float" :
					$this->appendLine ( "        kparams.addFloatIfNotNull(\"$paramName\", " . $paramName . ");" );
					break;
				case "int" :
					if ($isEnum)
						$this->appendLine ( "        kparams.addIntIfNotNull(\"$paramName\", " . $paramName . ".getHashCode());" );
					else
						$this->appendLine ( "        kparams.addIntIfNotNull(\"$paramName\", " . $paramName . ");" );
					break;
				case "bool" :
					$this->appendLine ( "        kparams.addBoolIfNotNull(\"$paramName\", " . $paramName . ");" );
					break;
				case "array" :
					$this->appendLine ( "        for(" . $paramNode->getAttribute ( "arrayType" ) . " obj : " . $paramName . ") {" );
					$this->appendLine ( "            kparams.add(\"$paramName\", obj.toParams());" );
					$this->appendLine ( "        }" );
					break;
				case "file" :
					$this->appendLine ( "        kfiles.put(\"$paramName\", " . $paramName . ");" );
					break;
				default : // for objects
					$this->appendLine ( "        if ($paramName != null) kparams.add(\"$paramName\", " . $paramName . ".toParams());" );
					break;
			}
		}
		
		if ($haveFiles)
			$this->appendLine ( "        this.kalturaClient.queueServiceCall(\"$serviceId\", \"$action\", kparams, kfiles);" );
		else
			$this->appendLine ( "        this.kalturaClient.queueServiceCall(\"$serviceId\", \"$action\", kparams);" );
		
		$this->appendLine ( "        if (this.kalturaClient.isMultiRequest())" );
		if (! $resultType)
			$this->appendLine ( "            return;" );
		else if ($resultType == "int" || $resultNode == "float")
			$this->appendLine ( "            return 0;" );
		else if ($resultType == "bool")
			$this->appendLine ( "            return false;" );
		else
			$this->appendLine ( "            return null;" );
		
		$this->appendLine ( "        Element resultXmlElement = this.kalturaClient.doQueue();" );
		
		if ($resultType) 
		{
			switch ($resultType) 
			{
				case "array" :
					$arrayType = $resultNode->getAttribute ( "arrayType" );
					$this->appendLine ( "        List<$arrayType> list = new ArrayList<$arrayType>();" );
					$this->appendLine ( "        for(int i = 0; i < resultXmlElement.getChildNodes().getLength(); i++) {" );
					$this->appendLine ( "            Element node = (Element)resultXmlElement.getChildNodes().item(i);" );
					$this->appendLine ( "            list.add(($arrayType)KalturaObjectFactory.create(node));" );
					$this->appendLine ( "        }" );
					$this->appendLine ( "        return list;" );
					break;
				case "int" :
					$this->appendLine ( "        String resultText = XmlUtils.getTextValue(resultXmlElement, \"result\");" );
					$this->appendLine ( "        return Integer.parseInt(resultText);" );
					break;
				case "float" :
					$this->appendLine ( "        String resultText = XmlUtils.getTextValue(resultXmlElement, \"result\");" );
					$this->appendLine ( "        return Float.parseFloat(resultText);" );
					break;
				case "bool" :
					$this->appendLine ( "        String resultText = XmlUtils.getTextValue(resultXmlElement, \"result\");" );
					$this->appendLine ( "        return ((resultText.equals(\"0\")) ? false : true);" );
					break;
				case "string" :
					$this->appendLine ( "        String resultText = XmlUtils.getTextValue(resultXmlElement, \"result\");" );
					$this->appendLine ( "        return resultText;" );
					break;
				default :
					$this->appendLine ( "        return ($resultType)KalturaObjectFactory.create(resultXmlElement);" );
					break;
			}
		}
		$this->appendLine ( "    }" );
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
		
		$this->addFile ( "com/kaltura/client/KalturaClient.java", $imports . $this->getTextBlock () );
	
	}
	
	function getSignature($paramNodes) 
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
					$javaType = "ArrayList<" . $paramNode->getAttribute ( "arrayType" ) . ">";
					break;
				case "bool" :
					$javaType = "boolean";
					break;
				case "file" :
					$javaType = "File";
					break;
				case "int" :
					if ($isEnum)
						$javaType = $paramNode->getAttribute ( "enumType" );
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
	
	function writeObjectFactoryClass(DOMNodeList $classNodes) 
	{
		$imports = "";
		$imports .= "package com.kaltura.client;\n\n";
		$imports .= "import org.w3c.dom.Element;\n";
		$imports .= "import org.w3c.dom.Node;\n";
		$imports .= "import org.w3c.dom.NodeList;\n";
		$imports .= "import com.kaltura.client.KalturaApiException;\n";
		
		$this->startNewTextBlock ();
		$this->appendLine ();
		$this->appendLine ( $this->getBanner () );
		$this->appendLine ( "public class KalturaObjectFactory {" );
		$this->appendLine ( "    public static Object create(Element xmlElement) throws KalturaApiException {" );
		$this->appendLine ( "        NodeList objectTypeNodes = xmlElement.getElementsByTagName(\"objectType\");" );
		$this->appendLine ( "        Node objectTypeNode = objectTypeNodes.item(0);" );
		$this->appendLine ( "        String objectType = objectTypeNode.getTextContent();" );
		$this->appendLine ( "        if (false) {" );
		$this->appendLine ( "        	// noop" );
		$this->appendLine ( "        }" );
		
		foreach ( $classNodes as $classNode ) 
		{
			if($classNode->getAttribute ( "abstract" ))
				continue;
				
			$imports .= "import com.kaltura.client.types." . $classNode->getAttribute ( "name" ) . ";\n";
			$this->appendLine ( "        else if (objectType.equals(\"" . $classNode->getAttribute ( "name" ) . "\")) {" );
			$this->appendLine ( "            return new " . $classNode->getAttribute ( "name" ) . "(xmlElement);" );
			$this->appendLine ( "        }" );
		}
		
		$this->appendLine ( "        else {" );
		$this->appendLine ( "            throw new KalturaApiException(\"Invalid object type\");" );
		$this->appendLine ( "        }" );
		$this->appendLine ( "        return null;" );
		$this->appendLine ( "    }" );
		$this->appendLine ( "}" );
		
		$this->addFile ( "com/kaltura/client/KalturaObjectFactory.java", $imports . $this->getTextBlock () );
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