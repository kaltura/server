<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryGenericHttp extends KalturaDelivery {
	
	/**
	 * @var string
	 * @filter eq,in
	 */
	public $pattern;
	
	
	private static $map_between_objects = array
	(
			"pattern"
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		$this->type = KalturaDeliveryType::GENERIC_HTTP;
		return parent::toObject($dbObject,$skip);
	}
}

