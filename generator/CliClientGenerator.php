<?php
class CliClientGenerator extends ClientGeneratorFromXml
{
	/**
	 * @var DOMDocument
	 */
	protected $_doc = null;
	
	function __construct($xmlPath, Zend_Config $config, $sourcePath = "sources/cli")
	{
		parent::__construct($xmlPath, $sourcePath, $config);
		$this->_doc = new KDOMDocument();
		$this->_doc->load($this->_xmlFile);
	}
	
	function getSingleLineCommentMarker()
	{
		return '';
	}
	
	function getServiceIds()
	{
		$xpath = new DOMXPath($this->_doc);
		$serviceNodes = $xpath->query("/xml/services/service");
		$result = array();
		foreach($serviceNodes as $serviceNode)
		{
			$result[] = $serviceNode->getAttribute("id");
		}
		return $result;
	}
	
	function getActionNames($serviceName)
	{
		$xpath = new DOMXPath($this->_doc);
		$actionNodes = $xpath->query("/xml/services/service[@id = '{$serviceName}']/action");
		$result = array();
		foreach($actionNodes as $actionNode)
		{
			$result[] = $actionNode->getAttribute("name");
		}
		return $result;
	}
	
	function getNodeAttributes($node, $attributes)
	{
		$result = array();
		foreach ($attributes as $attribute)
		{
			if ($node->hasAttribute($attribute))
				$result[$attribute] = $node->getAttribute($attribute);
		}
		return $result;
	}
	
	function getActionParameters($serviceName, $actionName)
	{
		$xpath = new DOMXPath($this->_doc);
		$paramNodes = $xpath->query("/xml/services/service[@id = '{$serviceName}']/action[@name = '{$actionName}']/param");
		$result = array();
		foreach($paramNodes as $paramNode)
		{
			$curParam = $this->getNodeAttributes($paramNode, array('name', 'type', 'enumType', 'arrayType'));

			$result[$curParam['name']] = $curParam;
		}
		return $result;
	}
	
	function getEnumValueList($enumType)
	{
		$xpath = new DOMXPath($this->_doc);
		$constNodes = $xpath->query("/xml/enums/enum[@name = '{$enumType}']/const");
		$result = array();
		foreach($constNodes as $constNode)
		{
			$result[] = $this->getNodeAttributes($constNode, array('name', 'value'));
		}
		return $result;
	}
	
	function getDerivedTypesList($typeName)
	{
		$result = array($typeName);
		$xpath = new DOMXPath($this->_doc);
		$subClassNodes = $xpath->query("/xml/classes/class[@base = '{$typeName}']");
		foreach ($subClassNodes as $subClassNode)
		{
			$result = array_merge($result, $this->getDerivedTypesList($subClassNode->getAttribute('name')));
		}
		return $result;
	}
	
	function getBaseClassNames($typeName)
	{
		$result = array();
		for (;;)
		{
			$xpath = new DOMXPath($this->_doc);
			$classNodes = $xpath->query("/xml/classes/class[@name = '{$typeName}']");
			if (!$classNodes->length)
				break;
			$classNode = $classNodes->item(0);
			$result[] = $classNode->getAttribute('name');
			if (!$classNode->hasAttribute('base'))
				break;
			$typeName = $classNode->getAttribute('base');
		}
		return $result;
	}
	
	function getXpathBaseClassOf($typeName)
	{
		$baseClasses = $this->getBaseClassNames($typeName);
		$attributeConds = array();
		foreach ($baseClasses as $baseClass)
			$attributeConds[] = "@name = '{$baseClass}'";
		return "/xml/classes/class[" . implode(' or ', $attributeConds). "]";
	}
	
	function getObjectPropertiesList($typeName)
	{
		$xpath = new DOMXPath($this->_doc);
		$propertyNodes = $xpath->query($this->getXpathBaseClassOf($typeName) . "/property");
		$result = array();
		foreach ($propertyNodes as $propertyNode)
		{
			$curParam = $this->getNodeAttributes($propertyNode, array('name', 'type', 'enumType', 'arrayType'));
			$result[$curParam['name']] = $curParam;
		}
		return $result;
	}
	
	function generate() 	
	{
		parent::generate();
	
		// services / actions / parameters
		$serviceIds = $this->getServiceIds();
		$this->addFile('services/services.map', serialize($serviceIds), false);
		
		foreach ($serviceIds as $serviceId)
		{
			$actionNames = $this->getActionNames($serviceId);
			$this->addFile("services/{$serviceId}.service", serialize($actionNames), false);
			
			foreach ($actionNames as $actionName)
			{
				$parameters = $this->getActionParameters($serviceId, $actionName);
				$this->addFile("actions/{$serviceId}/{$actionName}.action", serialize($parameters), false);
			}
		}
		
		// enums
		$xpath = new DOMXPath($this->_doc);
		$enumNodes = $xpath->query("/xml/enums/enum");
		foreach ($enumNodes as $enumNode)
		{
			$enumName = $enumNode->getAttribute('name');
			$values = $this->getEnumValueList($enumName);
			$this->addFile("enums/{$enumName}.enum", serialize($values), false);
		}
		
		// objects
		$xpath = new DOMXPath($this->_doc);
		$objectNodes = $xpath->query("/xml/classes/class");
		foreach ($objectNodes as $objectNode)
		{
			$objectName = $objectNode->getAttribute('name');
			$properties = $this->getObjectPropertiesList($objectName);
			$derivedTypes = $this->getDerivedTypesList($objectName);
			$objectDetails = array(
					'properties' => $properties, 
					'derivedTypes' => $derivedTypes);
			$this->addFile("objects/{$objectName}.object", serialize($objectDetails), false);
		}
	}
}
