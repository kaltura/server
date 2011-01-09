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
	 * Sets the given object's given property value 
	 * @param unknown_type $objectInstace
	 * @param $fieldName
	 * @param unknown_type $fieldValue
	 */
	private static function setPropertyValue(&$objectInstace, $fieldName, $fieldValue, $fieldValueType)
	{
		//set the object to this value
		if($objectInstace instanceof BaseObject)
		{
			$objectInstace->setByName($fieldName, $fieldValue);
		}
		else if($objectInstace instanceof KalturaObjectBase)
		{
			$objectInstace->$fieldName = $fieldValue;
		}
		else
		{
			//Set the attribute to its right type
			settype($fieldValue, $fieldValueType);
			$objectInstace = $fieldValue;
		}
	} 
	
	/**
	 * 
	 * Sets the given object's given property value 
	 * @param testCaseFailure $failure
	 * @param string $rootNodeName - default to 'data'
	 * @return DOMDocument - the xml for the given error
	 */
	public static function UnitTestErrorToXml(testCaseFailure $failure, $rootNodeName = 'data')
	{
		if(count($failure->failures) == 0)
		{
			return "";
		}
		
		$xml = new DOMDocument(1.0);
		$rootNode = $xml->createElement($rootNodeName);
		$xml->appendChild($rootNode);
		
		$inputsNode = $xml->createElement("Inputs");
				
		foreach ($failure->inputs as $inputKey => $inputValue)
		{
			//TODO: add support for non propel objects
			$node = $xml->createElement("Input");
			$node->setAttribute("type", get_class($inputValue));
			$node->setAttribute(get_class($inputValue)."Id", $inputValue->getId());
			$inputsNode->appendChild($node);
		}
		
		$failuresNode = $xml->createElement("Failures");
		
		foreach ($failure->failures as $unitTestFailure)
		{
			$node = $xml->createElement("Failure");
			
			$fieldNode = $xml->createElement("Field", $unitTestFailure->field);
			$node->appendChild($fieldNode);
			
			$value = $unitTestFailure->outputReferenceValue;
			
			if(!is_array($value ))
			{
				$outputReferenceNode = $xml->createElement("OutputReference", $unitTestFailure->outputReferenceValue);
				$node->appendChild($outputReferenceNode);
			}
			else
			{
				$outputReferenceNode = $xml->createElement("OutputReference");
				$node->appendChild($outputReferenceNode);
				
				//create the array node
				$arrayNode = $xml->createElement("Array");

				//TODO: support not hard coded
				$field = "CommandLines";
				
				foreach ($value as $key => $singleValue)
				{
					$arrayValueNode = $xml->createElement($field, $singleValue);
					$arrayValueNode->setAttribute("key", $key );
					$arrayNode->appendChild($arrayValueNode );
				}
									
				$outputReferenceNode ->appendChild($arrayNode);
			}
					
			$value = $unitTestFailure->actualValue;
			
			if(!is_array($value ))
			{
				$actualOutputNode = $xml->createElement("ActualOutput", $unitTestFailure->actualValue);
				$node->appendChild($actualOutputNode);
			}
			else
			{
				$actualOutputNode = $xml->createElement("ActualOutput");
				$node->appendChild($actualOutputNode);
				
				//TODO: support not hard coded
				$field = "CommandLines";
				
				//create the array node
				$arrayNode = $xml->createElement("Array");
				
				foreach ($value as $key => $singleValue)
				{
					$arrayValueNode = $xml->createElement($field, $singleValue);
					$arrayValueNode ->setAttribute("key", $key );
					$arrayNode->appendChild($arrayValueNode );
				}
									
				$actualOutputNode->appendChild($arrayNode);
			}
									
			$failuresNode->appendChild($node);
		}
											
		$rootNode->appendChild($inputsNode);
		$rootNode->appendChild($failuresNode);
	
		//pass back DomElement object
		return $xml;
	}
	
	/**
	 * 
	 * Returns an objetct instance from the given type 
	 * @param unknown_type $objectInstace
	 * @param $fieldName
	 * @param unknown_type $fieldValue
	 */
	private static function getObjectInstance($objectInstaceType)
	{
		$objectInstace = "";
		
		if(class_exists($objectInstaceType))
		{
			$objectInstace = new $objectInstaceType;
		}
		else  //regular type (string, int, ...)
		{
			//TODO: check all base types like int, string, ...
		}		
		
		return $objectInstace;
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