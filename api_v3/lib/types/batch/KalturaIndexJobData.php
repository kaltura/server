<?php
/**
 * @package api
 * @subpackage objects
 * 
 * TODO:
 * 1.	Filters should instantiate the correct core filter in their toObject method.
 * 2.	Need to think how to instantiate the correct KalturaFilter in the fromObject,
 * 		should it be a factory with a huge switch?
 */
class KalturaIndexJobData extends KalturaJobData
{
	/**
	 * @var KalturaFilter
	 */
	public $filter;
	
	private static $map_between_objects = array
	(
		"filter" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kIndexJobData();
			
		return parent::toObject($dbData, $props_to_skip);
	}
}
