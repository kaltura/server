<?php

/**
 * Base abstraction for realtime calculated integer value 
 * @package Core
 * @subpackage model.data
 */
abstract class kIntegerField extends kIntegerValue
{
	/**
	 * Calculates the value at realtime
	 * @param accessControlScope $scope
	 * @return int $value
	 */
	abstract protected function getFieldValue(accessControlScope $scope = null);
	
	/* (non-PHPdoc)
	 * @see kIntegerValue::getValue()
	 */
	public function getValue() 
	{
		return $this->getFieldValue();
	}
}