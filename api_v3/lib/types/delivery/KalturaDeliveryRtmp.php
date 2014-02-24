<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryRtmp extends KalturaDelivery {

	/**
	 * enforceRtmpe
	 *
	 * @var bool
	 * @filter eq,in
	 */
	public $enforceRtmpe;
	
	
	/**
	 * a prefix that is added to all stream urls (replaces storageProfile::rtmpPrefix)
	 *
	 * @var string
	 * @filter eq,in
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
	
	public function toObject($dbObject = null, $skip = array())
	{
		$this->type = KalturaDeliveryType::RTMP;
		return parent::toObject($dbObject, $skip);
	}
	
}

