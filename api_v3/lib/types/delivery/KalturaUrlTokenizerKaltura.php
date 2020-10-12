<?php
/**
 * @package api
 * @subpackage objects
 */

class KalturaUrlTokenizerKaltura  extends KalturaUrlTokenizer
{

	/**
	 * @var string
	 */
	public $secret;

	private static $map_between_objects = array
	(
		"secret",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kKalturaUrlTokenizer();

		parent::toObject($dbObject, $skip);

		return $dbObject;
	}

}