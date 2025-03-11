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
		KalturaLog::debug("Checking session type condition, needed: $this->sessionType, actual: " . $scope->getKs()->type . ", not: " . boolval($this->not));
		return $this->sessionType == $scope->getKs()->type;
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
