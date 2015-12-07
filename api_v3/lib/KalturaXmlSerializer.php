<?php
/**
 * @package api
 * @subpackage v3
 */
class KalturaXmlSerializer extends KalturaSerializer
{
	private $_ignoreNull = false;
	
	function __construct($ignoreNull)
	{
		$this->_ignoreNull = (bool)$ignoreNull;
	}
	
	function setHttpHeaders()
	{
		header("Content-Type: text/xml");
	}
	
	// Override base class functionality
	protected function prepareSerializedObject($object)
	{
		// Do nothing
	}
	
	function serialize($object)
	{
		if (function_exists('kaltura_serialize_xml'))
		{
			$serializedResult = kaltura_serialize_xml($object, $this->_ignoreNull);
		}
		else
		{
			ob_start();
			$this->serializeByType($object);
			$serializedResult = ob_get_contents();
			ob_end_clean();
		}
		
		return $serializedResult;
	}
	
	function serializeByType($object)
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
				if ($object instanceof KalturaAssociativeArray)
				{
					$this->serializeMap($object);
				}
		        elseif ($object instanceof KalturaTypedArray)
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
			$this->serializeByType($val);
			echo '</item>';
		}
	}
	
	function serializeMap($object)
	{
		foreach($object as $key => $val)
		{
			echo '<item>';
			echo "<itemKey>$key</itemKey>";
			$this->serializeByType($val);
			echo '</item>';
		}
	}
	
	function writeKalturaAPIExceptionArgsTag($object)
	{
		if ( $object instanceof KalturaAPIException )
		{
			echo '<args>';
			
			foreach ( $object->getArgs() as $name => $value )
			{
				echo '<item>';
				echo '<objectType>KalturaApiExceptionArg</objectType>'; // Hardcoded imaginary type for the client code parsers.
				echo '<name>' . kString::xmlEncode($name) . '</name>';
				echo '<value>' . kString::xmlEncode($value) . '</value>';
				echo '</item>';
			}
			
			echo '</args>';
		}
	}
	
	function serializeObject($object)
	{
		if ($object instanceof Exception)
		{
			echo '<error>';
			
			$this->writeTag('code', kString::xmlEncode($object->getCode()));
			$this->writeTag('message', kString::xmlEncode($object->getMessage()));
			$this->writeTag('objectType', get_class($object));
			$this->writeKalturaAPIExceptionArgsTag( $object );
			
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
				$this->serializeByType($value);
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
	
	public function getHeader()
	{
		return '<?xml version="1.0" encoding="utf-8"?>' .
						'<xml>' .
							'<result>' ;
	}
	
	public function getFooter($execTime = null)
	{
		if(is_null($execTime))
			$execTime = 0;
		
		return '</result>' .
							'<executionTime>' . $execTime . '</executionTime>' .
						'</xml>';
	}
	
	public function getItemHeader($itemIndex = null)
	{
		return '<item>';
	}
	
	public function getItemFooter($lastItem = false)
	{
		return '</item>';
	}
}
