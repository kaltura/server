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
	public $ottEnvironmentUrl;

	/**
	 * @var bool
	 */
	public $analyticsPersistentSessionId;

	private static $map_between_objects = array
	(
		"analyticsUrl",
		"ottEnvironmentUrl",
		"analyticsPersistentSessionId",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}