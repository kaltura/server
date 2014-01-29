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
	 * @var array
	 */
	protected $roleIds;

	/**
	 * @param array $roleIds
	 */
	public function setRoleIds($roleIds)
	{
		$this->roleIds = $roleIds;
	}

	/**
	 * @return array
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
		$roleIds = kPermissionManager::getRoleIds(kCurrentContext::$ks, kCurrentContext::$ks_partner_id, $partner, kCurrentContext::$ks_kuser, kCurrentContext::$is_admin_session);

		// all defined roles must exist in current session for the condition to fulfill
		foreach($this->roleIds as $roleId)
		{
			if (!in_array($roleId, $roleIds))
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
