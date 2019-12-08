<?php
/**
 * @package Core
 * @subpackage model.data
 */

class kHttpHeaderCondition extends kRegexCondition
{
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(ConditionType::HTTP_HEADER);
		parent::__construct($not);
	}

	/* (non-PHPdoc)
	 * @see kCondition::getFieldValue()
	 */
	public function getFieldValue(kScope $scope)
	{
		$fieldValue = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : null;
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