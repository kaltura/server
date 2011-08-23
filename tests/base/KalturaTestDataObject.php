<?php

require_once(dirname(__FILE__) . '/../bootstrap/bootstrapServer.php');

/**
 * 
 * Represent all tests objects
 * Holds all the data from the data xml
 * @author Roni
 *
 */
class KalturaTestDataObject extends KalturaTestDataBase
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
	 * 
	 * Additional data for the object identifier (such as key, partnerId, secret, value)
	 * @var array<key => value>
	 */
	private $additionalData = array();
	
	/**
	 * 
	 * The data object to be retrieved like propel or kaltura object
	 * @var unknown_type
	 */
	private $dataObject = null;
	
	/**
	 * 
	 * Holds all the objects comments
	 * @var array<string> - key => value where key is the object field name and value is the value in the comment
	 */
	private $comments = array(); 
	
	/**
	 * (non-PHPdoc)
	 * @see KalturaTestDataBase::getValue()
	 */
	public function getValue()
	{
		$value = parent::getValue();
		
		if(is_null($value) || empty($value)) //If value property was not set
		{
			KalturaLog::debug("Value is null or empty\n");
			KalturaLog::debug("isKey = " . isset($this->additionalData['key']) . "\n");
			KalturaLog::debug("isValue = " . isset($this->additionalData['value']) . "\n");
			
			//We get the value from teh additional data
			if(isset($this->additionalData['key']))
			{
				$value = $this->additionalData['key'];					
			}
			elseif(isset($this->additionalData['value']))
			{
				$value = $this->additionalData['value'];					
			}
		}
		
		return $value;
	}
	
	/**
	 * @return the $additionalData
	 */
	public function getAdditionalData() {
		return $this->additionalData;
	}

	/**
	 * @return the $dataObject
	 */
	public function getDataObject() {
		return $this->dataObject;
	}

	/**
	 * @param array<key $additionalData
	 */
	public function setAdditionalData($additionalData) {
		$this->additionalData = $additionalData;
	}

	/**
	 * @param unknown_type $dataObject
	 */
	public function setDataObject($dataObject) {
		$this->dataObject = $dataObject;
	}

	/**
	 * 
	 * Adds a comment to the data object
	 * @param string $commentName
	 * @param string $commentValue
	 */
	public function addComment($commentName, $commentValue)
	{
		$this->comments[$commentName] = $commentValue;
	}
	
	/**
	 * @return the $comments
	 */
	public function getComments() {
		return $this->comments;
	}

	/**
	 * @param field_type $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @param array<string> $comments
	 */
	public function setComments($comments) {
		$this->comments = $comments;
	}

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
	public function setByName($field, $value)
	{
		//If object is propel object
		if($this->dataObject instanceof BaseObject)
		{			
			$this->dataObject->setByName($field, $value);
		}
		elseif($this->dataObject instanceof KalturaObjectBase || $this->dataObject instanceof KalturaObject)
		{
			$this->dataObject->$field = $value; //Set the field as all fields are public in the API
		}
		else
			throw new Exception("Currentl only propel objects are supported");
	}

	/**
	 * The main function for converting to an Domdocument.
	 * Pass in a BaseObject (from kaltura) and returns that object serialized as a DomDocument
	 *
	 * @param KalturaTestDataObject $data
	 * @param string $rootNodeName - what you want the root node to be - defaultsto data.
	 * @return DomDocument XML
	 */
	public static function toXml(KalturaTestDataBase $data, $rootNodeName = 'data')
	{
		$dom = new DOMDocument(1.0);
		$rootNode = $dom->createElement($rootNodeName);
		$dom->appendChild($rootNode);
		
		foreach ($data->getAdditionalData() as $key => $value)
		{
			$rootNode->setAttribute($key, $value);
		}
		
		$dataObject = $data->getDataObject();
		
		//we need to check if this is a propel object
		if(is_object($dataObject ))
		{
			if($dataObject instanceof BaseObject)
			{
				//Gets the data peer of the object (used to geting all the obejct feilds)
				$dataPeer = $dataObject->getPeer(); 
				
				//Gets all object fields
				$fields = call_user_func(array($dataPeer, "getFieldNames"), BasePeer::TYPE_PHPNAME);
				
				//Create the xml elements by all fields and their values
				foreach ($fields as $field)
				{
					$value = $dataObject->getByName($field);
				
					$comment = null;
					$comments = $data->getComments();
					
					if(isset($comments[$field]))
					{
						$comment = $comments[$field];
					}
					
					KalturaTestDataObject::createFieldElement($dom, $rootNode, $value, $field, null, $comment);
				}
			}
			else // object is Kaltura object base
			{
				$reflector = new ReflectionClass($data->getDataObject());
				$properties = $reflector->getProperties(ReflectionProperty::IS_PUBLIC);
				$comments = $data->getComments();
				
				foreach ($properties as $property)
				{
					$value = $property->getValue($data->getDataObject());
					$propertyName = $property->getName();
					$propertyValueType = null;
					
					if($value != null)
					{
						$propertyValueType = gettype($value);
						if($propertyValueType == "NULL") //No such base type (int, string, ...)
						{
							$propertyValueType = null; //we null the type
							
							$class = get_class($value);
							if(class_exists($class))
							{
								$propertyValueType = $class;
							}
						}
					}
																		
					$comment = null;
					if(isset($comments[$propertyName]))
					{
						$comment = $comments[$propertyName];
					}
					
					KalturaTestDataObject::createFieldElement($dom, $rootNode, $value, $propertyName, $propertyValueType, $comment);
				}
			}
		}
		else
		{
			//Value types will be written in the same line as their type
		}
		 		
		// pass back DomElement object
		return $dom;
	}

	/**
	 * 
	 * Creates a new node in the given DomDocument xml under the root node with the values of the field (name and value)
	 * @param DomDocumnet $xml
	 * @param SimpleXmlElement $rootNode
	 * @param unknown_type $value
	 * @param string $fieldName
	 * @param string $fieldType
	 * @param string $fieldDbValue
	 */
	private static function createFieldElement(DOMDocument $xml, DomElement $rootNode, $value, $fieldName, $fieldType = null, $fieldDbValue = null)
	{
		//If the value is not an array then we just create the element and sets it's value
		if(!is_array($value))
		{
			$node = $xml->createElement($fieldName, $value);
			
			if($fieldType !== null)
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
			
			if(!$arrayNode->hasChildNodes()) //if there are no child nodes
			{
				//we add the name of the property
				$node = $xml->createElement($fieldName, "");
				$arrayNode->appendChild($node);
			}
			
			$rootNode->appendChild($arrayNode);
		}
	}
	
	/**
	 * 
	 * Creates a new Kaltura test data object 
	 * @param SimpleXMLElement $inputXml
	 */
	public static function generatefromXml($inputXml)
	{
		$objectInstace = new KalturaTestDataObject();
		$objectInstace->fromXml($inputXml);
		return $objectInstace;	
	}
	
	/**
	 * Gets a xml based data and returns a new unit test data object with those values
	 * @param SimpleXMLElement $xml
	 * @return KalturaTestDataObject the new unit test data object with the given data
	 */
	public function fromXml($xml)
	{
		$this->type = (string)$xml['type'];
			
		$this->dataObject = KalturaTestDataObject::getObjectInstance($this->type);	
	
		$this->additionalData = kXml::getAttributesAsArray($xml);
		
		if(class_exists($this->type))
		{
			foreach ($xml->children() as $child)
			{
				$childKey = $child->getName();
				
				//if dbValue exists
				if(isset($child["dbValue"]))
				{
					$this->comments[$childKey] = $child['dbValue'];
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
						$arrayKey = $childValue;
						
						foreach ($child->children() as $singleElementKey => $singleElementValue)
						{
							if(isset($singleElementValue["key"]))
							{
								$key = (string)$singleElementValue["key"];
								$arrayValue[$key] = (string)$singleElementValue;
							}
																												
							$arrayKey = $singleElementKey;
							
							//if dbValue exists
							if(isset($singleElementValue["dbValue"]))
							{
								$this->comments[$singleElementKey][$key] = $singleElementValue["dbValue"];
							}
						}
						
						if(count($arrayValue) > 0)
						{
							KalturaTestDataObject::setPropertyValue($this->dataObject, $arrayKey, $arrayValue, $childValueType);
						}
						else
						{
							KalturaTestDataObject::setPropertyValue($this->dataObject, $arrayKey, $arrayValue, 'Array');
						}
					}
	 				else 
	 				{
	 					KalturaTestDataObject::setPropertyValue($this->dataObject, $childKey, $childValue, $childValueType);
	 				}
				}	
				catch (Exception $e)
				{
					throw new Exception("Error can't set by name: " . $childValue . $e, $e->getCode());
				}
			}
		}
		else
		{
			//Handle no classes objects like string and int
			$child = $xml->children();
			if($this->type == 'fileHandler'){
				KalturaTestDataObject::setPropertyValue($this->value, "value", file_get_contents(getcwd().(string)$xml['key']), null);
			} else {
				if($child->count() == 0){
					KalturaTestDataObject::setPropertyValue($this->value, "value", trim($xml), null);
				}
				else{
					KalturaTestDataObject::setPropertyValue($this->value, "value", $child->saveXML(), null);
				}
			}
				
		}
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
						
		if(class_exists($objectInstaceType)) // TODO:handle abstract classes and parameters constructors classes
		{
			$reflectionClass = new ReflectionClass($objectInstaceType);	
			$isAbstract = $reflectionClass->isAbstract();
			$isInstantiable = $reflectionClass->isInstantiable();
			if(!$isAbstract && $isInstantiable)
			{
				KalturaLog::debug("Creating $objectInstace");
				$objectInstace = new $objectInstaceType;
			}
			else
			{
				KalturaLog::debug("$objectInstace wasn't created maybe it's abstract or not instantiable");
			}
		}
		else  //regular type (string, int, ...)
		{
			KalturaLog::debug("$objectInstaceType doesn't exitst as class \n");
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
		if(!$fieldName)
		{
			KalturaLog::debug("Can't set field $fieldName because it doesn't exist");	
			return;
		}
		
		//set the object to this value
		if($objectInstace instanceof BaseObject)
		{
			$objectInstace->setByName($fieldName, $fieldValue);
		}
		else if($objectInstace instanceof KalturaObjectBase || $objectInstace instanceof KalturaObject)
		{
			$objectInstace->$fieldName = $fieldValue;
		}
		else
		{
			if($fieldValueType) //If field value type is set
				settype($fieldValue, $fieldValueType);
			$objectInstace = $fieldValue;
		}
	}

	/**
	 * 
	 * Returns the data object behind the test data object
	 */
	public function toObject()
	{
		$value = null;
		
		if(isset($this->dataObject) || !is_null($this->dataObject)) //There is an object behind the data object 
		{
			KalturaLog::debug("Take value from data object [" . print_r($this->dataObject,true) ."]");
			$value = $this->dataObject;
		}
		else //Simple Type
		{
			KalturaLog::debug("Take value from value [" .print_r($this->getValue(),true) ."]");
			$value = $this->getValue();
		}
		
		return $value;
	}
}