<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchQuery extends KalturaESearchObject
{
	/**
	 * @var string
	 */
	public $eSerachQuery;

	private static $mapBetweenObjects = array
	(
		"eSerachQuery",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new ESearchParams();
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
