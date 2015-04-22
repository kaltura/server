<?php
/**
 * @package api
 * @subpackage objects
 */
abstract class KalturaAssociativeArray extends KalturaTypedArray
{
	/* (non-PHPdoc)
	 * @see KalturaTypedArray::offsetSet()
	 */
	public function offsetSet($offset, $value) 
	{
		$this->validateType($value);
		
		if ($offset === null)
		{
			$this->array[] = $value;
		}
		else
		{
			$this->array[$offset] = $value;
		}
			
		$this->count = count ( $this->array );
	}
}