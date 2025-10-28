<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kSessionTypeCondition extends kCondition
{
	/**
	 * The session type needed to remove the restriction
	 *
	 * @var int
	 */
	protected $sessionType = SessionType::ADMIN;
	
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(ConditionType::SESSION_TYPE);
		parent::__construct($not);
	}
	
	/**
	 * @param int $sessionType
	 * @return null
	 */
	public function setSessionType($sessionType)
	{
		$this->sessionType = $sessionType;
	}
	
	/**
	 * @return int
	 */
	function getSessionType()
	{
		return $this->sessionType;
	}
	
	/* (non-PHPdoc)
	 * @see kCondition::internalFulfilled()
	 */
	protected function internalFulfilled(kScope $scope)
	{
		if(!$scope->getKs())
		{
			KalturaLog::debug("No KS found in scope, session type condition not fulfilled");
			return $this->not;
		}
		
		KalturaLog::debug("Checking session type condition, needed: $this->sessionType, actual: " . $scope->getKs()->type . ", not: " . boolval($this->not));
		
		$isInternalRequest =  $this->isInternalRequest($scope->getIp());
		$disableConditionCheck = $scope->getKs()->hasPrivilege(kSessionBase::PRIVILEGE_DISABLE_ACP_SESSION_TYPE_CHECK);
		
		//If request ip is from internal subnet or has the privilege to disable session type check, bypass the session type condition
		if($disableConditionCheck || $isInternalRequest)
		{
			KalturaLog::debug("Bypassing session type condition check [{$scope->getIp()}] [$isInternalRequest] [$disableConditionCheck]");
			return $this->not;
		}
		
		return $this->sessionType == $scope->getKs()->type;
	}
	protected function isInternalRequest($requestIp)
	{
		$internalIpList = kConf::get('internal_partner_access_allowed_ips', kConfMapNames::SECURITY, array());
		foreach ($internalIpList as $curRange)
		{
			if (kIpAddressUtils::isIpInRange($requestIp, $curRange))
			{
				return true;
			}
		}
		
		return false;
	}

	/* (non-PHPdoc)
	 * @see kCondition::shouldDisableCache()
	 */
	public function shouldDisableCache($scope)
	{
		// the KS type and privileges are part of the cache key
		return false;
	}
}
