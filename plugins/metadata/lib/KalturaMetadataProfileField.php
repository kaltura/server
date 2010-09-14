<?php
class KalturaMetadataProfileField extends KalturaObject 
{
	/**
	 * 
	 * @var int
	 * @readonly
	 */
	public $id;

	
	/**
	 * 
	 * @var string
	 * @readonly
	 */
	public $xPath;

	
	/**
	 * 
	 * @var string
	 * @readonly
	 */
	public $key;

	
	/**
	 * 
	 * @var string
	 * @readonly
	 */
	public $label;
	
	private static $map_between_objects = array
	(
		"id",
		"xPath",
		"key",
		"label",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}