<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCategoryEntry extends KalturaObject implements IFilterable 
{
	/**
	 * 
	 * @var int
	 * @filter eq,in
	 */
	public $categoryId;
	
	/**
	 * User id
	 * 
	 * @var string
	 * @filter eq,in
	 */
	public $entryId;
	
	/**
	 * Creation date as Unix timestamp (In seconds)
	 *  
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;
	
	/**
	 * The full ids of the Category
	 * 
	 * @var string
	 * @readonly
	 * @filter likex,eq
	 */
	public $categoryFullIds;
	
	private static $mapBetweenObjects = array
	(
		"entryId",
		"categoryId",
		"createdAt",
		"categoryFullIds",
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
	
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('categoryId');
		$this->validatePropertyNotNull('entryId');
		parent::validateForInsert($propertiesToSkip);
	}
	
	public function validateForUpdate($sourceObject = null, $propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('categoryId');
		$this->validatePropertyNotNull('entryId');
		parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
}