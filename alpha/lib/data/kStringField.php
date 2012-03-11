<?php

/**
 * Base abstraction for realtime calculated string value 
 * @package Core
 * @subpackage model.data
 */
abstract class kStringField extends kStringValue
{
	/**
	 * Calculates the value at realtime
	 * @return string $value
	 */
	abstract protected function getFieldValue();
	
	/* (non-PHPdoc)
	 * @see kStringValue::getValue()
	 */
	public function getValue() 
	{
		return $this->getFieldValue();
	}
}