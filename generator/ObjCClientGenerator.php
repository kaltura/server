<?php
class ObjCClientGenerator extends ClientGeneratorFromXml
{
	/**
	 * @var DOMDocument
	 */
	protected $_doc = null;
	
	protected $_mFileData = '';
	protected $_hFileData = '';
	
	protected $_outputFileBase = '';
	
	protected $_projectSections = array();
	
	function __construct($xmlPath, Zend_Config $config, $sourcePath = "sources/objc")
	{
		parent::__construct($xmlPath, $sourcePath, $config);
		$this->_doc = new KDOMDocument();
		$this->_doc->load($this->_xmlFile);
	}
	
	function getSingleLineCommentMarker()
	{
		return '//';
	}
	
	// utility functions
	function getObjCType($propType)
	{
		switch ($propType)
		{
		case "" :
			return "void";
		case "bool" :
			return "KALTURA_BOOL";
		case "bigint" :
		case "int" :
			return "int";
		case "float" :
			return "double";
		case "string" :
			return "NSString*";
		case "array" :
			return "NSMutableArray*";
		case "file" :
			return 'NSString*';
		default : // object
			return "$propType*";
		}
	}
	
	function isSimpleType($propType)
	{
		switch ($propType)
		{
		case "bool" :
		case "int" :
		case "bigint" :
		case "float" :
			return true;
		default :
			return false;
		}
	}
	
	function getPropDefaultValue($propType)
	{
		switch ($propType)
		{
		case "bool" :
			return "KALTURA_UNDEF_BOOL";
		case "bigint":
		case "int" :
			return "KALTURA_UNDEF_INT";
		case "float" :
			return "KALTURA_UNDEF_FLOAT";
		default : // file, string, array, object
			return null;
		}
	}
	
	function getTypeName($propType)
	{
		switch ($propType)
		{
		case "bool" :
			return "Bool";
		case "bigint":
		case "int" :
			return "Int";
		case "float" :
			return "Float";
		case "file":
		case "string" :
			return "String";
		case "array" :
			return "Array";
		default : // object
			return "Object";
		}	
	}
	
	function getPropertyMemModifier($propType)
	{
		switch ($propType)
		{
		case "bool" :
		case "bigint":
		case "int" :
		case "float" :
			return "assign";
		case "file":
		case "string" :
			return "copy";
		default : // array + object
			return "retain";
		}
	}
	
	function renameReservedWords($propertyName)
	{
		switch ($propertyName)
		{
			case "NO":
			case "YES":
				return "{$propertyName}_";
			default:
				return $propertyName;
		}
	}

	function renameReservedProperties($propertyName)
	{
		if (kString::beginsWith($propertyName, 'new') ||
			kString::beginsWith($propertyName, 'copy'))
			return "_$propertyName";
		return $propertyName;
	}
	
	static function buildMultilineComment($description, $indent = "")
	{
		$description = trim($description);
		if (!$description)
		{
			return "";
		}
		
		$description = str_replace("\n", "\n$indent// ", $description);
		return "$indent// " . $description;
	}
	
	function getPluginClassName($pluginName)
	{
		if ($pluginName == '')
			return "KalturaClient";
		
		return "Kaltura" . $this->upperCaseFirstLetter($pluginName) . "ClientPlugin";
	}
	
	protected function appendHText($txt = "")
	{
		$this->_hFileData .= $txt;
	}
	
	protected function appendMText($txt = "")
	{
		$this->_mFileData .= $txt;
	}
	
	protected function appendHLine($txt = "")
	{
		$this->appendHText($txt . "\n");
	}
	
	protected function appendMLine($txt = "")
	{
		$this->appendMText($txt . "\n");
	}
	
	function appendSeparator($sectionName)
	{
		$this->appendHLine("///////////////////////// $sectionName /////////////////////////");
		$this->appendMLine("///////////////////////// $sectionName /////////////////////////");
 	}
	
	function classHasProps($classNode)
	{
		foreach($classNode->childNodes as $propertyNode)
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
							
			return true;
		}
		return false;
	}
	
	function classHasSimpleProps($classNode)
	{
		foreach($classNode->childNodes as $propertyNode)
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
							
			$propType = $propertyNode->getAttribute ( "type" );
			if (!$this->isSimpleType($propType))
				continue;
			
			return true;
		}
		return false;
	}	
	
	function classHasComplexProps($classNode)
	{
		foreach($classNode->childNodes as $propertyNode)
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
							
			$propType = $propertyNode->getAttribute ( "type" );
			if ($this->isSimpleType($propType))
				continue;
			
			return true;
		}
		return false;
	}	
	
	// enums generation
	function writeEnum(DOMElement $enumNode)
	{
		$enumName = $enumNode->getAttribute("name");
		
		if($this->generateDocs)
		{
			$this->appendHLine("// @package $this->package");
			$this->appendHLine("// @subpackage $this->subpackage");
		}
		
	 	$this->appendHLine("@interface $enumName : NSObject");
	 	$this->appendMLine("@implementation $enumName");
	 	foreach($enumNode->childNodes as $constNode)
		{
			if ($constNode->nodeType != XML_ELEMENT_NODE)
				continue;
				
			$propertyName = $constNode->getAttribute("name");
			$propertyName = $this->renameReservedWords($propertyName);
			
			$propertyValue = $constNode->getAttribute("value");
			if ($enumNode->getAttribute("enumType") == "string")
			{
				$this->appendHLine("+ (NSString*)$propertyName;");
				
				$this->appendMLine("+ (NSString*)$propertyName");
				$this->appendMLine('{');
				$this->appendMLine("    return @\"$propertyValue\";");
				$this->appendMLine('}');
			}
			else
			{
				$this->appendHLine("+ (int)$propertyName;");

				$this->appendMLine("+ (int)$propertyName");
				$this->appendMLine('{');
				$this->appendMLine("    return $propertyValue;");
				$this->appendMLine('}');
			}
		}		
		$this->appendHLine("@end\n");
		$this->appendMLine("@end\n");
	}
	
	// classes generation
	function writeClass(DOMElement $classNode)
	{
		$type = $classNode->getAttribute("name");
		
		if($this->generateDocs)
		{
			$this->appendHLine("// @package $this->package");
			$this->appendHLine("// @subpackage $this->subpackage");
		}
		
		if ($classNode->hasAttribute("base"))
			$baseClass = $classNode->getAttribute("base");
		else
			$baseClass = 'KalturaObjectBase';
			
		$extCode = $this->getClassExtProperties($classNode);
		if ($extCode)
		{
			$this->appendMLine("@interface $type()");
			$this->appendMText($extCode);
			$this->appendMLine("@end\n");			
		}
			
		$description = self::buildMultilineComment($classNode->getAttribute("description"));
		if ($description)
			$this->appendHLine($description);
		$this->appendHLine("@interface $type : $baseClass");
		$this->appendMLine("@implementation $type");
					
		$this->writeClassProperties($classNode, $extCode);
		$this->writeClassCtor($classNode);
		$this->writeClassGetPropTypeFunc($classNode);
		$this->writeClassSetSimplePropFunc($classNode);
		$this->writeClassToParamsFunc($classNode);
		$this->writeClassDtor($classNode);
		
		$this->appendHLine("@end\n");
		$this->appendMLine("@end\n");
	}
	
	function getClassExtProperties(DOMElement $classNode)
	{
		$result = '';
		foreach($classNode->childNodes as $propertyNode)
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			$isReadOnly = $propertyNode->getAttribute("readOnly") == 1;
			if (!$isReadOnly)
				continue;
				
			$propName = $propertyNode->getAttribute("name");
			$propName = $this->renameReservedProperties($propName);
			$propType = $propertyNode->getAttribute("type");
			$objcPropType = $this->getObjCType($propType);
			
			$modifiers = array('nonatomic', $this->getPropertyMemModifier($propType));
			$modifiers = implode(',', $modifiers);
			
			$result .= "@property ($modifiers) $objcPropType $propName;\n";
		}
		
		return $result;
	}
	
	function writeClassCtor(DOMElement $classNode)
	{
		if (!$this->classHasSimpleProps($classNode))
			return;
			
		$this->appendMLine("- (id)init");
		$this->appendMLine("{");
		$this->appendMLine("    self = [super init];");
		$this->appendMLine("    if (self == nil)");
		$this->appendMLine("        return nil;");
		
		foreach($classNode->childNodes as $propertyNode)
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			$propName = $propertyNode->getAttribute("name");
			$propName = $this->renameReservedProperties($propName);
			$propType = $propertyNode->getAttribute("type");
			$propValue = $this->getPropDefaultValue($propType);
			if (!$propValue)
				continue;
				
			$this->appendMLine("    self->_$propName = $propValue;");
		}
		$this->appendMLine("    return self;");
		$this->appendMLine("}\n");
	}
	
	function writeClassDtor(DOMElement $classNode)
	{
		if (!$this->classHasComplexProps($classNode))
			return;
			
		$this->appendMLine("- (void)dealloc");
		$this->appendMLine("{");
		
		foreach($classNode->childNodes as $propertyNode)
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			$propName = $propertyNode->getAttribute("name");
			$propName = $this->renameReservedProperties($propName);
			$propType = $propertyNode->getAttribute("type");
			if ($this->isSimpleType($propType))
				continue;

			$this->appendMLine("    [self->_$propName release];");
			
		}
		$this->appendMLine("    [super dealloc];");
		$this->appendMLine("}\n");
	}
	
	function writeClassProperties(DOMElement $classNode)
	{
		$hasProps = false;
		foreach($classNode->childNodes as $propertyNode)
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			$propName = $propertyNode->getAttribute("name");
			$propName = $this->renameReservedProperties($propName);
			$isReadOnly = $propertyNode->getAttribute("readOnly") == 1;
			$propType = $propertyNode->getAttribute("type");
			
			$objcPropType = $this->getObjCType($propType);
			$modifiers = array('nonatomic', $this->getPropertyMemModifier($propType));
			if ($isReadOnly)
				$modifiers[] = 'readonly';
			$modifiers = implode(',', $modifiers);
					
			$description = self::buildMultilineComment($propertyNode->getAttribute("description"));
			if ($description)
				$this->appendHLine($description);
			
			$comments = array();
			if ($propertyNode->hasAttribute("enumType"))
				$comments[] = "enum {$propertyNode->getAttribute("enumType")}";
			else if ($propType == "array")
				$comments[] = "of {$propertyNode->getAttribute("arrayType")} elements";
			$isInsertOnly = $propertyNode->getAttribute("insertOnly") == 1;
			if ($isInsertOnly)
				$comments[] = "insertonly";	
			if ($comments)
				$comments = "\t// " . implode(', ', $comments);
			else
				$comments = '';
				
			$this->appendHLine("@property ($modifiers) $objcPropType $propName;$comments");
			$this->appendMLine("@synthesize $propName = _$propName;");
			$hasProps = true;
		}
		
		if ($hasProps)
			$this->appendMLine();
	}
	
	function writeClassGetPropTypeFunc(DOMElement $classNode)
	{
		foreach($classNode->childNodes as $propertyNode)
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
							
			$propType = $propertyNode->getAttribute ( "type" );
			$propType = $this->getTypeName($propType);
			$propName = $propertyNode->getAttribute ( "name" );
			$propName = $this->upperCaseFirstLetter($propName);

			$this->appendHLine("- (KalturaFieldType)getTypeOf$propName;");
			$this->appendMLine("- (KalturaFieldType)getTypeOf$propName");
			$this->appendMLine("{");
			$this->appendMLine("    return KFT_$propType;");
			$this->appendMLine("}\n");
			
			$objectType = '';
			switch ($propType)
			{
			case "Object":
				$objectType = $propertyNode->getAttribute ( "type" );
				break;
			case "Array":
				$objectType = $propertyNode->getAttribute ( "arrayType" );
				break;
			}
			
			if ($objectType)
			{
				$this->appendHLine("- (NSString*)getObjectTypeOf$propName;");
				$this->appendMLine("- (NSString*)getObjectTypeOf$propName");
				$this->appendMLine("{");
				$this->appendMLine("    return @\"$objectType\";");
				$this->appendMLine("}\n");
			}
		}
	}
	
	function writeClassSetSimplePropFunc(DOMElement $classNode)
	{
		foreach($classNode->childNodes as $propertyNode)
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
							
			$propType = $propertyNode->getAttribute ( "type" );
			if (!$this->isSimpleType($propType))
				continue;
			
			$propType = $this->getTypeName($propType);
			$propName = $propertyNode->getAttribute ( "name" );
			$ucPropName = $this->upperCaseFirstLetter($propName);
			$propName = $this->renameReservedProperties($propName);
			$propValue = "[KalturaSimpleTypeParser parse$propType:aPropVal]";
			
			$this->appendHLine("- (void)set{$ucPropName}FromString:(NSString*)aPropVal;");
			$this->appendMLine("- (void)set{$ucPropName}FromString:(NSString*)aPropVal");
			$this->appendMLine("{");
			$this->appendMLine("    self.$propName = $propValue;");
			$this->appendMLine("}\n");
		}
	}
	
	function writeClassToParamsFunc(DOMElement $classNode)
	{
		$type = $classNode->getAttribute ( "name" );
		
		$this->appendMLine( "- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper" );
		$this->appendMLine( "{" );
		$this->appendMLine( "    [super toParams:aParams isSuper:YES];" );
		$this->appendMLine( "    if (!aIsSuper)" );
		$this->appendMLine( "        [aParams putKey:@\"objectType\" withString:@\"$type\"];" );
		
		foreach ( $classNode->childNodes as $propertyNode ) 
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			$propReadOnly = $propertyNode->getAttribute ( "readOnly" );
			if ($propReadOnly == "1")
				continue;
			
			$propType = $propertyNode->getAttribute ( "type" );
			$propType = $this->getTypeName($propType);
			$propName = $propertyNode->getAttribute ( "name" );
			$propName = $this->renameReservedProperties($propName);
			$this->appendMLine( "    [aParams addIfDefinedKey:@\"$propName\" with$propType:self.$propName];" );
		}
		$this->appendMLine( "}\n" );
	}
	
	// services generation
	function writeService(DOMElement $serviceNode)
	{
		$serviceName = $serviceNode->getAttribute("name");
		$serviceId = $serviceNode->getAttribute("id");
		
		$serviceClassName = "Kaltura".$this->upperCaseFirstLetter($serviceName)."Service";
		
		if($this->generateDocs)
		{
			$this->appendHLine("// @package $this->package");
			$this->appendHLine("// @subpackage $this->subpackage");
		}
		
		$description = self::buildMultilineComment($serviceNode->getAttribute("description"));
		if ($description)
			$this->appendHLine($description);
		$this->appendHLine("@interface $serviceClassName : KalturaServiceBase");
		$this->appendMLine("@implementation $serviceClassName");
					
		$actionNodes = $serviceNode->childNodes;
		foreach($actionNodes as $actionNode)
		{
		    if ($actionNode->nodeType != XML_ELEMENT_NODE)
				continue;
				
		    $this->writeAction($serviceId, $actionNode);
		}
		$this->appendHLine("@end\n");
		$this->appendMLine("@end\n");
	}
	
	// actions generation
	function writeAction($serviceId, DOMElement $actionNode)
	{
		$actionName = $actionNode->getAttribute("name");
	    $resultNode = $actionNode->getElementsByTagName("result")->item(0);
	    $resultType = $resultNode->getAttribute("type");
		$paramNodes = $actionNode->getElementsByTagName("param");
		
		$signature = $this->getSignature($resultType, $actionName, $paramNodes);
		$description = self::buildMultilineComment($actionNode->getAttribute("description"));
		if ($description)
			$this->appendHLine($description);
		$this->appendHLine("$signature;");
		$this->appendMLine($signature);
		$this->appendMLine("{");
		
		$optionalParams = 0;
		foreach($paramNodes as $paramNode)
		{
			if ($paramNode->getAttribute ( "optional" ) == "1")
				$optionalParams++;
			
			$paramType = $paramNode->getAttribute("type");
			if ($paramType == "file")
				$paramType = "FileName";
			else 
				$paramType = $this->getTypeName($paramType);
				
		    $paramName = $paramNode->getAttribute("name");
			$paramNameCapitalized = $this->upperCaseFirstLetter($paramName);
		    $this->appendMLine("    [self.client.params addIfDefinedKey:@\"$paramName\" with$paramType:a{$paramNameCapitalized}];");
		}
		
		$returnStmt = "return ";
		switch ($resultType)
		{
		case "":
			$returnStmt = "";
			$serviceType = "Void";
			break;
		case "file":
			$serviceType = "Serve";
			break;
		default:
			$serviceType = $this->getTypeName($resultType);
			break;
		}

		$withExpectedType = '';
		switch ($serviceType)
		{
		case "Object":
			$withExpectedType = " withExpectedType:@\"$resultType\"";
			break;
		case "Array":
			$resultArrayType = $resultNode->getAttribute("arrayType");
			$withExpectedType = " withExpectedType:@\"$resultArrayType\"";
			break;
		}
		
		$this->appendMLine("    {$returnStmt}[self.client queue{$serviceType}Service:@\"$serviceId\" withAction:@\"$actionName\"$withExpectedType];");
		$this->appendMLine("}\n");
		
		$this->writeActionDefaultOverloads($actionNode, $optionalParams);
	}

	function writeActionDefaultOverloads(DOMElement $actionNode, $optionalParams)
	{
		$actionName = $actionNode->getAttribute("name");
	    $resultNode = $actionNode->getElementsByTagName("result")->item(0);
	    $resultType = $resultNode->getAttribute("type");
		$paramNodes = $actionNode->getElementsByTagName("param");
		
		if ($resultType == "")
			$returnStmt = "";
		else  
			$returnStmt = "return ";
			
		for ($curIndex = 0; $curIndex < $optionalParams; $curIndex++)
		{
			$paramCount = $paramNodes->length - $curIndex - 1;
			$signature = $this->getSignature($resultType, $actionName, $paramNodes, $paramCount);
			$this->appendHLine("$signature;");
			$this->appendMLine($signature);
			$this->appendMLine("{");
			$funcBody = "";
			$paramIndex = 0;
			foreach($paramNodes as $paramNode)
			{
				if (!is_null($paramCount) && $paramIndex > $paramCount)
					break;
					
				$paramName = $paramNode->getAttribute("name");
				$paramName = $this->upperCaseFirstLetter($paramName);
				if ($paramIndex == $paramCount)
				{
					$paramType = $paramNode->getAttribute("type");
					$paramValue = $this->getPropDefaultValue($paramType);
					if (!$paramValue)
						$paramValue = "nil";
					$space = "";
				}
				else 
				{
					$paramValue = "a{$paramName}";
					$space = " ";
				}
				
				if ($paramIndex == 0)
					$withStr = "With";
				else 
					$withStr = "with";
				$funcBody .= "{$withStr}{$paramName}:{$paramValue}{$space}";
				$paramIndex++;
			}
			$this->appendMLine("    {$returnStmt}[self {$actionName}$funcBody];");
			$this->appendMLine("}\n");
		}
	}
	
	function getSignature($resultType, $actionName, $paramNodes, $paramCount = null)
	{
		$resultType = $this->getObjCType($resultType);
		$result = "- ($resultType)$actionName";
			
		$paramIndex = 0;
		foreach($paramNodes as $paramNode)
		{
			if (!is_null($paramCount) && $paramIndex >= $paramCount)
				break;
				
			$paramName = $paramNode->getAttribute("name");
			$paramName = $this->upperCaseFirstLetter($paramName);
			$paramType = $paramNode->getAttribute("type");

			if ($paramType == 'array')
				$paramType = "NSArray*";
			else
				$paramType = $this->getObjCType($paramType);
			
			if ($paramIndex < $paramNodes->length - 1 && 
				(is_null($paramCount) || $paramIndex < $paramCount - 1))
				$space = " ";
			else 
				$space = "";
			
			if ($paramIndex == 0)
				$withStr = "With";
			else 
				$withStr = "with";
			$result .= "{$withStr}{$paramName}:({$paramType})a{$paramName}{$space}";
			
			$paramIndex++;
		}
		
		return $result;
	}

	// plugin generation
	function writePluginHeader($pluginName)	
	{
    	if($this->generateDocs)
		{
			$this->appendHLine("// @package $this->package");
			$this->appendHLine("// @subpackage $this->subpackage");
		}
		
		if ($pluginName != '')
		{
			$this->appendHLine('#import "../KalturaClient.h"');

			$xpath = new DOMXPath($this->_doc);
			$dependencyNodes = $xpath->query("/xml/plugins/plugin[@name = '$pluginName']/dependency");
			foreach($dependencyNodes as $dependencyNode)
			{
				$curClassName = $this->getPluginClassName($dependencyNode->getAttribute("pluginName"));
				$this->appendHLine("#import \"$curClassName.h\"");
			}
		}
		else 
		{
			$this->appendHLine('#import "KalturaClientBase.h"');
		}
		$this->appendHLine('');

		if ($pluginName == '')
		{
			$apiVersion = $this->_doc->documentElement->getAttribute('apiVersion');
			$this->appendHLine("#define API_VERSION @\"$apiVersion\"");
			$this->appendHLine('');
		}
		
		$this->appendMLine('#import "'.$this->getPluginClassName($pluginName).'.h"');
		$this->appendMLine('');
	}
	
	function writePluginClass($pluginClassName, array $services)
	{
		if (!$services)
			return;
		
		if ($pluginClassName == "KalturaClient")
			$baseClassName = "KalturaClientBase";
		else
			$baseClassName = "KalturaClientPlugin";
			
		$this->appendHLine("@interface $pluginClassName : $baseClassName");	
		$this->appendHLine("{");	
		foreach ($services as $serviceName)
		{
			$serviceClassName = "Kaltura".$this->upperCaseFirstLetter($serviceName)."Service";
			$this->appendHLine("	$serviceClassName* _$serviceName;");
		}
		$this->appendHLine("}");	
		$this->appendHLine("");	
		$this->appendMLine("@implementation $pluginClassName");
		
		// properties
		if ($pluginClassName != "KalturaClient")
		{
			$this->appendHLine("@property (nonatomic, assign) KalturaClientBase* client;");
			$this->appendMLine("@synthesize client = _client;");
		}
		
		foreach ($services as $serviceName)
		{
			$serviceClassName = "Kaltura".$this->upperCaseFirstLetter($serviceName)."Service";
			$this->appendHLine("@property (nonatomic, readonly) $serviceClassName* $serviceName;");
		}
		$this->appendMLine();
		
		// init
		if ($pluginClassName == "KalturaClient")
		{
			$initParams = "WithConfig:(KalturaClientConfiguration*)aConfig";
			$initSuperParams = "WithConfig:aConfig";
			$clientVar = "self";
		}
		else
		{
			$initParams = "WithClient:(KalturaClient*)aClient";
			$initSuperParams = "";
			$clientVar = "self.client";
		}
		
		$this->appendMLine("- (id)init{$initParams}");
		$this->appendMLine("{");
		$this->appendMLine("    self = [super init{$initSuperParams}];");
		$this->appendMLine("    if (self == nil)");
		$this->appendMLine("        return nil;");
		if ($pluginClassName == "KalturaClient")
		{
			$this->appendMLine("    self.apiVersion = API_VERSION;");
		}
		else
		{
			$this->appendMLine("    self.client = aClient;");
		}
		$this->appendMLine("    return self;");
		$this->appendMLine("}");
		$this->appendMLine();

		foreach ($services as $serviceName)
		{
			$serviceClassName = "Kaltura".$this->upperCaseFirstLetter($serviceName)."Service";
			$this->appendMLine("- ($serviceClassName*)$serviceName");
			$this->appendMLine("{");
			$this->appendMLine("    if (self->_$serviceName == nil)");
			$this->appendMLine("    	self->_$serviceName = [[$serviceClassName alloc] initWithClient:$clientVar];");
			$this->appendMLine("    return self->_$serviceName;");
			$this->appendMLine("}");
			$this->appendMLine();
		}
		
		// dealloc
		$this->appendMLine("- (void)dealloc");
		$this->appendMLine("{");
		foreach ($services as $serviceName)
		{
			$this->appendMLine("    [self->_$serviceName release];");
		}
		$this->appendMLine("	[super dealloc];");
		$this->appendMLine("}");
		$this->appendMLine();
		
		$this->appendHLine("@end\n");
		$this->appendMLine("@end\n");
	}

	function writePlugin($pluginName, $enumNodes, $classNodes, $serviceNodes, $serviceNamesNodes)
	{
		$pluginClassName = $this->getPluginClassName($pluginName);		
		if ($pluginName == '')
		{
			$this->_outputFileBase = "$pluginClassName";
		}
		else 
		{
			$this->_outputFileBase = "KalturaPlugins/$pluginClassName";
		}

		$this->writePluginHeader($pluginName);	
    	
		$this->appendSeparator('enums');
		$enums = array();
		foreach($enumNodes as $enumNode)
		{
			if($enumNode->hasAttribute('plugin') && $pluginName == '')
				continue;
				
			$this->writeEnum($enumNode);
			$enums[] = $enumNode->getAttribute("name");
		}
	
		$this->appendSeparator('classes');
		$classes = array();
		foreach($classNodes as $classNode)
		{
			if($classNode->hasAttribute('plugin') && $pluginName == '')
				continue;
				
			$this->writeClass($classNode);
			$classes[] = $classNode->getAttribute("name");
		}
		
		$this->appendSeparator('services');
		foreach($serviceNodes as $serviceNode)
		{
			if($serviceNode->hasAttribute('plugin') && $pluginName == '')
				continue;
				
			$this->writeService($serviceNode);
		}
		
		$services = array();
		foreach($serviceNamesNodes as $serviceNode)
		{
			if($serviceNode->hasAttribute('plugin') && $pluginName == '')
				continue;
				
			$services[] = $serviceNode->getAttribute("name");
		}
		
		$this->writePluginClass($pluginClassName, $services);

		if ($pluginName != '')
		{
			$this->addPluginFileToProject($pluginClassName, 'h');
			$this->addPluginFileToProject($pluginClassName, 'm');
		}
		$this->addFile("KalturaClient/KalturaClient/{$this->_outputFileBase}.h", $this->_hFileData);
		$this->addFile("KalturaClient/KalturaClient/{$this->_outputFileBase}.m", $this->_mFileData);
		$this->_hFileData = '';
		$this->_mFileData = '';
	}
	
	function genRandomUid()
	{
		// generate a 96 bit random, as used in xCode projects
		return strtoupper(substr(md5(uniqid(rand(),true)), 0, 24));
	}
	
	function addPluginFileToProject($fileName, $fileExt)
	{
		$fileName .= ".$fileExt";
		$fileID = $this->genRandomUid();
		$fileRefID = $this->genRandomUid();
		if ($fileExt == 'h')
		{
			$fileCat = 'Headers';
			$fileType = 'h';
			$buildPhase = 'PBXHeadersBuildPhase';
		}
		else
		{
			$fileCat = 'Sources';
			$fileType = 'objc';
			$buildPhase = 'PBXSourcesBuildPhase';
		}
		
		$this->addLineToProjectSection(
			"<<< PBXBuildFile plugins >>>",
			"\t\t$fileID /* $fileName in $fileCat */ = {isa = PBXBuildFile; fileRef = $fileRefID /* $fileName */; };");

		$this->addLineToProjectSection(
			"<<< PBXFileReference plugins >>>",
			"\t\t$fileRefID /* $fileName */ = {isa = PBXFileReference; fileEncoding = 4; lastKnownFileType = sourcecode.c.$fileType; path = $fileName; sourceTree = \"<group>\"; };");

		$this->addLineToProjectSection(
			"<<< PBXGroup plugins >>>",
			"\t\t\t\t$fileRefID /* $fileName */,");
		
		$this->addLineToProjectSection(
			"<<< $buildPhase plugins >>>",
			"\t\t\t\t$fileID /* $fileName in $fileCat */,");
	}
	
	function addLineToProjectSection($sectionName, $line)
	{
		if (!array_key_exists($sectionName, $this->_projectSections))
			$this->_projectSections[$sectionName] = "";
		else
			$this->_projectSections[$sectionName] .= "\n";
		
		$this->_projectSections[$sectionName] .= $line;
	}
	
	function writeProjectFile()
	{
		$projectFilePath = 'KalturaClient/KalturaClient.xcodeproj/project.pbxproj';
		$projectFileData = file_get_contents($this->_sourcePath . "/" . $projectFilePath);
		foreach ($this->_projectSections as $sectionName => $sectionData)
		{
			$projectFileData = str_replace($sectionName, $sectionData, $projectFileData);
		}
		$this->addFile($projectFilePath, $projectFileData, false);
	}

	// main
 	function generate() 
	{
		parent::generate();
	
		$xpath = new DOMXPath($this->_doc);
				
		$enumNodes = $xpath->query("/xml/enums/enum");
		$classNodes = $xpath->query("/xml/classes/class");
		$serviceNodes = $xpath->query("/xml/services/service");
		$this->writePlugin('', $enumNodes, $classNodes, $serviceNodes, $serviceNodes);
		
		// plugins
		$pluginNodes = $xpath->query("/xml/plugins/plugin");
		foreach($pluginNodes as $pluginNode)
		{
			$pluginName = $pluginNode->getAttribute("name");
			$enumNodes = $xpath->query("/xml/enums/enum[@plugin = '$pluginName']");
			$classNodes = $xpath->query("/xml/classes/class[@plugin = '$pluginName']");
			$serviceNodes = $xpath->query("/xml/services/service[@plugin = '$pluginName']");
			$serviceNamesNodes = $xpath->query("/xml/plugins/plugin[@name = '$pluginName']/pluginService");
			$this->writePlugin($pluginName, $enumNodes, $classNodes, $serviceNodes, $serviceNamesNodes);
		}
		
		$this->writeProjectFile();
	}		
}
