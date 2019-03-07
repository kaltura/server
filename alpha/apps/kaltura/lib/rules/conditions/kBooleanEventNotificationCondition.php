<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kBooleanEventNotificationCondition extends kCondition
{
	/**
	 * @var string
	 */
	protected $booleanEventNotificationIds;

	public function __construct($not = false)
	{
		$this->setType(ConditionType::BOOLEAN);
		parent::__construct($not);
	}

	/* (non-PHPdoc)
 	* @see kCondition::internalFulfilled()
 	*/
	protected function internalFulfilled(kScope $scope)
	{
		return true;
	}

	/**
	 * @return string
	 */
	function getBooleanEventNotificationIds()
	{
		return $this->booleanEventNotificationIds;
	}

	/**
	 * @param string
	 */
	function setBooleanEventNotificationIds($booleanEventNotificationIds)
	{
		$this->booleanEventNotificationIds = $booleanEventNotificationIds;
	}

}

