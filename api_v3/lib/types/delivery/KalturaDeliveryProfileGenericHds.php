<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryProfileGenericHds extends KalturaDeliveryProfile {
	
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
		$this->type = KalturaDeliveryProfileType::GENERIC_HDS;
		return parent::toObject($dbObject,$skip);
	}
}

