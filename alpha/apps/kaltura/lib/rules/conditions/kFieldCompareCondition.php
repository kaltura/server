<?php

/**
 * @package Core
 * @subpackage model.data
 */
class kFieldCompareCondition extends kCompareCondition
{
	/**
	 * The field to evaluate against the values
	 * @var kIntegerField
	 */
	private $field;

	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(ConditionType::FIELD_COMPARE);
		parent::__construct($not);
	}
	
	/* (non-PHPdoc)
	 * @see kMatchCondition::getFieldValue()
	 */
	public function getFieldValue(kScope $scope)
	{
		$this->field->setScope($scope);
		return $this->field->getValue();
	}
	
	/**
	 * @return kIntegerField
	 */
	public function getField() 
	{
		return $this->field;
	}

	/**
	 * @param kIntegerField $field
	 */
	public function setField(kIntegerField $field) 
	{
		$this->field = $field;
	}
	
	/* (non-PHPdoc)
	 * @see kCompareCondition::shouldFieldDisableCache()
	 */
	public function shouldFieldDisableCache($scope)
	{
		return $this->field->shouldDisableCache($scope);
	}	
}
