<?php
class KalturaXmlSerializer
{
	private $_serializedString = "";
	private $_ignoreNull = false;
	
	function KalturaXmlSerializer($ignoreNull)
	{
		$this->_ignoreNull = (bool)$ignoreNull;
	}
	
	function serialize($object)
	{
		$type = gettype($object);

		switch($type)
		{
			case "boolean":
			    $object = ($object) ? 1 : 0;
			    $this->serializePrimitive($object);
			    break;
			case "integer":
			case "double":
			case "string":
			case "NULL":
				$this->serializePrimitive($object);
				break;
				
			case "array":
				$this->serializeArray($object);
				break;
				
			case "object":
		        if ($object instanceof KalturaTypedArray)
			    {
    				$this->serializeArray($object);
			    }
			    else 
			    {
    				$this->serializeObject($object);
			    }
				break;
				
			case "resource":
			case "unknown type":
			default:
				throw new Exception("The type [".$type."] cannot be serialized");
				break;
		}
	}
	
	function serializePrimitive($object)
	{
		$this->_serializedString .= kString::xmlEncode($object);		
	}
	
	function serializeArray($object)
	{
		foreach($object as $val)
		{
		    $this->writeStartTag("item");
			$this->serialize($val);
			$this->writeEndTag("item");
		}
	}
	
	function serializeObject($object)
	{
		if ($object instanceof Exception)
		{
			$this->writeStartTag("error");
			$this->writeTag("code", $object->getCode());
			$this->writeTag("message", $object->getMessage());
			$this->writeEndTag("error");
		}
		else
		{
			// get object class
			$reflectObject = new ReflectionObject($object);
			$class = $reflectObject->getName();
		
			// load class reflection
			$typeReflector = KalturaTypeReflectorCacher::get($class);
	
			$properties = $typeReflector->getProperties();
			
			// write the object type
			$this->writeTag("objectType", $class);

			foreach($properties as $property)
			{
				$name = $property->getName();
				$value = $object->$name;
				if ($this->_ignoreNull === true && $value === null)
					continue;
					
				$this->writeStartTag($name);
				$this->serialize($value);
				$this->writeEndTag($name);
			}
		}
	}
	
	function writeTag($tag, $value)
	{
		$this->writeStartTag($tag);
		$this->_serializedString .= $value;
		$this->writeEndTag($tag);
	}
	
	function writeStartTag($tag)
	{
		$this->_serializedString .= "<".$tag.">";
	}
	
	function writeEndTag($tag)
	{
		$this->_serializedString .= "</".$tag.">";
	}
	
	function getSerializedData()
	{
		return $this->_serializedString;
	}
}