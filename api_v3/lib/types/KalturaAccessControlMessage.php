<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAccessControlMessage extends KalturaObject{

	/**
	 * @var string
	 */
	public $message;

	/**
	 * @var string
	 */
	public $code;

	private static $map_between_objects = array
	(
		"message",
		"code",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

}