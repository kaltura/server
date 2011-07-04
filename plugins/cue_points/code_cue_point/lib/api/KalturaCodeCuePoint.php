<?php
/**
 * @package plugins.codeCuePoint
 * @subpackage api.objects
 */
class KalturaCodeCuePoint extends KalturaCuePoint
{
	/**
	 * @var string
	 */
	public $code;
	
	/**
	 * @var string 
	 */
	public $description;
	
	private static $map_between_objects = array
	(
		"code" => "name",
		"description" => "text",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}
