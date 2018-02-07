<?php
/**
 * @package server-infra
 * @subpackage content
 */

class kXsd
{
	/**
	 * @param DOMNode $from old node
	 * @param DOMNode $to new node
	 * @param string $xPath
	 * @param int $level indentation level
	 * @return bool|string true if no change required, or, xsl text if transform required
	 * @throws kXsdException
	 */
	public static function compareElement(DOMNode $from, DOMNode $to, $parentXPath = '', $level = 1)
	{
		$toName = $to->getAttribute('name');
		$fromName = $from->getAttribute('name');
		KalturaLog::debug("Compare elements [$fromName] [$toName]");
		
		$xPath = $parentXPath . "/*[local-name()='$fromName']";
						
		if($from->getAttribute('type') != $to->getAttribute('type'))
		{
			KalturaLog::debug("Elements types are different [" . $from->getAttribute('type') . "] [" . $to->getAttribute('type') . "]");
			throw new kXsdException(kXsdException::CAN_NOT_CHANGE_ELEMENT_TYPE, $from->getAttribute('type'), $to->getAttribute('type'), $xPath);
		}
			
		if($from->getAttribute('maxOccurs') > $to->getAttribute('maxOccurs'))
		{
			KalturaLog::debug("Elements max occurs reduced [" . $from->getAttribute('maxOccurs') . "] [" . $to->getAttribute('maxOccurs') . "]");
			throw new kXsdException(kXsdException::CAN_NOT_REDUCE_ELEMENT_MAX_OCCURS, $from->getAttribute('maxOccurs'), $to->getAttribute('maxOccurs'), $xPath);
		}
			
		$tabs = str_repeat("\t", $level);
		
		
		$isIdentical = true;
		
		$restriction = self::getNodeRestrictions($to, $from);
		if (strlen($restriction)){
			$isIdentical = false;
		}
		
		$xsl = self::compareNode($from, $to, $xPath, $level + 1);
		if($xsl === true)
		{
			if (strlen($restriction)){
				$xsl = '
	' . $tabs . '<xsl:for-each select="' . $parentXPath . '/*[local-name()=\'' . $fromName . '\']">
	' . $tabs . '	<xsl:choose>
	' . $tabs . '		<xsl:when test="' . $restriction . '">
	' . $tabs . '			<xsl:element name="' . $toName . '">
	' . $tabs . '				<xsl:value-of select="."/>
	' . $tabs . '			</xsl:element>
	' . $tabs . '		</xsl:when>
	' . $tabs . '	</xsl:choose>
	' . $tabs . '</xsl:for-each>';
			}
			else
			{
				$xsl = '
	' . $tabs . '<xsl:for-each select="' . $parentXPath . '/*[local-name()=\'' . $fromName . '\']">
	' . $tabs . '	<xsl:element name="' . $toName . '">
	' . $tabs . '		<xsl:value-of select="."/>
	' . $tabs . '	</xsl:element>
	' . $tabs . '</xsl:for-each>';
			}
		}
		else
		{
			$isIdentical = false;
			$xsl = '
		' . $tabs . '<xsl:element name="' . $toName . '">' . $xsl . '
		' . $tabs . '</xsl:element>';
		}
		
		if($fromName != $toName)
		{
			$isIdentical = false;
		}
		
		if(intval($to->getAttribute('minOccurs')) && $from->getAttribute('minOccurs') < $to->getAttribute('minOccurs'))
		{
			if(!$to->hasAttribute('default'))
			{
				KalturaLog::debug("Elements min occurs increased [" . $from->getAttribute('minOccurs') . "] [" . $to->getAttribute('minOccurs') . "]");
				throw new kXsdException(kXsdException::CAN_NOT_INCREASE_ELEMENT_MIN_OCCURS, $from->getAttribute('minOccurs'), $to->getAttribute('minOccurs'), $xPath);
			}
				
			for($i = $from->getAttribute('minOccurs'); $i < $to->getAttribute('minOccurs'); $i++)
					$xsl .= '
			' . $tabs . '<xsl:element name="' . $toName . '">' . $to->getAttribute('default') . '
			' . $tabs . '</xsl:element>';
					
			$isIdentical = false;
			KalturaLog::info("Node [$toName] minimum occurs changed from [" . $from->getAttribute('minOccurs') . "] to [" . $to->getAttribute('minOccurs') . "]");
		}
		
		if($isIdentical)
			return true;

		return $xsl;
	}
	
	public static function getElementById(DOMDocument $doc, $id)
	{
		$xpath = new DOMXPath($doc);

		$path = "//*[@id='$id']";
		KalturaLog::debug("Query xpath [$path]");
		$elements = $xpath->query($path);
		if(is_null($elements))
			return null;
			
		foreach ($elements as $element)
			return $element;
	}
	
	protected static function getMatchingElement($childrenArr, $element, &$index)
	{
		// try matching by id
		if ($element->getAttribute('id'))
		{
			$result = null;
			
			foreach($childrenArr as $curIndex => $curChild)
			{
				if(strtolower($curChild->localName) != strtolower($element->localName))
					continue;
					
				if(!$curChild->getAttribute('id') ||
					$curChild->getAttribute('id') != $element->getAttribute('id'))
					continue;
					
				if ($result)
					throw new kXsdException(kXsdException::MATCHED_MORE_THAN_ONE_NODE, $element->getAttribute('id'));
					
				$index = $curIndex;
				$result = $curChild;
			}
			
			return $result;
		}
		
		// try matching by name
		if ($element->getAttribute('name'))
		{
			$result = null;
			
			foreach($childrenArr as $curIndex => $curChild)
			{
				if(strtolower($curChild->localName) != strtolower($element->localName))
					continue;
					
				if ($curChild->getAttribute('id'))
					continue;
					
				if(!$curChild->getAttribute('name') ||
					$curChild->getAttribute('name') != $element->getAttribute('name'))
					continue;
					
				if ($result)
					throw new kXsdException(kXsdException::MATCHED_MORE_THAN_ONE_NODE, $element->getAttribute('name'));
					
				$index = $curIndex;
				$result = $curChild;
			}

			return $result;
		}
		
		// try matching by local name only
		foreach($childrenArr as $curIndex => $curChild)
		{
			if(strtolower($curChild->localName) != strtolower($element->localName))
				continue;

			if ($curChild->getAttribute('id') || $curChild->getAttribute('name'))
				continue;
				
			$index = $curIndex;
			return $curChild;
		}
		
		return null;
	}
	
	
	/**
	 * @param DOMNode $from old node
	 * @param DOMNode $to new node
	 * @param string $xPath
	 * @param int $level indentation level
	 * @return bool|string true if no change required, or, xsl text if transform required
	 * @throws kXsdException
	 */
	public static function compareNode(DOMNode $from, DOMNode $to, $xPath = '', $level = 1)
	{
		$toName = strtolower($to->localName);
		$fromName = strtolower($from->localName);
		KalturaLog::debug("Compare nodes [$toName] [$fromName]");
		if($toName != $fromName)
			throw new kXsdException(kXsdException::CAN_NOT_CHANGE_NODE, $fromName, $toName, $xPath);
		
		$xsl = '';
		
		$tabs = str_repeat("\t", $level);
		
		$isIdentical = true;
		
		// build an array of from children
		$fromChildren = $from->childNodes;
		$fromChildrenArr = array();
		if($fromChildren)
		{
			foreach($fromChildren as $index => $child)
			{
				if($child->nodeType == XML_COMMENT_NODE || $child->nodeType == XML_TEXT_NODE)
					continue;
					
				$childName = strtolower($child->localName);
				if($childName == 'annotation')
					continue;
					
				$fromChildrenArr[] = $child;
			}
		}
		KalturaLog::debug("From nodes [" . count($fromChildrenArr) . "]");
		
		// build an array of to children
		$toChildren = $to->childNodes;
		$toChildrenArr = array();
		if($toChildren)
		{
			foreach($toChildren as $index => $child)
			{
				if($child->nodeType == XML_COMMENT_NODE || $child->nodeType == XML_TEXT_NODE)
					continue;
					
				$childName = strtolower($child->localName);
				if($childName == 'annotation')
					continue;
					
				$toChildrenArr[] = $child;
			}
		}
		KalturaLog::debug("To nodes [" . count($toChildrenArr) . "]");
		
		// detect new nodes + order change
		$lastFromIndex = null;
		foreach($toChildrenArr as $toChild)
		{
			$toChildName = strtolower($toChild->localName);
			
			if($toChildName == 'attribute')
				throw new kXsdException(kXsdException::CAN_NOT_CHANGE_ATTRIBUTE, $xPath);
			
			$fromIndex = null;
			$fromChild = self::getMatchingElement($fromChildrenArr, $toChild, $fromIndex);

			
			if($fromChild)
			{
				if($toChildName != 'element')
				{
					$childXsl = self::compareNode($fromChild, $toChild, $xPath, $level + 1);
					if($childXsl === true)
						continue;
						
					$xsl .= $childXsl;
					$isIdentical = false;
					KalturaLog::info("Nodes [$fromName] [$toName] are different");
					continue;
				}
				$fromChildName = strtolower($toChild->localName);
				
				$toElementName = $toChild->getAttribute('name');
				$fromElementName = $fromChild->getAttribute('name');
				
				if (!is_null($lastFromIndex) && $fromIndex < $lastFromIndex)
				{
					KalturaLog::info("Node id=[". $toChild->getAttribute('id') ."] name=[$toElementName] index changed from original schema");
					$isIdentical = false;
				}
				
				$lastFromIndex = $fromIndex;
				

				$childXsl = self::compareElement($fromChild, $toChild, $xPath, $level);
				if($childXsl === true)
				{
					KalturaLog::debug("Element [$fromElementName] is identical");

					$xsl .= '
	' . $tabs . '<xsl:copy-of select="' . $xPath  .'/*[local-name()=\'' . $fromElementName . '\']"/>';
				}
				else
				{
					$xsl .= $childXsl;
					$isIdentical = false;
					KalturaLog::info("Elements [$toChildName] [$fromChildName] are different");
				}

				continue;
			}
			else
			{
				KalturaLog::debug("Node [". $toChild->getAttribute('id') ."] is new");
				
				if($toChild->hasAttribute('minOccurs') && $toChild->getAttribute('minOccurs') > 0)
				{
					if(!$toChild->hasAttribute('default'))
						throw new kXsdException(kXsdException::CAN_NOT_ADD_REQUIRED_ELEMENT, $toChild->hasAttribute('minOccurs'), $xPath);
					
					$isIdentical = false;
					KalturaLog::info("Node [" . $toChild->getAttribute('name') . "] added with minimum occurs [" . $toChild->getAttribute('minOccurs') . "]");
						$xsl .= '
			' . $tabs . '<xsl:element name="' . $toElementName . '">' . $toChild->getAttribute('default') . '</xsl:element>';
				}
			}
		}

		// detect deleted nodes
		$lastToIndex = null;
		foreach($fromChildrenArr as $fromChild)
		{
			$fromChildName = strtolower($fromChild->localName);
			
			if($fromChildName != 'element' && $fromChildName != 'attribute')
				continue;

			$toIndex = null;
			$toChild = self::getMatchingElement($toChildrenArr, $fromChild, $toIndex);
			
			if (!$toChild)
			{
				$isIdentical = false;
				KalturaLog::info("Node [". $fromChild->getAttribute('id') ."] deleted");
				continue;
			}
		}
		
		
		if($isIdentical)
			return true;
		
		return $xsl;
	}
	
	/**
	 * @param DOMNode $toNode
	 * @param DOMNode $fromNode
	 * @return string xsl - the restriction for the toNode if changed from the fromNode
	 */
	public static function getNodeRestrictions(DOMNode $toNode, DOMNode $fromNode)
	{
		
		$xpathTo = new DOMXPath($toNode->ownerDocument);
		
		$xpathTo->registerNamespace("xsd", "http://www.w3.org/2001/XMLSchema");
		$enumerations = $xpathTo->query("xsd:simpleType/xsd:restriction/xsd:enumeration", $toNode);

		$enumerationsValueToNode = array();
		
		foreach($enumerations as $enumeration)
		{
			$enumerationsValueToNode[] = htmlspecialchars($enumeration->getAttribute('value'),ENT_QUOTES,'UTF-8');
		}
		
		
		$xpathFrom = new DOMXPath($fromNode->ownerDocument);
		$xpathFrom->registerNamespace("xsd", "http://www.w3.org/2001/XMLSchema");
		$enumerations = $xpathFrom->query("xsd:simpleType/xsd:restriction/xsd:enumeration", $fromNode);

		$enumerationsValueFromNode = array();
		
		foreach($enumerations as $enumeration)
		{
			$enumerationsValueFromNode[] = htmlspecialchars($enumeration->getAttribute('value'),ENT_QUOTES,'UTF-8');
		}
		
		if (!count(array_diff($enumerationsValueFromNode, $enumerationsValueToNode)))
			return '';
		
		if (!count($enumerationsValueToNode))
			return '';
					
		return '.=\'' . implode('\' or .=\'', $enumerationsValueToNode) . '\'';
	}
	
	/**
	 * @param string $fromXsd old xsd
	 * @param string $toXsd new xsd
	 * @return bool|string true if no change required, or, xsl text if transform required
	 * @throws kXsdException
	 */
	public static function compareXsd($fromXsd, $toXsd)
	{
		$from = new KDOMDocument();
		$from->loadXML($fromXsd);
		
		if(!$from || !$from->documentElement)
			throw new kXsdException(kXsdException::INVALID_XSD_FILE, $fromXsd);
			
		$to = new KDOMDocument();
		$to->loadXML($toXsd);
		
		if(!$to || !$to->documentElement)
			throw new kXsdException(kXsdException::INVALID_XSD_FILE, $toXsd);
			
		$xsl = self::compareNode($from->documentElement, $to->documentElement);
	
		if($xsl === true)
			return true;
	
		$xsl = '<?xml version="1.0" encoding="ISO-8859-1"?>
	<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
		<xsl:output method="xml" version="1.0" omit-xml-declaration="yes" indent="yes"/>
		<xsl:strip-space elements="*" />
		<xsl:template match="/">' . $xsl . '
		</xsl:template>
	</xsl:transform>
	';
		
		return $xsl;
	}
	
	/**
	 * @param string $xmlPath
	 * @param string $xsdPath
	 * @param string $xslPath
	 * @return bool:string false if failed, xml text if succeed
	 */
	public static function transformXmlFile($xmlPath, $xsdPath, $xslPath)
	{
		$from = new KDOMDocument();
		$from->load($xmlPath);
		
		$xsl = new KDOMDocument();
		$xsl->load($xslPath);
		
		$proc = new XSLTProcessor;
		$proc->importStyleSheet($xsl);
		
		$output = $proc->transformToXML($from);
		
		$to = new KDOMDocument();
		$to->loadXML($output);
		if(!$to->schemaValidate($xsdPath))
			return false;
		
		return $output;
	}
	
	/**
	 * @param string $xml
	 * @param KalturaFileContainer $xsdFile
	 * @param string $xslStr
	 * @return bool:string false if failed, xml text if succeed
	 */
	public static function transformXmlData($xml, $xsdFile, $xslStr)
	{
		$from = new KDOMDocument();
		$from->loadXML($xml);
		
		$xsl = new KDOMDocument();
		$xsl->loadXML($xslStr);
		
		$proc = new XSLTProcessor;
		$proc->importStyleSheet($xsl);
		
		$output = $proc->transformToXML($from);
		
		$to = new KDOMDocument();
		$to->loadXML($output);
		if(!$to->schemaValidate($xsdFile->filePath, $xsdFile->encryptionKey, KBatchBase::getIV()))
			return false;
		
		return $output;
	}
	
	/**
	 * @param DOMNode $node
	 * @return string of xPath
	 */
	public static function getXsdXpath(DOMNode $node)
	{
		if(is_null($node->parentNode))
			return '';
			
		if($node->localName == 'element')
			return self::getXsdXpath($node->parentNode) . '/*[local-name()=\'' . $node->getAttribute('name') . '\']';
			
		return self::getXsdXpath($node->parentNode);
	}
	
	/**
	 * @param DOMNode $node
	 * @return string of type
	 */
	public static function getXsdNodeType(DOMNode $node)
	{
		if($node->hasAttribute('type'))
			return $node->getAttribute('type');
			
		// type not found
		if($node->localName == 'element' || $node->localName == 'attribute')
		{
			$restrictions = $node->getElementsByTagName('restriction');
			foreach($restrictions as $restriction)
			{
				if($restriction->hasAttribute('base'))
					return $restriction->getAttribute('base');
			}
					
			return null;
		}
			
		// nowhere else to search
		if(is_null($node->parentNode))
			return null;
			
		return self::getXsdNodeType($node->parentNode);
	}
	
	/**
	 * @param DOMNode $node
	 * @return string of type
	 */
	public static function getXsdElementName(DOMNode $node)
	{
		if($node->localName == 'element' || $node->localName == 'attribute')
		{
			if($node->hasAttribute('name'))
				return $node->getAttribute('name');
					
			return null;
		}
			
		// nowhere else to search
		if(is_null($node->parentNode))
			return null;
			
		return self::getXsdElementName($node->parentNode);
	}
	
	/**
	 * @param string $xsdPath
	 * @param string $appInfoField
	 * @param string $appInfoValue
	 * @return array of xPaths
	 */
	public static function findXpathsByAppInfo($xsdPath, $appInfoField, $appInfoValue, $isPath = true)
	{
		$xsd = new KDOMDocument();
		
		if ($isPath){
			$xsd->load($xsdPath);
		}else{
			$xsd->loadXML($xsdPath);
		}
		
		
		$xPaths = array();
		
		$appInfos = $xsd->getElementsByTagName('appinfo');
		foreach($appInfos as $appInfo)
		{
			$fields = $appInfo->getElementsByTagName($appInfoField);
			$found = false;
			foreach($fields as $field)
				if($field->nodeValue == $appInfoValue)
					$found = true;
					
			if(!$found)
				continue;
				
			$name = self::getXsdElementName($appInfo);
			$type = self::getXsdNodeType($appInfo);
			$xPath = self::getXsdXpath($appInfo);
			
			$data = array();
			if($name)
				$data['name'] = $name;
			if($type)
				$data['type'] = $type;
				
			foreach($appInfo->childNodes as $childNode)
			{
				if($childNode->nodeType == XML_TEXT_NODE || $childNode->nodeType == XML_COMMENT_NODE)
					continue;
					
				$data[$childNode->localName] = $childNode->nodeValue;
			}
				
			$xPaths[$xPath] = $data;
		}
		
		return $xPaths;
	}
}
