<?php
/**
 * @package plugins.eventNotification
 * @subpackage model.data
 */
class kEventFieldCondition extends kEventCondition
{
	/**
	 * The field to evaluate against the values
	 * @var kBooleanField
	 */
	private $field;

	/* (non-PHPdoc)
	 * @see kEventCondition::fulfilled()
	 */
	public function fulfilled(kEventScope $scope)
	{
		$this->field->setScope($scope);
		return $this->field->getValue();
	}
	
	/**
	 * @return kBooleanField
	 */
	public function getField() 
	{
		return $this->field;
	}

	/**
	 * @param kBooleanField $field
	 */
	public function setField(kBooleanField $field) 
	{
		$this->field = $field;
	}
}
