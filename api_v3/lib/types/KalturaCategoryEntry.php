<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCategoryEntry extends KalturaObject implements IRelatedFilterable 
{
	/**
	 * 
	 * @var int
	 * @filter eq,in
	 */
	public $categoryId;
	
	/**
	 * entry id
	 * 
	 * @var string
	 * @filter eq,in
	 */
	public $entryId;
	
	/**
	 * Creation date as Unix timestamp (In seconds)
	 *  
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;
	
	/**
	 * The full ids of the Category
	 * 
	 * @var string
	 * @readonly
	 * @filter likex
	 */
	public $categoryFullIds;
	
	/**
	 * 
	 * CategroyEntry status
	 * @var KalturaCategoryEntryStatus
	 * @readonly
	 * @filter eq,in
	 */
	public $status;
	
	/**
	 * 
	 * CategroyEntry creator puser ID
	 * @var string
	 * @readonly
	 * @filter eq,in
	 */
	public $creatorUserId;
	
	private static $mapBetweenObjects = array
	(
		"entryId",
		"categoryId",
		"createdAt",
		"categoryFullIds",
		"status",
		"creatorUserId" => "creatorPuserId",
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
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
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert($propertiesToSkip)
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('categoryId');
		$this->validatePropertyNotNull('entryId');
		parent::validateForInsert($propertiesToSkip);
	}
	
	public function toObject($dbObject = null, $skip = array()) 
	{
	    
		if (is_null ( $dbObject ))
			$dbObject = new categoryEntry();
			
		parent::toObject ( $dbObject, $skip );
		
		return $dbObject;
	}
	
}