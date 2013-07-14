<?php
/**
 * @package plugins.tagSearch
 * @subpackage api.objects
 */
class KalturaIndexTagsByPrivacyContextJobData extends KalturaJobData
{
	/**
	 * @var int
	 */
	public $changedCategoryId;
	
	/**
	 * @var string
	 */
	public $deletedPrivacyContexts;
	
	/**
	 * @var string
	 */
	public $addedPrivacyContexts;
	
	private static $map_between_objects = array
	(
		"changedCategoryId" ,
		"deletedPrivacyContexts" ,
		"addedPrivacyContexts" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kIndexTagsByPrivacyContextJobData();
			
		return parent::toObject($dbData, $props_to_skip);
	}
}