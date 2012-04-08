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
	 * @param accessControlScope $scope
	 * @return array<string>
	 */
	function getStringValues($scope)
	{
		$values = array();
		foreach($this->values as $value)
		{
			/* @var $value kStringValue */
			if(is_object($value))
			{
				if($value instanceof kStringField)
					$value->setScope($scope);
					
				$values[] = $value->getValue();
			}
			else
			{
				$values[] = strval($value);
			}
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
	
	/**
	 * @param string $field
	 * @param array $values
	 * @return boolean
	 */
	public function fieldFulfilled($field, $values)
	{
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
	
	/* (non-PHPdoc)
	 * @see kCondition::internalFulfilled()
	 */
	public function internalFulfilled(accessControl $accessControl)
	{
		$field = $this->getFieldValue($accessControl);
		$values = $this->getStringValues($accessControl->getScope());
		
		KalturaLog::debug("Matches field [$field] to values [" . print_r($values, true) . "]");
		if (!count($values))
		{
			KalturaLog::debug("No values found, condition is true");
			return true;
		}
		
		if (!$field)
		{
			KalturaLog::debug("Field is empty, condition is false");
			return false;
		}

		if(is_array($field))
		{
			foreach($field as $fieldItem)
			{
				if(!$this->fieldFulfilled($fieldItem, $values))
				{
					KalturaLog::debug("Field item [$fieldItem] does not fulfill, condition is false");
					return false;
				}
			}
			KalturaLog::debug("All field items fulfilled, condition is true");
			return true;
		}
		
		return $this->fieldFulfilled($field, $values);
	}
}
