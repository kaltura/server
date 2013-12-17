<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaReport extends KalturaObject implements IFilterable 
{
	/**
	 * Report id
	 * 
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $id;
	
	/**
	 * Partner id associated with the report
	 * 
	 * @var int
	 * @filter eq,in
	 */
	public $partnerId;
	
	/**
	 * Report name
	 * 
	 * @var string
	 */
	public $name;
	
	/**
	 * Used to identify system reports in a friendly way
	 * 
	 * @var string
	 * @filter eq,in
	 */
	public $systemName;
	
	/**
	 * Report description
	 * 
	 * @var string
	 */
	public $description;
	
	/**
	 * Report query
	 * 
	 * @var string
	 */
	public $query;
	
	/**
	 * Creation date as Unix timestamp (In seconds)
	 *  
	 * @var time
	 * @readonly
	 * @filter order
	 */
	public $createdAt;
	
	/**
	 * Last update date as Unix timestamp (In seconds)
	 *  
	 * @var time
	 * @readonly
	 */
	public $updatedAt;
	
	private static $mapBetweenObjects = array
	(
		'id',
		'partnerId',
		'name',
		'systemName',
		'description',
		'query',
		'createdAt',
		'updatedAt',
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
		parent::validateForInsert($propertiesToSkip);
		
		$this->validatePartnerId();
		$this->validatePropertyMinLength("name", 1);
		$this->validatePropertyMinLength("query", 1);
	}
	
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUpdate($sourceObject, $propertiesToSkip);
		
		if ($this->partnerId !== null)
			$this->validatePartnerId();
		$this->validatePropertyMinLength("name", 1, true);
	}
	
	protected function validatePartnerId()
	{
		if (!PartnerPeer::retrieveByPK($this->partnerId))
			throw new KalturaAPIException(KalturaErrors::INVALID_PARTNER_ID, $this->partnerId);
	}
}