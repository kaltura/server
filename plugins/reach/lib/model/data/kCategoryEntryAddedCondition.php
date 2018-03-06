<?php

/**
 * @package plugins.reach
 * @subpackage model.enum
 */

class kCategoryEntryAddedCondition extends kCondition
{
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(ReachPlugin::getConditionTypeCoreValue(ReachConditionType::EVENT_CATEGORY_ENTRY_ADDED));
		parent::__construct($not);
	}
	
	/**
	 * The categoryId to check if was added
	 *
	 * @var int
	 */
	protected $categoryId = null;
	
	/**
	 * The min category user permission level to chek for
	 *
	 * @var CategoryKuserPermissionLevel
	 */
	protected $categoryUserPermission = null;
	
	/**
	 * The min category user permission level to chek for
	 *
	 * @var searchConditionComparison
	 */
	protected $comparison = null;
	
	/**
	 * @param int categoryId
	 */
	public function setCategoryId($categoryId)
	{
		$this->categoryId = $categoryId;
	}
	
	/**
	 * @param int $categoryUserPermissionGreaterThanOrEqual
	 */
	public function setCategoryUserPermission($categoryUserPermission)
	{
		$this->categoryUserPermission = $categoryUserPermission;
	}
	
	/**
	 * @param int $searchConditionComparison
	 */
	public function setComparison($comparison)
	{
		$this->comparison = $comparison;
	}
	
	/**
	 * @return strin
	 */
	function getCategoryId()
	{
		return $this->categoryId;
	}
	
	/**
	 * @return int
	 */
	public function getCategoryUserPermission()
	{
		return $this->categoryUserPermission;
	}
	
	/**
	 * @return int
	 */
	public function getComparison()
	{
		return $this->comparison;
	}
	
	/* (non-PHPdoc)
	 * @see kCondition::internalFulfilled()
	 */
	protected function internalFulfilled(kScope $scope)
	{
		KalturaLog::debug("Validate if category added is one of the ids defined in the rule [$this->getCategoryId()]");
		
		$matchingCategoryEntry = null;
		$dbCategoryEntries = categoryEntryPeer::retrieveActiveByEntryId($scope->getEntryId());
		foreach($dbCategoryEntries as $dbCategoryEntry)
		{
			/* @var $dbCategoryEntry categoryEntry */
			if($dbCategoryEntry->getCategoryId() == $this->getCategoryId())
			{
				$matchingCategoryEntry = $dbCategoryEntry;
				break;
			}
		}
		
		if(!$matchingCategoryEntry)
		{
			KalturaLog::debug("No matching category entry found");
			return false;
		}
		
		$dbCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndKuserId($matchingCategoryEntry->getCategoryId(), $matchingCategoryEntry->getCreatorKuserId());
		if(!$dbCategoryKuser)
		{
			KalturaLog::debug("User [{$matchingCategoryEntry->getCreatorKuserId()}] not found in category user table");
			return false;
		}
		
		$dbUserPermission = $dbCategoryKuser->getPermissionLevel();
		$ValueToCompareTo = $this->getCategoryUserPermission();
		switch($this->getComparison())
		{
			case searchConditionComparison::GREATER_THAN:
				KalturaLog::debug("Compares field[$dbUserPermission] > value[$ValueToCompareTo]");
				return ($dbUserPermission > $ValueToCompareTo);
			
			case searchConditionComparison::GREATER_THAN_OR_EQUAL:
				KalturaLog::debug("Compares field[$dbUserPermission] >= value[$ValueToCompareTo]");
				return ($dbUserPermission >= $ValueToCompareTo);
			
			case searchConditionComparison::LESS_THAN:
				KalturaLog::debug("Compares field[$dbUserPermission] < value[$ValueToCompareTo]");
				return ($dbUserPermission < $ValueToCompareTo);
			
			case searchConditionComparison::LESS_THAN_OR_EQUAL:
				KalturaLog::debug("Compares field[$dbUserPermission] <= value[$ValueToCompareTo]");
				return ($dbUserPermission <= $ValueToCompareTo);
			
			case searchConditionComparison::EQUAL:
				KalturaLog::debug("Compares field[$dbUserPermission] == value[$ValueToCompareTo]");
				return ($dbUserPermission == $ValueToCompareTo);
		}
		
		return false;
	}
}
