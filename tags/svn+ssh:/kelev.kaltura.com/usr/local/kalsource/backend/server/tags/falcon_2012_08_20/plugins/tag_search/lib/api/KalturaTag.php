<?php
/**
 * @package plugins.tagSearch
 * @subpackage api.objects
 */
class KalturaTag extends KalturaObject
{
    /**
     * @var int
     * @readonly
     */
    public $id;
    
    /**
     * @var string
     * @readonly
     */
    public $tag;
    
    /**
     * @var KalturaTaggedObjectType
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
     * @readonly
     */
    public $instanceCount;
    
    /**
     * @var int
     * @readonly
     */
    public $createdAt;
    
    private static $map_between_objects = array
	(
		"id",
	    "tag",
	    "taggedObjectType" => "objectType",
	    "partnerId",
	    "instanceCount",
	    "createdAt",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
    
}