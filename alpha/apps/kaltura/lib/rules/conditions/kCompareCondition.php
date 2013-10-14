<?php
/**
 * @package Core
 * @subpackage model.data
 * @abstract
 */
abstract class kCompareCondition extends kCondition
{
	/**
	 * Value to evaluate against the field and operator
	 * @var kIntegerValue
	 */
	protected $value;
	
	/**
	 * Comparing operator, enum of searchConditionComparison
	 * @var int
	 */
	protected $comparison;

	/**
	 * @return kIntegerValue
	 */
	public function getValue() 
	{
		return $this->value;
	}

	/**
	 * Comparing operator, enum of searchConditionComparison
	 * @return int
	 */
	public function getComparison() 
	{
		return $this->comparison;
	}

	/**
	 * @param kIntegerValue $value
	 */
	public function setValue(kIntegerValue $value) 
	{
		$this->value = $value;
	}

	/**
	 * Comparing operator, enum of searchConditionComparison
	 * @param int $comparison
	 */
	public function setComparison($comparison) 
	{
		$this->comparison = $comparison;
	}

	/**
	 * Return single integer or array of integers
	 * @param kScope $scope
	 * @return int|array<int> the field content
	 */
	abstract public function getFieldValue(kScope $scope);
	
	/**
	 * @return int
	 */
	function getIntegerValue($scope)
	{
		if(is_object($this->value))
		{
			if($this->value instanceof kIntegerField)
				$this->value->setScope($scope);
				
			return $this->value->getValue();
		}
		
		return intval($this);
	}
	
	/**
	 * @param int $field
	 * @param int $value
	 * @return bool
	 */
	protected function fieldFulfilled($field, $value)
	{
		switch($this->comparison)
		{
			case searchConditionComparison::GREATER_THAN:
				KalturaLog::debug("Compares field[$field] > value[$value]");
				return ($field > $value);
				
			case searchConditionComparison::GREATER_THAN_OR_EQUAL:
				KalturaLog::debug("Compares field[$field] >= value[$value]");
				return ($field >= $value);
				
			case searchConditionComparison::LESS_THAN:
				KalturaLog::debug("Compares field[$field] < value[$value]");
				return ($field < $value);
				
			case searchConditionComparison::LESS_THAN_OR_EQUAL:
				KalturaLog::debug("Compares field[$field] <= value[$value]");
				return ($field <= $value);
				
			case searchConditionComparison::EQUAL:
			default:
				KalturaLog::debug("Compares field[$field] == value[$value]");
				return ($field == $value);
		}
	}
	
	/* (non-PHPdoc)
	 * @see kCondition::internalFulfilled()
	 */
	protected function internalFulfilled(kScope $scope)
	{
		$field = $this->getFieldValue($scope);
		$value = $this->getIntegerValue($scope);
		
		KalturaLog::debug("Copares field [$field] to value [$value]");
		if (is_null($value))
		{
			KalturaLog::debug("Value is null, condition is true");
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
				if(!$this->fieldFulfilled($fieldItem, $value))
				{
					KalturaLog::debug("Field item [$fieldItem] does not fulfill, condition is false");
					return false;
				}
			}
			KalturaLog::debug("All field items fulfilled, condition is true");
			return true;
		}
		
		return $this->fieldFulfilled($field, $value);
	}

	/* (non-PHPdoc)
	 * @see kCondition::shouldDisableCache()
	 */
	public function shouldDisableCache($scope)
	{
		return (is_object($this->value) && $this->value->shouldDisableCache($scope)) ||
			$this->shouldFieldDisableCache($scope);
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
