<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUrlTokenizerL3 extends KalturaUrlTokenizer
{
	/**
	 * gen
	 *
	 * @var string
	 */
	public $gen;

	/**
	 * paramName
	 *
	 * @var string
	 */
	public $paramName;

	private static $map_between_objects = array
	(
		'gen',
		'paramName'
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kL3UrlTokenizer();

		return parent::toObject($dbObject, $skip);
	}

}