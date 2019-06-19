<?php

/**
 * @package plugins.reach
 * @subpackage model.enum
 */

class kCategoryEntryCondition extends kCondition
{
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(ReachPlugin::getConditionTypeCoreValue(ReachConditionType::EVENT_CATEGORY_ENTRY));
		parent::__construct($not);
	}
	
	/**
	 * The categoryId to check if was added
	 *
	 * @var int
	 */
	protected $categoryId = null;
	
	/**
	 * The categoryIds to check if was added
	 *
	 * @var int
	 */
	protected $categoryIds = null;
	
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
	 * @param string categoryIds
	 */
	public function setCategoryIds($categoryIds)
	{
		$this->categoryIds = $categoryIds;
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
	 * @return int
	 */
	function getCategoryId()
	{
		return $this->categoryId;
	}
	
	/**
	 * @return string
	 */
	function getCategoryIds()
	{
		return $this->categoryIds;
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
		$categoryIdsToValidate = $this->getCategoryId() ? array($this->getCategoryId()) : trim(explode(",", $this->getCategoryIds()) );
		KalturaLog::debug("Validate if category added is one of the ids defined in the rule [" . implode(",", $categoryIdsToValidate) . "]");
		
		$matchingCategoryEntry = null;
		$dbCategoryEntries = categoryEntryPeer::retrieveActiveByEntryId($scope->getEntryId());
		foreach($dbCategoryEntries as $dbCategoryEntry)
		{
			/* @var $dbCategoryEntry categoryEntry */
			if(in_array($dbCategoryEntry->getCategoryId(), $categoryIdsToValidate))
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
		
		$categoryUserPermission = $this->getCategoryUserPermission();
		$comparisonOperator = $this->getComparison();
		
		if(!isset($categoryUserPermission) && !isset($comparisonOperator))
		{
			//By definition if the rule does not provide comparison level and user permission than it mean that at task
			// should be created without the restriction of the user being a member of the category
			KalturaLog::debug("Comparison and permission level are not defined by rule, task should be created for all users");
			return true;
		}
		
		$dbCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndKuserId($matchingCategoryEntry->getCategoryId(), $matchingCategoryEntry->getCreatorKuserId());
		if(!$dbCategoryKuser)
		{
			KalturaLog::debug("User [{$matchingCategoryEntry->getCreatorKuserId()}] not found in category user table");
			return false;
		}
		
		$dbUserPermission = $dbCategoryKuser->getPermissionLevel();
		switch($comparisonOperator)
		{
			case searchConditionComparison::GREATER_THAN:
				KalturaLog::debug("Compares field[$dbUserPermission] > value[$categoryUserPermission]");
				return ($dbUserPermission > $categoryUserPermission);
			
			case searchConditionComparison::GREATER_THAN_OR_EQUAL:
				KalturaLog::debug("Compares field[$dbUserPermission] >= value[$categoryUserPermission]");
				return ($dbUserPermission >= $categoryUserPermission);
			
			case searchConditionComparison::LESS_THAN:
				KalturaLog::debug("Compares field[$dbUserPermission] < value[$categoryUserPermission]");
				return ($dbUserPermission < $categoryUserPermission);
			
			case searchConditionComparison::LESS_THAN_OR_EQUAL:
				KalturaLog::debug("Compares field[$dbUserPermission] <= value[$categoryUserPermission]");
				return ($dbUserPermission <= $categoryUserPermission);
			
			case searchConditionComparison::EQUAL:
				KalturaLog::debug("Compares field[$dbUserPermission] == value[$categoryUserPermission]");
				return ($dbUserPermission == $categoryUserPermission);
		}
		
		return false;
	}
}