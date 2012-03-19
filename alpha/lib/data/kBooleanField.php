<?php

/**
 * Base abstraction for realtime calculated boolean value 
 * @package Core
 * @subpackage model.data
 */
abstract class kBooleanField extends kBooleanValue
{
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
		return $this->getFieldValue();
	}
}