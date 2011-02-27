<?php
/** 
 * @package infra
 * @subpackage utils
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
	public static function compareElement(DOMNode $from, DOMNode $to, $xPath = '', $level = 1)
	{
		$toName = $to->getAttribute('name');
		$fromName = $from->getAttribute('name');
		KalturaLog::debug("Compare elements [$fromName] [$toName]");
		
		$xPath .= "/*[local-name()='$fromName']";
			
		if($from->getAttribute('id') != $to->getAttribute('id'))
		{
			KalturaLog::debug("Elements ids are different [" . $from->getAttribute('id') . "] [" . $to->getAttribute('id') . "]");
			return '';
		}
			
		if(!$from->hasAttribute('id') && $fromName != $toName)
		{
			KalturaLog::debug("Elements names are different [$fromName] [$toName]");
			return '';
		}
			
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
		$xsl = self::compareNode($from, $to, $xPath, $level);
		if(is_bool($xsl))
		{
			if(!$xsl)
				return false;
				
			$xsl = '<xsl:value-of select="' . $xPath . '"/>';
		}
		else 
		{
			$isIdentical = false;
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
			' . $tabs . '<xsl:element name="' . $toName . '">' . $to->getAttribute('default') . '</xsl:element>';
					
			$isIdentical = false;	
		}
		
		if($isIdentical)
			return true;
			
		$xsl = '
		' . $tabs . '<xsl:element name="' . $toName . '">' . $xsl . '</xsl:element>';
		
		return $xsl;
	}
	
	public static function getElementById(DOMDocument $doc, $id)
	{
		$xpath = new DOMXpath($doc);

		$path = "//*[@id='$id']";
		KalturaLog::debug("Query xpath [$path]");
		$elements = $xpath->query($path);
		if(is_null($elements))
			return null;
			
		foreach ($elements as $element)
			return $element;
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
		
		$fromChildren = $from->childNodes;
		$toChildren = $to->childNodes;
		
		$fromChildrenArr = array();
		foreach($fromChildren as $index => $child)
		{
			if($child->nodeType == XML_COMMENT_NODE || $child->nodeType == XML_TEXT_NODE)
				continue;
				
			$childName = strtolower($child->localName);
			if($childName == 'annotation')
				continue;
				
			$fromChildrenArr[] = $child;
		}
		KalturaLog::debug("From nodes [" . count($fromChildrenArr) . "]");
		
		$toChildrenArr = array();
		foreach($toChildren as $index => $child)
		{
			if($child->nodeType == XML_COMMENT_NODE || $child->nodeType == XML_TEXT_NODE)
				continue;
				
			$childName = strtolower($child->localName);
			if($childName == 'annotation')
				continue;
				
			$toChildrenArr[] = $child;
		}
		KalturaLog::debug("To nodes [" . count($toChildrenArr) . "]");
		
		if(count($toChildrenArr) != count($fromChildrenArr))
		{
			KalturaLog::debug("From and To nodes count are different");
			foreach($toChildrenArr as $toChild)
			{
				$toChildName = strtolower($toChild->localName);
				
				if($toChildName != 'element' && $toChildName != 'attribute')
					continue;
					
				$elementName = $toChild->getAttribute('name');
				
				if($toChild->hasAttribute('id'))
				{
					$id = $toChild->getAttribute('id');
					$fromDoc = $from->ownerDocument;
					$fromChild = self::getElementById($fromDoc, $id);
					if($fromChild)
					{
						$fromElementName = $fromChild->getAttribute('name');
						if($fromElementName == $elementName)
						{
							$xsl .= '
			' . $tabs . '<xsl:copy-of select="' . $xPath . '/*[local-name()=\'' . $elementName . '\']"/>';
						}
						else
						{
							$isIdentical = false;
							$xsl .= '
			' . $tabs . '<xsl:element name="' . $elementName . '">
			' . $tabs . '	<xsl:copy-of select="' . $xPath . '/*[local-name()=\'' . $elementName . '\']"/>
			' . $tabs . '</xsl:element>';
						}
						continue;
					}
				}
				
				$fromChildFound = false;
				foreach($fromChildrenArr as $fromChild)
				{
					$fromChildName = strtolower($fromChild->localName);
					if($fromChildName != $toChildName)
						continue;
						
					if($fromChild->getAttribute('name') != $toChild->getAttribute('name'))
						continue;
						
					$fromChildFound = $fromChild;
				}
				
				if($fromChildFound)
				{
					$xsl .= '
			' . $tabs . '<xsl:copy-of select="' . $xPath . '/*[local-name()=\'' . $elementName . '\']"/>';
					continue;
				}
				
				if($toChild->hasAttribute('minOccurs') && $toChild->getAttribute('minOccurs') > 0)
				{
					if(!$toChild->hasAttribute('default'))
						throw new kXsdException(kXsdException::CAN_NOT_ADD_REQUIRED_ELEMENT, $toChild->hasAttribute('minOccurs'), $xPath);
					
					$isIdentical = false;
						
						$xsl .= '
			' . $tabs . '<xsl:element name="' . $elementName . '">' . $toChild->getAttribute('default') . '</xsl:element>';	
				}
			}
			
			foreach($fromChildrenArr as $fromChild)
			{
				$fromChildName = strtolower($fromChild->localName);
				
				if($fromChildName != 'element' && $fromChildName != 'attribute')
					continue;
					
				if($fromChild->hasAttribute('id'))
				{
					$id = $fromChild->getAttribute('id');
					$toDoc = $to->ownerDocument;
					$toChild = self::getElementById($toDoc, $id);
					if(!$toChild)
					{
						$isIdentical = false;
						continue;
					}
				}
				
				$toChildFound = false;
				foreach($toChildrenArr as $toChild)
				{
					$toChildName = strtolower($toChild->localName);
					if($toChildName != $fromChildName)
						continue;
						
					if($toChild->getAttribute('name') != $fromChild->getAttribute('name'))
						continue;
						
					$toChildFound = $toChild;
				}
				
				if(!$fromChildFound)
				{
					$isIdentical = false;
					continue;
				}
			}
		}
		else
		{
			KalturaLog::debug("From and To nodes count are the same");
			foreach($toChildrenArr as $index => $toChild)
			{
				$fromChild = $fromChildrenArr[$index];
	
				$toChildName = strtolower($toChild->localName);
				$fromChildName = strtolower($fromChild->localName);
				KalturaLog::debug("Compare child nodes [$toChildName] [$fromChildName]");
		
				if($toChildName != $fromChildName)
					throw new kXsdException(kXsdException::CAN_NOT_CHANGE_NODE, $fromChildName, $toChildName, $xPath);
			
				if($toChildName == 'element')
				{
					if($toChild->hasAttribute('id'))
					{
						$toChildId = $toChild->getAttribute('id');
						$fromDoc = $from->ownerDocument;
						$tmpFromChild = self::getElementById($fromDoc, $toChildId);
						if($tmpFromChild)
						{
							KalturaLog::debug("Found element by id [$toChildId]");
							if($fromChild !== $tmpFromChild)
								$isIdentical = false;
							$fromChild = $tmpFromChild;
						}
						else
						{
							KalturaLog::debug("Coudnt find element by id [$toChildId]");
						}
					}
					
					$childXsl = self::compareElement($fromChild, $toChild, $xPath, $level);
					if(is_bool($childXsl))
					{
						if(!$childXsl)
							return false;
						
						$elementName = $fromChild->getAttribute('name');
						KalturaLog::debug("Element [$elementName] is identical");
						$xsl .= '
		' . $tabs . '<xsl:copy-of select="' . $xPath . '/*[local-name()=\'' . $elementName . '\']"/>';
					}
					else
					{
						$xsl .= $childXsl;
						$isIdentical = false;
						KalturaLog::debug("Elements [$toChildName] [$fromChildName] are different");
					}
					continue;
				}
				
				if($toChildName == 'attribute')
					throw new kXsdException(kXsdException::CAN_NOT_CHANGE_ATTRIBUTE, $xPath);
			
				$childXsl = self::compareNode($fromChild, $toChild, $xPath, $level);
				if(is_bool($childXsl))
				{
					if(!$childXsl)
						return false;
				}
				else 
				{
					$xsl .= $childXsl;
					$isIdentical = false;
					KalturaLog::debug("Nodes [$fromName] [$toName] are different");
				}
			}
		}
		
		if($isIdentical)
			return true;
		
		return $xsl;
	}
	
	/**
	 * @param string $fromXsd old xsd path
	 * @param string $toXsd new xsd path
	 * @return bool|string true if no change required, or, xsl text if transform required
	 * @throws kXsdException
	 */
	public static function compareXsd($fromXsd, $toXsd)
	{
		$from = new DOMDocument();
		$from->load($fromXsd);
		
		if(!$from || !$from->documentElement)
			return false;
			
		$to = new DOMDocument();
		$to->load($toXsd);
		
		if(!$to || !$to->documentElement)
			return false;
			
		$xsl = self::compareNode($from->documentElement, $to->documentElement);
	
		if(is_bool($xsl))
		{
			if($xsl)
				return true;
				
			return false;
		}
	
		$xsl = '<?xml version="1.0" encoding="ISO-8859-1"?>
	<xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
		<xsl:output method="xml" version="1.0" encoding="iso-8859-1" indent="yes"/>
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
		$from = new DOMDocument();
		$from->load($xmlPath);
		
		$xsl = new DOMDocument();
		$xsl->load($xslPath);
		
		$proc = new XSLTProcessor;
		$proc->importStyleSheet($xsl);
		
		$output = $proc->transformToXML($from);
		
		$to = new DOMDocument();
		$to->loadXML($output);
		if(!$to->schemaValidate($xsdPath))
			return false;
		
		return $output;
	}
	
	/**
	 * @param string $xml
	 * @param string $xsdPath
	 * @param string $xslPath
	 * @return bool:string false if failed, xml text if succeed
	 */
	public static function transformXmlData($xml, $xsdPath, $xslPath)
	{
		$from = new DOMDocument();
		$from->loadXML($xml);
		
		$xsl = new DOMDocument();
		$xsl->load($xslPath);
		
		$proc = new XSLTProcessor;
		$proc->importStyleSheet($xsl);
		
		$output = $proc->transformToXML($from);
		
		$to = new DOMDocument();
		$to->loadXML($output);
		if(!$to->schemaValidate($xsdPath))
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
	public static function findXpathsByAppInfo($xsdPath, $appInfoField, $appInfoValue)
	{
		$xsd = new DOMDocument();
		$xsd->load($xsdPath);
		
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
