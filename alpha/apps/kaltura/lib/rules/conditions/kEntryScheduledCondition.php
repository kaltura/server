<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kEntryScheduledCondition extends kCondition
{
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(ConditionType::ENTRY_SCHEDULED);
		parent::__construct($not);
	}

	
	/* (non-PHPdoc)
	 * @see kCondition::internalFulfilled()
	 */
	protected function internalFulfilled(kScope $scope)
	{
		if($scope->getEntryId())
		{
			$dbEntry = entryPeer::retrieveByPK($scope->getEntryId());
			if($dbEntry)
			{
				return $dbEntry->isScheduledNow();
			}
		}

		return false;
	}
}
