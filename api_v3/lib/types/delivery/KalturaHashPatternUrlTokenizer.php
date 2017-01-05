<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaHashPatternUrlTokenizer extends KalturaUrlTokenizer
{
	
	/**
	 * Regex pattern to find the part of the URL that should be hashed
	 *
	 * @var string
	 */
	public $hashPatternRegex;
	
	private static $map_between_objects = array
	(
			"hashPatternRegex",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
