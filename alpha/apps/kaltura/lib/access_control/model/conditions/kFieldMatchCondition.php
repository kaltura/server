<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kFieldMatchCondition extends kMatchCondition
{
	/**
	 * The field to evaluate against the values
	 * @var kStringField
	 */
	private $field;

	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(ConditionType::FIELD_MATCH);
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
	 * @return kStringField
	 */
	public function getField() 
	{
		return $this->field;
	}

	/**
	 * @param kStringField $field
	 */
	public function setField(kStringField $field) 
	{
		$this->field = $field;
	}

	/* (non-PHPdoc)
	 * @see kMatchCondition::shouldFieldDisableCache()
	 */
	public function shouldFieldDisableCache($scope)
	{
		return $this->field->shouldDisableCache($scope);
	}	
}
