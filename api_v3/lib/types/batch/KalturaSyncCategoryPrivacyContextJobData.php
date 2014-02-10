<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaSyncCategoryPrivacyContextJobData extends KalturaJobData
{
	/**
	 * category id
	 * @var int
	 */   	
    public $categoryId;
    
    /**
     * Saves the last category entry creation date that was updated
     * In case of crash the batch will restart from that point
     * @var int
     */
    public $lastUpdatedCategoryEntryCreatedAt;
    
    /**
     * Saves the last sub category creation date that was updated
     * In case of crash the batch will restart from that point
     * @var int
     */
    public $lastUpdatedCategoryCreatedAt;
    
    
      
	private static $map_between_objects = array
	(
	    'categoryId',
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kSyncCategoryPrivacyContextJobData();
			
		return parent::toObject($dbData);
	}
}
