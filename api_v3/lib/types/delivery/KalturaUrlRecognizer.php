<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUrlRecognizer extends KalturaObject {

	/**
	 * The hosts that are recognized
	 *
	 * @var string
	 */
	public $hosts;
	
	/**
	 * The URI prefix we use for security
	 * @var string
	 */
	public $uriPrefix;
	
	private static $map_between_objects = array
	(
			"hosts",
			"uriPrefix"
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
