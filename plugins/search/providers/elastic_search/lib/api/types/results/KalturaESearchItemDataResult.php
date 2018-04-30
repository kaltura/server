<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchItemDataResult extends KalturaObject
{
	/**
	 * @var int
	 */
	public $totalCount;

	/**
	 * @var KalturaESearchItemDataArray
	 */
	public $items;

	/**
	 * @var string
	 */
	public $itemsType;

	private static $map_between_objects = array(
		'totalCount',
		'items',
		'itemsType',
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

}
