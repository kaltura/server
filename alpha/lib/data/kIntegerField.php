<?php

/**
 * Base abstraction for realtime calculated integer value 
 * @package Core
 * @subpackage model.data
 */
abstract class kIntegerField extends kIntegerValue implements IScopeField
{
	/**
	 * @var kScope
	 */
	protected $scope = null;
	
	/**
	 * Calculates the value at realtime
	 * @param kScope $scope
	 * @return int $value
	 */
	abstract protected function getFieldValue(kScope $scope = null);
	
	/* (non-PHPdoc)
	 * @see kIntegerValue::getValue()
	 */
	public function getValue() 
	{
		return $this->getFieldValue($this->scope);
	}
	
	/**
	 * @param kScope $scope
	 */
	public function setScope(kScope $scope) 
	{
		$this->scope = $scope;
	}

	/* (non-PHPdoc)
	 * @see kIntegerValue::shouldDisableCache()
	 */
	public function shouldDisableCache($scope)
	{
		return true;
	}
}