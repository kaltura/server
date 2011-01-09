<?php

class kXml
{
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
					debugUtils::st();
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
			$simpleXML = simplexml_load_file($xmlFilePath);
		}
		catch(Exception $e)
		{
			if(UnitTestBase::$failureFile != null)
			{
				//TODO: exception handling
				print("Unable to load file : " . $xmlFilePath. " as xml.\n Error: " . $e->getMessage());
				die();
			}
		}
		
		return $simpleXML;
	}
}

?>