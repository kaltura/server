<?php
/**
 * @package api
 * @subpackage v3
 */
class KalturaXmlSerializer
{
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
			case 'boolean':
			    $object = ($object) ? 1 : 0;
			    $this->serializePrimitive($object);
			    break;
			case 'integer':
			case 'double':
			case 'string':
			case 'NULL':
				$this->serializePrimitive($object);
				break;
				
			case 'array':
				$this->serializeArray($object);
				break;
				
			case 'object':
		        if ($object instanceof KalturaTypedArray)
			    {
    				$this->serializeArray($object);
			    }
			    else 
			    {
    				$this->serializeObject($object);
			    }
				break;
				
			case 'resource':
			case 'unknown type':
			default:
				throw new Exception('The type ['.$type.'] cannot be serialized');
				break;
		}
	}
	
	function serializePrimitive($object)
	{
		echo kString::xmlEncode($object);		
	}
	
	function serializeArray($object)
	{
		foreach($object as $val)
		{
			echo '<item>';
			$this->serialize($val);
			echo '</item>';
		}
	}
	
	function serializeObject($object)
	{
		if ($object instanceof Exception)
		{
			echo '<error>';
			$this->writeTag('code', $object->getCode());
			$this->writeTag('message', $object->getMessage());
			echo '</error>';
		}
		else
		{
			// get object class
			$reflectObject = new ReflectionObject($object);
			$class = $reflectObject->getName();
		
			// write the object type
			$this->writeTag('objectType', $class);
			
			// load class reflection
			$typeReflector = KalturaTypeReflectorCacher::get($class);
			if(!$typeReflector)
			{
				echo '<error>';
				$this->writeTag('message', 'Type reflector not found');
				echo '</error>';
				return;
			}
	
			$properties = $typeReflector->getProperties();
			
			foreach($properties as $property)
			{
				$name = $property->getName();
				$value = $object->$name;
				if ($this->_ignoreNull === true && $value === null)
					continue;
					
				echo '<'.$name.'>';
				$this->serialize($value);
				echo '</'.$name.'>';
			}
		}
	}
	
	function writeTag($tag, $value)
	{
		echo '<'.$tag.'>';
		echo $value;
		echo '</'.$tag.'>';
	}
}
