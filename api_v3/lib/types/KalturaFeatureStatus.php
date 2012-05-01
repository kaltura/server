<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFeatureStatus extends KalturaObject
{
	/**
	 * @var KalturaFeatureStatusType
	 */
	public $statusType;
	
	/**
	 * @var int
	 */
	public $statusValue;
	
	private static $map_between_objects = array
	(
		"statusType",
		"statusValue",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}		
}