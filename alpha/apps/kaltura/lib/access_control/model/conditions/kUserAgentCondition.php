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
	public function getFieldValue(kScope $scope)
	{
		kApiCache::addExtraField(kApiCache::ECF_USER_AGENT, kApiCache::COND_REGEX, $this->getStringValues($scope));
		return $scope->getUserAgent();
	}

	/* (non-PHPdoc)
	 * @see kMatchCondition::shouldFieldDisableCache()
	 */
	public function shouldFieldDisableCache($scope)
	{
		return false;
	}
}
