<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchParams extends KalturaObject
{
	/**
	 * @var KalturaESearchOperator
	 */
	public $searchOperator;

	/**
	 * @var string
	 */
	public $objectStatuses;

	/**
	 * @var KalturaESearchOrderBy
	 */
	public $orderBy;


	private static $mapBetweenObjects = array
	(
		"searchOperator",
		"objectStatuses",
		"orderBy",
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
