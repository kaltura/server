<?php
class BpmnClientGenerator extends ClientGeneratorFromXml
{
	/**
	 * @var DOMDocument
	 */
	protected $_doc = null;
	
	/**
	 * @var string
	 */
	protected $template = null;
	
	function BpmnClientGenerator($xmlPath, $sourcePath = null)
	{
		if(!$sourcePath)
			$sourcePath = realpath("sources/bpmn");
		
		parent::ClientGeneratorFromXml($xmlPath, $sourcePath);
		
		$this->template = file_get_contents("$sourcePath/action.template.bpmn");
		$this->excludeSourcePaths[] = 'action.template.bpmn';
		
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
		
		$serviceNodes = $xpath->query("/xml/services/service");
		foreach($serviceNodes as $serviceNode)
		{
			$this->writeService($serviceNode);
		}
	}
	
	function writeService(DOMElement $serviceNode, $serviceName = null, $serviceId = null, $actionPrefix = "", $extends = "KalturaServiceBase")
	{
		$serviceName = $serviceNode->getAttribute('name');
		$actionNodes = $serviceNode->getElementsByTagName("action");
		foreach($actionNodes as $actionNode)
		{
			$this->writeAction($serviceName, $actionNode, $actionPrefix);
		}
	}
	
	protected function replaceReservedWords($name)
	{
		switch($name)
		{
			case "goto":
				return "{$name}_";
			
			default:
				return $name;
		}
	}
	
	function writeAction($serviceName, DOMElement $actionNode, $actionPrefix = "")
	{
		$action = $actionNode->getAttribute("name");
		$method = $this->replaceReservedWords($action);
		$serviceUName = ucfirst($serviceName);
		
		$bpmn = new DOMDocument('1.0', 'UTF-8');
		$bpmn->loadXML($this->template);
		$xpath = new DOMXPath($bpmn);
		
		$processNodes = $xpath->query("/*[local-name()='definitions']/*[local-name()='process']");
		$processNode = $processNodes->item(0);
		$processNode->setAttribute('id', "kaltura-$serviceName-$action");
		$processNode->setAttribute('name', "$serviceUName.$action");
		
		$signature = array();
		$paramNodes = $actionNode->getElementsByTagName('param');
		foreach($paramNodes as $paramNode)
		{
			$paramType = $paramNode->getAttribute('type');
			$paramName = $paramNode->getAttribute('name');
			$signature[] = $paramName;
		
			switch ($paramType) 
			{
				case 'bool' :
					$paramType = 'boolean';
		
				case 'float' :
					$paramType = 'double';
		
				case 'bigint' :
					$paramType = 'long';
					
				case 'int' :
					$paramType = 'int';
		
				case 'string' :
				case 'array' :		
				case 'file' :
				default :
					$paramType = 'string';
			}
			
			$dataObject = $bpmn->createElement('dataObject');
			$dataObject->setAttribute('id', $paramName);
			$dataObject->setAttribute('name', $paramName);
			$dataObject->setAttribute('itemSubjectRef', "xsd:$paramType");
			$processNode->appendChild($dataObject);
		}
		$signature = implode(', ', $signature);
		
		$script = "
var service = client.get{$serviceUName}Service();
var response = service.{$method}($signature);
execution.setVariable(\"response\", response);
			";
		
		$scriptTaskNodes = $xpath->query("*[local-name()='scriptTask']", $processNode);
		$scriptTaskNode = $scriptTaskNodes->item(0);
		$scriptTaskNode->setAttribute('name', "$serviceUName.$action");
		
		$scriptNodes = $xpath->query("*[local-name()='script']", $scriptTaskNode);
		$scriptNode = $scriptNodes->item(0);
		$scriptNode->nodeValue = $script;
		
		$filename = "kaltura.$serviceName.$action.bpmn";
		$this->addFile($filename, $bpmn->saveXML(), false);
	}
}
