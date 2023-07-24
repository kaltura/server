<?php

/**
 * @package api
 * @subpackage objects
 */
class KalturaUserCapability extends KalturaObject
{
	/**
	 * @var KalturaUserCapabilityEnum
	 */
	public $capability;
	
	private static $map_between_objects = array
	(
		'capability',
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
