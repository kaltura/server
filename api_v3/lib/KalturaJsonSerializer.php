<?php
class KalturaJsonSerializer
{
	private $_serializedString = "";
	private $_ignoreNull = false;
	
	function KalturaJsonSerializer($ignoreNull)
	{
		$this->_ignoreNull = (bool)$ignoreNull;
	}
	
	function serialize($object)
	{
		$object = $this->convertTypedArraysToArrays($object);
		
		$object = $this->convertExceptionsToArrays($object);
		
		$this->_serializedString .= json_encode($object);	
	}
	
	function convertTypedArraysToArrays($object)
	{
	    if (is_object($object))
    	{
    		if ($object instanceof KalturaTypedArray)
    		{
	    		$array = array();
	    		foreach($object as $item)
	    		{
	    			$array[] = $this->convertTypedArraysToArrays($item);					
	    		}
	    		$object = $array;
    		}
    		else 
    		{
    			foreach($object as $key => $value)
        	    {
        	        $object->$key = $this->convertTypedArraysToArrays($value);
        	    }
    		}
    	}
    	else if (is_array($object))
    	{
    		$array = array();
    		foreach($object as $item)
    		{
    			$array[] = $this->convertTypedArraysToArrays($item);					
    		}
    		$object = $array;
    	}
    	
    	return $object;
	}
	
	function convertExceptionsToArrays($object)
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
    			$array[] = $this->convertExceptionsToArrays($item);					
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