<?php

/**
 * @package api
 * @subpackage object
 */
class KalturaRegexItem extends KalturaObject
{
	/**
	 * @var string
	 */
	public $regex;
	
	/**
	 * @var string
	 */
	public $description;
	
	private static $map_between_objects = array(
		'regex',
		'description',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		return array( $this->regex, $this->description );
	}
}
