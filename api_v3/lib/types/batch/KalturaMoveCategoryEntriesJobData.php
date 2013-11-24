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
     * Saves the last category id that its entries moved completely
     * In case of crash the batch will restart from that point
     * @var int
     */
    public $lastMovedCategoryId;
    
    /**
     * Saves the last page index of the child categories filter pager
     * In case of crash the batch will restart from that point
     * @var int
     */
    public $lastMovedCategoryPageIndex;
    
    /**
     * Saves the last page index of the category entries filter pager
     * In case of crash the batch will restart from that point
     * @var int
     */
    public $lastMovedCategoryEntryPageIndex;
    
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
	
    /**
     * Destination categories fallback ids
     * @var string
     */
    public $destCategoryFullIds;
    
	private static $map_between_objects = array
	(
	    'srcCategoryId',
	    'destCategoryId',
	    'lastMovedCategoryId',
	    'lastMovedCategoryPageIndex',
	    'lastMovedCategoryEntryPageIndex',
	    'moveFromChildren',
	    'copyOnly',
		'destCategoryFullIds',
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
