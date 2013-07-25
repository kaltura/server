<?php

/**
 * Base abstraction for realtime calculated boolean value 
 * @package Core
 * @subpackage model.data
 */
abstract class kBooleanField extends kBooleanValue implements IScopeField
{
	/**
	 * @var kScope
	 */
	protected $scope = null;
	
	/**
	 * Calculates the value at realtime
	 * @param kScope $scope
	 * @return bool $value
	 */
	abstract protected function getFieldValue(kScope $scope = null);
	
	/* (non-PHPdoc)
	 * @see kBooleanValue::getValue()
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
	 * @see kBooleanField::shouldDisableCache()
	 */
	public function shouldDisableCache($scope)
	{
		return true;
	}
}