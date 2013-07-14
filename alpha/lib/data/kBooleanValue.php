<?php

/**
 * Base abstraction for boolean value, constant or calculated that retreived from the API 
 * @package Core
 * @subpackage model.data
 */
class kBooleanValue extends kValue
{
	/**
	 * @param bool $value
	 */
	public function __construct($value = null) 
	{
		$this->value = $value;
	}
	
	/**
	 * @return bool $value
	 */
	public function getValue() 
	{
		return $this->value;
	}

	/**
	 * @param bool $value
	 */
	public function setValue($value) 
	{
		$this->value = $value;
	}

	/**
	 * @param kScope $scope
	 * @return bool
	 */
	public function shouldDisableCache($scope)
	{
		return false;
	}
}