<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
abstract class KalturaESearchParams extends KalturaObject
{
	/**
	 * @var string
	 */
	public $objectStatuses;

	/**
	 * @var string
	 */
	public $objectId;

	/**
	 * @var KalturaESearchOrderBy
	 */
	public $orderBy;

	/**
	 * @var bool
	 */
	public $useHighlight;

	private static $mapBetweenObjects = array
	(
		"objectStatuses",
		"objectId",
		"orderBy",
		"useHighlight",
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
