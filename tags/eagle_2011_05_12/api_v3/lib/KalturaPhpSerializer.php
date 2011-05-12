<?php
class KalturaPhpSerializer
{
	private $_serializedString = "";
	private $_ignoreNull = false;
	
	function KalturaPhpSerializer($ignoreNull)
	{
		$this->_ignoreNull = (bool)$ignoreNull;
	}
	
	function serialize($object)
	{
		$object = $this->convertTypedArraysToPhpArrays($object);
		
		$object = $this->convertExceptionsToPhpArrays($object);
		
		$this->_serializedString .= serialize($object);	
		
		if (is_object($object))
		{
			if ($object instanceof Exception)
			{
				$error = array(
					"code" => $object->getCode(),
					"message" => $object->getMessage()
				);
				$this->_serializedString = serialize($error);
			}
			else 
			{
				$this->_serializedString = serialize($object);
			}
		}
		else 
		{
					
		}
	}
	
	function convertTypedArraysToPhpArrays($object)
	{
	    if (is_object($object))
    	{
    		if ($object instanceof KalturaTypedArray)
    		{
	    		$array = array();
	    		foreach($object as $item)
	    		{
	    			$array[] = $this->convertTypedArraysToPhpArrays($item);					
	    		}
	    		$object = $array;
    		}
    		else 
    		{
    			foreach($object as $key => $value)
        	    {
        	        $object->$key = $this->convertTypedArraysToPhpArrays($value);
        	    }
    		}
    	}
    	else if (is_array($object))
    	{
    		$array = array();
    		foreach($object as $item)
    		{
    			$array[] = $this->convertTypedArraysToPhpArrays($item);					
    		}
    		$object = $array;
    	}
    	
    	return $object;
	}
	
	function convertExceptionsToPhpArrays($object)
	{
	    if (is_object($object) && $object instanceof Exception)
    	{
			$error = array(
				"code" => $object->getCode(),
				"message" => $object->getMessage()
			);
			$object = $error;
    	}
    	else if (is_array($object))
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
	
	function getSerializedData()
	{
		return $this->_serializedString;
	}
}