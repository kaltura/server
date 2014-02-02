<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kUserRoleCondition extends kCondition
{
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(ConditionType::USER_ROLE);
		parent::__construct($not);
	}
	
	/**
	 * @var string
	 */
	protected $roleIds;

	/**
	 * @param string $roleIds
	 */
	public function setRoleIds($roleIds)
	{
		$this->roleIds = $roleIds;
	}

	/**
	 * @return string
	 */
	public function getRoleIds()
	{
		return $this->roleIds;
	}

	/* (non-PHPdoc)
	 * @see kCondition::internalFulfilled()
	 */
	protected function internalFulfilled(kScope $scope)
	{
		$partner = PartnerPeer::retrieveByPK(kCurrentContext::$ks_partner_id);
		$roleIds = kPermissionManager::getRoleIds($partner, kCurrentContext::$ks_kuser);
		$conditionRoleIds = array_map('trim', explode(',', $this->roleIds));

		foreach($roleIds as $roleId)
		{
			if (!in_array($roleId, $conditionRoleIds))
			{
				return false;
			}
		}
		return true;
	}

	/* (non-PHPdoc)
	 * @see kCondition::shouldDisableCache()
	 */
	public function shouldDisableCache($scope)
	{
		return false;
	}
}
