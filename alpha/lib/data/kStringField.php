<?php

/**
 * Base abstraction for realtime calculated string value 
 * @package Core
 * @subpackage model.data
 */
abstract class kStringField extends kStringValue implements IScopeField
{
	/**
	 * @var kScope
	 */
	protected $scope = null;
	
	/**
	 * Calculates the value at realtime
	 * @param kScope $scope
	 * @return string $value
	 */
	abstract protected function getFieldValue(kScope $scope = null);
	
	/* (non-PHPdoc)
	 * @see kStringValue::getValue()
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
	 * @see kStringValue::shouldDisableCache()
	 */
	public function shouldDisableCache($scope)
	{
		return true;
	}
}