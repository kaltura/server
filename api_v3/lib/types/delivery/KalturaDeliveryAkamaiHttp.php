<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryAkamaiHttp extends KalturaDelivery {
	
	/**
	 * Should we use intelliseek
	 * 
	 * @var bool
	 * @filter eq,in
	 */
	public $useIntelliseek;
	
	private static $map_between_objects = array
	(
			"useIntelliseek",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new DeliveryAkamaiHttp();
	
		$this->type = KalturaDeliveryType::AKAMAI_HTTP;
		parent::toObject($dbObject, $skip);
		
		return $dbObject;
	}
	
	public function fromObject($sourceObject) {
		parent::fromObject($sourceObject);
	}
}

