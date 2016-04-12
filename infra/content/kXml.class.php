<?php

/** 
 * @package server-infra
 * @subpackage content
 */
class XSLTErrorCollector
{
	public $errors = array();
	
	public function __construct()
	{
		set_error_handler(array($this, "errorHandler"));
	}

	public function restoreErrorHandler()
	{
		restore_error_handler();
	}
	
	public function errorHandler($errNo, $errStr, $errFile, $errLine)
	{
		$startPos = strpos($errStr, ': ');
		if ($startPos !== false)
			$errStr = substr($errStr, $startPos + 2);
		$this->errors[] = $errStr;
	}
}

function xml_load_for_xslt($xmlStr)
{
	$dom = DOMDocument::loadXML($xmlStr);
	return $dom->documentElement;
}

/** 
 * @package infra
 * @subpackage utils
 */
class kXml
{
	public static function getXslEnabledPhpFunctions()
	{
		return array('date', 'gmdate', 'strtotime','urlencode','xml_load_for_xslt');
	}
	
	//check if the prop's value is valid for xml encoding.
	public static function isXMLValidContent($value) {
		if (preg_match ( '/[^\t\n\r\x{20}-\x{d7ff}\x{e000}-\x{fffd}\x{10000}-\x{10ffff}]/u', $value ))
			return false;
		return true;
	}	
	
	//strip invalid xml characters 
	public static function stripXMLInvalidChars($value) {
		preg_match_all( '/[^\t\n\r\x{20}-\x{d7ff}\x{e000}-\x{fffd}\x{10000}-\x{10ffff}]/u', $value, $invalidChars );
		$invalidChars = reset($invalidChars);
		if (count($invalidChars))
		{
			$value = str_replace($invalidChars, "", $value);
		}
		return $value;
	}
	
	public static function getLibXmlErrorDescription($xml)
	{
		$errors = libxml_get_errors();
		if(!count($errors))
			return null;
			
		$lines = explode("\r", $xml);
		
		$errorsMsg = array();
		foreach($errors as $error)
		{
			$lineNum = ($error->line) - 1;
    		$line = htmlspecialchars(isset($lines[$lineNum]) ? '[' . $lines[$lineNum] . ']' : '');
			$msg = htmlspecialchars($error->message);
			$errorsMsg[] = "$msg at line $error->line $line";
		}
		return implode("\n", $errorsMsg);
	}
	
	public static function getFirstElement ( $xml_node , $element_name , $xpath_str = null )
	{
		if ( $xpath_str )
		{
			if ( isset ( $xml_node->my_xpath ) )
			{
				$xpath = $xml_node->my_xpath;
			}
			else
			{
				$xapth = new DOMXPath($xml_node);
				// store it for next time
				$xml_node->my_xpath = $xpath ;
			}
			$elem_list = $xpath->query($xpath_str ); 
		}
		else
		{
			$elem_list = $xml_node->getElementsByTagName( $element_name );
		}
		if ( $elem_list ) return $elem_list->item(0);
		else return null;
	}
	
	public static function getLastElement ( $xml_node , $element_name , $xpath_str = null )
	{
		if ( $xpath_str )
		{
			if ( isset ( $xml_node->my_xpath ) )
			{
				$xpath = $xml_node->my_xpath;
			}
			else
			{
				$xapth = new DOMXPath($xml_node);
				// store it for next time
				$xml_node->my_xpath = $xpath ;
			}
			$elem_list = $xpath->query($xpath_str ); 
		}
		else
		{
			$elem_list = $xml_node->getElementsByTagName( $element_name );
		}
		if ( $elem_list->length > 0 ) 
			return $elem_list->item($elem_list->length - 1);
		else 
			return null;
	}
	
	public static function getFirstElementAsText ( DOMDocument $xml_doc , $element_name , $xpath_str = null )
	{
		$node = self::getFirstElement($xml_doc, $element_name, $xpath_str);
		return $node ? $node->nodeValue : "";
	}
	
	public static function getLastElementAsText ( DOMDocument $xml_doc , $element_name , $xpath_str = null )
	{
		$node = self::getLastElement($xml_doc, $element_name, $xpath_str);
		return $node ? $node->nodeValue : "";
	}
		
	// manipulate the xml_dom
	// @return if the xml_doc was modified
	public static function setChildElement ( DOMDocument &$xml_doc , $parent_element , 
		$element_name , $element_value, $remove_element_if_empty_value = false  )
	{
		$modified = true;
		$elem = self::getFirstElement ( $xml_doc , $element_name );
		if ( $elem )
		{
			// element aleardy exists
			if ( empty ( $element_value  ) && $remove_element_if_empty_value  )
			{
				// new value is empty - and should remove - remove !
				$parent_element->removeChild ( $elem );
			}
			else
			{
				if( $elem->nodeValue != $element_value )
				{
					$elem->nodeValue = $element_value ;
				}
				else
				{
					$modified = false;
				}
			}
		}
		else
		{
			// element does not exist - and no reason to create it
			if ( empty ( $element_value  ) && $remove_element_if_empty_value  ) 
			{
				$modified = false;
				
			}
			else
			{
				if (!$parent_element)
				{
					return false;
				}
				// need to create and set the value
				$elem =  $xml_doc->createElement( $element_name , $element_value  );
				$parent_element->appendChild ( $elem );
			}
		}
		
		return $modified;
	}
	
	/**
	 * 
	 * Gets a xml attribute if one exists Safe method (no exception is thrown)
	 * @param unknown_type $object
	 * @param unknown_type $attribute
	 * @return string  - the attribute value if such exists
	 */
	public static function getXmlAttributeAsString($object, $attribute)
	{
	    if(isset($object[$attribute]))
	        return (string) $object[$attribute];
	}
	
	/**
	 * 
	 * Gets a xml attribute if one exists Safe method (no exception is thrown)
	 * @param unknown_type $object
	 * @param unknown_type $attribute
	 * @return int - the attribute value if such exists
	 */
	public static function getXmlAttributeAsInt($object, $attribute)
	{
	    if(isset($object[$attribute]))
	        return (string) $object[$attribute];
	}

	/**
	 * 
	 * creates the additional data from the given xml object
	 * @param SimpleXMLElement $xmlobject
	 * @return array<key => value> - the additional data as key / value pair
	 */
	public static function getAttributesAsArray(SimpleXMLElement $xmlobject)
	{
		$attributesArray = array();
		
		foreach ($xmlobject->attributes() as $attributeKey => $attributeValue) 
		{
			$attributesArray[$attributeKey] = (string)$attributeValue;
		}
		
		return $attributesArray;
	}

	/**
	 * 
	 * Opens a given xml and returns it as a simpleXMLElement
	 * @param string $xmlFilePath - the xml file path
	 * @return simpleXMLElement - the xml
	 */
	public static function openXmlFile($xmlFilePath)
	{
		try 
		{
			$simpleXML = simplexml_load_string(file_get_contents($xmlFilePath));
		}
		catch(Exception $e)
		{
			//TODO: exception handling
			throw new Exception("Unable to load file : " . $xmlFilePath. " as xml.\n Error: " . $e->getMessage());
		}
		
		return $simpleXML;
	}

	/**
	 * 
	 * gets a dom to append and an element and another dom to append into them.
	 * @param DomDocument $domToAppend
	 * @param DOMElement $element
	 * @param DomDocument $elementsDom
	 * @throws Exception
	 */
	public static function appendDomToElement(DomDocument $domToAppend,DOMElement &$element, DomDocument $elementsDom)
	{ 
		if($domToAppend->documentElement != NULL)
		{
			$importedNode = $elementsDom->importNode($domToAppend->documentElement, true);
	
			//Add him to the output reference elements
			$element->appendChild($importedNode);
		}
		else
		{
			//DO nothing because the Dom is empty 
//			throw new Exception("The dom to append document element was null : " . $domToAppend);
		}
	}
	
	/**
	 * @param int $int milliseconds 
	 * @return string hh:mm:ss
	 */
	public static function integerToTime($int)
	{
		$hours = floor($int / (60 * 60 * 1000));
		$int -= ($hours * 60 * 60 * 1000);
		$minutes = floor($int / (60 * 1000));
		$int -= ($minutes * 60 * 1000);
		$seconds = floor($int / 1000);
		$millis = round(($int % 1000) / 100);
		$ret = array(
			str_pad($hours, 2, 0, STR_PAD_LEFT),
			str_pad($minutes, 2, 0, STR_PAD_LEFT),
			str_pad($seconds, 2, 0, STR_PAD_LEFT) . ($millis ? ".$millis" : ''),			
		);
		
		return implode(':', $ret);
	}
	
	/**
	 * @param string $time hh:mm:ss
	 * @return int milliseconds
	 */
	public static function timeToInteger($time)
	{
		$parts = explode(':', $time);
		if(!isset($parts[0]) || !is_numeric($parts[0]))
			return null;
			
		$ret = intval($parts[0]) * (60 * 60 * 1000);  // hours im milliseconds
		
		if(!isset($parts[1]))
			return $ret;
		if(!is_numeric($parts[1]))
			return null;
			
		$ret += intval($parts[1]) * (60 * 1000);  // minutes im milliseconds
		
		if(!isset($parts[2]))
			return $ret;
		if(!is_numeric($parts[2]))
			return null;
			
		$ret += floatval($parts[2]) * 1000;  // seconds im milliseconds
		
		return round($ret);
	}
	
	/**
	 * @param string $xml
	 * @param string $xslt
	 * @param array $xsltParams
	 * @return string  
	 */
	public static function transformXmlUsingXslt($xmlStr, $xslt, $xsltParams = array(), &$xsltErrors = array())
	{
					
		$xml = new KDOMDocument();
		if(!$xml->loadXML($xmlStr))
		{
			KalturaLog::err("Could not load xmlStr");
			return null;
		}
		
		$xsl = new KDOMDocument();
		if(!$xsl->loadXML($xslt))
		{
			KalturaLog::err("Could not load xslt");
			return null;
		}
		
		$proc = new XSLTProcessor;
		foreach ($xsltParams as $key => $value)
		{
			$proc->setParameter( '', $key, $value);
		}		
	    $proc->registerPHPFunctions(kXml::getXslEnabledPhpFunctions());
		@$proc->importStyleSheet($xsl);
		
		$errorHandler = new XSLTErrorCollector();
		
		$xml = @$proc->transformToDoc($xml);
		
		$errorHandler->restoreErrorHandler();	
		$xsltErrors = $errorHandler->errors;

		if(!$xml)
		{
			KalturaLog::err("XML Transformation failed");
			return null;
		}
		
		if (isset($xml->documentElement)) {
			$xml->documentElement->removeAttributeNS('http://php.net/xsl', 'php');
		}
				
		return $xml->saveXML();
	}
	
	public static function decodeXml($encodeXml)
	{
		$from = array ('&lt;',	'&gt;',	'&quot;');
		$to   = array ('<',		'>',	'"');
		return str_replace($from, $to, $encodeXml);
	}
	
	/**
	 * @param DOMXPath $domXpath - the DOMXPath in which to set the value  
	 * @param string $xpathPath - the path in the DOMXPath object of where the value should be set
	 * @param string $value
	 * @param DOMNode $contextnode
	 */
	public static function setNodeValue(DOMXPath $domXpath, $xpathPath, $value, DOMNode $contextnode = null)
	{
		if ($contextnode)
		{
			$node = $domXpath->query($xpathPath, $contextnode)->item(0);
		}
		else
		{
			$node = $domXpath->query($xpathPath)->item(0);
		}
		
		if (!$node)
		{
			return;
		}
		
		// if CDATA inside, set the value of CDATA
		if ($node->childNodes->length > 0 && $node->childNodes->item(0)->nodeType == XML_CDATA_SECTION_NODE)
		{
			$node->childNodes->item(0)->nodeValue = $value;
		}
		else
		{
			$node->nodeValue = htmlspecialchars($value,ENT_QUOTES,'UTF-8');
		}
	}	
}
