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
	 * @return int $value
	 */
	abstract protected function getFieldValue();
	
	/* (non-PHPdoc)
	 * @see kIntegerValue::getValue()
	 */
	public function getValue() 
	{
		return $this->getFieldValue();
	}
}