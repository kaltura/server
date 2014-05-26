<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryProfileRtmp extends KalturaDeliveryProfile {

	/**
	 * enforceRtmpe
	 *
	 * @var bool
	 */
	public $enforceRtmpe;
	
	
	/**
	 * a prefix that is added to all stream urls (replaces storageProfile::rtmpPrefix)
	 *
	 * @var string
	 */
	public $prefix;
	
	
	private static $map_between_objects = array
	(
			"enforceRtmpe",
			"prefix"
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
}

