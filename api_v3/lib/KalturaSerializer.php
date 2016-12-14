<?php
/**
 * Base class for plugin serializers
 * @package api
 * @subpackage v3
 */
abstract class KalturaSerializer
{
	protected function prepareSerializedObject($object)
	{
		$object = $this->convertTypedArraysToPhpArrays($object);
		$object = $this->convertExceptionsToPhpArrays($object);
		return $object;
	}
	
	public function setHttpHeaders() {}
	
	abstract public function serialize($object);
	
	public function getHeader() { return '';}
	public function getMulitRequestHeader($itemsCount = null) { return '';}
	public function getItemHeader($itemIndex = null) { return '';}
	
	public function getFooter($execTime = null) { return '';}
	public function getMulitRequestFooter() { return '';}
	public function getItemFooter($lastItem = false) { return '';}
	
	protected function convertExceptionsToPhpArrays($object)
	{
	    if (is_object($object) && $object instanceof Exception)
    	{
			$error = array(
				"code" => $object->getCode(),
				"message" => $object->getMessage(),
				"objectType" => get_class($object)
			);
			
			if ( $object instanceof KalturaAPIException )
			{
				$error["args"] = $object->getArgs();
			}
			
			$object = $error;
    	}
    	else if (is_array($object)) // Support for multi-request
    	{
    		$array = array();
    		foreach($object as $item)
    		{
    			$array[] = $this->convertExceptionsToPhpArrays($item);					
    		}
			
    		$object = $array;
    	}
		
    	return $object;
	}
	
	protected function convertTypedArraysToPhpArrays($object, $wantedType = null)
	{
	    if (is_object($object))
    	{
    		if ($object instanceof KalturaTypedArray)
			{
    			return $this->convertTypedArraysToPhpArrays($object->toArray());
			}

		    $reflector = new  KalturaTypeReflectorCacher::get(get_class($object));

			foreach($object as $key => $value)
			{
				$propertyInfo = $reflector->getProperty($key);
				$expectedType = null;
				if ($propertyInfo)
					$expectedType = $propertyInfo->getType();
				$object->$key = $this->convertTypedArraysToPhpArrays($value, $expectedType);
			}
				
			return $object;
    	}
    	
    	if (is_array($object))
    	{
    		$array = array();
    		foreach($object as $key => $item)
			{
				$array[$key] = $this->convertTypedArraysToPhpArrays($item);
			}
			
    		return $array;
    	}

		switch ($wantedType)
		{
			case 'bool':
				$object =  ($object)? true:false;
				break;
			case 'int':
				$object = (int)$object;
				break;
			case 'double':
				$object = (double)$object;
				break;
			case 'float':
				$object = (float)$object;
				break;
			case 'string':
				$object = strval($object);
				break;
		}
    	
    	return $object;
	}
}
