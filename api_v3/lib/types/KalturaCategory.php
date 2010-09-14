<?php
class KalturaCategory extends KalturaObject implements IFilterable 
{
	/**
	 * The id of the Category
	 * 
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $id;
	
	/**
	 * 
	 * @var int
	 * @filter eq,in
	 */
	public $parentId;
	
	/**
	 * 
	 * @var int
	 * @readonly
	 * @filter order,eq
	 */
	public $depth;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * The name of the Category. 
	 * The following characters are not allowed: '<', '>', ','
	 * 
	 * @var string
	 */
	public $name;
	
	/**
	 * The full name of the Category
	 * 
	 * @var string
	 * @readonly
	 * @filter order,eq,likex
	 */
	public $fullName;
	
	/**
	 * Number of entries in this Category (including child categories)
	 * 
	 * @var int
	 * @readonly
	 */
	public $entriesCount;
	
	/**
	 * Creation date as Unix timestamp (In seconds)
	 *  
	 * @var int
	 * @readonly
	 * @filter order
	 */
	public $createdAt;
	
	private static $mapBetweenObjects = array
	(
		"id",
		"parentId",
		"depth",
		"name",
		"fullName",
		"partnerId",
		"entriesCount",
		"createdAt"
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	}
	
	public function validateParentId(KalturaCategory $category)
	{
		if ($category->parentId === null)
			$category->parentId = 0;
			
		if ($category->parentId !== 0)
		{
			$parentCategoryDb = categoryPeer::retrieveByPK($category->parentId);
			if (!$parentCategoryDb)
				throw new KalturaAPIException(KalturaErrors::PARENT_CATEGORY_NOT_FOUND, $category->parentId);
		}
	}
}