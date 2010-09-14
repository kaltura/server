<?php
/**
 * @package api
 * @subpackage objects
 */

/**
 * Will be used as the base class for all the job objects.
 */
class KalturaBaseJob extends KalturaObject implements IFilterable 
{
	/**
	 * @var int
	 * @readonly
	 * @filter eq,gte
	 */
	public $id;

	/**
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $partnerId;	
	
	
    /**
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
    public $createdAt;
    
    /**
	 * @var int
	 * @readonly 
	 */
    public $updatedAt;
    
    /**
	 * @var int
	 * @readonly 
	 */
    public $deletedAt;
	
	
	/**
	 * @var int
	 * @readonly 
	 */	
	public $processorExpiration;
	
	/**
	 * @var int
	 * @readonly
	 * @filter order
	 */	
	public $executionAttempts;
	
	/**
	 * @_var int
	 * @readonly
	 */	
	public $lockVersion;

	
	private static $map_between_objects = array
	(
		"id" ,
		"partnerId" , 
	 	"createdAt" , "updatedAt" , "deletedAt" , 
	 	"processorExpiration" ,
		"executionAttempts", "lockVersion" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	}
}

?>