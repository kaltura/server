<?php

require_once(dirname(__FILE__) . '/../bootstrap/bootstrapServer.php');

/**
 * 
 * Represent all unit tests objects identifiers
 * Holds all the data from the data xml
 * @author Roni
 *
 */
class KalturaUnitTestDataObject
{
	/**
	 * Creates a new Kaltura Object Identifier
	 * @param $keys
	 * @param $type
	 */
	function __construct($type = null, $additionalData = null, $dataObject = null)
	{
		$this->type = $type;
		$this->additionalData = $additionalData;
		$this->dataObject = $dataObject;
	}
	
	/**
	 * the kaltura object type
	 */
	public $type;
	
	/**
	 * 
	 * Additional data for the object identifier (such as key, partnerId, secret, value)
	 * @var array<key => value>
	 */
	public $additionalData = array();
	
	/**
	 * 
	 * The data object to be retrieved like propel or kaltura object
	 * @var unknown_type
	 */
	public $dataObject;
	
	/**
	 * 
	 * Holds all the objects comments
	 * @var array<string> - key => value where key is the object field name and value is the value in the comment
	 */
	public $comments = array(); 
	
	/**
	 * 
	 * Returns the object id 
	 */
	public function getId()
	{
		$objectId = null;
		
		if(isset($this->additionalData['key']))
		{
			$objectId = $this->additionalData['key'];
		}
		else if(isset($this->additionalData[$this->type . 'Id']))
		{
			$objectId = $this->additionalData[$this->type . 'Id'];
		}
		else if(isset($this->additionalData['Id']))
		{
			$objectId = $this->additionalData['Id'];
		}
		
		return $objectId;
	}
	
	/**
	 * 
	 * Returns the object type 
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * 
	 * Sets a given field by its name and given value
	 * @param string $field
	 * @param unknown_type $actualValue
	 * @throws Exception - if the object is not a propel object
	 */
	public function setByName($field, $actualValue)
	{
		//If object is propel object
		if($this->dataObject instanceof BaseObject)
		{			
			$this->dataObject->setByName($field, $actualValue);
		}
		else
		{
			throw new Exception("Currentl only propel objects are supported");
		}
	}

	/**
	 * The main function for converting to an Domdocument.
	 * Pass in a BaseObject (from kaltura) and returns that object serialized as a DomDocument
	 *
	 * @param KalturaUnitTestDataObject $data
	 * @param string $rootNodeName - what you want the root node to be - defaultsto data.
	 * @return DomDocument XML
	 */
	public static function toXml(KalturaUnitTestDataObject $data, $rootNodeName = 'data')
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
				//Gets the data peer of the object (used to geting all the obejct feilds)
				$dataPeer = $data->dataObject->getPeer(); 
				
				//Gets all object fields
				$fields = call_user_func(array($dataPeer, "getFieldNames"), BasePeer::TYPE_PHPNAME);
				
				//Create the xml elements by all fields and their values
				foreach ($fields as $field)
				{
					$value = $data->dataObject->getByName($field);
				
					$comment = null;
					if(isset($data->comments[$field]))
					{
						$comment = $data->comments[$field];
					}
					
					KalturaUnitTestDataObject::createFieldElement($xml, $rootNode, $value, $field, null, $comment);
				}
			}
			else // object is Kaltura object base
			{
				$reflector = new ReflectionClass($data->dataObject);
				$properties = $reflector->getProperties(ReflectionProperty::IS_PUBLIC);
				foreach ($properties as $property)
				{
					$value = $property->getValue($data->dataObject);
					$propertyName = $property->getName();
					$propertyValueType = gettype($value);
					
					KalturaUnitTestDataObject::createFieldElement($xml, $rootNode, $value, $propertyName, $propertyValueType, $data->comments[$propertyName]);
				}
			}
		}
		else
		{
			//Value types will be written in the same line as their type
		}
		 		
		// pass back DomElement object
		return $xml;
	}

	/**
	 * 
	 * Creates a new node in the gien xml under the root node with the values of the field (name and value)
	 * @param DomDocumnet $xml
	 * @param SimpleXmlElement $rootNode
	 * @param unknown_type $value
	 * @param string $fieldName
	 * @param string $fieldType
	 */
	private static function createFieldElement(DOMDocument $xml, DomElement $rootNode, $value, $fieldName, $fieldType = null, $fieldDbValue = null)
	{
		//If the value is not an array then we just create the element and sets it's value
		if(!is_array($value ))
		{
			$node = $xml->createElement($fieldName, $value);
			
			if($fieldType != null)
			{
				$node->setAttribute("type", $fieldType);
			}
			
			if($fieldDbValue != null)
			{
				$node->setAttribute("dbValue", $fieldDbValue);
			}
			
			$rootNode->appendChild($node);
		}
		else
		{
			//create the array node
			$arrayNode = $xml->createElement("Array");
			
			foreach ($value as $key => $singleValue)
			{
				$node = $xml->createElement($fieldName, $singleValue);
				
				if($fieldType != null)
				{
					$node->setAttribute("type", $fieldType);
				}
				
				if($fieldDbValue != null)
				{
					$dbValue= null;
					if(isset($fieldDbValue[$key]))
					{
						$dbValue = $fieldDbValue[$key];
					}
					
					$node->setAttribute("dbValue", $dbValue);
				}
			
				$node->setAttribute("key", $key );
				$arrayNode->appendChild($node);
			}
								
			$rootNode->appendChild($arrayNode);
		}
	}
	
	/**
	 * Gets a xml based data and returns a new unit test data object with those values
	 * @param SimpleXMLElement $xml
	 * @return KalturaUnitTestDataObject the new unit test data object with the given data
	 */
	public static function fromXml($xml)
	{
		$objectInstace = new KalturaUnitTestDataObject();
		 
		$objectInstace->type = (string)$xml['type'];
			
		$objectInstace->dataObject = KalturaUnitTestDataObject::getObjectInstance($objectInstace->type);	
	
		$objectInstace->additionalData = kXml::getAttributesAsArray($xml);
		
		if(class_exists($objectInstace->type))
		{
			foreach ($xml->children() as $child)
			{
				$childKey = $child->getName();
				
				//if dbValue exists
				if(isset($child["dbValue"]))
				{
					$objectInstace->comments[$childKey] = (string)$child["dbValue"];					
				}
												
				if(strlen($child) != 0)
				{
					$childValue = (string)$child;
				}
				else
				{
					$childValue = null;	
				}
				
				$childValueType = (string)$child["type"];
				
				try
				{
					//TODO: Handle fields which are arrays (currently handled hard coded)
					if($childKey == "array" || $childKey == "Array")
					{
						$arrayValue = array();
						
						foreach ($child->children() as $singleElementKey => $singleElementValue)
						{
							$key = (string)$singleElementValue["key"];
							$arrayValue[$key] = (string)$singleElementValue;
							$arrayKey = $singleElementKey;
							
							//if dbValue exists
							if(isset($singleElementValue["dbValue"]))
							{
								$objectInstace->comments[$singleElementKey][$key] = (string)$singleElementValue["dbValue"];
							}
						}
											
						KalturaUnitTestDataObject::setPropertyValue(&$objectInstace->dataObject, $arrayKey, $arrayValue, $childValueType);
					}
	 				else 
	 				{
	 					KalturaUnitTestDataObject::setPropertyValue(&$objectInstace->dataObject, $childKey, $childValue, $childValueType);
	 				}
				}	
				catch (Exception $e)
				{
					throw new Exception("Error can't set by name" . $childValue . $e, $e->getCode());
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
}