<?php

/**
 * Base abstraction for string value, constant or calculated that retreived from the API 
 * @package Core
 * @subpackage model.data
 */
class kStringValue extends kValue
{
	/**
	 * @param string $value
	 */
	public function __construct($value) 
	{
		$this->value = $value;
	}
	
	/**
	 * @return string $value
	 */
	public function getValue() 
	{
		return $this->value;
	}

	/**
	 * @param string $value
	 */
	public function setValue($value) 
	{
		$this->value = $value;
	}
}