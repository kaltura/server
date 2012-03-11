<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kUserAgentCondition extends kRegexCondition
{
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(ConditionType::USER_AGENT);
		parent::__construct($not);
	}
	
	/* (non-PHPdoc)
	 * @see kCondition::getFieldValue()
	 */
	public function getFieldValue(accessControl $accessControl)
	{
		$scope = $accessControl->getScope();
		return $scope->getUserAgent();;
	}
}
