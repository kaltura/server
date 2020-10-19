<?php
/**
 * @package api
 * @subpackage objects
 */

class KalturaUrlRecognizerKaltura extends KalturaUrlRecognizer
{

	/**
	 *
	 * @var string
	 */
	public $key;

	private static $map_between_objects = array
	(
		"key",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kKalturaUrlRecognizer();

		parent::toObject($dbObject, $skip);

		return $dbObject;
	}

}