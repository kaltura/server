<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaExtractDataJobData extends KalturaJobData
{
	/**
	 * @var KalturaFileContainer
	 */
	public $fileContainer;

	/**
	 * @var string
	 */
	public $entryId;

	/**
	 * @var array
	 */
	public $enginesType;


	private static $map_between_objects = array
	(
		"fileContainer",
		"entryId",
		"enginesType",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kExtractDataJobData();
			
		return parent::toObject($dbData);
	}
}

