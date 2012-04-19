<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaMoveCategoryEntriesJobData extends KalturaJobData
{
	/**
	 * Source category id
	 * @var int
	 */   	
    public $srcCategoryId;
    
    /**
     * Destination category id
     * @var int
     */
    public $destCategoryId;
    
    /**
     * All entries from all child categories will be moved as well
     * @var bool
     */
    public $moveFromChildren;
    
    /**
     * Entries won't be deleted from the source entry
     * @var bool
     */
    public $copyOnly;
	
    
    
	private static $map_between_objects = array
	(
	    'srcCategoryId',
	    'destCategoryId',
	    'moveFromChildren',
	    'copyOnly',
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kMoveCategoryEntriesJobData();
			
		return parent::toObject($dbData);
	}
}
