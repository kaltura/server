<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kOrCondition extends kCondition
{
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(ConditionType::OR_OPERATOR);
		parent::__construct($not);
	}
	
	/**
	 * The privelege needed to remove the restriction
	 * 
	 * @var array
	 */
	protected $conditions = array();
	
	/**
	 * @return the $conditions
	 */
	public function getConditions()
	{
		return $this->conditions;
	}

	/**
	 * @param array $conditions
	 */
	public function setConditions($conditions)
	{
		$this->conditions = $conditions;
	}

	/* (non-PHPdoc)
	 * @see kCondition::internalFulfilled()
	 */
	protected function internalFulfilled(kScope $scope)
	{
		foreach($this->conditions as $condition)
		{
			/* @var $condition kCondition */
			if($condition->fulfilled($scope))
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
		foreach($this->conditions as $condition)
		{
			/* @var $condition kCondition */
			if($condition->shouldDisableCache($scope))
			{
				return true;
			}
		}
		
		return false;
	}
}
