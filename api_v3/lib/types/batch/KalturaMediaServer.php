<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaMediaServer extends KalturaObject implements IFilterable
{
	/**
	 * Unique identifier
	 * 
	 * @var int
	 * @readonly
	 */
	public $id;

	
	/**
	 * Server data center id
	 * 
	 * @var int
	 * @readonly
	 */
	public $dc;

	
	/**
	 * Server host name
	 * 
	 * @var string
	 * @readonly
	 */
	public $hostname;
	
	/**
	 * Server first registration date as Unix timestamp (In seconds)
	 * 
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;
	
	/**
	 * Server last update date as Unix timestamp (In seconds)
	 * 
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;
	
	private static $mapBetweenObjects = array
	(
		'id',
		'dc',
		'hostname',
		'createdAt',
		'updatedAt',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	/* (non-PHPdoc)
	 * @see IFilterable::getExtraFilters()
	 */
	public function getExtraFilters()
	{
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IFilterable::getFilterDocs()
	 */
	public function getFilterDocs()
	{
		return array();
	}
}