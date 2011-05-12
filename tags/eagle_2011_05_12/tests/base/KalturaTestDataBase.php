<?php

/**
 * 
 * Represents all kaltura test data
 * @author Roni
 *
 */
class KalturaTestDataBase
{
	/**
	 * Creates a new Kaltura Object Identifier
	 * @param $keys
	 * @param $type
	 */
	function __construct($type = null, $id = null, $value = null)
	{
		$this->type = $type;
		$this->id = $id;
		$this->value = $value;
	}
	
	/**
	 * the test data type
	 */
	private $type;
	
	/**
	 * 
	 * The test data id
	 * @var unknown_type
	 */
	private $id;
	
	/**
	 * 
	 * The test data value (single value data)
	 * @var unknown_type
	 */
	private $value;

	
	/**
	 * @param field_type $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @param unknown_type $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return the $value
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * @param field_type $value
	 */
	public function setValue($value) {
		$this->value = $value;
	}

	/**
	 * 
	 * Returns the data object id 
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * 
	 * Returns the data object type 
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * The main function for converting test data objects to a Domdocument.
	 * Gets a KalturaTestDataBase object. 
	 *
	 * @param KalturaTestDataBase $data
	 * @param string $rootNodeName - what you want the root node to be - defaultsto data.
	 * @return DomDocument XML
	 */
	public static function toXml(KalturaTestDataBase $data, $rootNodeName = 'data')
	{
		$dom = new DOMDocument(1.0);
		$rootNode = $dom->createElement($rootNodeName);
		$dom->appendChild($rootNode);
		
		//Value types will be written in the same line as their type
		$rootNode->setAttribute('type', $this->type);
		$rootNode->setAttribute('id', $this->id);
		$rootNode->setAttribute('value', $this->value);
		
		// pass back DomElement object
		return $dom;
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
			
			//TODO: fix to support dbValue of null
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
	 * Gets a xml based data and returns a new test data object base with those values
	 * @param SimpleXMLElement $xml
	 * @return KalturaTestDataObject the new unit test data object with the given data
	 */
	public static function generatefromXml($xml)
	{
		$objectInstace = new KalturaTestDataObject();
		 
		$objectInstace->fromXml($xml);
		
		// pass back propel object
		return $objectInstace;
	}
	
	/**
	 * Gets a xml based data and sets the test data object with those values
	 * @param SimpleXMLElement $xml
	 * @return KalturaTestDataObject the new unit test data object with the given data
	 */
	public function fromXml($xml)
	{
		$this->type = (string)$xml['type'];
		$this->id = (string)$xml['id'];
		$this->value = (string)$xml['value'];
	}

	/**
	 * 
	 * Returns an object instance from the given type 
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