<?php

/**
 * @package plugins.reach
 * @subpackage api.objects 
 */

class KalturaCategoryEntryCondition extends KalturaCondition
{
	/**
	 * Category id to check condition for
	 *
	 * @var int
	 */
	public $categoryId;
	
	/**
	 * Category id's to check condition for
	 *
	 * @var string
	 */
	public $categoryIds;
	
	/**
	 * Minimum category user level permission to validate
	 *
	 * @var KalturaCategoryUserPermissionLevel
	 */
	public $categoryUserPermission;
	
	/**
	 * Comparing operator
	 * @var KalturaSearchConditionComparison
	 */
	public $comparison;
	
	private static $mapBetweenObjects = array
	(
		'categoryId',
		'categoryUserPermission',
		'comparison',
		'categoryIds',
	);
	
	/**
	 * Init object type
	 */
	public function __construct()
	{
		$this->type = ReachPlugin::getApiValue(ReachConditionType::EVENT_CATEGORY_ENTRY);
	}
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kCategoryEntryCondition();
		
		return parent::toObject($dbObject, $skip);
	}
	
	public function validateForInsert($propertiesToSkip = array())
	{
		$propertiesToSkip[] = "type";
		parent::validateForInsert($propertiesToSkip);
		
		$this->validatePropertyNotNull(array("categoryId", "categoryIds"), true);
		if($this->categoryUserPermission)
		{
			$this->validatePropertyNotNull("comparison");
		}
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		/* @var $sourceObject kCategoryEntryCondition */
		if(($this->categoryIds && $sourceObject->getCategoryId()) || ($this->categoryId && $sourceObject->getCategoryIds()))
		{
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_ALL_MUST_BE_NULL_BUT_ONE, implode("/", array("categoryId", "categoryIds")));
		}
		
		$propertiesToSkip[] = "type";
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
}
