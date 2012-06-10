<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBulkUploadResultCategory extends KalturaBulkUploadResult
{
    /**
     * @var string
     */
    public $relativePath;
    /**
     * @var string
     */
    public $name;
    
    /**
     * @var string
     */
    public $referenceId;
    
    /**
     * @var string
     */
    public $description;
    
    /**
     * @var string
     */
    public $tags;
    
    /**
     * @var int
     */
    public $appearInList;
    
    /**
     * @var int
     */
    public $privacy;
    
    /**
     * @var int
     */
    public $inheritance;
    
    /**
     * @var int
     */
    public $userJoinPolicy;
    
    /**
     * @var int
     */
    public $defaultPermissionLevel;
    
    /**
     * @var string
     */
    public $owner;
    
    /**
     * @var int
     */
    public $contributionPolicy; 
    
    /**
     * @var int
     */
    public $partnerSortValue;
    
    private static $mapBetweenObjects = array
	(
	    "relativePath",
		"name",
	    "referenceId",
	    "description",
	    "tags",
	    "appearInList",
	    "privacy",
	    "inheritance",
	    "userJoinPolicy",
	    "defaultPermissionLevel",
	    "owner",
	    "contributionPolicy",
	    "partnerSortValue",
	);
	
    public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
    public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		return parent::toInsertableObject(new BulkUploadResultCategory(), $props_to_skip);
	}
}