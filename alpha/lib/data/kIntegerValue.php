<?php

/**
 * Base abstraction for integer value, constant or calculated that retreived from the API 
 * @package Core
 * @subpackage model.data
 */
class kIntegerValue extends kValue
{
	/**
	 * @param int $value
	 */
	public function __construct($value = null) 
	{
		$this->value = $value;
	}
	
	/**
	 * @return int $value
	 */
	public function getValue() 
	{
		return $this->value;
	}

	/**
	 * @param int $value
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