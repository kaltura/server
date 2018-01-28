<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCsvAdditionalFieldInfo extends KalturaObject

{
	/**
	 * @var string
	 */
	public $fieldName;

	/**
	 * @var string
	 */
	public $xpath;


	private static $map_between_objects = array
	(
		"fieldName",
		"xpath",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

}