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
	 * @var int
	 */
	protected $value;
	
	/**
	 * Comparing operator, enum of searchConditionComparison
	 * @var int
	 */
	protected $comparison;

	/**
	 * @return int
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
	 * @param int $value
	 */
	public function setValue($value) 
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
	 * @param accessControl $accessControl
	 * @return int|array<int> the field content
	 */
	abstract public function getFieldValue(accessControl $accessControl);
	
	/**
	 * @param int $field
	 * @return bool
	 */
	protected function fieldFulfilled($field)
	{
		switch($this->comparison)
		{
			case searchConditionComparison::GREATER_THAN:
				return ($field > $this->value);
				
			case searchConditionComparison::GREATER_THAN_OR_EQUEL:
				return ($field >= $this->value);
				
			case searchConditionComparison::LESS_THAN:
				return ($field < $this->value);
				
			case searchConditionComparison::LESS_THAN_OR_EQUEL:
				return ($field <= $this->value);
				
			case searchConditionComparison::EQUEL:
			default:
				return ($field == $this->value);
		}
	}
	
	/* (non-PHPdoc)
	 * @see kCondition::internalFulfilled()
	 */
	public function internalFulfilled(accessControl $accessControl)
	{
		$field = $this->getFieldValue($accessControl);
		
		if (is_null($this->value))
			return true;
		
		if (is_null($field))
			return false;

		if(is_array($field))
		{
			$fulfilled = true;
			foreach($field as $fieldItem)
				$fulfilled = $fulfilled && $this->fieldFulfilled($fieldItem);
				
			return $fulfilled;
		}
		
		return $this->fieldFulfilled($field);
	}
}
