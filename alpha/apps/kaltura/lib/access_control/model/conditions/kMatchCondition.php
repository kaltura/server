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
			if(is_object($value))
				$values[] = $value->getValue();
			else
				$values[] = strval($value);
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
		
		KalturaLog::debug("Matches field [$field] to values [" . print_r($values, true) . "]");
		if (!count($values))
		{
			KalturaLog::debug("No values found, condition is true");
			return true;
		}
		
		if (!strlen($field))
		{
			KalturaLog::debug("Field is empty, condition is false");
			return false;
		}
			
		if(in_array($field, $values))
		{
			KalturaLog::debug("Field found in the values list, condition is true");
			return true;
		}
		
		foreach($values as $value)
		{
			if($this->matches($field, $value))
			{
				KalturaLog::debug("Field [$field] matches value [$value], condition is true");
				return true;
			}
		}
			
		KalturaLog::debug("No match found, condition is false");
		return false;
	}
}
