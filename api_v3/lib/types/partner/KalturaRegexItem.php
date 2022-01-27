<?php
/**
 * @package api
 * @subpackage object
 */
class KalturaRegexItem extends KalturaObject
{
	/**
	 *  @var string
	 */
	public $regex;

	private static $map_between_objects = array(
		'regex',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}



}
?>