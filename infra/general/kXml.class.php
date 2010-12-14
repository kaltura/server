<?php

class kXml
{
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
	 * The main function for converting to an Domdocument.
	 * Pass in a BaseObject (from kaltura) and returns that object serialized as a DomDocument
	 *
	 * @param BaseObject $data
	 * @param string $rootNodeName - what you want the root node to be - defaultsto data.
	 * @return DomDocument XML
	 */
	public static function objectToXml(UnitTestDataObject $data, $rootNodeName = 'data')
	{
		$xml = new DOMDocument(1.0);
		$rootNode = $xml->createElement($rootNodeName);
		$xml->appendChild($rootNode); 
		
		foreach ($data->additionalData as $key => $value)
		{
			$rootNode->setAttribute($key, $value);
		}
		
		//we need to check if this is a propel object
		if(is_object($data->dataObject))
		{
			if($data->dataObject instanceof BaseObject)
			{
				//TODO: support in a different method, break this method into two parts
//				propelObjectToXml();
				
				//Gets the data peer of the object (used to geting all the obejct feilds)
				$dataPeer = $data->dataObject->getPeer(); 
				
				//Gets all object fields
				$fields = call_user_func(array($dataPeer, "getFieldNames"), BasePeer::TYPE_PHPNAME);
				
				//Create the xml elements by all fields and their values
				foreach ($fields as $field)
				{
					$value = $data->dataObject->getByName($field);
				
					if(!is_array($value ))
					{
						$node = $xml->createElement($field, $value);
						$rootNode->appendChild($node);
					}
					else
					{
						//create the array node
						$arrayNode = $xml->createElement("array");
						
						foreach ($value as $key => $singleValue)
						{
							$node = $xml->createElement($field, $singleValue);
							$node->setAttribute("key", $key );
							$arrayNode->appendChild($node);
						}
											
						$rootNode->appendChild($arrayNode); 
					}
				}
			}
			else // object is Kaltura object base
			{
				//TODO: serialize the kaltura object into XML.
				$reflector = new ReflectionClass($data->dataObject);
				$properties = $reflector->getProperties(ReflectionProperty::IS_PUBLIC);
				foreach ($properties as $property)
				{
					$value = $property->getValue($data->dataObject);
					$propertyName = $property->getName();
					$propertyValueType = gettype($value);
					
					if(!is_array($value))
					{
						$node = $xml->createElement($propertyName, $value);
						$node->setAttribute("type", $propertyValueType);
						$rootNode->appendChild($node);
					}
					else
					{
						//create the array node
						$arrayNode = $xml->createElement("array");
						
						foreach ($value as $key => $singleValue)
						{
							$node = $xml->createElement($propertyName, $singleValue);
							$node->setAttribute("type", $propertyValueType);
							$node->setAttribute("key", $key);
							$arrayNode->appendChild($node);
						}
											
						$rootNode->appendChild($arrayNode); 
					}
				}
			}
		}
		else
		{
			//Value types will be written in the same line as their type
			
//			$node = $xml->createElement(gettype($data), $data);
//			$rootNode->appendChild($node);
//			$rootNode->setAttribute("value", $data);
		}
		 		
		// pass back DomElement object
		return $xml;
	}

	/**
	 * Gets a xml based data and returns a new unit test data object with those values
	 * @param SimpleXMLElement $xml
	 * @return UnitTestDataObject the new unit test data object with the given data
	 */
	public static function XmlToObject($xml)
	{
		$objectInstace = new UnitTestDataObject();
		 
		$objectInstace->type = (string)$xml['type'];
			
		$objectInstace->dataObject = kXml::getObjectInstance($objectInstace->type);	
	
		$objectInstace->additionalData = kXml::getAttributesAsArray($xml);
		
		if(class_exists($objectInstace->type))
		{
			foreach ($xml->children() as $child)
			{
				$childKey = $child->getName();
				$childValue = (string)$child;
				$childValueType = (string)$child["type"];
				try
				{
					//TODO: handle fields which are arrays (currently handled hard coded)
					if($childKey == "array")
					{
						$arrayValue = array();
						
						foreach ($child->children() as $singleElementKey => $singleElementValue)
						{
							$key = (string)$singleElementValue["key"];
							$arrayValue[$key] = (string)$singleElementValue;
							$arrayKey = $singleElementKey;
						}
						
						kXml::setPropertyValue(&$objectInstace->dataObject, $arrayKey, $arrayValue, $childValueType);
					}
	 				else 
	 				{
	 					kXml::setPropertyValue(&$objectInstace->dataObject, $childKey, $childValue, $childValueType);
	 				}
				}	
				catch (Exception $e)
				{
					print("Error can't set by name" . $childValue . $e);
				}
			}
		}
		else
		{
			//Handle no classes objects like string and int
			//TODO: add support for file... here or there...
		}

		// pass back propel object
		return $objectInstace;
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
}

?>