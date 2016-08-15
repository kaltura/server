<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUrlTokenizer extends KalturaObject
{

	/**
	 * Window
	 *
	 * @var int
	 */
	public $window;
	
	/**
	 * key
	 *
	 * @var string
	 */
	public $key;
	
	/**
	 * @var bool
	 */
	public $limitIpAddress;
	
	private static $map_between_objects = array
	(
			"key",
			"window",
			"limitIpAddress",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

}
