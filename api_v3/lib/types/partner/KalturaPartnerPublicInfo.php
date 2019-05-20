<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPartnerPublicInfo extends KalturaObject
{
	/**
	 * @var string
	 */
	public $analyticsUrl;

	/**
	 * @var string
	 */
	public $ottServiceUrl;

	private static $map_between_objects = array
	(
		"analyticsUrl",
		"ottServiceUrl",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}