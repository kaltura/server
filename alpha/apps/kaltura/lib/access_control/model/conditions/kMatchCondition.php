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
		$this->values = $values;
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
	 * @see kCondition::fulfilled()
	 */
	public function fulfilled(accessControl $accessControl)
	{
		$field = $this->getFieldValue($accessControl);
		
		$values = $this->getStringValues();
		
		if (!count($values))
			return $this->calcNot(true);
		
		if (!strlen($field))
			return $this->calcNot(false);
			
		if(in_array($field, $values))
			return $this->calcNot(true);
		
		$matches = true;
		foreach($values as $value)
			$matches = $matches && $this->calcNot($this->matches($field, $value));
			
		return $matches;
	}
}
