<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryProfileHttp extends KalturaDeliveryProfile
{

	/**
	 * @var int
	 */
	public $maxSize;

	private static $map_between_objects = array
	(
		"maxSize"
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

}