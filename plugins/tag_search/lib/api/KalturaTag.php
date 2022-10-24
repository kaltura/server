<?php
/**
 * @package plugins.tagSearch
 * @subpackage api.objects
 * @relatedService TagService
 */
class KalturaTag extends KalturaObject implements IRelatedFilterable
{
    /**
     * @var int
     * @readonly
     */
    public $id;
    
    /**
     * @var string
     * @filter eq,likex
     * @readonly
     */
    public $tag;
    
    /**
     * @var KalturaTaggedObjectType
     * @filter eq
     * @readonly
     */
    public $taggedObjectType;
    
    /**
     * @var int
     * @readonly
     */
    public $partnerId;
    
    /**
     * @var int
     * @filter eq,in,gte,lte,order
     * @readonly
     */
    public $instanceCount;
    
    /**
     * @var time
     * @readonly
     * @filter gte,lte,order
     */
    public $createdAt;
    
    /**
     * @var time
     * @readonly
     */
    public $updatedAt;
    
    private static $map_between_objects = array
	(
		"id",
	    "tag",
	    "taggedObjectType" => "objectType",
	    "partnerId",
	    "instanceCount",
	    "createdAt",
		"updatedAt",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/**
	 * @inheritDoc
	 */
	function getExtraFilters()
	{
		return array();
	}

	/**
	 * @inheritDoc
	 */
	function getFilterDocs()
	{
		return array();
	}
}