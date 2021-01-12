<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryProfileVod extends KalturaDeliveryProfile
{

	/**
	 * @var bool
	 */
	public $simuliveSupport;

	private static $map_between_objects = array
	(
		'simuliveSupport'
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge (parent::getMapBetweenObjects() , self::$map_between_objects);
	}
}