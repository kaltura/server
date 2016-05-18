<?php
class TestmeGenerator extends ClientGeneratorFromXml
{
	function __construct($xmlPath, Zend_Config $config, $sourcePath = "sources/testme")
	{
		parent::__construct($xmlPath, $sourcePath, $config);
	}
	
	function generate()
	{
		parent::generate();
	
		$xpath = new DOMXPath($this->_doc);
		
		// classes
		$classNodes = $xpath->query("/xml/classes/class");
		foreach($classNodes as $classNode)
		{
			$this->writeClass($classNode);
		}
		
		
		$serviceNodes = $xpath->query("/xml/services/service");
		foreach($serviceNodes as $serviceNode)
		{
	    	$this->writeService($serviceNode);
		}
		
		$configurationNodes = $xpath->query("/xml/configurations/*");
	    $this->writeIndex($serviceNodes, $configurationNodes, 'index.html', true);
	    $this->writeIndex($serviceNodes, $configurationNodes, 'no-menu.html', false);
	}
	
	/**
	 * @param string $type
	 * @return array<DOMElement>
	 */
	function getExtendingClasses($type)
	{
		$subClasses = array();
		$xpath = new DOMXPath($this->_doc);
		$classNodes = $xpath->query("/xml/classes/class[@base='$type']");
		foreach($classNodes as $classNode)
		{
			$subType = $classNode->getAttribute("name");
			$subClasses[$subType] = $classNode;
			$subSubClasses = $this->getExtendingClasses($subType);
			foreach($subSubClasses as $subSubClass)
			{
				$subSubType = $subSubClass->getAttribute("name");
				$subClasses[$subSubType] = $subSubClass;
			}
		}
		
		return $subClasses;
	}
	
	function getClassJson(DOMElement $classNode, &$loaded = array())
	{
		$type = $classNode->getAttribute("name");
		if(in_array($type, $loaded))
			return array('type' => $type);
			
		$loaded[] = $type;
		
		$json = array(
			'type' => $type,
			'description' => $classNode->getAttribute('description'),
			'isAbstract' => ($classNode->getAttribute('abstract') == 1),
			'isSimpleType' => false,
			'isComplexType' => true,
			'isFile' => false,
			'isEnum' => false,
			'isStringEnum' => false,
			'isArray' => false,
			'isReadOnly' => false,
			'isInsertOnly' => false,
			'isWriteOnly' => false,
		);
		
		$json['properties'] = $this->getPropertiesJson($classNode, $loaded);
		
		return $json;
	}
	
	function getEnumConstantsJson(DOMElement $enumNode)
	{
		$json = array();

		$enumType = $enumNode->getAttribute("enumType");
		
		foreach($enumNode->childNodes as $constNode)
		{
			if ($constNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			$json[] = array(
				'type' => $enumType,
				'name' => $constNode->getAttribute("name"),
				'defaultValue' => $constNode->getAttribute("value"),
				'description' => $constNode->getAttribute("description"),
				'isSimpleType' => true,
			);
		}
		
		return $json;
	}
	
	function getPropertiesJson(DOMElement $classNode, &$loaded = array())
	{
		$json = array();

		$type = $classNode->getAttribute('name');
		if($classNode->getAttribute('base'))
		{
			$baseType = $classNode->getAttribute('base');
			$xpath = new DOMXPath($this->_doc);
			$baseNodes = $xpath->query("/xml/classes/class[@name='$baseType']");
			$baseNode = $baseNodes->item(0);
			if(!$baseNode)
				throw new Exception("Base object [$baseType] not found for type [$type]");
		
				$baseProperties = $this->getPropertiesJson($baseNode, $loaded);
				foreach($baseProperties as $baseProperty)
					$json[] = $baseProperty;
		}
		
		foreach($classNode->childNodes as $propertyNode)
		{
			if ($propertyNode->nodeType != XML_ELEMENT_NODE)
				continue;

			$type = $propertyNode->getAttribute("type");
			$propertyJson = array(
				'type' => $type,
				'name' => $propertyNode->getAttribute("name"),
				'isSimpleType' => $this->isSimpleType($type),
				'isComplexType' => $this->isComplexType($type),
				'isFile' => ($type == 'file'),
				'isEnum' => false,
				'isStringEnum' => false,
				'isArray' => false,
				'isReadOnly' => ($propertyNode->getAttribute("readOnly") == 1),
				'isInsertOnly' => ($propertyNode->getAttribute("insertOnly") == 1),
				'isWriteOnly' => ($propertyNode->getAttribute("writeOnly") == 1),
				'description' => $propertyNode->getAttribute("description"),
			);
			
			if($propertyNode->hasAttribute("arrayType"))
			{
				$propertyJson['isArray'] = true;
				
				$arrayType = $propertyNode->getAttribute("arrayType");
				$xpath = new DOMXPath($this->_doc);
				$classNodes = $xpath->query("/xml/classes/class[@name='$arrayType']");
				$classNode = $classNodes->item(0);
				if(!$classNode)
					throw new Exception("Class [$arrayType] not found");
				$propertyJson['arrayType'] = $this->getClassJson($classNode, $loaded);
			}
			
			if($propertyNode->hasAttribute("enumType"))
			{
				$propertyJson['isComplexType'] = true;
				$propertyJson['isEnum'] = true;
				$propertyJson['isStringEnum'] = ($type == 'string');
				
				$enumType = $propertyNode->getAttribute("enumType");
				$xpath = new DOMXPath($this->_doc);
				$enumNodes = $xpath->query("/xml/enums/enum[@name='$enumType']");
				$enumNode = $enumNodes->item(0);
				if(!$enumNode)
					throw new Exception("Enum [$enumType] not found");
				$propertyJson['constants'] = $this->getEnumConstantsJson($enumNode);
			}
									
			$json[] = $propertyJson;
		}
		
		return $json;
	}
	
	function writeClass(DOMElement $classNode)
	{
		$type = $classNode->getAttribute("name");
		if(!$this->shouldIncludeType($type))
			return;

		$subClasses = $this->getExtendingClasses($type);
		$json = array();
		foreach($subClasses as $subClassType => $subClass)
		{
			/* @var $subClass DOMElement */
			$json[] = $this->getClassJson($subClass);
		}

		$this->addFile("json/$type-subclasses.json", json_encode($json), false);
	}
	
	function writeService(DOMElement $serviceNode)
	{
		$serviceId = $serviceNode->getAttribute("id");
		if(!$this->shouldIncludeService($serviceId))
			return;
		
		$json = array();
		$actionNodes = $serviceNode->getElementsByTagName("action");
		foreach($actionNodes as $actionNode)
		{
            $this->writeAction($serviceId, $actionNode);

            $actionName = $actionNode->getAttribute("name");
		    $actionLabel = $actionName;
		    if ($actionNode->getAttribute("deprecated"))
		    	$actionLabel .= ' (deprecated)';
		    
            $json[] = array(
				'action' => $actionName,
				'name' => $actionName,
				'label' => $actionLabel,
            );
		}

		$this->addFile("json/$serviceId-actions.json", json_encode($json), false);
	}
	
	function writeAction($serviceId, DOMElement $actionNode)
	{
		$actionId = $actionNode->getAttribute('name');
		if(!$this->shouldIncludeAction($serviceId, $actionId))
			return;
		
		$json = array(
			'actionParams' => array(),
		    'description' => $actionNode->getAttribute('description')
		);

		$paramNodes = $actionNode->getElementsByTagName('param');
		foreach($paramNodes as $paramNode)
		{
			$paramType = $paramNode->getAttribute('type');
			$paramName = $paramNode->getAttribute('name');
			$enumType = $paramNode->hasAttribute('enumType');
			$isOptional = $paramNode->getAttribute('optional');
					
			if($enumType)
				$paramType = $enumType;
			
			$xpath = new DOMXPath($this->_doc);
			$classNodes = $xpath->query("/xml/classes/class[@name='$paramType']");
			$classNode = $classNodes->item(0);
			if($classNode)
			{
				$paramData = $this->getClassJson($classNode);
			}
			else 
			{
				$paramData = array(
					'type' => $paramType,
					'isSimpleType' => $this->isSimpleType($paramType),
					'isComplexType' => false,
					'isFile' => ($paramType == 'file'),
					'isEnum' => false,
					'isStringEnum' => false,
					'isArray' => false,
					'isAbstract' => false,
				);
			}

			$paramData['description'] = $paramNode->getAttribute('description');
			$paramData['name'] = $paramName;
			$paramData['isOptional'] = (bool) $isOptional;
			
			$json['actionParams'][] = $paramData;
		}
		
		$this->addFile("json/$serviceId-$actionId-action-info.json", json_encode($json), false);
	}
	
	function writeIndex(DOMNodeList $serviceNodes, DOMNodeList $configurationNodes, $fileName, $includeMenu)
	{
		$this->startNewTextBlock();
		
		$apiVersion = $this->_doc->documentElement->getAttribute('apiVersion');

		$this->appendLine('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
		$this->appendLine('<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">');
		$this->appendLine('<head>');
		$this->appendLine('	<title>Kaltura - Test Me Console</title>');
		$this->appendLine('	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />');
		$this->appendLine('	<link rel="stylesheet" type="text/css" href="css/main.css" />');
		$this->appendLine('	<link rel="stylesheet" type="text/css" href="css/code.example.css" />');
		$this->appendLine('	<link rel="stylesheet" type="text/css" href="css/http.spy.css" />');
		$this->appendLine('	<link rel="stylesheet" type="text/css" href="css/jquery.tooltip.css" />');
		$this->appendLine('	<script type="text/javascript" src="js/jquery-1.3.1.min.js"></script>');
		$this->appendLine('	<script type="text/javascript" src="js/jquery.tooltip.js"></script>');
		$this->appendLine('	<script type="text/javascript" src="js/jquery.scrollTo-min.js"></script>');
		$this->appendLine('	<script type="text/javascript" src="js/kCodeExample.js"></script>');
		$this->appendLine('	<script type="text/javascript" src="js/kField.js"></script>');
		$this->appendLine('	<script type="text/javascript" src="js/kDialog.js"></script>');
		$this->appendLine('	<script type="text/javascript" src="js/kTestMe.js"></script>');
		$this->appendLine('	<script type="text/javascript" src="js/ace-builds/src/ace.js"></script>');
		$this->appendLine('	<script type="text/javascript" src="js/kPrettify.js"></script>');
		$this->appendLine('	<script type="text/javascript">');
		
		foreach($serviceNodes as $serviceNode)
		{
			/* @var $serviceNode DOMElement */
			$serviceId = $serviceNode->getAttribute('id');
			$serviceName = $serviceNode->getAttribute('name');
			$pluginName = $serviceNode->getAttribute('plugin');
			$deprecated = $serviceNode->getAttribute('deprecated') ? 'true' : 'false';

			$this->appendLine("		kTestMe.registerService(\"$serviceId\", \"$serviceName\", \"$pluginName\", $deprecated);");
		}
		
		$this->appendLine('	</script>');
		$this->appendLine('	<script type="text/javascript">');
		$this->appendLine('		$(document).ready(function() {');
		$this->appendLine('			var myframe = document.getElementById("hiddenResponse");');
		$this->appendLine('');
		$this->appendLine('			if (myframe.attachEvent) {');
		$this->appendLine('	 			myframe.attachEvent("onload", function(){handleResponse();});');
		$this->appendLine('			} else {');
		$this->appendLine('				myframe.onload = function(){handleResponse();};');
		$this->appendLine('			}');
		$this->appendLine('		});');
		$this->appendLine('');
		$this->appendLine('		function handleResponse(){');
		$this->appendLine('			var myframe = document.getElementById("hiddenResponse");');
		$this->appendLine('			var doc = ( myframe.contentDocument || myframe.contentWindow.document);');
		$this->appendLine('');		
		$this->appendLine('			var formatSelector = document.getElementsByName("format");');
		$this->appendLine('			var format = "XML";');
		$this->appendLine('			if (formatSelector && formatSelector.length > 0){');
		$this->appendLine('				format = formatSelector[0].options[formatSelector[0].selectedIndex].text;');
		$this->appendLine('			}');
		$this->appendLine('			var text ="";');
		$this->appendLine('			if ( format == "JSON" &&');
		$this->appendLine('				typeof doc.body != "undefined" &&');
		$this->appendLine('				typeof doc.body.innerText != "undefined"&&');
		$this->appendLine('				(doc.body.innerText.indexOf("{" != -1) )){');
		$this->appendLine('				format = "json";');
		$this->appendLine('				text = indentJSON(doc.body.innerText, "\n", " ");');
		$this->appendLine('			} else if (format == "XML" &&');
		$this->appendLine('				typeof doc.firstChild != "undefined" )  {');
		$this->appendLine('				if ( doc.firstChild.localName == "xml"){');
		$this->appendLine('					var para = document.createElement("div");');
		$this->appendLine('					para.appendChild(doc.firstChild);');
		$this->appendLine('					text = para.innerHTML;');
		$this->appendLine('					text = indentXML(text);');
		$this->appendLine('					format = "xml";');
		$this->appendLine('				} else {');
		$this->appendLine('					var data = doc.getElementsByTagName("pre")[0];');
		$this->appendLine('					text = data.innerHTML;');
		$this->appendLine('				}');
		$this->appendLine('			} else {');
		$this->appendLine('				text = doc;');
		$this->appendLine('				format = "txt";');
		$this->appendLine('			}');
		$this->appendLine('			document.getElementById("response").contentWindow.setAceEditorWithText(text, format);');
		$this->appendLine('			kTestMe.onResponse(text, format);');
		$this->appendLine('		}');
		$this->appendLine('');
		$this->appendLine('	</script>');
		$this->appendLine('</head>');
		
		if($includeMenu)
		{
			$this->appendLine('<body class="body-bg">');
			$this->appendLine('	<ul id="kmcSubMenu">');
			$this->appendLine('		<li class="active"><a href="#">Test Console</a></li>');
			$this->appendLine('		<li><a href="../testmeDoc/index.php">API Documentation</a></li>');
			$this->appendLine('		<li><a href="../xsdDoc/index.php">XML Schema</a></li>');
			$this->appendLine('		<li><a href="client-libs.php">API Client Libraries</a></li>');
			$this->appendLine('	</ul>');
		}
		else 
		{
			$this->appendLine('<body>');
		}
		
		$this->appendLine('	<div class="left">');
		$this->appendLine('		<form id="request" action="../" method="post" target="hiddenResponse" enctype="multipart/form-data">');
		$this->appendLine('			<div class="left-content">');
		$this->appendLine('				<div class="attr">');
		$this->appendLine('					<label for="history">History: </label>');
		$this->appendLine('					<select id="history">');
		$this->appendLine('						<option>Select request</option>');
		$this->appendLine('					</select>');
		$this->appendLine('				</div>');
		$this->appendLine('');
		$this->appendLine('				<div class="param">');
		$this->appendLine('					<label for="format">Format (int):</label>');
		$this->appendLine('					<select name="format">');
		$this->appendLine('						<option value="2">XML</option>');
		$this->appendLine('						<option value="1">JSON</option>');
		$this->appendLine('					</select>');
		$this->appendLine('				</div>');
		
		foreach($configurationNodes as $configurationNode)
		{
			foreach($configurationNode->childNodes as $configurationPropertyNode)
			{	
				if ($configurationPropertyNode->nodeType != XML_ELEMENT_NODE)
					continue;

				/* @var $configurationPropertyNode DOMElement */
					
				$title = $configurationPropertyNode->getAttribute('description');
				if(!$title)
					$title = $configurationPropertyNode->localName;
				
				$type = $configurationPropertyNode->getAttribute('type');
				
				$value = '';
				if($configurationPropertyNode->localName == 'apiVersion')
				{
					$value = $apiVersion;
				}
				elseif($configurationPropertyNode->localName == 'clientTag')
				{
					$value = 'testme';
				}
				elseif($configurationPropertyNode->localName == 'responseProfile')
				{
					continue; // TODO support responseProfile

				}
				$checked = '';
				if($value)
					$checked = 'checked="checked"';
	
				$this->appendLine('				<div class="param">');
				$this->appendLine("					<label for=\"{$configurationPropertyNode->localName}\">$title ($type):</label>");
				$this->appendLine("					<input type=\"text\" name=\"{$configurationPropertyNode->localName}\" size=\"30\" class=\"\" value=\"$value\" />");
				$this->appendLine("					<input id=\"chk-{$configurationPropertyNode->localName}\" type=\"checkbox\" $checked />");
				$this->appendLine('				</div>');
			}
		}
		
		$this->appendLine('				<div id="dvService">');
		$this->appendLine('					<div class="attr">');
		$this->appendLine('						<label for="service">Select service:</label>');
		$this->appendLine('						<select name="service">');
		$this->appendLine('							<option value="">Select service</option>');
		$this->appendLine('							<option value="multirequest">Multirequest</option>');

		foreach($serviceNodes as $serviceNode)
		{
			/* @var $serviceNode DOMElement */
			$serviceId = $serviceNode->getAttribute('id');
			$serviceName = $serviceNode->getAttribute('name');
			$pluginName = $serviceNode->getAttribute('plugin');
			$deprecated = $serviceNode->getAttribute('deprecated');

			if ($pluginName)
				$serviceName = "$pluginName.$serviceName";

			$serviceLabel = $serviceName;
			if ($deprecated)
				$serviceLabel .= ' (deprecated)';

			$this->appendLine("							<option value=\"$serviceId\" title=\"$serviceName\">$serviceLabel</option>");
		}
		
		$this->appendLine('						</select>');
		$this->appendLine('						<img src="images/help.png" class="service-help help" />');
		$this->appendLine('					</div>');
		$this->appendLine('					<div class="attr" style="display: none">');
		$this->appendLine('						<label for="action">Select action:</label>');
		$this->appendLine('						<select name="action"></select>');
		$this->appendLine('						<img src="images/help.png" class="action-help help" title="" />');
		$this->appendLine('					</div>');
		$this->appendLine('					<div class="attr" style="display: none">');
		$this->appendLine('						<input type="button" class="add-request-button button" value="Add Request" />');
		$this->appendLine('					</div>');
		$this->appendLine('');				
		$this->appendLine('					<div class="action-params"></div>');
		$this->appendLine('					<div class="objects-containter"></div>');
		$this->appendLine('				</div>');
		$this->appendLine('');				
		$this->appendLine('				<div>');
		$this->appendLine('					<button type="submit">Send</button>');
		$this->appendLine('				</div>');
		$this->appendLine('			</div>');
		$this->appendLine('		</form>');
		$this->appendLine('	</div>');
		$this->appendLine('	<div class="right">');
		$this->appendLine('		<iframe class="right-content" id="response" name="response" src="./testme.result.html" scrolling="no"></iframe>');
		$this->appendLine('		<iframe id="hiddenResponse" name="hiddenResponse" src=""></iframe>');
		$this->appendLine('	</div>');
		$this->appendLine('	<ul id="codeSubMenu">');
		$this->appendLine('		<li class="code-menu code-menu-php active"><a href="#" onclick="switchToPHP()">PHP</a></li>');
		$this->appendLine('		<li class="code-menu code-menu-java"><a href="#" onclick="switchToJava()">Java</a></li>');
		$this->appendLine('		<li class="code-menu code-menu-csharp"><a href="#" onclick="switchToCSharp()">C#</a></li>');
		$this->appendLine('		<li class="code-menu code-menu-python"><a href="#" onclick="switchToPython()">Python</a></li>');
		$this->appendLine('		<li class="code-menu code-menu-javascript"><a href="#" onclick="switchToJavascript()">Javascript</a></li>');
		$this->appendLine('		<li class="code-menu"><a class="code-menu-toggle" href="#" onclick="toggleCode()" id="codeToggle">Show Code Example</a></li>');
		$this->appendLine('	</ul>');
		$this->appendLine('	<div class="code" id="codeExample" style="display: none;">');
		$this->appendLine('		<div id="disclaimer"><p>Note: The auto-generated code is for reference purposes and orientation. A direct copy&amp;paste will not work on its own. Please make sure to review the sample and adapt to your own application code.</p></div>');
		$this->appendLine('		<div id="example"></div>');
		$this->appendLine('	</div>');
		$this->appendLine('		<div id="httpSpy" style="display: none;">');
		$this->appendLine('		<div id="httpSpyForm">');
		$this->appendLine('			<input type="file" id="fileHttpSpy" />');
		$this->appendLine('			<input type="button" id="parseHttpSpy" value="Parse"  />');
		$this->appendLine('		</div>');
		$this->appendLine('	</div>');
		$this->appendLine('</body>');
		$this->appendLine('</html>');
		
		$this->addFile($fileName, $this->getTextBlock(), false);
	}
}
