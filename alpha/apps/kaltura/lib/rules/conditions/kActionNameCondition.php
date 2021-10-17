<?php

/**
 * @package Core
 * @subpackage model.data
 */
class kActionNameCondition extends kRegexCondition
{
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(ConditionType::VALIDATE_ACTION_NAME);
		parent::__construct($not);
	}

	/* (non-PHPdoc)
    * @see kCondition::getFieldValue()
    */
	public function getFieldValue(kScope $scope)
	{
		return kCurrentContext::$service . '.' . kCurrentContext::$action;
	}

	protected function matches($field, $value)
	{
		return ($field === $value) || preg_match("/$value/i", $field);
	}

}
