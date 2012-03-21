<?php
/**
 * @package Core
 * @subpackage model.data
 * @abstract
 */
abstract class kMatchCondition extends kCondition
{
	/**
	 * @var array<kStringValue>
	 */
	protected $values;

	/**
	 * @param array $values
	 */
	function setValues(array $values)
	{
		$kStringValues = $values;
		foreach($values as $index => $value)
			if(is_string($value))
				$kStringValues[$index] = new kStringValue($value);
				
		$this->values = $kStringValues;
	}
	
	/**
	 * @return array
	 */
	function getValues()
	{
		return $this->values;
	}
	
	/**
	 * @return array<string>
	 */
	function getStringValues()
	{
		$values = array();
		foreach($this->values as $value)
		{
			/* @var $value kStringValue */
			$values[] = $value->getValue();
		}
		
		return $values;
	}
	
	/**
	 * @param accessControl $accessControl
	 * @return string the field content
	 */
	abstract public function getFieldValue(accessControl $accessControl);
	
	/**
	 * @param string $field
	 * @param string $value
	 */
	protected function matches($field, $value)
	{
		return ($field === $value);
	}
	
	/* (non-PHPdoc)
	 * @see kCondition::internalFulfilled()
	 */
	public function internalFulfilled(accessControl $accessControl)
	{
		$field = $this->getFieldValue($accessControl);
		
		$values = $this->getStringValues();
		
		if (!count($values))
			return true;
		
		if (!strlen($field))
			return false;
			
		if(in_array($field, $values))
			return true;
		
		$matches = true;
		foreach($values as $value)
			$matches = $matches && $this->matches($field, $value);
			
		return $matches;
	}
}
