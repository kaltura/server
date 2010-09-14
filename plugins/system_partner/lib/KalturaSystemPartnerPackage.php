<?php
class KalturaSystemPartnerPackage extends KalturaObject
{
	/**
	 * @var int
	 */
	public $id;
	
	/**
	 * @var string
	 */
	public $name;
	
	private static $map_between_objects = array
	(
		"id",
		"name",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}