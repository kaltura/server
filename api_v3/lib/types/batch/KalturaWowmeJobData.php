<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaWowmeJobData extends KalturaJobData
{

	/**
	 * @var string
	 */
	public $outEntryId;

	/**
	 * @var KalturaHighlightType
	 */
	public $highlightType;

	/**
	 * @var string
	 */
	public $fileSyncPath;

	private static $map_between_objects = array
	(
		"outEntryId",
		"highlightType",
		"fileSyncPath",
	);


	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	    
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject(  $dbWowmeJobData = null, $props_to_skip = array())
	{
		if(is_null($dbWowmeJobData))
			$dbWowmeJobData = new kWowmeJobData();
			
		return parent::toObject($dbWowmeJobData, $props_to_skip);
	}
	    
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject($srcObj)
	 */
	public function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null) 
	{
		parent::doFromObject($srcObj, $responseProfile);
	}
}
