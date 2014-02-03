<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryAkamaiRtsp extends KalturaDelivery {

	/**
	 * CP-code
	 *
	 * @var int
	 * @filter eq,in
	 */
	public $cpCode;
	
	private static $map_between_objects = array
	(
			"cpCode",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new DeliveryAkamaiRtsp();
	
		$this->type = KalturaDeliveryType::AKAMAI_RTSP;
		parent::toObject($dbObject, $skip);
		return $dbObject;
	}
}

