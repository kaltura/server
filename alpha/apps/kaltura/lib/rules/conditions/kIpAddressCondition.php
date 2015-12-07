<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kIpAddressCondition extends kMatchCondition
{
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(ConditionType::IP_ADDRESS);
		parent::__construct($not);
	}
	
	/* (non-PHPdoc)
	 * @see kCondition::getFieldValue()
	 */
	public function getFieldValue(kScope $scope)
	{
		kApiCache::addExtraField(kApiCache::ECF_IP, kApiCache::COND_IP_RANGE, $this->getStringValues($scope));
		return $scope->getIp();	
	}

	/* (non-PHPdoc)
	 * @see kMatchCondition::matches()
	 */
	protected function matches($field, $value)
	{
		return kIpAddressUtils::isIpInRange($field, $value);
	}

	/**
	 * @param kScope $scope
	 * @return bool
	 */
	public function shouldFieldDisableCache($scope)
	{
		return false;
	}
}
