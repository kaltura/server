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
	public function getFieldValue(accessControl $accessControl)
	{
		return $this->field->getFieldValue($accessControl->getScope());
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
}
