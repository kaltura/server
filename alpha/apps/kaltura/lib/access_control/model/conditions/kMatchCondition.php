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
	 * @var array
	 */
	protected $dynamicValues;
	
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

	/* (non-PHPdoc)
	 * @see kCondition::applyDynamicValues()
	 */
	protected function applyDynamicValues(kScope $scope)
	{
		parent::applyDynamicValues($scope);
		$this->dynamicValues = $scope->getDynamicValues('{', '}');
	}
	
	/**
	 * @param kScope $scope
	 * @return array<string>
	 */
	function getStringValues($scope = null)
	{
		if(!is_array($this->values))
			return array();
			
		$values = array();
		$dynamicValuesKeys = null;
		if(is_array($this->dynamicValues) && count($this->dynamicValues))
			$dynamicValuesKeys = array_keys($this->dynamicValues);
		
		foreach($this->values as $value)
		{
			/* @var $value kStringValue */
			$calculatedValue = null;
			if(is_object($value))
			{
				if($scope && $value instanceof kStringField)
					$value->setScope($scope);
				
				$calculatedValue = $value->getValue();
			}
			else
			{
				$calculatedValue = strval($value);
			}
			
			if($dynamicValuesKeys)
				$calculatedValue = str_replace($dynamicValuesKeys, $this->dynamicValues, $calculatedValue);
		
			$values[] = $calculatedValue;
		}
		
		return $values;
	}
	
	/**
	 * @param kScope $scope
	 * @return string the field content
	 */
	abstract public function getFieldValue(kScope $scope);
	
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
	protected function internalFulfilled(kScope $scope)
	{
		$field = $this->getFieldValue($scope);
		$values = $this->getStringValues($scope);
		
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
	
	/* (non-PHPdoc)
	 * @see kCondition::shouldDisableCache()
	 */
	public function shouldDisableCache($scope)
	{
		if(is_array($this->values))
		{
			foreach($this->values as $value)
			{
				if (is_object($value) && $value->shouldDisableCache($scope))
				{
					return true;
				}
			}
		}
		
		return $this->shouldFieldDisableCache($scope);
	}

	/**
	 * @param kScope $scope
	 * @return bool
	 */
	public function shouldFieldDisableCache($scope)
	{
		return true;
	}
}
